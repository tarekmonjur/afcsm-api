<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 5:43 PM
 */

namespace App\Http\Controllers;

use App\Models\Prescription;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class PrescriptionController extends Controller
{

    protected $auth;

    protected $prescription;

    public function __construct(Auth $auth, Prescription $prescription)
    {
        $this->auth = $auth->user();
        $this->prescription = $prescription;
    }


    public function index($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->prescription->where('user_id', $user_id)->get();
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Prescriptions data.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Prescriptions not found.');
        }
    }


    public function create(Request $request)
    {
        $validator = $this->prescriptionValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $this->prescription->user_id = $request->input('user_id');

            $prescription_image = base64_decode($request->input('prescription'));
            $upload_path = 'files/prescriptions/'.time().'.png';
            file_put_contents($upload_path, $prescription_image);
            $this->prescription->prescription = $upload_path;
            $this->prescription->patient_name = $request->input('patient_name');
            $this->prescription->patient_mobile = $request->input('patient_mobile');
            $this->prescription->date = ($request->has('date'))?$request->input('date'):date('Y-m-d');
            $this->prescription->save();
            $data = $this->prescription->find($this->prescription->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Prescription successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Prescription not added.');
        }
    }


    private function prescriptionValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'prescription' => 'required',
            'patient_name' => 'required|max:50',
            'patient_mobile' => 'required|max:11|min:11|regex:/01+[0-9]+$/',
            'date' => 'nullable|date',
        ]);

        return $validator;
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required|max:50',
            'patient_mobile' => 'required|max:11|min:11|regex:/01+[0-9]+$/',
            'date' => 'nullable|date',
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
            $prescription = $this->prescription->find($request->input('id'));

            if($request->has('prescription')) {
                $prescription_image = base64_decode($request->input('prescription'));
                $upload_path = 'files/prescriptions/' . time() . '.png';
                file_put_contents($upload_path, $prescription_image);
                $this->prescription->prescription = $upload_path;
            }

            $prescription->patient_name = $request->input('patient_name');
            $prescription->patient_mobile = $request->input('patient_mobile');
            if($request->has('date')){
                $prescription->date = $request->input('date');
            }
            $prescription->save();
            $data = $this->prescription->find($prescription->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Prescription successfully updated!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Prescription not updated.');
        }
    }


    public function delete($id)
    {
        try{
            $this->prescription->find($id)->delete();
            return $this->setReturnMessage([],'success','OK',200,'Success!','Prescription successfully deleted!.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Prescription not deleted.');
        }
    }



}