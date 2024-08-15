<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\payments\paymentdetails;
use App\Models\classes;
use App\Models\subjects;
use App\Models\status;
use App\Models\PaymentMode;
use App\Models\studentregistration;
use App\Models\tutorregistration;
use App\Models\payments\paymentstudents;
use App\Models\tutorsubjectmapping;
use Illuminate\Http\Request;
use App\Models\payout;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function index(){
        $statuses = (new CommonController)->status();
        $payments = paymentdetails::select('paymentdetails.id as transaction_id','paymentdetails.transaction_id as transaction_no','paymentdetails.payment_date as transaction_date','paymentdetails.payment_mode as payment_mode','paymentdetails.amount as transaction_amount','statuses.id as transaction_status_id','statuses.name as transaction_status','paymentdetails.remarks as remarks','studentregistrations.id as student_id','studentregistrations.name as student_name','classes.name as class_name','subjects.name as subject_name','tutorregistrations.id as tutor_id','tutorregistrations.name as tutor_name')
        ->join('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
        ->join('studentregistrations','studentregistrations.id','paymentstudents.student_id')
        ->join('classes','classes.id','studentregistrations.class_id')
        ->join('subjects','subjects.id','paymentstudents.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('statuses','statuses.id','paymentdetails.status')
        ->paginate(10);
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $students = studentregistration::where('is_active',1)->get();
        $tutors = tutorregistration::where('is_active',1)->get();
        $totalTransactionAmount = $payments->sum('transaction_amount');
        return view('admin.payments',get_defined_vars());
    }

    // tutor dashboard studebt payments

    public function tutorStudentPayments(){
        $statuses = (new CommonController)->status();
        $payments = paymentdetails::select('paymentdetails.id as transaction_id','paymentdetails.transaction_id as transaction_no','paymentdetails.payment_date as transaction_date','paymentdetails.payment_mode as payment_mode','paymentdetails.amount as transaction_amount','statuses.id as transaction_status_id','statuses.name as transaction_status','paymentdetails.remarks as remarks','studentregistrations.id as student_id','studentregistrations.name as student_name','classes.name as class_name','subjects.name as subject_name','tutorregistrations.id as tutor_id','tutorregistrations.name as tutor_name')
        ->join('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
        ->join('studentregistrations','studentregistrations.id','paymentstudents.student_id')
        ->join('classes','classes.id','studentregistrations.class_id')
        ->join('subjects','subjects.id','paymentstudents.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('statuses','statuses.id','paymentdetails.status')
        ->where('paymentstudents.tutor_id',session('userid')->id)
        ->paginate(10);
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $students = studentregistration::where('is_active',1)->get();
        $tutors = tutorregistration::where('is_active',1)->get();
        $totalTransactionAmount = $payments->sum('transaction_amount');
        return view('tutor.payments',get_defined_vars());
    }




    public function studentpaymentsreport(){
        $statuses = (new CommonController)->status();
        $payments = paymentdetails::select('paymentdetails.id as transaction_id','paymentdetails.transaction_id as transaction_no','paymentdetails.payment_date as transaction_date','paymentdetails.payment_mode as payment_mode','paymentdetails.amount as transaction_amount','statuses.id as transaction_status_id','statuses.name as transaction_status','paymentdetails.remarks as remarks','studentregistrations.id as student_id','studentregistrations.name as student_name','classes.name as class_name','subjects.name as subject_name','tutorregistrations.id as tutor_id','tutorregistrations.name as tutor_name')
        ->join('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
        ->join('studentregistrations','studentregistrations.id','paymentstudents.student_id')
        ->join('classes','classes.id','studentregistrations.class_id')
        ->join('subjects','subjects.id','paymentstudents.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('statuses','statuses.id','paymentdetails.status')
        ->get();
        // $payments = paymentstudents::select('*')
        // ->join('paymentdetails','paymentdetails.transaction_id','paymentstudents.transaction_id')
        // ->join('studentregistrations','studentregistrations.id','paymentstudents.student_id')
        // ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        // ->get();
        // ->paginate(10);
        $totalTransactionAmount = $payments->sum('transaction_amount');
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $students = studentregistration::where('is_active',1)->get();
        $tutors = tutorregistration::where('is_active',1)->get();
        return view('admin.studentpayments',get_defined_vars());
    }

    public function tutorpaymentsreport(){
        $statuses = (new CommonController)->status();
        $payments = paymentdetails::select('paymentdetails.id as transaction_id','paymentdetails.transaction_id as transaction_no','paymentdetails.payment_date as transaction_date','paymentdetails.payment_mode as payment_mode','paymentdetails.amount as transaction_amount','statuses.id as transaction_status_id','statuses.name as transaction_status','paymentdetails.remarks as remarks','classes.name as class_name','subjects.name as subject_name','tutorregistrations.id as tutor_id','tutorregistrations.name as tutor_name')
        ->join('tutorpayments','tutorpayments.transaction_id','paymentdetails.transaction_id')
        ->join('tutorregistrations','tutorregistrations.id','tutorpayments.tutor_id')
        ->join('classes','classes.id','tutorpayments.class_id')
        ->join('subjects','subjects.id','tutorpayments.subject_id')
        // ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('statuses','statuses.id','paymentdetails.status')
        ->paginate(10);
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $tutors = tutorregistration::where('is_active',1)->get();
        $totalTransactionAmount = $payments->sum('transaction_amount');
        return view('admin.tutorpayments',get_defined_vars());
    }

    // search functionality for payment details

    public function paymentSearch(Request $request)
    {
        $statuses = (new CommonController)->status();
        $query = paymentdetails::select('paymentdetails.id as transaction_id', 'paymentdetails.transaction_id as transaction_no', 'paymentdetails.payment_date as transaction_date', 'paymentdetails.payment_mode as payment_mode', 'paymentdetails.amount as transaction_amount', 'statuses.id as transaction_status_id', 'statuses.name as transaction_status', 'paymentdetails.remarks as remarks', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'classes.name as class_name', 'subjects.name as subject_name', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name')
            ->join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->join('studentregistrations', 'studentregistrations.id', 'paymentstudents.student_id')
            ->join('classes', 'classes.id', 'studentregistrations.class_id')
            ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'paymentstudents.tutor_id')
            ->join('statuses', 'statuses.id', 'paymentdetails.status');

        // Filter based on search parameters
        if ($request->student_name) {
            $query->where('paymentstudents.student_id', $request->student_name);
        }
        if ($request->class_name) {
            $query->where('paymentstudents.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('paymentstudents.subject_id', $request->subject_name);
        }
        if ($request->tutor_name) {
            $query->where('paymentstudents.tutor_id', $request->tutor_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '<=', $request->end_date);
        }
        if ($request->email) {
            $query->where('studentregistrations.email',  'like', '%' . $request->email . '%');
        }
        if ($request->mobile) {
            $query->where('studentregistrations.mobile',  'like', '%' . $request->mobile . '%');
        }
        if ($request->transaction_id) {
            $query->where('paymentdetails.transaction_id',  'like', '%' .$request->transaction_id . '%');
        }
        $payments = $query->paginate(10);
        $totalTransactionAmount = $payments->sum('transaction_amount');
        $type="admin";
        $viewTable = view('admin.partials.payments-search', compact('payments','totalTransactionAmount','type'))->render();
        $viewPagination = $payments->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);

        // $subjects = subjects::where('is_active', 1)->get();
        // $classes = classes::where('is_active', 1)->get();
        // $students = studentregistration::where('is_active', 1)->get();
        // $tutors = tutorregistration::where('is_active', 1)->get();

        // return view('admin.payments', get_defined_vars());
    }

    // search functionality tutor dashboard

    public function paymentSearchTutor(Request $request)
    {
        // return "tutor";
        $statuses = (new CommonController)->status();
        $query = paymentdetails::select('paymentdetails.id as transaction_id', 'paymentdetails.transaction_id as transaction_no', 'paymentdetails.payment_date as transaction_date', 'paymentdetails.payment_mode as payment_mode', 'paymentdetails.amount as transaction_amount', 'statuses.id as transaction_status_id', 'statuses.name as transaction_status', 'paymentdetails.remarks as remarks', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'classes.name as class_name', 'subjects.name as subject_name', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name')
            ->join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->join('studentregistrations', 'studentregistrations.id', 'paymentstudents.student_id')
            ->join('classes', 'classes.id', 'studentregistrations.class_id')
            ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'paymentstudents.tutor_id')
            ->join('statuses', 'statuses.id', 'paymentdetails.status')
            ->where('paymentstudents.tutor_id',session('userid')->id);

        // Filter based on search parameters
        if ($request->student_name) {
            $query->where('paymentstudents.student_id', $request->student_name);
        }
        if ($request->class_name) {
            $query->where('paymentstudents.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('paymentstudents.subject_id', $request->subject_name);
        }
        if ($request->tutor_name) {
            $query->where('paymentstudents.tutor_id', $request->tutor_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '<=', $request->end_date);
        }
        if ($request->email) {
            $query->where('studentregistrations.email',  'like', '%' . $request->email . '%');
        }
        if ($request->mobile) {
            $query->where('studentregistrations.mobile',  'like', '%' . $request->mobile . '%');
        }
        if ($request->transaction_id) {
            $query->where('paymentdetails.transaction_id',  'like', '%' .$request->transaction_id . '%');
        }
        $payments = $query->paginate(10);
        $totalTransactionAmount = $payments->sum('transaction_amount');
        $type="tutor";
        $viewTable = view('admin.partials.payments-search', compact('payments','totalTransactionAmount','type'))->render();
        $viewPagination = $payments->links()->render();

        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $students = studentregistration::where('is_active',1)->get();
        $tutors = tutorregistration::where('is_active',1)->get();
        $totalTransactionAmount = $payments->sum('transaction_amount');
        return view('tutor.payments',get_defined_vars());
    }


    public function tutorPaymentSearch(Request $request)
    {
        $statuses = (new CommonController)->status();
        $query = paymentdetails::select('paymentdetails.id as transaction_id', 'paymentdetails.transaction_id as transaction_no', 'paymentdetails.payment_date as transaction_date', 'paymentdetails.payment_mode as payment_mode', 'paymentdetails.amount as transaction_amount', 'statuses.id as transaction_status_id', 'statuses.name as transaction_status', 'paymentdetails.remarks as remarks', 'classes.name as class_name', 'subjects.name as subject_name', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name')
        ->join('tutorpayments','tutorpayments.transaction_id','paymentdetails.transaction_id')
        ->join('tutorregistrations','tutorregistrations.id','tutorpayments.tutor_id')
        ->join('classes','classes.id','tutorpayments.class_id')
        ->join('subjects','subjects.id','tutorpayments.subject_id')
        ->join('statuses','statuses.id','paymentdetails.status');


        // Filter based on search parameters
        if ($request->tutor_name) {
            $query->where('tutorpayments.tutor_id', $request->tutor_name);
        }

        if ($request->class_name) {
            $query->where('tutorpayments.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('tutorpayments.subject_id', $request->subject_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '<=', $request->end_date);
        }
        if ($request->email) {
            $query->where('tutorregistrations.email',  'like', '%' . $request->email . '%');
        }
        if ($request->mobile) {
            $query->where('tutorregistrations.mobile',  'like', '%' . $request->mobile . '%');
        }
        if ($request->transaction_id) {
            $query->where('paymentdetails.transaction_id',  'like', '%' .$request->transaction_id . '%');
        }
        $payments = $query->paginate(10);
        $totalTransactionAmount = $payments->sum('transaction_amount');
        $viewTable = view('admin.partials.tutor-payments-search', compact('payments','totalTransactionAmount'))->render();
        $viewPagination = $payments->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);

        // $subjects = subjects::where('is_active', 1)->get();
        // $classes = classes::where('is_active', 1)->get();
        // $students = studentregistration::where('is_active', 1)->get();
        // $tutors = tutorregistration::where('is_active', 1)->get();

        // return view('admin.payments', get_defined_vars());
    }


    public function update(Request $request){
        $request->validate([
            'transactionid'=>'required',
            'paymentstatus'=>'required',
            'transactionremarks'=>'required'
        ]);
        $data = paymentdetails::find($request->transactionid);
        $data->status = $request->paymentstatus;
        $data->remarks = $request->transactionremarks;
        $res = $data->save();
        if($res){
            return back()->with('success','Payment details updated successfully');
        }
        else{
            return back()->with('fail','Something went wrong. Please try again later');
        }
    }

    public function tutorpaymentslist(){
        $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::all();
        $PaymentModes = PaymentMode::all();
        $tutorpayouts = payout::select('payouts.*','tutorregistrations.name','statuses.name as status_name','tutorregistrations.mobile','tutorregistrations.email')
        ->join('tutorregistrations','tutorregistrations.id','payouts.tutor_id')->join('statuses','statuses.id','payouts.status')
        ->paginate(10);
        return view('admin.tutorpaymentlist',get_defined_vars());
    }



    public function tutorPaymentAdmin(Request $request){
        $request->validate([
            'class'=>'required',
            'subject'=>'required',
            'tutor'=>'required',
            'account'=>'required',
            'transNo'=>'required',
            'date'=>'required',
            'status'=>'required',
            'payment_mode'=>'required',
            'total_amount'=>'required',
            'amount_payable' => ['required', 'numeric', 'min:0', 'max:' . $request->pending_amount_payable],
            'admin_commission_amount'=>'required',
            'admin_commission_percentage'=>'required',
        ]);

        $payout = new payout;
        $payout->tutor_id= $request->tutor;
        $payout->total_amount= $request->total_amount;
        $payout->net_amount_received= $request->amount_payable;
        $payout->admin_commission_percentage= $request->admin_commission_percentage;
        $payout->admin_commission_amount= $request->admin_commission_amount;
        $payout->account_no= $request->account;
        $payout->transaction_no= $request->transNo;
        $payout->transaction_date= $request->date;
        $payout->status= $request->status;
        $payout->payment_mode= $request->payment_mode;
        $payout->save();
        return response()->json([
            'success' => 'Payment is added successfully',
        ]);
    }

    public function fetchtutorsAmount(Request $request){
        $moneyEarned = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
                    ->where('tutor_id', $request->tutor_id)
                    ->selectRaw('COUNT(DISTINCT student_id) as student_count, SUM(amount) as total_amount')
                    ->first();
        if($moneyEarned){
            $totalAmount = $moneyEarned['total_amount'];
            $adminCommission = tutorsubjectmapping::where('tutor_id',$request->tutor_id)->avg('admin_commission');
            $already_paid = payout::where('tutor_id',$request->tutor_id)->sum('net_amount_received');
            if($totalAmount > 0 && $adminCommission > 0){
                    $commissionAmount = ($totalAmount ) * ( $adminCommission/100);
                    $netPayableAmount = $totalAmount - $commissionAmount;
                    $totalPayable = $netPayableAmount-$already_paid;
            }else{
                $commissionAmount =0;
                $netPayableAmount = 0;
            }
        }else{
            $totalAmount = 0;
            $commissionAmount =0;
            $adminCommission = 0;
            $netPayableAmount = 0;

        }
        return response()->json([
            'totalAmount' => $totalAmount ?? '0',
            'adminComissionPercentage'=>$adminCommission,
            'commissionAmount'=>$commissionAmount,
            'netPayableAmount'=>$netPayableAmount ?? '0',
            'alreadyPaid' => $already_paid,
            'totalPayable' => $totalPayable ?? '0'
        ]);

    }





    public function tutorpayments(){

        return view('admin.tutorpayment');
    }

    public function studentpayments(){

        $payments = paymentstudents::select(
            'paymentstudents.*',
            'classes.name as class',
            'subjects.name as subject',
            'tutorregistrations.name as tutor',
            DB::raw('(SELECT id FROM paymentdetails WHERE paymentdetails.transaction_id = paymentstudents.transaction_id LIMIT 1) as paymentdetails_id'),
            DB::raw('(SELECT amount FROM paymentdetails WHERE paymentdetails.transaction_id = paymentstudents.transaction_id LIMIT 1) as amount')
        )
        ->join('classes', 'classes.id', '=', 'paymentstudents.class_id')
        ->join('subjects', 'subjects.id', '=', 'paymentstudents.subject_id')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'paymentstudents.tutor_id')
        ->where('paymentstudents.student_id', session('userid')->id)
        ->paginate(10);
    // dd($payments);

        // dd(session('userid')->id);
        return view('student.fees',compact('payments'));
    }
    public function studentpaymentsParent(){

        $payments = paymentstudents::select('paymentstudents.*','classes.name as class','subjects.name as subject','tutorregistrations.name as tutor','paymentdetails.id as paymentdetails_id','paymentdetails.amount as amount')
        ->join('classes','classes.id','paymentstudents.class_id')
        ->join('subjects', 'subjects.id','paymentstudents.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('paymentdetails','paymentdetails.transaction_id','paymentstudents.transaction_id')
        ->where('paymentstudents.student_id',session('userid')->id)
        ->where('paymentstudents.student_id',session('userid')->id)
        ->paginate(10);
        // dd($payments);
        return view('parent.fees',compact('payments'));
    }
    // students payments search functionality
    public function studentpaymentsSearch(Request $request){

        // return $request->all();

        $query = paymentstudents::select('paymentstudents.*','classes.name as class','subjects.name as subject','tutorregistrations.name as tutor','paymentdetails.id as paymentdetails_id','paymentdetails.amount as amount')
        ->join('classes','classes.id','paymentstudents.class_id')
        ->join('subjects', 'subjects.id','paymentstudents.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','paymentstudents.tutor_id')
        ->join('paymentdetails','paymentdetails.transaction_id','paymentstudents.transaction_id')
        ->where('paymentstudents.student_id',session('userid')->id);

        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(paymentdetails.payment_date)'), '<=', $request->end_date);
        }

        if ($request->transaction_id) {
            $query->where('paymentdetails.transaction_id',  'like', '%' .$request->transaction_id . '%');
        }
        $payments = $query->paginate(10);
        $type="students-payments";
        $viewTable = view('admin.partials.common-search', compact('payments','type'))->render();
        $viewPagination = $payments->links()->render();

        return view('student.fees',compact('payments'));
    }
    public function tutorpayouts(){
        $tutorpayouts = payout::select('payouts.*','tutorregistrations.name','statuses.name as status_name','tutorregistrations.mobile','tutorregistrations.email')
        ->join('tutorregistrations','tutorregistrations.id','payouts.tutor_id')->join('statuses','statuses.id','payouts.status')
        ->where('payouts.tutor_id',session('userid')->id)->paginate(10);
        $statuses = (new CommonController)->status();
        return view('tutor.payouts',get_defined_vars());
    }
    public function tutorpayoutsSearch(Request $request){

        $query = payout::select('payouts.*','tutorregistrations.name','statuses.name as status_name','tutorregistrations.mobile','tutorregistrations.email')
                 ->join('tutorregistrations','tutorregistrations.id','payouts.tutor_id')->join('statuses','statuses.id','payouts.status')
                 ->where('payouts.tutor_id',session('userid')->id);
                //  ->get();
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(payouts.transaction_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(payouts.transaction_date)'), '<=', $request->end_date);
        }
        if ($request->tansaction_no) {
            $query->where('payouts.transaction_no','like', '%' .$request->tansaction_no . '%');
        }
        if ($request->status) {
            $query->where('payouts.status',$request->status);
        }
        $tutorpayouts = $query->paginate(10);
        $type="tutor";
        $viewTable = view('admin.partials.payments-search', compact('tutorpayouts','type'))->render();
        $viewPagination = $tutorpayouts->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }

    public function adminPayoutsSearch(Request $request){

        $query = payout::select('payouts.*','tutorregistrations.name','statuses.name as status_name','tutorregistrations.mobile','tutorregistrations.email')
                 ->join('tutorregistrations','tutorregistrations.id','payouts.tutor_id')->join('statuses','statuses.id','payouts.status');
                //  ->where('payouts.tutor_id',session('userid')->id);
                //  ->get();
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(payouts.transaction_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(payouts.transaction_date)'), '<=', $request->end_date);
        }
        if ($request->tansaction_no) {
            $query->where('payouts.transaction_no','like', '%' .$request->tansaction_no . '%');
        }
        if ($request->tutorname) {
            $query->where('tutorregistrations.name','like', '%' .$request->tutorname . '%');
        }
        if ($request->tutormobile) {
            $query->where('tutorregistrations.mobile','like', '%' .$request->tutormobile . '%');
        }
        if ($request->tutoremail) {
            $query->where('tutorregistrations.email','like', '%' .$request->tutoremail . '%');
        }
        if ($request->status) {
            $query->where('payouts.status',$request->status);
        }
        $tutorpayouts = $query->paginate(10);
        $type="admin";
        $viewTable = view('admin.partials.payments-search', compact('tutorpayouts','type'))->render();
        $viewPagination = $tutorpayouts->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }
}
