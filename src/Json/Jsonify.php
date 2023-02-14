<?php

namespace FT\Attributes\Json;

trait Jsonify
{

    public function toJson() {
        return Json::encode($this);
    }

}


?>