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

    public function scopeGetActionList($query)
    {
        $query = DB::table("ops_damages_action AS action")
            ->select('action.*')
            ->orderBy('action.id','ASC')
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
