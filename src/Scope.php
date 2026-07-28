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

use Closure;
use EliasHaeussler\ScopeProfiler\Exception\ScopeIsNotActive;

use function max;

/**
 * Scope.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Scope
{
    private ?MeasurementPoint $startPoint = null;
    private ?MeasurementPoint $endPoint = null;
    private ?Measurement $measurement = null;

    /**
     * @param non-empty-string $action
     */
    public function __construct(
        public readonly string $action,
    ) {}

    /**
     * @template T
     *
     * @param Closure(): T $task
     *
     * @return T
     */
    public function run(Closure $task): mixed
    {
        $this->start();

        try {
            return (static fn () => $task())();
        } finally {
            $this->stop();
        }
    }

    public function start(): void
    {
        $this->startPoint = MeasurementPoint::now();
    }

    /**
     * @throws ScopeIsNotActive
     */
    public function stop(): void
    {
        if (null === $this->startPoint) {
            throw new ScopeIsNotActive($this->action);
        }

        $this->endPoint = MeasurementPoint::now();
    }

    /**
     * @throws ScopeIsNotActive
     */
    public function measure(): Measurement
    {
        if (null === $this->startPoint) {
            throw new ScopeIsNotActive($this->action);
        }

        if (null !== $this->measurement) {
            return $this->measurement;
        }

        $endPoint = $this->endPoint ?? MeasurementPoint::now();
        $measurement = new Measurement(
            $this,
            max(0, $endPoint->time - $this->startPoint->time),
            max(0, $endPoint->memoryUsage - $this->startPoint->memoryUsage),
            max($endPoint->memoryPeak, $this->startPoint->memoryPeak),
        );

        if (null !== $this->endPoint) {
            $this->measurement = $measurement;
        }

        return $measurement;
    }
}
