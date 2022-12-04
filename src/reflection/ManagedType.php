<?php

namespace FT\Attributes\Reflection;

use ReflectionClass;

final class ManagedType extends AnnotatedMember {
    public readonly string $name;
    public readonly string $shortname;
    /**
     * @var PropertyDescriptor[]
     */
    public readonly array $properties;

    public function __construct(public readonly ReflectionClass $delegate)
    {
        parent::__construct($delegate);
        $this->name = $delegate->name;
        $this->shortname = $delegate->getShortName();
        $this->properties = $this->get_properties_for_type($delegate);
    }

    public function get_property(string $name)  : PropertyDescriptor | null {
        foreach ($this->properties as $pd) {
            if ($pd->name === $name) return $pd;
        }

        return null;
    }

    /**
     * @return PropertyDescriptor[]
     */
    private function get_properties_for_type(ReflectionClass | bool $class) : array {
        if (is_bool($class)) return [];

        $this_props = array_map(fn ($i) => new PropertyDescriptor($i), $class->getProperties());
        $super_props = $this->get_properties_for_type($class->getParentClass());

        $this_prop_names = array_map(fn ($i) => $i->name, $this_props);
        array_push($this_props, ...array_filter($super_props, fn ($i) => !in_array($i->name, $this_prop_names)));
        return $this_props;
    }

    public function get_class_name(): string
    {
        return $this->delegate->name;
    }
}

?>