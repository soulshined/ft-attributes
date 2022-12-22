<?php

use FT\Attributes\Json\Json;
use Tests\Model\BasicEnum;
use Tests\Model\Foo;
use Tests\Model\FooWithAliases;
use Tests\Model\FooWithAliasesSubclass;
use Tests\Model\FooWithClassAndLocalIgnoredSubclass;
use Tests\Model\FooWithEmbeddables;
use Tests\Model\FooWithEmbeddablesSubclass;
use Tests\Model\FooWithEnums;
use Tests\Model\FooWithJsonArray;
use Tests\Model\FooWithJsonVia;
use Tests\Model\FooWithLocalIgnored;
use Tests\Model\IntEnum;
use Tests\Model\MyClass;
use Tests\Model\OnlyTemporalDeserialized;
use Tests\Model\StringEnum;

require(__DIR__ . '/../vendor/autoload.php');

final class JsonDecodeTest extends BaseTest {

    /**
    * @test
    * @dataProvider should_decode_args
    */
    public function should_decode_test($object, string ...$ignore_properties) {
        array_push($ignore_properties, 'subscribers');
        $encoded = Json::encode($object);
        $decoded = Json::decode($encoded, $object::class);
        $this->assertObjectsEqual($object, $decoded, ...$ignore_properties);
    }

    /**
    * @test
    */
    public function should_decode_json_array_test() {
        $foo = new FooWithJsonArray;
        array_push(
            $foo->myClasses,

            new MyClass("First Name", "Last Name", 10),
            new MyClass("First Name 2", "Last Name 2", 11),
            new MyClass("First Name 3", "Last Name 3", 11)
        );

        $encoded = Json::encode($foo);
        $decoded = Json::decode($encoded, FooWithJsonArray::class);

        $this->assertEquals($decoded::class, $foo::class);
        $this->assertNotEmpty($decoded->myClasses);

        foreach ($decoded->myClasses as $class) {
            $this->assertStringStartsWith("First Name", $class->fname);
            $this->assertStringStartsWith("Last Name", $class->lname);
            $this->assertGreaterThan(0, $class->age);
        }
    }

    /**
    * @test
    */
    public function should_decode_for_json_via_test() {
        $GLOBALS['now'] = new DateTime();

        global $now;

        $foo = FooWithJsonVia::new();

        $encoded = Json::encode($foo);

        $expected = new FooWithJsonVia;

        $expected->alias = "misc";
        $expected->codes = [99,98,97,96];
        $expected->temporal_num = $now->getTimestamp();
        $expected->temporal_str = $now->format(DATE_RFC3339_NTZ);
        $expected->default = [1,2,3,4];
        $expected->foos = ['bar', 'bazz', 'buzz', 'bizz', 'bozz'];
        $expected->owner = new MyClass('fnamez', 'lnamez');
        $expected->intEnumTypeCode = IntEnum::F->value;
        $expected->ownerAssociations = [
            new MyClass("assc1", "doe"),
            new MyClass("assc2", "doe"),
            new MyClass("assc3", "doe")
        ];

        $this->assertObjectsEqual($expected, Json::decode($encoded, FooWithJsonVia::class), ...['subscribers']);
    }

    private function build_foo($base_foo) {
        global $now, $inow;
        $myClass = new MyClass("First Name", "Last Name", 10);

        $base_foo->id = 1;
        $base_foo->bar = 'bar';
        $base_foo->createdAt = DateTime::createFromFormat(DATE_RFC3339_NTZ, $now->format(DATE_RFC3339_NTZ));
        $base_foo->myClass = $myClass;
        $base_foo->float = 1.;
        $base_foo->object = new stdClass;
        $base_foo->array = [1, 2, 3];
        $base_foo->mixed = null;
        $base_foo->dateTimeImmutable = DateTimeImmutable::createFromFormat(DATE_RFC3339_NTZ, $inow->format(DATE_RFC3339_NTZ));

        return $base_foo;
    }

    public function should_decode_args() {
        $GLOBALS['now'] = new DateTime();
        $GLOBALS['inow'] = new DateTimeImmutable();

        $foo_with_enums = new FooWithEnums;
        $foo_with_enums->basic_null = BasicEnum::F;
        $foo_with_enums->int_null =IntEnum::F;
        $foo_with_enums->string_null = StringEnum::F;

        return [
            [$this->build_foo(new Foo)],
            [$this->build_foo(new FooWithAliases)],
            [$this->build_foo(new FooWithAliasesSubclass)],

            //test simple case of unwrapped
            [$this->build_foo(new FooWithEmbeddables)],
            [$this->build_foo(new FooWithEmbeddablesSubclass)],

            //enums
            [$foo_with_enums],

            //ignored properties
            [$this->build_foo(new FooWithLocalIgnored), 'encrypted_data'],
            [$this->build_foo(new FooWithClassAndLocalIgnoredSubclass), 'encrypted_data'],

            [new OnlyTemporalDeserialized]
        ];
    }
}