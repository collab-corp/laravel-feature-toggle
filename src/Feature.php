<?php

namespace CollabCorp\LaravelFeatureToggle;

use Illuminate\Support\Facades\Blade;

class Feature
{
    /**
     * Array of named evaluation callbacks.
     *
     * @var array
     */
    protected static $evaluations = [];

    /**
     * Bind a string to a callback for evaluation.
     *
     * This allows for assigning a string to a callback during
     * eg. features.thing => 'onlyDevelopers',
     *
     * @param  string   $name
     * @param  \Closure $callback
     * @return void
     */
    public static function bind($name, $callback)
    {
        static::$evaluations[$name] = $callback;
    }

    /**
     * Register the @feature('thing') blade if directive.
     *
     * @return void
     */
    public static function registerBladeIfDirective()
    {
        Blade::if('feature', function (string $feature, $user = null) {
            return Feature::isEnabled($feature, $user);
        });
    }

    /**
     * Register the @features directive that outputs the JavaScript function.
     *   
     * @return void
     */
    public static function registerJavaScriptBladeDirective()
    {
        Blade::directive('features', function () {
            return "<?php echo \CollabCorp\LaravelFeatureToggle\Feature::javaScriptFunction() ?>";
        });
    }

    /**
     * Output the JavaScript function.
     * 
     * @return string
     */
    public static function javaScriptFunction()
    {
        $jsonPayload = json_encode(static::compile());
        
        return <<<EOT
<script type="text/javascript">
    var features = $jsonPayload
            
    var feature = function (value) {
        return String(
            features[value],
        ).toLowerCase() === 'true'
    }

    window.feature = feature;
</script>
EOT;
    }

    /**
     * Determine if given feature is enabled.
     *
     * @param  string                                   $feature
     * @param  \Illuminate\Auth\Authenticatable | null  $user
     * @return boolean
     */
    public static function isEnabled($feature, $user = null)
    {
        return static::evaluate(
            config("features.{$feature}", false),
            $user
        );
    }

    /**
     * Determine if given feature is disabled.
     *     
     * @param  string                                   $feature
     * @param  \Illuminate\Auth\Authenticatable | null  $user
     * @return boolean         
     */
    public static function isDisabled(string $feature, $user = null)
    {
        return !static::isEnabled($feature, $user);
    }

    /**
     * Compile the features down to primitive values.
     * 
     * @param  \Illuminate\Auth\Authenticatable | null  $user 
     * @return array
     */
    public static function compile($user = null)
    {
        return array_map(function ($value) use ($user) {
            return static::evaluate($value, $user);
        }, static::all());
    }

    /**
     * Get all the features
     * 
     * @return array
     */
    public static function all()
    {
        return array_dot(config('features') ?? []);
    }

    /**
     * Evaluate the feature.
     *
     * @param callable|boolean $value
     * @param \App\User        $user
     *
     * @return boolean
     */
    private static function evaluate($value, $user)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value) && array_key_exists($value, static::$evaluations)) {
            $value = static::$evaluations[$value];
        }
        
        return (bool)app()->call($value, ['user' => $user ?? auth()->user()]);
    }
}
