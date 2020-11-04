<?php


namespace Zing\LaravelSubscribe\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscriber;

class User extends Model
{
    use Subscriber;
}