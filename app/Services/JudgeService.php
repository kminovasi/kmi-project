<?php

namespace App\Services;

use App\Models\Judge;
use App\Models\User;
use Log;

class JudgeService
{
    /**
     * Cek apakah pengguna adalah juri.
     *
     * @param User $user
     * @return bool
     */
    public function isJudge(User $user): bool
    {
        $employee_id = $user->employee_id;
        return Judge::where('employee_id', $employee_id)->exists();
    }
    /**
     * Cek apakah juri terdaftar di event tertentu.
     *
     * @param User $user
     * @param int $eventId
     * @return bool
     */
    public function isJudgeInEvent(User $user, int $eventId): bool
    {
        $employee_id = $user->employee_id;

        return Judge::where('employee_id', $employee_id)
            ->where('event_id', $eventId) // Asumsikan ada kolom event_id di tabel judges
            ->exists();
    }
}
