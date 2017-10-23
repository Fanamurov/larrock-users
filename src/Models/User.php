<?php
namespace Larrock\ComponentUsers\Models;

use Larrock\ComponentCart\Facades\LarrockCart;
use Larrock\ComponentUsers\Facades\LarrockUsers;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Nicolaslopezj\Searchable\SearchableTrait;

use Larrock\ComponentUsers\Roles\Traits\HasRoleAndPermission;
use Larrock\ComponentUsers\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\Media;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Role[] $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Permission[] $userPermissions
 * @method static \Illuminate\Database\Query\Builder|\App\User search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Query\Builder|\App\User searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract, HasMediaConversions
{
    use Authenticatable, CanResetPassword, HasRoleAndPermission, Notifiable;

    use HasMediaTrait;

    use SearchableTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockUsers::addFillableUserRows(['first_name', 'last_name', 'email', 'password', 'name', 'fio', 'address', 'tel']));
        $this->table = LarrockUsers::getConfig()->table;
    }

    protected $guarded = [];

    // no need for this, but you can define default searchable columns:
    protected $searchable = [
        'columns' => [
            'users.email' => 10,
            'users.fio' => 7,
            'users.tel' => 5,
            'users.address' => 2,
        ]
    ];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('110x110')
            ->height(110)->width(110)
            ->performOnCollections('images');
    }

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
        return $this->belongsToMany(config('larrock-roles.models.role'), 'role_user', 'user_id', 'role_id');
    }

    public function cart()
    {
        return $this->hasMany(LarrockCart::getModelName(), 'user', 'id')->orderBy('updated_at', 'desc');
    }

    public function getImages()
    {
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')
            ->where('model_type', '=', LarrockUsers::getModelName())
            ->orderBy('order_column', 'DESC');
    }
}