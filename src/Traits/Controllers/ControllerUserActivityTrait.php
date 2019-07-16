<?php

namespace Omadonex\LaravelSupport\Traits\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ControllerUserActivityTrait
{
   public function updateUserActivity()
   {
       if (auth()->check()) {
           DB::update(['last_active_at' => Carbon::now()->timestamp])->where('user_id', auth()->id());
       }
   }
}
