<?php

namespace CollabCorp\LaravelFeatureToggle\Tests;

use PHPUnit\Framework\Assert as PHPUnit;
use CollabCorp\LaravelFeatureToggle\Feature;

class FeatureToggleTest extends TestCase
{
    /** @test */
    public function it_checks_if_a_feature_is_enabled()
    {
        config([
            'features' => [
                'some' => ['feature' => true],
                'another' => ['feature' => false]
            ]
        ]);


        $this->assertTrue(Feature::isEnabled('some.feature'));
        $this->assertFalse(Feature::isEnabled('another.feature'));
    }

    /** @test */
    public function it_executes_a_callback_if_present()
    {
        config([
            'features' => [
                'some' => [
                    'feature' => function ($user) {
                        return $user->isDev();
                    }
                ]
            ]
        ]);

        $this->assertTrue(Feature::isEnabled('some.feature', new class {
            public function isDev()
            {
                return true;
            }
        }));
        $this->assertFalse(Feature::isEnabled('some.feature', new class {
            public function isDev()
            {
                return false;
            }
        }));
    }

    /** @test */
    public function it_reduce_callbacks_to_primitives_on_compile()
    {
        config([
            'features' => [
                'some' => [
                    'feature' => function ($user) {
                        return $user->isDev();
                    }
                ]
            ]
        ]);

        tap(Feature::compile(new class {
            public function isDev()
            {
                return true;
            }
        }), function (array $features) {
            $this->assertEquals(['some.feature' => true], $features);
        });

        tap(Feature::compile(new class {
            public function isDev()
            {
                return false;
            }
        }), function (array $features) {
            $this->assertEquals(['some.feature' => false], $features);
        });
    }

    /** @test */
    public function a_feature_is_disabled_by_default()
    {
        $this->assertFalse(Feature::isEnabled('feature.that.does.not.exist'));
    }

    /** @test */
    public function can_bind_a_named_evaluation_callback()
    {
        Feature::bind('test', function ($authenticated)  {
            return true;
        });

    	$this->app['config']->set('features.test', 'test');

        $this->assertTrue(Feature::isEnabled('test'));
    }

    /** @test */
    public function it_generates_a_javaScript_functions() 
    {
        $this->app['config']->set('features.test', function () {
            return false;
        });

        $expected = <<<EOT
<script type="text/javascript">
    var features = {"test":false}
            
    var feature = function (value) {
        return String(
            features[value],
        ).toLowerCase() === 'true'
    }

    window.feature = feature;
</script>
EOT;

        $this->assertEquals($expected, Feature::javaScriptFunction());
    } 
}
