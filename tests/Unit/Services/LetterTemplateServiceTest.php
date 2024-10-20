<?php

namespace Tests\Unit\Services;

use App\Models\LetterTemplate;
use App\Services\LetterTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LetterTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return LetterTemplateService
     */
    public function getService()
    {
        return new LetterTemplateService();
    }

    /**
     * Test validateLetterTemplate method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'letter' => 'Sample Letter',
            'status' => 1,
        ];

        $validatedData = $service->validateLetterTemplate($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateLetterTemplate method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'letter' => '', // invalid
            'status' => 'not_an_integer', // invalid
        ];

        $service->validateLetterTemplate($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateLetterTemplate()
    {
        $service = $this->getService();

        $data = [
            'letter' => 'Sample Letter',
            'status' => 1,
        ];

        $letterTemplate = $service->create($data);

        $this->assertInstanceOf(LetterTemplate::class, $letterTemplate);
        $this->assertEquals($data['letter'], $letterTemplate->letter);
        $this->assertEquals($data['status'], $letterTemplate->status);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateLetterTemplate()
    {
        $service = $this->getService();

        $letterTemplate = LetterTemplate::factory()->create([
            'letter' => 'Old Letter',
            'status' => 0,
        ]);

        $data = [
            'id' => $letterTemplate->id,
            'letter' => 'Updated Letter',
            'status' => 1,
        ];

        $updatedLetterTemplate = $service->update($data);

        $this->assertEquals('Updated Letter', $updatedLetterTemplate->letter);
        $this->assertEquals(1, $updatedLetterTemplate->status);
    }

    /**
     * Test update method with invalid data.
     *
     * @return void
     */
    public function testUpdateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $letterTemplate = LetterTemplate::factory()->create();

        $data = [
            'id' => $letterTemplate->id,
            'letter' => '', // invalid
        ];

        $service->update($data);
    }

    /**
     * Test update method without an ID.
     *
     * @return void
     */
    public function testUpdateWithoutId()
    {
        $this->expectException(\InvalidArgumentException::class);

        $service = $this->getService();

        $data = [
            'letter' => 'New Letter',
            'status' => 1,
        ];

        $service->update($data);
    }
}
