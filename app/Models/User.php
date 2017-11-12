<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d');
    }


    public function get_user_details_by_id($user_id){
        return $this->select('users.id as user_id','users.full_name','users.mobile_no','users.email','users.referral_code','users.referrer_code','users.city','users.photo','users.gender',
            'details.national_id','details.dob','details.area','details.present_address','details.permanent_address',
            'company.company_name','experiences.position_name','experiences.is_current','experiences.start_date','experiences.end_date','experiences.company_verify','users.created_at')
            ->where('users.id','=',$user_id)
            ->leftJoin('details','details.user_id','=','users.id')
            ->leftJoin('experiences', function($join)use($user_id){
                $join->on('users.id','=','experiences.user_id')
                    ->where('experiences.user_id',$user_id)
                    ->where('experiences.is_current','YES');
//                    ->where(function($q){
//                        $q->where('experiences.is_current','YES')
//                            ->orWhere('experiences.is_current','No');
//                    });
            })
            ->leftJoin('company','company.id','=','experiences.company_id')
            ->first();
    }
}
