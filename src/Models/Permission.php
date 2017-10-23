<?php

namespace Larrock\ComponentUsers\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentUsers\Roles\Contracts\PermissionHasRelations as PermissionHasRelationsContract;
use Larrock\ComponentUsers\Roles\Traits\PermissionHasRelations;
use Larrock\ComponentUsers\Roles\Traits\Slugable;

class Permission extends Model implements PermissionHasRelationsContract
{
    use Slugable, PermissionHasRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'model'];

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($connection = config('larrock-roles.connection')) {
            $this->connection = $connection;
        }
    }
}
