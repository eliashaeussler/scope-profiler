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
use Throwable;

/**
 * TaskTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Scope\Task::class)]
final class TaskTest extends Framework\TestCase
{
    private Src\Profile\Profile $profile;

    protected function setUp(): void
    {
        $this->profile = Src\Profile\Profile::for(new Src\Scope\Scope('foo'));
    }

    #[Framework\Attributes\Test]
    public function executeCallsFunctionWithProfile(): void
    {
        $subject = new Src\Scope\Task(fn (Src\Profile\Profile $profile) => $profile->scope->name);

        $subject->execute($this->profile);

        $result = $subject->result();

        self::assertNotNull($result);
        self::assertTrue($result->successful());
        self::assertSame('foo', $result->returnValue());
        self::assertNull($result->exception());
    }

    #[Framework\Attributes\Test]
    public function executeCallsOnSuccessFunctionWithProfileAndResult(): void
    {
        $this->profile->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start));

        $subject = new Src\Scope\Task(
            fn (Src\Profile\Profile $profile) => $profile->scope->name,
            function (Src\Profile\Profile $profile, Src\Scope\TaskExecutionResult $result) {
                self::assertIsString($result->returnValue());

                $profile->addPoint(Src\Profile\MeasurementPoint::now(note: $result->returnValue()));
            },
        );

        $subject->execute($this->profile);

        self::assertCount(2, $this->profile->points());
        self::assertSame('foo', $this->profile->points()[1]->describe());
    }

    #[Framework\Attributes\Test]
    public function executeCallsOnFailureFunctionWithProfileAndException(): void
    {
        $this->profile->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start));

        $subject = new Src\Scope\Task(
            function (Src\Profile\Profile $profile): never {
                throw new Exception('oops, something went wrong with '.$profile->scope->name);
            },
            onFailure: function (Src\Profile\Profile $profile, Throwable $exception) {
                $profile->addPoint(Src\Profile\MeasurementPoint::now(note: $exception->getMessage()));
            },
        );

        $subject->execute($this->profile);

        self::assertCount(2, $this->profile->points());
        self::assertSame('oops, something went wrong with foo', $this->profile->points()[1]->describe());
    }

    #[Framework\Attributes\Test]
    public function executeThrowsExceptionOnFailureIfNoOnFailureFunctionIsConfigured(): void
    {
        $exception = new Exception('oops, something went wrong');

        $subject = new Src\Scope\Task(
            function () use ($exception): never {
                throw $exception;
            },
        );

        $this->expectExceptionObject($exception);

        $subject->execute($this->profile);
    }
}
