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

namespace EliasHaeussler\ScopeProfiler\Tests\Scope;

use EliasHaeussler\ScopeProfiler as Src;
use Exception;
use PHPUnit\Framework;

/**
 * TaskExecutionResultTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Scope\TaskExecutionResult::class)]
final class TaskExecutionResultTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function successReturnsSuccessfulResult(): void
    {
        $actual = Src\Scope\TaskExecutionResult::success('foo');

        self::assertTrue($actual->successful());
        self::assertSame('foo', $actual->returnValue());
        self::assertNull($actual->exception());
    }

    #[Framework\Attributes\Test]
    public function failureReturnsFailureResult(): void
    {
        $exception = new Exception('foo');

        $actual = Src\Scope\TaskExecutionResult::failure($exception);

        self::assertFalse($actual->successful());
        self::assertNull($actual->returnValue());
        self::assertSame($exception, $actual->exception());
    }
}
