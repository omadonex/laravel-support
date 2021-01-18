<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxShellException extends \Exception
{
    protected $result;
    protected $output;

    public function __construct($result, $output)
    {
        $this->result = $result;
        $this->output = $output;

        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'result' => $result,
        ]), ConstCustom::EXCEPTION_SHELL);
    }
}