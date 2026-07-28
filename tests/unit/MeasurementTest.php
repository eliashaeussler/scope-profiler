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

namespace EliasHaeussler\ScopeProfiler\Tests;

use EliasHaeussler\ScopeProfiler as Src;
use Generator;
use PHPUnit\Framework;

/**
 * MeasurementTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Measurement::class)]
final class MeasurementTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function formatReturnsFormattedMeasurement(): void
    {
        $scope = new Src\Scope('Foo');
        $subject = new Src\Measurement($scope, 123.45, 678, 901);

        self::assertSame('Foo took 123 ms and consumed 678 B of memory (peak at 901 B).', $subject->format());
    }

    /**
     * @return Generator<string, array{float, string}>
     */
    public static function formatDurationReturnsFormattedDurationDataProvider(): Generator
    {
        yield 'milliseconds' => [1.23456, '1 ms'];
        yield 'seconds' => [1234.56, '1 s'];
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('formatDurationReturnsFormattedDurationDataProvider')]
    public function formatDurationReturnsFormattedDuration(float $duration, string $expected): void
    {
        $subject = new Src\Measurement(new Src\Scope('Foo'), $duration, 0, 0);

        self::assertSame($expected, $subject->formatDuration());
    }

    #[Framework\Attributes\Test]
    public function formatMemoryUsageReturnsFormattedMemoryUsage(): void
    {
        $subject = new Src\Measurement(new Src\Scope('Foo'), 0, 678, 901);

        self::assertSame('678 B', $subject->formatMemoryUsage());
    }

    #[Framework\Attributes\Test]
    public function subjectIsStringable(): void
    {
        $subject = new Src\Measurement(new Src\Scope('Foo'), 123.45, 678, 901);

        self::assertSame('Foo took 123 ms and consumed 678 B of memory (peak at 901 B).', (string) $subject);
    }
}
