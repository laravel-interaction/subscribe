<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelSubscribe\Events\Unsubscribed;
use Zing\LaravelSubscribe\Tests\Models\Channel;
use Zing\LaravelSubscribe\Tests\Models\User;
use Zing\LaravelSubscribe\Tests\TestCase;

class UnsubscribedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        Event::fake();
        $user->unsubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        Event::fake();
        $user->unsubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->toggleSubscribe($channel);
        $user->toggleSubscribe($channel);
        Event::assertDispatchedTimes(Unsubscribed::class);
    }
}
