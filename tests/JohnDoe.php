<?php

namespace CollabCorp\LaravelFeatureToggle\Tests;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class JohnDoe extends Model implements AuthenticatableContract
{
	use Authenticatable;

	public $name = 'John Doe';

	public $email = 'john@example.com';

	public $password = 'secret';
}