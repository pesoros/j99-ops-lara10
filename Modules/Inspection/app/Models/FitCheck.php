<?php

namespace Modules\Inspection\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FitCheck extends Model
{
    protected $table = 'ops_fit_check';

    public function scopeGetList($query, $filters = [])
    {
        $q = DB::table('ops_fit_check')->orderBy('id', 'desc');

        if (!empty($filters['date'])) {
            $q->whereDate('date', $filters['date']);
        }

        return $q->get();
    }

    public function scopeGetById($query, $id)
    {
        return DB::table('ops_fit_check')->where('id', $id)->first();
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