<!doctype html>
<!--[if lt IE 7]>       <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>          <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>          <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="zxx">
<!--<![endif]-->

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>My Choice Tutor</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="apple-touch-icon.html">
	<link rel="icon" href="{{url('frontend/images/favicon.png')}}" type="image/x-icon">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
	{{-- <link rel="stylesheet" href="{{url('frontend/css/bootstrap.min.css')}}"> --}}
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css">
	{{-- <link rel="stylesheet" href="{{url('frontend/css/feather.css')}}"> --}}
    <link rel="stylesheet" href="{{url('frontend/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{url('frontend/css/splide.min.css')}}">
	<link rel="stylesheet" href="{{url('frontend/css/main.css')}}">
	{{-- <link rel="stylesheet" href="{{url('frontend/css/fontawesome/fontawesome.css')}}"> --}}
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Preloader Start -->
    {{-- <div class="tu-preloader">
        <div class="tu-preloader_holder">
            <img src="{{url('frontend/images/favicon.png')}}" alt="laoder img">
            <div class="tu-loader"></div>
        </div>
    </div> --}}
    <!-- Preloader End -->
	<!-- HEADER START -->
    <header class="tu-header">
        <nav class="navbar navbar-expand-xl tu-navbar">
            <div class="container-fluid">
                <strong>
                    <a class="navbar-brand" href="/"><img src="{{url('frontend/images/logo.png')}}" alt="Logo"></a>
                </strong>
                <button class="tu-menu"  aria-label="Main Menu" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse">
                    <i class="icon icon-menu"></i>
                </button>
                <div class="collapse navbar-collapse tu-themenav" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                      
                        <li class="nav-item">
                            <a class="nav-link active" href="{{('/findatutor')}}">Find a tutor<span class="tu-tag">NEW</span>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{('/learningcontents')}}">Learning material<span class="tu-tag tu-bggreen">FREE</span></a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{('/howitworks')}}">How it works</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{('/aboutus')}}">About us</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{('/contact')}}">Contact us</a>
                        </li> --}}
                        <li class="menu-item-has-children nav-item">
                            <a class="" href="javascript:void(0);">Academics</a>
                            <ul class="sub-menu">
                                {{-- <li class="menu-item-has-children">
                                    <a href="javascript:void(0)">Home Pages</a>
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="/">Home</a>
                                        </li>
                                        <li>
                                            <a href="index-without-login.html">Home v2 <em class="tu-menutag">without login</em></a>
                                        </li>
                                        <li>
                                            <a href="indexv2.html">Home v3</a>
                                        </li>
                                        <li>
                                            <a href="indexv3.html">Home v4</a>
                                        </li>
                                    </ul>
                                </li> --}}
                               
                                <li>
                                    <a href="{{('/allsubjects')}}">Subjects</a>
                                </li>
                                {{-- <li>
                                    <a href="{{('/courses')}}">Courses</a>
                                </li>
                                <li>
                                    <a href="{{('/quizes')}}">Quizes</a>
                                </li>
                                <li>
                                    <a href="{{('/studymaterials')}}">Study Materials</a>
                                </li> --}}
                                <li>
                                    <a href="#blogs">Blogs</a>
                                    {{-- <a href="{{('/blog')}}">Blogs</a> --}}
                                </li>
                                
                            </ul>
                        </li>
                       
                        <li class="nav-item tu-loginlink">
                            <a class="nav-link" href="tel:+91-1234567890">+91-1234567890</a>
                        </li>
                        
                        @if(session('userid') && session('userid')->id)
                            <li class="nav-item tu-loginlink">
                                <a class="nav-link" href="/logout">Logout</a>
                            </li>
                        @else
                            <li class="nav-item tu-loginlink">
                                <a class="nav-link" href="/student/login">Login</a>
                            </li>
                        @endif

                    </ul>
                </div>
                @if(session('userid') && session('userid')->id)
                <div class="tu-headerbtn">
                    <a href="student/dashboard" class="tu-primbtn"><span>Dashboard</span></a>
                </div>
                        @else
                        <div class="tu-headerbtn">
                            <a href="{{('/student/register')}}" class="tu-primbtn"><span>Join Us Today</span></a>
                        </div>
                        @endif
                
            </div>
        </nav>
    </header>
	<!-- HEADER END -->