<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Concerns;

use LaravelInteraction\Subscribe\Tests\Models\Channel;
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\TestCase;

class SubscribableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[[Channel::class], [User::class]];
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

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Subscribe\Tests\Models\User|\LaravelInteraction\Subscribe\Tests\Models\Channel|string $modelClass
     */
    public function testSubscribersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->subscribe($model);
        self::assertSame('1', $model->subscribersCountForHumans());
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
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotSubscribedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotSubscribedBy($other)->count());
    }
}
