<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{
    public function scopeGetManifestList($query)
    {
        $query = DB::table('manifest as manif')
            ->select(
                'manif.id',
                'manif.uuid',
                'manif.status',
                'manif.email_assign',
                'manif.trip_date',
                'manif.trip_assign',
                'tr.trip_title',
                'freg.reg_no as fleetname',
                'bus.name as busname',
            )
            ->leftJoin('trip_assign as tras', 'manif.trip_assign', 'tras.id')
            ->leftJoin('trip as tr', 'tras.trip', 'tr.trip_id')
            ->leftJoin("fleet_registration as freg", "freg.id", "=", "manif.fleet")
            ->leftJoin("v2_bus as bus", "bus.uuid", "=", "manif.fleet")
            ->orderBy('manif.id', 'desc')
            ->take(300)
            ->get();

        return $query;
    }

    public function scopeGetManifest($query, $id)
    {
        $query = DB::table('manifest as manif')
            ->select(
                'manif.id',
                'manif.uuid',
                'manif.status',
                'manif.trip_assign',
                'manif.trip_date',
                'trip.trip_title',
                'emp1.first_name', 
                'emp1.first_name as driver1_name', 
                'emp1.second_name as driver1_lastname', 
                'emp2.first_name as driver2_name', 
                'emp2.second_name as driver2_lastname',
                'emp3.first_name as codriver_name', 
                'emp3.second_name as codriver_lastname',
                'freg.reg_no as fleetname',
                'bus.name as busname',
                'tras.allowance',
            )
            ->join('trip_assign as tras', 'tras.id', 'manif.trip_assign')
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->leftJoin("ops_roadwarrant as rw", "manif.uuid", "=", "rw.manifest_uuid")
            ->leftJoin("employee_history as emp1", "emp1.id", "=", "rw.driver_1")
            ->leftJoin("employee_history as emp2", "emp2.id", "=", "rw.driver_2")
            ->leftJoin("employee_history as emp3", "emp3.id", "=", "rw.codriver")
            ->leftJoin("fleet_registration as freg", "freg.id", "=", "manif.fleet")
            ->leftJoin("v2_bus as bus", "bus.uuid", "=", "manif.fleet")
            ->where('manif.id', $id)
            ->first();

        return $query;
    }

    public static function getPassengerList($tripAssign, $booking_date)
    {
        $query = "
            SELECT 
                tps.name,
                tps.ticket_number,
                tbook.booking_date,
                tbook.price,
                tbook.adult,
                tps.seat_number,
                tps.phone,
                tps.food_served,
                tbook.pickup_trip_location,
                tbook.drop_trip_location,
                tbookhead.booking_code,
                tbookhead.created_at,
                tbookhead.note AS note,
                tpoint.dep_time,
                tpoint.arr_time,
                ftype.type AS class,
                resto.food_name,
                paymentreg.payment_channel_code AS channel,
                arrival_loc.ttpg_id,
                rem.is_succeed AS reminderSucceed,
                rem.created_at AS reminder_created_at
            FROM trip_assign AS tras
            JOIN tkt_booking AS tbook
                ON tbook.trip_id_no = tras.trip
            JOIN tkt_booking_head AS tbookhead
                ON tbookhead.booking_code = tbook.booking_code
            JOIN tkt_passenger_pcs AS tps
                ON tps.booking_id = tbook.id_no
            JOIN fleet_type AS ftype
                ON ftype.id = tps.fleet_type
            LEFT JOIN trip_point AS tpoint
                ON tpoint.trip_assign_id = tras.id
                AND tpoint.dep_point = tbook.pickup_trip_location
                AND tpoint.arr_point = tbook.drop_trip_location
            LEFT JOIN resto_menu AS resto
                ON resto.id = tps.food
            LEFT JOIN payment_registration AS paymentreg
                ON paymentreg.booking_code = tbookhead.booking_code
            LEFT JOIN trip_location AS arrival_loc
                ON arrival_loc.name = tbook.drop_trip_location

            LEFT JOIN (
                SELECT r1.*
                FROM tkt_reminder r1
                INNER JOIN (
                    SELECT ticket_number, MAX(created_at) AS max_created_at
                    FROM tkt_reminder
                    GROUP BY ticket_number
                ) r2 ON r1.ticket_number = r2.ticket_number
                    AND r1.created_at = r2.max_created_at
            ) AS rem
                ON rem.ticket_number = tps.ticket_number

            WHERE tbookhead.payment_status = 1
            AND tps.cancel = 0
            AND tpoint.trip_assign_id = :tripAssign
            AND DATE(tbook.booking_date) = :booking_date
            ORDER BY tps.seat_number ASC
        ";

        return collect(DB::select($query, [
            'tripAssign' => $tripAssign,
            'booking_date' => $booking_date,
        ]));
    }

    public function scopeGetExpensesList($query, $id)
    {
        $query = DB::table('manifest as manif')
            ->select(
                'expense.*',
                'tras.allowance'
            )
            ->join('trip_assign as tras', 'tras.id', '=', 'manif.trip_assign')
            ->join('ops_roadwarrant as roadwarrant', 'roadwarrant.manifest_uuid', '=', 'manif.uuid')
            ->join('trip_expenses as expense', 'expense.roadwarrant_uuid', '=', 'roadwarrant.uuid')
            ->where('manif.id',$id)
            ->orderBy('expense.id', 'ASC')
            ->get();

        return $query;
    }

    public function scopeUpdateManifest($query, $id, $data)
    {
        $query = DB::table("manifest")
            ->where('id',$id)
            ->update($data);

        return $query;
    }

    public function scopeGetExpense($query, $id)
    {
        $query = DB::table("trip_expenses")
            ->where('id',$id)
            ->first();

        return $query;
    }

    public function scopeUpdateExpense($query, $id, $data)
    {
        $query = DB::table("trip_expenses")
            ->where('id',$id)
            ->update($data);

        return $query;
    }

    public function scopeGetPoint($query, $id)
    {
        $query = DB::table("trip_point")
            ->where('trip_assign_id',$id)
            ->groupBy('dep_point')
            ->get();

        return $query;
    }
}
