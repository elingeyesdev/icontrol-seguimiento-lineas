<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    public function test_swagger_documentation_can_be_generated(): void
    {
        $this->artisan('l5-swagger:generate')->assertExitCode(0);

        $docsPath = storage_path('api-docs/api-docs.json');

        $this->assertFileExists($docsPath);

        $document = json_decode(file_get_contents($docsPath), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('API Linea 61 Control', $document['info']['title']);
        $this->assertArrayHasKey('/api/login', $document['paths']);
        $this->assertArrayHasKey('/api/conductores', $document['paths']);
        $this->assertArrayHasKey('sanctum', $document['components']['securitySchemes']);
    }

    public function test_swagger_ui_route_is_available(): void
    {
        $this->artisan('l5-swagger:generate')->assertExitCode(0);

        $this->get('/api/documentation')
            ->assertOk()
            ->assertSee('SwaggerUIBundle', false);
    }
}
