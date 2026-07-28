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
 * ScopeProfilerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ScopeProfiler::class)]
final class ScopeProfilerTest extends Framework\TestCase
{
    private Src\ScopeProfiler $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\ScopeProfiler();
    }

    #[Framework\Attributes\Test]
    public function getReturnsSingletonInstance(): void
    {
        self::assertSame(Src\ScopeProfiler::get(), Src\ScopeProfiler::get());
    }

    #[Framework\Attributes\Test]
    public function pushScopeCreatesAndAppliesNewScope(): void
    {
        $expected = new Src\Scope('foo');

        $actual = $this->subject->pushScope('foo');

        self::assertEquals($expected, $actual);
        self::assertEquals([$expected], $this->subject->releaseScopes());
    }

    #[Framework\Attributes\Test]
    public function pullScopeDropsScope(): void
    {
        $this->subject->pushScope('foo');

        $expected = new Src\Scope('foo');

        $actual = $this->subject->pullScope('foo');

        self::assertEquals($expected, $actual);
        self::assertEquals([], $this->subject->releaseScopes());
    }

    //    #[Framework\Attributes\Test]
    //    public function startAndExecuteMeasuresScope(): void
    //    {
    //        $function = function () {
    //            sleep(2);
    //
    //            return 'foo';
    //        };
    //
    //        $actual = Src\ScopeProfiler::startAndExecute('foo', $function, $result);
    //
    //        self::assertSame('foo', $result);
    //        self::assertGreaterThan(2000, $actual->duration);
    //    }
    //
    //    #[Framework\Attributes\Test]
    //    public function stopReturnsMeasuredScope(): void
    //    {
    //        $subject = Src\ScopeProfiler::starrt('foo');
    //
    //        sleep(1);
    //
    //        $actual = $subject->stop();
    //
    //        self::assertSame('foo', $actual->action);
    //        self::assertGreaterThan(0, $actual->duration);
    //    }
}
