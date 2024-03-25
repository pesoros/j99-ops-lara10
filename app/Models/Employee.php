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
        $query = DB::table("employee_attendance")
            ->select('*')
            ->where('employee_id', $uuid)
            ->orderBy('id', 'desc')
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
}
