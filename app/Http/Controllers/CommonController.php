<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 10/8/2017
 * Time: 11:54 AM
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;
//use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{

    protected  $client;


    public function __construct()
    {
        $this->client = new Client();
    }


    public function sendRequest($method, $url, $query=[])
    {
        $result = $this->client->request($method, $url, [
            'form_params' => $query,
            'allow_redirects' => false,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);

        $result->getStatusCode();
        $body = $result->getBody();
        $content = $body->getContents();
        $data = json_decode($body);
        return $data;
    }


    public function getCompanyList()
    {
        try{
            $company_list = DB::table('company')->get();
            return $this->setReturnMessage($company_list,'success','OK',200,'Success!','company list data.');
        }catch(\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','internal error.');
        }
    }


    public function companyList(Request $request)
    {
        try{
            if($request->has('app_version_code')) {
                if ($this->app_version_code !=null || !empty($this->app_version_code)) {
                    if($this->app_version_code < $request->input('app_version_code')) {
                        return $this->setReturnMessage((object)[], 'error', 'NotOK', 420, 'Error!', 'App version update.');
                    }
                }
            }
            $data['company_list'] = DB::table('company')->get();
           // $data['order_delivery_city'] = ["Dhaka","Chittagong","Khulna"];
            return $this->setReturnMessage($data,'success','OK',200,'Success!','company list data.');
        }catch(\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','internal error.');
        }
    }


    public function getFrequentQueAns()
    {
        try{
            $faqs = DB::table('faqs')->select('id as faq_row', 'faq_qus', 'faq_ans', 'created_at as date')->get();

            // $objectData1 = (object)[];
            // $objectData1->faq_row = 1;
            // $objectData1->faq_qus = "What is Smart Marketeer?";
            // $objectData1->faq_ans = "mamshad";
            // $objectData1->faq_created_at = date('Y-m-d');
            // $data[0] = $objectData1;

            // $objectData2 = (object)[];
            // $objectData2->faq_row = 2;
            // $objectData2->faq_qus = "What is your city?";
            // $objectData2->faq_ans = "Dhaka";
            // $objectData2->faq_created_at = date('Y-m-d');
            // $data[1] = $objectData2;

            // $objectData3 = (object)[];
            // $objectData3->faq_row = 3;
            // $objectData3->faq_qus = "What is your school?";
            // $objectData3->faq_ans = "Wills";
            // $objectData3->faq_created_at = date('Y-m-d');
            // $data[2] = $objectData3;

            return $this->setReturnMessage($faqs,'success','OK',200,'Success!','Frequent Ask Questions.');
        }catch(\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',500,'Error!','internal error.');
        }
    }


    public function getPin($mobile_no)
    {
        return \App\Models\User::where('mobile_no', $mobile_no)->pluck('pin');
    }

}