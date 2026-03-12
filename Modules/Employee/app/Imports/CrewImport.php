<?php

namespace Modules\Employee\app\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CrewImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Employee::saveCrew([
            'first_name'     => $row['first_name'] ?? '',
            'second_name'    => $row['last_name'] ?? '',
            'position'       => $row['position'] ?? '',
            'phone_no'       => $row['phone'] ?? '',
            'email_no'       => $row['email'] ?? '',
            'bank_name'      => $row['bank_name'] ?? '',
            'bank_number'    => $row['bank_number'] ?? '',
            'document_id'    => $row['idcard'] ?? null,
            'address_line_1' => $row['address'] ?? null,
            'blood_group'    => $row['blood_group'] ?? null,
            'city'           => $row['city'] ?? null,
            'zip'            => $row['zipcode'] ?? null,
            'picture'        => '-',
            'document_pic'   => '-',
        ]);
    }
}
