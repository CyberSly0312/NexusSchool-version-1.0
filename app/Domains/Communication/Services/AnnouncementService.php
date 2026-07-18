<?php

declare(strict_types=1);

namespace App\Domains\Communication\Services;

use App\Domains\Communication\DTOs\AnnouncementData;
use App\Domains\Communication\Enums\AnnouncementScope;
use App\Domains\Communication\Events\AnnouncementPublished;
use App\Domains\Communication\Rules\AnnouncementRules;
use App\Models\Announcement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnnouncementService
{
    public function __construct(
        protected AnnouncementRules $announcementRules,
    ) {
    }

    public function create(mixed $data): AnnouncementData
    {
        $validated = $this->announcementRules->validate($data, 'create');

        $validated['status'] = $validated['status'] ?? 'draft';

        $announcement = DB::transaction(function () use ($validated) {
            return Announcement::create($validated);
        });

        return AnnouncementData::create($announcement->toArray());
    }

    public function update(string $id, mixed $data): AnnouncementData
    {
        $announcement = Announcement::findOrFail($id);

        if ($announcement->isPublished()) {
            throw new \InvalidArgumentException('Published announcements cannot be edited.');
        }

        $validated = $this->announcementRules->validate($data, 'update');

        $announcement = DB::transaction(function () use ($announcement, $validated) {
            $announcement->update($validated);

            return $announcement;
        });

        return AnnouncementData::create($announcement->fresh()->toArray());
    }

    public function publish(string $id): AnnouncementData
    {
        $announcement = Announcement::findOrFail($id);

        $announcement = DB::transaction(function () use ($announcement) {
            $announcement->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            return $announcement;
        });

        AnnouncementPublished::dispatch($announcement);

        return AnnouncementData::create($announcement->fresh()->toArray());
    }

    public function archive(string $id): AnnouncementData
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->update(['status' => 'archived']);

        return AnnouncementData::create($announcement->fresh()->toArray());
    }

    public function delete(string $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
    }

    public function getActive(?string $schoolId = null): Collection
    {
        $query = Announcement::published()->notExpired();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('author')->latest('published_at')->get();
    }

    public function getByScope(string $scope, ?string $targetId = null, ?string $schoolId = null): Collection
    {
        $query = Announcement::published()->notExpired()
            ->where('scope', $scope);

        if ($targetId) {
            $query->where('target_id', $targetId);
        }

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('author')->latest('published_at')->get();
    }

    public function getPinned(?string $schoolId = null): Collection
    {
        $query = Announcement::published()->notExpired()->pinned();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('author')->get();
    }

    public function getForUser(string $userId, ?string $schoolId = null): Collection
    {
        $query = Announcement::published()->notExpired()
            ->where(function ($q) use ($userId) {
                $q->where('scope', AnnouncementScope::SchoolWide->value)
                    ->orWhere('scope', AnnouncementScope::Individual->value)
                    ->where('target_id', $userId);
            });

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('author')->latest('published_at')->get();
    }
}
