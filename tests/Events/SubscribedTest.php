<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Subscribe\Events\Subscribed;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

class SubscribedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Subscribed::class]);
        $user->subscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Subscribed::class]);
        $user->subscribe($channel);
        $user->subscribe($channel);
        $user->subscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Subscribed::class]);
        $user->toggleSubscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }
}
