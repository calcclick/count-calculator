<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileInfo extends Model
{
    //
    protected $guarded = [];
//    protected $hidden = ['created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
