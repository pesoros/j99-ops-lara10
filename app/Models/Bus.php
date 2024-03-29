<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bus extends Model
{
    public function scopeGetBusList($query)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.*')
            ->orderBy('bus.created_at')
            ->get();

        return $query;
    }

    public function scopeGetBusActiveList($query)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.*')
            ->where('status',1)
            ->orderBy('bus.created_at')
            ->get();

        return $query;
    }

    public function scopeGetBus($query, $bus_uuid)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.*')
            ->where('bus.uuid', $bus_uuid)
            ->orderBy('bus.created_at')
            ->first();

        return $query;
    }

    public function scopeUpdateBus($query, $uuid, $data)
    {
        $query = DB::table("v2_bus")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }
}
