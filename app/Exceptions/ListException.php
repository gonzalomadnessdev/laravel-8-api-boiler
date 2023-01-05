<?php

namespace App\Exceptions;

use Exception;

class ListException extends Exception
{

    public function __construct($messageList, $code = 0, $previous = null)
    {
        parent::__construct("", $code, $previous);

        $this->message = implode("\n", $messageList);
    }

    public function getMessageArray()
    {
        return explode("\n", $this->message);
    }
}
