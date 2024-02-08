<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseRequest extends Model
{
    public function scopeGetPurchaseRequestList($query)
    {
        $query = DB::table("ops_purchaserequest AS pr")
            ->select('pr.*')
            ->orderBy('pr.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeGetPurchaseRequest($query,$uuid)
    {
        $query = DB::table("ops_purchaserequest AS pr")
            ->select('pr.*')
            ->where('pr.uuid',$uuid)
            ->first();

        return $query;
    }

    public function scopeGetPurchaseRequestParts($query,$purchaserequest_uuid)
    {
        $query = DB::table("ops_purchaserequest_parts AS pr_parts")
            ->select('pr_parts.*')
            ->where('pr_parts.purchaserequest_uuid',$purchaserequest_uuid)
            ->orderBy('pr_parts.id','ASC')
            ->get();

        return $query;
    }

    public function scopeGetPurchaseRequestCount($query)
    {
        $query = DB::table("ops_purchaserequest AS pr")
            ->select('pr.count')
            ->whereYear('pr.created_at', Carbon::now()->year)
            ->whereMonth('pr.created_at', Carbon::now()->month)
            ->orderby('pr.count','DESC')
            ->first();

        return $query;
    }

    public function scopeSavePurchaseRequest($query, $data)
    {
        $query = DB::table("ops_purchaserequest")->insert($data);

        return $query;
    }

    public function scopeSavePurchaseRequestParts($query, $data)
    {
        $query = DB::table("ops_purchaserequest_parts")->insert($data);

        return $query;
    }
}
