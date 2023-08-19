<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Configuration;

use LaravelInteraction\Subscribe\Subscription;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'subscribe.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $subscription = new Subscription();
        $this->assertSame('string', $subscription->getKeyType());
    }

    public function testIncrementing(): void
    {
        $subscription = new Subscription();
        $this->assertFalse($subscription->getIncrementing());
    }

    public function testKeyName(): void
    {
        $subscription = new Subscription();
        $this->assertSame('uuid', $subscription->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        $this->assertIsString($user->subscriberSubscriptions()->firstOrFail()->getKey());
    }
}
