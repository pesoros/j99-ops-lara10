<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterData extends Model
{
    public function scopeGetMasterPartsAreaList($query)
    {
        $query = DB::table("ops_parts_area AS partscope")
            ->select('partscope.*')
            ->orderBy('partscope.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsArea($query, $data)
    {
        $query = DB::table("ops_parts_area")->insert($data);

        return $query;
    }

    public function scopeGetMasterPartsScopeList($query)
    {
        $query = DB::table("ops_parts_scope AS partsscope")
            ->select('partsscope.*', 'partscope.name AS scope_name', 'partscope.code AS scope_code')
            ->join("ops_parts_area AS partscope", "partscope.uuid", "=", "partsscope.parts_area_uuid")
            ->orderBy('partsscope.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsScope($query, $data)
    {
        $query = DB::table("ops_parts_scope")->insert($data);

        return $query;
    }
}
