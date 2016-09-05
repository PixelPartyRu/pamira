<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model {

  protected $table = 'banners';
  protected $fillable = array("title","main_page");

  public function getUrl() {
    if (!preg_match("~^(?:f|ht)tps?://~i", $this->url)) {
      $this->url = "http://" . $this->url;
    }
    return $this->url;
  }
}
