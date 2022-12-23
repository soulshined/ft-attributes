<?php

require(__DIR__ . '/../vendor/autoload.php');

use FT\Attributes\Json\Json;
use Tests\Model\BasicEnum;
use Tests\Model\Foo;
use Tests\Model\FooWithDefaults;
use Tests\Model\MyClass;
use Tests\Model\OnlyTemporal;
use Tests\Model\FooWithAliases;
use Tests\Model\FooWithAliasesSubclass;
use Tests\Model\FooWithClassAndLocalIgnored;
use Tests\Model\FooWithClassIgnored;
use Tests\Model\FooWithEmbeddables;
use Tests\Model\FooWithEnums;
use Tests\Model\IntEnum;
use Tests\Model\StringEnum;
use Tests\Model\FooWithLocalIgnored;
use Tests\Model\FooWithClassAndLocalIgnoredSubclass;
use Tests\Model\FooWithDefaultsSubclass;
use Tests\Model\FooWithEmbeddablesSubclass;
use Tests\Model\FooWithJsonVia;

final class JsonEncodeTest extends BaseTest {

    /**
     * @test
     * @dataProvider should_encode_args
     */
    public function should_encode($actual, $expected) {
        $this->assertEquals($expected, Json::encode($actual));
    }

    public function build_myClass($fname, $lname, $age) {
        return [
            'fname' => $fname,
            'lname' => $lname,
            'age' => $age
        ];
    }

    public function build_foo($id = null, $bar = null, $createdAt = null, $myClass = null, $float = null, $object = null, $array = null, $mixed = null, $dateTimeImmutable = null) {
        return [
            'id' => $id ?? null,
            'bar' => $bar ?? null,
            'createdAt' => $createdAt ?? null,
            'myClass' => $myClass ?? null,
            'float' => $float ?? null,
            'object' => $object ?? null,
            'array' => $array ?? null,
            'mixed' => $mixed ?? null,
            'dateTimeImmutable' => $dateTimeImmutable ?? null
        ];
    }


