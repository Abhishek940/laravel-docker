<?php

namespace App\Kafka\Handlers;

use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\KafkaConsumerMessage;

class ProductEventHandler
{
    public function __invoke(KafkaConsumerMessage $message): void
    {
        $body = $message->getBody();
        $headers = $message->getHeaders();

        Log::info('Product event consumed from Kafka', [
            'topic' => $message->getTopicName(),
            'partition' => $message->getPartition(),
            'offset' => $message->getOffset(),
            'key' => $message->getKey(),
            'headers' => $headers,
            'body' => $body,
        ]);

        $event = $body['event'] ?? ($headers['event'] ?? null);
        $product = $body['product'] ?? [];

        match ($event) {
            'product.created' => $this->onCreated($product),
            'product.updated' => $this->onUpdated($product),
            'product.deleted' => $this->onDeleted($product),
            default => Log::warning('Unknown product Kafka event', ['event' => $event]),
        };
    }

    private function onCreated(array $product): void
    {
        // Example: sync search index, notify warehouse, etc.
        Log::info('Handled product.created', ['product_id' => $product['id'] ?? null]);
    }

    private function onUpdated(array $product): void
    {
        Log::info('Handled product.updated', ['product_id' => $product['id'] ?? null]);
    }

    private function onDeleted(array $product): void
    {
        Log::info('Handled product.deleted', ['product_id' => $product['id'] ?? null]);
    }
}
