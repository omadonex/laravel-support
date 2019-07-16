<?php

namespace Omadonex\LaravelSupport\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    protected $table = 'user_activations';
    protected $fillable = ['token', 'sent_at'];
    protected $primaryKey = 'user_id';
    protected $dates = ['sent_at'];
    public $timestamps = false;
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivationUrl()
    {
        return route('app.user.activation', $this->token);
    }
}
