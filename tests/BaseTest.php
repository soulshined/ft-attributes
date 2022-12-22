<?php

use FT\Reflection\ClassCache;
use PHPUnit\Framework\TestCase;

$include_models = [
    'BasicEnum',
    'Foo',
    'FooWithAliases',
    'FooWithAliasesSubclass',
    'FooWithClassAndLocalIgnored',
    'FooWithClassAndLocalIgnoredSubclass',
    'FooWithClassIgnored',
    'FooWithDefaults',
    'FooWithDefaultsSubclass',
    'FooWithEmbeddables',
    'FooWithEmbeddablesSubclass',
    'FooWithEnums',
    'FooWithJsonArray',
    'FooWithJsonVia',
    'FooWithLocalIgnored',
    'IntEnum',
    'MyClass',
    'OnlyTemporal',
    'OnlyTemporalDeserialized',
    'StringEnum'
];

foreach ($include_models as $model) {
    require_once(__DIR__ . "/./model/$model.php");
}

abstract class BaseTest extends TestCase {

    public static function setUpBeforeClass(): void
    {
        $GLOBALS['now'] = new DateTime();
        $GLOBALS['inow'] = new DateTimeImmutable();
    }

    protected function assertObjectsEqual(object $expected, object $actual, string ...$ignored_properties) {
        $this->assertEquals($expected::class, $actual::class);

        $expcl = ClassCache::get($expected::class);
        $target = ClassCache::get($actual::class);

        $this->assertSameSize($expcl->properties, $target->properties);
        $this->assertEmpty(get_object_vars($expected), var_export(get_object_vars($expected), true));
        $this->assertEmpty(get_object_vars($actual), "Actual has unexpected public vars applied");

        foreach ($expcl->properties as $pd) {
            if (in_array($pd->name, $ignored_properties)) continue;

            $target_prop = $target->get_property($pd->name);

            $this->assertNotNull($target_prop, "Actual does not have property: $pd->name");

            $exp_val = $pd->delegate->getValue($expected);
            $target_val = $target_prop->delegate->getValue($actual);
            $this->assertEquals($exp_val, $target_val, "prop name: " . $target_prop->name);
        }
    }

}

?>