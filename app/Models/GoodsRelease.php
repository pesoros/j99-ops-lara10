<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GoodsRelease extends Model
{
    public function scopeGetGoodsReleaselist($query)
    {
        $query = DB::table("ops_goodsrelease AS goodsrelease")
            ->select('goodsrelease.uuid','goodsrelease.numberid','goodsrelease.status')
            ->orderBy('goodsrelease.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeGetGoodsRelease($query,$uuid)
    {
        $query = DB::table("ops_goodsrelease AS goodsrelease")
            ->where('goodsrelease.uuid',$uuid)
            ->orderBy('goodsrelease.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetGoodsReleaseParts($query,$goodsrelease_uuid)
    {
        $query = DB::table("ops_goodsrelease_parts AS goodsrelease_parts")
            ->select(
                'goodsrelease_parts.*',
            )
            ->where('goodsrelease_parts.goodsrelease_uuid',$goodsrelease_uuid)
            ->orderBy('goodsrelease_parts.id','ASC')
            ->get();

        return $query;
    }

    public function scopeSaveGoodsRelease($query, $data)
    {
        $query = DB::table("ops_goodsrelease")->insert($data);

        return $query;
    }

    public function scopeSaveGoodsReleaseParts($query, $data)
    {
        $query = DB::table("ops_goodsrelease_parts")->insert($data);

        return $query;
    }

    public function scopeGetGoodsReleaseCount($query)
    {
        $query = DB::table("ops_goodsrelease AS goodsrelease")
            ->select('goodsrelease.count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderby('goodsrelease.count','DESC')
            ->first();

        return $query;
    }

    public function scopeUpdateGoodsRelease($query, $uuid, $data)
    {
        $query = DB::table("ops_goodsrelease")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeUpdateGoodsParts($query, $uuid, $data)
    {
        $query = DB::table("ops_goodsrelease_parts")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }
    
    public function scopeCheckPartsValid($query,$goodsrelease_uuid)
    {
        $query = DB::table("ops_goodsrelease_parts AS goodsrelease_parts")
            ->select('goodsrelease_parts.uuid')
            ->where('goodsrelease_parts.goodsrelease_uuid',$goodsrelease_uuid)
            ->where('goodsrelease_parts.status',0)
            ->get();

        return $query;
    }
}
