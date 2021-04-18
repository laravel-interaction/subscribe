<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Concerns;

use LaravelInteraction\Subscribe\Subscription;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

class SubscriberTest extends TestCase
{
    public function testSubscribe(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        $this->assertDatabaseHas(
            Subscription::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'subscribable_type' => $channel->getMorphClass(),
                'subscribable_id' => $channel->getKey(),
            ]
        );
        $user->load('subscriberSubscriptions');
        $user->unsubscribe($channel);
        $user->load('subscriberSubscriptions');
        $user->subscribe($channel);
    }

    public function testUnsubscribe(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        $this->assertDatabaseHas(
            Subscription::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'subscribable_type' => $channel->getMorphClass(),
                'subscribable_id' => $channel->getKey(),
            ]
        );
        $user->unsubscribe($channel);
        $this->assertDatabaseMissing(
            Subscription::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'subscribable_type' => $channel->getMorphClass(),
                'subscribable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleSubscribe(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleSubscribe($channel);
        $this->assertDatabaseHas(
            Subscription::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'subscribable_type' => $channel->getMorphClass(),
                'subscribable_id' => $channel->getKey(),
            ]
        );
        $user->toggleSubscribe($channel);
        $this->assertDatabaseMissing(
            Subscription::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'subscribable_type' => $channel->getMorphClass(),
                'subscribable_id' => $channel->getKey(),
            ]
        );
    }

    public function testSubscriptions(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleSubscribe($channel);
        self::assertSame(1, $user->subscriberSubscriptions()->count());
        self::assertSame(1, $user->subscriberSubscriptions->count());
    }

    public function testHasSubscribed(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleSubscribe($channel);
        self::assertTrue($user->hasSubscribed($channel));
        $user->toggleSubscribe($channel);
        $user->load('subscriberSubscriptions');
        self::assertFalse($user->hasSubscribed($channel));
    }

    public function testHasNotSubscribed(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleSubscribe($channel);
        self::assertFalse($user->hasNotSubscribed($channel));
        $user->toggleSubscribe($channel);
        self::assertTrue($user->hasNotSubscribed($channel));
    }
}
