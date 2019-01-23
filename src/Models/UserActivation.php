<?php

namespace Omadonex\LaravelSupport\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    protected $fillable = ['token'];
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivationUrl()
    {
        return route('user.activation', $this->token);
    }
}
