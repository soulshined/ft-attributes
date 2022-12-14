<?php

namespace FT\Attributes\Reflection;

use ReflectionNamedType;
use ReflectionProperty;

class PropertyDescriptor extends AnnotatedMember {

    public readonly string $name;
    public readonly \ReflectionNamedType | \ReflectionUnionType | \ReflectionIntersectionType | null $type;

    public function __construct(public readonly ReflectionProperty $property)
    {
        parent::__construct($property);
        $this->name = $property->name;
        $this->type = $property->getType();
        $this->property->setAccessible(true);
    }

    public function get_qualified_name() {
        return $this->property->getDeclaringClass()->getShortName() . "." . $this->name;
    }

    public function get_class_name(): ?string
    {
        if ($this->type === null) return null;

        if ($this->type instanceof ReflectionNamedType) {
            if ($this->type->isBuiltin()) return null;
            return $this->type->getName();
        }
        else
        foreach ($this->type->getTypes() as $type) {
            if ($type->isBuiltin()) continue;
            return $type->getName();
        }

        return null;
    }

    public function has_type($name) {
        if ($this->type instanceof ReflectionNamedType) {
            return strtolower($this->type->getName()) === strtolower($name);
        }
        else
        foreach ($this->type->getTypes() as $type) {
            if (strtolower($type->getName()) === strtolower($name)) return true;
        }

        return false;
    }

    public function is_enum() {
        if ($this->type instanceof ReflectionNamedType)  {
            if ($this->type->isBuiltin()) return false;

            return enum_exists($this->type->getName());
        }
        else
        foreach ($this->type->getTypes() as $type) {
            if ($type->isBuiltin()) continue;

            if (enum_exists($type->getName())) return true;
        }

        return false;
    }

}

?>