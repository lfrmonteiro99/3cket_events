<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Validation;

use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Validation\ValidatorBag;
use PHPUnit\Framework\TestCase;

class ValidatorBagTest extends TestCase
{
    private EventIdValidator $eventIdValidator;
    private PaginationValidator $paginationValidator;
    private ValidatorBag $validatorBag;

    public function testEventIdReturnsCorrectValidator(): void
    {
        $validator = $this->validatorBag->eventId();

        $this->assertInstanceOf(EventIdValidator::class, $validator);
        $this->assertSame($this->eventIdValidator, $validator);
    }

    public function testPaginationReturnsCorrectValidator(): void
    {
        $validator = $this->validatorBag->pagination();

        $this->assertInstanceOf(PaginationValidator::class, $validator);
        $this->assertSame($this->paginationValidator, $validator);
    }

    public function testEventIdValidatorWorksThroughBag(): void
    {
        $validator = $this->validatorBag->eventId();

        // Test valid ID
        $result = $validator->validate('123');
        $this->assertTrue($result->isValid());

        // Test invalid ID
        $result = $validator->validate('abc');
        $this->assertFalse($result->isValid());
        $this->assertContains('Event ID must be a numeric value', $result->getErrors());
    }

    public function testPaginationValidatorWorksThroughBag(): void
    {
        $validator = $this->validatorBag->pagination();

        // Test valid pagination data
        $validData = [
            'page' => 1,
            'page_size' => 10,
            'sort_by' => 'id',
            'sort_direction' => 'ASC',
        ];
        $result = $validator->validate($validData);
        $this->assertTrue($result->isValid());

        // Test invalid pagination data
        $invalidData = [
            'page' => -1,
            'page_size' => 200,
        ];
        $result = $validator->validate($invalidData);
        $this->assertFalse($result->isValid());
    }

    protected function setUp(): void
    {
        $this->eventIdValidator = new EventIdValidator();
        $this->paginationValidator = new PaginationValidator();
        $this->validatorBag = new ValidatorBag(
            $this->eventIdValidator,
            $this->paginationValidator
        );
    }
}
