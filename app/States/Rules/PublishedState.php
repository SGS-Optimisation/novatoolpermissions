<?php


namespace App\States\Rules;


class PublishedState extends RuleState
{
    public static $name = 'Published';

    public static function order(): string
    {
        return 100;
    }
}
