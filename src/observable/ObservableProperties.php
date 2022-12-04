<?php

namespace FT\Attributes\Observable;

use FT\Attributes\ClassCache;
use FT\Attributes\Json\JsonIgnore;
use FT\Attributes\Lombok\AccessLevel;
use FT\Attributes\Lombok\Setter;

trait ObservableProperties
{
    #[JsonIgnore]
    #[Setter(AccessLevel::PRIVATE)]
    private array $subscribers = ['__get' => [], '__set' => []];

    public function &__get($name) {
        $pd = ClassCache::get(__CLASS__)->get_property($name);
        if ($pd !== null)
            $this->emit(ObservableAction::GET, $pd, null);

        return $this->{$name};
    }

    public function __set($name, $value) {
        $pd = ClassCache::get(__CLASS__)->get_property($name);
        if ($pd !== null)
            $this->emit(ObservableAction::SET, $pd, $value);

        $this->{$name} = $value;
    }

    protected function observe($name, ObservableAction $type, $callback) {
        if ($type === ObservableAction::ANY) {
            $this->subscribers[ObservableAction::GET->value][$name][] = $callback;
            $this->subscribers[ObservableAction::SET->value][$name][] = $callback;
        }
        else $this->subscribers[$type->value][$name][] = $callback;
    }

    private function emit(ObservableAction $action, $pd, $value) {
        if (!key_exists($pd->name, $this->subscribers[$action->value]))
            $this->subscribers[$action->value][$pd->name] = [];

        if (key_exists('*', $this->subscribers[$action->value])) {
            foreach ($this->subscribers[$action->value]['*'] as $callback)
                $callback($pd, $value);
        }
        else
        foreach ($this->subscribers[$action->value][$pd->name] as $callback)
            $callback($pd, $value);
    }
}

?>