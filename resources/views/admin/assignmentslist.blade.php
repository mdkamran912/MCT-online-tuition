@extends('admin.layouts.main')
@section('main-section')
<meta name="csrf-token" content="{{ csrf_token() }}">


        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <style>
                .listHeader {
                    display: flex;
                    justify-content: space-between;
                }
            </style>

            <div class="page-content">
                <div class="container-fluid">
        @if (Session::has('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
        @endif
        @if (Session::has('fail'))
        <div class="alert alert-danger">{{Session::get('fail')}}</div>
        @endif
        <div id="listHeader">
            <h3 class="mb-3">Student's Assignments</h3>

        </div>

        <form id="payment-search">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text"  class="form-control" name="assaignment_name" id="sname" placeholder="Assignment Name">

                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                            <option value="">Select Class</option>
                            @foreach ($classes as $class)
                                <option  value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <select name="subject_name" class="form-control" id="subject" onchange="fetchTopics()">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option  value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <select  class="form-control" name="topic_name" id="topicid">
                            <option value="">Select Topic</option>
                            @foreach ($topics as $topic)
                                <option  value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <select  class="form-control" name="assigned_by" id="assgnBy">
                            <option value="">Assigned By</option>
                            @foreach ($tutors as $tutor)
                                <option  value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                            <select  class="form-control" name="status_field" id="class">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="2">In Active</option>
                            </select>
                    </div>
                </div>

            </div>
            <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                            <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="smob" placeholder="Student Mobile">

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">

                            </div>
                        </div>


                        <div class="col-md-8 mt-4">
                            <div class="form-group">
                            <button class="btn btn-primary" style="float:right"> <span
                                class="fa fa-search"></span> Search</button>
                            </div>
                        </div>
            </div>

        </form>
        <hr>



         <div class="table-responsive">
            <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                <thead>
                    <tr>
                        <th scope="col">S.No.</th>
                        <th scope="col">Assignment Name </th>
                        <th scope="col">Class/Grade</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Topic</th>
                        <th scope="col">Assigned By</th>
                        <th scope="col">Assigned On</th>
                        <th scope="col">Assignment End Date</th>
                        {{-- <th scope="col">Assignment To</th> --}}
                        <th scope="col">View Submissions</th>
                        <th>Status</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $datalist)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$datalist->assignment_name}}</td>
                            <td>{{$datalist->class_name}}</td>
                            <td>{{$datalist->subject_name}}</td>
                            <td>{{$datalist->topic_name}}</td>
                            <td><a href="{{url('admin/tutorprofile').'/'.$datalist->tutor_id}}">{{$datalist->tutor_name}}</td>
                            <td>{{$datalist->assignment_start_date}}</td>
                            <td>{{$datalist->assignment_end_date}}</td>
                            {{-- <td>{{$datalist->assigned_to}}</td> --}}
                            <td><div class="text-center"> <a href="{{url('admin/assignments/').'/'.$datalist->assignment_id}}" class="badge bg-primary">View</a></div></td>
                            <td>
                                <div class="form-check form-switch">
                                    @if ($datalist->assignment_status == 1)
                                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                    @else
                                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                    @endif
                                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{$datalist->assignment_id}}','{{$datalist->assignment_status}}');" class="checkbox" @if ($datalist->assignment_status == 1) then checked

                                    @endif>
                                </div>
                            </td>


                        </tr>
                    @endforeach
                </tbody>



            </table>
        </div>


<!-- content-wrapper ends -->
<div class="d-flex justify-content-center" id="paginationContainer">
    {!! $data->links() !!}
</div>





    </div>


<!-- login modal -->

<div class="modal fade" id="popUpVideoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="text-center mt-3">
        <h5 class="modal-title" id="exampleModalLabel"> Sample Video</h5>
    </div>
    <div class="modal-body">
        <iframe width="100%" height="300px" src="https://www.youtube.com/embed/n5FNuytDFFE"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>


    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


    </div>
    <!-- <div class="modal-body">
                <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
            </div> -->
</div>
</div>
</div>

<div class="modal fade" id="studyMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
    <div class="text-center mt-3">
        <h5 class="modal-title" id="exampleModalLabel">Study Content</h5>
    </div>
    <div class="modal-body">
        <iframe width="100%" height="500px"
            src="assets/studyMaterial/10_maths_key_notes_ch_01_real_numbers.pdf"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>


    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


    </div>
    <!-- <div class="modal-body">
                    <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
                </div> -->
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function changestatus(id,status){

        var url = "{{URL('admin/assignments/status')}}";
        // var id=
		$.ajax({
			url: url,
			type: "GET",
			cache: false,
			data:{
                _token:'{{ csrf_token() }}',
				id:id,
                status:status
			},
			success: function(dataResult){
                dataResult = JSON.parse(dataResult);
             if(dataResult.statusCode)
             {
                window.location = "/admin/assignments";
             }
             else{
                 alert("Something went wrong. Please try again later");
             }

			}
		});

    }
    function fetchSubjects() {

        var classId = $('#classname option:selected').val();
        $("#subject").html('');
        $.ajax({
            url: "{{ url('fetchsubjects') }}",
            type: "POST",
            data: {
                class_id: classId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#subject').html('<option value="">-- Select Subject --</option>');
                $.each(result.subjects, function(key, value) {
                    $("#subject").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };
    function fetchTopics() {

        var subjectId = $('#subject option:selected').val();
        $("#topicid").html('');
        $.ajax({
            url: "{{ url('fetchtopics') }}",
            type: "POST",
            data: {
                subject_id: subjectId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result)
                $('#topicid').html('<option value="">-- Select Topic --</option>');
                $.each(result.topics, function(key, value) {
                    $("#topicid").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }
        });
    };
    </script>
    <script>
        function updateTableAndPagination(data) {
            // $('#tableContainer').html(data.table);
            $('.users-table tbody').html(data.table);
            $('#paginationContainer').html(data.pagination);
        }

        $(document).ready(function () {
            $('#payment-search').submit(function (e) {
                e.preventDefault();
                const page = 1;
                const ajaxUrl = '{{ route("admin.assignments-search") }}'
                var formData = $(this).serialize();

                formData += `&page=${page}`;

                $.ajax({
                    type: 'post',
                    url: ajaxUrl, // Define your route here
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (data) {
                        // console.log(data)
                        updateTableAndPagination(data);
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            });


            $(document).on('click', '#paginationContainer .pagination a', function (e) {
            e.preventDefault();
            var formData = $('#payment-search').serialize();
            const page = $(this).attr('href').split('page=')[1];
            formData += `&page=${page}`;
            $.ajax({
                type: 'post',
                url: '{{ route("admin.assignments-search") }}', // Define your route here
                data:formData,
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                success: function (data) {
                    updateTableAndPagination(data);
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });



        });
    </script>

@endsection
