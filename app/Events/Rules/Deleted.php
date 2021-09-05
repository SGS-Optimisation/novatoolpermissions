<?php

namespace App\Events\Rules;

use App\Models\Rule;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Deleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rule;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Rule $rule, User $user = null)
    {
        $this->rule = $rule;
        $this->user = $user ?? (!\Auth::guest() ? \Auth::user() : null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
