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

use function sleep;

/**
 * ProfileTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Profile\Profile::class)]
final class ProfileTest extends Framework\TestCase
{
    private Src\Scope\Scope $scope;
    private Src\Profile\Profile $subject;

    public function setUp(): void
    {
        $this->scope = new Src\Scope\Scope('Foo');
        $this->subject = Src\Profile\Profile::for($this->scope);
    }

    #[Framework\Attributes\Test]
    public function addPointThrowsExceptionIfFirstPointIsNotAStartPoint(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\ScopeIsNotActive($this->scope),
        );

        $this->subject->addPoint(Src\Profile\MeasurementPoint::now());
    }

    #[Framework\Attributes\Test]
    public function addPointThrowsExceptionIfGivenPointCannotFollowLastPoint(): void
    {
        $startPoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start);
        $resumePoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Resume);

        $this->subject->addPoint($startPoint);

        $this->expectExceptionObject(
            new Src\Exception\InvalidOrderOfPoints($resumePoint, $startPoint),
        );

        $this->subject->addPoint($resumePoint);
    }

    #[Framework\Attributes\Test]
    public function addPointAddsPoints(): void
    {
        $startPoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start);
        $pausePoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Pause);
        $resumePoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Resume);
        $endPoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::End);

        $this->subject->addPoint($startPoint);
        $this->subject->addPoint($pausePoint);
        $this->subject->addPoint($resumePoint);
        $this->subject->addPoint($endPoint);

        self::assertSame([$startPoint, $pausePoint, $resumePoint, $endPoint], $this->subject->points());
    }

    #[Framework\Attributes\Test]
    public function measureThrowsExceptionIfScopeIsNotActive(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\ScopeIsNotActive($this->scope),
        );

        $this->subject->measure();
    }

    #[Framework\Attributes\Test]
    public function measureReturnsMeasurementBetweenStartPointAndTemporaryCheckpointIfOnlyStartPointWasAdded(): void
    {
        $startPoint = Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start);

        $this->subject->addPoint($startPoint);

        sleep(1);

        $checkpoint = Src\Profile\MeasurementPoint::now();
        $expected = Src\Profile\Measurement::betweenPoints($startPoint, $checkpoint);

        $actual = $this->subject->measure();

        self::assertEqualsWithDelta($expected->duration, $actual->duration, 1);
    }

    #[Framework\Attributes\Test]
    public function measureReturnsMeasurementBetweenStartPointAndLastPoint(): void
    {
        $this->subject->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Start));
        $this->subject->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Pause));

        sleep(1);

        $this->subject->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::Resume));

        sleep(1);

        $this->subject->addPoint(Src\Profile\MeasurementPoint::now(Src\Profile\MeasurementEvent::End));

        $actual = $this->subject->measure();

        self::assertGreaterThan(1000, $actual->duration);
        self::assertLessThan(2000, $actual->duration);
    }
}
