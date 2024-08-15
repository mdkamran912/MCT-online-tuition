@extends('tutor.layouts.main')
@section('main-section')


<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">a
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <style>
    .listHeader {
        display: flex;
        justify-content: space-between;
    }

    .moveRight {
        float: right;
    }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if (Session::has('fail'))
            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            @endif
            <div id="" class="mb-3 listHeader page-title-box">
                <h3>My Schedule Classes </h3>
                <a href="tutorslots"><button class="btn btn-sm btn-success"><i class="ri-calendar-todo-fill"></i>
                    View Slots</button></a>
            </div>

            <div class="mt-4 table-responsive" id="">

                <table class="table table-hover table-striped align-middle mb-0 ">
                    <thead>
                        <tr>
                            <th scope="col">S.No.</th>
                            <th scope="col">Status</th>
                            <th scope="col">Student</th>
                            <th scope="col">Class</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Slot Date</th>
                            <th scope="col">Slot Time
                            <th scope="col">Duration</th>
                            <th scope="col" class="text-center" >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($liveclasses as $liveclass)

                  <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td>{{ $liveclass->meeting_id }}</td> --}}
                            {{-- <td>{{ $liveclass->status }}</td> --}}
                            <td>
                                @if ($liveclass->status == 'confirmed' || $liveclass->status == 'Confirmed')
                                <span class="badge bg-success">Confirmed</span>
                                @elseif ($liveclass->status == 'waiting' || $liveclass->status == 'Waiting')
                                <span class="badge bg-primary">Waiting Confirmation</span>
                                @elseif ($liveclass->status == 'started' || $liveclass->status == 'Started')
                                <span class="badge bg-success">Started</span>
                                @elseif ($liveclass->status == 'cancelled' || $liveclass->status == 'Cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                                
                                @elseif ($liveclass->status == 'Completed' || $liveclasses->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                {{-- @elseif ($liveclasses->status == 8)
                                <span class="badge bg-primary">{{ $liveclasses->currentstatus }}</span> --}}
                                @endif
                            </td>
                            <td>{{ $liveclass->studentname }}</td>
                            <td>{{ $liveclass->classname }}</td>
                            <td>{{ $liveclass->subjectname }}</td>
                            <td>{{ \Carbon\Carbon::parse($liveclass->slotdate)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($liveclass->slottime)->format('h:i A') }}</td>
                            <td>{{ $liveclass->duration }} min</td>

                            <td class="btns gap-2 text-nowrap">
                                @if ($liveclass->status == 'confirmed' || $liveclass->status == 'Confirmed')
                                <button class="btn btn-sm btn-success "
                                    onclick="warningModal('{{$liveclass->liveclass_id}}','{{$liveclass->start_url}}')"><span
                                        class="fa fa-play-circle "></span> Start Class</button>
                                @endif
                                @if ($liveclass->status == 'started' || $liveclass->status == 'Started')
                                <button class="btn btn-sm btn-success "
                                    onclick="warningModal('{{$liveclass->liveclass_id}}','{{$liveclass->start_url}}')"><span
                                        class="fa fa-play-circle "></span> Join Class</button>
                                @endif
                                @if ($liveclass->status == 'started' || $liveclass->status == 'Started')
                                <!-- <a href="{{url('tutor/liveclass/completed').'/'.$liveclass->liveclass_id}}">
                                        <button class="btn btn-sm btn-primary"><span
                                                class="fa fa-check "></span> Mark Completed</button>
                                    </a> -->
                                <button class="btn btn-sm btn-primary" onclick="openMarkModal({{$liveclass->liveclass_id}});"><span
                                        class="fa fa-check "></span> Mark Completed</button>
                                @endif
                            </td>
                            {{-- <td><button class="btn btn-sm btn-primary"

                                    onclick="openstudentmodal({{ $liveclass->batch_id }});"><span
                                class="fa fa-search"></span> Start Class</button>
                            </td> --}}
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{-- {!! $demos->links() !!} --}}
                </div>
                {{-- <form action="{{ route('tutor.liveclass.store') }}" method="POST">
                @csrf
                <input type="text" id="url" name="url" value="{{ url()->full() }}">{{ url()->full('code') }}
                <button type="submit" class="success">Submit</button>
                </form> --}}
                <br>

                {{-- <form action="{{ route('tutor.liveclass.getuser') }}" method="GET">
                @csrf
                <input type="text" id="zuser" name="zuser"><button type="submit" class="success">Submit</button>
                </form> --}}

            </div>
        </div>
        <!-- content-wrapper ends -->


        <!--Student List modal -->
        <div class="modal fade" id="studentlistmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4">Student List</h3>
                        </header>

                        <form action="" method="">
                            <div class="row">
                                <div class="col-12 col-md-12 col-ms-12 mb-3">
                                    {{-- <select id="studentlist" name="studentlist[]" multiple>

                         </select> --}}
                                    <style>
                                    .newclass td,
                                    .newclass th {
                                        padding: 2px !important
                                    }
                                    </style>
                                    <table class="table table-bordered newclass" style="margin: 0%;">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Student Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentlist">

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                data-dismiss="modal"><span class="fa fa-times"></span> Close</button>



                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!--Schedule modal -->
        <div class="modal fade" id="scheduleclassmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4">Schedule New Class</h3>
                        </header>

                        <form action="{{ route('tutor.liveclass.scheduleclass') }}" method="POST">
                            @csrf
                            <div class="row">

                                <div class="col-12 col-md-4 col-ms-6 mb-3">
                                    <label>Class/Grade<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="class" name="class"
                                        onchange="fetchSubjects()">
                                        @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('class')
                                        {{ 'Class is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-4 col-ms-6 mb-3">
                                    <label>Subject<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="subject" name="subject"
                                        onchange="fetchTopics();batchbysubject()">

                                    </select>
                                    <span class="text-danger">
                                        @error('subject')
                                        {{ 'Subject is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-4 col-ms-6 mb-3">
                                    <label>Batch<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="batchid" name="batchid">

                                    </select>
                                    <span class="text-danger">
                                        @error('batchid')
                                        {{ 'Batch is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Topic<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="topic" name="topic">

                                    </select>
                                    <span class="text-danger">
                                        @error('topic')
                                        {{ 'Topic is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Start Time<span style="color:red">*</span></label>
                                    <input type="datetime-local" class="form-control" id="classstarttime"
                                        name="classstarttime">
                                    <span class="text-danger">
                                        @error('classstarttime')
                                        {{ 'Class start time is required' }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Duration(minutes)<span style="color:red">*</span></label>
                                    <input type="tet" class="form-control" id="classduration" name="classduration">
                                    <span class="text-danger">
                                        @error('classduration')
                                        {{ 'Class duration is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Password<span style="color:red">*</span></label>
                                    <input type="tet" class="form-control" id="classpassword" name="classpassword">
                                    <span class="text-danger">
                                        @error('classpassword')
                                        {{ 'Class password is required' }}
                                        @enderror
                                    </span>
                                </div>
                            </div>


                            <div style="float:right">
                                <button type="button" class="btn btn-sm btn-danger mr-1 " data-dismiss="modal"
                                    onclick="closeModal();">Close</button>
                                <button type="submit" id="" class="btn btn-sm btn-success ">Submit</button>

                            </div>



                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--recording warning modal -->
        <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">

                        <header>
                            <h3 class="text-center mb-4 text-danger"><u>Warning!</u></h3>
                        </header>

                        <h4 class="">Please Make Sure To Start The <span style="color: red"><i
                                    class="ri-record-circle-fill"></i> Recording</span></h4>
                        <br>
                        <p class=""><span class="text-primary"><b>Hint To Start Recording :</b></span> <br>Step 1: Start
                            The Class<br>Step 2: Click On 3 vertical dots(<i class="ri-more-2-fill"></i>)<br>Step 3:
                            Manage Recordings<br>Step 4: Start Recording<br>Step 5: Start(Sometimes A Consent Message
                            Will Display)</p>
                        <div id='warningbtn' style="float:right">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="markcompleted" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Recorded Video Link</h5>
                    </div>
                    <form id="change-class-status">
                        <input type="hidden" id="liveclass-id" name="class_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 col-12 col-sm-12">
                                    <input type="text" class="form-control" placeholder="Paste Video Link Here" name="video_link">
                                </div>
                            </div>
                            <div style="float:right; margin-top:5px; margin-bottom:5px">
                                <button class="btn btn-sm btn-success">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function closeModal() {
        $('#scheduleclassmodal').modal('hide');
    }

    function openMarkModal(class_id) {
        $('#liveclass-id').val(class_id);
        $('#markcompleted').modal('show');
    }

    function openclassmodal(batchid, subjectid) {
        $('#batchid').val(batchid);
        $("#topic").html('');
        $.ajax({
            url: "{{ url('fetchtopics') }}",
            type: "POST",
            data: {
                subject_id: subjectid,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#topic').html('<option value="">-- Select Topic --</option>');
                $.each(result.topics, function(key, value) {
                    $("#topic").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

        $('#scheduleclassmodal').modal('show')

    }

    function openstudentmodal(id) {
        var classId = $('#classname option:selected').val();
        $("#subject").html('');
        $.ajax({
            url: "{{ url('tutor/batches/students') }}/" + id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                // console.log(result)
                $('#studentlist').html('');
                $.each(result, function(key, value) {
                    // $("#studentlist").append(value.name);
                });
                var table = "";
                var p = 0;
                for (var i in result) {
                    p++;
                    table += "<tr>";
                    table += "<td hidden>" +
                        result[i].id + "</td>" +
                        "<td>" + p + "</td>" +
                        "<td>" + result[i].name + "</td>";
                    table += "</tr>";
                }

                document.getElementById("studentlist").innerHTML = table;
            }

        });
        // $('#studentlist').val()
        $('#studentlistmodal').modal('show')

    }

    function fetchSubjects() {

        var classId = $('#class option:selected').val();
        $("#subject").html('');
        $("#topic").html('');
        $.ajax({
            url: "{{ url('fetchsubjects') }}",
            type: "POST",
            data: {
                class_id: classId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#subject').html('<option value="">-- Select Type --</option>');
                $.each(result.subjects, function(key, value) {
                    $("#subject").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    function fetchTopics() {

        var subjectId = $('#subject option:selected').val();
        $("#topic").html('');
        $.ajax({
            url: "{{ url('fetchtopics') }}",
            type: "POST",
            data: {
                subject_id: subjectId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#topic').html('<option value="">-- Select Type --</option>');
                $.each(result.topics, function(key, value) {
                    $("#topic").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    function batchbysubject() {

        var subjectId = $('#subject option:selected').val();
        $("#batchid").html('');
        $.ajax({
            url: "{{ url('batchbysubject') }}",
            type: "POST",
            data: {
                subject_id: subjectId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#batchid').html('<option value="">-- Select Type --</option>');
                $.each(result.batches, function(key, value) {
                    $("#batchid").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    //     function warningModal(link){
    //     document.getElementById('warningbtn').innerHTML = `<a href="${link}"><button class="btn btn-sm btn-success">Ok</button></a>`;
    //     $('#warningModal').modal('show');

    // }
    function warningModal(id, link) {

        var url = "{{ URL('tutor/liveclass/status/update') }}";
        // var id=
        $.ajax({
            url: url,
            type: "GET",
            cache: false,
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status
            },
            success: function(dataResult) {
                dataResult = JSON.parse(dataResult);
                if (dataResult.statusCode) {

                    toastr.success('status changed')
                    document.getElementById('warningbtn').innerHTML =
                        `<a href="${link}" target="_blank"><button class="btn btn-sm btn-success">Ok</button></a>`;
                    $('#warningModal').modal('show');

                } else {
                    alert("Something went wrong. Please try again later");
                }

            }
        });

    }
    </script>
    <script>
        $(document).ready(function () {
            $("#change-class-status").submit(function (e) {
                e.preventDefault();
              var class_id = $("#liveclass-id").val();
              const ajaxUrl = "{{ url('tutor/liveclass/completed') }}/" + class_id;
              var formData = $(this).serialize();

                // Send an AJAX request
                $.ajax({
                    type: "POST", // Change to the appropriate HTTP method (GET, POST, etc.)
                    url: ajaxUrl, // Replace with your server-side endpoint URL
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        $('#markcompleted').modal('hide');

                    },
                    error: function (xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, errorMessages) {
                                $.each(errorMessages, function (index, errorMessage) {
                                    toastr.error(errorMessage);
                                });
                            });
                        }
                    },
                });
            });
        });
    </script>
@endsection

