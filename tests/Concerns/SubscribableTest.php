<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Concerns;

use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

/**
 * @internal
 */
final class SubscribableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Subscribe\Tests\Models\Channel|\LaravelInteraction\Subscribe\Tests\Models\User>>>
     */
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
     */
    public function testSubscribersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame('1', $model->subscribersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
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
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotSubscribedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotSubscribedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotSubscribedBy($other)->count());
    }
}
