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

    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     *
     * @return bool
     */
    public function isSubscribedBy(Model $user): bool
    {
        if (! is_a($user, config('subscribe.models.user'))) {
            return false;
        }
        $subscribersThisRelationLoaded = $this->relationLoaded('subscribers');

        if ($subscribersThisRelationLoaded) {
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
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereSubscribedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'subscribers',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscribableSubscriptions(): MorphMany
    {
        return $this->morphMany(config('subscribe.models.subscription'), 'subscribable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers(): BelongsToMany
    {
        return $this->morphToMany(
            config('subscribe.models.user'),
            'subscribable',
            config('subscribe.models.subscription'),
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

    public function subscribersCountForHumans($precision = 1, $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->subscribersCount(),
            $precision,
            $mode,
            $divisors ?? config('subscribe.divisors')
        );
    }
}
