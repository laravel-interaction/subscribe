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
        $this->assertSame(1, $model->subscribableSubscriptions()->count());
        $this->assertSame(1, $model->subscribableSubscriptions->count());
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
        $this->assertSame(1, $model->subscribersCount());
        $user->unsubscribe($model);
        $this->assertSame(1, $model->subscribersCount());
        $model->loadCount('subscribers');
        $this->assertSame(0, $model->subscribersCount());
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
        $this->assertSame('1', $model->subscribersCountForHumans());
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
        $this->assertFalse($model->isSubscribedBy($model));
        $user->subscribe($model);
        $this->assertTrue($model->isSubscribedBy($user));
        $model->load('subscribers');
        $user->unsubscribe($model);
        $this->assertTrue($model->isSubscribedBy($user));
        $model->load('subscribers');
        $this->assertFalse($model->isSubscribedBy($user));
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
        $this->assertTrue($model->isNotSubscribedBy($model));
        $user->subscribe($model);
        $this->assertFalse($model->isNotSubscribedBy($user));
        $model->load('subscribers');
        $user->unsubscribe($model);
        $this->assertFalse($model->isNotSubscribedBy($user));
        $model->load('subscribers');
        $this->assertTrue($model->isNotSubscribedBy($user));
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
        $this->assertSame(1, $model->subscribers()->count());
        $user->unsubscribe($model);
        $this->assertSame(0, $model->subscribers()->count());
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
        $this->assertSame(1, $modelClass::query()->whereSubscribedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereSubscribedBy($other)->count());
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
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotSubscribedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotSubscribedBy($other)->count());
    }
}
