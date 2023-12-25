<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoadWarrant extends Model
{
    public function scopeGetRoadWarrantList($query)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.*','bus.name AS busname')
            ->join("v2_bus AS bus", "bus.uuid", "=", "roadwarrant.bus_uuid")
            ->orderBy('roadwarrant.created_at','DESC')
            ->get();

        return $query;
    }

    public function scopeGetRoadWarrant($query, $uuid)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.*','bus.name AS busname')
            ->join("v2_bus AS bus", "bus.uuid", "=", "roadwarrant.bus_uuid")
            ->where('roadwarrant.uuid', $uuid)
            ->orderBy('roadwarrant.created_at','DESC')
            ->first();

        return $query;
    }
}
