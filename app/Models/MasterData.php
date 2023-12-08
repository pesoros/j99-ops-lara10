<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterData extends Model
{
    public function scopeGetMasterBusList($query)
    {
        $query = DB::table("v2_bus")
            ->select('uuid','name','seat','registration_number','brand','model','status')
            ->orderBy('created_at')
            ->get();

        return $query;
    }

    public function scopeGetMasterFacilities($query)
    {
        $query = DB::table("v2_facilities")
            ->select('id','name')
            ->orderBy('id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterBus($query, $data)
    {
        $query = DB::table("v2_bus")->insertGetId($data);

        return $query;
    }

    public function scopeSaveBusFacilities($query, $data)
    {
        $query = DB::table("v2_bus_facilities")->insert($data);

        return $query;
    }

    public function scopesaveFacility($query, $data)
    {
        $query = DB::table("v2_facilities")->insert($data);

        return $query;
    }
}
