<?php

namespace App\Services;

use Jenssegers\Agent\Agent;

class ActiveDeviceService
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function getDeviceType(Agent $agent): string
    {
        if ($agent->isDesktop()) {
            return 'desktop';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isMobile()) {
            return 'mobile';
        }
        return 'unknown';
    }

    public function getLocation(string $ip): ?string
    {
        $location = $this->locationService->getLocation($ip);

        if ($location) {
            return implode(', ', array_filter([
                $location['city'],
                $location['region'],
                $location['country']
            ]));
        }
        return null;
    }
}
