<?php

namespace App\Exports;

use App\Models\Rule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ImportedRulesErrorsPerSheet implements FromCollection, WithTitle, WithMapping
{
    public $client;
    public $rules;

    /**
     * ImportedRulesErrors constructor.
     * @param $rules
     */
    public function __construct($client, $rules)
    {
        $this->client = $client;
        $this->rules = $rules;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->rules);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->client;
    }

    /**
     * @param Rule $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            $row->content,
            $row->metadata,
        ];
    }
}
