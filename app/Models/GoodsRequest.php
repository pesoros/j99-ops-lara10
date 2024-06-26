<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GoodsRequest extends Model
{
    public function scopeGetGoodsRequestlist($query)
    {
        $query = DB::table("ops_goodsrequest AS goodsrequest")
            ->select('goodsrequest.uuid','goodsrequest.numberid','goodsrequest.status')
            ->join("ops_workorder AS workorder", "workorder.uuid", "=", "goodsrequest.workorder_uuid")
            ->orderBy('goodsrequest.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeGetGoodsRequest($query,$uuid)
    {
        $query = DB::table("ops_goodsrequest AS goodsrequest")
            ->select(
                'goodsrequest.*',
                'workorder.numberid AS workorder_numberid',
                'bus.name AS bus_name',
                'bus.registration_number AS registration_number'
            )
            ->join("ops_workorder AS workorder", "workorder.uuid", "=", "goodsrequest.workorder_uuid")
            ->join("v2_bus AS bus", "bus.uuid", "=", "workorder.bus_uuid")
            ->where('goodsrequest.uuid',$uuid)
            ->orderBy('goodsrequest.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetGoodsRequestParts($query,$goodsrequest_uuid)
    {
        $query = DB::table("ops_goodsrequest_parts AS goodsrequest_parts")
            ->select(
                'goodsrequest_parts.*', 
                'scope.name as scopename',
                'scope.code as scopecode',
                'area.code as areacode'
            )
            ->where('goodsrequest_parts.goodsrequest_uuid',$goodsrequest_uuid)
            ->leftJoin("ops_damages AS damage", "damage.uuid", "=", "goodsrequest_parts.damage")
            ->leftJoin("ops_parts_scope AS scope", "scope.uuid", "=", "damage.scope_uuid")
            ->leftJoin("ops_parts_area AS area", "area.uuid", "=", "scope.parts_area_uuid")
            ->orderBy('goodsrequest_parts.id','ASC')
            ->get();

        return $query;
    }

    public function scopeGetWorkorder($query, $uuid)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid','workorder.bus_uuid')
            ->where('workorder.uuid', $uuid)
            ->orderBy('workorder.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeSaveGoodsRequest($query, $data)
    {
        $query = DB::table("ops_goodsrequest")->insert($data);

        return $query;
    }

    public function scopeSaveGoodsRequestParts($query, $data)
    {
        $query = DB::table("ops_goodsrequest_parts")->insert($data);

        return $query;
    }

    public function scopeGetGoodsRequestCount($query)
    {
        $query = DB::table("ops_goodsrequest AS goodsrequest")
            ->select('goodsrequest.count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderby('goodsrequest.count','DESC')
            ->first();

        return $query;
    }

    public function scopeUpdateGoodsRequest($query, $uuid, $data)
    {
        $query = DB::table("ops_goodsrequest")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeUpdateGoodsParts($query, $uuid, $data)
    {
        $query = DB::table("ops_goodsrequest_parts")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }
    
    public function scopeCheckPartsValid($query,$goodsrequest_uuid)
    {
        $query = DB::table("ops_goodsrequest_parts AS goodsrequest_parts")
            ->select('goodsrequest_parts.uuid')
            ->where('goodsrequest_parts.goodsrequest_uuid',$goodsrequest_uuid)
            ->where('goodsrequest_parts.status',0)
            ->get();

        return $query;
    }
}
