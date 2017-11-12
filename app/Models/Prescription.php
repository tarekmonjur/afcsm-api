<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 5:44 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{

    public function getPrescriptionAttribute($value){
        return url('/'.$value);
    }


}