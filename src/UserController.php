<?php

namespace Larrock\ComponentUsers;

use Alert;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Larrock\ComponentUsers\Models\SocialAccount;
use Larrock\ComponentUsers\Models\User;
use Larrock\ComponentCatalog\CatalogComponent;
use Mail;

class UserController extends Controller
{
    public $ykassa;
    protected $config_catalog;

    public function __construct()
    {
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-catalog')) {
            $Component = new CatalogComponent();
            $this->config_catalog = $Component->shareConfig();
            \View::share('config_catalog', $this->config_catalog);
        }

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')) {
            $this->ykassa = config('yandexkassa');
            \View::share('ykassa', $this->ykassa);
        }
    }


    public function index()
    {
        if(Auth::check()){
            return redirect()->intended('/user/cabinet');
        }
        return view('larrock::front.auth.login-register');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            //IF admin
            if(auth()->user()->level() === 3) {
                return redirect()->intended('/admin');
            }
            // Authentication passed...
            if($request->has('page') && !empty($request->get('page'))){
                return redirect($request->get('page', '/user/cabinet'));
            }
            return redirect()->intended('/user/cabinet');
        }
        \Alert::add('error', 'Логин или пароль не верные')->flash();
        return back();
    }

    public function socialite($provider)
    {
        $user = $this->createOrGetUser(\Socialite::driver($provider)->user(), $provider);
        auth()->login($user);
        return redirect()->to('/user');
    }

    //TODO: Восстановление пароля

    public function cabinet()
    {
        \View::share('current_user', Auth::guard()->user());

        if(Auth::check() !== TRUE){
            Alert::add('error', 'Вы не авторизованы')->flash();
            return redirect()->intended();
        }
        $data['user'] = User::whereId(Auth::id())->with('orders')->first();
        $data['discounts'] = Discount::whereActive(1)
            ->whereType('Накопительная скидка')
            ->where('d_count', '>', 0)
            ->where('cost_min', '<', $data['user']->orders->sum('cost'))
            ->where('cost_max', '>', $data['user']->orders->sum('cost'))->first();

        return view('front.user.cabinet', $data);
    }

    public function updateProfile(Request $request)
    {
        \View::share('current_user', Auth::guard()->user());

        $user = User::whereId(Auth::id())->firstOrFail();
        $user->fill($request->except(['password', 'old-password']));
        if($request->has('password')){
            if(\Hash::check($request->get('old-password'), $user->password)){
                $user->password = \Hash::make($request->get('password'));
            }else{
                Alert::add('error', 'Введенный вами старый пароль не верен')->flash();
            }
        }
        if($user->save()){
            Alert::add('success', 'Ваш профиль успешно обновлен')->flash();
        }else{
            Alert::add('error', 'Произошла ошибка во время обновления профиля')->flash();
        }
        return back()->withInput();
    }

    public function removeOrder($id)
    {
        $order = Cart::find($id);
        if($order->delete()){
            $this->changeTovarStatus($order->items);
            Alert::add('success', 'Заказ успешно отменен')->flash();
        }else{
            Alert::add('error', 'Произошла ошибка во время отмены заказа')->flash();
        }
        return back()->withInput();
    }

    /**
     * Меняем количество товара в наличии
     * @param $cart
     */
    protected function changeTovarStatus($cart)
    {
        foreach($cart as $item){
            if($data = Catalog::find($item->id)){
                $data->nalichie += $item->qty; //Остаток товара
                $data->sales -= $item->qty; //Количество продаж
                if($data->save()){
                    Alert::add('success', 'Резервирование товара под ваш заказ снято')->flash();
                }else{
                    Alert::add('error', 'Не удалось отменить резервирование товара под ваш заказ')->flash();
                }
            }
        }
    }

    public function createOrGetUser(ProviderUser $providerUser, $provider)
    {
        $account = SocialAccount::whereProvider($provider)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if( !$account){
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $provider
            ]);

            $email = $providerUser->getEmail();
            if( !$email){
                Alert::add('error', 'В вашем соц.профиле не указан email. Регистрация на сайте через ваш аккаунт в '. $provider .' не возможна');
                return redirect('/user')->withInput();
            }

            if( !$name = $providerUser->getName()){
                $name = 'Покупатель';
            }

            if( !$user = User::whereEmail($providerUser->getEmail())->first()){
                $user = User::create([
                    'email' => $email,
                    'name' => $name,
                    'fio' => $name,
                    'password' => \Hash::make($providerUser->getId() . $name),
                ]);

                if($get_user = User::whereEmail($email)->first()){
                    $get_user->attachRole(3); //role user
                    Alert::add('success', 'Пользователь '. $email .' успешно зарегистрированы')->flash();
                    //$this->mailRegistry($request, $get_user);
                }
            }

            $account->user()->associate($user);
            $account->save();
            return $user;
        }

        return $account->user;
    }

    /**
     * Отправка письма о регистрации
     * @param Request $request
     * @param User    $user
     */
    public function mailRegistry(Request $request, User $user)
    {
        //FormsLog::create(['formname' => 'register', 'params' => $request->all(), 'status' => 'Новое']);

        $mails = collect(array_map('trim', explode(',', env('MAIL_TO_ADMIN', 'robot@martds.ru'))));

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $send = Mail::send('emails.register', ['data' => $user->toArray()],
            function($message) use ($mails){
                $message->from('no-reply@'. array_get($_SERVER, 'HTTP_HOST'), env('MAIL_TO_ADMIN_NAME', 'ROBOT'));
                foreach($mails as $value){
                    $message->to($value);
                }
                $message->subject('Вы успешно зарегистрировались на сайте '. env('SITE_NAME', array_get($_SERVER, 'HTTP_HOST'))
                );
            });

        if($send){
            Alert::add('success', 'На Ваш email отправлено письмо с регистрационными данными')->flash();
        }else{
            Alert::add('danger', 'Письмо с информацией по регистрации не отправлено')->flash();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }
}