<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Concerns;

use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;
use Mockery;

class SubscribableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[
            [Channel::class],
            [User::class],
        ];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testSubscriptions(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame(1, $model->subscribableSubscriptions()->count());
        self::assertSame(1, $model->subscribableSubscriptions->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testSubscribersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame(1, $model->subscribersCount());
        $user->unsubscribe($model);
        self::assertSame(1, $model->subscribersCount());
        $model->loadCount('subscribers');
        self::assertSame(0, $model->subscribersCount());
    }

    public function data(): array
    {
        return [
            [0, '0', '0', '0'],
            [1, '1', '1', '1'],
            [12, '12', '12', '12'],
            [123, '123', '123', '123'],
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

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testIsSubscribedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isSubscribedBy($model));
        $user->subscribe($model);
        self::assertTrue($model->isSubscribedBy($user));
        $model->load('subscribers');
        $user->unsubscribe($model);
        self::assertTrue($model->isSubscribedBy($user));
        $model->load('subscribers');
        self::assertFalse($model->isSubscribedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotSubscribedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotSubscribedBy($model));
        $user->subscribe($model);
        self::assertFalse($model->isNotSubscribedBy($user));
        $model->load('subscribers');
        $user->unsubscribe($model);
        self::assertFalse($model->isNotSubscribedBy($user));
        $model->load('subscribers');
        self::assertTrue($model->isNotSubscribedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testSubscribers(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame(1, $model->subscribers()->count());
        $user->unsubscribe($model);
        self::assertSame(0, $model->subscribers()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereSubscribedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame(1, $modelClass::query()->whereSubscribedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereSubscribedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotSubscribedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotSubscribedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotSubscribedBy($other)->count());
    }
}
