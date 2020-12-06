# Laravel Subscribe

User subscribe/unsubscribe behaviour for Laravel.

<p align="center">
<a href="https://github.com/zingimmick/laravel-subscribe/actions"><img src="https://github.com/zingimmick/laravel-subscribe/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-subscribe"><img src="https://codecov.io/gh/zingimmick/laravel-subscribe/branch/master/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-subscribe"><img src="https://poser.pugx.org/zing/laravel-subscribe/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-subscribe"><img src="https://poser.pugx.org/zing/laravel-subscribe/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-subscribe"><img src="https://poser.pugx.org/zing/laravel-subscribe/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-subscribe"><img src="https://poser.pugx.org/zing/laravel-subscribe/license" alt="License"></a>
<a href="https://codeclimate.com/github/zingimmick/laravel-subscribe/maintainability"><img src="https://api.codeclimate.com/v1/badges/82036f5ecf894e9c395d/maintainability" alt="Code Climate" /></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Subscribe using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-subscribe
```

## Usage

### Setup Subscriber

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscriber;

class User extends Model
{
    use Subscriber;
}
```

### Setup Subscribable

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscribable;

class Channel extends Model
{
    use Subscribable;
}
```

### Subscriber

```php
use Zing\LaravelSubscribe\Tests\Models\Channel;
/** @var \Zing\LaravelSubscribe\Tests\Models\User $user */
/** @var \Zing\LaravelSubscribe\Tests\Models\Channel $channel */
// Subscribe to Subscribable
$user->subscribe($channel);
$user->unsubscribe($channel);
$user->toggleSubscribe($channel);

// Compare Subscribable
$user->hasSubscribed($channel);
$user->hasNotSubscribed($channel);

// Get subscribed info
$user->subscriptions()->count(); 

// with type
$user->subscriptions()->withType(Channel::class)->count(); 

// get subscribed channels
Channel::query()->whereSubscribedBy($user)->get();

// get subscribed channels doesnt subscribed
Channel::query()->whereNotSubscribedBy($user)->get();
```

### Subscribable

```php
use Zing\LaravelSubscribe\Tests\Models\User;
use Zing\LaravelSubscribe\Tests\Models\Channel;
/** @var \Zing\LaravelSubscribe\Tests\Models\User $user */
/** @var \Zing\LaravelSubscribe\Tests\Models\Channel $channel */
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

## License

Laravel Subscribe is an open-sourced software licensed under the [MIT license](LICENSE).
