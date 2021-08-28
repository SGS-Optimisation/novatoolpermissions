<?php


namespace App\Broadcast;


use App\Models\Job;
use App\Services\Jobs\RuleFilter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RulesFiltered implements ShouldBroadcast
{

    use Dispatchable, SerializesModels, InteractsWithSockets;

    /**
     * @var Job $jobItem
     */
    private Job $jobItem;

    /**
     * @var array $rules
     */
    private array $rules;

    public function __construct($jobItem)
    {
        $this->jobItem = $jobItem;
        $this->rules = RuleFilter::handle($this->jobItem);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'rules-updated';
    }

    /**
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('rules-filtered.'.$this->jobItem->job_number);
    }
}
