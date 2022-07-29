<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Subscribe\Subscription[] $subscribableSubscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Subscribe\Concerns\Subscriber[] $subscribers
 * @property-read string|int|null $subscribers_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereSubscribedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotSubscribedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Subscribable
{
    public function isNotSubscribedBy(Model $user): bool
    {
        return ! $this->isSubscribedBy($user);
    }

    public function isSubscribedBy(Model $user): bool
    {
        if (! is_a($user, config('subscribe.models.user'))) {
            return false;
        }

        $subscribersLoaded = $this->relationLoaded('subscribers');

        if ($subscribersLoaded) {
            return $this->subscribers->contains($user);
        }

        return ($this->relationLoaded(
            'subscribableSubscriptions'
        ) ? $this->subscribableSubscriptions : $this->subscribableSubscriptions())
            ->where(config('subscribe.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function scopeWhereNotSubscribedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'subscribers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereSubscribedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'subscribers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function subscribableSubscriptions(): MorphMany
    {
        return $this->morphMany(config('subscribe.models.pivot'), 'subscribable');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->morphToMany(
            config('subscribe.models.user'),
            'subscribable',
            config('subscribe.models.pivot'),
            null,
            config('subscribe.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function subscribersCount(): int
    {
        if ($this->subscribers_count !== null) {
            return (int) $this->subscribers_count;
        }

        $this->loadCount('subscribers');

        return (int) $this->subscribers_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function subscribersCountForHumans(
        int $precision = 1,
        int $mode = PHP_ROUND_HALF_UP,
        $divisors = null
    ): string {
        return Interaction::numberForHumans(
            $this->subscribersCount(),
            $precision,
            $mode,
            $divisors ?? config('subscribe.divisors')
        );
    }
}
