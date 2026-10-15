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

use function memory_get_peak_usage;
use function memory_get_usage;
use function microtime;

/**
 * MeasurementPoint.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class MeasurementPoint
{
    /**
     * @param non-negative-int $memoryUsage
     * @param non-negative-int $memoryPeak
     */
    public function __construct(
        public float $time,
        public int $memoryUsage,
        public int $memoryPeak,
        public MeasurementEvent $event = MeasurementEvent::Checkpoint,
        public ?string $note = null,
    ) {}

    public static function now(MeasurementEvent $event = MeasurementEvent::Checkpoint, ?string $note = null): self
    {
        return new self(
            self::currentTime(),
            self::currentMemoryUsage(),
            self::currentMemoryPeak(),
            $event,
            $note,
        );
    }

    public function describe(): string
    {
        return $this->note ?? $this->event->name;
    }

    private static function currentTime(): float
    {
        return microtime(true) * 1000;
    }

    /**
     * @return non-negative-int
     */
    private static function currentMemoryUsage(): int
    {
        return memory_get_usage(true);
    }

    /**
     * @return non-negative-int
     */
    private static function currentMemoryPeak(): int
    {
        return memory_get_peak_usage(true);
    }
}
