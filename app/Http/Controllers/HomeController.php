<?php

namespace App\Http\Controllers;

use App\Events\RealTimeMessage;
use App\Mail\SendMail;
use App\Models\Blogs;
use App\Models\classes;
use App\Models\country;
use App\Models\Notification;
use App\Models\SlotBooking;
use App\Models\studentprofile;
use App\Models\studentregistration;
use App\Models\subjects;
use App\Models\tutorachievements;
use App\Models\tutorprofile;
use App\Models\tutorregistration;
use App\Models\tutorreviews;
use App\Models\tutorsubjectmapping;
// use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends Controller
{
    // public function deepesh(){
    //     event(new \App\Events\TestNotification('This is testing data'));

    // }
    public function index()
    {

        // return 'Email has been sent!';

        $classes = classes::all('id', 'name');

        // $tutors = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject',DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'))
        //     ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        //     ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
        //     ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        //     ->get();
        $tutors = tutorprofile::select(
            'tutorprofiles.name',
            'tutorprofiles.tutor_id',
            'tutorprofiles.headline',
            'tutorprofiles.profile_pic',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) as rateperhour'), // Calculate rateperhour with commission
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name) as subjects'), // Concatenate subjects
            DB::raw('GROUP_CONCAT(DISTINCT classes.name) as classNames'), // Concatenate class names
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating'),
            DB::raw('COUNT(tutorreviews.id) as total_reviews') // Count total reviews
        )
            ->distinct()
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->where('tutorregistrations.is_active', 1)
            ->groupBy(
                'tutorprofiles.name',
                'tutorprofiles.tutor_id',
                'tutorprofiles.profile_pic',
                'tutorprofiles.headline',
                'tutorsubjectmappings.rate',
                'tutorsubjectmappings.admin_commission',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission'
            )
            ->get();

        $tutorlists = tutorprofile::select('tutorprofiles.id', 'tutorprofiles.tutor_id', 'classes.name as class_name', 'tutorprofiles.name', 'tutorprofiles.headline', 'tutorprofiles.qualification as tutor_qualification', 'tutorprofiles.intro_video_link', 'tutorprofiles.experience', 'tutorprofiles.rate as rateperhour', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'), 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings) / COUNT(ratings) AS starrating, COUNT(DISTINCT topics.name) as total_topics'), 'tutorsubjectmappings.id as sub_map_id',
            DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done')
        )
            ->join('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->join('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.is_active', '1')
            ->groupby('tutorprofiles.id', 'tutorprofiles.tutor_id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.intro_video_link', 'tutorprofiles.qualification', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();
        // dd($tutorlists);
        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();
        // Subject Categories with subjects count
        $subjectcategories = DB::table('subjectcategories')
            ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            ->where('subjectcategories.is_active', 1)
            ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            ->get();

        $reviews = tutorreviews::select('tutorreviews.*', 'subjects.name as subjectname', 'tutorregistrations.name as tutorname', 'studentregistrations.name as studentname')
            ->leftJoin('subjects', 'subjects.id', 'tutorreviews.subject_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorreviews.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', 'tutorreviews.student_id')
            ->where('tutorreviews.ratings', '>', 3)->get();

        $blogs = Blogs::select('*')->where('is_active', 1)->orderby('created_at')->get();
        // dd($subjectcategories);
        // dd( ($blogs));
        return view('front-cms.index', get_defined_vars());
        // return view('front-cms.index', compact('classes'));
    }
    public function indexresources()
    {
        $blogs = Blogs::select('*')->where('is_active', 1)->get();
        return view('front-cms.resources', compact('blogs'));
    }
    public function indexresourcesdetails($id)
    {

        $blog = Blogs::select('*')->where('id', $id)->where('is_active', 1)->first();
        return view('front-cms.blogdetails', compact('blog'));
    }
    public function toptutorsearch(Request $request)
    {
        $subjectid = $request->subject;
        $classid = $request->grade;

        $classes = classes::all('id', 'name');

        $tutors = tutorprofile::select(
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.profile_pic',
            'tutorprofiles.tutor_id as tutor_id',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) as rateperhour'), // Calculate rateperhour with commission
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name) as subjects'), // Concatenate subjects
            DB::raw('GROUP_CONCAT(DISTINCT classes.name) as classNames'), // Concatenate class names
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating'),
            DB::raw('COUNT(tutorreviews.id) as total_reviews') // Count total reviews
        )
            ->distinct()
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->where('tutorregistrations.is_active', 1)
            ->where('tutorsubjectmappings.subject_id', 'LIKE', '%' . $subjectid . '%')
            ->where('tutorsubjectmappings.class_id', 'LIKE', '%' . $classid . '%')
            ->groupBy(
                'tutorprofiles.name',
                'tutorprofiles.profile_pic',
                'tutorprofiles.rate',
                'tutorsubjectmappings.rate',
                'tutorsubjectmappings.admin_commission',
                'tutorprofiles.headline',
                'tutorprofiles.tutor_id',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission'
            )
            ->get();

        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();

        // Subject Categories with subjects count
        $subjectcategories = DB::table('subjectcategories')
            ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            ->where('subjectcategories.is_active', 1)
            ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            ->get();

        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());

    }
    public function advancesearch(Request $request)
    {
        $tutorname = $request->name;
        $subjectid = $request->subject;
        $classid = $request->grade;

        $classes = classes::all('id', 'name');

        $tutors = tutorprofile::select(
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.profile_pic',
            'tutorprofiles.tutor_id as tutor_id',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) as rateperhour'), // Calculate rateperhour with commission
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name) as subjects'), // Concatenate subjects
            DB::raw('GROUP_CONCAT(DISTINCT classes.name) as classNames'), // Concatenate class names
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating'),
            DB::raw('COUNT(tutorreviews.id) as total_reviews') // Count total reviews
        )
            ->distinct()
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->where('tutorregistrations.is_active', 1)
            ->where('tutorsubjectmappings.subject_id', 'LIKE', '%' . $subjectid . '%')
            ->where('tutorsubjectmappings.class_id', 'LIKE', '%' . $classid . '%')
            ->where('tutorregistrations.name', 'LIKE', '%' . $tutorname . '%')
            ->groupBy(
                'tutorprofiles.name',
                'tutorprofiles.profile_pic',
                'tutorprofiles.rate',
                'tutorsubjectmappings.rate',
                'tutorsubjectmappings.admin_commission',
                'tutorprofiles.headline',
                'tutorprofiles.tutor_id',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission'
            )
            ->get();

        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();

        // Subject Categories with subjects count
        $subjectcategories = DB::table('subjectcategories')
            ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            ->where('subjectcategories.is_active', 1)
            ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            ->get();

        //     $classes = classes::all('id', 'name');

        //     $tutors = tutorprofile::select(
        //     'tutorprofiles.name',
        //     'tutorprofiles.headline',
        //     'tutorprofiles.profile_pic',
        //     'tutorprofiles.tutor_id as tutor_id',
        //     DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) as rateperhour'), // Calculate rateperhour with commission
        //     DB::raw('GROUP_CONCAT(DISTINCT subjects.name) as subjects'), // Concatenate subjects
        //     DB::raw('GROUP_CONCAT(DISTINCT classes.name) as classNames'), // Concatenate class names
        //     DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
        //     DB::raw('AVG(tutorreviews.ratings) as avg_rating'),
        //     DB::raw('COUNT(tutorreviews.id) as total_reviews') // Count total reviews
        // )
        // ->distinct()
        // ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        // ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
        // ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        // ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
        // ->leftJoin('tutorregistrations','tutorregistrations.id','tutorprofiles.tutor_id')
        // ->leftJoin('tutorreviews', function($join) {
        //     $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
        //          ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
        // })
        // ->where('tutorsubjectmappings.subject_id', 'LIKE', '%' . $subjectid . '%')
        // ->where('tutorsubjectmappings.class_id', 'LIKE', '%' . $classid . '%')
        // ->where('tutorprofiles.country_id', 'LIKE', '%' . $countryid . '%')
        // ->where('tutorprofiles.name', 'LIKE', '%' . $tutorname . '%')
        // ->where('tutorregistrations.is_active', 1)
        // ->groupBy(
        //     'tutorprofiles.name',
        //     'tutorprofiles.profile_pic',
        //     'tutorprofiles.rate',
        //     'tutorsubjectmappings.rate',
        //     'tutorsubjectmappings.admin_commission',
        //     'tutorprofiles.headline',
        //     'tutorprofiles.rateperhour',
        //     'tutorprofiles.admin_commission',
        //     'tutorprofiles.tutor_id'
        // )
        // ->havingRaw('AVG(tutorreviews.ratings) >= ?', [$ratings])
        // ->get();

        //     $subjectlists = DB::table('subjects')
        //     ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
        //     ->select('subjectcategories.name as category_name', 'subjects.name as subject_name','subjects.id as subject_id')
        //     ->where('subjects.is_active', 1)
        //     ->orderBy('subjectcategories.name')
        //     ->get();

        //     // Grades/Level
        //     $gradelists = Classes::where('is_active',1)->get();
        //     $subjects = Subjects::where('is_active',1)->get();
        //     $countries = Country::where('is_active',1)->get();

        //     // Subject Categories with subjects count
        //     $subjectcategories = DB::table('subjectcategories')
        // ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
        // ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
        // ->where('subjectcategories.is_active',1)
        // ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active','subjectcategories.created_at','subjectcategories.updated_at')
        // ->get();

        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());

    }
    public function allsubjects()
    {
        $classes = classes::all('id', 'name');

        $tutors = tutorprofile::select(
            'tutorsubjectmappings.id as submapid', 'tutorprofiles.name', 'tutorprofiles.profile_pic',
            'subjects.name as subject',
            'classes.name as className',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating')
        )
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftjoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'tutorprofiles.name', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Tutors List
        $tutorlists = tutorprofile::select('tutorprofiles.id', 'classes.name as class_name', 'tutorprofiles.name', 'tutorprofiles.headline', 'tutorprofiles.qualification as tutor_qualification', 'tutorprofiles.intro_video_link', 'tutorprofiles.experience', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'), 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings) / COUNT(ratings) AS starrating, COUNT(DISTINCT topics.name) as total_topics'), 'tutorsubjectmappings.id as sub_map_id',
            DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done')
        )
            ->join('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->join('topics', 'topics.subject_id', '=', 'subjects.id')
            ->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.intro_video_link', 'tutorprofiles.qualification', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();
        // dd($tutorlists);
        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // Subject Categories with subjects count
        $subjectcategories = DB::table('subjectcategories')
            ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            ->where('subjectcategories.is_active', 1)
            ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            ->get();

        // dd($subjectcategories);
        // dd( ($tutors));
        return view('front-cms.allsubjects', get_defined_vars());
        // return view('front-cms.index', compact('classes'));
    }
    public function findatutor()
    {
        $tutors = tutorprofile::select(
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.profile_pic',
            'tutorprofiles.tutor_id as tutor_id',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) as rateperhour'), // Calculate rateperhour with commission
            DB::raw("GROUP_CONCAT(DISTINCT SUBSTRING_INDEX(subjects.name, ', ', 2) SEPARATOR ', ') as subjects"), // Limit to maximum 2 subjects
            DB::raw('GROUP_CONCAT(DISTINCT classes.name) as classNames'), // Concatenate class names
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating'),
            DB::raw('COUNT(tutorreviews.id) as total_reviews') // Count total reviews
        )
            ->distinct()
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->where('tutorregistrations.is_active', 1)
            ->groupBy(
                'tutorprofiles.name',
                'tutorprofiles.profile_pic',
                'tutorprofiles.rate',
                'tutorsubjectmappings.rate',
                'tutorsubjectmappings.admin_commission',
                'tutorprofiles.headline',
                'tutorprofiles.tutor_id',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission'
            )
            ->get();

        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();
        // Subject Categories with subjects count
        $subjectcategories = DB::table('subjectcategories')
            ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            ->where('subjectcategories.is_active', 1)
            ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            ->get();

        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());
        // return view('front-cms.index', compact('classes'));
    }

    public function tutordetails($id)
    {

        $tutorpd = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'))
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutorsubjectmappings.tutor_id', '=', $id)
            ->first();

        if ($tutorpd) {
            $achievement = tutorachievements::select('*')->where('tutor_id', '=', $tutorpd->id)->get();

            $reviews = tutorreviews::select('tutorreviews.id', 'tutorreviews.name', 'tutorreviews.ratings', 'studentprofiles.name as student_name', 'studentprofiles.profile_pic as student_pic', 'tutorreviews.subject_id', 'tutorreviews.tutor_id', 'subjects.name as subject')
                ->leftjoin('subjects', 'subjects.id', '=', 'tutorreviews.subject_id')
                ->leftjoin('studentprofiles', 'studentprofiles.student_id', '=', 'tutorreviews.student_id')
                ->where('tutorreviews.tutor_id', '=', $tutorpd->id)->get();
        }

        if (!$tutorpd) {
            return view('front-cms.tutordetails')->with('fail', 'Something went wrong');
        }
        $subjects = tutorSubjectMapping::select('tutorsubjectmappings.*', 'subjects.name as subject_name')
            ->join('subjects', 'subjects.id', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->get();

        // dd($reviews);
        $averagereview = tutorreviews::select(
            DB::raw('AVG(tutorreviews.ratings) as avg_rating')
        )
            ->where('tutorreviews.tutor_id', $id)
            ->first();

        $averagecount = tutorreviews::where('tutorreviews.tutor_id', $id)->count();
        $totalStudents = SlotBooking::where('tutor_id', $id)
            ->select('student_id') // Only select student_id
            ->distinct() // Ensure unique students
            ->get()->count();
        // dd($totalStudents);
        $primarysubjects = tutorSubjectMapping::select('tutorsubjectmappings.*', 'subjects.name as subject_name')
            ->join('subjects', 'subjects.id', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->first();

        $othertutors = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject')
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutorsubjectmappings.subject_id', '=', $primarysubjects->subject_id)
            ->get();

        // dd($othertutors);

        return view('front-cms.tutordetails', compact('tutorpd', 'achievement', 'reviews', 'subjects', 'averagecount', 'averagereview', 'totalStudents', 'othertutors', 'primarysubjects'));
    }

    public function registration(Request $request)
    {

        if ($request->id == "1") {
            $request->validate([
                'studentmobile' => 'required|min:4|max:11',
                'class' => 'required',
            ]);

            $user = new studentregistration();
            $user->mobile = $request->studentmobile;
            $user->role_id = "3";
            $user->class_id = $request->class;
            $user->parent_password = Hash::make($request->studentmobile);
            // $user->is_active = "1";
        } else {
            $request->validate([
                'tutormobile' => 'required|min:4|max:11',
            ]);

            $user = new tutorregistration();
            $user->mobile = $request->tutormobile;
            $user->role_id = "2";
            $user->is_active = "0";
        }
        $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'min:4|required_with:confirmpassword|same:confirmpassword',
            'confirmpassword' => 'min:4',

        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_active = "0";
        $user->password = Hash::make($request->password);

        $res = $user->save();
        if ($res) {
            return back()->with('success', 'Registration successfull');
            // return redirect('student/dashboard');
        } else {
            return back()->with('fail', 'Registration failed');
        }
    }

    // Login Controller Code

    public function std_login()
    {
        // event(new RealTimeMessage('Clicked On Student Login'));
        // event(new RealTimeMessage('This is a sample broadcast message.'));
        // Create notification
        // $notification = new Notification();
        // $notification->message = 'Hello Bro, Kaisa hai ?';
        // $notification->save();

        // Broadcast the new notification
        // broadcast(new RealTimeMessage('$notification'));
        // return view('common.student-login');
        return view('front-cms.login');
    }
    // Logout Controller
    public function logout()
    {

        if (session()->has('userid')) {
            session()->pull('userid');
            return redirect('/');
        } else {
            return redirect('/');
        }
    }

    // Student Calls
    public function student_login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'loginAs' => 'required',
        ]);
        if ($request->loginAs == 'student') {$user = studentregistration::where('mobile', '=', $request->username)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Student');
                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            return redirect('student/dashboard');
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }}
        if ($request->loginAs == 'parent') {
            $user = studentregistration::where('mobile', '=', $request->username)->first();

            if ($user) {
                if (Hash::check($request->password, $user->parent_password)) {
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Parent');
                    return redirect('student/dashboard');
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
        if ($request->loginAs == 'tutor') {
            $user = tutorregistration::where('mobile', '=', $request->username)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);

                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            return redirect('student/dashboard');
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }

    }
    public function std_registration()
    {

        // return view('common.student-register');
        return view('front-cms.register');
    }
    public function student_registration_form(Request $request)
    {

        // if ($request->id == "1") {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|min:4|max:13',
            'password' => 'required|min:8|max:50',
            'confpassword' => 'required|min:8|max:50|same:password',
            'expcheck' => 'required|accepted',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.min' => 'Mobile number must be at least 4 digits.',
            'mobile.max' => 'Mobile number must not exceed 13 digits.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 50 characters.',
            'confpassword.required' => 'Confirmation password is required.',
            'confpassword.min' => 'Confirmation password must be at least 8 characters.',
            'confpassword.max' => 'Confirmation password must not exceed 50 characters.',
            'confpassword.same' => 'Password and confirmation password must match.',
            'expcheck.required' => 'You must accept the terms.',
            'expcheck.accepted' => 'The terms must be accepted.',
        ]);

        if ($request->registerAs == 'student') {

            $user = studentregistration::where('mobile', '=', $request->mobile, )->first();
            // echo $user;
            // dd();
            if ($user) {
                return back()->with('fail', 'Mobile Already Registered');
            }

            $user = studentregistration::where('email', '=', $request->email, )->first();
            if ($user) {
                return back()->with('fail', 'Email Already Registered');
            }
            $user = new studentregistration();
            $user->mobile = $request->mobile;
            $user->role_id = "3";
            $user->class_id = '0';
            $user->name = $request->name;
            $user->email = $request->email;
            $user->is_active = "1";
            $user->password = Hash::make($request->password);
            $user->parent_password = Hash::make($request->mobile);
            $res = $user->save();

            $finduser = studentregistration::select('*')->where('mobile', $request->mobile)->first();
            $studentprofile = new studentprofile();
            $studentprofile->name = $request->name;
            $studentprofile->mobile = $request->mobile;
            $studentprofile->email = $request->email;
            $studentprofile->student_id = $finduser->id;
            // $studentprofile->profile_pic =
            $studentprofile->grade = 1;
            $studentprofile->save();

            // Send welcome mail
            $details = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'password' => $request->password,
                'mailtype' => 1,
            ];

            Mail::to($request->email)->send(new SendMail($details));
            // Send welcome mail ends here ..

            $mobile = $request->mobile;
            $formattedDate = Carbon::now()->format('Y-m-d');
            // Generate a random 4-digit OTP
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $username = 'BhashWAPAI';
            $pass = '123456';
            $sender = 'BUZWAP';
            // $phone = '+917004920897';
            $phone = $mobile;
            // $phone = $res->mobile;
            $text = 'delivery';
            $priority = 'wa';
            $stype = 'normal';
            $params = $otp . ',' . $formattedDate;

            $url = "https://bhashsms.com/api/sendmsg.php?user=$username&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

            // Initialize Guzzle client
            $client = new Client();

            try {
                // Send GET request to the URL
                $response = $client->get($url);

                // Get the response body
                $responseBody = $response->getBody();

                // You can process the response here
                // For example, you can log the response or return it to the view
                response()->json(['message' => 'OTP sent successfully', 'response' => $responseBody]);
                // return view('common.tutor-mobile-verify');
            } catch (\Exception $e) {
                // Handle any exceptions that occur during the request
                return response()->json(['message' => 'OTP sending failed', 'error' => $e->getMessage()], 500);
            }

            if ($res) {

                $studentRegistration = studentregistration::where('mobile', $mobile)->first();
                $studentRegistration->mobile_otp = '1234';
                // $studentRegistration->mobile_otp = $otp;
                $studentRegistration->save();

                return view('common.student-mobile-verify', compact('mobile'))->with('success', 'Registration successful. Please Login Now To Access More Features.');

                // return redirect('student/dashboard');
            } else {
                return back()->with('fail', 'Registration failed');
            }
        }
        if ($request->registerAs == 'tutor') {

            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required|min:4|max:11',
                'password' => 'required|min:8|max:50',
            ]);

            $user = tutorregistration::where('email', '=', $request->email, )->first();
            if ($user) {
                return back()->with('fail', 'Email Already Registered');
            }

            $user = tutorregistration::where('mobile', '=', $request->mobile, )->first();
            if ($user) {
                return back()->with('fail', 'Mobile Already Registered');
            }

            $user = new tutorregistration();
            $user->mobile = $request->mobile;
            $user->role_id = "2";
            $user->name = $request->name;
            $user->email = $request->email;
            $user->is_active = "0";
            $user->password = Hash::make($request->password);

            $res = $user->save();

            $checktutorid = tutorregistration::select('*')->where('mobile', $request->mobile)->first();
            $tprofile = new tutorprofile();
            $tprofile->name = $request->name;
            $tprofile->mobile = $request->mobile;
            $tprofile->email = $request->email;
            $tprofile->tutor_id = $checktutorid->id;
            $tprofile->qualification = " ";
            $tprofile->rateperhour = 0;
            $tprofile->admin_commission = 0;
            $tprofile->save();

            // Send welcome mail
            $details = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'password' => $request->password,
                'mailtype' => 1,
            ];

            Mail::to($request->email)->send(new SendMail($details));
            // Send welcome mail ends here ..

            $mobile = $request->mobile;

            $user = 'BhashWAPAI';
            $pass = '123456';
            $sender = 'BUZWAP';
            // $phone = '7004920897';
            $phone = $request->mobile;
            $text = 'delivery';
            $priority = 'wa';
            $stype = 'normal';
            $params = '1195,23aug2023';

            $url = "https://bhashsms.com/api/sendmsg.php?user=$user&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

            // Initialize Guzzle client
            $client = new Client();

            $mobile = $request->mobile;
            if ($res) {
                $user = tutorregistration::where('mobile', '=', $mobile)->first();

                // if (Hash::check($request->password, $user->password)) {
                //  event(new Registered($user));

                $user_role = Auth::user();
                // dd($user->role_id);
                $request->session()->put('userid', $user);
                // Notification Starts here
                $notificationdata = new Notification();
                $notificationdata->alert_type = 8;
                $notificationdata->notification = $request->name . " Registered as tutor and pending for approval";
                $notificationdata->initiator_id = session('userid')->id;
                $notificationdata->initiator_role = "2";
                $notificationdata->event_id = $user->id;
                // Sending to admin

                $notificationdata->show_to_admin = 1;
                $notificationdata->show_to_admin_id = 1;
                $notificationdata->show_to_all_admin = 1;

                $notificationdata->read_status = 0;

                $notified = $notificationdata->save();
                broadcast(new RealTimeMessage('$notification'));

                // Notification ends here
                switch ($user->role_id) {
                    case 1:
                        echo "Admin - Under development";
                        dd($user->role_id);
                        break;
                    case 2:
                        return redirect('tutor/dashboard');
                    case 3:
                        return redirect('student/dashboard');
                    case 4:
                        echo "Parent";
                        dd($user->role_id);

                        break;
                }
                // return redirect(RouteServiceProvider::HOME);
                // }

                // return view('common.tutor-mobile-verify', compact('mobile'))->with('success', 'Registration successful. Please Login Now To Access More Features.');

                // return redirect('student/dashboard');
            } else {
                return back()->with('fail', 'Registration failed');
            }

            // try {
            //     // Send GET request to the URL
            //     $response = $client->get($url);

            //     // Get the response body
            //     $responseBody = $response->getBody();

            //     // You can process the response here
            //     // For example, you can log the response or return it to the view
            //     return view('common.tutor-mobile-verify')->with('success','OTP sent successfully');
            // } catch (\Exception $e) {
            //     // Handle any exceptions that occur during the request
            //     return response()->json(['message' => 'OTP sending failed', 'error' => $e->getMessage()], 500);
            // }
        }
    }

    public function student_mobile_verify()
    {
        return view('common.student-mobile-verify');
    }

    public function verify_student_mobile(Request $request)
    {

        $request->validate([
            'digit1_input' => 'required',
            'digit2_input' => 'required',
            'digit3_input' => 'required',
            'digit4_input' => 'required',
            'mobile' => 'required',

        ]);
        $userotp = $request->digit1_input . $request->digit2_input . $request->digit3_input . $request->digit4_input;
        // echo $otp;
        // dd();
        $otp = studentregistration::select('*')->where('mobile', $request->mobile)->first();
        if ($userotp == $otp->mobile_otp) {
            return view('common.student-mobile-verified')->with('success', 'OTP Verified');
        } else {

            return back()->with('fail', 'Invalid OTP. Please try again');
        }
    }

    // Tutor Calls

    public function ttr_login()
    {
        // return view('common.tutor-login');
        return view('front-cms.login');
    }

    public function tutor_login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $user = tutorregistration::where('mobile', '=', $request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                //  event(new Registered($user));

                $user_role = Auth::user();
                // dd($user->role_id);
                $request->session()->put('userid', $user);

                switch ($user->role_id) {
                    case 1:
                        echo "Admin - Under development";
                        dd($user->role_id);
                        break;
                    case 2:
                        return redirect('tutor/dashboard');
                    case 3:
                        return redirect('student/dashboard');
                    case 4:
                        echo "Parent";
                        dd($user->role_id);

                        break;
                }
                // return redirect(RouteServiceProvider::HOME);
            }
            return back()->with('fail', 'Password does not match');
        } else {
            return back()->with('fail', 'Mobile No. Not Registered');
        }
    }
    public function ttr_registration()
    {

        return view('front-cms.register');
    }
    public function tutor_registration_form(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required|min:4|max:11',
            'password' => 'required|min:8|max:50',
        ]);

        $user = new tutorregistration();
        $user->mobile = $request->mobile;
        $user->role_id = "2";
        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_active = "0";
        $user->password = Hash::make($request->password);

        $res = $user->save();

        $mobile = $request->mobile;
        if ($res) {
            return view('common.tutor-mobile-verify', compact('mobile'))->with('success', 'Registration successful. Please Login Now To Access More Features.');

            // return redirect('student/dashboard');
        } else {
            return back()->with('fail', 'Registration failed');
        }
    }

    public function tutor_mobile_verify()
    {
        $user = 'BhashWAPAI';
        $pass = '123456';
        $sender = 'BUZWAP';
        $phone = '7004920897';
        $text = 'delivery';
        $priority = 'wa';
        $stype = 'normal';
        $params = '1195,23aug2023';

        $url = "https://bhashsms.com/api/sendmsg.php?user=$user&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

        // Initialize Guzzle client
        $client = new Client();

        try {
            // Send GET request to the URL
            $response = $client->get($url);

            // Get the response body
            $responseBody = $response->getBody();

            // You can process the response here
            // For example, you can log the response or return it to the view
            return view('common.tutor-mobile-verify')->with('success', 'OTP sent successfully');
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the request
            return response()->json(['message' => 'OTP sending failed', 'error' => $e->getMessage()], 500);
        }
        // return view('common.tutor-mobile-verify');
    }

    public function verify_tutor_mobile(Request $request)
    {
        // echo "test";
        // dd();
        $request->validate([
            'digit1_input' => 'required',
            'digit2_input' => 'required',
            'digit3_input' => 'required',
            'digit4_input' => 'required',
            // 'mobile' => 'required',

        ]);
        $otp = $request->digit1_input . $request->digit2_input . $request->digit3_input . $request->digit4_input;
        echo $otp;
        // dd();

        if ($otp == '1234') {
            return view('common.tutor-mobile-verified')->with('success', 'OTP Verified');
        } else {

            return back()->with('fail', 'Invalid OTP. Please try again');
        }
    }

    // parent authentications
    public function parent_login()
    {
        return view('front-cms.login');
    }
    public function parent_login_attempt(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $user = studentregistration::where('mobile', '=', $request->username)->first();

        if ($user) {
            if (Hash::check($request->password, $user->parent_password)) {
                $request->session()->put('userid', $user);
                $request->session()->put('usertype', 'Parent');
                return redirect('student/dashboard');
            }
            return back()->with('fail', 'Password does not match');
        } else {
            return back()->with('fail', 'Mobile No. Not Registered');
        }
    }

    public function notifications()
    {
        // Logged In User Role
        $logged_in_role = session('userid')->role_id;

        // Notification for Admin
        if ($logged_in_role == 1) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_admin_id', session('userid')->id)
                        ->orWhere('show_to_all_admin', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_admin_id', session('userid')->id)
                        ->orWhere('show_to_all_admin', 1);
                })
                ->count();
        }
        // Notification for Tutor
        if ($logged_in_role == 2) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_tutor_id', session('userid')->id)
                        ->orWhere('show_to_all_tutor', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_tutor_id', session('userid')->id)
                        ->orWhere('show_to_all_tutor', 1);
                })
                ->count();
        }
        // Notification for Student
        if ($logged_in_role == 3) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_student_id', session('userid')->id)
                        ->orWhere('show_to_all_student', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_student_id', session('userid')->id)
                        ->orWhere('show_to_all_student', 1);
                })
                ->count();
        }
        // Notification for Parent
        if ($logged_in_role == 4) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_parent_id', session('userid')->id)
                        ->orWhere('show_to_all_parent', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_parent_id', session('userid')->id)
                        ->orWhere('show_to_all_parent', 1);
                })
                ->count();
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {

        $updatestatus = Notification::find($id);
        $updatestatus->read_status = 1;
        $updatestatus->update();

        $notifications = Notification::orderBy('created_at', 'desc')->get();
        $unreadCount = Notification::where('read_status', 0)->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
    public function checkNotificationDetails($id)
    {
        $notificationData = Notification::find($id);
        //    dd($notificationData);
        // Notification Event On Chat
        if ($notificationData->alert_type == 1) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/adminmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/adminmessages/' . $notificationData->initiator_id);
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/tutormessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('/student/tutormessages/' . $notificationData->initiator_id);
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/studentmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/studentmessages/' . $notificationData->initiator_id);
                }

            }
            // Initiated by parent
            if ($notificationData->initiator_role == 4) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/studentmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/studentmessages/' . $notificationData->initiator_id);
                }
            }
        }
        // Notification Event On Trial Class
        if ($notificationData->alert_type == 2) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/demolist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/demolist');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/demolist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/demolist');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/demolist');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/demolist');
                }

            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Assignments
        if ($notificationData->alert_type == 3) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/assignments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/assignments');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/assignments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/assignments');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/assignments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/assignments');
                }

            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Quiz/Online Test
        if ($notificationData->alert_type == 4) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/onlinetestlist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/exams');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/onlinetestlist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/exams');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/onlinetestlist');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/onlinetestlist');
                }

            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Feedback
        if ($notificationData->alert_type == 5) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/feedback');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/myfeedback');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/dashboard');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/myfeedback');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/dashboard');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/feedback');
                }

            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }

        // Notification Event On Enrollment
        if ($notificationData->alert_type == 6) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/students');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/yourtutor');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/yourtutor');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/students');
                }

            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Slot Booking
        if($notificationData->alert_type == 7){

           // Slot Booked By Student
            if($notificationData->initiator_role == 3){

                if(session('userid')->role_id == 1){
                    return redirect()->to('admin/payments');
                }
                if(session('userid')->role_id == 2){
                    return redirect()->to('tutor/tutorslots');
                }
                if(session('userid')->role_id == 3){
                    return redirect()->to('student/enrollupdate/'.$notificationData->initiator_id);
                }

            }
            // Slot Booked By Student
            if($notificationData->initiator_role == 2){

                if(session('userid')->role_id == 1){
                    return redirect()->to('admin/payments');
                }
                if(session('userid')->role_id == 2){
                    return redirect()->to('tutor/tutorslots');
                }
                if(session('userid')->role_id == 3){
                    return redirect()->to('student/enrollupdate/'.$notificationData->initiator_id);
                }

            }
            
        }
        // Notification Event On Tutor Registration
        if ($notificationData->alert_type == 8) {

            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/tutors');
                }

            }

        }

    }
    public function reviewslist()
    {
        $reviews = tutorreviews::select('tutorreviews.*', 'subjects.name as subject_name', 'tutorprofiles.name as tutor_name', 'studentprofiles.name as student_name')
            ->join('subjects', 'subjects.id', 'tutorreviews.subject_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'tutorreviews.tutor_id')
            ->join('studentprofiles', 'studentprofiles.student_id', 'tutorreviews.student_id')
            ->where('tutorreviews.ratings', '>', 3)
            ->get();
        // dd($reviews);
        return view('front-cms.reviews', compact('reviews'));
    }
}
