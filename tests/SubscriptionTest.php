<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Subscribe\Subscription;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;

class SubscriptionTest extends TestCase
{
    /**
     * @var \LaravelInteraction\Subscribe\Tests\Models\User
     */
    protected $user;

    /**
     * @var \LaravelInteraction\Subscribe\Tests\Models\Channel
     */
    protected $channel;

    /**
     * @var \LaravelInteraction\Subscribe\Subscription
     */
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->subscribe($this->channel);
        $this->subscription = Subscription::query()->firstOrFail();
    }

    public function testSubscriptionTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->subscription->created_at);
        self::assertInstanceOf(Carbon::class, $this->subscription->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Subscription::query()->withType(Channel::class)->count());
        self::assertSame(0, Subscription::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('subscribe.table_names.subscriptions'), $this->subscription->getTable());
    }

    public function testSubscriber(): void
    {
        self::assertInstanceOf(User::class, $this->subscription->subscriber);
    }

    public function testSubscribable(): void
    {
        self::assertInstanceOf(Channel::class, $this->subscription->subscribable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->subscription->user);
    }

    public function testIsSubscribedTo(): void
    {
        self::assertTrue($this->subscription->isSubscribedTo($this->channel));
        self::assertFalse($this->subscription->isSubscribedTo($this->user));
    }

    public function testIsSubscribedBy(): void
    {
        self::assertFalse($this->subscription->isSubscribedBy($this->channel));
        self::assertTrue($this->subscription->isSubscribedBy($this->user));
    }
}
