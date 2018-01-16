<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/26/2017
 * Time: 1:25 PM
 */

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use App\Models\Company;

use Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct()
    {
        $httpClient = new CommonController;
        $this->httpClient = $httpClient;

        if(env('API_MODE') == 0) {
            // test api
            $this->smapi = 'http://103.23.41.190/smapi/v0.0.1/';
            $this->saapi = 'http://103.23.41.190/saapi/v0.0.1/';
        }
        elseif(env('API_MODE') == 1)
        {
            // live api
            $this->smapi = 'http://35.162.97.16/smapi/v0.0.1/';
            $this->saapi = 'http://35.162.97.16/saapi/v0.0.1/';
        }
    }


    public function userRegister(Request $request)
    {
        $validator = $this->registerValidation($request);
        if($validator->fails()){
            if($request->input('user_type') == 'mr'){
                $has_user = User::where('mobile_no', $request->input('mobile_no'))
                    ->where('status', 'unverified')
                    ->first();
                if($has_user)
                    return $this->setReturnMessage(['pin' => $has_user->pin],'error','NotOK',402,'Verification!','User unverified!');
            }

            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{

//            if($request->has('referrer_code') && !empty($request->input('referrer_code'))){
//                $check_referer_code = User::where('referral_code', $request->input('referrer_code'))
//                    ->where('status', 'verified')
//                    ->count();
//                if($check_referer_code <= 0){
//                    return $this->setReturnMessage([],'error','NotOK',400,'Error!','Referrer code is invalid/unverified');
//                }
//            }

             if($request->has('referrer_code') && !empty($request->input('referrer_code'))) {
                 $referrer_code = $request->input('referrer_code');
             }else{
                 $referrer_code = '';
             }

             $checkReferrer = $this->httpClient->sendRequest('POST', $this->saapi.'smart-assistant-referral-request-from-mr', [
                 'referrer_code' => $referrer_code,
                 'mr_mobile_no' => $request->input('mobile_no'),
                  'sm_full_name' => $request->input('full_name'),
                  'sm_gender' => $request->input('gender'),
                  'sm_dob' => '',
                  'sm_email' => $request->input('email'),
                  'sm_city' => $request->input('city'),
                  'sm_area' => '',
                  'sm_full_address' => '',
                  'sm_api_version' => '',
                  'sm_app_version' => '',
                  'sm_uuid' => '',
                  'sm_firebaseid' =>'',
                  'sm_referral_code' => '',
             ]);
             if ($checkReferrer->code == 200 || $checkReferrer->code == '200') {
                 $mr_mobile_no = $request->input('mobile_no');
                 if(isset($checkReferrer->data->sa_mobile_no)){
                     $sa_mobile_no = $checkReferrer->data->sa_mobile_no;
                 }
             }else{
                 return $this->setReturnMessage([], 'error', 'NotOK', 400, 'Error!', $checkReferrer->message);
             }

            $user = new User;
            $user->full_name = $request->input('full_name');
            $user->email = $request->input('email');
            $user->mobile_no = $request->input('mobile_no');
            $user->password = app('hash')->make($request->input('password'));
            $user->user_type = $request->input('user_type');
            $user->gender = $request->input('gender');
            $user->city = $request->input('city');
            $user->referrer_code  = ($request->has('referrer_code'))?$request->input('referrer_code'):'';
            $user->pin = random_int(100000,999999);
            $user->save();

            $user->referral_code = $this->generateReferralCode($user->id, $user->full_name, $user->mobile_no);
            $user->save();

            if(isset($mr_mobile_no) && isset($sa_mobile_no)) {
                $connection = new Connection;
                $connection->mr_mobile_no = $mr_mobile_no;
                $connection->sa_mobile_no = $sa_mobile_no;
                $connection->save();
            }

            $pinMessage = "Registration successfully you PIN number is ".$user->pin;
            $mobileNumber = '88'.$request->input('mobile_no');
            $result = $this->sendPin($pinMessage, $mobileNumber);

            return $this->setReturnMessage(['pin' => $user->pin],'success','OK',200,'Success!','Registration Success.');

        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Registration not success.');
        }
    }


    private function registerValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|max:45|min:2',
            'email' => 'required|email',
            'mobile_no' => 'required|unique:users|min:11|max:11|regex:/01+[0-9]+$/',
            'password' => 'required|max:20|min:6',
            'user_type' => 'required',
        ]);

        return $validator;
    }


    public function companyRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id'  => 'required',
            'company_license_number'  => 'required',
            'company_address'  => 'required',
            'contact_person_name'  => 'required',
            'email'  => 'required|email',
            'mobile_no'  => 'required|unique:users|min:11|max:11|regex:/01+[0-9]+$/',
            'password'  => 'required|min:6|max:20',
        ]);

        if($validator->fails()){
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!', '');
        }

        try{
            \DB::beginTransaction();

            $company = new Company;
            $company->company_id = $request->input('company_id');
            $company->company_logo = $request->input('company_logo');
            $company->company_license_number = $request->input('company_license_number');
            $company->company_address  = $request->input('company_address ');
            $company->company_details = $request->input('company_details');
            $company->contact_person_name = $request->input('contact_person_name');
            $company->contact_person_email = $request->input('email');
            $company->contact_person_mobile_no = $request->input('mobile_no');
            $company->save();

            $user = new User;
            $user->company_id = $company->id;
            $user->full_name = $request->input('contact_person_name');
            $user->email = $request->input('email');
            $user->mobile_no = $request->input('mobile_no');
            $user->password = app('hash')->make($request->input('password'));
            $user->user_type = 'company';
            $user->save();

            $user->referral_code = $this->generateReferralCode($user->id, $user->full_name, $user->mobile_no);
            $user->save();

            \DB::commit();

            return $this->setReturnMessage([],'success','OK',200,'Success!','Registration Success.');
        }catch(\Exception $e){
            \DB::rollBack();
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Registration not success.');
        }
    }


    private function sendPin($sms, $msisdn)
    {
         $send_sms_flag = env('SEND_SMS_FLAG');

          $userName = 'afc_health';
          $password = 'SSl@123';
          $sid = 'AFC_HEALTH';
          $sms = urlencode($sms);
          $msisdn = $msisdn;//'8801783605360';
          $csmsid = '123456789';
          $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php?user='.$userName.'&pass='.$password.'&sid='.$sid.'&sms='.$sms.'&msisdn='.$msisdn.'&csmsid='.$csmsid.'';

          if($send_sms_flag == 1){

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($curl, CURLOPT_USERAGENT, 'Sample cURL Request');
              $response = curl_exec($curl);
              curl_close($curl);

              if(substr($response, 0, 5) == "<?xml") {

                  //echo 'Is XML';
                  $responseObj = new \SimpleXMLElement($response);

                  if( (is_object($responseObj)) && ($responseObj->LOGIN == 'SUCCESSFULL') ){

                      $insert = array(
                          'mobile_number' => $msisdn,
                          'sms_body'      => $sms,
                          'sms_response'  => $response,
                          'sms_status'    => 1,
                          'created_at'    => date('Y-m-d H:i:s')
                      );

                      \DB::table('sms_log')->insert($insert);
                      return true;

                  }else{

                      $insert = array(
                          'mobile_number' => $msisdn,
                          'sms_body'      => $sms,
                          'sms_response'  => $response,
                          'sms_status'    => 2,
                          'created_at'    => date('Y-m-d H:i:s')
                      );

                      \DB::table('sms_log')->insert($insert);
                      return false;
                  }

              } else {

                  //echo 'Not XML';
                  $insert = array(
                      'mobile_number' => $msisdn,
                      'sms_body'      => $sms,
                      'sms_response'  => $response,
                      'sms_status'    => 2,
                      'created_at'    => date('Y-m-d H:i:s')
                  );

                  \DB::table('sms_log')->insert($insert);
                  return false;
              }

          }else{
              return true;
          }
    }


    public function verifyRegistrationPin(Request $request)
    {
        $validator = Validator::make($request->all(), ['pin' => 'required|min:6', 'mobile_no' => 'required|min:11|max:11|regex:/01+[0-9]+$/']);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{
            $user = User::where('pin', $request->input('pin'))
                    ->where('mobile_no', $request->input('mobile_no'))
                    ->first();

            $reward_balance = 0.00;
            if($user->user_type == 'mr'){
                $checkMR = $this->httpClient->sendRequest('POST', $this->smapi.'smart-marketeer-get-reward-balance-amount', ['mr_mobile_no' => $request->input('mobile_no')]);
                if ($checkMR->code == 200 || $checkMR->code == '200') {
                    $reward_balance = doubleval($checkMR->data->reward_balance);
                }

                $connection = Connection::where('mr_mobile_no', $request->input('mobile_no'))->first();
                if(count($connection) > 0) {
                    $mr_mobile_no = $connection->mr_mobile_no;
                    $sa_mobile_no = $connection->sa_mobile_no;
                }else{
                    $mr_mobile_no = $request->input('mobile_no');
                    $sa_mobile_no = '';
                }
                $this->httpClient->sendRequest('POST', $this->saapi . 'smart-assistant-referral-connection-confirm-request-from-mr', ['mr_mobile_no' => $mr_mobile_no, 'sa_mobile_no' => $sa_mobile_no, 'is_registration_successful' => 1]);
            }

            if($user){
                $user->status = 'verified';
                $user->pin = '';
                $user->api_token = str_random(32).time();
                $user->save();

                $userData = (object)[];
                $userData->user_id = $user->id;
                $userData->{'api-token'} = $user->api_token;
                $userData->full_name = $user->full_name;
                $userData->email = $user->email;
                $userData->mobile_no = $user->mobile_no;
                $userData->gender = $user->gender;
                $userData->city = $user->city;
                $userData->referral_code = $user->referral_code;
                $userData->photo = $user->photo;
                $userData->reward_balance = $reward_balance;

                return $this->setReturnMessage($userData,'success','OK',200,'Success!','Verification Success.');
            }else{
                return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Your pin/mobile not match.');
            }

        }catch(\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Verification not success.');
        }
    }


    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:11|max:11|regex:/01+[0-9]+$/',
            'password' => 'required|min:6|max:20',
        ]);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{
            $user = User::where('mobile_no', $request->input('mobile_no'))->first();

            if(count($user) > 0) {
                $reward_balance = 0.00;
                //check for web company login
                if($request->has('user_type')){
                    if($request->input('user_type') == 'notmr' && $user->user_type == 'mr'){
                        return $this->setReturnMessage([], 'error', 'NotOK', 402, 'Verification!', 'Unauthorized account.');
                    }elseif($request->input('user_type') == 'mr' && $user->user_type == 'mr'){
                        $checkMR = $this->httpClient->sendRequest('POST', $this->smapi.'smart-marketeer-get-reward-balance-amount', ['mr_mobile_no' => $request->input('mobile_no')]);
                        if ($checkMR->code == 200 || $checkMR->code == '200') {
                            $reward_balance = doubleval($checkMR->data->reward_balance);
                        }
                    }
                }

                if($user->status == 'unverified'){
                    return $this->setReturnMessage([], 'error', 'NotOK', 402, 'Verification!', 'User unverified!');
                }

                if (app('hash')->check($request->input('password'), $user->password)) {
                    $user->api_token = str_random(32) . time();
                    $user->save();

                    $userData = (object)[];
                    $userData->user_id = $user->id;
                    if($request->input('user_type') == 'notmr'){
                      $userData->user_type = $user->user_type;
                    }
                    $userData->company_id = $user->company_id;
                    $userData->{'api-token'} = $user->api_token;
                    $userData->full_name = $user->full_name;
                    $userData->email = $user->email;
                    $userData->mobile_no = $user->mobile_no;
                    $userData->gender = $user->gender;
                    $userData->city = $user->city;
                    $userData->referral_code = $user->referral_code;
                    $userData->photo = $user->photo;
                    $userData->reward_balance = $reward_balance;

                    return $this->setReturnMessage($userData, 'success', 'OK', 200, 'Success!', 'Login Success.');
                } else {
                    return $this->setReturnMessage([], 'error', 'NotOK', 400, 'Error!', 'UserID/Password mismatch.');
                }
            }else{
                return $this->setReturnMessage([], 'error', 'NotOK', 400, 'Error!', 'User account not found.');
            }
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Login not success.');
        }
    }


    public function resendVerifyPin(Request $request)
    {
        $validator = Validator::make($request->all(),['mobile_no' => 'required|min:11|max:11|regex:/01+[0-9]+$/']);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{
            $pin = random_int(100000,999999);
            $user = User::where('mobile_no', $request->input('mobile_no'))->update(['pin' => $pin]);
            if(!$user){
                return $this->setReturnMessage([],'error','NotOK',400,'Error!','User not found.');
            }

            $pinMessage = "Your PIN number is ".$pin;
            $mobileNumber = '88'.$request->input('mobile_no');
            $result = $this->sendPin($pinMessage, $mobileNumber);
            if($result){
                return $this->setReturnMessage(['pin' => $pin],'success','OK',200,'Success!','Pin send success.');
            }else{
                return $this->setReturnMessage([],'error','NotOK',400,'Error!','Pin not send.');
            }
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Pin not send.');
        }
    }


    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(),['mobile_no' => 'required|min:11|max:11|regex:/01+[0-9]+$/']);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{
            $pin = random_int(100000,999999);
            $user = User::where('mobile_no', $request->input('mobile_no'))->update(['pin' => $pin]);
            if(!$user){
                return $this->setReturnMessage([],'error','NotOK',500,'Error!','User not found.');
            }
            $pinMessage = "Your PIN number is ".$pin;
            $mobileNumber = '88'.$request->input('mobile_no');
            $result = $this->sendPin($pinMessage, $mobileNumber);
            if($result){
                return $this->setReturnMessage(['pin' => $pin],'success','OK',200,'Success!','Pin send success.');
            }else{
                return $this->setReturnMessage([],'error','NotOK',400,'Error!','Pin not send.');
            }
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Pin not send.');
        }
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pin' => 'required|max:6',
            'mobile_no' => 'required|min:11|max:11|regex:/01+[0-9]+$/',
            'password' => 'required|max:20|min:6',
        ]);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage($validator->errors()->messages(),'error','NotOK',400,'Required!',$message);
        }

        try{
            $user = User::where('mobile_no', $request->input('mobile_no'))
                ->where('pin', $request->input('pin'))
                ->update([
                    'status' => 'verified',
                    'password' => app('hash')->make($request->input('password'))
                ]);

            if(!$user){
                return $this->setReturnMessage([],'error','NotOK',400,'Error!','Pin/Mobile not match.');
            }

            return $this->setReturnMessage([],'success','OK',200,'Success!','Password successfully changed.');

        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Password not changed.');
        }
    }


    public function userLogout(Request $request)
    {
        try{
            User::where('api_token', $request->header('api-token'))
                ->update(['api_token' => '']);
            return $this->setReturnMessage([],'success','OK',200,'Success!','Logout success.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Logout not success.');
        }
    }


    public function userChecking(Request $request)
    {
        $mobile_no = $request->input('doctor_mobile');
        $referral_code = $request->input('mr_referral_code');

        if(!empty($mobile_no)){
            $user = User::where('user_type', 'mr')
                ->where('mobile_no', $mobile_no)
                ->first();

            if(count($user) > 0){
                return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','User found.');
            }else{
                $success = true;
            }
        }


        if(!empty($referral_code))
        {
            $user = User::where('user_type', 'mr')
                ->where('referral_code', $referral_code)
                ->first();

            if(count($user) > 0){
                if($user->status == 'verified'){
                    $data = (object)[];
                    $data->user_id = $user->id;
                    $data->mr_mobile = $user->mobile_no;

                    return $this->setReturnMessage($data,'success','OK',200,'Success!','verified referral code.');
                }else{
                    return $this->setReturnMessage((object)[],'error','OK',300,'Error!','Unverified user.');
                }
            }else{
                return $this->setReturnMessage((object)[],'error','NotOK',420,'Error!','Referral code not found.');
            }
        }

        if(isset($success) && $success === true){
            return $this->setReturnMessage((object)[],'success','OK',220,'Success!','User not found.');
        }

    }


    public function smartMrChecking(Request $request)
    {
        if ($request->has('api_token') && $request->has('mr_mobile_no')){
            $user = User::where('api_token', $request->input('api_token'))
                ->where('mobile_no', $request->input('mr_mobile_no'))
                ->where('status', 'verified')
                ->first();
            if($user){
                $user_id = $user->id;
                $userData = User::select('users.id as user_id','users.full_name','users.mobile_no','users.email','users.city','users.gender','details.firebase_id','company.company_name','experiences.position_name')
                    ->where('users.id','=',$user_id)
                    ->leftJoin('details','details.user_id','=','users.id')
                    ->leftJoin('experiences', function($join)use($user_id){
                        $join->on('users.id','=','experiences.user_id')
                            ->where('experiences.user_id',$user_id)
                            ->where(function($q){
                                $q->where('experiences.is_current','YES')
                                    ->orWhere('experiences.is_current','No');
                            });
                    })
                    ->leftJoin('company','company.id','=','experiences.company_id')
                    ->first();

                return $this->setReturnMessage($userData,'success','NotOK',200,'Success!','Authorized Access!');
            }
        }
        return $this->setReturnMessage((object)[],'error','NotOK',400,'Unauthorized!','Unauthorized Access!');
    }


    public function smartMrList(Request $request)
    {

        $mr_mobile_no = $request->input('mr_mobile_no');
        $mr_mobiles = json_decode($mr_mobile_no);

        $userData = User::select('users.id as user_id','users.full_name','users.mobile_no','users.email','users.city','users.gender','details.firebase_id','company.company_name','experiences.position_name');
        if(count($mr_mobiles) > 0) {
            $userData->whereIn('users.mobile_no', $mr_mobiles);
        }
        $result = $userData->leftJoin('details','details.user_id','=','users.id')
            ->leftJoin('experiences', function($join){
                $join->on('users.id','=','experiences.user_id')
                    ->where(function($q){
                        $q->where('experiences.is_current','YES')
                            ->orWhere('experiences.is_current','No');
                    });
            })
            ->leftJoin('company','company.id','=','experiences.company_id')
            ->groupBy('user_id')
            ->get();

        if(count($result)>0){
            return $this->setReturnMessage($result,'success','OK',200,'Success!','mr list data!');
        }else{
            return $this->setReturnMessage([],'warning','OK',400,'Warning!','mr not found!');
        }
    }



}