<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Approval extends Model
{
    public function scopeGetApprovalList($query, $purchaserequest_uuid, $purpose, $role_uuid)
    {
        $query = DB::table("ops_role AS role")
            ->select('role.title','approvalst.status','approvalst.note')
            ->leftJoin('ops_approval_status AS approvalst', function($leftJoin) use($purchaserequest_uuid, $purpose) {
                $leftJoin->on('approvalst.role_uuid', '=', 'role.uuid')
                    ->where('approvalst.purpose', '=', $purpose)
                    ->where('approvalst.related_uuid', '=', $purchaserequest_uuid)
                    ->where('approvalst.isCanceled', '=', 0);
            })
            ->where(function ($query) use ($role_uuid) 
            {
                foreach ($role_uuid as $key => $value)
                {
                    $query->orWhere('role.uuid',$value);
                }
            })
            ->get();

        return $query;
    }

    public function scopeSaveApproval($query, $data)
    {
        $query = DB::table("ops_approval_status")->insert($data);

        return $query;
    }

    public function scopeClearance($query, $whereData)
    {
        $data = ['isCanceled' => 1];
        $query = DB::table("ops_approval_status")
            ->where('related_uuid',$whereData['related_uuid'])
            ->where('approved_by',$whereData['approved_by'])
            ->where('role_uuid',$whereData['role_uuid'])
            ->update($data);

        return $query;
    }
}
