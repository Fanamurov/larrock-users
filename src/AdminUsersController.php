<?php

namespace Larrock\ComponentUsers;

use Alert;
use Larrock\Core\Component;
//use Ultraware\Roles\Models\Role;
use Breadcrumbs;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Validator;
use Redirect;
use View;
use Larrock\ComponentUsers\Facades\LarrockUsers;

/* https://github.com/romanbican/roles */

class AdminUsersController extends Controller
{
    public function __construct()
    {
        $this->config = LarrockUsers::shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. LarrockUsers::getName() .'.index', function($breadcrumbs){
            $breadcrumbs->push(LarrockUsers::getTitle(), '/admin/'. LarrockUsers::getName());
        });
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
        Breadcrumbs::register('admin.'. LarrockUsers::getName() .'.create', function($breadcrumbs)
        {
            $breadcrumbs->parent('admin.'. LarrockUsers::getName() .'.index');
            $breadcrumbs->push('Создание');
        });

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

        if($data->save()){
            $data->attachRole((int) $request->get('role'));
            \Cache::flush();
            Alert::add('successAdmin', 'Пользователь '. $request->input('email') .' добавлен')->flash();
            return Redirect::to('/admin/'. LarrockUsers::getName() .'/'. $data->id .'/edit')->withInput();
        }

        Alert::add('errorAdmin', 'Пользователь '. $request->input('email') .' не добавлен')->flash();
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

        $validator = JsValidator::make(Component::_valid_construct(LarrockUsers::getConfig(), 'update', $id));
        View::share('validator', $validator);

        Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $data)
        {
            $breadcrumbs->parent('admin.'. LarrockUsers::getName() .'.index');
            $breadcrumbs->push($data->email);
        });
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
            Alert::add('successAdmin', 'Пользователь изменен')->flash();
            \Cache::flush();
        }else{
            Alert::add('errorAdmin', 'Не удалось изменить пользователя')->flash();
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
                Alert::add('successAdmin', 'Пользователь удален')->flash();
            }else{
                Alert::add('errorAdmin', 'Не удалось удалить пользователя')->flash();
            }
        }else{
            Alert::add('errorAdmin', 'Такого пользователя больше нет')->flash();
        }

        if($request->get('place') === 'material'){
            return Redirect::to('/admin/'. LarrockUsers::getName());
        }
        return back();
    }
}
