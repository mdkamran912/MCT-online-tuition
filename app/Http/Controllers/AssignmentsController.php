<?php

namespace App\Http\Controllers;

use App\Models\StudentAssignmentList;
use App\Models\StudentAssignments;
use App\Models\classes;
use App\Models\subjects;
use App\Models\topics;
use App\Models\tutorregistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\RealTimeMessage;
use App\Models\Notification;
use App\Models\studentprofile;
use App\Models\studentregistration;

class AssignmentsController extends Controller
{
    public function adminindex()
    {
        $data = StudentAssignmentList::select('*', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.name as assignment_name', 'student_assignment_lists.student_id as assigned_to', 'student_assignment_lists.is_active as assignment_status', 'classes.name as class_name', 'subjects.name as subject_name', 'topics.name as topic_name', 'tutorregistrations.name as tutor_name', 'tutorregistrations.id as tutor_id')
            ->join('classes', 'classes.id', 'student_assignment_lists.class_id')
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('topics', 'topics.id', 'student_assignment_lists.topic_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'student_assignment_lists.assigned_by')
            ->paginate(10);
        $classes = classes::where('is_active',1)->get();
        $subjects = subjects::where('is_active',1)->get();
        $topics = topics::where('is_active',1)->get();
        $tutors = tutorregistration::all();
        return view('admin.assignmentslist', get_defined_vars());
    }
    // search functionality
    public function assignmentsSearch(Request $request)
    {
        // return $request->all();
        $query = StudentAssignmentList::select('*', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.name as assignment_name', 'student_assignment_lists.student_id as assigned_to', 'student_assignment_lists.is_active as assignment_status', 'classes.name as class_name', 'subjects.name as subject_name', 'topics.name as topic_name', 'tutorregistrations.name as tutor_name', 'tutorregistrations.id as tutor_id')
            ->join('classes', 'classes.id', 'student_assignment_lists.class_id')
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('topics', 'topics.id', 'student_assignment_lists.topic_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'student_assignment_lists.assigned_by');

        if ($request->assaignment_name) {
            $query->where('student_assignment_lists.name','like', '%' . $request->assaignment_name . '%');
        }
        if ($request->assigned_by) {
            $query->where('student_assignment_lists.assigned_by',$request->assigned_by);
        }
        if ($request->class_name) {
            $query->where('student_assignment_lists.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('student_assignment_lists.subject_id', $request->subject_name);
        }
        if ($request->topic_name) {
            $query->where('student_assignment_lists.topic_id', $request->topic_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(student_assignment_lists.assignment_start_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(student_assignment_lists.assignment_end_date)'), '<=', $request->end_date);
        }
        if ($request->status_field) {
            if($request->status_field=='2'){
                $request->status_field = '0';
            }
            $query->where('student_assignment_lists.is_active',$request->status_field);
        }
        $data = $query->paginate(10);
        $type = 'assignments';
        $viewTable = view('admin.partials.students-tutor-search', compact('data','type'))->render();
        $viewPagination = $data->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);


    }



    public function status(Request $request)
    {
        // echo 'Test';
        // echo $request->id;
        // echo $request->status;
        // dd();
        $data = StudentAssignmentList::find($request->id);
        if ($request->status == 1) {
            $status = 0;
        }
        if ($request->status == 0) {
            $status = 1;
        }
        $data->is_active = $status;

        $res = $data->save();
        return json_encode(array('statusCode' => 200));
    }

    public function view($id)
    {

        // $datas = StudentAssignments::find($id);
        $datas = StudentAssignments::select('*', 'student_assignment_lists.name as assignment_name', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name')
            ->join('student_assignment_lists', 'student_assignment_lists.id', 'student_assignments.assignment_id')
            ->join('studentregistrations', 'studentregistrations.id', 'student_assignments.submitted_by')
            ->get();
        return view('admin.assignments', compact('datas'));
    }

    public function tutorassignments()
    {
        $classes = (new CommonController)->classes();
        $assignments = StudentAssignmentList::select(
            'student_assignment_lists.id as assignment_id',
            'student_assignment_lists.name as assignment_name',
            'student_assignment_lists.assignment_link as assignment_link',
            'student_assignment_lists.topic',
            'subjects.name as subject',
            'classes.name as class',
            'studentregistrations.name as studentname',
            'studentregistrations.id as studentid',
            DB::raw('MAX(student_assignment_lists.created_at) as created_at')
        )
        ->join('subjects', 'subjects.id', '=', 'student_assignment_lists.subject_id')
        ->join('classes', 'classes.id', '=', 'student_assignment_lists.class_id')
        ->leftJoin('studentregistrations', 'studentregistrations.id', '=', 'student_assignment_lists.student_id')
        ->leftJoin('student_assignments', 'student_assignments.submitted_by', '=', 'student_assignment_lists.student_id')
        ->where('student_assignment_lists.is_active', 1)
        ->where('student_assignment_lists.assigned_by', session('userid')->id)
        ->groupBy('student_assignment_lists.id', 'student_assignment_lists.name','student_assignment_lists.assignment_link','student_assignment_lists.topic', 'subjects.name', 'classes.name', 'studentregistrations.name', 'studentregistrations.id')
        ->orderBy('created_at', 'desc')
        ->get();
        // $students = studentregistration::select('*')
        // ->where('is_active','1')
        // ->get();
        $students = studentregistration::select(
            'studentregistrations.*',
            'studentprofiles.profile_pic as profile_pic'
        )
        ->join('studentprofiles', 'studentprofiles.student_id', '=', 'studentregistrations.id')
        ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
        ->where('paymentstudents.tutor_id', session('userid')->id)
        ->where('is_active', 1)
        ->groupBy('studentregistrations.id',
        'studentprofiles.profile_pic',
        'studentregistrations.name',
        'studentregistrations.mobile',
        'studentregistrations.email',
        'studentregistrations.is_mobile_verified',
        'studentregistrations.mobile_verified_at',
        'studentregistrations.is_email_verified',
        'studentregistrations.email_verified_at',
        'studentregistrations.password',
        'studentregistrations.class_id',
        'studentregistrations.role_id',
        'studentregistrations.is_active',
        'studentregistrations.remember_token',
        'studentregistrations.created_at',
        'studentregistrations.updated_at',
        'studentregistrations.mobile_otp',
        'studentregistrations.email_otp',
        'studentregistrations.parent_password'
        )
        ->get();
        // dd($assignments);
        return view('tutor.assignments', compact('assignments', 'classes','students'));
    }

    public function tutorview($id)
    {

        // $datas = StudentAssignments::find($id);
        $datas = StudentAssignments::select('*', 'student_assignment_lists.name as assignment_name', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name')
            ->join('student_assignment_lists', 'student_assignment_lists.id', 'student_assignments.assignment_id')
            ->join('studentregistrations', 'studentregistrations.id', 'student_assignments.submitted_by')
            ->where('assignment_id', $id)
            ->get();
        return view('tutor.assignmentsubmissions', compact('datas'));
    }

    public function tutorassignmentscreate(Request $request)
    {

        $request->validate([
            'class' => 'required',
            'subject' => 'required',
            'studentid' => 'required',
            'topic' => 'required',
            // 'assigupload'=>'required',
            'assignname' => 'required',
            'assigndesc' => 'required',
            'assigstartdate' => 'required',
            'assigenddate' => 'required',
        ]);
        // dd($request->id);
        if ($request->id) {
            $data = StudentAssignmentList::find($request->id);
            $msg = 'Assignment updated successfully!';
        } else {
            $data = new StudentAssignmentList();
            $msg = 'Assignment added successfully';
        }
        $data->class_id = $request->class;
        $data->subject_id = $request->subject;
        $data->student_id = $request->studentid;
        $data->topic = $request->topic;
        $data->assigned_by = session('userid')->id;
        $data->name = $request->assignname;
        $data->assignment_description = $request->assigndesc;
        if ($request->assigupload) {
            $contentlink = time() . '.' . $request->assigupload->extension();
            $request->assigupload->move(public_path('uploads/documents/assignments'), $contentlink);
            $data->assignment_link = $contentlink;
        }
        $data->assignment_start_date = $request->assigstartdate;
        $data->assignment_end_date = $request->assigenddate;

        $res = $data->save();
        if ($res) {
            return back()->with('success', $msg);
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }
    public function studentassignmentslist()
    {
        $classes = (new CommonController)->classes();
        $subjects = subjects::where('is_active',1)->get();
        $assignments = StudentAssignmentList::select('*', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.topic', 'student_assignment_lists.name as assignment_name', 'subjects.name as subject', 'classes.name as class',)
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('classes', 'classes.id', 'student_assignment_lists.class_id')
            // ->join('batches', 'batches.id', 'student_assignment_lists.batch_id')
            ->where('student_assignment_lists.is_active', 1)
            ->where('student_assignment_lists.student_id', session('userid')->id)
            ->paginate(10);

            $submissions = StudentAssignments::select('*')->where('submitted_by',session('userid')->id)->where('is_active',1)->get();
        return view('student.assignments',get_defined_vars());
    }

    public function studentassignmentslistParent()
    {
        $classes = (new CommonController)->classes();
        $subjects = subjects::where('is_active',1)->get();
        $assignments = StudentAssignmentList::select('*', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.name as assignment_name', 'subjects.name as subject', 'classes.name as class', 'topics.name as topic', 'batches.name as batch')
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('classes', 'classes.id', 'student_assignment_lists.class_id')
            ->join('topics', 'topics.id', 'student_assignment_lists.topic_id')
            ->join('batches', 'batches.id', 'student_assignment_lists.batch_id')
            ->where('student_assignment_lists.is_active', 1)
            ->where('student_assignment_lists.class_id', session('userid')->class_id)
            // ->where('student_assignment_lists.student_id', session('userid')->id)
            ->paginate(10);

            $submissions = StudentAssignments::select('*')->where('submitted_by',session('userid')->id)->where('is_active',1)->get();
        return view('parent.assignments',get_defined_vars());
    }

    public function studentassignmentsSearch(Request $request)
    {
        $query = StudentAssignmentList::select('*', 'student_assignment_lists.id as assignment_id', 'student_assignment_lists.topic', 'student_assignment_lists.name as assignment_name', 'subjects.name as subject', 'classes.name as class')
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('classes', 'classes.id', 'student_assignment_lists.class_id')
            ->where('student_assignment_lists.is_active', 1)
            ->where('student_assignment_lists.student_id', session('userid')->id);

        if ($request->class_name) {
            $query->where('student_assignment_lists.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('student_assignment_lists.subject_id', $request->subject_name);
        }
        if ($request->topic) {
            $query->where('topics.name','like', '%' . $request->topic . '%');
        }
        $assignments = $query->paginate(10);
        $type = 'student-assignments';
        $submissions = StudentAssignments::select('*')->where('submitted_by',session('userid')->id)->where('is_active',1)->get();
        $viewTable = view('admin.partials.common-search', compact('assignments','type','submissions'))->render();
        $viewPagination = $assignments->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }

    public function studentassignmentsupload(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'assigupload' => 'required',
        ]);

        $data = new StudentAssignments();
        $data->assignment_id = $request->id;
        if ($request->assigupload) {
            $contentlink = time() . '.' . $request->assigupload->extension();
            $request->assigupload->move(public_path('uploads/documents/assignments'), $contentlink);
            $data->submission_link = $contentlink;
        }
        $tutor_id = StudentAssignmentList::find($request->id);
        // dd($tutor_id);
        $data->submitted_on = now();
        $data->submitted_by = session('userid')->id;
        $data->reamrks = $request->remarks;
        $data->is_active = '1';
        $res = $data->save();
        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 3;
            $notificationdata->notification = 'Assignment Submitted By '.session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $data->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            //     $notificationdata->show_to_admin = 1;
            //     $notificationdata->show_to_admin_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
                $notificationdata->show_to_tutor = 1;
                $notificationdata->show_to_tutor_id = $tutor_id->assigned_by;
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

            return back()->with('success', 'Assignment submitted successfully');
        } else {
            return back()->with('fail', 'Something went wrong, please try again later');
        }
    }
}
