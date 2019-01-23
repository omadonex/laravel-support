<?php

namespace Omadonex\LaravelSupport\Classes\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Omadonex\LaravelSupport\Traits\CustomizeMailable;

class OmxMailable extends Mailable
{
    use Queueable, SerializesModels, CustomizeMailable;

    protected $transNs;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $reflection = new \ReflectionClass($this);
        $namespace = $reflection->getNamespaceName();
        $className = $reflection->getShortName();

        $this->transNs = "mail.{$className}";
        $nsArr = explode('\\', $namespace);
        if ($nsArr[0] === 'Modules') {
            $this->transNs = lcfirst($nsArr[1]) . "::" . $this->transNs;
        }
    }

    public function addMailSubject()
    {
        return $this->subject(trans("{$this->transNs}.subject"));
    }

    public function addMailGreeting($username = null)
    {
        $greetingPartKey = $username ? 'greetingUser' : 'greeting';
        $greetingKey = "{$this->transNs}.$greetingPartKey";
        $text = trans($greetingKey, ['username' => $username]);

        if ($text === $greetingKey) {
            return $this->useDefaultGreeting($username);
        }

        return $this->addGreeting($text);
    }
}
