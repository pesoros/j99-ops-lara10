<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Accurate extends Model
{
    public function scopeGetToken($query, $role)
    {
        $query = DB::table("token_credential")
            ->select('value AS token','refresh_token','expires_at')
            ->where('vendor','accurate')
            ->where('role',$role)
            ->first();

        return $query;
    }

    public function scopeUpdateAccurateToken($query, $role, $data)
    {
        $query = DB::table("token_credential")
            ->where('vendor','accurate')
            ->where('role',$role)
            ->update($data);

        return $query;
    }

    public function scopeGetSales($query)
    {
        $query = DB::table("tkt_booking_head as thead")
            ->select(
                'thead.id',
                'thead.booking_code',
                'thead.accurate_soid',
                'refund.tkt_booking_id_no',
                'thead_ref.accurate_soid as ref_soid',
            )
            ->leftJoin("tkt_refund AS refund", "refund.code_related", "=", "thead.booking_code")
            ->leftJoin("tkt_booking_head AS thead_ref", "thead_ref.booking_code", "=", "refund.tkt_booking_id_no")
            ->whereIn('thead.payment_status', [1, 2])
            ->where('thead.accurate_soid', '!=', NULL)
            ->orderBy('thead.id', 'DESC')
            ->take(200)
            ->get();

        return $query;
    }
}
