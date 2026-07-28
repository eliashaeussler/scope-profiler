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

namespace EliasHaeussler\ScopeProfiler;

use function array_values;

/**
 * ScopeProfiler.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ScopeProfiler
{
    private static ?self $instance = null;

    /**
     * @var array<non-empty-string, Scope>
     */
    private array $scopes = [];

    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @param non-empty-string $action
     */
    public function pushScope(string $action): Scope
    {
        return $this->scopes[$action] = new Scope($action);
    }

    /**
     * @param non-empty-string $action
     */
    public function pullScope(string $action): ?Scope
    {
        $scope = $this->scopes[$action] ?? null;

        unset($this->scopes[$action]);

        return $scope;
    }

    /**
     * @return list<Scope>
     */
    public function releaseScopes(): array
    {
        $scopes = $this->scopes;

        $this->scopes = [];

        return array_values($scopes);
    }
}
