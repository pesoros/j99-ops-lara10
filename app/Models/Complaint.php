<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Complaint extends Model
{
    public function scopeGetComplaintList($query)
    {
        $query = DB::table("ops_complaint AS complaint")
            ->select('complaint.uuid','complaint.description','complaint.workorder_uuid','bus.name AS busname')
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
        $query = DB::table("ops_damages")->insert($data);

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

    public function scopeGetComplaint($query, $uuid)
    {
        $query = DB::table("ops_complaint AS complaint")
            ->select('complaint.*','bus.name AS busname','workorder.numberid AS workorder_numberid')
            ->join("v2_bus AS bus", "bus.uuid", "=", "complaint.bus_uuid")
            ->leftJoin("ops_workorder AS workorder", "workorder.uuid", "=", "complaint.workorder_uuid")
            ->where('complaint.uuid',$uuid)
            ->orderBy('complaint.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetComplaintDamages($query, $uuid)
    {
        $query = DB::table("ops_damages AS damage")
            ->select('damage.uuid','damage.scope_uuid','damage.description','scope.name AS scopename','scope.code AS scopecode','area.code AS areacode')
            ->join("ops_parts_scope AS scope", "scope.uuid", "=", "damage.scope_uuid")
            ->join("ops_parts_area AS area", "area.uuid", "=", "scope.parts_area_uuid")
            ->where('damage.complaint_uuid',$uuid)
            ->orderBy('damage.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeUpdateComplaint($query, $uuid, $data)
    {
        $query = DB::table("ops_complaint")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeRemoveDamages($query, $complaint_uuid)
    {
        $query = DB::table("ops_damages")
            ->where('complaint_uuid',$complaint_uuid)
            ->delete();

        return $query;
    }

    public function scopeSaveWorkorder($query, $data)
    {
        $query = DB::table("ops_workorder")->insert($data);

        return $query;
    }

    public function scopeGetWorkorderCount($query)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderby('workorder.count','DESC')
            ->first();

        return $query;
    }
}
