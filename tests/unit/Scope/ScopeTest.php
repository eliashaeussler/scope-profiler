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
use PHPUnit\Framework;

/**
 * ScopeTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Scope\Scope::class)]
final class ScopeTest extends Framework\TestCase
{
    private Src\Scope\Scope $subject;
    private Src\Profile\Profile $profile;

    protected function setUp(): void
    {
        $this->subject = new Src\Scope\Scope('foo');
        $this->profile = Src\Profile\Profile::for($this->subject);
    }

    #[Framework\Attributes\Test]
    public function addTaskAddsGivenTask(): void
    {
        $task = new Src\Scope\Task(fn () => 'baz');

        $this->subject->addTask($task);

        $this->subject->run($this->profile);

        self::assertNotNull($task->result());
    }

    #[Framework\Attributes\Test]
    public function removeTaskRemovesGivenTask(): void
    {
        $task = new Src\Scope\Task(fn () => 'baz');

        $this->subject->addTask($task);
        $this->subject->removeTask($task);

        $this->expectExceptionObject(
            new Src\Exception\NoTasksAvailable(),
        );

        $this->subject->run($this->profile);
    }

    #[Framework\Attributes\Test]
    public function runThrowsExceptionIfNoTasksAreAvailable(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\NoTasksAvailable(),
        );

        $this->subject->run($this->profile);
    }

    #[Framework\Attributes\Test]
    public function runExecutesAndDetachesAllTasks(): void
    {
        $task = new Src\Scope\Task(fn () => 'baz');

        $this->subject->addTask($task);

        $this->subject->run($this->profile);

        $this->expectExceptionObject(
            new Src\Exception\NoTasksAvailable(),
        );

        $this->subject->run($this->profile);
    }
}
