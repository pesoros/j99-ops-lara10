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
            ->select('ops_fit_check.*', 'v2_users.name AS created_by_name')
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