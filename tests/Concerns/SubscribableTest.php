<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Concerns;

use Mockery;
use Zing\LaravelSubscribe\Tests\Models\Channel;
use Zing\LaravelSubscribe\Tests\Models\User;
use Zing\LaravelSubscribe\Tests\TestCase;

class SubscribableTest extends TestCase
{
    public function testSubscriptions(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        self::assertSame(1, $channel->subscriptions()->count());
        self::assertSame(1, $channel->subscriptions->count());
    }

    public function testSubscribersCount(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        self::assertSame(1, $channel->subscribersCount());
        $user->unsubscribe($channel);
        self::assertSame(1, $channel->subscribersCount());
        $channel->loadCount('subscribers');
        self::assertSame(0, $channel->subscribersCount());
    }

    public function data(): array
    {
        return [
            [0, '0.0', '0.00', '0.00'],
            [1, '1.0', '1.00', '1.00'],
            [12, '12.0', '12.00', '12.00'],
            [123, '123.0', '123.00', '123.00'],
            [12345, '12.3K', '12.35K', '12.34K'],
            [1234567, '1.2M', '1.23M', '1.23M'],
            [123456789, '123.5M', '123.46M', '123.46M'],
            [12345678901, '12.3B', '12.35B', '12.35B'],
            [1234567890123, '1.2T', '1.23T', '1.23T'],
            [1234567890123456, '1.2Qa', '1.23Qa', '1.23Qa'],
            [1234567890123456789, '1.2Qi', '1.23Qi', '1.23Qi'],
        ];
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testSubscribersCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('subscribersCountForHumans')->passthru();
        $channel->shouldReceive('subscribersCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->subscribersCountForHumans());
        self::assertSame($twoPrecision, $channel->subscribersCountForHumans(2));
        self::assertSame($halfDown, $channel->subscribersCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    public function testIsSubscribedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertFalse($channel->isSubscribedBy($channel));
        $user->subscribe($channel);
        self::assertTrue($channel->isSubscribedBy($user));
        $channel->load('subscribers');
        $user->unsubscribe($channel);
        self::assertTrue($channel->isSubscribedBy($user));
        $channel->load('subscribers');
        self::assertFalse($channel->isSubscribedBy($user));
    }

    public function testIsNotSubscribedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertTrue($channel->isNotSubscribedBy($channel));
        $user->subscribe($channel);
        self::assertFalse($channel->isNotSubscribedBy($user));
        $channel->load('subscribers');
        $user->unsubscribe($channel);
        self::assertFalse($channel->isNotSubscribedBy($user));
        $channel->load('subscribers');
        self::assertTrue($channel->isNotSubscribedBy($user));
    }

    public function testSubscribers(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->subscribe($channel);
        self::assertSame(1, $channel->subscribers()->count());
        $user->unsubscribe($channel);
        self::assertSame(0, $channel->subscribers()->count());
    }
}
