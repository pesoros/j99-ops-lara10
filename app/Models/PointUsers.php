<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointUsers extends Model
{
    public function scopeGetUser($query, $email)
    {
        $query = DB::table("users_client AS ucl")
            ->select(
                'ucl.id as user_id',
                'ucl.email',
                'ucl.first_name',
                'ucl.last_name',
                'usp.point',
            )
            ->leftJoin("v2_userpoint AS usp", "usp.user_id", "=", "ucl.id")
            ->where('email',$email)
            ->first();

        return $query;
    }

    public function scopeGetUserPointHistory($query, $userid)
    {
        $query = DB::table("v2_userpoint_history AS uph")
            ->select(
                'uph.booking_code',
                'uph.point',
                'uph.is_debit',
                'uph.created_at',
                'uph.note',
            )
            ->where('user_id',$userid)
            ->orderBy('uph.created_at','DESC')
            ->get();

        return $query;
    }    
}
