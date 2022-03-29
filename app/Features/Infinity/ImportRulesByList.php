<?php


namespace App\Features\Infinity;


use App\Features\BaseFeature;
use App\Jobs\ImportInfinityTask;
use App\Jobs\ImportInfinityTaskList;
use App\Models\ClientAccount;
use App\Services\Infinity\Api\BaseApi;
use App\Services\Infinity\Api\GetItemsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportRulesByList extends BaseFeature
{


    /**
     * ImportRules constructor.
     */
    public function __construct(
        public ClientAccount|string|int|null $client,
        public $folder,
        public ?string $board = null,
        public ?string $workspace = null
    ) {
        if (is_string($this->client)) {
            $this->client = ClientAccount::whereSlug($this->client)->first();
        }
        if (is_int($this->client)) {
            $this->client = ClientAccount::find($this->client);
        }

    }

    public function handle()
    {
        $itemsService = (new GetItemsService($this->workspace, $this->board))->handle($this->folder);

        $tasksByList = collect($itemsService->tasks)->groupBy('list');

        foreach ($tasksByList as $listname => $tasks) {

            dispatch(new ImportInfinityTaskList($this->client, $listname, $tasks));
        }
    }
}
