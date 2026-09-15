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

use Throwable;

/**
 * TaskExecutionResult.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class TaskExecutionResult
{
    private function __construct(
        private mixed $returnValue,
        private ?Throwable $exception,
    ) {}

    public static function success(mixed $returnValue): self
    {
        return new self($returnValue, null);
    }

    public static function failure(Throwable $exception): self
    {
        return new self(null, $exception);
    }

    /**
     * @phpstan-assert-if-true !null $this->exception()
     */
    public function successful(): bool
    {
        return null === $this->exception;
    }

    public function returnValue(): mixed
    {
        return $this->returnValue;
    }

    public function exception(): ?Throwable
    {
        return $this->exception;
    }
}
