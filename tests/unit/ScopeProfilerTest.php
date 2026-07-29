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
    public function pushScopeAddsGivenScope(): void
    {
        $scope = new Src\Scope('foo');

        $this->subject->pushScope($scope);

        self::assertEquals([$scope], $this->subject->releaseScopes());
    }

    #[Framework\Attributes\Test]
    public function pullScopeDropsScope(): void
    {
        $scope = new Src\Scope('foo');

        $this->subject->pushScope($scope);
        $this->subject->pullScope($scope);

        self::assertEquals([], $this->subject->releaseScopes());
    }
}
