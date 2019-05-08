<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxShellException extends \Exception
{
    protected $result;
    protected $output;

    public function __construct($result, $output)
    {
        $this->result = $result;
        $this->output = $output;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'result' => $result,
        ]), ConstantsCustom::EXCEPTION_SHELL);
    }
}