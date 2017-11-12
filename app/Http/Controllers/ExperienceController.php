<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 12:46 AM
 */

namespace App\Http\Controllers;

use App\Models\Experience;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class ExperienceController extends Controller
{

    protected $auth;

    protected $experience;

    public function __construct(Auth $auth, Experience $experience)
    {
        $this->auth = $auth->user();
        $this->experience = $experience;
    }


    public function index($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->experience->where('user_id', $user_id)->get();
            return $this->setReturnMessage($data,'success','OK',200,'Success!','user experiences.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','experiences not found.');
        }
    }


    public function create(Request $request)
    {
        $validator = $this->experienceValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            if($request->has('is_current') && $request->input('is_current')){
                $this->experience->is_current = $request->input('is_current');
                if($request->input('is_current') == 'Yes'){
                    $check_is_current = $this->experience->where('user_id', $request->input('user_id'))
                        ->where('is_current', 'Yes')
                        ->count();
                    if($check_is_current > 0){
                        return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Already have a current job position.');
                    }
                }
            }else{
                $this->experience->is_current = 'No';
            }

            $this->experience->user_id = $request->input('user_id');
            $this->experience->company_id = $request->input('company_id');
            $this->experience->position_name = $request->input('position_name');
            $this->experience->department_name = $request->input('department_name');
            $this->experience->start_date = $request->input('start_date');
            $this->experience->end_date = ($request->input('end_date'))?$request->input('end_date'):null;
            if($request->has('company_verify') && !empty($request->input('company_verify'))) {
                $this->experience->company_verify = $request->input('company_verify');
            }
            $this->experience->company_address = $request->input('company_address');
            $this->experience->save();

            $data = $this->experience->find($this->experience->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Experience successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Experience not added.');
        }
    }


    private function experienceValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'position_name' => 'required|max:50',
            'department_name' => 'required|max:50',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'nullable|date_format:"Y-m-d"',
            'company_id' => 'required',
            'company_address' => 'required',
        ]);

        return $validator;
    }


    public function update(Request $request)
    {
        $validator = $this->experienceValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $experience = $this->experience->find($request->input('id'));

            if($request->has('is_current') && $request->input('is_current')){
                $experience->is_current = $request->input('is_current');
                if($request->input('is_current') == 'Yes'){
                    $check_is_current = $this->experience->where('id','!=' ,$request->input('id'))
                        ->where('user_id', $experience->user_id)
                        ->where('is_current', 'Yes')
                        ->count();
                    if($check_is_current > 0){
                        return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Already have a current job position.');
                    }
                }
            }else{
                $experience->is_current = 'No';
            }

            $experience->company_id = $request->input('company_id');
            $experience->position_name = $request->input('position_name');
            $experience->department_name = $request->input('department_name');
            $experience->start_date = $request->input('start_date');
            $experience->end_date = $request->input('end_date');
            if($request->has('company_verify') && !empty($request->input('company_verify'))) {
                $this->experience->company_verify = $request->input('company_verify');
            }
            $experience->company_address = $request->input('company_address');
            $experience->save();
            $data = $this->experience->find($experience->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Experience successfully updated!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Experience not updated.');
        }
    }


    public function delete($id)
    {
        try{
            $this->experience->find($id)->delete();
            return $this->setReturnMessage([],'success','OK',200,'Success!','Experience successfully deleted!.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Experience not deleted.');
        }
    }



}