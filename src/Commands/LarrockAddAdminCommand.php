<?php

namespace Larrock\ComponentUsers\Commands;

use DB;
use Illuminate\Console\Command;
use App\Exceptions\Handler;
use Larrock\ComponentUsers\Models\User;

class LarrockAddAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larrock:addAdmin {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->option('email');
        if( !$email){
            $email = $this->ask('What is your email?', 'admin@larrock-cms.ru');
        }
        $password = $this->option('password');
        if( !$password){
            $password = $this->ask('What is your password', 'password');
        }

        if( !DB::table('roles')->exists()){
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
        }

        $first_user = new User();
        $first_user->name = 'Admin';
        $first_user->email = $email;
        $first_user->password = bcrypt($password);
        $first_user->first_name = 'Admin';
        $first_user->last_name = 'Larrock';
        $first_user->fio = 'Admin Larrock';
        $first_user->save();
        $first_user->attachRole(1);

        $this->info('Admin user created successfully');
        $this->info('Login/email: '. $email);
        $this->info('Password: '. $password);
    }
}