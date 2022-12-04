A catalog of php attributes for flexible and maintainable modeling.

Attributes for logging, json, property validation and more.

Quick example of property validation:

```php
final class MyClass {
    use PropertyValidator;

    #[Email]
    private string $email;

    #[Min(value: 12)]
    private int $age;

    #[Size(min: 1, max: 3)]
    private array $phone_numbers;
}
```

Quick example of json:

```php
class MyClass {
    private int $id = 1;
    #[JsonIgnore]
    private string $ssn = "xxx-xx-xxxx";
}

Json::encode(new MyClass); // { "id" : 1 }
```

For complete documentation and attribute usages see the [documentation wiki](https://github.com/soulshined/ft-attributes/wiki/Documentation)