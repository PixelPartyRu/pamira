<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Extensions\MyCrudController;
use App\Shares;
use App\Http\Controllers\Extensions\MyDataEdit as DataEdit;
use App\Http\Controllers\Extensions\MyDataGrid as DataGrid;
use App\Shares_participants;
use App\Shares_point;
use Zofe\Rapyd\Facades\Rapyd;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use LaravelCaptcha\BotDetectCaptcha;
use Illuminate\Support\Facades\Session;




class SharesController extends MyCrudController {
    
    public function all($entity) {
        parent::all($entity);
        $this->filter = \DataFilter::source(Shares::query()->where("type","shares"));
        $this->grid = DataGrid::source($this->filter);
        $this->grid->add('id', 'ID', true);
        $this->grid->add('name', 'name', true);
        $this->addStylesToGrid();   
        $cell = $this->grid->add('parent',"Участники и баллы")->actions("participants_points", array("share"));
        $this->grid->paginate(1000);
        return $this->returnView();
        
        
    }
    
    public function edit($entity) {
        parent::edit($entity);
        $this->edit = DataEdit::source(new Shares());
        $typeField = $this->edit->add('type', '', 'hidden');
        $typeField->insertValue('shares');
        $typeField->updateValue('shares');
        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('img', 'Картинка', 'image')->move('uploads/shares')->preview(100, 100);
        $this->edit->add('description', 'Описание', 'redactor');
        $this->edit->checkbox('auth', 'Авторизация в акции');
        return $this->returnEditView();
    }
    
    public function shares_participants() {
        $this->filter = \DataFilter::source(Shares_participants::query());
        $this->grid = DataGrid::source($this->filter);
        $this->grid->add('id', 'ID', true);
        $this->grid->add('name', 'Имя', true);
        $this->grid->add('getRegion()', 'Регион', true);
        $this->grid->add('parent',"Редиктировать")->actions("edit_participants", array("modify"));
        $this->grid->edutUrl = "edit_participants";
        //$this->addStylesToGrid();
        return $this->returnView();
        //print "1";
    }
    
    public function edit_participants() {
 
        $this->edit = DataEdit::source(new Shares_participants());
        $typeField = $this->edit->add('type', '', 'hidden');
        $typeField->insertValue('shares');
        $typeField->updateValue('shares');
        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('salon', 'Салон', 'text')->rule('required');
        $this->edit->add('adress', 'Адрес', 'text')->rule('required');
        $this->edit->add('region_id', 'Регион', 'select')
                ->options(\App\Region::lists("name", "id")->all());
        return $this->returnEditView();
    }
    
    
    
    //Сетка редактирования баллов участников акций
    public function participants_points() {
        


        $params = \Illuminate\Support\Facades\Request::all();
        $share_id = $params["share"];
        
        $this->filter = \DataFilter::source(Shares_participants::query());
        $this->grid = DataGrid::source($this->filter);

        //$this->grid->setRelation( array("shares_point") );
        $this->grid->add('id', 'ID', true);
        $this->grid->add('name', 'Имя', true);
        $this->grid->add('points', 'Баллы', true);
        
        
        $data_all = Shares_participants::all();
        
        $region = \App\Region::all();
        
        $regions_info = array();
        
        foreach($region as $r) {
          //  d($r->id);
            $filter = \DataFilter::source(Shares_participants::query());
            
            $grid = DataGrid::source($filter);
            
            $grid->label = $r->name;
            $grid->custom_build = true;
            $grid->add('id', 'ID', true);
            $grid->add('name', 'Имя', true);
            $grid->add('points', 'Баллы', true);
            $grid->build_row($this->setPCforGrid($r->shares_participants, $share_id));


            $regions_info[] = $grid;
 
        }

        
        

        $data_col = $this->setPCforGrid(Shares_participants::query()->take(1)->get(), $share_id);

        $grid = $this->grid;
       $grid->build_row($data_col);
        $grid->custom_build = true;
        
        Rapyd::js("public/js/participants_points.js");

        Rapyd::style("public/css/style.css");
        $view_data = array(
                    'share_id' => $share_id,
                    'regions_info' => $regions_info, 
                    'filter' => $this->filter,
                    'title' => $this->entity,
                    'current_entity' => $this->entity,
                    'import_message' =>
                    (\Session::has('import_message')) ? \Session::get('import_message') : ''
            );
        return view('admin.participants_points_grid',$view_data);
    }
    
    //Форматирует коллекцию участников акций для отображения в сетке
    private function setPCforGrid($pc,$share_id) {
        $data_col = new \Illuminate\Database\Eloquent\Collection();
        foreach ($pc as $sp) {
            $sp_elem = new \stdClass();
            $sp_elem->name = $sp->name;
            $sp_elem->id = $sp->id;
            $qp = $sp->shares_point()->where("share_id", $share_id);
            if ($qp->count() == 0) {
                $sp_elem->points = 0;
            } else {
                $sp_elem->points = $sp->shares_point()->where("share_id", $share_id)->get()->first()->points;
            }
            $data_col->push($sp_elem);
        }
        
        return $data_col;
    }
    


