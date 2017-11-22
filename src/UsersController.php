<?php

namespace Larrock\ComponentUsers;

use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Larrock\ComponentUsers\Facades\LarrockUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use LarrockCatalog;
use LarrockDiscount;
use Session;

class UsersController extends Controller{

    use AuthenticatesUsers, ValidatesRequests, SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-catalog')) {
            \View::share('config_catalog', LarrockCatalog::getConfig());
        }

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')) {
            \View::share('ykassa', config('yandex_kassa'));
        }
        LarrockUsers::shareConfig();
        $this->middleware(LarrockUsers::combineFrontMiddlewares());
    }

    public function index()
    {
        if(Auth::check()){
            return redirect()->intended('/cabinet');
        }
        return $this->showLoginForm();
    }

    public function showLoginForm()
    {
        return view('larrock::front.auth.login-register');
    }

    /**
     * Редирект после успешного логина
     *
     * @return string
     */
    public function redirectPath()
    {
        if(auth()->user()->level() === 3) {
            return '/admin';
        }
        return '/cabinet';
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return LarrockUsers::getModel()->create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $user->attachRole(3);
        return redirect($this->redirectPath());
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

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPasswordRequestForm()
    {
        return view('larrock::front.auth.passwords.email');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('larrock::front.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
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
}