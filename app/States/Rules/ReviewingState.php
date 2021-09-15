<?php


namespace App\States\Rules;


class ReviewingState extends RuleState
{
    public static $name = 'Reviewing';

    public $requiresAssignee = true;

    public static function order(): string
    {
        return 10;
    }
}
