<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Notifiable;
    protected $guarded = [];

    protected $appends = ['full_name'];

    public function studentSessions(){
        return $this->hasMany(StudentSession::class);
    }


    public function getFullNameAttribute(){

        return $this->first_name .' '.$this->middle_name.''.$this->last_name;
    }
}
