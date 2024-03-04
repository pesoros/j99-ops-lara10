<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{
    public function scopeGetManifestList($query)
    {
        $query = DB::table('manifest as mn')
        ->select(
            'mn.id',
            'mn.uuid',
            'mn.status',
            'mn.email_assign',
            'mn.trip_date',
            'mn.trip_assign',
            'tr.trip_title',
            'fr.reg_no'
        )
        ->leftJoin('trip_assign as tras', 'mn.trip_assign', 'tras.id')
        ->leftJoin('trip as tr', 'tras.trip', 'tr.trip_id')
        ->leftJoin('fleet_registration as fr', 'fr.id', 'mn.fleet')
        ->orderBy('mn.id', 'desc')
        ->take(100)
        ->get();

        return $query;
    }
}
