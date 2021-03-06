<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 11/12/2017
 * Time: 1:44 AM
 */

namespace App\Http\Controllers;

use App\Models\DoctorVisitHistory;

use Illuminate\Support\Facades\DB;
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


    public function index($mr_mobile_no=null, $doctor_mobile_no=null)
    {
        try{
            $doctors = $this->doctorVisit->get_doctor($mr_mobile_no, $doctor_mobile_no);
            $data = [];
            foreach($doctors as $doctor){
                $doctorObject = (object)[];
                $doctorObject->doctorMobileNo = $doctor->doctor_mobile_no;
                $doctorObject->doctorFullname = $doctor->doctor_fullname;
                $doctorObject->doctorDesignation = $doctor->doctor_designation;
                $doctorObject->doctorEducation = $doctor->doctor_education;
                $doctorObject->doctorVisitedChamber = [];

                $doctor_chambers = $this->doctorVisit->get_chamber($mr_mobile_no, $doctor->doctor_mobile_no);
                foreach($doctor_chambers as $doctor_chamber){
                    $doctorVisitedChamber = (object)[];
                    $doctorVisitedChamber->doctorChamberId = $doctor_chamber->doctor_chamber_id;
                    $doctorVisitedChamber->doctorChamberName = $doctor_chamber->doctor_chamber_name;
                    $doctorVisitedChamber->doctorChamberAddress = $doctor_chamber->doctor_chamber_address;
                    $doctorVisitedChamber->chamberVisitHistory = [];

                    $doctor_visits = $this->doctorVisit->where('mr_mobile_no', $mr_mobile_no)->where('doctor_mobile_no', $doctor_chamber->doctor_mobile_no)->where('doctor_chamber_id', $doctor_chamber->doctor_chamber_id)->get();
                    foreach($doctor_visits as $doctor_visit){
                        $chamberVisitHistory = (object)[];
                        $chamberVisitHistory->id = $doctor_visit->id;
                        $chamberVisitHistory->smVisitStart = $doctor_visit->visit_start;
                        $chamberVisitHistory->smVisitEnd = $doctor_visit->visit_end;
                        $chamberVisitHistory->remarks = $doctor_visit->remarks;
                        $chamberVisitHistory->totalVisitTime = $doctor_visit->total_visit_time;
                        $doctorVisitedChamber->chamberVisitHistory[] = $chamberVisitHistory;
                    }
                    $doctorObject->doctorVisitedChamber[] = $doctorVisitedChamber;
                }
                $data[] = $doctorObject;
            }

            return $this->setReturnMessage($data,'success','OK',200,'Success!','User doctor visit.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Doctor visit not found.');
        }
    }


    public function doctorVisitSearch(Request $request)
    {
        try{
            $mr_mobile_no = $request->input('mr_mobile_no');
            $doctor_mobile_no = $request->input('doctor_mobile_no');
            $date = $request->input('date');

            $data = $this->doctorVisit->get_doctor_visit_history_search($mr_mobile_no, $doctor_mobile_no, $date);
            return $this->setReturnMessage($data,'success','OK',200,'Success!','User doctor visit.');
        }catch (\Exception $e){
            return $this->setReturnMessage([],'error','NotOK',400,'Error!','Doctor visit not found.');
        }
    }


    private function doctorVisitHistoryValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'doctorFullname' => 'required|max:45',
            'smMobileNo' => 'required|max:11',
            'doctorMobileNo' => 'required|max:11',
            'smVisitStart' => 'required',
            'smVisitEnd' => 'required',
        ]);

        return $validator;
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
            $doctorVisit = new DoctorVisitHistory;
            if($request->has('user_id')) {
                $doctorVisit->user_id = $request->input('user_id');
            }else{
                $doctorVisit->user_id = $this->auth->id;
            }
            $doctorVisit->mr_mobile_no = $request->input('smMobileNo');
            $doctorVisit->doctor_mobile_no = $request->input('doctorMobileNo');
            $doctorVisit->doctor_fullname = $request->input('doctorFullname');
            $doctorVisit->doctor_designation = $request->input('doctorDesignation');
            $doctorVisit->doctor_education = $request->input('doctorEducation');
            $doctorVisit->doctor_chamber_id = $request->input('doctorChamberId');
            $doctorVisit->doctor_chamber_name = $request->input('doctorChamberName');
            $doctorVisit->doctor_chamber_address = $request->input('doctorChamberAddress');
            $doctorVisit->remarks = $request->input('remarks');
            if($request->has('doctorChamberGeo')){
                $doctorChamberGeo = json_decode($request->input('doctorChamberGeo'));
                if(is_object($doctorChamberGeo)){
                    $doctorVisit->doctor_chamber_lat = $doctorChamberGeo->lat;
                    $doctorVisit->doctor_chamber_long = $doctorChamberGeo->long;
                }
            }
            $doctorVisit->visit_start = $request->input('smVisitStart');
            $doctorVisit->visit_end = $request->input('smVisitEnd');
            $doctorVisit->total_visit_time = date('H:i:s', strtotime($doctorVisit->visit_end) - strtotime($doctorVisit->visit_start));
            $doctorVisit->save();

            $doctorVisitGeos = json_decode($request->input('smVisitingGeo'));
            if(is_array($doctorVisitGeos)) {
                $doctorVisitGeoData = [];
                foreach ($doctorVisitGeos as $doctorVisitGeo) {
                    $doctorVisitGeoData[] = ['doctor_visit_history_id' => $doctorVisit->id, 'visit_lat' => $doctorVisitGeo->lat, 'visit_long' => $doctorVisitGeo->long,];
                }
                if (count($doctorVisitGeoData) > 0) {
                    DB::table('doctor_visit_history_geo')->insert($doctorVisitGeoData);
                }
            }

            $data = (object)[];
            $data->id = $doctorVisit->id;
            $data->smMobileNo = $doctorVisit->mr_mobile_no;
            $data->doctorMobileNo = $doctorVisit->doctor_mobile_no;
            $data->doctorFullname = $doctorVisit->doctor_fullname;
            $data->doctorDesignation = $doctorVisit->doctor_designation;
            $data->doctorEducation = $doctorVisit->doctor_education;
            $data->doctorChamberId = $doctorVisit->doctor_chamber_id;
            $data->doctorChamberName = $doctorVisit->doctor_chamber_name;
            $data->doctorChamberAddress = $doctorVisit->doctor_chamber_address;
            $data->smVisitStart = $doctorVisit->visit_start;
            $data->smVisitEnd = $doctorVisit->visit_end;
            $data->remarks = $doctorVisit->remarks;
            $data->totalVisitTime = $doctorVisit->total_visit_time;

            return $this->setReturnMessage($data,'success','OK',200,'Success!','Doctor visit successfully added!.');
        }catch (\Exception $e){
            return $this->setReturnMessage((object)[],'error','NotOK',400,'Error!','Doctor visit not added.');
        }
    }

}