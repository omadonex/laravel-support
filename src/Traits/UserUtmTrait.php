<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\Utm;

trait UserUtmTrait
{
    public function authenticates()
    {
        return $this->belongsTo(Utm::class);
    }

    public function setUtm()
    {
        if (session()->has('utm_source')) {
            $source = session('utm_source');
            $medium = session('utm_medium');
            $campaign = session('utm_campaign');
            $content = session('utm_content');

            $utm = Utm::where('source', $source)
                ->where('medium', $medium)
                ->where('campaign', $campaign)
                ->where('content', $content)
                ->first();

            if (is_null($utm)) {
                $utm = Utm::create([
                    'source' => $source,
                    'medium' => $medium,
                    'campaign' => $campaign,
                    'content' => $content
                ]);
            }

            $this->update(['utm_id' => $utm->id]);

            session()->forget(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']);
        }
    }
}
