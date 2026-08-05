<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/scope-profiler".
 *
 * Copyright (C) 2026 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\ScopeProfiler;

use EliasHaeussler\ScopeProfiler\Profile\MeasurementEvent;

/**
 * ScopeProfiler.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ScopeProfiler
{
    private static ?self $instance = null;

    /**
     * @var list<Profile\Profile>
     */
    private array $profiles = [];

    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    public function profile(Scope\Scope $scope): Profile\Profile
    {
        $this->profiles[] = $profile = Profile\Profile::for($scope);

        $profile->addPoint(Profile\MeasurementPoint::now(MeasurementEvent::Start));

        try {
            $scope->run($profile);
        } finally {
            $profile->addPoint(Profile\MeasurementPoint::now(MeasurementEvent::End));
        }

        return $profile;
    }

    /**
     * @return list<Profile\Profile>
     */
    public function release(): array
    {
        $profiles = $this->profiles;

        $this->profiles = [];

        return $profiles;
    }
}
