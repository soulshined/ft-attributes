<?php

namespace FT\Attributes\Reflection;

use ReflectionParameter;

class ParameterDescriptor extends AnnotatedMember
{

    public string $name;
    public string $displayName;
    public int $position;
    public bool $allowsNull;
    public mixed $defaultValue;
    public bool $hasDefault;

    public function __construct(ReflectionParameter $param)
    {
        parent::__construct($param);
        $this->name = strtolower($param->name);
        $this->displayName = $param->name;
        $this->allowsNull = $param->allowsNull();
        $this->hasDefault = $param->isDefaultValueAvailable();
        if ($this->hasDefault)
            $this->defaultValue = $param->getDefaultValue();
        $this->position = $param->getPosition();
    }

    public function get_class_name(): ?string
    {
        if ($this->type === null) return null;

        if ($this->type instanceof \ReflectionNamedType) {
            if ($this->type->isBuiltin()) return null;
            return $this->type->getName();
        } else
            foreach ($this->type->getTypes() as $type) {
                if ($type->isBuiltin()) continue;
                return $type->getName();
        }
    }
}

?>