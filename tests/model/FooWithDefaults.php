<?php
namespace Tests\Model;

use DateTime;
use DateTimeImmutable;
use FT\Attributes\Json\JsonDefault;

class FooWithDefaults {
    #[JsonDefault(-1)]
    private int $id;

    #[JsonDefault('default bars')]
    private string $bar;

    #[JsonDefault(new DateTime())]
    private \DateTime $createdAt;

    #[JsonDefault(new MyClass)]
    private MyClass $myClass;

    #[JsonDefault(-1.1)]
    private float $float;

    #[JsonDefault([ 'name' => 'john' ])]
    private object $object;

    #[JsonDefault([1,'two',3])]
    private array $array;

    #[JsonDefault()]
    private mixed $mixed;

    #[JsonDefault(new DateTimeImmutable())]
    private \DateTimeImmutable $dateTimeImmutable;
}

?>