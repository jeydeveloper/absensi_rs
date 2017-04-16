<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table      = 'users';   // it's always better to specify it
    protected $primaryKey = 'usr_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return User::all();
    }

    public static function getUserByID($id)
    {
      $user = User::where('usr_id', $id)->first();
      return $user;
    }
}
