<?php

namespace Modules\Employee\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CrewTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'phone',
            'email',
            'position',
            'bank_name',
            'bank_number',
            'idcard',
            'address',
            'blood_group',
            'city',
            'zipcode',
        ];
    }
}
