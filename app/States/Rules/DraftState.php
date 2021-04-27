<?php


namespace App\States\Rules;


class DraftState extends RuleState
{
    public static $name = 'Draft';

    public static function order(): string
    {
        return 0;
    }
}
