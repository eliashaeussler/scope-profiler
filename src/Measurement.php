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

use Stringable;
use Symfony\Component\Console;

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
        public Scope $scope,
        public float $duration,
        public int $memoryUsage,
        public int $memoryPeak,
    ) {}

    public function format(): string
    {
        return sprintf(
            '%s took %s and consumed %s of memory (peak at %s).',
            $this->scope->action,
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
