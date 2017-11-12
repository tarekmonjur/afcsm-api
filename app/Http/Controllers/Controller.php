<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected $app_version_name = '';
    protected $app_version_code = '';

    protected function setReturnMessage($data,$status,$type,$code,$title,$message){

        $sendData['data'] = $data;
        $sendData['status'] = $status;
        $sendData['statusType'] = $type;
        $sendData['code'] = $code;
        $sendData['title'] = $title;
        $sendData['message'] = $message;
        return response()->json($sendData,200);
    }


    protected function generateReferralCode($id, $first_name, $mobile_no)
    {
        $lastInsertId = $id;
        $docRowID = str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
        $doc_code = 'D'.$docRowID;

        $inputString = $first_name;
        $userMobile = $mobile_no;

        $testEmailAry = explode("@", $inputString);

        if(count($testEmailAry) > 1){

            $inputString = str_replace('-', '', $testEmailAry[0]);
            $targetName = preg_replace(['/[^A-Za-z0-9\-]/'], '', $inputString);
        }
        else{

            $inputString = str_replace(' ', '-', $inputString);
            $inputString = preg_replace(['/[^A-Za-z0-9\-]/'], '', $inputString);

            $inputStringAry = explode("-", $inputString);

            foreach($inputStringAry as $val){

                if(strlen($val) > 2){

                    $targetName = $val;
                    break;
                }
            }

            if(empty($targetName)){
                $targetName = "afcreferral";
            }
        }

        $name_prefix = $targetName;
        $row_id_prefix = substr(str_pad($lastInsertId,3,"0",STR_PAD_LEFT),-3);
        $mobile_prefix = str_shuffle(substr($userMobile,-3));
        $generate_referral_code = "$name_prefix$row_id_prefix$mobile_prefix";
        return $generate_referral_code;
    }


}
