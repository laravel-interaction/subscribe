# Laravel Subscribe

User subscribe/unsubscribe behaviour for Laravel.

<p align="center">
<a href="https://packagist.org/packages/laravel-interaction/subscribe"><img src="https://poser.pugx.org/laravel-interaction/subscribe/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/subscribe"><img src="https://poser.pugx.org/laravel-interaction/subscribe/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-interaction/subscribe"><img src="https://poser.pugx.org/laravel-interaction/subscribe/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/subscribe"><img src="https://poser.pugx.org/laravel-interaction/subscribe/license" alt="License"></a>
</p>

## Introduction

It used for users to subscribe to the model(user/topic/channel) in order to receive notifications.

![](https://img.shields.io/badge/%F0%9F%94%94-1.2k-green?style=social)

## Installation

### Requirements

- [PHP 8.0+](https://php.net/releases/)
- [Composer](https://getcomposer.org)
- [Laravel 8.0+](https://laravel.com/docs/releases)

### Instructions

Require Laravel Subscribe using [Composer](https://getcomposer.org).

```bash
composer require laravel-interaction/subscribe
```

Publish configuration and migrations

```bash
php artisan vendor:publish --tag=subscribe-config
php artisan vendor:publish --tag=subscribe-migrations
```

Run database migrations.

```bash
php artisan migrate
```

## Usage

### Setup Subscriber

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Subscribe\Concerns\Subscriber;

class User extends Model
{
    use Subscriber;
}
```

### Setup Subscribable

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Subscribe\Concerns\Subscribable;

class Channel extends Model
{
    use Subscribable;
}
```

### Subscriber

```php
use LaravelInteraction\Subscribe\Tests\Models\Channel;
/** @var \LaravelInteraction\Subscribe\Tests\Models\User $user */
/** @var \LaravelInteraction\Subscribe\Tests\Models\Channel $channel */
// Subscribe to Subscribable
$user->subscribe($channel);
$user->unsubscribe($channel);
$user->toggleSubscribe($channel);

// Compare Subscribable
$user->hasSubscribed($channel);
$user->hasNotSubscribed($channel);

// Get subscribed info
$user->subscriberSubscriptions()->count(); 

// with type
$user->subscriberSubscriptions()->withType(Channel::class)->count(); 

// get subscribed channels
Channel::query()->whereSubscribedBy($user)->get();

// get subscribed channels doesnt subscribed
Channel::query()->whereNotSubscribedBy($user)->get();
```

### Subscribable

```php
use LaravelInteraction\Subscribe\Tests\Models\User;
use LaravelInteraction\Subscribe\Tests\Models\Channel;
/** @var \LaravelInteraction\Subscribe\Tests\Models\User $user */
/** @var \LaravelInteraction\Subscribe\Tests\Models\Channel $channel */
// Compare Subscriber
$channel->isSubscribedBy($user); 
$channel->isNotSubscribedBy($user);
// Get subscribers info
$channel->subscribers->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('subscribers')->get();
$channels->each(function (Channel $channel){
    echo $channel->subscribers()->count(); // 1100
    echo $channel->subscribers_count; // "1100"
    echo $channel->subscribersCount(); // 1100
    echo $channel->subscribersCountForHumans(); // "1.1K"
});
```

### Events

| Event | Fired |
| --- | --- |
| `LaravelInteraction\Subscribe\Events\Subscribed` | When an object get subscribed. |
| `LaravelInteraction\Subscribe\Events\Unsubscribed` | When an object get unsubscribed. |

## License

Laravel Subscribe is an open-sourced software licensed under the [MIT license](LICENSE).
