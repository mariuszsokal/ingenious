<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Invoices\Application\Services\InvoiceService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

readonly class InvoiceController
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function view(string $id): JsonResponse
    {
        $uuid = Uuid::fromString($id);
        $invoice = $this->invoiceService->findInvoiceById($uuid);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($this->serializeInvoice($invoice));
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customerName' => 'required|string',
            'customerEmail' => 'required|email',
        ]);

        $invoice = $this->invoiceService->createInvoice(
            $validated['customerName'],
            $validated['customerEmail']
        );

        return response()->json($this->serializeInvoice($invoice), Response::HTTP_CREATED);
    }

    public function send(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $this->invoiceService->sendInvoice($uuid);
            return response()->json(['message' => 'Invoice sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function serializeInvoice($invoice): array
    {
        return [
            'id' => (string) $invoice->getId(),
            'status' => $invoice->getStatus()->getValue(),
            'customerName' => $invoice->getCustomerName(),
            'customerEmail' => $invoice->getCustomerEmail(),
            'productLines' => array_map(fn ($line) => $this->serializeProductLine($line), $invoice->getProductLines()),
            'totalPrice' => $invoice->getTotalPrice(),
        ];
    }

    private function serializeProductLine($productLine): array
    {
        return [
            'productName' => $productLine->getProductName(),
            'quantity' => $productLine->getQuantity()->getValue(),
            'unitPrice' => $productLine->getUnitPrice()->getValue(),
            'totalPrice' => $productLine->getTotalUnitPrice(),
        ];
    }
}
