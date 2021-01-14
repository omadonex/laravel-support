<?php

namespace Omadonex\LaravelSupport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Utm extends Model
{
	protected $table = 'utms';
    protected $fillable = ['source', 'medium', 'campaign', 'content'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
