<?php


namespace App\Services\Infinity\Api;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GetItemsService extends BaseApi
{
    public string $folder;

    public $items;
    public $tasks = [];

    /** @var Collection */
    public $listnames;

    public function handle($folder = null)
    {
        $this->getItemsForFolder($folder);
        $this->analyzeItems();

        return $this;
    }

    public function getItemsForFolder($folder = null)
    {
        if ($folder) {
            $this->withFolder($folder);
        }

        $hasMore = true;
        $params = ['limit' => '100', 'expand[]' => 'values.attribute'];
        $this->items = collect();

        while ($hasMore) {
            logger('getting items for folder '.$this->folder);
            logger('params '.print_r($params, true));

            $body = $this->items('?'.http_build_query($params));
            $hasMore = $body->has_more;
            $params['after'] = $body->after;

            $data = Arr::where($body->data, function ($value, $key) {
                return $value->folder_id == $this->folder;
            });

            logger('retrieved '.count($data).' matching items');

            $this->items = $this->items->concat((array) $data);
        }

        return $this->items;
    }

    public function analyzeItems()
    {
        $this->getListNames($this->items[0]);

        foreach ($this->items as $item) {
            $sections = collect($item->values);

            $content = $listname = $attachments = null;

            $contentSection = $sections->firstWhere('attribute.name', 'Name');
            if ($contentSection) {
                $content = $contentSection->data;
            }

            $listSection = $sections->firstWhere('attribute.name', 'List');
            if ($listSection) {
                $listname = $this->listnames->firstWhere('id', $listSection->data[0])->name;
            }

            $attachmentSections = $sections->where('attribute.name', 'Attachments');
            if ($attachmentSections) {
                $attachments = [];
                foreach ($attachmentSections as $attachmentSection) {
                    $attachments[] = [
                        'link' => $attachmentSection->data[0]->link,
                        'name' => $attachmentSection->data[0]->original_name,
                    ];
                }
            }

            $task = [
                'id' => $item->id,
                'content' => $content,
                'list' => $listname,
                'attachments' => $attachments,
            ];

            $this->tasks[] = $task;
        }
    }

    protected function getListNames($item)
    {
        $list = null;
        foreach ($item->values as $value) {
            try {
                if ($value->attribute->settings->labels) {
                    $list = $value->attribute->settings->labels;
                }
            } catch (\Exception $e) {
            }
        }

        if ($list) {
            logger('got list');
            $this->listnames = collect($list);
        }
    }
}
