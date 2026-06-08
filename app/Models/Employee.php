<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    public function scopeGetCrewList($query)
    {
        $query = DB::table("employee_history")
            ->select('*')
            ->orderBy('id')
            ->get();

        return $query;
    }

    public function scopeGetCrew($query, $uuid)
    {
        $query = DB::table("employee_history")
            ->where('id', $uuid)
            ->first();

        return $query;
    }

    public function scopeGetCrewAttendance($query, $uuid)
    {
        $query = DB::table("employee_attendance AS attendance")
            ->select('attendance.*', 'rw.numberid AS spj_number')
            ->leftJoin("ops_roadwarrant AS rw", "rw.uuid", "=", "attendance.roadwarrant_uuid")
            ->where('attendance.employee_id', $uuid)
            ->orderBy('attendance.id', 'desc')
            ->get();

        return $query;
    }

    public function scopeGetPosition($query)
    {
        $query = DB::table("employee_type")
            ->get();

        return $query;
    }

    public function scopeSaveCrew($query, $data)
    {
        $query = DB::table("employee_history")->insert($data);

        return $query;
    }

    public function scopeUpdateCrew($query, $uuid, $data)
    {
        $query = DB::table("employee_history")
            ->where('id',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeDeleteCrew($query, $uuid)
    {
        $query = DB::table("employee_history")
            ->where('id', $uuid)
            ->delete();

        return $query;
    }

    public function scopeLogDeleteCrew($query, $data)
    {
        $query = DB::table("employee_delete_log")->insert($data);

        return $query;
    }

    public function scopeGetCrewDrivingHistory($query, $uuid)
    {
        $query = DB::table("ops_roadwarrant_driverlog AS log")
            ->select(
                'log.*',
                'rw.status',
                'rw.numberid AS spj_number',
                'bus.name AS busname',
                'bus.registration_number',
            )
            ->leftJoin("ops_roadwarrant AS rw", "rw.uuid", "=", "log.roadwarrant_uuid")
            ->leftJoin("v2_bus AS bus", "bus.uuid", "=", "rw.bus_uuid")
            ->where('log.driver_id', $uuid)
            ->orderBy('log.start_at', 'desc')
            ->get();

        return $query;
    }
}
