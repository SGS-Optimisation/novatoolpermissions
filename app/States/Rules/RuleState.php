<?php


namespace App\States\Rules;

use App\Transitions\Rules\DraftToPublished;
use App\Transitions\Rules\RequestReview;
use App\Transitions\Rules\ReviewingToPublished;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class RuleState extends State
{
    abstract public static function order(): string;

    public $requiresAssignee = false;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PublishedState::class)
            ->allowTransition(PublishedState::class, DraftState::class)
            ->allowTransition(PublishedState::class, ReviewingState::class, RequestReview::class)

            ->allowTransition(DraftState::class, ReviewingState::class, RequestReview::class)
            ->allowTransition(DraftState::class, PublishedState::class, DraftToPublished::class)

            ->allowTransition(ReviewingState::class, PublishedState::class, ReviewingToPublished::class)
            ->allowTransition(ReviewingState::class, DraftState::class)
            ;
    }
}
