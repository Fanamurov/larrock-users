<?php
namespace Larrock\ComponentUsers\Models;

use App\Models\Cart;
use App\Models\Orders;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Nicolaslopezj\Searchable\SearchableTrait;

use Ultraware\Roles\Traits\HasRoleAndPermission;
use Ultraware\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use Larrock\ComponentUsers;

/**
 * App\User
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $permissions
 * @property string $last_login
 * @property string $first_name
 * @property string $last_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $remember_token
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePermissions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @mixin \Eloquent
 * @property string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName($value)
 * @property string $fio
 * @property string $address
 * @property string $tel
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Orders[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFio($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTel($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ultraware\Roles\Models\Role[] $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ultraware\Roles\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ultraware\Roles\Models\Permission[] $userPermissions
 * @method static \Illuminate\Database\Query\Builder|\App\User search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Query\Builder|\App\User searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract, HasMediaConversions
{
	use Authenticatable, CanResetPassword, HasRoleAndPermission, Notifiable;

    use HasMediaTrait;

    use SearchableTrait;

    // no need for this, but you can define default searchable columns:
    protected $searchable = [
        'columns' => [
            'users.email' => 10,
            'users.fio' => 7,
            'users.tel' => 5,
            'users.address' => 2,
        ]
    ];

    public function registerMediaConversions()
    {
        $this->addMediaConversion('110x110')
            ->setManipulations(['w' => 110, 'h' => 110])
            ->performOnCollections('manual');

        $this->addMediaConversion('110x110')
            ->setManipulations(['w' => 110, 'h' => 110])
            ->performOnCollections('photo');
    }

	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name', 'last_name', 'email', 'password', 'name', 'fio', 'address', 'tel'
	];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function role()
	{
		return $this->belongsToMany('Ultraware\Roles\Models\Role', 'role_user', 'user_id', 'role_id');
	}

    public function orders()
    {
        return $this->hasMany(Orders::class, 'user_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function getImages()
    {
        $config = new ComponentUsers\UsersComponent();
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where('model_type', '=', $config->model)->orderBy('order_column', 'DESC');
    }

    public function getPhoto()
    {
        $config = new ComponentUsers\UsersComponent();
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where('model_type', '=', $config->model)->where('collection_name', '=', 'user_photo')->orderBy('order_column', 'DESC');
    }

    public function getCroppedPhoto()
    {
        $config = new ComponentUsers\UsersComponent();
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where('model_type', '=', $config->model)->where('collection_name', '=', 'photo')->orderBy('order_column', 'DESC');
    }
}