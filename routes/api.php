<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("uploadfile",'API\FileController@uploadfile');
Route::post("research/save_status",'API\ResearchController@save_status');
Route::get("person/author/{id}",'API\PersonController@get_author');
Route::get("person/get_authors",'API\PersonController@get_authors');
Route::get("person/roles",'API\PersonController@roles');
Route::get("person/author_activity/{id}",'API\PersonController@get_author_Activity');
Route::get("research/public_list",'API\ResearchController@public_list');
Route::get("research/public_list_by_year",'API\ResearchController@public_list_by_year');
Route::get("research/list_status",'API\ResearchController@list_status');
Route::get("research/py_by_author",'API\ResearchController@py_by_author');
Route::get("outcome/by",'API\OutcomeController@out_by');
Route::get("outcome/r_for_unit",'API\OutcomeController@r_for_unit');
Route::get("document",'API\DocumentController@index');
Route::get("event",'API\EventController@index');
// Route::get("outcome/by_author",'API\OutcomeController@out_by_author');
// Route::get("outcome/by_college",'API\OutcomeController@out_by_college');
// Route::get("outcome/in_journal",'API\OutcomeController@out_in_journal');
Route::get("research/py_by_state",'API\ResearchController@py_by_state');
Route::get("research/py_by_college",'API\ResearchController@py_by_college');
Route::get("research/sunedu",'API\ResearchController@sunedu_list');
Route::get("research/constancy",'API\ResearchController@constancy_by_author');
Route::get("research/certified",'API\ResearchController@certified_by_author');
Route::get("plan/list_lines",'API\PlanController@list_lines');
Route::post("plan/save_line_actives",'API\PlanController@save_line_actives');
Route::get("category/list_programs_and_lines",'API\CategoryController@list_programs_and_lines');
Route::get("outcome/incentives",'API\OutcomeController@incentives');
Route::get("research/py_by_period",'API\ResearchController@py_by_period');
Route::post("outcome/approved",'API\OutcomeController@save_pending_outcomes');
Route::post("outcome/reviewed",'API\OutcomeController@save_reviewed_pending_outcomes');
Route::get("user/loginbygoogle",'API\UserController@loginbygoogle');
Route::get("user/loginbyoffice",'API\UserController@loginbyoffice');
Route::get("person/incentives_list",'API\PersonController@incentives_list');
Route::get("category/{id}/members",'API\CategoryController@get_members');
Route::get("master/list_states",'API\MasterController@list_states');
//Route::middleware('auth.api')->get('user/access', 'API\UserController@list_access');   
Route::apiResources([
    'person' => 'API\PersonController',
    'user' => 'API\UserController',
    'file' => 'API\FileController',
    'document' => 'API\DocumentController',
    'event' => 'API\EventController',
    'research'=>'API\ResearchController',
    'category'=>'API\CategoryController',
    'organization'=>'API\OrganizationController',
    'college'=>'API\collegeController',
    'journal'=>'API\JournalController',
    'renacyt'=>'API\RenacytController',
    'plan'=>'API\PlanController',
    'author'=>'API\AuthorController',
    'outcome'=>'API\OutcomeController',
    ]);


