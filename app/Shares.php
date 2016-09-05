<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Shares extends Model{
    
  protected $table = 'shares';
  
  public function getCaption() {
      switch ($this->type){
          case "help": return "Помощь в выборе"; break;
          case "shares": return "Акции"; break;
      }
  }
  
}

?>