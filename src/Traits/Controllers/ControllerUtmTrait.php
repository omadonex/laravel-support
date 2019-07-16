<?php

namespace Omadonex\LaravelSupport\Traits\Controllers;

use Illuminate\Http\Request;

trait ControllerUtmTrait
{
   public function refreshUtm(Request $request)
   {
       if (!is_null($request->utm_source)) {
           session()->forget(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']);
           session([
               'utm_source' => $request->utm_source,
               'utm_medium' => $request->utm_medium,
               'utm_campaign' => $request->utm_campaign,
               'utm_content' => $request->utm_content,
           ]);
       }
   }
}
