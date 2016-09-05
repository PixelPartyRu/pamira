<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Extensions\MyCrudController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\MyDataGrid as DataGrid;
use \Illuminate\Support\Facades\Schema;
use App\Content;
//use App\Product;
use App\Http\Controllers\Extensions\MyDataEdit;
use Zofe\Rapyd\Facades\Rapyd;
use Illuminate\Support\Facades\Response;

class ContentController extends MyCrudController {

    public function all($entity) {

        parent::all($entity);


        $this->filter = \DataFilter::source(new Content());

        $this->filter->add('type', 'Тип', 'select')->options(array(
            "article" => "Статья",
            "page" => "Страница",
            "news" => "Новость",
            "help" => "Помощь в выборе"
            )); // Filter with Select List
        $this->filter->add('name', 'Имя', 'text'); // Filter by String
        $this->filter->submit('search');
        $this->filter->reset('reset');
        $this->filter->build();

        $this->grid = \DataGrid::source($this->filter);
        $this->grid->add('id', 'ID', true)->style("width:100px");
        // $this->grid->add('parent_id','parent_id');
        //this->grid->add('name', 'Categories', 'button'); 


        $this->addStylesToGrid();
        $this->grid->add("name", "Название статьи", "text");
        $this->grid->add("alias", "Путь к материалу", "text");
        $this->grid->add("text", "Текст", "text");
        $this->grid->add("type", "Тип", "text");

        // $this->grid->add('parent', "Дочерний каталог")->actions("all", array("catalog"));
$this->grid->paginate(1000);
        return $this->returnView();
    }

    public function edit($entity) {

        $r = Request::all();


        parent::edit($entity);


        $this->edit = MyDataEdit::source(new Content());

        $this->edit->add('name', 'Имя', 'text')->rule('required');
        $this->edit->add('alias', 'Для формирования пути', 'text')->rule('required');
        $this->edit->add("generate_alias", "Сгенерировать на базе названия материала", self::$ext_apth . '\ButtonField');

        $this->edit->add('img', 'Картинка', 'image')->move('uploads/content/')->preview(100, 100)->rule('required');


        $this->edit->add('type', 'Тип', 'select')->options(array(
            "article" => "Статья",
            "page" => "Страница",
            "news" => "Новость",
            "help" => "Помощь в выборе"
            ));


        //d($values);


        $this->edit->add('text', 'Текст', 'redactor');

        /*Rapyd::js("public/js/add_rubric.js");
        if (isset($r["update"]) && isset($_POST['rubric'])) {
            $this->edit->model->rubric = implode("|", $_POST['rubric']);
            unset($_POST['rubric']);

            $this->edit->model->save();




            // return redirect("/panel/Content/edit?modify=".$this->edit->model->id);
            // $test->update($attributes)
        }
        $this->edit->add('rubric', 'Рубрики', 'checkboxgroup')->options(Content::all_rubric_values());
        $this->edit->add('add_rubric', 'Добавить рубрику', 'text');
        $this->edit->add("add_rubric_button", "Добавить", self::$ext_apth . '\ButtonField');*/
        return $this->returnEditView();
    }

    public function translit_alias($val) {

        return \App\Jobs\Helper::translit($val);
    }

    //Страница с материалами
    public function news_page($rubric = null) {

        $data = array();

        $data['all_rubric_list'] = Content::get_all_content_rubric();
        // dd($data['all_rubric_list']);
        $q = Content::where("type", "article");
        if( !is_null($rubric) ) {
            $q = $q->where("rubric",$rubric)->orWhere("rubric","like","%|$rubric|%")
                ->orWhere("rubric","like","%|$rubric")->orWhere("rubric","like","$rubric|%");
            $data["rubric_name"] = $rubric;
        }
                
                
        $data["article"] = $q->get();
        


        return view("content.news_page", $data);
    }
    
    public function article_page($alias) {
        
        $data['article'] = Content::getArticleByAlias($alias);
        if( is_null( $data['article'] ) ) {
            return view("message",array("message" => "Такого материала не существует"));
        }
        $data['rubrics'] = $data['article']->getContentRubric();
      //  dd(  $data['rubric']  );
        return view("content.article_page", $data);
        
    }
    
    
    //раздел новости
    public function news() {
        
        $q = Content::where("type", "news");
        $data["news"] = $q->get();
        return view("content.news", $data);
        
    }
    
    //Помощь в выборе
    public function help() {

        $q = Content::where("type", "help");
        $data["news"] = $q->get();
        return view("content.help", $data);
    }

}
