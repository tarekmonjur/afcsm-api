<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 10/19/2017
 * Time: 1:40 PM
 */

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class WebController extends Controller
{
    protected $auth;

    protected $experience;

    public function __construct(Auth $auth, Experience $experience)
    {
        $this->auth = $auth->user();
        $this->experience = $experience;

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


    public function mrVerify(Request $request)
    {
        $result = $this->experience->where('id', $request->input('experience_id'))
            ->where('company_id', $request->input('company_id'))
            ->update([
                'company_verify' => 'verified'
            ]);

        if($result){
            $setVerifyCompany = $this->httpClient->sendRequest('POST', $this->smapi.'smart-marketeer-set-verified-company-id', [
                'sm_mobile_no' => $request->input('mobile_no'),
                'sm_company_id' => $request->input('company_id'),
            ]);
            return $this->setReturnMessage([],'success','OK',200,'Success!','MR successfully verified.');
        }else{
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','MR not verified.');
        }
    }

}