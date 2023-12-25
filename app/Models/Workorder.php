<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Workorder extends Model
{
    public function scopeGetWorkorderList($query)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid','workorder.status','bus.name AS busname','complaint.description')
            ->join("ops_complaint AS complaint", "complaint.workorder_uuid", "=", "workorder.uuid")
            ->join("v2_bus AS bus", "bus.uuid", "=", "complaint.bus_uuid")
            ->orderBy('workorder.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeGetWorkorder($query, $uuid)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select(
                'workorder.uuid',
                'workorder.numberid',
                'workorder.status',
                'workorder.created_at',
                'complaint.uuid AS complaint_uuid',
                'complaint.description',
                'bus.name AS busname'
            )
            ->join("ops_complaint AS complaint", "complaint.workorder_uuid", "=", "workorder.uuid")
            ->join("v2_bus AS bus", "bus.uuid", "=", "complaint.bus_uuid")
            ->where('workorder.uuid', $uuid)
            ->orderBy('workorder.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetActionList($query)
    {
        $query = DB::table("ops_damages_action AS action")
            ->select('action.*')
            ->orderBy('action.id','ASC')
            ->get();

        return $query;
    }

    public function scopeGetComplaintDamages($query, $uuid)
    {
        $query = DB::table("ops_damages AS damage")
            ->select(
                'damage.uuid',
                'damage.description',
                'damage.action_status',
                'damage.action_description',
                'scope.name AS scopename',
                'scope.code AS scopecode',
                'area.code AS areacode'
            )
            ->join("ops_parts_scope AS scope", "scope.uuid", "=", "damage.scope_uuid")
            ->join("ops_parts_area AS area", "area.uuid", "=", "scope.parts_area_uuid")
            ->where('damage.complaint_uuid',$uuid)
            ->orderBy('damage.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeUpdateWorkorder($query, $uuid, $data)
    {
        $query = DB::table("ops_workorder")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeUpdateDamage($query, $uuid, $data)
    {
        $query = DB::table("ops_damages")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }
}
