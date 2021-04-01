<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportedRulesErrors implements WithMultipleSheets
{

    public $rules;

    /**
     * ImportedRulesErrors constructor.
     * @param $rules
     */
    public function __construct($rules)
    {
        $this->rules = $rules;
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->rules as $client => $rules) {
            $sheets[] = new ImportedRulesErrorsPerSheet($client, $rules);
        }

        return $sheets;
    }
}
