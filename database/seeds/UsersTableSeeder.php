<?php

namespace Larrock\ComponentUsers\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Larrock\ComponentUsers\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'Admin',
            'slug' => 'Админ',
            'description' => NULL,
            'level' => 3
        ]);

        DB::table('roles')->insert([
            'name' => 'Moderator',
            'slug' => 'Модератор',
            'description' => NULL,
            'level' => 2
        ]);

        DB::table('roles')->insert([
            'name' => 'User',
            'slug' => 'Пользователь',
            'description' => NULL,
            'level' => 1
        ]);

        $first_user = new User();
        $first_user->name = 'Admin';
        $first_user->email = 'admin@larrock-cms.ru';
        $first_user->password = bcrypt('password');
        $first_user->first_name = 'Admin';
        $first_user->last_name = 'Khabarovsk';
        $first_user->fio = 'Admin Khabarovsk';
        $first_user->save();
        $first_user->attachRole(1);
    }
}
