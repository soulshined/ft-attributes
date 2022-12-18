<?php

use FT\Attributes\Lombok\LombokProperties;
use FT\Attributes\Validation\CustomPropertyValidators;
use FT\Attributes\Validation\Email;
use FT\Attributes\Validation\IllegalArgumentException;
use FT\Attributes\Validation\Max;
use FT\Attributes\Validation\Min;
use FT\Attributes\Validation\Negative;
use FT\Attributes\Validation\NotEmpty;
use FT\Attributes\Validation\Pattern;
use FT\Attributes\Validation\Positive;
use FT\Attributes\Validation\PropertyValidator;
use FT\Attributes\Validation\Size;
use PHPUnit\Framework\TestCase;
use Tests\Model\NotNoAttribute;

require(__DIR__ . '/../vendor/autoload.php');

require_once(__DIR__ . '/./model/NotNoAttribute.php');

final class ValidationTest extends TestCase {

    private function expectInvalid(string $message) {
        $this->expectException(IllegalArgumentException::class);
        $this->expectExceptionMessage($message);
    }

    private function invalid_emails() {
        return [
            ['notanemail.email'],
            [''],
            ['    '],
            ['notanemail@email'],
            ['@email.com'],
            ['  @email.com'],
            ['notanemail@email.'],
            [null]
        ];
    }

    /**
     * @test
     * @dataProvider invalid_emails
     */
    public function invalid_email_test($email) {
        $this->expectInvalid(".email is not a valid email");

        $c = new class {
            use PropertyValidator;

            #[Email]
            private string $email;
        };

        $c->email = $email;
    }

    /**
    * @test
    */
    public function valid_email_test() {
        $c = new class {
            use PropertyValidator;

            #[Email]
            private string $email;

            public function getEmail() {
                return $this->email;
            }
        };

        $c->email = "abc123@example.com";
        $this->assertEquals('abc123@example.com',$c->getEmail());
    }

    /**
    * @test
    */
    public function invalid_max_array_test() {
        $this->expectInvalid(".items is greater than allowed 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private array $items = [];
        };

        $c->items = [1,2,3,4,5,6,7,8,9,10,11];
    }

