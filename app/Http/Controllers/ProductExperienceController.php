<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 11:10 AM
 */

namespace App\Http\Controllers;

use App\Models\ProductExperience;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class ProductExperienceController extends Controller
{

    protected $auth;

    protected $productExperience;

    public function __construct(Auth $auth, ProductExperience $productExperience)
    {
        $this->auth = $auth->user();
        $this->productExperience = $productExperience;
    }


    public function index($user_id=null)
    {
        try{
            $user_id = ($user_id != null)?$user_id:$this->auth->id;
            $data = $this->productExperience->where('user_id', $user_id)->get();
            return $this->setReturnMessage($data,'success','OK',200,'Success!','User product experiences.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Product Experiences not found.');
        }
    }


    public function create(Request $request)
    {
        $validator = $this->ProductExperienceValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $this->productExperience->user_id = $request->input('user_id');
            $this->productExperience->brand_name = $request->input('brand_name');
            $this->productExperience->generic_name = $request->input('generic_name');
            $this->productExperience->company_name = $request->input('company_name');
            if($request->input('status')){
                $this->productExperience->status = $request->input('status');
            }
            $this->productExperience->save();
            $data = $this->productExperience->find($this->productExperience->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Product Experiences successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Product Experiences not added.');
        }
    }


    private function ProductExperienceValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|max:45',
            'generic_name' => 'required|max:45',
            'company_name' => 'required|max:45',
        ]);

        return $validator;
    }


    public function update(Request $request)
    {
        $validator = $this->ProductExperienceValidation($request);
        if($validator->fails()){
            $message = '';
            foreach($validator->errors()->messages() as $errors){
                $message = $errors[0];
                break;
            }
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Required!',$message);
        }

        try{
            $productExperience = $this->productExperience->find($request->input('id'));

            $productExperience->brand_name = $request->input('brand_name');
            $productExperience->generic_name = $request->input('generic_name');
            $productExperience->company_name = $request->input('company_name');
            if($request->input('status')){
                $productExperience->status = $request->input('status');
            }
            $productExperience->save();
            $data = $this->productExperience->find($productExperience->id);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','Product Experiences successfully updated!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Product Experiences not updated.');
        }
    }


    public function delete($id)
    {
        try{
            $this->productExperience->find($id)->delete();
            return $this->setReturnMessage([],'success','OK',200,'Success!','Product Experiences successfully deleted!.');
        }catch(\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Product Experiences not deleted.');
        }
    }



}