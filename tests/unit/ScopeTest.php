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
use PHPUnit\Framework;

use function sleep;

/**
 * ScopeTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Scope::class)]
final class ScopeTest extends Framework\TestCase
{
    private Src\Scope $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Scope('foo');
    }

    #[Framework\Attributes\Test]
    public function runStartsProfilingAndExecutesTaskAndStopsProfiling(): void
    {
        $function = function () {
            sleep(2);

            return 'foo';
        };

        $actual = $this->subject->run($function);

        self::assertSame('foo', $actual);
        self::assertGreaterThan(2000, $this->subject->measure()->duration);
    }

    #[Framework\Attributes\Test]
    public function startStartsProfiling(): void
    {
        $this->subject->start();

        sleep(2);

        self::assertGreaterThan(2000, $this->subject->measure()->duration);
    }

    #[Framework\Attributes\Test]
    public function stopThrowsExceptionIfScopeIsNotActive(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\ScopeIsNotActive('foo'),
        );

        $this->subject->stop();
    }

    #[Framework\Attributes\Test]
    public function stopStopsProfiling(): void
    {
        $this->subject->start();

        sleep(1);

        $this->subject->stop();

        sleep(1);

        self::assertLessThan(2000, $this->subject->measure()->duration);
    }

    #[Framework\Attributes\Test]
    public function measureThrowsExceptionIfScopeIsNotActive(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\ScopeIsNotActive('foo'),
        );

        $this->subject->measure();
    }

    #[Framework\Attributes\Test]
    public function measureReturnsCurrentMeasurementIfScopeIsStillActive(): void
    {
        $this->subject->start();

        sleep(1);

        self::assertLessThan(2000, $this->subject->measure()->duration);

        sleep(1);

        self::assertGreaterThan(2000, $this->subject->measure()->duration);
    }

    #[Framework\Attributes\Test]
    public function measureStoresMeasurementIfScopeIsNoLongerActive(): void
    {
        $this->subject->start();
        $this->subject->stop();

        self::assertSame($this->subject->measure(), $this->subject->measure());
    }
}
