@extends('student.layouts.main')
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

            @if (request()->has('message'))
                <div class="alert alert-success">
                    {{ urldecode(request()->get('message')) }}
                </div>
            @endif
            @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif
           <form action="{{route('student.exams-search')}}" method="POST">
            @csrf
                    <div class="row ">
                        <div class="col-md-3 mt-4">
                            <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-4">
                            <select name="subject_name" class="form-control" id="subject">
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-4">
                            <input type="text" class="form-control" placeholder="Enter Topic" name="topic">
                        </div>



                        <div class="col-md-3 mt-4">
                            <button class="btn  btn-primary" type="submit" style="float:right"> <span
                                class="fa fa-search"></span> Search</button>
                        </div>
                    </div>
                </form>



                <div id="" class="mt-3 listHeader page-title-box">
                    <h3>My Examination</h3>
                </div>



            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                    <thead class="">
                        <tr>
                            <th scope="col">S.No.</th>
                            <th scope="col">Class</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Topic</th>
                            <th scope="col">Exam Type</th>
                            <th scope="col">Exam Name</th>
                            <th scope="col">Exam Description</th>
                            <th scope="col">Attempts Pending</th>
                            <th scope="col">Exam Duration</th>
                            <th scope="col">Exam Start Date & Time</th>
                            <th scope="col">Exam End Date & Time</th>
                            @if (session('usertype') == 'Parent')
                                            @else
                            <th scope="col">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($exams as $exam)
                            {{-- @if ($exam->attemptsRemaining > 0) --}}
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $exam->class }}</td>
                                    <td>{{ $exam->subject }}</td>
                                    <td>{{ $exam->topic }}</td>

                                    <td>@if($exam->test_type ==1) Objective @else subjective @endif</td>

                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->description }}</td>
                                    <td>{{ $exam->attemptsRemaining }}</td>
                                    <td>{{ $exam->test_duration }} min</td>
                                    <td>{{ \Carbon\Carbon::parse($exam->test_start_date)->format('d-m-Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($exam->test_end_date)->format('d-m-Y h:i A') }}</td>

                                    {{-- <td><a href="{{ url('student/taketest') }}/{{ $exam->id }}"
                                            class="badge bg-success">Start Test</a></td> --}}
                                        @if (session('usertype') == 'Parent')
                                            @else
                                            <td><a @if($exam->test_type ==1) href="{{ url('student/taketest') }}/{{ $exam->id }}" @elseif($exam->test_type ==2)href="{{ url('student/taketest-subjective') }}/{{ $exam->id }} @endif"
                                                class="badge bg-success p-2">Start Test</a></td>
                                                @endif

                                </tr>
                                @php
                                    $i++;
                                @endphp
                            {{-- @endif --}}
                        @endforeach


                    </tbody>
                </table>
            </div>
            {{-- <div class="d-flex justify-content-center" id="paginationContainer">
                {!! $exams->links() !!}
            </div> --}}

            <br>
            <br>

            <div id="" class="mb-3 listHeader page-title-box">
                <h3 class="mt-3">Previous Exams</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                    <thead class="">
                        <tr>
                            <th scope="col">S.No.</th>
                            <th scope="col">Exam Type</th>
                            <th scope="col">Exam Name</th>
                            <th scope="col">Exam Description</th>
                            <th scope="col">Exam Duration</th>
                            <th scope="col">Exam Start Date & Time</th>
                            <th scope="col">Exam End Date & Time</th>
                            <th scope="col">Attempted On</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($extakens as $extaken)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td></td>
                                <td>{{ $extaken->exam_name }}</td>
                                <td>{{ $extaken->exam_description }}</td>
                                <td>{{ $extaken->duration }} min</td>
                                <td>{{  \Carbon\Carbon::parse($extaken->test_start_date)->format('d-m-Y h:i A')}}</td>
                                <td>{{  \Carbon\Carbon::parse($extaken->test_end_date)->format('d-m-Y h:i A')}}</td>
                                <td>{{  \Carbon\Carbon::parse($extaken->test_attempted_on)->format('d-m-Y h:i A')}}</td>
                                <td><a href="{{url('student/exam/report')}}/{{$extaken->id}}" class="badge bg-primary p-2"> Report</a></td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
        <!-- content-wrapper ends -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
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
        </script>
        <script>
            function updateTableAndPagination(data) {
                // $('#tableContainer').html(data.table);
                $('.users-table tbody').html(data.table);
                $('#paginationContainer').html(data.pagination);
            }

        </script>
    @endsection
