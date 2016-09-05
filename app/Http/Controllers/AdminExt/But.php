<?php 

namespace App\Http\Controllers\AdminExt;
use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\DataGrid\Cell;
use Illuminate\Support\Facades\View;

class But extends Column
{

    public function build($view = '') {
        parent::build();
        Persistence::save();
        return 1;
    }

}



