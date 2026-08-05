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

namespace EliasHaeussler\ScopeProfiler\Profile;

use function in_array;

/**
 * MeasurementEvent.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
enum MeasurementEvent
{
    case Checkpoint;
    case End;
    case Pause;
    case Resume;
    case Start;

    public function canFollow(self $other): bool
    {
        return match ($other) {
            self::Checkpoint, self::Resume, self::Start => in_array($this, [self::Checkpoint, self::End, self::Pause], true),
            self::End => false,
            self::Pause => in_array($this, [self::End, self::Resume], true),
        };
    }
}
