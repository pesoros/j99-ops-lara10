<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExpenseRecapApproval extends Model
{
    public const STAGE_OPERATIONAL = 'operational';
    public const STAGE_ACCOUNTING = 'accounting';
    public const STAGE_SYSTEM = 'system';

    public const DECISION_APPROVED = 'approved';
    public const DECISION_REJECTED = 'rejected';
    public const DECISION_INVALIDATED = 'invalidated';

    protected $table = 'ops_expense_recap_approvals';

    public static function history(string $roadWarrantUuid)
    {
        return DB::table('ops_expense_recap_approvals as approval')
            ->select('approval.*', 'users.name as decided_by_name')
            ->leftJoin('v2_users as users', 'users.uuid', '=', 'approval.decided_by')
            ->where('approval.roadwarrant_uuid', $roadWarrantUuid)
            ->orderBy('approval.id')
            ->get();
    }

    public static function state(string $roadWarrantUuid): array
    {
        $history = self::history($roadWarrantUuid);
        $lastInvalidationId = (int) ($history
            ->where('decision', self::DECISION_INVALIDATED)
            ->max('id') ?? 0);
        $current = $history->where('id', '>', $lastInvalidationId);
        $operational = $current->where('stage', self::STAGE_OPERATIONAL)->last();
        $accounting = $current->where('stage', self::STAGE_ACCOUNTING)->last();

        if (!$operational) {
            $status = 'pending_operational';
        } elseif ($operational->decision === self::DECISION_REJECTED) {
            $status = 'rejected_operational';
        } elseif (!$accounting || $accounting->id < $operational->id) {
            $status = 'pending_accounting';
        } elseif ($accounting->decision === self::DECISION_REJECTED) {
            $status = 'rejected_accounting';
        } else {
            $status = 'approved';
        }

        return [
            'status' => $status,
            'history' => $history,
            'operational' => $operational,
            'accounting' => $accounting,
            'is_approved' => $status === 'approved',
        ];
    }

    public static function record(array $data): bool
    {
        return DB::table('ops_expense_recap_approvals')->insert($data + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function invalidate(string $roadWarrantUuid, ?string $userUuid, string $note): void
    {
        $state = self::state($roadWarrantUuid);
        if ($state['history']->isEmpty() || $state['status'] === 'pending_operational') {
            return;
        }

        self::record([
            'uuid' => generateUuid(),
            'roadwarrant_uuid' => $roadWarrantUuid,
            'stage' => self::STAGE_SYSTEM,
            'decision' => self::DECISION_INVALIDATED,
            'decided_by' => $userUuid,
            'role_slug' => 'system',
            'note' => $note,
        ]);
    }
}