    /**
    * @test
    */
    public function valid_max_array_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private array $items = [];
        };

        $c->items = [1,2,3,4,5,6,7,8,9,10];
        $this->assertNotEmpty($c->items);
    }

    /**
    * @test
    */
    public function invalid_max_string_test() {
        $this->expectInvalid(".string is greater than allowed 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private string $string;
        };

        $c->string = "12345678910";
    }

    /**
    * @test
    */
    public function valid_max_string_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private string $string;
        };

        $c->string = "abcdefghij";
        $this->assertGreaterThanOrEqual(10, strlen($c->string));
    }

    /**
    * @test
    */
    public function invalid_max_number_test() {
        $this->expectInvalid(".number is greater than allowed 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private float $number;
        };

        $c->number = 10.0000000001;
    }

    /**
    * @test
    */
    public function valid_max_number_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Max(value: 10)]
            private float $number;
        };

        $c->number = 9.9999999999;
        $this->assertLessThan(10, $c->number);
    }

    /**
    * @test
    */
    public function invalid_min_array_test() {
        $this->expectInvalid(".items is less than allowed minimum 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private array $items = [];
        };

        $c->items = [1,2,3,4,5,6,7,8,9];
    }

    /**
    * @test
    */
    public function valid_min_array_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private array $items = [];
        };

        $c->items = [1,2,3,4,5,6,7,8,9,10];
        $this->assertGreaterThanOrEqual(10, count($c->items));
    }

    /**
    * @test
    */
    public function invalid_min_string_test() {
        $this->expectInvalid(".string is less than allowed minimum 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private string $string;
        };

        $c->string = "abcdefghi";
    }

    /**
    * @test
    */
    public function valid_min_string_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private string $string;
        };

        $c->string = "1234567891";
        $this->assertGreaterThanOrEqual(10, strlen($c->string));
    }

    /**
    * @test
    */
    public function invalid_min_number_test() {
        $this->expectInvalid(".number is less than allowed minimum 10");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private float $number;
        };

        $c->number = 9.99999999;
    }

    /**
    * @test
    */
    public function valid_min_number_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Min(value: 10)]
            private float $number;
        };

        $c->number = 10;
        $this->assertGreaterThanOrEqual(10, $c->number);
    }

    /**
     * @test
     */
    public function invalid_negative_test() {
        $this->expectInvalid(".number is not a negative number");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Negative]
            private int $number;
        };

        $c->number = 0.000000000;
    }

    /**
     * @test
     */
    public function valid_negative_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Negative]
            private float $number;
        };

        $c->number = -0.00000001;
        $this->assertLessThan(0, $c->number);
    }

    /**
     * @test
     */
    public function invalid_positive_test() {
        $this->expectInvalid(".number is not a positive number");
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Positive]
            private int $number;
        };

        $c->number = -0.0000000001;
    }

    /**
     * @test
     */
    public function valid_positive_test() {
        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Positive]
            private float $number;
        };

        $c->number = 0.00000001;
        $this->assertGreaterThanOrEqual(0, $c->number);
    }

    /**
    * @test
    */
    public function invalid_notempty_string_test() {
        $this->expectInvalid(".string is not expected to be empty");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[NotEmpty]
            private string $string;
        };

        $c->string = "             ";
    }

    /**
    * @test
    */
    public function invalid_notempty_array_test() {
        $this->expectInvalid(".items is not expected to be empty");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[NotEmpty]
            private array $items;
        };

        $c->items = [];
    }

    /**
    * @test
    */
    public function invalid_pattern_test() {
        $this->expectInvalid(".string does not match pattern [a-z]+");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Pattern(value: "[a-z]+")]
            private string $string;
        };

        $c->string = "   ";
    }

    /**
    * @test
    */
    public function valid_pattern_test() {
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Pattern(value: "[a-z]+")]
            private string $string;
        };

        $c->string = "  aye ";
        $this->assertEquals("  aye ", $c->string);
    }

    private function invalid_sizes_array() {
        return [
            [[1], "less than expected (2)"],
            [[1,2,3,4,5,6], "greater than expected (5)"]
        ];
    }

    /**
    * @test
    * @dataProvider invalid_sizes_array
    */
    public function invalid_size_array_test($list, $error_msg) {
        $this->expectInvalid(".items size is $error_msg");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 2, max: 5)]
            private array $items;
        };

        $c->items = $list;
    }

    /**
    * @test
    */
    public function valid_size_array_test() {
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 2, max: 5)]
            private array $items;
        };

        $c->items = [1,2,3];
        $this->assertSameSize([1,2,3], $c->items);
    }

    private function invalid_sizes_string() {
        return [
            ['jr', "less than expected (3)"],
            ['boomer', "greater than expected (5)"]
        ];
    }

    /**
    * @test
    * @dataProvider invalid_sizes_string
    */
    public function invalid_size_string_test($val, $error_msg) {
        $this->expectInvalid(".string size is $error_msg");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 3, max: 5)]
            private string $string;
        };

        $c->string = $val;
    }

    /**
    * @test
    */
    public function valid_size_string_test() {
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 3, max: 5)]
            private string $string;
        };

        $c->string = "_abc_";
        $this->assertEquals(5, strlen($c->string));
    }

    private function invalid_sizes_num() {
        return [
            [2, "less than expected (3)"],
            [6, "greater than expected (5.5)"]
        ];
    }

    /**
    * @test
    * @dataProvider invalid_sizes_num
    */
    public function invalid_size_num_test($val, $error_msg) {
        $this->expectInvalid(".number size is $error_msg");
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 3, max: 5.5)]
            private float $number;
        };

        $c->number = $val;
    }

    /**
    * @test
    */
    public function valid_size_num_test() {
        $c = new class
        {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[Size(min: 3, max: 5.5)]
            private float $number;
        };

        $c->number = 5.45;
        $this->assertEquals(5.45, $c->number);
    }

    /**
     * @test
     */
    public function custom_validator_test() {
        $this->expectInvalid("Not allowed to be no");

        CustomPropertyValidators::add(new NotNoAttribute);

        $c = new class {
            use PropertyValidator, LombokProperties {
                PropertyValidator::__construct as __construct_pv;
                LombokProperties::__construct as __construct_lombok;
            }

            public function __construct()
            {
                $this->__construct_pv();
                $this->__construct_lombok();
            }

            #[NotNoAttribute]
            private string $value;
        };

        $c->value = 'no';
    }
}