<?php

namespace Larrock\ComponentUsers\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentUsers\Facades\LarrockUsers;

class SocialAccount extends Model
{
    protected $fillable = ['user_id', 'provider_user_id', 'provider'];

    public function user()
    {
        return $this->belongsTo(LarrockUsers::getModel());
    }
}
