<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Shares_point extends Model{
    
  protected $table = 'shares_point';
  protected $fillable = ['share_id','shares_participant_id','points'];
  
}

?>