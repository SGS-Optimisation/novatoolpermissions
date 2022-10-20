<?php

namespace App\Exceptions;

use Exception;

class MultipleClientAccountsMatchedException extends Exception
{
    /**
     * @param  array  $possible_accounts
     */
    public function __construct(protected $message, public array $possible_accounts = [])
    {
        parent::__construct($this->message);
    }


}
