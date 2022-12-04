<?php

namespace FT\Attributes\PEL;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class PEL {

    public static function resolve_placeholders(string $value) {
        $out = $value;
        while (preg_match("/{{\s*(.+?)\s*}}/", $out, $_, PREG_OFFSET_CAPTURE)) {
            $out = substr($out, 0, $_[0][1])
                . eval("return " . $_[1][0] . ";")
                . substr($out, $_[0][1] + strlen($_[0][0]));
        }
        return $out;
    }

}

?>