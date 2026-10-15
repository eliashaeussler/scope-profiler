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

namespace EliasHaeussler\ScopeProfiler\Scope;

use EliasHaeussler\ScopeProfiler\Exception;
use EliasHaeussler\ScopeProfiler\Profile;

use function array_shift;

/**
 * Scope.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Scope
{
    /**
     * @var Task[]
     */
    private array $tasks = [];

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public readonly string $name,
    ) {}

    public function addTask(Task $task): self
    {
        $this->tasks[] = $task;

        return $this;
    }

    public function removeTask(Task $task): self
    {
        foreach ($this->tasks as $i => $storedTask) {
            if ($storedTask === $task) {
                unset($this->tasks[$i]);
                break;
            }
        }

        return $this;
    }

    /**
     * @throws Exception\NoTasksAvailable
     */
    public function run(Profile\Profile $profile): void
    {
        if ([] === $this->tasks) {
            throw new Exception\NoTasksAvailable();
        }

        while (null !== ($task = array_shift($this->tasks))) {
            $task->execute($profile);
        }
    }
}
