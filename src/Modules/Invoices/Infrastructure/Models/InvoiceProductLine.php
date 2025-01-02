<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProductLine extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'invoice_product_lines';

    protected $fillable = [
        'id',
        'invoice_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
