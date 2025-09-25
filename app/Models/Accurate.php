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
        $sql = "
            SELECT 
                thead.id,
                thead.booking_code,
                thead.accurate_soid,
                refund.tkt_booking_id_no,
                thead_ref.accurate_soid AS ref_soid
            FROM tkt_booking_head AS thead
            JOIN tkt_booking AS tb ON tb.booking_code = thead.booking_code
            LEFT JOIN tkt_refund AS refund 
                ON refund.code_related = thead.booking_code
            LEFT JOIN tkt_booking_head AS thead_ref 
                ON thead_ref.booking_code = refund.tkt_booking_id_no
            WHERE thead.payment_status IN (1, 2)
            AND (thead.accurate_soid = 0 OR thead.accurate_soid IS NULL)
            AND tb.booking_date  >= '2025-09-01 00:00:00'
            AND tb.booking_date  <= NOW()
            ORDER BY thead.id ASC
            LIMIT 1000
        ";

        return DB::select($sql);
    }

    public function scopeGetManifest($query)
    {
        $sql = "
            SELECT 
                mn.id AS manifestId,
                mn.uuid AS manifestUuid,
                rw.numberid,
                mn.trip_date,
                mn.isSynced,
                tr.trip_title,
                fr.reg_no,
                bus.name as busname
            FROM manifest AS mn
            JOIN ops_roadwarrant AS rw 
                ON rw.uuid = mn.roadwarrant_uuid
            LEFT JOIN trip_assign AS tras 
                ON tras.id = mn.trip_assign
            LEFT JOIN trip AS tr 
                ON tr.trip_id = tras.trip
            LEFT JOIN fleet_registration AS fr
                ON fr.id = tras.fleet_registration_id
            LEFT JOIN v2_bus AS bus
                ON bus.uuid = mn.fleet
            WHERE mn.status = '2'
            AND mn.uuid IS NOT NULL
            AND mn.isSynced = 0
            AND mn.trip_date >= '2025-09-01 00:00:00'
            AND mn.trip_date <= NOW()
            ORDER BY mn.id ASC
            LIMIT 300
        ";

        return DB::select($sql);
    }
}
