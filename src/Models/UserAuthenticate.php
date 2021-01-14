<?php

namespace Omadonex\LaravelSupport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserAuthenticate extends Model
{
    protected $table = 'user_authenticates';
    protected $fillable = ['user_id', 'network', 'uid', 'identity', 'profile'];

	public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVkontakte($query)
    {
        return $query->where('network', 'vkontakte');
    }

    public function scopeFacebook($query)
    {
        return $query->where('network', 'facebook');
    }
}
