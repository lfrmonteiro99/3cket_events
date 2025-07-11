<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Coordinates;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CoordinatesTest extends TestCase
{
    public function testValidCoordinatesCanBeCreated(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);

        $this->assertEquals(40.7128, $coordinates->getLatitude());
        $this->assertEquals(-74.0060, $coordinates->getLongitude());
    }

    public function testInvalidLatitudeTooHighThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Latitude must be between -90 and 90 degrees');

        new Coordinates(91.0, 0.0);
    }

    public function testInvalidLatitudeTooLowThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Latitude must be between -90 and 90 degrees');

        new Coordinates(-91.0, 0.0);
    }

    public function testInvalidLongitudeTooHighThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Longitude must be between -180 and 180 degrees');

        new Coordinates(0.0, 181.0);
    }

    public function testInvalidLongitudeTooLowThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Longitude must be between -180 and 180 degrees');

        new Coordinates(0.0, -181.0);
    }

    public function testBoundaryLatitudeValuesAreValid(): void
    {
        $northPole = new Coordinates(90.0, 0.0);
        $southPole = new Coordinates(-90.0, 0.0);

        $this->assertEquals(90.0, $northPole->getLatitude());
        $this->assertEquals(-90.0, $southPole->getLatitude());
    }

    public function testBoundaryLongitudeValuesAreValid(): void
    {
        $east = new Coordinates(0.0, 180.0);
        $west = new Coordinates(0.0, -180.0);

        $this->assertEquals(180.0, $east->getLongitude());
        $this->assertEquals(-180.0, $west->getLongitude());
    }

    public function testCoordinatesEquality(): void
    {
        $coords1 = new Coordinates(40.7128, -74.0060);
        $coords2 = new Coordinates(40.7128, -74.0060);
        $coords3 = new Coordinates(34.0522, -118.2437);

        $this->assertTrue($coords1->equals($coords2));
        $this->assertFalse($coords1->equals($coords3));
    }

    public function testDistanceCalculation(): void
    {
        $nyc = new Coordinates(40.7128, -74.0060);
        $la = new Coordinates(34.0522, -118.2437);

        $distance = $nyc->distanceTo($la);

        // Distance between NYC and LA is approximately 3944 km
        $this->assertGreaterThan(3900, $distance);
        $this->assertLessThan(4000, $distance);
    }

    public function testDistanceToSameLocationIsZero(): void
    {
        $coords = new Coordinates(40.7128, -74.0060);

        $distance = $coords->distanceTo($coords);

        $this->assertEquals(0.0, $distance);
    }

    public function testToString(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);

        $this->assertEquals('40.7128,-74.006', $coordinates->__toString());
    }

    public function testToArray(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);

        $array = $coordinates->toArray();

        $this->assertEquals([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ], $array);
    }
}
