<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 10/2/2017
 * Time: 2:43 PM
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorVisitHistory;

use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function referralData($user_type, $referral_code)
    {
        try{
            $users = User::select('users.id as user_id','users.full_name','users.mobile_no','users.email','users.referral_code','users.referrer_code','users.city','users.photo','users.gender',
                'details.national_id','details.dob','details.area','details.present_address','details.permanent_address',
                'company.company_name','experiences.position_name','experiences.is_current','experiences.start_date','experiences.end_date','experiences.company_verify','users.created_at')
                ->leftJoin('details','details.user_id','=','users.id')
                ->leftJoin('experiences', function($q){
                    $q->on('users.id','=','experiences.user_id')
                        ->where('experiences.is_current','YES')
                        ->orWhere('experiences.is_current','No');
                })
                ->leftJoin('company','company.id','=','experiences.company_id')
                ->where('users.user_type',$user_type)
                ->where('users.referrer_code', $referral_code)
                ->groupBy('user_id')->get();

            return $this->setReturnMessage($users,'success','OK',200,'Success!','referral user data.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','user not found.');
        }
    }


    public function myMrList(Request $request)
    {
        $full_name = $request->input('full_name');
        $company_id = $request->input('company_id');
        $product = $request->input('product');
        $city = $request->input('city');
        $status = $request->input('status');

        $users = User::where('user_type', 'mr')
            ->select('users.id as user_id','users.api_token','users.full_name','users.mobile_no','users.email','users.referral_code','users.referrer_code','users.city','users.photo','users.gender',
                'details.national_id','details.dob','details.area','details.present_address','details.permanent_address',
                'company.company_name','experiences.id as experience_id','experiences.position_name','experiences.is_current','experiences.start_date','experiences.end_date','experiences.company_verify','users.created_at')
            ->leftJoin('details','details.user_id','=','users.id')
            ->leftJoin('experiences', 'experiences.user_id' ,'=', 'users.id')
            ->leftJoin('company','company.id','=','experiences.company_id')
            ->leftJoin('product_experiences', 'product_experiences.user_id' ,'=', 'users.id')
            ->where('experiences.company_id', $company_id);

            if(!empty($full_name) && $full_name !=''){
                $users->where('users.full_name', 'like', '%'.$full_name.'%');
            }
            if(!empty($city) && $city !=''){
                $users->where('users.city', $city);
            }
            if(!empty($status) && $status !=''){
                $users->where('experiences.company_verify', $status);
            }
            if(!empty($product) && $product !=''){
                $users->where('product_experiences.generic_name', '%'.$product.'%');
            }

        $result = $users->groupBy('user_id')->get();
        return $this->setReturnMessage($result,'success','OK',200,'Success!','company mr list.');
    }


    public function searchMr(Request $request)
    {
        $company_id = $request->input('company_id');
        $full_name = $request->input('full_name');
        $product = $request->input('product');
        $city = $request->input('city');

        $users = User::where('user_type', 'mr')
            ->select('users.id as user_id','users.full_name','users.mobile_no','users.email','users.referral_code','users.referrer_code','users.city','users.photo','users.gender',
            'details.national_id','details.dob','details.area','details.present_address','details.permanent_address',
            'company.company_name','experiences.id as experience_id','experiences.company_id','experiences.position_name','experiences.is_current','experiences.start_date','experiences.end_date','experiences.company_verify','users.created_at')
            ->leftJoin('details','details.user_id','=','users.id')
            ->leftJoin('experiences', 'experiences.user_id' ,'=', 'users.id')
            ->leftJoin('company','company.id','=','experiences.company_id')
            ->leftJoin('product_experiences', 'product_experiences.user_id' ,'=', 'users.id')
            ->where(function($q){
                $q->where('experiences.is_current','YES')
                    ->orWhere('experiences.is_current','No');
            });
            if(!empty($company_id) && $company_id !=''){
                $users->where('experiences.company_id', $company_id);
            }
            if(!empty($full_name) && $full_name !=''){
                $users->where('users.full_name', 'like', '%'.$full_name.'%');
            }
            if(!empty($city) && $city !=''){
                $users->where('users.city', $city);
            }
            if(!empty($product) && $product !=''){
                $users->where('product_experiences.generic_name', '%'.$product.'%');
            }

        $result = $users->groupBy('user_id')->get();
        return $this->setReturnMessage($result,'success','OK',200,'Success!','all mr list.');
    }


    public function myDoctorVisit(Request $request)
    {
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');
        $full_name = $request->input('full_name');
        $city = $request->input('city');

        if($request->has('visit_date') && !empty($request->input('visit_date'))){
            $date = date_create($request->input('visit_date'));
            $visit_date = date_format($date, 'Y-m-d');
        }else{
            $visit_date = '';
        }

        $users = DoctorVisitHistory::select('users.id as user_id','users.full_name','users.mobile_no','users.city','users.photo',
                'doctor_visit_histories.doctor_name','doctor_visit_histories.doctor_mobile','doctor_visit_histories.doctor_designation','doctor_visit_histories.doctor_chamber','doctor_visit_histories.visit_date','doctor_visit_histories.visit_duration','doctor_visit_histories.visit_date','doctor_visit_histories.visit_purpose','doctor_visit_histories.sp_verified','doctor_visit_histories.remarks')
            ->leftJoin('users','users.id','=','doctor_visit_histories.id');

        if(!empty($company_id) && $company_id !=''){
            $users->where('doctor_visit_histories.company_id', $company_id);
        }
        if(!empty($user_id) && $user_id !=''){
            $users->where('doctor_visit_histories.user_id', $user_id);
        }
        if(!empty($visit_date) && $visit_date !=''){
            $users->where('doctor_visit_histories.visit_date', $visit_date);
        }
        if(!empty($full_name) && $full_name !=''){
            $users->where('users.full_name', 'like', '%'.$full_name.'%');
        }
        if(!empty($city) && $city !=''){
            $users->where('users.city', $city);
        }

        $result = $users->groupBy('user_id')->get();
        return $this->setReturnMessage($result,'success','OK',200,'Success!','all doctor list.');
    }





}







