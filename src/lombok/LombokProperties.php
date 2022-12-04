<?php
namespace FT\Attributes\Lombok;

use FT\Attributes\Observable\ObservableAction;
use FT\Attributes\Observable\ObservableProperties;
use RuntimeException;

trait LombokProperties
{
    use ObservableProperties;

    public function __construct() {
        $this->observe('*', ObservableAction::GET, function ($pd) {
            $allowed = true;

            $pd_getter = $pd->get_attribute(Getter::class);
            if ($pd_getter !== null) $allowed = $pd_getter->getArgument('level') === AccessLevel::PUBLIC;

            if (!$allowed)
                throw new RuntimeException("Can not access private property $pd->name");
        });

        $this->observe('*', ObservableAction::SET, function ($pd) {
            $allowed = true;

            $pd_setter = $pd->get_attribute(Setter::class);
            if ($pd_setter !== null) $allowed = $pd_setter->getArgument('level') === AccessLevel::PUBLIC;

            if (!$allowed)
                throw new RuntimeException("Can not set private property $pd->name");
        });
    }
}

?>