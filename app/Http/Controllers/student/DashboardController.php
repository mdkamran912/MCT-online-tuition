<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\questionbank;
use App\Models\studentprofile;
use App\Models\testattempted;
use App\Models\testresponssheet;
use DB;
use App\Models\classes;
use Illuminate\Http\Request;
use App\Models\tutorprofile;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\zoom_classes;
use App\Models\OnlineTests;
use Illuminate\Support\Carbon;
use App\Models\payments\paymentdetails;
use App\Models\StudentAssignmentList;
use App\Models\StudentAssignments;

class DashboardController extends Controller
{
    public function index()
    {

        $targetValue = session('userid')->id;
        $studentpro = studentprofile::select('*')->where('student_id', '=', session('userid')->id)->first();
        $subjects_enrolled = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)
            ->groupBy('paymentstudents.subject_id')
            ->selectRaw('paymentstudents.subject_id, COUNT(*) as subject_count')
            ->get();
        $atendedclasses = zoom_classes::
        // join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            // ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            // ->where('zoom_classes.is_active', 1)
            // ->where('zoom_classes.status', 'like', '%completed%')
            where('is_completed', 1)
            ->where('student_id', session('userid')->id)
            ->count();
        // dd($atendedclasses);
        $non_atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->count();
        $classes_purchased = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)->sum('classes_purchased');
        $upcomingClasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            // ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc') // Order by start_time in ascending order
            ->take(5) // Limit to the latest 5
            ->get();

        // ============================
        $upclasses = zoom_classes::select('*', 'zoom_classes.id as class_id', 'zoom_classes.tutor_id as tutor_id', 'zoom_classes.topic_name as topics', 'tutorprofiles.name as tutor_name', 'tutorprofiles.profile_pic')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'zoom_classes.tutor_id')
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.is_completed', 0)
            ->where('zoom_classes.start_time', '>=', Carbon::now())
            ->orderBy('zoom_classes.start_time', 'asc')
            ->take(5)
            ->get()
            ->each(function ($item) {
                $item->start_time = Carbon::parse($item->start_time)->format('Y-m-d H:i:s');
            });
        //     echo Carbon::now();

        // dd($upclasses);

        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'classes.name as class_name', 'tutorregistrations.name as tutor', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus', 'tutorprofiles.name as tutor_name')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->where('democlasses.student_id', '=', session('userid')->id)
            ->where(function ($query) {
                $query->where('democlasses.slot_confirmed', '>', Carbon::now())
                    ->orWhere('statuses.name', '=', 'started');
            })
            ->orderBy('democlasses.slot_confirmed', 'asc')
            ->take(5)
            ->get()
            ->each(function ($item) {
                $item->slot_confirmed = Carbon::parse($item->slot_confirmed)->format('Y-m-d H:i:s');
            });


        $upcomingClasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time); // Convert start_time to Carbon
            return $class;
        });
        // dd($demos);
        // Upcoming Quiz/Tests
        $upcomingQuizes = OnlineTests::select('online_tests.*', 'subjects.name as subject_name', 'subjects.image as subject_image', 'online_tests.topic_name as topic_name')
            ->where('online_tests.class_id', session('userid')->class_id)
            // ->where('online_tests.test_start_date', '>', Carbon::now())
            ->join('subjects', 'subjects.id', 'online_tests.subject_id')
            ->orderBy('online_tests.test_start_date', 'asc')
            ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });

        $pastQuizes = testattempted::select('testattempteds.*', 'online_tests.name as exam_name', 'online_tests.description as exam_description', 'online_tests.test_duration as duration', 'online_tests.test_start_date as test_start_date', 'online_tests.test_end_date as test_end_date')
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();


        // Past Tests
        $pastQuizes->transform(function ($quiz) {
            $testid = testattempted::find($quiz->id);
            $onlineTest = OnlineTests::where('id', $testid->test_id)
                ->where('class_id', session('userid')->class_id)
                ->first();

            // Decode the JSON string to an array
            $questionIds = json_decode($onlineTest->question_id, true); // Ensure it's decoded as an associative array
            $responseIds = json_decode($testid->response_id, true); // Ensure it's decoded as an associative array

            $questionsCount = is_array($questionIds) ? count($questionIds) : 0;
            $responsesCount = is_array($responseIds) ? count($responseIds) : 0;
            $correctResponsesCount = is_array($responseIds) ? count(array_filter($responseIds, function ($responseId) {
                // You may need to adjust this filtering logic based on your data structure
                return testresponssheet::where('id', $responseId)
                    ->whereColumn('correct_option', 'marked_option')
                    ->exists();
            })) : 0;


            // Assign counts to the quiz object
            $quiz->questionsCount = $questionsCount;
            $quiz->responsesCount = $responsesCount;
            $quiz->correctResponsesCount = $correctResponsesCount;

            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });


        // Test Reports - Merged By Tests
        $pastQuizzes = testattempted::select('testattempteds.*', 'online_tests.subject_id', 'online_tests.name as exam_name', 'online_tests.description as exam_description', 'online_tests.test_duration as duration', 'online_tests.test_start_date as test_start_date', 'online_tests.test_end_date as test_end_date')
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();


        $pastQuizzes = testattempted::select(
            'testattempteds.*',
            'online_tests.subject_id',
            'online_tests.question_id', // Include the question_id from online_tests
            'online_tests.name as exam_name',
            'online_tests.description as exam_description',
            'online_tests.test_duration as duration',
            'online_tests.test_start_date as test_start_date',
            'online_tests.test_end_date as test_end_date'
        )
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();

        $pastQuizzes = $pastQuizzes->groupBy('subject_id')->map(function ($quizzes) {
            // Initialize aggregated counts
            $totalQuestions = 0;
            $totalAttempted = 0;

            // Iterate over quizzes to sum counts
            foreach ($quizzes as $quiz) {
                $questionIds = json_decode($quiz->question_id, true);
                $responseIds = json_decode($quiz->response_id, true);

                $questionsCount = is_array($questionIds) ? count($questionIds) : 0;
                $responsesCount = is_array($responseIds) ? count($responseIds) : 0;

                // Aggregate counts
                $totalQuestions += $questionsCount;
                $totalAttempted += $responsesCount;
            }

            // Pick the first quiz to get subject information
            $firstQuiz = $quizzes->first();
            $subjectName = subjects::find($firstQuiz->subject_id)->name;

            return [
                'subjectName' => $subjectName,
                'totalTests' => $quizzes->count(),
                'totalQuestions' => $totalQuestions,
                'totalAttempted' => $totalAttempted,
            ];
        });

        // Tutors List
        $tutorlists = tutorprofile::select(
            'tutorprofiles.id',
            'classes.name as class_name',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.experience',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            'tutorprofiles.profile_pic',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('SUM(ratings) / COUNT(ratings) AS starrating, COUNT(DISTINCT topics.name) as total_topics'),
            'tutorsubjectmappings.id as sub_map_id',
            DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done')
        )
            ->join('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->join('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.is_active', 1)
            ->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();

        // Subject lists with category
        $subjectlistsdata = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        $formattedSubjects = [];

        foreach ($subjectlists as $subject) {
            $categoryName = $subject->category_name;
            $subjectName = $subject->subject_name;

            if (!isset($formattedSubjects[$categoryName])) {
                $formattedSubjects[$categoryName] = [];
            }

            $formattedSubjects[$categoryName][] = $subjectName;
        }


        // dd($subjectlists);
        // Upcoming Assignments
        $upcomingAssignments = StudentAssignmentList::select('student_assignment_lists.name as assignment_name', 'subjects.name as subject_name', 'topics.name as topic_name', 'student_assignment_lists.assignment_start_date as assignment_start_date', 'student_assignment_lists.assignment_link as assignment_link', 'tutorprofiles.profile_pic as tutor_pic')
            ->where('student_assignment_lists.class_id', session('userid')->class_id)
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('topics', 'topics.id', 'student_assignment_lists.topic_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'student_assignment_lists.assigned_by')
            // ->where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
            ->take(5)->get();



        $latest_payments = paymentdetails::leftjoin('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->leftjoin('subjects', 'subjects.id', 'paymentstudents.subject_id')->where('paymentstudents.student_id', '=', session('userid')->id)->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        $classes = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->join('subjects', 'subjects.id', 'topics.subject_id')
            // ->where('zoom_classes.is_completed',0)
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->groupBy('subjects.id')
            ->selectRaw('subjects.id, COUNT(*) as subject_count')
            ->get();



        $totalclassesTaken =  zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")->count();

        $subjectData = [];
        foreach ($classes as $demo) {
            $subjectName = subjects::find($demo->id)->name;
            $subjectCount = $demo->subject_count;
            $percentage = ($subjectCount / $totalclassesTaken) * 100;
            $subjectData[] = [
                'subject_id' => $demo->id,
                'subject_name' => $subjectName,
                'percentage' => $percentage,
            ];
        }

        return view('student.dashboard', get_defined_vars());
    }

    public function parent_dashboard()
    {

        $targetValue = session('userid')->id;
        $studentpro = studentprofile::select('*')->where('student_id', '=', session('userid')->id)->first();
        $subjects_enrolled = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)
            ->groupBy('paymentstudents.subject_id')
            ->selectRaw('paymentstudents.subject_id, COUNT(*) as subject_count')
            ->get();
        $atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%completed%')
            ->where('zoom_classes.is_completed', 1)
            ->count();
        $non_atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->count();
        $classes_purchased = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)->sum('classes_purchased');
        $upcomingClasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc') // Order by start_time in ascending order
            ->take(5) // Limit to the latest 5
            ->get();
        $upcomingClasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time); // Convert start_time to Carbon
            return $class;
        });

        $upcomingQuizes = OnlineTests::
            // where('class_id',session('userid')->class_id)
            where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
            ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });
        $latest_payments = paymentdetails::leftjoin('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->leftjoin('subjects', 'subjects.id', 'paymentstudents.subject_id')->where('paymentstudents.student_id', '=', session('userid')->id)->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        $classes = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->join('subjects', 'subjects.id', 'topics.subject_id')
            // ->where('zoom_classes.is_completed',0)
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->groupBy('subjects.id')
            ->selectRaw('subjects.id, COUNT(*) as subject_count')
            ->get();



        $totalclassesTaken =  zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")->count();

        $subjectData = [];
        foreach ($classes as $demo) {
            $subjectName = subjects::find($demo->id)->name;
            $subjectCount = $demo->subject_count;
            $percentage = ($subjectCount / $totalclassesTaken) * 100;
            $subjectData[] = [
                'subject_id' => $demo->id,
                'subject_name' => $subjectName,
                'percentage' => $percentage,
            ];
        }

        return view('parent.dashboard', get_defined_vars());
    }
}
