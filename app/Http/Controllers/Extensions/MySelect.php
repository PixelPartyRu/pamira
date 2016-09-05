<?php

namespace App\Http\Controllers\Extensions;

use Zofe\Rapyd\DataForm\Field\Select;

class MySelect extends Select {

    public function setValue($val) {
        $this->value = $val;
    }

}
