<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopeGetWorkorder($query)
    {
        $query = DB::table("ops_workorder AS workorder")
            ->select('workorder.uuid','workorder.numberid')
            ->where('workorder.status','!=',2)
            ->orderBy('workorder.created_at','DESC')
            ->get();

        return $query;
    }
}
