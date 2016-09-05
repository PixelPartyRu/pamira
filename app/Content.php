<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Content
 *
 * @author Alina2
 */
class Content extends Model {

    protected $table = 'content';
    protected $fillable = ['name','img','text','rubric','type',"alias"];
    protected $guarded = array("id");
    
    
    //Получаем все рубрики, который существуют для контента
    public static function get_all_content_rubric() {
        
        $rubric = self::query()->lists("rubric")->toArray();
       // dd($rubric);
        $rubric = implode("|",$rubric);
        $rubrics = explode("|",$rubric);
        
       // dd($rubrics);
        foreach($rubrics as $k=>$v) {
            if($v === "") unset($rubrics[$k]);
        }
        return array_unique($rubrics);
        
        //foreach($rubric as $rubric)
        
    }
   public static function all_rubric_values() {
       $rubrics = self::get_all_content_rubric();
       $values = array();
       foreach($rubrics as $rubric) {
           $values[$rubric] = $rubric;
       }
       return $values;
       
   }
   
   public static function getArticleByAlias( $alias ){
       
       $q = self::where("alias",$alias);
       if($q->count() > 0 ){
           return $q->get()->first();
       }
       return null;
       
       
   }
    
    public function add_rubric($rubric) {
      /*  $catalogs = explode("|", $this->catalogs);
        if (!in_array($catalog_id, $catalogs)) {
            $catalogs[] = "<$catalog_id>";
        }
        $this->catalogs = implode("|", $catalogs);
        $this->save();*/
    }
    
    public function getFormatDate() {
        $date = new \DateTime($this->created_at);

        return date("d-m-Y", $date->getTimestamp());
    }
    
    public function getContentRubric() {
        $rubric = explode("|", $this->rubric);
        foreach ($rubric as $k => $v) {
            if ($v === "")
                unset($rubric[$k]);
        }
        return $rubric;
    }

    //put your code here
}
