<?php

namespace Larrock\ComponentUsers;

use Larrock\Core\Component;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormPassword;
use Larrock\Core\Helpers\FormBuilder\FormTagsRole;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;
use Larrock\ComponentUsers\Models\Roles;
use Larrock\ComponentUsers\Facades\LarrockUsers;
use Larrock\ComponentUsers\Models\User;

class UsersComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'users';
        $this->title = 'Пользователи';
        $this->description = 'Зарегистрированные пользователи на сайте';
        $this->model = \config('larrock.models.users', User::class);
        $this->addRows()->isSearchable();
    }

    public function config()
    {
        return $this;
    }

    protected function addRows()
    {
        $row = new FormInput('email', 'Email/login');
        $this->rows['email'] = $row->setValid('email|min:4|required|unique:users,email,:id')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdmin();

        $row = new FormPassword('password', 'Пароль');
        $this->rows['password'] = $row->setValid('min:5|required')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('name', 'Name');
        $this->rows['name'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setValid('required');

        $row = new FormInput('first_name', 'Имя');
        $this->rows['first_name'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('last_name', 'Фамилия');
        $this->rows['last_name'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('fio', 'ФИО');
        $this->rows['fio'] = $row->setInTableAdmin()->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('tel', 'Телефон');
        $this->rows['tel'] = $row->setInTableAdmin()->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormTagsRole('role', 'Роль');
        $this->rows['role'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')
            ->setConnect(Roles::class, 'role')
            ->setAttached()->setValid('required')->setMaxItems(1);

        $row = new FormTextarea('address', 'Адрес');
        $this->rows['address'] = $row;

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = \Cache::remember('count-data-admin-'. LarrockUsers::getName(), 1440, function(){
            return LarrockUsers::getModel()->count(['id']);
        });
        return view('larrock::admin.sectionmenu.types.default', ['count' => $count, 'app' => LarrockUsers::getConfig(), 'url' => '/admin/'. LarrockUsers::getName()]);
    }
}