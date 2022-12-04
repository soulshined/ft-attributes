<?php

namespace FT\Attributes\Reflection;

use ReflectionClass;

abstract class AnnotatedMember
{
    /**
     * @var Attribute[]
     */
    public readonly array $attributes;

    protected function __construct($delegate)
    {
        if ($delegate instanceof ReflectionClass)
            $this->attributes = $this->get_and_merge_hierarchy_attributes($delegate);

        else $this->attributes = array_map(fn ($i) => new Attribute($i), $delegate->getAttributes());
    }

    public function has_attribute(string $class): bool
    {
        return $this->get_attribute($class) !== null;
    }

    /**
     * @return Attribute | null
     */
    public function get_attribute(string $class): Attribute | null
    {
        foreach ($this->attributes as $attr) {
            if ($attr->name === strtolower($class))
                return $attr;
        }
        return null;
    }

    /**
     * @return Attribute[]
     */
    public function get_attributes(string $class): array
    {
        return array_map(fn ($i) => strtolower($class) === $i->name, $this->attributes);
    }

    public abstract function get_class_name() : ?string;

    /**
     * @return Attribute[]
     */
    private function get_and_merge_hierarchy_attributes(ReflectionClass | bool $class) : array {
        if ($class === false) return [];

        $this_attrs = array_map(fn ($i) => new Attribute($i), $class->getAttributes());
        $super_attrs = $this->get_and_merge_hierarchy_attributes($class->getParentClass());

        $this_attr_names = array_map(fn ($i) => $i->name, $this_attrs);
        foreach ($super_attrs as $attr) {
            if (!$attr->is_inheritable) continue;

            if (in_array($attr->name, $this_attr_names) && !$attr->is_repeated) {
                $this_match = array_filter($this_attrs, fn ($i) => $i->name === $attr->name)[0];
                foreach ($attr->getArguments() as $key => $value) {
                    if (is_array($value)) {
                        array_unshift($this_match->getArguments()->{$key}, ...$value);
                        $this_match->getArguments()->{$key} = array_unique($this_match->getArguments()->{$key});
                    }
                }
            }
            else $this_attrs[] = $attr;
        }
        return $this_attrs;
    }

    // public function is_builtin() : bool {
    //     if ($this->type === null) return true;

    //     if ($this->type instanceof ReflectionNamedType) {
    //         return $this->type->isBuiltin();
    //     }
    //     else {
    //         foreach ($this->type->getTypes() as $type) {
    //             if ($type->isBuiltin()) return true;
    //         }
    //     }

    //     return false;
    // }

}

?>