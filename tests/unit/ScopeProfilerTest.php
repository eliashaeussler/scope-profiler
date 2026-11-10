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
use Exception;
use PHPUnit\Framework;

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
    public function profileRunsScopeAndReturnsProfile(): void
    {
        $functionPoint = Src\Profile\MeasurementPoint::now(note: 'function');
        $failurePoint = Src\Profile\MeasurementPoint::now(note: 'failure');

        $task = new Src\Scope\Task(
            function (Src\Profile\Profile $profile) use ($functionPoint) {
                $profile->addPoint($functionPoint);

                throw new Exception('oops');
            },
            onFailure: function (Src\Profile\Profile $profile) use ($failurePoint) {
                $profile->addPoint($failurePoint);
            },
        );

        $scope = new Src\Scope\Scope('Foo');
        $scope->addTask($task);

        $actual = $this->subject->profile($scope);

        self::assertSame($scope, $actual->scope);
        self::assertCount(4, $actual->points());
        self::assertSame(Src\Profile\MeasurementEvent::Start, $actual->points()[0]->event);
        self::assertSame($functionPoint, $actual->points()[1]);
        self::assertSame($failurePoint, $actual->points()[2]);
        self::assertSame(Src\Profile\MeasurementEvent::End, $actual->points()[3]->event);
    }

    #[Framework\Attributes\Test]
    public function releaseReturnsAndDetachesAllProfiles(): void
    {
        $fooScope = new Src\Scope\Scope('foo');
        $fooScope->addTask(new Src\Scope\Task(fn () => 'foo'));

        $bazScope = new Src\Scope\Scope('baz');
        $bazScope->addTask(new Src\Scope\Task(fn () => 'baz'));

        $fooProfile = $this->subject->profile($fooScope);
        $bazProfile = $this->subject->profile($bazScope);

        self::assertSame([$fooProfile, $bazProfile], $this->subject->release());
        self::assertSame([], $this->subject->release());
    }
}
