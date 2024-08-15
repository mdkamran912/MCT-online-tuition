@extends('front-cms.layouts.main')
@section('main-section')
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader">
                <h1 class="mt-4">
                    Learn whatever you want
                </h1>
                <p class="charcol text-center mb-5">Our Subjects</p>


            </div>
        </div>
        </div>
    </section>



    <!-- -----------testimonial---------- -->
    <section class="testimonial-sec">
        <div class="container topheader">
            <h2 class="my-5">Review</h2>
            <div class="row">
                @foreach ($reviews as $review)
                    
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb-4">
                    <div class="testi-card">
                        <span class="nameTo">
                            {{$review->tutor_name}}
                            <p>
                                {{$review->subject_name}} tutor
                                <br>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </p>
                        </span>
                        <p class="mt-4">“{{$review->name}}”</p>
                        <p class="nameFrom">{{$review->student_name}}</p>
                    </div>
                </div>
                @endforeach

            </div>

        </div>
    </section>


    <section>
        <div class="subj-bottom-banner">

            <div class="row">
                <div class="col-md-6 banncol">



                    <div class="bannText">
                        <h2>Is MCT the right fit for you?<br>
                            There is only one way to find out.</h2>

                        <button class="orange-btn">Register Now</button>
                    </div>

                </div>
                <div class="col-md-6">
                    <img src="img/subj-bann-img.png" alt="">
                </div>
            </div>

        </div>



    </section>
 
    @endsection