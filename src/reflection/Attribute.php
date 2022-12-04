<?php

namespace FT\Attributes\Reflection;

use Exception;
use FT\Attributes\Inheritable;
use FT\Attributes\PEL\PEL;
use ReflectionAttribute;
use ReflectionClass;

final class Attribute {
    public readonly string $name;
    public readonly string $displayName;
    public readonly int $target;
    public readonly bool $is_repeated;
    private readonly object $arguments;
    public readonly bool $is_inheritable;

    public function __construct(private readonly ReflectionAttribute $attr)
    {
        $this->displayName = $attr->getName();
        $this->name = strtolower($this->displayName);
        $this->target = $attr->getTarget();
        $this->is_repeated = $attr->isRepeated();

        $rflc = new ReflectionClass($attr->getName());
        $this->is_inheritable = !empty($rflc->getAttributes(Inheritable::class));
        $constr = $rflc->getConstructor();
        $params = [];
        if ($constr !== null)
            $params = array_map(fn ($i) => new ParameterDescriptor($i), $constr->getParameters());

        $fargs = [];
        foreach ($attr->getArguments() as $key => $value) {
            if (is_int($key)) {
                if (count($params) > 1)
                    throw new Exception("Positional arguments are not permitted for attributes with more than 1 parameter. Attribute members must be qualified by their name");

                $fargs[$params[0]->name] = $params[0]->has_attribute(PEL::class)
                    ? PEL::resolve_placeholders($value)
                    : $value;
                continue;
            }

            $pindex = array_search($key, array_map(fn ($i) => $i->name, $params));
            if ($pindex === false)
                throw new Exception("$key does not exist on @" . $rflc->getShortName());

            $param = $params[$pindex];
            $fargs[$key] = $param->has_attribute(PEL::class) ? PEL::resolve_placeholders($value) : $value;
        }

        $out_params = array_diff(array_map(fn ($i) => $i->name, $params), array_keys($fargs));
        foreach ($out_params as $name) {
            $pindex = array_search($name, array_map(fn ($i) => $i->name, $params));
            if ($params[$pindex]->hasDefault) {
                $resolved = $params[$pindex]->defaultValue;
                if ($params[$pindex]->has_attribute(PEL::class))
                    $resolved = PEL::resolve_placeholders($resolved);

                $fargs[$name] = $resolved;
            }
        }

        $this->arguments = (object) $fargs;
    }

    public function getArguments() : object
    {
        return $this->arguments;
    }

    public function getArgument($name): mixed
    {
        if (property_exists($this->arguments, $name))
            return $this->arguments->{$name};

        return null;
    }

    public function newInstance() {
        $rflc = new ReflectionClass($this->name);
        $constr = $rflc->getConstructor();
        $args = [];
        if ($constr !== null) {
            $params = array_map(fn ($i) => new ParameterDescriptor($i), $constr->getParameters());

            foreach ($params as $p)
                $args[] = $this->getArgument($p->name);

            return $rflc->newInstance(...$args);
        }

        return $this->attr->newInstance();
    }
}

?>