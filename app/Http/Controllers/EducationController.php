<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 1:51 AM
 */

namespace App\Http\Controllers;

use App\Models\Education;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class EducationController extends Controller
{

    protected $auth;

    protected $education;

    public function __construct(Auth $auth, Education $education)
    {
        $this->auth = $auth->user();
        $this->education = $education;
    }


    public function index($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->education->where('user_id', $user_id)->get();
            return $this->setReturnMessage($data,'success','OK',200,'Success!','user educations.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Education not found.');
        }
    }


    public function create(Request $request)
    {
        $validator = $this->educationValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $this->education->user_id = $request->input('user_id');
            $this->education->education_title = $request->input('education_title');
            $this->education->education_board = $request->input('education_board');
            $this->education->institute_name = $request->input('institute_name');
            $this->education->result = $request->input('result');
            $this->education->pass_year = $request->input('pass_year');
            $this->education->save();
            $data = $this->education->find($this->education->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Education successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Education not added.');
        }
    }


    private function educationValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'education_title' => 'required|max:100',
            'education_board' => 'required|max:100',
            'institute_name' => 'required|max:100',
        ]);

        return $validator;
    }


    public function update(Request $request)
    {
        $validator = $this->educationValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $education = $this->education->find($request->input('id'));

            $education->education_title = $request->input('education_title');
            $education->education_board = $request->input('education_board');
            $education->institute_name = $request->input('institute_name');
            $education->result = $request->input('result');
            $education->pass_year = $request->input('pass_year');
            $education->save();
            $data = $this->education->find($education->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Education successfully updated!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Education not updated.');
        }
    }


    public function delete($id)
    {
        try{
            $this->education->find($id)->delete();
            return $this->setReturnMessage([],'success','OK',200,'Success!','Education successfully deleted!.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Education not deleted.');
        }
    }



}