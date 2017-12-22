<?php

namespace Larrock\ComponentUsers;

use Larrock\Core\Component;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JsValidator;
use Larrock\Core\Traits\ShareMethods;
use Validator;
use Redirect;
use View;
use Larrock\ComponentUsers\Facades\LarrockUsers;

/* https://github.com/romanbican/roles */

class AdminUsersController extends Controller
{
    use ShareMethods;

    public function __construct()
    {
        $this->shareMethods();
        $this->middleware(LarrockUsers::combineAdminMiddlewares());
        LarrockUsers::shareConfig();
        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $with = ['role'];
        $enable_cart = null;
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')){
            $with[] = 'cart';
            $enable_cart = true;
        }
        $users = LarrockUsers::getModel()->with($with)->paginate(15);
        return view('larrock::admin.users.index', array('data' => $users, 'enable_cart' => $enable_cart));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create()
    {
        $data['app'] = LarrockUsers::tabbable(NULL);
        return view('larrock::admin.admin-builder.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Component::_valid_construct(LarrockUsers::getValid()));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = LarrockUsers::getModel()->fill($request->all());
        $data->password = bcrypt($request->get('password'));
        unset($data->config);

        if($data->save()){
            $data->attachRole((int) $request->get('role'));
            \Cache::flush();
            \Session::push('message.success', 'Пользователь '. $request->input('email') .' добавлен');
            return Redirect::to('/admin/'. LarrockUsers::getName() .'/'. $data->id .'/edit')->withInput();
        }

        \Session::push('message.danger', 'Пользователь '. $request->input('email') .' не добавлен');
        return Redirect::to('/admin/users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['data'] = LarrockUsers::getModel()->whereId($id)->with('role')->first();
        $data['app'] = LarrockUsers::tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct(LarrockUsers::getConfig(), 'update', $id));
        View::share('validator', $validator);
        return view('larrock::admin.admin-builder.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Component::_valid_construct(LarrockUsers::getConfig(), 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $user = LarrockUsers::getModel()->whereId($id)->first();
        $user->detachAllRoles();
        $user->attachRole($request->get('role'));

        $submit = $request->all();
        if($submit['password'] !== $user->password){
            $submit['password'] = bcrypt($submit['password']);
        }else{
            unset($submit['password']);
        }

        if($user->update($submit)){
            \Session::push('message.success', 'Пользователь изменен');
            \Cache::flush();
        }else{
            \Session::push('message.danger', 'Не удалось изменить пользователя');
        }

        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($user = LarrockUsers::getModel()->whereId($id)->first()){
            $user->detachAllRoles();

            if($user->delete()){
                \Session::push('message.success', 'Пользователь удален');
            }else{
                \Session::push('message.danger', 'Не удалось удалить пользователя');
            }
        }else{
            \Session::push('message.danger', 'Такого пользователя больше нет');
        }

        if($request->get('place') === 'material'){
            return Redirect::to('/admin/'. LarrockUsers::getName());
        }
        return back();
    }
}
