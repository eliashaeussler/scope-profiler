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

use EliasHaeussler\ScopeProfiler\Scope;
use Stringable;
use Symfony\Component\Console;

use function array_sum;
use function max;
use function sprintf;

/**
 * Measurement.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class Measurement implements Stringable
{
    /**
     * @param non-negative-int $memoryUsage
     * @param non-negative-int $memoryPeak
     */
    public function __construct(
        public float $duration,
        public int $memoryUsage,
        public int $memoryPeak,
        public ?Scope\Scope $scope = null,
    ) {}

    public static function betweenPoints(
        MeasurementPoint $from,
        MeasurementPoint $to,
        ?Scope\Scope $scope = null,
    ): self {
        return new self(
            max(0, $to->time - $from->time),
            max(0, $to->memoryUsage - $from->memoryUsage),
            max($to->memoryPeak, $from->memoryPeak),
            $scope,
        );
    }

    /**
     * @param non-empty-list<self> $measurements
     */
    public static function accumulate(array $measurements, ?Scope\Scope $scope = null): self
    {
        return new self(
            array_sum(array_map(static fn (self $measurement) => $measurement->duration, $measurements)),
            array_sum(array_map(static fn (self $measurement) => $measurement->memoryUsage, $measurements)),
            max(array_map(static fn (self $measurement) => $measurement->memoryPeak, $measurements)),
            $scope,
        );
    }

    public function format(?string $action = null): string
    {
        $action ??= $this->scope->name ?? 'Task';

        return sprintf(
            '%s took %s and consumed %s of memory (peak at %s).',
            $action,
            $this->formatDuration(),
            $this->formatMemoryUsage(),
            $this->formatMemoryPeak(),
        );
    }

    public function formatDuration(): string
    {
        return Console\Helper\Helper::formatTime($this->duration / 1000);
    }

    public function formatMemoryUsage(): string
    {
        return Console\Helper\Helper::formatMemory($this->memoryUsage);
    }

    public function formatMemoryPeak(): string
    {
        return Console\Helper\Helper::formatMemory($this->memoryPeak);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
