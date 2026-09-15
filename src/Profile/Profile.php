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

namespace EliasHaeussler\ScopeProfiler\Profile;

use EliasHaeussler\ScopeProfiler\Exception;
use EliasHaeussler\ScopeProfiler\Scope;

use function array_filter;
use function array_values;
use function count;

/**
 * Profile.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Profile
{
    /**
     * @var list<MeasurementPoint>
     */
    private array $points = [];

    private function __construct(
        public readonly Scope\Scope $scope,
    ) {}

    public static function for(Scope\Scope $scope): self
    {
        return new self($scope);
    }

    /**
     * @throws Exception\InvalidOrderOfPoints
     * @throws Exception\ScopeIsNotActive
     */
    public function addPoint(MeasurementPoint $point): self
    {
        if ([] === $this->points && MeasurementEvent::Start !== $point->event) {
            throw new Exception\ScopeIsNotActive($this->scope);
        }

        if (null !== ($lastPoint = $this->getLastPoint()) && !$point->event->canFollow($lastPoint->event)) {
            throw new Exception\InvalidOrderOfPoints($point, $lastPoint);
        }

        $this->points[] = $point;

        return $this;
    }

    /**
     * @throws Exception\ScopeIsNotActive
     */
    public function measure(): Measurement
    {
        if ([] === $this->points) {
            throw new Exception\ScopeIsNotActive($this->scope);
        }

        $measurements = [];
        $pointsToConsider = array_values(
            array_filter(
                $this->points,
                static fn (MeasurementPoint $point) => MeasurementEvent::Checkpoint !== $point->event,
            ),
        );

        foreach ($pointsToConsider as $i => $point) {
            // Don't measure when profiling is paused
            if (MeasurementEvent::Pause === $point->event) {
                continue;
            }

            $nextPoint = $pointsToConsider[$i + 1] ?? null;

            if (null === $nextPoint) {
                if (MeasurementEvent::Checkpoint->canFollow($point->event)) {
                    // Measure until temporary current checkpoint if no final point was added yet
                    $nextPoint = MeasurementPoint::now();
                } else {
                    // Exit if last point is passed
                    break;
                }
            }

            $measurements[] = Measurement::betweenPoints($point, $nextPoint);
        }

        return Measurement::accumulate($measurements, $this->scope);
    }

    /**
     * @return list<MeasurementPoint>
     */
    public function points(): array
    {
        return $this->points;
    }

    private function getLastPoint(): ?MeasurementPoint
    {
        if ([] === $this->points) {
            return null;
        }

        return $this->points[count($this->points) - 1];
    }
}
