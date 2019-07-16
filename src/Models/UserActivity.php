<?php

namespace Omadonex\LaravelSupport\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table = 'user_activities';
    protected $fillable = ['last_active_at'];
    protected $primaryKey = 'user_id';
    protected $dates = ['last_active_at'];
    public $timestamps = false;
    public $incrementing = false;

	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
