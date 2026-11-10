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

namespace EliasHaeussler\ScopeProfiler\Tests\Profile;

use EliasHaeussler\ScopeProfiler as Src;
use Generator;
use PHPUnit\Framework;

/**
 * MeasurementTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Profile\Measurement::class)]
final class MeasurementTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function betweenPointsReturnsMeasurementBetweenGivenPoints(): void
    {
        $from = new Src\Profile\MeasurementPoint(123.45, 678, 901);
        $to = new Src\Profile\MeasurementPoint(223.45, 778, 1001);
        $scope = new Src\Scope\Scope('foo');

        $actual = Src\Profile\Measurement::betweenPoints($from, $to, $scope);

        self::assertEqualsWithDelta(100, $actual->duration, 0.01);
        self::assertEqualsWithDelta(100, $actual->memoryUsage, 0.01);
        self::assertEqualsWithDelta(1001, $actual->memoryPeak, 0.01);
        self::assertSame($scope, $actual->scope);
    }

    #[Framework\Attributes\Test]
    public function accumulateReturnsAccumulatedMeasurementForGivenMeasurements(): void
    {
        $measurementA = new Src\Profile\Measurement(123.45, 678, 901);
        $measurementB = new Src\Profile\Measurement(123.45, 678, 2001);
        $measurementC = new Src\Profile\Measurement(123.45, 678, 1001);
        $scope = new Src\Scope\Scope('foo');

        self::assertEquals(
            new Src\Profile\Measurement(3 * 123.45, 3 * 678, 2001, $scope),
            Src\Profile\Measurement::accumulate([$measurementA, $measurementB, $measurementC], $scope),
        );
    }

    /**
     * @return Generator<string, array{Src\Profile\Measurement, string|null, string}>
     */
    public static function formatReturnsFormattedMeasurementDataProvider(): Generator
    {
        $scope = new Src\Scope\Scope('Foo');

        yield 'with scope' => [
            new Src\Profile\Measurement(123.45, 678, 901, $scope),
            null,
            'Foo took 123 ms and consumed 678 B of memory (peak at 901 B).',
        ];
        yield 'without scope' => [
            new Src\Profile\Measurement(123.45, 678, 901),
            null,
            'Task took 123 ms and consumed 678 B of memory (peak at 901 B).',
        ];
        yield 'with scope and custom action' => [
            new Src\Profile\Measurement(123.45, 678, 901, $scope),
            'Baz',
            'Baz took 123 ms and consumed 678 B of memory (peak at 901 B).',
        ];
        yield 'without scope and custom action' => [
            new Src\Profile\Measurement(123.45, 678, 901),
            'Baz',
            'Baz took 123 ms and consumed 678 B of memory (peak at 901 B).',
        ];
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('formatReturnsFormattedMeasurementDataProvider')]
    public function formatReturnsFormattedMeasurement(
        Src\Profile\Measurement $subject,
        ?string $action,
        string $expected,
    ): void {
        self::assertSame($expected, $subject->format($action));
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
        $subject = new Src\Profile\Measurement($duration, 0, 0, new Src\Scope\Scope('Foo'));

        self::assertSame($expected, $subject->formatDuration());
    }

    #[Framework\Attributes\Test]
    public function formatMemoryUsageReturnsFormattedMemoryUsage(): void
    {
        $subject = new Src\Profile\Measurement(0, 678, 901, new Src\Scope\Scope('Foo'));

        self::assertSame('678 B', $subject->formatMemoryUsage());
    }

    #[Framework\Attributes\Test]
    public function subjectIsStringable(): void
    {
        $subject = new Src\Profile\Measurement(123.45, 678, 901, new Src\Scope\Scope('Foo'));

        self::assertSame('Foo took 123 ms and consumed 678 B of memory (peak at 901 B).', (string) $subject);
    }
}
