# Laravel Component Users Larrock CMS

##Installation
config/auth.php
change 
'providers' => [
        'users' => [
            'driver' => 'eloquent',
            //'model' => App\User::class, //REMOVE
            'model' => \Larrock\ComponentUsers\Models\User::class, //ADD ComponentUsers model
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],