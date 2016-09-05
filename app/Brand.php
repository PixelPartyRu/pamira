<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\Helper;
use App\Product;
use App\Catalog;

class Brand extends Model {

  protected $table = 'brands';
  protected $fillable = array("title","main_page");

  //пусть поле с промо-текстом будет description1
  public function getSlogan() {
    return $this->description1;
  }

  public function getSEOtext() {
    return $this->description2;
  }

  public function products() {
    return $this->hasMany("App\Product","brand_id","id");
  }

  public static function getByAlias($alias) {
    $q = self::where("alias", $alias);
    if ($q->count() == 0)
      return null;
    return $q->get()->first();
  }

  public static function getByName($title) {
    $q = self::where("title", $title);
    if ($q->count() == 0)
      return null;
    return $q->get()->first();
  }

  /*public function getTypes() {
     $products = $this->products()->
  }*/

  // Категории товаров, достпуные для бренда
  public function getCategories() {
    $catalog = $this->products()->lists('catalog_id');
    return Catalog::find( $catalog->toArray() );
  }

  public function getCatalogs() {
    $catalogs = explode("|", $this->catalogs);
    foreach($catalogs as $k=>&$cat) {
      if($cat=="") {unset($catalogs[$k]);continue;}
      $cat = (int) preg_replace('/[^0-9]/', '', $cat);
    }
    return $catalogs;
  }

  public function hasAccessCatalog($catalog_id) {
    $catalogs = explode("|", $this->catalogs);
    if (in_array("<$catalog_id>", $catalogs)) {
      foreach ($catalogs as $k => $v) {
        if ($v == "<$catalog_id>") {
          return true;
        }
      }
    }
    return false;
  }

  public function add_catalog($catalog_id) {
    $catalogs = explode("|", $this->catalogs);
    $catalogs[] = "<$catalog_id>";
    $catalogs = array_unique($catalogs);
    $this->catalogs = implode("|", $catalogs);
    $this->save();
  }

  public function delete_catalog($catalog_id) {
    $catalogs = explode("|", $this->catalogs);
    if (in_array("<$catalog_id>", $catalogs)) {
      foreach($catalogs as $k=>$v) {
        if($v == "<$catalog_id>" ) {
          unset($catalogs[$k]);
        }
      }
    }
    $this->catalogs = implode("|", $catalogs);
    $this->save();
  }

  public static function boot() {
    parent::boot();
    static::created(function($brand) {
      $brand->alias = Helper::translit($brand->title);
      $brand->save();
    });
  }
}
