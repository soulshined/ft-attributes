<?php

namespace Tests\Model;

use DateTimeImmutable;
use FT\Attributes\Lombok\LombokProperties;

class Foo {
    use LombokProperties;

    private int $id;
    private string $bar;
    private \DateTime $createdAt;
    protected MyClass $myClass;
    protected float $float;
    private object $object;
    private array $array;
    private mixed $mixed;
    private DateTimeImmutable $dateTimeImmutable;

    public static function builder() {

        return new class {
            private Foo $foo;

            public function __construct()
            {
                $this->foo = new Foo();
            }


            public function id(int $id) {
                $this->foo->id = $id;
                return $this;
            }

            public function bar($val) {
                $this->foo->bar = $val;
                return $this;
            }

            public function createdAt($val) {
                $this->foo->createdAt = $val;
                return $this;
            }

            public function myClass($val) {
                $this->foo->myClass = $val;
                return $this;
            }

            public function float($val) {
                $this->foo->float = $val;
                return $this;
            }

            public function object($val) {
                $this->foo->object = $val;
                return $this;
            }

            public function array($val) {
                $this->foo->array = $val;
                return $this;
            }

            public function mixed($val) {
                $this->foo->mixed = $val;
                return $this;
            }

            public function dateTimeImmutable($val) {
                $this->foo->dateTimeImmutable = $val;
                return $this;
            }


            public function build(): Foo {
                return $this->foo;
            }

        };

    }
}

?>