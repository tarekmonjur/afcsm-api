<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function(){
//    $visit_date = date_create('2017/01/01');
//    echo date_format($visit_date, 'Y-m-d');
//    echo json_encode([0 => '456789', 1=>'456789']);
//});

//$router->get('/dbseed', function () use ($router) {
//    return \Illuminate\Support\Facades\Artisan::call('db:seed');
//});

$router->get('/get-pin/{mobile_no}', 'CommonController@getPin');


$router->group(['prefix' => env('APP_API_VERSION_0')], function() use ($router){
    $router->get('/users[/{user_type}]', 'UserController@allUsers');
    $router->post('/user', 'AuthController@userRegister');
    $router->post('/company', 'AuthController@companyRegister');
    $router->post('/login', 'AuthController@userLogin');
    $router->post('/user-verify', 'AuthController@verifyRegistrationPin');
    $router->post('/resend-user-verify', 'AuthController@resendVerifyPin');
    $router->post('/password-reset', 'AuthController@passwordReset');
    $router->post('/reset-password', 'AuthController@resetPassword');

    $router->get('/get-company-list', 'CommonController@getCompanyList');
    $router->get('/company-list', 'CommonController@companyList');
    $router->post('/search-mr', 'ReportController@searchMr');

    $router->post('/user-checking', 'AuthController@userChecking');
    $router->post('/smart-mr-auth-check', 'AuthController@smartMrChecking');
    $router->post('/smart-mr-list', 'AuthController@smartMrList');

});


$router->group(['prefix' => env('APP_API_VERSION_0')], function() use ($router){
    $router->get('/logout', ['middleware' => 'auth:arr', 'uses' => 'AuthController@userLogout']);

    $router->get('/user[/{user_id}]', ['middleware' => 'auth:obj', 'uses' => 'UserController@userDetails']);
    $router->post('/user-update', ['middleware' => 'auth:obj', 'uses' => 'UserController@userUpdate']);
    $router->post('/trace-user', ['middleware' => 'auth:obj', 'uses' => 'UserController@traceUser']);

    $router->get('/experiences[/{user_id}]', ['middleware' => 'auth:arr', 'uses' => 'ExperienceController@index']);
    $router->post('/experiences', ['middleware' => 'auth:obj', 'uses' => 'ExperienceController@create']);
    $router->post('/experiences-update', ['middleware' => 'auth:obj', 'uses' => 'ExperienceController@update']);
    $router->delete('/experiences/{id}', ['middleware' => 'auth:arr', 'uses' => 'ExperienceController@delete']);

    $router->get('/educations[/{user_id}]', ['middleware' => 'auth:arr', 'uses' => 'EducationController@index']);
    $router->post('/educations', ['middleware' => 'auth:obj', 'uses' => 'EducationController@create']);
    $router->post('/educations-update', ['middleware' => 'auth:obj', 'uses' => 'EducationController@update']);
    $router->delete('/educations/{id}', ['middleware' => 'auth:arr', 'uses' => 'EducationController@delete']);

    $router->get('/product-experiences[/{user_id}]', ['middleware' => 'auth:obj', 'uses' => 'ProductExperienceController@index']);
    $router->post('/product-experiences', ['middleware' => 'auth:obj', 'uses' => 'ProductExperienceController@create']);
    $router->post('/product-experiences-update', ['middleware' => 'auth:obj', 'uses' => 'ProductExperienceController@update']);
    $router->delete('/product-experiences/{id}', ['middleware' => 'auth:arr', 'uses' => 'ProductExperienceController@delete']);

    $router->get('/doctor-visits/{mr_mobile_no}[/{doctor_mobile_no}]', ['middleware' => 'auth:arr', 'uses' => 'DoctorVisitHistoryController@index']);
    $router->post('/doctor-visits-search', ['middleware' => 'auth:arr', 'uses' => 'DoctorVisitHistoryController@doctorVisitSearch']);
    $router->post('/doctor-visits', ['middleware' => 'auth:obj', 'uses' => 'DoctorVisitHistoryController@create']);
    $router->post('/doctor-visits-update', ['middleware' => 'auth:obj', 'uses' => 'DoctorVisitHistoryController@update']);
    $router->delete('/doctor-visits/{id}', ['middleware' => 'auth:arr', 'uses' => 'DoctorVisitHistoryController@delete']);

//    $router->get('/prescriptions[/{user_id}]', ['middleware' => 'auth:arr', 'uses' => 'PrescriptionController@index']);
//    $router->post('/prescriptions', ['middleware' => 'auth:obj', 'uses' => 'PrescriptionController@create']);
//    $router->post('/prescriptions-update', ['middleware' => 'auth:obj', 'uses' => 'PrescriptionController@update']);
//    $router->delete('/prescriptions/{id}', ['middleware' => 'auth:arr', 'uses' => 'PrescriptionController@delete']);

    $router->post('/contact', ['middleware' => 'auth:obj', 'uses' => 'UserController@createContact']);
    $router->get('/faq-que-ans', ['middleware' => 'auth:arr', 'uses' => 'CommonController@getFrequentQueAns']);

    // Report Controller
//    $router->get('/referral-data/{user_type}/{referral_code}', ['middleware' => 'auth:arr', 'uses' => 'ReportController@referralData']);
    $router->post('/my-mr', ['middleware' => 'auth:arr', 'uses' => 'ReportController@myMrList']);


    $router->post('/mr-verify', ['middleware' => 'auth:arr', 'uses' => 'WebController@mrVerify']);


});