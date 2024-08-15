@extends('admin.layouts.main')
@section('main-section')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::has('fail'))
            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
        @endif
            <!-- <h3 class="text-center"></h3> -->
            <div id="listHeader" class="mb-3">
                <h3>Payment History</h3><br>
                <!-- <a class="btn btn-sm btn-primary" href="createtestseries.html"> <span class="fa fa-plus"></span>
                                Add New
                                Test Series</a> -->


            </div>
            <div id="listHeader" class="mb-3">

                <form  id="payment-search"  class="form-inline">
                    {{-- method="post" action="{{route('admin.paymentsearch')}}" --}}
                    @csrf
                    <div class="form-group">
                        <select name="student_name" class="form-control mr-2">
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                                <option  value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="class_name" class="form-control mr-2">
                            <option value="">Select Class</option>
                            @foreach ($classes as $class)
                                <option  value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="subject_name" class="form-control mr-2">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option  value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="tutor_name" class="form-control mr-2">
                            <option value="">Select Tutor</option>
                            @foreach ($tutors as $tutor)
                                <option  value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date"  name="start_date" class="form-control mr-2">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date"  class="form-control mr-2">
                    </div>
                    <button type="submit"  class="btn btn-primary">Search </button>
                </form>
            </div>





            <table class="table table-hover table-bordered table-responsive users-table">
                <thead class="thead-dark ">
                    <tr>
                        <th scope="col">S.No</th>
                        <th>Student Name</th>
                        <th scope="col">Class</th>
                        <th scope="col">Subject</th>
                        <th>Tutor</th>
                        <th>Transaction Date</th>
                        <th>Trasaction Id</th>
                        <th>Amount Paid</th>
                        <th>Mode Of Payment</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}
                            <td><a href="studentprofile/{{$payment->student_id}}">{{ $payment->student_name }}</a></td>
                            <td>{{ $payment->class_name }}</td>
                            <td>{{ $payment->subject_name }}</td>
                            <td><a href="tutorprofile/{{$payment->tutor_id}}">{{ $payment->tutor_name }}</a></td>
                            <td>{{ $payment->transaction_date }}</td>
                            <td>{{ $payment->transaction_no }}</td>
                            <td>{{ $payment->transaction_amount }}</td>
                            <td>{{ $payment->payment_mode }}</td>
                            @if ($payment->transaction_status_id == "3")
                            <td><span  class="badge bg-success">{{ $payment->transaction_status }}</span></td>

                            @elseif ($payment->transaction_status_id == "5")
                            <td><span  class="badge bg-danger">{{ $payment->transaction_status }}</span></td>
                            @else
                            <td><span  class="badge bg-primary">{{ $payment->transaction_status }}</span></td>
                            @endif

                            <td>{{ $payment->remarks }}</td>
                            <td><button class="badge bg-primary"
                                onclick="openmodal('{{ $payment->transaction_id }}','{{ $payment->transaction_no }}','{{ $payment->student_name }}','{{ $payment->transaction_status_id }}','{{ $payment->remarks }}');">Update</button>

                        </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="d-flex justify-content-center" id="paginationContainer">
                {!! $payments->links() !!}
            </div>
        </div>
        <!-- content-wrapper ends -->

        <!-- modal -->
        <div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">
                        <h3 class="text-center mb-3"><u>Update Payment Status</u></h3>
                        <form action="{{ route('admin.payments.update') }}" method="POST">
                            @csrf
                            <div class=" row">
                                <div class="form-group col-md-6">
                                    <label for="">Transaction No.</label>
                                    <input type="hidden" class="" id="transactionid" name="transactionid">
                                    <span class="text-danger">
                                        @error('transactionid')
                                            {{ 'Transaction Id is required' }}
                                        @enderror
                                    </span>
                                    <input type="text" class="form-control" id="transactionno" name="transactionno" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Student Name</label>
                                    <input type="text" class="form-control" id="studentname" name="studentname" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Payment Status<i style="color:red">*</i></label>
                                    <select type="text" class="form-control" id="paymentstatus" name="paymentstatus" required>
                                        @foreach ($statuses as $status)
                                            <option value="{{$status->id}}">{{$status->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('paymentstatus')
                                            {{ 'Status is required'}}
                                        @enderror
                                    </span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Remarks<i style="color:red">*</i></label>
                                    <input type="text" class="form-control" id="transactionremarks" name="transactionremarks" required>
                                    <span class="text-danger">
                                        @error('transactionremarks')
                                            {{ 'Remarks is required' }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <button type="submit" id="" class="btn btn-sm btn-primary float-right"><span
                                class="fa fa-check"></span>
                                Update</button>
                                <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" data-dismiss="modal"><span
                                    class="fa fa-times"></span> Close</button>
                                </form>

                    </div>

                </div>
            </div>
        </div>
        <script>
            function openmodal(id,txnno,stdn,ps,txnr){
                $('#transactionid').val(id);
                $('#transactionno').val(txnno);
                $('#studentname').val(stdn);
                $('#paymentstatus').val(ps);
                $('#transactionremarks').val(txnr);
                $('#popupModal').modal('show')
            }
            </script>
             <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        const ajaxUrl = '{{ route("admin.paymentsearch") }}'
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

                    const page = $(this).attr('href').split('page=')[1];

                    $.ajax({
                        type: 'post',
                        url: '{{ route("admin.paymentsearch") }}', // Define your route here
                        data: {
                            student_name: $('select[name="student_name"]').val(),
                            class_name: $('select[name="class_name"]').val(),
                            tutor_name: $('select[name="tutor_name"]').val(),
                            subject_name: $('select[name="subject_name"]').val(),
                            end_date : $('input[name="end_date"]').val(),
                            start_date : $('input[name="start_date"]').val(),
                            page: page
                        },
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
