@extends('front-cms.layouts.main')
@section('main-section')
    <!-- BANNER START -->
    <div class="tu-banner">
        <div class="container">
            <div class="row align-items-center g-0 gy-5">
                <div class="col-lg-6">
                    <div class="tu-banner_title">
                        <h1>A good <span>#education</span> is always a base of</h1>
                        <span class="tu-bannerinfo type"></span>
                        <p>Education forms the cornerstone of a bright career. It equips individuals with knowledge, skills, and the foundation to thrive in various fields. Investing in education opens doors to endless opportunities, empowering individuals to achieve their aspirations and contribute meaningfully to society.</p>
                        <ul class="tu-banner_list">
                            <li>
                                <div class="tu-starthere">
                                    <span>Start from here</span>
                                    <img src="{{url('frontend/images/knob_line.svg')}}" alt="img">
                                </div>
                                <a href="/student/register" class="tu-primbtn tu-primbtn-gradient"><span>Start as student</span><i class="icon icon-chevron-right"></i></a>
                            </li>
                            <li><a href="/tutor/register" class="tu-secbtn"><span>Join as Instructor</span><em>It’s Free!</em></a></li>
                        </ul>
                        <div class="tu-banner_explore">
                            <i class="icon icon-shield"></i>
                            <p>You can also join as parent to explore</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="tu-bannerv1_img">
                        <img src="{{url('frontend/images/index/banner/img-02.png')}}" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BANNER END -->
    <!-- MAIN START -->
	<main class="tu-main">
        <!-- BRANDS START -->
        <section>
            <div class="tu-brand">
                <div class="container">
                    <ul class="tu-brand_list">
                        <li><img src="{{url('frontend/images/brand/img-01.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-02.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-03.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-04.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-05.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-06.png')}}" alt="img"></li>
                        <li><img src="{{url('frontend/images/brand/img-07.png')}}" alt="img"></li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- BRANDS END -->

        <!-- PLATFORM START -->
        <section class="tu-main-section">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-md-12 col-lg-6">
                        <div class="tu-maintitle p-0">
                            <img src="{{url('frontend/images/zigzag-line.svg')}}" alt="img">
                            <h4>Better Learning. Better Results</h4>
                            <h2>Online education platform that fits for everyone</h2>
                            <p>Discover an online education platform tailored for everyone. Our versatile platform caters to diverse learning styles and preferences, providing a personalized and accessible educational experience. Whether you're a student, professional, or lifelong learner, our platform ensures a seamless and enriching educational journey for all.</p>
                            <a href="aboutus" class="tu-primbtn-lg"><span>Explore more about us</span><i class="icon icon-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="tu-betterresult">
                            <figure>
                                <img src="{{url('frontend/images/index/platform/img-01.png')}}" alt="image-description">
                            </figure>
                            <img src="{{url('frontend/images/index/platform/img-02.png')}}" alt="image-description">
                            <div class="tu-resultperson">
                                <h6>Founder & CEO</h6>
                                <h5>Mr. ABC</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- PLATFORM END -->
        <!-- COUNTER START -->
        <section>
            <div class="tu-statsholder">
                <div class="container">
                    <ul id="tu-counter" class="tu-stats">
                        <li>
                            <img src="{{url('frontend/images/stats/img-01.png')}}" alt="img">
                            <div class="tu-stats_info">
                                <h4><span data-from="0" data-to="50" data-speed="8000" data-refresh-interval="10">50</span>+ Courses</h4>
                                <p>Courses available from verified and top tutors</p>
                            </div>
                        </li>
                        <li>
                            <img src="{{url('frontend/images/stats/img-02.png')}}" alt="img">
                            <div class="tu-stats_info">
                                <h4><span data-from="0" data-to="100" data-speed="8000" data-refresh-interval="30">100</span>+ Quizes</h4>
                                <p>Online quiz & online tests to improve your skills</p>
                            </div>
                        </li>
                        <li>
                            <img src="{{url('frontend/images/stats/img-03.png')}}" alt="img">
                            <div class="tu-stats_info">
                                <h4><span data-from="0" data-to="10" data-speed="8000" data-refresh-interval="50">10</span>+ Hours</h4>
                                <p>User daily average time spent on the platform</p>
                            </div>
                        </li>
                        <li>
                            <img src="{{url('frontend/images/stats/img-04.png')}}" alt="img">
                            <div class="tu-stats_info">
                                <h4><span data-from="0" data-to="1000" data-speed="8000" data-refresh-interval="100">1000</span>+ Users</h4>
                                <p>Active instructor and students available on the platform</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- COUNTER END -->
        <!-- INSTRUCTOR START -->
        <section class="tu-main-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="tu-maintitle text-center">
                            <img src="{{url('frontend/images/zigzag-line.svg')}}" alt="img">
                            <h4>Our featured instructors</h4>
                            <h2>Every instructor is professional and highly qualified</h2>
                            <p>Our team of instructors comprises seasoned professionals, each holding high qualifications in their respective fields. We take pride in delivering quality education through experts who bring extensive knowledge and experience to ensure a valuable and enriching learning experience for every student.</p>
                        </div>
                    </div>
                </div>
                <div id="tu-featurelist" class="splide tu-featurelist  tu-splidedots ">
                    <div class="splide__track">
                        <ul class="splide__list">
                            @if(isset($tutorlists))
                            @foreach ($tutorlists as $tutorlist)
                            <li class="splide__slide">
                                <div class="tu-featureitem">
                                    <figure>
                                        <a href="tutor-detail.html" class="video-container">
                                            <iframe width="100%" height="100%" src="{{$tutorlist->intro_video_link}}" frameborder="0" allowfullscreen></iframe>
                                        </a>
                                        <span class="tu-featuretag">FEATURED</span>
                                    </figure>
                                    <div class="tu-authorinfo">
                                        <div class="tu-authordetail">
                                            <figure>
                                                <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorlist->profile_pic ?? url('images/avatar/default-profile-pic.png') }}" alt="image-description">
                                            </figure>
                                            <div class="tu-authorname">
                                                <h5><a href="{{url('tutor-details')}}/{{$tutorlist->id}}"> {{ $tutorlist->name }}</a>  <i class="icon icon-check-circle tu-greenclr" data-tippy-trigger="mouseenter" data-tippy-html="#tu-verifed" data-tippy-interactive="true" data-tippy-placement="top"></i></h5>
                                                <span>{{ $tutorlist->headline }}</span>
                                            </div>
                                            <ul class="tu-authorlist">
                                                <li>
                                                    <span>Starting from:<em>${{$tutorlist->rate}}/hr</em></span>
                                                </li>
                                                <li>
                                                    <span>Subjects:<em>{{ $tutorlist->subject }}</em></span>
                                                </li>
                                                <li>
                                                    <span>Classes:<em>{{$tutorlist->total_classes_done}}+</em></span>
                                                </li>
                                                <li>
                                                    <span>Qualification:<em>{{$tutorlist->tutor_qualification}}</em></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tu-instructors_footer">
                                            <div class="tu-rating">
                                                <i class="fas fa-star"></i>
                                                <h6>5.0</h6>
                                                <span>({{$tutorlist->starrating}})</span>
                                            </div>
                                            <div class="tu-instructors_footer-right">
                                                <a href="javascript:void(0);"><i class="icon icon-heart"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="tu-mainbtn">
                    <a href="{{'/findatutor'}}" class="tu-primbtn-lg"><span>Explore all instructors</span><i class="icon icon-chevron-right"></i></a>
                </div>
            </div>
        </section>
        <!-- INSTRUCTOR END -->

         <!-- SUCCESS START -->
        <section id="tu-sucesstorsection">
            <div class="tu-success-stories">
                <div class="container">
                    <div class="tu-sucesstor_pattren">
                        <img src="{{url('frontend/images/index/success_stories/pattren.svg')}}" alt="img">
                    </div>
                    <div class="row tu-sucesstorslider_title">
                        <div class="col-lg-8">
                            <div class="tu-maintitle">
                                <h2>See how our visitors & members made their <span>#Success Stories</span></h2>
                            </div>
                        </div>
                    </div>
                    <div id="tu-sucesstorslider" class="splide tu-splidearrow tu-sucesstorslider">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <li class="splide__slide">
                                    <div class="tu-sucesstor">
                                        <div class="tu-sucesstor_img">
                                            <figure>
                                                <img src="{{url('frontend/images/index/success_stories/img-01.jpg')}}" alt="img">
                                                <figcaption><img src="{{url('frontend/images/index/success_stories/comma.svg')}}" alt="img"></figcaption>
                                            </figure>
                                        </div>
                                        <div class="tu-sucesstor_title">
                                            <h3>I highly recommend this platform, amazing experience with fast delivery</h3>
                                            <blockquote>“ Their teaching method is conceptual, motivating and friendly. I can clear my doubt any time. They have very deep knowledge of subject and exam pattern, with all the guidance of their tutos, I scored 98% in Mathematics and 96% in Physics. And yet qualified in IIT MAINS with 12th rank. ”</blockquote>
                                            <h4>
                                                Leonard Sullivan
                                                <span>2nd Standard, Manchester UK</span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="splide__slide">
                                    <div class="tu-sucesstor">
                                        <div class="tu-sucesstor_img">
                                            <figure>
                                                <img src="{{url('frontend/images/index/success_stories/img-02.jpg')}}" alt="img">
                                                <figcaption><img src="{{url('frontend/images/index/success_stories/comma.svg')}}" alt="img"></figcaption>
                                            </figure>
                                        </div>
                                        <div class="tu-sucesstor_title">
                                            <h3>I highly recommend this platform, amazing experience with fast delivery</h3>
                                            <blockquote>“ Their teaching method is conceptual, motivating and friendly. I can clear my doubt any time. They have very deep knowledge of subject and exam pattern, with all the guidance of their tutos, I scored 98% in Mathematics and 96% in Physics. And yet qualified in IIT MAINS with 12th rank. ”</blockquote>
                                            <h4>
                                                Deanna Griffin
                                                <span>2nd Standard, Manchester UK</span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="splide__slide">
                                    <div class="tu-sucesstor">
                                        <div class="tu-sucesstor_img">
                                            <figure>
                                                <img src="{{url('frontend/images/index/success_stories/img-03.jpg')}}" alt="img">
                                                <figcaption><img src="{{url('frontend/images/index/success_stories/comma.svg')}}" alt="img"></figcaption>
                                            </figure>
                                        </div>
                                        <div class="tu-sucesstor_title">
                                            <h3>I highly recommend this platform, amazing experience with fast delivery</h3>
                                            <blockquote>“ Their teaching method is conceptual, motivating and friendly. I can clear my doubt any time. They have very deep knowledge of subject and exam pattern, with all the guidance of their tutos, I scored 98% in Mathematics and 96% in Physics. And yet qualified in IIT MAINS with 12th rank. ”</blockquote>
                                            <h4>
                                                Bruce Mccarthy
                                                <span>2nd Standard, Manchester UK</span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="splide__slide">
                                    <div class="tu-sucesstor">
                                        <div class="tu-sucesstor_img">
                                            <figure>
                                                <img src="{{url('frontend/images/index/success_stories/img-04.jpg')}}" alt="img">
                                                <figcaption><img src="{{url('frontend/images/index/success_stories/comma.svg')}}" alt="img"></figcaption>
                                            </figure>
                                        </div>
                                        <div class="tu-sucesstor_title">
                                            <h3>I highly recommend this platform, amazing experience with fast delivery</h3>
                                            <blockquote>“ Their teaching method is conceptual, motivating and friendly. I can clear my doubt any time. They have very deep knowledge of subject and exam pattern, with all the guidance of their tutos, I scored 98% in Mathematics and 96% in Physics. And yet qualified in IIT MAINS with 12th rank. ”</blockquote>
                                            <h4>
                                                Evelyn Mccoy
                                                <span>2nd Standard, Manchester UK</span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="splide__slide">
                                    <div class="tu-sucesstor">
                                        <div class="tu-sucesstor_img">
                                            <figure>
                                                <img src="{{url('frontend/images/index/success_stories/img-05.jpg')}}">
                                                <figcaption><img src="{{url('frontend/images/index/success_stories/comma.svg')}}"></figcaption>
                                            </figure>
                                        </div>
                                        <div class="tu-sucesstor_title">
                                            <h3>I highly recommend this platform, amazing experience with fast delivery</h3>
                                            <blockquote>“ Their teaching method is conceptual, motivating and friendly. I can clear my doubt any time. They have very deep knowledge of subject and exam pattern, with all the guidance of their tutos, I scored 98% in Mathematics and 96% in Physics. And yet qualified in IIT MAINS with 12th rank. ”</blockquote>
                                            <h4>Frederick Hicks
                                                <span>2nd Standard, Manchester UK</span>
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SUCCESS END -->
         <!-- CATEGORIES START -->
        <section class="tu-main-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="tu-maintitle text-center">
                            <img src="{{url('frontend/images/zigzag-line.svg')}}" alt="img">
                            <h4>Let’s make a quick start today</h4>
                            <h2>Choose from the top visited categories you may like</h2>
                            <p>Explore our top-visited categories tailored for your preferences. From trending topics to personalized recommendations, discover a curated selection that aligns with your interests and ensures an engaging experience. Choose from the best and elevate your exploration.</p>
                        </div>
                    </div>
                </div>
                <div id="tu-categoriesslider" class="splide tu-categoriesslider tu-splidedots">
                    <div class="splide__track">
                        <ul class="splide__list">
                            @foreach ($subjectcategories as $subjectcatgory)
                                
                            
                            <li class="splide__slide">
                                <a class="tu-categories_content" href="#getsubjects">
                                    <img src="{{$subjectcatgory->category_image}}" alt="img">
                                    <div class="tu-categories_title">
                                        <h6>{{$subjectcatgory->name}}</h6>
                                        <span>{{$subjectcatgory->subject_count}}+ Subjects</span>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="tu-mainbtn">
                    <a href="/allsubjects" class="tu-primbtn-lg"><span>Explore All categories</span><i class="icon icon-chevron-right"></i></a>
                </div>
            </div>
        </section>
         <!-- CATEGORIES END -->
    </main>
	<!-- MAIN END -->
 

	@endsection