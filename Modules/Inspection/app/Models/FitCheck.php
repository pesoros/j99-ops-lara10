<?php

namespace Modules\Inspection\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FitCheck extends Model
{
    protected $table = 'ops_fit_check';

    public function scopeGetList($query, $filters = [])
    {
        $q = DB::table('ops_fit_check')
            ->leftJoin('v2_users', 'v2_users.id', '=', 'ops_fit_check.created_by')
            ->select(
                'ops_fit_check.id',
                'ops_fit_check.driver_id',
                'ops_fit_check.driver_name',
                'ops_fit_check.bus_id',
                'ops_fit_check.bus_unit',
                'ops_fit_check.route_id',
                'ops_fit_check.route',
                'ops_fit_check.date',
                'ops_fit_check.work_day_count',
                'ops_fit_check.rest_hours_last_12h',
                'ops_fit_check.is_sick',
                'ops_fit_check.under_medication',
                'ops_fit_check.blood_pressure_systolic',
                'ops_fit_check.blood_pressure_diastolic',
                'ops_fit_check.body_temperature',
                'ops_fit_check.heart_rate_status',
                'ops_fit_check.fit_to_work',
                // driver_signature sengaja tidak diambil di list: isinya base64
                // dan tidak pernah dirender di tabel.
                'ops_fit_check.driver_signed_at',
                'ops_fit_check.created_by',
                'ops_fit_check.created_at',
                'v2_users.name AS created_by_name'
            )
            ->orderBy('ops_fit_check.id', 'desc');

        if (!empty($filters['date'])) {
            $q->whereDate('ops_fit_check.date', $filters['date']);
        }

        return $q->get();
    }

    public function scopeGetById($query, $id)
    {
        return DB::table('ops_fit_check')
            ->leftJoin('v2_users', 'v2_users.id', '=', 'ops_fit_check.created_by')
            ->select('ops_fit_check.*', 'v2_users.name AS created_by_name')
            ->where('ops_fit_check.id', $id)
            ->first();
    }

    public function scopeSaveFitCheck($query, $data)
    {
        return DB::table('ops_fit_check')->insertGetId($data);
    }

    public function scopeUpdateById($query, $id, $data)
    {
        return DB::table('ops_fit_check')->where('id', $id)->update($data);
    }

    public function scopeDeleteById($query, $id)
    {
        return DB::table('ops_fit_check')->where('id', $id)->delete();
    }
}