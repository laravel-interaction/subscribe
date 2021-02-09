<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Subscribe\Events\Unsubscribed;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

class UnsubscribedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        Event::fake([Unsubscribed::class]);
        $user->unsubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        Event::fake([Unsubscribed::class]);
        $user->unsubscribe($channel);
        $user->unsubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unsubscribed::class]);
        $user->toggleSubscribe($channel);
        $user->toggleSubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }
}
