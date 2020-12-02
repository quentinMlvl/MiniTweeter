<?php

namespace tweeterapp\model;

class Follow extends \Illuminate\Database\Eloquent\Model {

       protected $table = 'follow';
       protected $primaryKey = 'id';
       public $timestamps = false;

       public function user(){
              return $this->belongsTo('\tweeterapp\model\User', 'follower');
       }

       public function userFollowee(){
              return $this->belongsTo('\tweeterapp\model\User', 'followee');
       }

}