<?php

namespace Larrock\ComponentUsers\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentUsers\Roles\Contracts\RoleHasRelations as RoleHasRelationsContract;
use Larrock\ComponentUsers\Roles\Traits\RoleHasRelations;
use Larrock\ComponentUsers\Roles\Traits\Slugable;

class Role extends Model implements RoleHasRelationsContract
{
    use Slugable, RoleHasRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'level'];

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
