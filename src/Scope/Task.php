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

use Closure;
use EliasHaeussler\ScopeProfiler\Profile;
use Throwable;

use function call_user_func;

/**
 * Task.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Task
{
    private ?TaskExecutionResult $result = null;

    /**
     * @template T
     *
     * @param Closure(Profile\Profile): T                              $function
     * @param Closure(Profile\Profile, TaskExecutionResult): void|null $onSuccess
     * @param Closure(Profile\Profile, Throwable): void|null           $onFailure
     */
    public function __construct(
        private readonly Closure $function,
        private readonly ?Closure $onSuccess = null,
        private readonly ?Closure $onFailure = null,
    ) {}

    public function execute(Profile\Profile $profile): void
    {
        try {
            $result = call_user_func($this->function, $profile);

            $this->result = TaskExecutionResult::success($result);

            if (null !== $this->onSuccess) {
                call_user_func($this->onSuccess, $profile, $this->result);
            }
        } catch (Throwable $exception) {
            $this->result = TaskExecutionResult::failure($exception);

            if (null === $this->onFailure) {
                throw $exception;
            }

            call_user_func($this->onFailure, $profile, $exception);
        }
    }

    public function result(): ?TaskExecutionResult
    {
        return $this->result;
    }
}