    public function should_encode_args() {
        $now = new DateTime();
        $GLOBALS['now'] = $now;
        $inow = new DateTimeImmutable();
        $GLOBALS['inow'] = $inow;
        return array_map(function ($i) {
            $i[1] = json_encode($i[1]);
            return $i;
        }, [
            //ensure encode provides nulls
            [new Foo, $this->build_foo()],

            //  test all values of Foo
            [
                Foo::builder()->id(1)
                    ->bar('bar')
                    ->createdAt($now)
                    ->myClass(new MyClass)
                    ->float(1.)
                    ->object(new class { })
                    ->array([1, 'two', '\u1000'])
                    ->mixed(null)
                    ->dateTimeImmutable($inow)
                    ->build(),
                $this->build_foo(1, 'bar', $now->format(DATE_RFC3339_NTZ), $this->build_myClass(null, null, null), 1., new class { }, [1, 'two', '\u1000'], null, $inow->format(DATE_RFC3339_NTZ))
            ],

            //test JsonDefaults get applied
            [new FooWithDefaults, $this->build_foo(-1, 'default bars', $now->format(DATE_RFC3339_NTZ), ['fname' => null, 'lname' => null, 'age' => null], -1.1, ['name' => 'john'], [1,'two',3], null, $inow->format(DATE_RFC3339_NTZ))],
            [new FooWithDefaultsSubclass, $this->build_foo(-1, 'default bars', $now->format(DATE_RFC3339_NTZ), ['fname' => null, 'lname' => null, 'age' => null], -1.1, ['name' => 'john'], [1,'two',3], null, $inow->format(DATE_RFC3339_NTZ))],

            //test aliases
            [new FooWithAliases, ['owner' => ['fname' => null, 'lname' => null, 'age' => null], 'amount' => null, 'id' => null, 'bar' => null, 'createdAt' => null, 'object' => null, 'array' => null, 'mixed' => null, 'dateTimeImmutable' => null]],
            [new FooWithAliasesSubclass, ['owner' => ['fname' => null, 'lname' => null, 'age' => null], 'amount' => null, 'id' => null, 'bar' => null, 'createdAt' => null, 'object' => null, 'array' => null, 'mixed' => null, 'dateTimeImmutable' => null]],

            //test unwrapped
            [new FooWithEmbeddables, ['fname' => null, 'lname' => null, 'age' => null, 'amount' => null, 'id' => null, 'bar' => null, 'createdAt' => null, 'object' => null, 'array' => null, 'mixed' => null, 'dateTimeImmutable' => null]],
            [new FooWithEmbeddablesSubclass, ['fname' => null, 'lname' => null, 'age' => null, 'amount' => null, 'id' => null, 'bar' => null, 'createdAt' => null, 'object' => null, 'array' => null, 'mixed' => null, 'dateTimeImmutable' => null]],

            //test enums
            [new FooWithEnums, ['basic_null' => null, 'int_null' => null, 'string_null' => null,'basic' => BasicEnum::A->name, 'int' => ['name' => IntEnum::B->name, 'value' => IntEnum::B->value], 'string' => ['name' => StringEnum::C->name, 'value' => StringEnum::C->value]]],

            //ignores output of properties via #[JsonIgnore]
            [new FooWithLocalIgnored, [
                'phone_number' => "xxx-xxx-xxxx",
                'myClass' => null,
                'float' => null,
                'id' => null,
                'bar' => null,
                'createdAt' => null,
                'object' => null,
                'array' => null,
                'mixed' => null,
                'dateTimeImmutable' => null
            ]],

            //ignores output of properties via #[Json(ignored_properties)]
            [new FooWithClassIgnored, [
                'phone_number' => "xxx-xxx-xxxx",
                'myClass' => null,
                'float' => null,
                'id' => null,
                'bar' => null,
                'createdAt' => null,
                'object' => null,
                'array' => null,
                'mixed' => null,
                'dateTimeImmutable' => null
            ]],

            //ignores output of properties defined
            [new FooWithClassAndLocalIgnored, [
                'phone_number' => "xxx-xxx-xxxx",
                'myClass' => null,
                'float' => null,
                'id' => null,
                'bar' => null,
                'createdAt' => null,
                'object' => null,
                'array' => null,
                'mixed' => null,
                'dateTimeImmutable' => null
            ]],
            [new FooWithClassAndLocalIgnoredSubclass, [
                'myClass' => null,
                'float' => null,
                'phone_number' => "xxx-xxx-xxxx",
                'id' => null,
                'bar' => null,
                'createdAt' => null,
                'object' => null,
                'array' => null,
                'mixed' => null,
                'dateTimeImmutable' => null
            ]],

            //test only dates
            [new OnlyTemporal, [
                'dateTimeNoDefault' => null,
                'dateTimeInteger' => $now->getTimestamp(),
                'dateTimeFormat' => $now->format(DATE_RFC3339),
                'dateTimeFormatInvalid' => $now->format("invalid"),
                'dateTimeImmutableNoDefault' => null,
                'dateTimeImmutableInteger' => $inow->getTimestamp(),
                'dateTimeImmutableFormat' => $inow->format(DATE_RFC3339),
                'dateTimeImmutableFormatInvalid' => $inow->format('invalid')
            ]],

            //test JsonVia
            [new FooWithJsonVia, array_merge(['intEnumType' => [ 'name' => 'D', 'value' => 4]],
                ['fname' => 'fnamez',
                'lname' => 'lnamez',
                'age' => null,
                'codes' => [99,98,97,96],
                'ownerAssociations' => [
                    [ 'fname' => 'assc1', 'lname' => 'doe', 'age' => null ],
                    [ 'fname' => 'assc2', 'lname' => 'doe', 'age' => null ],
                    [ 'fname' => 'assc3', 'lname' => 'doe', 'age' => null ]
                ],
                'foos' => ['bar', 'bazz', 'buzz', 'bizz', 'bozz'],
                'default' => [1,2,3,4],
                'temporal_num' => $now->getTimestamp(),
                'temporal_str' => $now->format(DATE_RFC3339_NTZ),
                'misc' => 'misc'
            ])],

            //test array of classes
            [[
                new MyClass('name1f', 'name1l', 1),
                new MyClass('name2f', 'name2l', 2),
                new MyClass('name3f', 'name3l', 3)
            ], [
                [ 'fname' => 'name1f', 'lname' => 'name1l', 'age' => 1 ],
                [ 'fname' => 'name2f', 'lname' => 'name2l', 'age' => 2 ],
                [ 'fname' => 'name3f', 'lname' => 'name3l', 'age' => 3 ]
            ]]
        ]);
    }

}


?>