<?php

namespace App\Jobs;

use App\Models\ClientAccount;
use App\States\Rules\DraftState;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportInfinityTaskList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public ClientAccount $client,
        public string $listname,
        public Collection $tasks
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = "";

        foreach ($this->tasks as $task) {
            $content .= !empty($task['content']) ? $task['content'] : $task['name'];

            if ($task['attachments']) {
                foreach ($task['attachments'] as $attachment) {

                    $img_content = file_get_contents($attachment['link']);
                    $img_name = uniqid().'_'.$attachment['name'];
                    $image_path = 'rules/'.Carbon::now()->format('Y-m-d').'/'.$img_name;

                    Storage::disk('azure')->put($image_path, $img_content);

                    $content .= '<br><img alt="'.$attachment['name'].'" src="'.Storage::disk('azure')->url($image_path).'"/>';
                }
            }
            $content .= '<br>';

        }

        $existing_rules_same_name = $this->client->rules()
            ->where('name', $this->listname)
            ->whereNull('metadata->infinity_import->id')
            ->where('state', DraftState::class)->get();

        if (count($existing_rules_same_name)) {
            foreach ($existing_rules_same_name as $rule) {
                $rule->delete();
            }
        }

        if (DB::table('rules')->where('metadata->infinity_import->id', $task['listid'])->count() == 0) {
            $this->client->rules()->create([
                'content' => $content,
                'name' => $this->listname,
                'metadata' => [
                    'infinity_import' => [
                        'id' => $task['listid'],
                        'date' => Carbon::now(),
                    ]
                ]
            ]);
        }
    }
}
