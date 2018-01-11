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

use Larrock\Core\Component;
use Larrock\Core\Traits\GetFilesAndImages;
use Larrock\Core\Traits\GetLink;
use Nicolaslopezj\Searchable\SearchableTrait;

use Larrock\ComponentUsers\Roles\Traits\HasRoleAndPermission;
use Larrock\ComponentUsers\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Larrock\ComponentUsers\Models
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
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User wherePermissions($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereLastLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereRememberToken($value)
 * @mixin \Eloquent
 * @property string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereName($value)
 * @property string $fio
 * @property string $address
 * @property string $tel
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Models\Orders $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereFio($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User whereTel($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Role[] $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Larrock\ComponentUsers\Roles\Models\Permission[] $userPermissions
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\User searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract, HasMediaConversions
{
    /**
     * @var $this Component
     */
    protected $config;

    use Authenticatable, CanResetPassword, HasRoleAndPermission, Notifiable;
    use HasMediaTrait;
    use SearchableTrait;
    use GetFilesAndImages;
    use GetLink;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockUsers::addFillableUserRows([]));
        $this->table = LarrockUsers::getConfig()->table;
        $this->config = LarrockUsers::getConfig();
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

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getConfig()
    {
        return $this->config;
    }

    public function role()
    {
        return $this->belongsToMany(config('larrock-roles.models.role'), 'role_user', 'user_id', 'role_id');
    }

    public function cart()
    {
        return $this->hasMany(LarrockCart::getModelName(), 'user', 'id')->orderBy('updated_at', 'desc');
    }
}