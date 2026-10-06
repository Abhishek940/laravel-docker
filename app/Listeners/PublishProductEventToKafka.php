<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Services\Kafka\ProductKafkaPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishProductEventToKafka implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly ProductKafkaPublisher $publisher)
    {
    }

    public function handle(ProductCreated|ProductUpdated|ProductDeleted $event): void
    {
        $eventName = match (true) {
            $event instanceof ProductCreated => 'product.created',
            $event instanceof ProductUpdated => 'product.updated',
            $event instanceof ProductDeleted => 'product.deleted',
        };

        $this->publisher->publish($eventName, $event->product);
    }
}
