<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Accurate extends Model
{
    public function scopeGetToken($query, $role)
    {
        $query = DB::table("token_credential")
            ->select('value AS token','refresh_token')
            ->where('vendor','accurate')
            ->where('role',$role)
            ->first();

        return $query;
    }

    public function scopeUpdateAccurateToken($query, $role, $data)
    {
        $query = DB::table("token_credential")
            ->where('vendor','accurate')
            ->where('role',$role)
            ->update($data);

        return $query;
    }
}
