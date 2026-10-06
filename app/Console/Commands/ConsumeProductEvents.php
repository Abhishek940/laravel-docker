<?php

namespace App\Console\Commands;

use App\Kafka\Handlers\ProductEventHandler;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class ConsumeProductEvents extends Command
{
    protected $signature = 'kafka:consume-products';

    protected $description = 'Consume product lifecycle events from Kafka';

    public function handle(ProductEventHandler $handler): int
    {
        $topic = config('kafka.topics.products');

        $this->info("Consuming topic [{$topic}]...");

        $consumer = Kafka::createConsumer([$topic])
            ->withAutoCommit()
            ->withHandler($handler)
            ->build();

        $consumer->consume();

        return self::SUCCESS;
    }
}
