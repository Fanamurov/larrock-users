<?php

namespace Larrock\ComponentUsers\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoleUsers.
 *
 * @property int $user_id
 * @property int $role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\RoleUsers whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\RoleUsers whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\RoleUsers whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\RoleUsers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentUsers\Models\RoleUsers find($value)
 * @mixin \Eloquent
 */
class RoleUsers extends Model
{
    protected $table = 'role_user';
}
