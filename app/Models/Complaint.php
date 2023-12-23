<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Complaint extends Model
{
    public function scopeGetComplaintList($query)
    {
        $query = DB::table("ops_complaint AS complaint")
            ->select('complaint.description','bus.name AS busname')
            ->join("v2_bus AS bus", "bus.uuid", "=", "complaint.bus_uuid")
            ->orderBy('complaint.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeSaveComplaint($query, $data)
    {
        $query = DB::table("ops_complaint")->insert($data);

        return $query;
    }

    public function scopeSaveDamages($query, $data)
    {
        $query = DB::table("ops_complaint_damages")->insert($data);

        return $query;
    }

    public function scopeGetBusList($query)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid','bus.name','bus.registration_number','bus.brand','bus.model','bus.status')
            ->orderBy('bus.created_at')
            ->get();

        return $query;
    }

    public function scopeGetPartsScope($query)
    {
        $query = DB::table("ops_parts_scope AS partsscope")
            ->select('partsscope.*', 'partsarea.name AS scope_name', 'partsarea.code AS scope_code')
            ->join("ops_parts_area AS partsarea", "partsarea.uuid", "=", "partsscope.parts_area_uuid")
            ->orderBy('partsscope.id')
            ->get();

        return $query;
    }
}
