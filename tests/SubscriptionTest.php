<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Subscribe\Subscription;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;

/**
 * @internal
 */
final class SubscriptionTest extends TestCase
{
    private \LaravelInteraction\Subscribe\Tests\Models\User $user;

    private \LaravelInteraction\Subscribe\Tests\Models\Channel $channel;

    private \LaravelInteraction\Subscribe\Subscription $subscription;

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
        $this->assertInstanceOf(Carbon::class, $this->subscription->created_at);
        $this->assertInstanceOf(Carbon::class, $this->subscription->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Subscription::query()->withType(Channel::class)->count());
        $this->assertSame(0, Subscription::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('subscribe.table_names.pivot'), $this->subscription->getTable());
    }

    public function testSubscriber(): void
    {
        $this->assertInstanceOf(User::class, $this->subscription->subscriber);
    }

    public function testSubscribable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->subscription->subscribable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->subscription->user);
    }

    public function testIsSubscribedTo(): void
    {
        $this->assertTrue($this->subscription->isSubscribedTo($this->channel));
        $this->assertFalse($this->subscription->isSubscribedTo($this->user));
    }

    public function testIsSubscribedBy(): void
    {
        $this->assertFalse($this->subscription->isSubscribedBy($this->channel));
        $this->assertTrue($this->subscription->isSubscribedBy($this->user));
    }
}
