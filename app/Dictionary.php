<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model {
    protected $table = 'dictionary';
    
    protected $fillable = ['name', 'value'];
    protected $guarded = array("id");
    
    public static function getByName($name) {
        return self::where('name', $name)->first();
    }

    public static function getAll() {
        return self::get();
    }
}
