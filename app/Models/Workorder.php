<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Workorder extends Model
{
    public function scopeGetWorkorderList($query)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid','workorder.status','bus.name AS busname','user.name AS creator')
            ->join("v2_bus AS bus", "bus.uuid", "=", "workorder.bus_uuid")
            ->join("v2_users AS user", "user.uuid", "=", "workorder.created_by")
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
                'workorder.bus_uuid',
                'bus.name AS busname',
                'user.name AS creator'
            )
            ->join("v2_bus AS bus", "bus.uuid", "=", "workorder.bus_uuid")
            ->join("v2_users AS user", "user.uuid", "=", "workorder.created_by")
            ->where('workorder.uuid', $uuid)
            ->orderBy('workorder.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetWorkorderDamages($query, $uuid)
    {
        $query = DB::table("ops_damages AS damage")
            ->select(
                'damage.uuid',
                'damage.scope_uuid',
                'damage.description',
                'damage.action_status',
                'damage.action_description',
                'damage.created_at',
                'scope.name AS scopename',
                'scope.code AS scopecode',
                'area.code AS areacode',
                'wodam.uuid AS wodam_uuid',
                'damac.name AS action_name'
            )
            ->join("ops_parts_scope AS scope", "scope.uuid", "=", "damage.scope_uuid")
            ->join("ops_parts_area AS area", "area.uuid", "=", "scope.parts_area_uuid")
            ->join("ops_workorder_damages AS wodam", "wodam.damage_uuid", "=", "damage.uuid")
            ->join("ops_workorder AS wo", "wo.uuid", "=", "wodam.workorder_uuid")
            ->leftJoin("ops_damages_action AS damac", "damac.id", "=", "damage.action_status")
            ->where('wo.uuid',$uuid)
            ->get();

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

    public function scopeGetPartsRequest($query, $workorder_uuid, $damage_uuid)
    {
        $query = DB::table("ops_goodsrequest_parts AS goodparts")
            ->select('goodparts.part_id','goodparts.part_name','goodparts.qty')
            ->where('goodparts.status', 0)
            ->where('good.workorder_uuid', $workorder_uuid)
            ->where('goodparts.damage', $damage_uuid)
            ->join("ops_goodsrequest AS good", "good.uuid", "=", "goodparts.goodsrequest_uuid")
            ->orderBy('goodparts.id','ASC')
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
