<?php

namespace Z3d0X\FilamentLogger\Loggers;

use Filament\Facades\Filament;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\ActivityLogStatus;

class AccessLogger
{
    /**
     * Log user login
     */
    public function handle(Login $event): void
    {
        /** @var Model&Authenticatable $event->user */
        $user = $event->user;

        $description = Filament::getUserName($user).' logged in';

        app(ActivityLogger::class)
            ->useLog(config('filament-logger.access.log_name'))
            ->setLogStatus(app(ActivityLogStatus::class))
            ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
            ->performedOn($user)
            ->event('Login')
            ->log($description);
    }
}
