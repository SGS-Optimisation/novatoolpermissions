<?php

namespace App\Exports;

use App\Services\Matomo\Reports\UserVisits;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserVisitsExport implements FromArray, WithHeadings, WithMapping
{
    public array $visits;

    public function __construct(public ?string $period = null, public ?string $date = null)
    {
        $generator = (new UserVisits)->handle($this->period, $this->date);
        $this->visits = $generator->visits_list;
    }

    public function array(): array
    {
        return $this->visits;
    }

    public function headings(): array
    {
        return [
            'Job#',
            'Client',
            'User',
            'Datetime'
        ];
    }

    public function map($entry): array
    {
        return [
            $entry['job_number'],
            $entry['client'],
            $entry['user'],
            $entry['time']
        ];

    }
}
