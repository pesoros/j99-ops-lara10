<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterData extends Model
{
    public function scopeGetMasterPartsAreaList($query)
    {
        $query = DB::table("ops_parts_area AS partsarea")
            ->select('partsarea.*')
            ->orderBy('partsarea.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsArea($query, $data)
    {
        $query = DB::table("ops_parts_area")->insert($data);

        return $query;
    }

    public function scopeGetMasterPartsArea($query, $uuid)
    {
        $query = DB::table("ops_parts_area")
            ->where('uuid', $uuid)
            ->first();

        return $query;
    }

    public function scopeUpdateMasterPartsArea($query, $uuid, $data)
    {
        $query = DB::table("ops_parts_area")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeRemoveMasterPartsArea($query, $uuid)
    {
        $query = DB::table("ops_parts_area")
            ->where('uuid',$uuid)
            ->delete();

        return $query;
    }

    public function scopeGetMasterPartsScopeList($query)
    {
        $query = DB::table("ops_parts_scope AS partsscope")
            ->select('partsscope.*', 'partsarea.name AS scope_name', 'partsarea.code AS scope_code')
            ->join("ops_parts_area AS partsarea", "partsarea.uuid", "=", "partsscope.parts_area_uuid")
            ->orderBy('partsscope.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsScope($query, $data)
    {
        $query = DB::table("ops_parts_scope")->insert($data);

        return $query;
    }

    public function scopeGetMasterPartsScope($query, $uuid)
    {
        $query = DB::table("ops_parts_scope AS partsscope")
            ->select('partsscope.*', 'partsarea.name AS scope_name', 'partsarea.code AS scope_code')
            ->join("ops_parts_area AS partsarea", "partsarea.uuid", "=", "partsscope.parts_area_uuid")
            ->orderBy('partsscope.id')
            ->where('partsscope.uuid', $uuid)
            ->first();

        return $query;
    }

    public function scopeUpdateMasterPartsScope($query, $uuid, $data)
    {
        $query = DB::table("ops_parts_scope")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeRemoveMasterPartsScope($query, $uuid)
    {
        $query = DB::table("ops_parts_scope")
            ->where('uuid',$uuid)
            ->delete();

        return $query;
    }

    // BUS
    public function scopeGetMasterBusList($query)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid','bus.name','bus.registration_number','bus.brand','bus.model','bus.status')
            ->where('bus.category','AKAP')
            ->orderBy('bus.created_at')
            ->get();

        return $query;
    }

    public function scopeSaveMasterBus($query, $data)
    {
        $query = DB::table("v2_bus")->insert($data);

        return $query;
    }

    public function scopeGetMasterBus($query, $uuid)
    {
        $query = DB::table("v2_bus AS bus")
            ->where('bus.category','AKAP')
            ->where('bus.uuid', $uuid)
            ->first();

        return $query;
    }

    public function scopeUpdateMasterBus($query, $uuid, $data)
    {
        $query = DB::table("v2_bus")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeRemoveMasterbus($query, $uuid)
    {
        $query = DB::table("v2_bus")
            ->where('uuid',$uuid)
            ->delete();

        return $query;
    }

    public function scopeGetMasterClassList($query)
    {
        $query = DB::table("v2_class")
            ->select('id','uuid','name','seat')
            ->whereNotNull('seat_numbers')
            ->orderBy('id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterClass($query, $data)
    {
        $query = DB::table("v2_class")->insert($data);

        return $query;
    }

    public function scopeSaveClassFacilities($query, $data)
    {
        $query = DB::table("v2_class_facilities")->insert($data);

        return $query;
    }

    public function scopeGetMasterClass($query, $uuid)
    {
        $query = DB::table("v2_class AS class")
            ->where('class.uuid', $uuid)
            ->first();

        return $query;
    }

    public function scopeGetMasterClassFacilities($query, $uuid)
    {
        $query = DB::table("v2_class_facilities AS facilities")
            ->where('facilities.class_id', $uuid)
            ->get();

        return $query;
    }

    public function scopeUpdateMasterClass($query, $uuid, $data)
    {
        $query = DB::table("v2_class")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    public function scopeGetMasterFacilitiesList($query)
    {
        $query = DB::table("v2_facilities")
            ->select('id','name')
            ->orderBy('id')
            ->get();

        return $query;
    }

    function scopeGetTripAssignList($query)
    {
        $query = DB::table("trip_assign AS tras")
            ->select('tras.id as trasid','tras.trip as trip','trip.trip_title', 'freg.reg_no')
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->leftJoin("fleet_registration as freg", "freg.id", "=", "tras.fleet_registration_id")
            ->orderBy('trasid','ASC')
            ->get();

        return $query;
    }

    public function scopeSaveBusClass($query, $data)
    {
        $query = DB::table("v2_bus_class")->insert($data);

        return $query;
    }

    public function scopeRemoveClassFacilities($query, $uuid)
    {
        $query = DB::table("v2_class_facilities")
            ->where('class_id',$uuid)
            ->delete();

        return $query;
    }

    public function scopeGetBusClass($query, $uuid)
    {
        $query = DB::table("v2_bus_class AS busclass")
            ->select('class.*')
            ->join("v2_class as class", "class.uuid", "=", "busclass.class_uuid")
            ->select('class.*')
            ->where('busclass.bus_uuid', $uuid)
            ->get();

        return $query;
    }

    public function scopeRemoveBusClass($query, $uuid)
    {
        $query = DB::table("v2_bus_class")
            ->where('bus_uuid',$uuid)
            ->delete();

        return $query;
    }

    public function scopeCheckClassContains($query, $uuid)
    {
        $query = DB::table("v2_bus")
            ->where('class_uuid',$uuid)
            ->get();

        return $query;
    }

    public function scopeRemoveMasterClass($query, $uuid)
    {
        $query = DB::table("v2_class")
            ->where('uuid',$uuid)
            ->delete();

        return $query;
    }
}
