<?php

namespace Larrock\ComponentUsers;

use Cache;
use LarrockUsers;
use Larrock\Core\Component;
use Larrock\ComponentUsers\Models\User;
use Larrock\ComponentUsers\Roles\Models\Role;
use Larrock\Core\Helpers\FormBuilder\FormTags;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormPassword;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;

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

    protected function addRows()
    {
        $row = new FormInput('email', 'Email/login');
        $this->setRow($row->setValid('email|min:4|required|unique:users,email,:id')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setInTableAdmin()->setFillable());

        $row = new FormPassword('password', 'Пароль');
        $this->setRow($row->setValid('min:5|required')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('name', 'Name');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('first_name', 'Имя');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('last_name', 'Фамилия');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('fio', 'ФИО');
        $this->setRow($row->setInTableAdmin()->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('tel', 'Телефон');
        $this->setRow($row->setInTableAdmin()->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormTags('role', 'Роль');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setModels(User::class, Role::class)->setMaxItems(1)
            ->setValid('required')->setTitleRow('slug'));

        $row = new FormTextarea('address', 'Адрес');
        $this->setRow($row->setFillable());

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = Cache::rememberForever('count-data-admin-'.LarrockUsers::getName(), function () {
            return LarrockUsers::getModel()->count(['id']);
        });

        return view('larrock::admin.sectionmenu.types.default', ['count' => $count, 'app' => LarrockUsers::getConfig(),
            'url' => '/admin/'.LarrockUsers::getName(), ]);
    }
}
