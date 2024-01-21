<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;
    protected $fillable=['group_id','user_id','status'];
    protected $table='group_users';

    public function getGroup(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Group::class,'id','group_id')
            ->where('status','=',1);
    }
}
