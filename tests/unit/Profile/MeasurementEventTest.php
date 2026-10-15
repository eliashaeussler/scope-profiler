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

namespace EliasHaeussler\ScopeProfiler\Tests\Profile;

use EliasHaeussler\ScopeProfiler as Src;
use PHPUnit\Framework;

/**
 * MeasurementEventTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Profile\MeasurementEvent::class)]
final class MeasurementEventTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function checkpointCanBeFollowed(): void
    {
        $checkpoint = Src\Profile\MeasurementEvent::Checkpoint;

        self::assertTrue(Src\Profile\MeasurementEvent::Checkpoint->canFollow($checkpoint));
        self::assertTrue(Src\Profile\MeasurementEvent::End->canFollow($checkpoint));
        self::assertTrue(Src\Profile\MeasurementEvent::Pause->canFollow($checkpoint));
        self::assertFalse(Src\Profile\MeasurementEvent::Resume->canFollow($checkpoint));
        self::assertFalse(Src\Profile\MeasurementEvent::Start->canFollow($checkpoint));
    }

    #[Framework\Attributes\Test]
    public function endCannotBeFollowed(): void
    {
        $end = Src\Profile\MeasurementEvent::End;

        self::assertFalse(Src\Profile\MeasurementEvent::Checkpoint->canFollow($end));
        self::assertFalse(Src\Profile\MeasurementEvent::End->canFollow($end));
        self::assertFalse(Src\Profile\MeasurementEvent::Pause->canFollow($end));
        self::assertFalse(Src\Profile\MeasurementEvent::Resume->canFollow($end));
        self::assertFalse(Src\Profile\MeasurementEvent::Start->canFollow($end));
    }

    #[Framework\Attributes\Test]
    public function pauseCanBeFollowed(): void
    {
        $pause = Src\Profile\MeasurementEvent::Pause;

        self::assertFalse(Src\Profile\MeasurementEvent::Checkpoint->canFollow($pause));
        self::assertTrue(Src\Profile\MeasurementEvent::End->canFollow($pause));
        self::assertFalse(Src\Profile\MeasurementEvent::Pause->canFollow($pause));
        self::assertTrue(Src\Profile\MeasurementEvent::Resume->canFollow($pause));
        self::assertFalse(Src\Profile\MeasurementEvent::Start->canFollow($pause));
    }

    #[Framework\Attributes\Test]
    public function resumeCanBeFollowed(): void
    {
        $resume = Src\Profile\MeasurementEvent::Resume;

        self::assertTrue(Src\Profile\MeasurementEvent::Checkpoint->canFollow($resume));
        self::assertTrue(Src\Profile\MeasurementEvent::End->canFollow($resume));
        self::assertTrue(Src\Profile\MeasurementEvent::Pause->canFollow($resume));
        self::assertFalse(Src\Profile\MeasurementEvent::Resume->canFollow($resume));
        self::assertFalse(Src\Profile\MeasurementEvent::Start->canFollow($resume));
    }

    #[Framework\Attributes\Test]
    public function startCanBeFollowed(): void
    {
        $start = Src\Profile\MeasurementEvent::Resume;

        self::assertTrue(Src\Profile\MeasurementEvent::Checkpoint->canFollow($start));
        self::assertTrue(Src\Profile\MeasurementEvent::End->canFollow($start));
        self::assertTrue(Src\Profile\MeasurementEvent::Pause->canFollow($start));
        self::assertFalse(Src\Profile\MeasurementEvent::Resume->canFollow($start));
        self::assertFalse(Src\Profile\MeasurementEvent::Start->canFollow($start));
    }
}
