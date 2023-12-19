<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterData extends Model
{
    public function scopeGetMasterPartsScopeList($query)
    {
        $query = DB::table("ops_parts_scope AS partscope")
            ->select('partscope.*')
            ->orderBy('partscope.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsScope($query, $data)
    {
        $query = DB::table("ops_parts_scope")->insert($data);

        return $query;
    }

    public function scopeGetMasterPartsItemList($query)
    {
        $query = DB::table("ops_parts AS partsitem")
            ->select('partsitem.*', 'partscope.name AS scope_name', 'partscope.code AS scope_code')
            ->join("ops_parts_scope AS partscope", "partscope.uuid", "=", "partsitem.parts_scope_uuid")
            ->orderBy('partsitem.id')
            ->get();

        return $query;
    }

    public function scopeSaveMasterPartsItem($query, $data)
    {
        $query = DB::table("ops_parts")->insert($data);

        return $query;
    }
}
