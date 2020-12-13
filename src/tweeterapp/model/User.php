<?php

namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model {

       protected $table = 'user';
       protected $primaryKey = 'id';
       public $timestamps = false;
       
       public function tweets()
       {
              return $this->hasMany('\tweeterapp\model\Tweet', 'author');
       }
       
       public function follow()
       {
              return $this->belongsToMany('tweeterapp\model\User',
                                          'follow',
                                          'followee',
                                          'follower');
       }

       public function followTweets()
       {
              return $this->belongsToMany('tweeterapp\model\User',
                                          'follow',
                                          'followee',
                                          'follower')->with('tweets');
       }

       public function followedBy()
       {
              return $this->belongsToMany('tweeterapp\model\User',
                                          'follow',
                                          'follower',
                                          'followee');
       }
}