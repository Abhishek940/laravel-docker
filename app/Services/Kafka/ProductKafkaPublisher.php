<?php

namespace App\Services\Kafka;

use Illuminate\Support\Facades\Log;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Throwable;

class ProductKafkaPublisher
{
    public function publish(string $event, array $product): void
    {
        $topic = config('kafka.topics.products');
        $productId = (string) ($product['id'] ?? '');

        $message = new Message(
            topicName: $topic,
            headers: [
                'event' => $event,
                'source' => config('app.name', 'laravel'),
            ],
            body: [
                'event' => $event,
                'product' => $product,
                'occurred_at' => now()->toISOString(),
            ],
            key: $productId !== '' ? $productId : null,
        );

        try {
            Kafka::publishOn($topic)
                ->withMessage($message)
                ->send();

            Log::info('Product event published to Kafka', [
                'topic' => $topic,
                'event' => $event,
                'product_id' => $productId,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to publish product event to Kafka', [
                'topic' => $topic,
                'event' => $event,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
