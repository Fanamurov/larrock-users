<?php

namespace Larrock\ComponentUsers;

use Alert;
use App\User;
use Larrock\Core\Component;
//use Ultraware\Roles\Models\Role;
use Breadcrumbs;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Validator;
use Redirect;
use View;

/* https://github.com/romanbican/roles */

class AdminUsersController extends Controller
{
	protected $config;

	public function __construct()
	{
        $Component = new UsersComponent();
        $this->config = $Component->shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. $this->config->name .'.index', function($breadcrumbs){
            $breadcrumbs->push($this->config->title, '/admin/'. $this->config->name);
        });
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$users = User::with('role', 'orders')->paginate(15);
		return view('larrock::admin.users.index', array('data' => $users));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create()
    {
        $data['app'] = $this->config->tabbable(NULL);
        Breadcrumbs::register('admin.'. $this->config->name .'.create', function($breadcrumbs)
        {
            $breadcrumbs->parent('admin.'. $this->config->name .'.index');
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
        $validator = Validator::make($request->all(), Component::_valid_construct($this->config->valid));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = new User();
        $data->fill($request->all());
        $data->password = bcrypt($request->get('password'));

        if($data->save()){
            $data->attachRole((int) $request->get('role'));
            \Cache::flush();
            Alert::add('successAdmin', 'Пользователь '. $request->input('email') .' добавлен')->flash();
            return Redirect::to('/admin/'. $this->config->name .'/'. $data->id .'/edit')->withInput();
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
        $data['data'] = User::whereId($id)->with('role')->first();
        $data['app'] = $this->config->tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct($this->config, 'update', $id));
        View::share('validator', $validator);

        Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $data)
        {
            $breadcrumbs->parent('admin.'. $this->config->name .'.index');
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
        $validator = Validator::make($request->all(), Component::_valid_construct($this->config, 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

		$user = User::whereId($id)->first();
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
		if($user = User::whereId($id)->first()){
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
            return Redirect::to('/admin/'. $this->config->name);
        }
        return back();
    }
}
