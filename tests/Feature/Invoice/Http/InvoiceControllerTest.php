<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice\Http;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Invoices\Application\Services\InvoiceService;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function setUp(): void
    {
        $this->setUpFaker();
        parent::setUp();
    }

    public function testCreateInvoice(): void
    {
        $data = [
            'customerName' => $this->faker->name,
            'customerEmail' => $this->faker->email,
        ];

        $uri = route('invoices.create');
        $response = $this->postJson($uri, $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'id',
                'customerName',
                'customerEmail',
                'status',
            ]);
    }

    public function testSendInvoice(): void
    {
        $invoiceId = Str::uuid()->toString();
        $invoiceService = $this->mock(InvoiceService::class);

        $invoiceService
            ->shouldReceive('sendInvoice')
            ->withArgs([$invoiceId])
            ->andReturnTrue();

        $uri = route('invoices.send', ['id' => $invoiceId]);
        $response = $this->postJson($uri);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Invoice sent successfully',
            ]);
    }


    public function testSendInvalidInvoice(): void
    {
        $invoiceId = Str::uuid()->toString();

        $uri = route('invoices.send', ['id' => $invoiceId]);
        $response = $this->postJson($uri);

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Invoice not found.',
            ]);
    }

    public function testInvalidInvoice(): void
    {
        $uri = route('invoices.view', ['id' => Str::uuid()->toString()]);
        $response = $this->getJson($uri);

        $response->assertNotFound()
            ->assertJson([
                'error' => 'Invoice not found',
            ]);
    }
}
