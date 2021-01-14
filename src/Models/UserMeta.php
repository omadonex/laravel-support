<?php

namespace Omadonex\LaravelSupport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Omadonex\LaravelSupport\Traits\PersonNamesTrait;

class UserMeta extends Model
{
    use PersonNamesTrait;

    protected $table = 'user_metas';
    protected $fillable = ['display_name', 'first_name', 'last_name', 'opt_name', 'avatar', 'phone', 'email_reserve'];
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    public $incrementing = false;

	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
