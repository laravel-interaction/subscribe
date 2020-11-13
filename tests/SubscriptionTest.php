<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests;

use Zing\LaravelSubscribe\Subscription;
use Zing\LaravelSubscribe\Tests\Models\Channel;
use Zing\LaravelSubscribe\Tests\Models\User;

class SubscriptionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Subscription::query()->withType(Channel::class)->count());
        self::assertSame(0, Subscription::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        /** @var \Zing\LaravelSubscribe\Subscription $subscription */
        $subscription = Subscription::query()->first();
        self::assertSame(config('subscribe.table_names.subscriptions'), $subscription->getTable());
    }

    public function testSubscriber(): void
    {
        /** @var \Zing\LaravelSubscribe\Subscription $subscription */
        $subscription = Subscription::query()->first();
        self::assertInstanceOf(User::class, $subscription->subscriber);
    }

    public function testSubscribable(): void
    {
        /** @var \Zing\LaravelSubscribe\Subscription $subscription */
        $subscription = Subscription::query()->first();
        self::assertInstanceOf(Channel::class, $subscription->subscribable);
    }

    public function testUser(): void
    {
        /** @var \Zing\LaravelSubscribe\Subscription $subscription */
        $subscription = Subscription::query()->first();
        self::assertInstanceOf(User::class, $subscription->user);
    }
}
