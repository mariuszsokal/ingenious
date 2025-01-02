<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Modules\Invoices\Application\Listeners\UpdateInvoiceStatusListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ResourceDeliveredEvent::class => [
            UpdateInvoiceStatusListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
