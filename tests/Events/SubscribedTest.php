<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelSubscribe\Events\Subscribed;
use Zing\LaravelSubscribe\Tests\Models\Channel;
use Zing\LaravelSubscribe\Tests\Models\User;
use Zing\LaravelSubscribe\Tests\TestCase;

class SubscribedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->subscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->subscribe($channel);
        $user->subscribe($channel);
        $user->subscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->toggleSubscribe($channel);
        Event::assertDispatchedTimes(Subscribed::class);
    }
}
