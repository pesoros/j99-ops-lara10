<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Complaint extends Model
{
    public function scopeGetDamagesCount($query, $bus_uuid)
    {
        $query = DB::table("ops_damages AS damage")
            ->selectRaw('count(damage.id) AS counter')
            ->where('damage.bus_uuid',$bus_uuid)
            ->where('damage.action_status',1)
            ->first();

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

    public function scopeGetDamages($query, $bus_uuid)
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
                'user.name AS creator'
                )
            ->join("ops_parts_scope AS scope", "scope.uuid", "=", "damage.scope_uuid")
            ->join("ops_parts_area AS area", "area.uuid", "=", "scope.parts_area_uuid")
            ->join("v2_users AS user", "user.uuid", "=", "damage.created_by")
            ->where('damage.bus_uuid',$bus_uuid)
            ->where('damage.action_status',1)
            ->orderBy('damage.created_at','ASC')
            ->get();

        return $query;
    }

    public function scopeRemoveDamages($query, $uuid)
    {
        $query = DB::table("ops_damages")
            ->where('uuid', $uuid)
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

    public function scopeGetWorkorderActive($query, $bus_uuid)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.numberid')
            ->where('workorder.bus_uuid', $bus_uuid)
            ->where('status','!=',2)
            ->first();

        return $query;
    }

    public function scopeGetValidateDamage($query, $bus_uuid, $scope_uuid)
    {
        $query = DB::table("ops_damages AS damage")
            ->select('damage.uuid')
            ->where('damage.bus_uuid', $bus_uuid)
            ->where('damage.scope_uuid', $scope_uuid)
            ->where('is_closed', 0)
            ->get();

        return $query;
    }
}
