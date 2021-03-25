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

class Flagged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Rule
     */
    public Rule $rule;

    /**
     * @var User
     */
    public $flagging_user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
        $this->flagging_user = auth()->user();
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
