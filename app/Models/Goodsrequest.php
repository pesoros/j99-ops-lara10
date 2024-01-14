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
            ->select('goodsrequest.uuid','goodsrequest.numberid','goodsrequest.description','goodsrequest.status')
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
                'workorder.numberid AS workorder_numberid'
            )
            ->join("ops_workorder AS workorder", "workorder.uuid", "=", "goodsrequest.workorder_uuid")
            ->where('goodsrequest.uuid',$uuid)
            ->orderBy('goodsrequest.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetGoodsRequestParts($query,$goodsrequest_uuid)
    {
        $query = DB::table("ops_goodsrequest_parts AS goodsrequest_parts")
            ->select('goodsrequest_parts.*')
            ->where('goodsrequest_parts.goodsrequest_uuid',$goodsrequest_uuid)
            ->orderBy('goodsrequest_parts.id','ASC')
            ->get();

        return $query;
    }

    public function scopeGetWorkorder($query,$uuid)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid')
            ->where('workorder.uuid',$uuid)
            ->orderBy('workorder.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetWorkorderList($query)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid')
            ->where('workorder.status','!=',2)
            ->orderBy('workorder.created_at','DESC')
            ->get();

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
