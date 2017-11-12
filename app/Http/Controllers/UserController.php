<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/27/2017
 * Time: 12:44 PM
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Detail;
use App\Models\Contact;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;


class UserController extends Controller
{

    protected $auth;

    protected $user;

    public function __construct(Auth $auth, User $user)
    {
        $this->auth = $auth->user();
        $this->user = $user;
    }


    public function allUsers($user_type=null)
    {
        try{
            if($user_type != null){
                $users = User::where('user_type',$user_type)->get();
            }else{
                $users = User::get();
            }

            return $this->setReturnMessage($users,'success','OK',200,'Success!','User Data.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',500,'Error!','User data not found.');
        }
    }


    public function traceUser(Request $request)
    {
        try{
            $details = Detail::where('user_id', $this->auth->id)->first();
            if(count($details) <= 0){
                $details = new Detail;
                $details->user_id = $this->auth->id;
            }

            $details->mr_api_version = $request->input('mr_api_version');
            $details->sm_api_version = $request->input('sm_api_version');
            $details->sa_api_version = $request->input('sa_api_version');
            $details->sp_api_version = $request->input('sp_api_version');
            $details->sh_api_version = $request->input('sh_api_version');
            $details->uuid = $request->input('uuid');
            $details->firebase_id = $request->input('firebase_id');
            $details->app_version_name = $request->input('app_version_name');
            $details->app_version_code = $request->input('app_version_code');
            $details->firebase_id = $request->input('firebase_id');
            $details->save();

            return $this->setReturnMessage([],'success','OK',200,'Success!','Success.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',500,'Error!','not success.');
        }
    }


    public function userDetails($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->user->get_user_details_by_id($user_id);
            if($data){
                return $this->setReturnMessage($data,'success','OK',200,'Success!','User details.');
            }else{
                return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','User not found.');
            }

        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','user not found.');
        }
    }


    public function userUpdate(Request $request)
    {
        $validator = $this->userValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $user = User::find($request->input('id'));
            if(!$user){
                return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','User not found.');
            }

            $user->full_name = $request->input('full_name');
            $user->email = $request->input('email');
            //$user->mobile_no = $request->input('mobile_no');
            if($request->has('password')){
                $user->password = app('hash')->make($request->input('password'));
            }
            $user->gender = $request->input('gender');
            $user->city = $request->input('city');
            $user->save();


            $details = Detail::where('user_id', $request->input('id'))->first();
            if(!$details){
                $details = new Detail;
                $details->user_id = $request->input('id');
            }
            $details->national_id = $request->input('national_id');
            $details->area = $request->input('area');
            $details->dob = $request->input('dob');
            $details->present_address = $request->input('present_address');
            $details->permanent_address = $request->input('permanent_address');
            $details->save();

            $data = $this->user->get_user_details_by_id($user->id);

            return $this->setReturnMessage($data,'success','OK',200,'Success!','Profile successfully updated.');

        }catch(\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','Profile not updated.');
        }
    }


    private function userValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|max:45|min:2',
            'email' => 'nullable|email',
            'mobile_no' => 'nullable|max:11|min:11|regex:/01+[0-9]+$/',
//            'email' => 'nullable|email|unique:users,email,'.$request->input('id'),
//            'mobile_no' => 'nullable|max:11|min:11|regex:/01+[0-9]+$/|unique:users,mobile_no,'.$request->input('id'),
            'password' => 'nullable|max:20|min:6',
        ]);

        return $validator;
    }


    public function createContact(Request $request)
    {
        try{
            $contact = new Contact;
            $contact->user_id = $request->input('user_id');
            $contact->contact_mobile = $request->input('contact_mobile');
            $contact->contact_message = $request->input('contact_message');
            $contact->save();
            $data = $contact->find($contact->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Your message has been send successfully we will contact you soon.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','Your message not send.');
        }
    }


}