<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 11:24 AM
 */

namespace App\Http\Controllers;

use App\Models\DoctorVisitHistory;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class DoctorVisitHistoryController extends Controller
{

    protected $auth;

    protected $doctorVisit;

    public function __construct(Auth $auth, DoctorVisitHistory $doctorVisit)
    {
        $this->auth = $auth->user();
        $this->doctorVisit = $doctorVisit;
    }


    public function index($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->doctorVisit->where('user_id', $user_id)->get();
            return $this->setReturnMessage($data,'success','OK',200,'Success!','User doctor visit.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Doctor visit not found.');
        }
    }


    public function create(Request $request)
    {
        $validator = $this->doctorVisitHistoryValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $this->doctorVisit->user_id = $request->input('user_id');
            $this->doctorVisit->doctor_name = $request->input('doctor_name');
            $this->doctorVisit->doctor_mobile = $request->input('doctor_mobile');
            $this->doctorVisit->doctor_designation = $request->input('doctor_designation');
            $this->doctorVisit->doctor_chamber = $request->input('doctor_chamber');
            $this->doctorVisit->visit_date = $request->input('visit_date');
            $this->doctorVisit->visit_duration = $request->input('visit_duration');
            $this->doctorVisit->visit_purpose = $request->input('visit_purpose');
            if($request->has('sp_verified')){
                $this->doctorVisit->sp_verified = $request->input('sp_verified');
            }
            $this->doctorVisit->remarks = $request->input('remarks');
            $this->doctorVisit->save();
            $data = $this->doctorVisit->find($this->doctorVisit->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Doctor visit successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Doctor visit not added.');
        }
    }


    private function doctorVisitHistoryValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_name' => 'required|max:45',
            'doctor_mobile' => 'required|max:13',
            'doctor_designation' => 'required|max:45',
            'doctor_chamber' => 'required|max:45',
            'visit_date' => 'required|date_format:"Y-m-d"',
        ]);

        return $validator;
    }


    public function update(Request $request)
    {
        $validator = $this->doctorVisitHistoryValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $doctorVisit = $this->doctorVisit->find($request->input('id'));

            if($doctorVisit->visit_date != $request->input('visit_date')){
                return $this->setReturnMessage([],'error','NotOK',400,'Error!',"You can't update previous data.");
            }

            $doctorVisit->doctor_name = $request->input('doctor_name');
            $doctorVisit->doctor_mobile = $request->input('doctor_mobile');
            $doctorVisit->doctor_designation = $request->input('doctor_designation');
            $doctorVisit->doctor_chamber = $request->input('doctor_chamber');
            $doctorVisit->visit_date = $request->input('visit_date');
            $doctorVisit->visit_duration = $request->input('visit_duration');
            $doctorVisit->visit_purpose = $request->input('visit_purpose');
            if($request->has('sp_verified')) {
                $doctorVisit->sp_verified = $request->input('sp_verified');
            }
            $doctorVisit->remarks = $request->input('remarks');
            $doctorVisit->save();

            $data = $this->doctorVisit->find($doctorVisit->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Doctor visit successfully updated!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Doctor visit not updated.');
        }
    }


    public function delete($id)
    {
        try{
            $this->doctorVisit->find($id)->delete();
            return $this->setReturnMessage([],'success','OK',200,'Success!','Doctor visit successfully deleted!.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Doctor visit not deleted.');
        }
    }



}