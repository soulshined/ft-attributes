<?php

namespace FT\Attributes\Observable;

enum ObservableAction : string {

    case GET = '__get';
    case SET = '__set';
    case ANY = '__get__set';

}

?>