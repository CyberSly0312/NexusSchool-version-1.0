<?php

declare(strict_types=1);

namespace App\Domains\System\Events;

use App\Models\SystemSetting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemSettingUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SystemSetting $setting,
        public readonly ?array $oldValues = null,
    ) {
    }
}
