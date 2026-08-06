<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Services;

use App\Domains\Attendance\Application\Actions\BulkAttendanceAction;
use App\Domains\Attendance\Application\Actions\CalculateAttendanceStatisticsAction;
use App\Domains\Attendance\Application\Actions\CorrectAttendanceAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSummaryAction;
use App\Domains\Attendance\Application\Actions\LockAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\OpenAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\RecordAttendanceAction;
use App\Domains\Attendance\Application\Actions\UnlockAttendanceSessionAction;
use App\Domains\Attendance\Application\DTOs\AttendanceStatisticsResult;
use App\Domains\Attendance\Application\DTOs\BulkAttendanceData;
use App\Domains\Attendance\Application\DTOs\CorrectionData;
use App\Domains\Attendance\Application\DTOs\RecordAttendanceData;
use App\Domains\Attendance\Application\DTOs\SessionGenerationData;
use App\Domains\Attendance\Application\DTOs\StatisticsData;
use App\Domains\Attendance\Application\DTOs\SummaryData;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Models\User;

/**
 * Facade over the Attendance actions (SPR-008). The single entry point the
 * presentation layer uses; actions stay individually injectable for tests.
 */
class AttendanceService
{
    public function __construct(
        private readonly GenerateAttendanceSessionAction $generateSessions,
        private readonly OpenAttendanceSessionAction $openSession,
        private readonly RecordAttendanceAction $record,
        private readonly BulkAttendanceAction $bulkRecord,
        private readonly CorrectAttendanceAction $correct,
        private readonly LockAttendanceSessionAction $lock,
        private readonly UnlockAttendanceSessionAction $unlock,
        private readonly CalculateAttendanceStatisticsAction $calculate,
        private readonly GenerateAttendanceSummaryAction $generateSummary,
    ) {
    }

    /**
     * @return list<AttendanceSession>
     */
    public function generateSessions(SessionGenerationData $data, ?User $actor = null): array
    {
        return $this->generateSessions->execute($data, $actor);
    }

    public function openSession(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        return $this->openSession->execute($session, $actor);
    }

    public function record(RecordAttendanceData $data, ?User $actor = null): AttendanceRecord
    {
        return $this->record->execute($data, $actor);
    }

    /**
     * @return array{inserted: int, skipped: int, rejected: int}
     */
    public function recordBulk(BulkAttendanceData $data, ?User $actor = null): array
    {
        return $this->bulkRecord->execute($data, $actor);
    }

    public function correct(CorrectionData $data, ?User $actor = null): AttendanceCorrection
    {
        return $this->correct->execute($data, $actor);
    }

    public function lockSession(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        return $this->lock->execute($session, $actor);
    }

    public function unlockSession(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        return $this->unlock->execute($session, $actor);
    }

    public function calculateStatistics(StatisticsData $data): AttendanceStatisticsResult
    {
        return $this->calculate->execute($data);
    }

    public function generateSummary(SummaryData $data, ?User $actor = null): AttendanceSummary
    {
        return $this->generateSummary->execute($data, $actor);
    }
}
