<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\tutorregistration;
use App\Models\studentprofile;
use App\Models\status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\RealTimeMessage;
use App\Models\Notification;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
class DemoListController extends Controller
{
    public function index(){

        $demos = democlasses::select('*','democlasses.id as demo_id','classes.name as class_name','tutorregistrations.name as tutor','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->where('democlasses.student_id','=', session('userid')->id)
        ->orderBy('democlasses.created_at', 'desc')
        ->paginate(100);

        $subjects = subjects::where('is_active',1)->where('class_id',session('userid')->class_id)->get();
        $statuses = status::select('*')->get();
        $tutors = tutorregistration::select('*')->get();
        return view('student.demolist',get_defined_vars());
    }
    public function parentindex(){

        $demos = democlasses::select('*','democlasses.id as demo_id','classes.name as class_name','tutorregistrations.name as tutor','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->where('democlasses.student_id','=', session('userid')->id)
        ->orderBy('democlasses.created_at', 'desc')
        ->paginate(10);
        $subjects = subjects::where('is_active',1)->where('class_id',session('userid')->class_id)->get();
        $statuses = status::select('*')->get();
        $tutors = tutorregistration::select('*')->get();
        return view('parent.demolist',get_defined_vars());
    }
    public function demolistSearch(Request $request){
        // return $request->all();
        $query = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','classes.name as class_name','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->where('democlasses.student_id','=', session('userid')->id);


        if ($request->tutor) {
            $query->where('democlasses.tutor_id', $request->tutor);
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
        $type = "student";
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
             //////////////// Here I need to pass notification into db
             $notificationdata = new Notification();
             $notificationdata->alert_type = 2;
             $notificationdata->notification = 'Trial Class Cancelled By '.session('userid')->name;
             $notificationdata->initiator_id = session('userid')->id;
             $notificationdata->initiator_role = session('userid')->role_id;
             $notificationdata->event_id = $demo->id;
             // Sending to admin
             // if($request->receiver_role_id == 1){
                 $notificationdata->show_to_admin = 1;
                 // $notificationdata->show_to_admin_id = $request->receiver_id;
                 $notificationdata->show_to_all_admin = 1;
             // }
             // Sending to tutor
             // if($request->receiver_role_id == 2){
                 $notificationdata->show_to_tutor = 1;
                 $notificationdata->show_to_tutor_id = $demo->tutor_id;
                 // $notificationdata->show_to_all_tutor = 0;
             // }
             // Sending to student
             // if($request->receiver_role_id == 3){
             //     $notificationdata->show_to_student = 1;
             //     $notificationdata->show_to_student_id = $request->receiver_id;
             //     // $notificationdata->show_to_all_student = 0;
             // }
             // // Sending to parent
             // if($request->receiver_role_id == 3){
             //     $notificationdata->show_to_parent = 1;
             //     $notificationdata->show_to_parent_id = $request->receiver_id;
             //     // $notificationdata->show_to_all_parent = 0;
             // }
             $notificationdata->read_status = 0;

             $notified = $notificationdata->save();
             broadcast(new RealTimeMessage('$notification'));

            return back()->with('success','Demo Cancelled Successfully');
        }
        else{
            return back()->with('fail','Something Went Wrong. Try Again Later');

        }
    }

    public function bookdemo(Request $request){

        // $studentprofile = studentprofile::select('*')->where('student_id',session('userid')->id)->first();
        $profchk = studentprofile::select('email')->where('student_id',session('userid')->id)->first();
        $tutorname = tutorregistration::select('*')->where('id',$request->demotutorid)->first();

        if (!$profchk || $profchk->email === null) {
            return back()->with('fail','Please update your profile first. Visit your profile section to update');
        }
        // dd($request->message);
        $demo = new democlasses();
        $demo->student_id = session('userid')->id;
        $demo->tutor_id = $request->demotutorid;
        $demo->subject_id = $request->demosubjectid;
        // $demo->subject_id = $request->demosubjectid;
        $demo->slot_1 = $request->demoslotfirst;
        $demo->slot_2 = $request->demoslotsecond;
        $demo->slot_3 = $request->demoslotthird;
        $demo->remarks = $request->message;
        // $demo->slot_confirmed = "";
        // $demo->slot_confirmed_at = "";
        // $demo->slot_confirmed_by = "";
        $demo->status = "1";

        $res = $demo->save();

        // Send welcome mail
        $details = [
        'name' => session('userid')->name,
        'slot_1' => $request->demoslotfirst,
        'slot_2' => $request->demoslotsecond,
        'slot_3' => $request->demoslotthird,
        'tutor_name' => $tutorname->name,
        'mailtype' => 2
        ];

        Mail::to(session('userid')->email)->send(new SendMail($details));
        // Send welcome mail ends here ..

        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 2;
            $notificationdata->notification = 'New Trial Class Scheduled By '.session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $demo->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
                $notificationdata->show_to_admin = 1;
                // $notificationdata->show_to_admin_id = $request->receiver_id;
                $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
                $notificationdata->show_to_tutor = 1;
                $notificationdata->show_to_tutor_id = $request->demotutorid;
                // $notificationdata->show_to_all_tutor = 0;
            // }
            // Sending to student
            // if($request->receiver_role_id == 3){
            //     $notificationdata->show_to_student = 1;
            //     $notificationdata->show_to_student_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_student = 0;
            // }
            // // Sending to parent
            // if($request->receiver_role_id == 3){
            //     $notificationdata->show_to_parent = 1;
            //     $notificationdata->show_to_parent_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_parent = 0;
            // }
            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();
            broadcast(new RealTimeMessage('$notification'));

            return redirect()->to('student/searchtutor')->with('success', 'Demo Scheduled Successfully');
        } else {
            return redirect()->to('student/searchtutor')->with('fail', 'Something Went Wrong. Try Again Later');
        }
    }
}

// democlasses
