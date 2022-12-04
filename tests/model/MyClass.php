<?php
namespace Tests\Model;

final class MyClass {
    public function __construct(
        public string | null $fname = null,
        public string | null $lname = null,
        public int | null $age = null
    ) { }
}
?>