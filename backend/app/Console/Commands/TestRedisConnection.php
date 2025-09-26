<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class TestRedisConnection extends Command
{
    protected $signature = 'test:redis';
    protected $description = 'Tests the Redis connection and publishes a message.';

    public function handle()
    {
        $this->info('Testing Redis connection...');

        try {
            Redis::ping();
            $this->info('Redis connection successful!');

            $channel = 'test:channel';
            $message = json_encode(['status' => 'success', 'message' => 'Hello from Laravel!']);

            Redis::publish($channel, $message);
            $this->info('Message published to channel: ' . $channel);

        } catch (\Exception $e) {
            $this->error('Redis connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
