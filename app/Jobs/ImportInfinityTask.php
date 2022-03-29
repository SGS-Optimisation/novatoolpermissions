<?php

namespace App\Jobs;

use App\Models\ClientAccount;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportInfinityTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public ClientAccount $client,
        public array $task
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
        $task = $this->task;
        $content = $task['content'] ?? $task['name'];

        if ($task['attachments']) {
            foreach ($task['attachments'] as $attachment) {

                $img_content = file_get_contents($attachment['link']);
                $img_name = uniqid().'_'.$attachment['name'];
                $image_path = 'rules/'.Carbon::now()->format('Y-m-d').'/'.$img_name;

                Storage::disk('azure')->put($image_path, $img_content);

                $content .= '<br><img alt="'.$attachment['name'].'" src="'.Storage::disk('azure')->url($image_path).'"/>';
            }
        }

        if (DB::table('rules')->where('metadata->infinity_import->id', $task['id'])->count() == 0) {
            $this->client->rules()->create([
                'content' => $content,
                'name' => $task['name'] ?? $task['list'] ?? Str::limit($content, 20),
                'metadata' => [
                    'infinity_import' => [
                        'id' => $task['id'],
                        'date' => Carbon::now(),
                    ]
                ]
            ]);
        }
    }
}
