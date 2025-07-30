<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsInfoExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'Judul Inovasi' => $row->innovation_title,
                'Nama Tim' => $row->team_name,
                'Benefit Real' => empty($row->financial) ? 0 : $row->financial,
                'Benefit Potensial' => empty($row->potential_benefit) ? 0 : $row->potential_benefit
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Judul Inovasi',
            'Nama Tim',
            'Benefit Real',
            'Benefit Potensial'
        ];
    }
}