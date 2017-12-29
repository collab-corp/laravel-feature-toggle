# Laravel 5 Feature toggle

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
    - [Binding callbacks](#binding_callbacks)
- [Usage](#usage)
    - [JavaScript] (#javascript)
- [Testing](#testing)
- [Events](#events)
- [Issues](#issues)
- [License](#license)

<a name="installation" />

## Installation

## For Laravel ~5

    composer require collab-corp/laravel-feature-toggle

As with any package, it's a good idea to refresh composer autoloader.
```bash
composer dump-autoload
```

<a name="configuration"/>

## Configuration

To publish `features.php` config file, run the following, `vendor:publish` command.

```bash
php artisan vendor:publish --provider="\CollabCorp\LaravelFeatureToggle\FeatureToggleServiceProvider"
```

You may then configure your config to your liking, it is possible to use callbacks or callables strings as values.

<a name="binding_callbacks" />

### Binding callbacks
It can become quite cumbersome with a lot of callbacks in your features config,
binding a callback to an alias makes this a breeze.

```php
Feature::bind('evaluation', function ($user, ...$dependencies) {
	// logic.

	return (boolean) $result;
});
```

**And you are ready to go.**

<a name="usage" />

## Usage
This package adds the @features blade directive, it outputs JavaScript that adds the feature function to the window.

it is also possible to check a feature inside a blade file, like so
```php
@feature('name')
	// Feature is enabled
@else
	// Feature is disabled
@endfeature
```

In your application code you can simply call 
```php
 use Feature;

 Feature::isEnabled('name');
 Feature::isDisabled('name');
```

<a name="javascript" />

### Evaluating features in JavaScript

To evaluate a feature toggle in your frontend, simply add ``` @features ``` to your blade file. Likely in your header.

This will add a ``` bool feature(value) ``` helper to your window.

<a name="testing" />
## Testing
```php
composer test
```

<a name="issues" />

## Issues 

If you discover any vulnerabilities, please e-mail them to me at jonas.kerwin.hansen@gmail.com.

For issues, open a issue on Github.

<a name="license" />

## License

laravel-feature-toggle is free software distributed under the terms of the MIT license.
