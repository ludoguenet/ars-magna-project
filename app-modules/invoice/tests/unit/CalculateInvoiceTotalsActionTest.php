<?php

namespace AppModules\Invoice\tests\unit;

use AppModules\Invoice\src\Actions\CalculateInvoiceTotalsAction;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateInvoiceTotalsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_invoice_totals_correctly(): void
    {
        // Arrange
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::DRAFT,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'tax_rate' => 20.00,
            'discount_amount' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'tax_rate' => 10.00,
            'discount_amount' => 5.00,
        ]);

        $action = new CalculateInvoiceTotalsAction;

        // Act
        $result = $action->handle($invoice->fresh());

        // Assert
        $this->assertEquals(245.00, $result->subtotal); // (2*100) + (1*50-5) = 200 + 45 = 245
        $this->assertEquals(44.50, $result->tax_amount); // (200*0.20) + (45*0.10) = 40 + 4.5 = 44.5
        $this->assertEquals(289.50, $result->total); // 245 + 44.5 = 289.5
    }
}
