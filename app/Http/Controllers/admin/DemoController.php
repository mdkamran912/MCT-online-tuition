<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\democlasses;
use App\Models\status;
use App\Models\classes;
use App\Models\subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    public function index(){
        $demos = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','tutorregistrations.mobile as tutor_mobile','subjects.name as subject','subjects.id as subjectid','classes.name as class_name','statuses.name as currentstatus','studentregistrations.id as student_id','studentregistrations.name as student_name','studentregistrations.mobile as student_mobile')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('studentregistrations','studentregistrations.id','=','democlasses.student_id')
        ->orderby('democlasses.created_at','desc')
        // ->where('democlasses.student_id','=', session('userid')->id)
        ->paginate(100);
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::select('*')->get();
        return view('admin.demolist',get_defined_vars());
    }
    // search functionality
    public function demolistsearch(Request $request){

        $query = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','tutorregistrations.mobile as tutor_mobile','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus','studentregistrations.id as student_id','studentregistrations.name as student_name','studentregistrations.mobile as student_mobile')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('studentregistrations','studentregistrations.id','=','democlasses.student_id');

        // ->where('democlasses.student_id','=', session('userid')->id)
        // ->get();


        if ($request->student_name) {
            $query->where('studentregistrations.name','like', '%' . $request->student_name . '%');
        }
        if ($request->student_mobile) {
            $query->where('studentregistrations.mobile','like', '%' . $request->student_mobile . '%');
        }
        if ($request->tutor_name) {
            $query->where('tutorregistrations.name','like', '%' . $request->tutor_name . '%');
        }
        if ($request->tutor_mobile) {
            $query->where('tutorregistrations.mobile','like', '%' . $request->tutor_mobile . '%');
        }
        if ($request->class_name) {
            $query->where('classes.id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('democlasses.subject_id', $request->subject_name);
        }

        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '<=', $request->end_date);
        }
        if ($request->status) {
            $query->where('democlasses.status',$request->status );
        }
        $demos = $query->paginate(10);
        $type = "admin";
        $viewTable = view('admin.partials.democlass-search', compact('demos','type'))->render();
        $viewPagination = $demos->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }


    public function democancel(Request $request){
        $demo = democlasses::find($request->id);
        // echo $demo;
        $demo->status = "5";
        $res = $demo->save();
        if($res){

            return back()->with('success','Demo Cancelled Successfully');
        }
        else{
            return back()->with('fail','Something Went Wrong. Try Again Later');

        }
    }

    public function bookdemo(Request $request){
        $demo = new democlasses();
        $demo->student_id = session('userid')->id;
        $demo->tutor_id = $request->demotutorid;
        $demo->subject_id = $request->demosubjectid;
        // $demo->subject_id = $request->demosubjectid;
        $demo->slot_1 = $request->demoslotfirst;
        $demo->slot_2 = $request->demoslotsecond;
        $demo->slot_3 = $request->demoslotthird;
        // $demo->slot_confirmed = "";
        // $demo->slot_confirmed_at = "";
        // $demo->slot_confirmed_by = "";
        $demo->status = "1";

        $res = $demo->save();
        if($res){

            return back()->with('success','Demo Scheduled Successfully');
        }
        else{
            return back()->with('fail','Something Went Wrong. Try Again Later');

        }
    }

    public function demodetails($id){
        $demo = democlasses::find($id);
        return json_encode(array($demo));
    }

 
    public function demoupdate(Request $request){
        $request->validate([
            'slotupdate1'=>'required',
            'statusupdate'=>'required'
        ]);

        $dcnf = democlasses::find($request->demoupdateid);

        $dcnf->slot_1 = $request->slotupdate1;
        $dcnf->slot_2 = $request->slotupdate2;
        $dcnf->slot_3 = $request->slotupdate3;
        $dcnf->status = $request->statusupdate;

       $res= $dcnf->save();
       if($res){
        return back()->with('success','Slot updated successfully');
    }
    else{
        return back()->with('fail','Something went wrong. Please try again later');
    }
    }

    public function tutordemolist(){

        $demos = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','tutorregistrations.mobile as tutor_mobile','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus','classes.name as class_name','studentregistrations.id as student_id','studentregistrations.name as student_name','studentregistrations.mobile as student_mobile','classes.name as classname')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('studentregistrations','studentregistrations.id','=','democlasses.student_id')
        ->where('democlasses.tutor_id','=', session('userid')->id)->orderBy('democlasses.created_at', 'desc')
        ->paginate(10);

        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::select('*')->get();
        return view('tutor.demolist-new',get_defined_vars());
    }

    // search functionaity tutor
    public function tutorDemolistsearch(Request $request){


        $query = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','tutorregistrations.mobile as tutor_mobile','subjects.name as subject','classes.name as class_name','subjects.id as subjectid','statuses.name as currentstatus','studentregistrations.id as student_id','studentregistrations.name as student_name','studentregistrations.mobile as student_mobile')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('studentregistrations','studentregistrations.id','=','democlasses.student_id')
        ->where('democlasses.tutor_id','=', session('userid')->id);

        // ->where('democlasses.student_id','=', session('userid')->id)
        // ->get();


        if ($request->student_name) {
            $query->where('studentregistrations.name','like', '%' . $request->student_name . '%');
        }
        if ($request->student_mobile) {
            $query->where('studentregistrations.mobile','like', '%' . $request->student_mobile . '%');
        }
        // if ($request->tutor_name) {
        //     $query->where('tutorregistrations.name','like', '%' . $request->tutor_name . '%');
        // }
        // if ($request->tutor_mobile) {
        //     $query->where('tutorregistrations.mobile','like', '%' . $request->tutor_mobile . '%');
        // }
        if ($request->class_name) {
            $query->where('classes.id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('democlasses.subject_id', $request->subject_name);
        }

        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '<=', $request->end_date);
        }
        if ($request->status) {
            $query->where('democlasses.status',$request->status );
        }
        $demos = $query->paginate(10);
        $type = "tutor";
        $viewTable = view('admin.partials.democlass-search', compact('demos','type'))->render();
        $viewPagination = $demos->links()->render();
        
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::select('*')->get();
        return view('tutor.demolist-new',get_defined_vars());
    }


public function tutordemoupdate(Request $request){
    // echo $request->statusupdate;
    // dd();
$request->validate([
    'statusupdate'=>'required',
    'demoid'=>'required'
]);

$data = democlasses::find($request->demoid);
$data->status = $request->statusupdate;
$data->remarks = $request->remarks;

$res = $data->save();
if($res){
    return back()->with('success','Demo details updated successfully');
}
else{
    return back()->with('fail','Something went wrong. Please try again later');
}

}
public function demostatusupdate(Request $request)
{
    $data = democlasses::find($request->id);
    $data->status = '8';
    $res = $data->save();
    return json_encode(array('statusCode' => 200));
}
}