    public function shares_page() {

        $data = array();

        
        $type = Request::route()->getPrefix();
        $type = str_replace("/", "", $type);
        $data["type"] = $type;

              switch ($type) {
            case "help": $data["caption"] =  "Помощь в выборе";
                break;
            case "shares": $data["caption"] = "Акции";
                break;
        }

        $data["shares"] = Shares::where("type",$type)->get();
        
        
        
        
        
        return view("shares.page",$data);
        
    }
    
    public function share_info_page($id) {
        $type = Request::route()->getPrefix();
        $type = str_replace("/", "", $type);
        $data["type"] = $type;

        switch ($type) {
            case "help": $data["caption"] = "Помощь в выборе";
                break;
            case "shares": $data["caption"] = "Акции";
                break;
        }


        if (\Illuminate\Support\Facades\Request::getMethod() == 'POST') {


            $form_data = Request::all();
            $result = Shares_participants::auth($form_data);
            
            if (isset($result["errors"])) {

                $data["form_errors"] = $result["errors"];
            }
            if(!$result) {
                $data["is_login"] = false;
            }

            
            
        }


        $data["share"] = Shares::find($id);
        if (isset($validator)) {
            $data["valid"] = $validator;
        }
        $data["user"] = Auth::guard("share_p")->user();

        if (!is_null($data["user"]) && $data["share"]->auth == 1) {
            //Участники акций
            $data["share_p"] = Shares_participants::all();
        }



        return view("shares.shares_info_page", $data);
    }

    public function share_registration() {
        $data = array();
        $type = Request::route()->getPrefix();
        $type = str_replace("/", "", $type);
        $data["type"] = $type;

        switch ($type) {
            case "help": $data["caption"] = "Помощь в выборе";
                break;
            case "shares": $data["caption"] = "Акции";
                break;
        }
        $share_id = Request::get("share_id");
        $data["share_id"] = $share_id;

        if (\Illuminate\Support\Facades\Request::getMethod() == 'POST') {
            $form_data = Request::all();
            $valid = Shares_participants::saveNewUserFrom($form_data);

            if (isset($valid["errors"])) {


                $data["form_errors"] = $valid["errors"];
            } else {
                if ($valid) {
                    if (isset($form_data["share_id"]))
                        return redirect("/shares/share/" . $form_data["share_id"]);
                    return redirect("/shares");
                }
            }
        }
        
        $data["regions_p"] = \App\Region::lists("name","id")->toArray();


        return view("shares.share_registration", $data);
    }

    public function logout() {
        Auth::guard("share_p")->logout();

    }
    
    public function save_point_info() {
        
        $r = \Illuminate\Support\Facades\Request::all();
        if(!empty($r)) {

            $points = $r["points"];
            unset($r["points"]);
            $sp = Shares_point::firstOrCreate($r);
            $sp->points = $points;
            $sp->save();

            
        }

    }
    
    public function isHuman($code) {
        
       $instance = BotDetectCaptcha::getInstance(); 


    }
    
    public function captcha() {



        $letters = '123456789'; // алфавит

        $caplen = 4; //длина текста
        $width = 100;
        $height = 30; //ширина и высота картинки
        $font = 'fonts/cour.ttf'; //шрифт текста
        $fontsize = 12; // размер текста

        header('Content-type: image/png'); //тип возвращаемого содержимого (картинка в формате PNG) 

        $im = imagecreatetruecolor($width, $height); //создаёт новое изображение
        imagesavealpha($im, true); //устанавливает прозрачность изображения
        $bg = imagecolorallocatealpha($im, 0, 0, 0, 127); //идентификатор цвета для изображения
        imagefill($im, 0, 0, $bg); //выполняет заливку цветом

        putenv('GDFONTPATH=' . realpath('.')); //проверяет путь до файла со шрифтами

        $captcha = ''; //обнуляем текст
        for ($i = 0; $i < $caplen; $i++) {
            $captcha .= $letters[rand(0, strlen($letters) - 1)]; // дописываем случайный символ из алфавила 
            $x = ($width - 20) / $caplen * $i + 10; //растояние между символами
            $x = rand($x, $x + 4); //случайное смещение
            $y = $height - ( ($height - $fontsize) / 2 ); // координата Y
            $curcolor = imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)); //цвет для текущей буквы
            $angle = rand(-25, 25); //случайный угол наклона 
            imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]); //вывод текста
        }

        // открываем сессию для сохранения сгенерированного текста
        session_start();
        $_SESSION['captcha_hash'] = bcrypt($captcha);

        imagepng($im); //выводим изображение
        imagedestroy($im); //отчищаем память
    }

}
?>