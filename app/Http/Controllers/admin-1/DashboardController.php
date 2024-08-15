<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tutorprofile;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\zoom_classes;
use App\Models\OnlineTests;
use Illuminate\Support\Carbon;
use App\Models\payments\paymentdetails;
use DB;

class DashboardController extends Controller
{
    public function index(){
        
            $studentpro = tutorprofile::select('*')
                // ->where('tutor_id', '=', session('userid')->id)
                ->first();
            $pending_demos = democlasses::whereIn('status', [1, 6])
            // ->where('tutor_id', '=', session('userid')->id)
            ->count();
            $classes_taken = zoom_classes::where('zoom_classes.is_completed',1)
            // ->where('tutor_id', '=', session('userid')->id)
            ->count();
            $moneyEarned = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
                        // ->where('tutor_id', session('userid')->id)
                        ->selectRaw('COUNT(DISTINCT student_id) as student_count, SUM(amount) as total_earned')
                        ->first();
            // democlasses taken and queries for percentage bar
            $demosTaken = democlasses::join('subjects', 'subjects.id', 'democlasses.subject_id')
                        ->where('democlasses.status', 4)
                        // ->where('democlasses.tutor_id', '=', session('userid')->id)
                        ->groupBy('subjects.id')
                        ->selectRaw('subjects.id, COUNT(*) as subject_count')
                        ->get();
            $totalDemosTaken = democlasses::where('status', 4)
            // ->where('tutor_id', '=', session('userid')->id)
            ->count();
            $subjectData = [];
            foreach ($demosTaken as $demo) {
                $subjectName = subjects::find($demo->id)->name;
                $subjectCount = $demo->subject_count;
                $percentage = ($subjectCount / $totalDemosTaken) * 100;
                $subjectData[] = [
                    'subject_id' => $demo->id,
                    'subject_name' => $subjectName,
                    'percentage' => $percentage,
                ];
            }
    
            $upcomingClasses = zoom_classes::select(
                'zoom_classes.*',
                'subjects.name as subject',
                DB::raw('JSON_LENGTH(batchstudentmappings.student_data) as student_count')
            )
                ->join('topics', 'topics.id', 'zoom_classes.batch_id')
                ->join('subjects', 'subjects.id', 'topics.subject_id')
                ->join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
                // ->where('zoom_classes.tutor_id', '=', session('userid')->id)
                ->orderBy('zoom_classes.start_time', 'asc')
                ->take(5)
                ->get();
            
            $upcomingClasses->transform(function ($class) {
                $class->start_time = Carbon::parse($class->start_time);
                return $class;
            });
            
            
            
    
           
    
            $upcomingQuizes = OnlineTests::where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
                                           ->take(5)->get();
            $upcomingQuizes->transform(function ($quiz) {
                $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
                $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
                return $quiz;
            });
            
            $tUpcomingClasses = zoom_classes::where('start_time', '>', Carbon::now())
        ->orderBy('start_time', 'asc')
        ->get();
    
            $totalUpcomingClasses = $tUpcomingClasses->count();
    
            $latest_payments = paymentdetails::leftjoin('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
                        ->leftjoin('subjects','subjects.id','paymentstudents.subject_id')
                        // ->where('paymentstudents.tutor_id', '=', session('userid')->id)
                        ->orderBy('payment_date', 'desc')->take(5)->get();
            $latest_payments->transform(function ($payment) {
                $payment->payment_date = Carbon::parse($payment->payment_date);
                return $payment;
            });
    
            $upcoming_demos = democlasses::select('democlasses.*','subjects.name as subject','studentprofiles.name as student','studentprofiles.profile_pic as student_img')
            ->join('studentprofiles','studentprofiles.student_id','democlasses.student_id')
            ->join('subjects','subjects.id','democlasses.subject_id')
            ->where('democlasses.slot_confirmed', '>', Carbon::now())
            // ->where('democlasses.tutor_id', '=', session('userid')->id)
            ->orderBy('democlasses.slot_confirmed', 'asc')->take(5)->get();
            $upcoming_demos->transform(function ($demos) {
                $demos->slot_confirmed = Carbon::parse($demos->slot_confirmed);
                $demos->slot_1 = Carbon::parse($demos->slot_1);
                return $demos;
            });
    
    
            // return view('admin.dashboard',get_defined_vars());
        }
    }

