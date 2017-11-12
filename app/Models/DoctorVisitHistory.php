<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 9/28/2017
 * Time: 11:25 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorVisitHistory extends Model
{

    public function get_doctor_visit_history($mr_mobile_no, $doctor_mobile_no)
    {
        $result =  $this->select('id','mr_mobile_no as smMobileNo','doctor_mobile_no as doctorMobileNo',
            'doctor_fullname as doctorFullname','doctor_designation as doctorDesignation','doctor_education as doctorEducation',
            'doctor_chamber_name as doctorChamberName','doctor_chamber_address as doctorChamberAddress',
            'visit_start as smVisitStart','visit_end as smVisitEnd','remarks', 'total_visit_time as totalVisitTime');
//            app('db')->raw("CONCAT(TIMESTAMPDIFF(HOUR, visit_start, visit_end),':',TIMESTAMPDIFF(MINUTE, visit_start, visit_end),':00') as totalVisitTime"))
        if(!empty($mr_mobile_no) && $mr_mobile_no !=null){
            $result->where('mr_mobile_no', $mr_mobile_no);
        }
        if(!empty($doctor_mobile_no) && $doctor_mobile_no !=null){
            $result->where('doctor_mobile_no', $doctor_mobile_no);
        }
        $data = $result->get();
        return $data;
    }

}