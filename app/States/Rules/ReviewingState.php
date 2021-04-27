<?php


namespace App\States\Rules;


class ReviewingState extends RuleState
{
    public static $name = 'Reviewing';

    public static function order(): string
    {
        return 10;
    }
}
