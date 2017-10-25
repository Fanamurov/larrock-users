<?php

namespace Larrock\ComponentUsers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Larrock\ComponentCart\Facades\LarrockCart;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Larrock\ComponentUsers\Facades\LarrockUsers;
use Larrock\ComponentUsers\Models\SocialAccount;
use Larrock\ComponentDiscount\Facades\LarrockDiscount;
use Session;

class UserController extends Controller
{
    public function __construct()
    {
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-catalog')) {
            \View::share('config_catalog', LarrockCatalog::getConfig());
        }

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')) {
            \View::share('ykassa', config('yandex_kassa'));
        }
        LarrockUsers::shareConfig();
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
            if(auth()->user()->level() === 3) {
                return redirect()->intended('/admin');
            }
            // Authentication passed...
            if($request->has('page') && !empty($request->get('page'))){
                return redirect($request->get('page', '/user/cabinet'));
            }
            return redirect()->intended('/user/cabinet');
        }
        Session::push('message.danger', 'Логин или пароль не верные');
        return back();
    }

    public function socialite($provider)
    {
        $user = $this->createOrGetUser(\Socialite::driver($provider)->user(), $provider);
        auth()->login($user);
        return redirect()->to('/user');
    }

    public function cabinet()
    {
        \View::share('current_user', Auth::guard()->user());

        if(Auth::check() !== TRUE){
            Session::push('message.danger', 'Вы не авторизованы');
            return redirect()->intended();
        }
        $user = LarrockUsers::getModel()::whereId(Auth::id());

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')){
            $user->with('cart');
        }

        $data['user'] = $user->first();

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discount')){
            $data['discounts'] = LarrockDiscount::getModel()->whereActive(1)
                ->whereType('Накопительная скидка')
                ->where('d_count', '>', 0)
                ->where('cost_min', '<', $data['user']->cart->sum('cost'))
                ->where('cost_max', '>', $data['user']->cart->sum('cost'))->first();
        }

        return view('larrock::front.user.cabinet', $data);
    }

    public function updateProfile(Request $request)
    {
        \View::share('current_user', Auth::guard()->user());

        $user = LarrockUsers::config()->model::whereId(Auth::id())->firstOrFail();
        $user->fill($request->except(['password', 'old-password']));
        if($request->has('password')){
            if(\Hash::check($request->get('old-password'), $user->password)){
                $user->password = \Hash::make($request->get('password'));
            }else{
                Session::push('message.danger', 'Введенный вами старый пароль не верен');
            }
        }
        if($user->save()){
            Session::push('message.danger', 'Ваш профиль успешно обновлен');
        }else{
            Session::push('message.danger', 'Произошла ошибка во время обновления профиля');
        }
        return back()->withInput();
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

            if( !$email = $providerUser->getEmail()){
                Session::push('message.danger', 'В вашем соц.профиле не указан email. Регистрация на сайте через ваш аккаунт в '. $provider .' не возможна');
                return redirect('/user')->withInput();
            }

            if( !$name = $providerUser->getName()){
                $name = 'Покупатель';
            }

            if( !$user = LarrockUsers::config()->model::whereEmail($providerUser->getEmail())->first()){
                $user = LarrockUsers::config()->model::create([
                    'email' => $email,
                    'name' => $name,
                    'fio' => $name,
                    'password' => \Hash::make($providerUser->getId() . $name),
                ]);

                if($get_user = LarrockUsers::config()->model::whereEmail($email)->first()){
                    $get_user->attachRole(3); //role user
                    Session::push('message.danger', 'Пользователь '. $email .' успешно зарегистрированы');
                }
            }

            $account->user()->associate($user);
            $account->save();
            return $user;
        }

        return $account->user;
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }
}