<?php

namespace Rednose\FrameworkBundle\Message;

class Message
{
    const SUCCESS_TYPE = 'success';
    const INFO_TYPE    = 'info';
    const WARN_TYPE    = 'warn';
    const ERROR_TYPE   = 'error';

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $severity;

    /**
     * @param string $text
     * @param string $severity
     */
    public function __construct($text, $severity = self::SUCCESS_TYPE)
    {
        $this->text = $text;
        $this->severity = $severity;
    }
}