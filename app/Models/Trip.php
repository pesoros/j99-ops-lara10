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

    public function scopeGetPassengerList($query, $tripAssign, $booking_date)
    {
        $query = DB::table('trip_assign as tras')
            ->select(
                'tps.name',
                'tps.ticket_number',
                'tbook.booking_date',
                'tbook.price',
                'tbook.adult',
                'tps.seat_number',
                'tps.phone',
                'tps.food_served',
                'tbook.pickup_trip_location',
                'tbook.drop_trip_location',
                'tbookhead.booking_code',
                'tbookhead.created_at',
                'tbookhead.note as note',
                'tpoint.dep_time',
                'tpoint.arr_time',
                'ftype.type as class',
                'resto.food_name',
                'paymentreg.payment_channel_code as channel',
                'arrival_loc.ttpg_id',
                'rem.is_succeed as reminderSucceed'
            )
            ->join('tkt_booking as tbook', 'tbook.trip_id_no', 'tras.trip')
            ->join('tkt_booking_head AS tbookhead', 'tbookhead.booking_code', '=', 'tbook.booking_code')
            ->join('tkt_passenger_pcs as tps', 'tps.booking_id', 'tbook.id_no')
            ->join('fleet_type as ftype', 'ftype.id', '=', 'tps.fleet_type')
            ->leftJoin('trip_point AS tpoint', function($leftJoin) {
                $leftJoin->on('tpoint.trip_assign_id', '=', 'tras.id')
                    ->on('tpoint.dep_point', '=', 'tbook.pickup_trip_location')
                    ->on('tpoint.arr_point', '=', 'tbook.drop_trip_location');
            })
            ->leftJoin('resto_menu as resto', 'resto.id', '=', 'tps.food')
            ->leftJoin('payment_registration AS paymentreg', 'paymentreg.booking_code', '=', 'tbookhead.booking_code')
            ->leftJoin('trip_location as arrival_loc', 'arrival_loc.name', '=', 'tbook.drop_trip_location')
            ->leftJoin('tkt_reminder as rem', 'rem.ticket_number', '=', 'tps.ticket_number')
            ->where('tbookhead.payment_status', 1)
            ->where('tps.cancel', 0)
            ->where('tras.id',$tripAssign)
            ->whereDate('tbook.booking_date',$booking_date)
            ->orderBy('tps.seat_number','ASC')
            ->get();

        return $query;
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
