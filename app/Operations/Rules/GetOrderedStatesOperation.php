<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Operations\BaseOperation;

class GetOrderedStatesOperation extends BaseOperation
{
    public $states;

    /**
     * GetOrderedStatesOperation constructor.
     */
    public function __construct(public Rule $rule)
    {
    }

    public function handle()
    {
        $allowedStates = static::buildStates($this->rule);
        $this->states = static::orderedStateObjects($this->rule, $allowedStates);

        return $this->states;
    }

    public static function buildStates(Rule $rule)
    {
        $transitionable_states = $rule->state->transitionableStates();
        $currentState = $rule->state->getMorphClass();
        $shown_states = [];

        foreach ($transitionable_states as $state) {
            $transitionClass = $rule->state->config()->resolveTransitionClass($currentState, $state);

            if (!$transitionClass
                || (new $transitionClass($rule, auth()->user()))->canTransition()
            ) {
                $shown_states[] = $state;
            }
        }

        return $shown_states;
    }

    /**
     * @param  Rule  $rule
     * @param  array  $state_names
     */
    public function orderedStateObjects($rule, $state_names)
    {
        $currentStateClass = \Spatie\ModelStates\State::resolveStateClass($rule->state);
        $namespaceParts = explode('\\', $currentStateClass);
        array_pop($namespaceParts);
        $namespaceStart = implode('\\', $namespaceParts).'\\';

        $states = [];
        $state_names[] = $rule->state;

        foreach ($state_names as $state_name) {
            $classname = $namespaceStart.$state_name.'State';
            $state = new $classname($rule);
            $states[] = $state;
        }

        return collect($states)->map(function ($state) {
            return [
                'name' => $state->getValue(),
                'order' => $state->order(),
                'requiresAssignee' => $state->requiresAssignee,
                'requiresNoError' => $state->requiresNoError,
            ];
        })->sortBy(function ($state, $key) {
            return $state['order'];
        })->values();
    }

}
