<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Complaint extends Model
{
    public function scopeGetComplaintList($query)
    {
        $query = DB::table("ops_parts_area AS partarea")
            ->select('partarea.*')
            ->orderBy('partarea.id')
            ->get();

        return $query;
    }

    public function scopeSaveComplaint($query, $data)
    {
        $query = DB::table("ops_parts_area")->insert($data);

        return $query;
    }

    public function scopeGetMasterPartsAreaList($query)
    {
        $query = DB::table("ops_parts_area AS partsarea")
            ->select('partsarea.*', 'partarea.name AS scope_name', 'partarea.code AS scope_code')
            ->join("ops_parts_area AS partarea", "partarea.uuid", "=", "partsarea.parts_area_uuid")
            ->orderBy('partsarea.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterpartsarea($query, $data)
    {
        $query = DB::table("ops_parts_scope")->insert($data);

        return $query;
    }
}
