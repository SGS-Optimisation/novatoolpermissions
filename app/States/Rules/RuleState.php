<?php


namespace App\States\Rules;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class RuleState extends State
{
    abstract public static function order(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PublishedState::class)
            ->allowTransition(PublishedState::class, DraftState::class)
            ->allowTransition(PublishedState::class, ReviewingState::class)

            ->allowTransition(DraftState::class, ReviewingState::class)
            ->allowTransition(DraftState::class, PublishedState::class)

            ->allowTransition(ReviewingState::class, PublishedState::class)
            ->allowTransition(ReviewingState::class, DraftState::class)
            ;
    }
}
