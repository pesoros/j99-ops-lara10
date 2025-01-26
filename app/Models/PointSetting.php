<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointSetting extends Model
{
    public function scopeGetPointList($query)
    {
        $query = DB::table("fleet_type AS ftp")
            ->select(
                'ftp.id as type_id',
                'ftp.type',
                'ups.uuid as ups_uuid',
                'ups.percentage',
            )
            ->leftJoin("v2_userpoint_setting AS ups", "ups.fleet_type", "=", "ftp.id")
            ->orderBy('ftp.id')
            ->get();

        return $query;
    }

    public function scopeGetFleet($query, $fleetid)
    {
        $query = DB::table("fleet_type AS ftp")
            ->select(
                'ftp.id as type_id',
                'ftp.type',
            )
            ->where('ftp.id', $fleetid)
            ->first();

        return $query;
    }

    public function scopeGetPoint($query, $fleetid)
    {
        $query = DB::table("v2_userpoint_setting AS ups")
            ->select(
                'ups.uuid as ups_uuid',
                'ups.percentage',
            )
            ->where('ups.fleet_type', $fleetid)
            ->first();

        return $query;
    }

    public function scopeSaveNewPoint($query, $data)
    {
        $query = DB::table("v2_userpoint_setting")->insert($data);

        return $query;
    }

    public function scopeUpdatePoint($query, $fleetid, $data)
    {
        $query = DB::table("v2_userpoint_setting")
            ->where('fleet_type',$fleetid)
            ->update($data);

        return $query;
    }
}
