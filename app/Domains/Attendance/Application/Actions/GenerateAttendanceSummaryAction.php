<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\AttendanceStatisticsResult;
use App\Domains\Attendance\Application\DTOs\SummaryData;
use App\Domains\Attendance\Application\Services\AttendanceAlertDispatcher;
use App\Domains\Attendance\Application\Services\AttendanceRecordQuery;
use App\Domains\Attendance\Application\Services\AttendanceRiskEngine;
use App\Domains\Attendance\Application\Services\AttendanceStatisticsCalculator;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Materializes a persistent attendance summary for one enrollment over one
 * period, and (optionally) feeds the alert engine (SPR-008).
 */
final class GenerateAttendanceSummaryAction
{
    public function __construct(
        private readonly AttendanceRecordQuery $records,
        private readonly AttendanceStatisticsCalculator $calculator,
        private readonly AttendanceRiskEngine $risk,
        private readonly AttendanceAlertDispatcher $alerts,
    ) {
    }

    public function execute(SummaryData $data, ?User $actor = null): AttendanceSummary
    {
        $result = $this->compute($data);

        return DB::transaction(function () use ($data, $result, $actor): AttendanceSummary {
            // Dates are stored with a time component (date cast), so the
            // uniqueness window must be matched with whereDate, not exact
            // string equality against the plain 'Y-m-d' value.
            $summary = AttendanceSummary::query()
                ->forSchool($data->schoolId)
                ->where('enrollment_id', $data->enrollmentId)
                ->where('period_type', $data->period->type)
                ->whereDate('period_start', $data->period->startsOn->toDateString())
                ->whereDate('period_end', $data->period->endsOn->toDateString())
                ->first();

            $attributes = [
                'school_id' => $data->schoolId,
                'sessions_count' => $result->sessionsCount,
                'attended_count' => $result->breakdown[AttendanceStatus::Present->value]
                    + $result->breakdown[AttendanceStatus::Remote->value]
                    + $result->breakdown[AttendanceStatus::SchoolActivity->value],
                'present_count' => $result->breakdown[AttendanceStatus::Present->value],
                'late_count' => $result->breakdown[AttendanceStatus::Late->value],
                'excused_count' => $result->breakdown[AttendanceStatus::Excused->value]
                    + $result->breakdown[AttendanceStatus::Medical->value]
                    + $result->breakdown[AttendanceStatus::AuthorizedLeave->value],
                'unexcused_count' => $result->breakdown[AttendanceStatus::Unexcused->value],
                'absent_count' => $result->breakdown[AttendanceStatus::Absent->value],
                'attendance_percentage' => $result->attendancePercentage,
                'late_percentage' => $result->latePercentage,
                'consecutive_absences' => $result->consecutiveAbsences,
                'consecutive_lates' => $result->consecutiveLates,
                'risk_score' => $result->riskScore,
                'trend' => $result->trend,
                'breakdown' => $result->breakdown,
                'calculated_at' => now(),
                'calculated_by' => $actor?->getKey(),
            ];

            if ($summary === null) {
                $summary = AttendanceSummary::create(array_merge([
                    'enrollment_id' => $data->enrollmentId,
                    'period_type' => $data->period->type,
                    'period_start' => $data->period->startsOn->toDateString(),
                    'period_end' => $data->period->endsOn->toDateString(),
                ], $attributes));
            } else {
                $summary->update($attributes);
            }

            if ($data->dispatchAlerts) {
                $this->alerts->dispatch(
                    schoolId: $data->schoolId,
                    enrollmentId: $data->enrollmentId,
                    metrics: $result->toArray(),
                    periodStart: $data->period->startsOn->toDateString(),
                    periodEnd: $data->period->endsOn->toDateString(),
                    actor: $actor,
                );
            }

            return $summary->refresh();
        });
    }

    private function compute(SummaryData $data): AttendanceStatisticsResult
    {
        $rows = $this->records->rowsForEnrollment(
            $data->schoolId,
            $data->enrollmentId,
            $data->period->startsOn,
            $data->period->endsOn,
        );

        $percentage = $this->calculator->percentage($rows);
        $latePercentage = $this->calculator->latePercentage($rows);
        $consecutiveAbsences = $this->calculator->consecutiveAbsences($rows);
        $consecutiveLates = $this->calculator->consecutiveLates($rows);

        $riskScore = $this->risk->score(
            $percentage,
            $latePercentage,
            $consecutiveAbsences,
            $consecutiveLates,
        );

        $breakdown = [];

        foreach (AttendanceStatus::cases() as $status) {
            $breakdown[$status->value] = 0;
        }

        foreach ($rows as $row) {
            $breakdown[$row['status']->value]++;
        }

        return new AttendanceStatisticsResult(
            sessionsCount: count($rows),
            attendancePercentage: $percentage->value,
            latePercentage: $latePercentage,
            consecutiveAbsences: $consecutiveAbsences,
            consecutiveLates: $consecutiveLates,
            riskScore: $riskScore->value,
            trend: 0.0,
            breakdown: $breakdown,
        );
    }
}
