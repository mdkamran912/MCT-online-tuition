@extends('student.layouts.main')
@section('main-section')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="card chatPannel">
                <div class="row g-0">
                    <div class="col-12 col-lg-5 col-xl-3 border-right">
                        <a href="{{ route('student.messages.tutor') }}"> <button
                                class="badge badge-primary">Tutors</button></a>
                        <a href="{{ route('student.messages.admins') }}"> <button class="badge badge-primary">Admin</button></a>

                        <div class="px-4 d-none d-md-block">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control my-3" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                        {{-- <div style=" display: flex; flex-direction: column;   max-height: 500px;overflow-y:scroll; "> --}}
                        {{-- Populating chat user list --}}
                        @foreach ($userlists as $userlist)
                            @if ($userlist->role_id == 1)
                                <a href="{{ url('student/adminmessages') }}/{{ $userlist->id }}"
                                    class="list-group-item list-group-item-action border-0">
                                @elseif ($userlist->role_id == 2)
                                <a href="{{ url('student/tutormessages') }}/{{ $userlist->id }}"
                                    class="list-group-item list-group-item-action border-0">
                                @elseif ($userlist->role_id == 3)
                                    <a href="{{ url('tutor/studentmessages') }}/{{ $userlist->id }}"
                                        class="list-group-item list-group-item-action border-0">
                            @endif

                            <div class="d-flex align-items-start">

                                @if (empty($userlist->profile_pic))
                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                        class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                @else
                                @if ($userlist->role_id == 2)
                                    <img src="{{ url('images/tutors/profilepics') }}/{{ $userlist->profile_pic }}"
                                        class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @elseif ($userlist->role_id == 3)
                                        <img src="{{ url('images/students/profilepics') }}/{{ $userlist->profile_pic }}"
                                        class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @endif
                                @endif

                                <div class="flex-grow-1 ml-3">
                                    {{ $userlist->name }}
                                    <div class="small"><span class="fa fa-circle chat-online"></span>
                                        Online</div>
                                </div>
                            </div>
                            </a>
                        @endforeach
                        {{-- </div> --}}
                        <hr class="d-block d-lg-none mt-1 mb-0">
                    </div>

                    <div class="col-12 col-lg-7 col-xl-9">
                        @if ($header->name ?? '')
                            <div class="py-2 px-4 border-bottom d-none d-lg-block">
                                <div class="d-flex align-items-center py-1">
                                    <div class="position-relative">
                                        @if (empty($header->profile_pic))
                                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                                class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @else
                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @endif

                                    </div>
                                    <div class="flex-grow-1 pl-3">
                                        <strong>{{ $header->name }}</strong>
                                        {{-- <div class="text-muted small"><em>Typing...</em></div> --}}
                                    </div>

                                </div>
                            </div>
                        @endif
                        <div class="position-relative">
                            <div class="chat-messages p-4">

                                @if (empty($messages))
                                    <div class="chat-message-center pb-4">

                                        Please select anyone from the list to start chat
                                    </div>
                                @else
                                    @foreach ($messages as $message)
                                        @if ($message->from_id === session('userid')->id && $message->from_role_id === 3)
                                            {{-- <div class="my-message">
                                            <p>{{ $message->content }}</p>
                                            <span>{{ $message->created_at->format('H:i') }}</span>
                                        </div> --}}
                                            <div class="chat-message-right pb-4">
                                                <div>
                                                    <img src="{{ url('images/students/profilepics') }}/{{ $profile_pics->profile_pic }}"
                                                        class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                        height="40">
                                                    <div class="text-muted small text-nowrap mt-2">
                                                        {{ $message->created_at }}</div>
                                                </div>
                                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                    <div class="font-weight-bold mb-1">You</div>
                                                    {{ $message->body }}
                                                </div>
                                            </div>
                                        @else
                                            {{-- <div class="their-message">
                                            <p>{{ $message->content }}</p>
                                            <span>{{ $message->created_at->format('H:i') }}</span>
                                        </div> --}}
                                            <div class="chat-message-left pb-4">
                                                <div>
                                                    @if ($header->role_id == 2)
                                                    <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                    class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                    height="40">
                                                    @elseif ($header->role_id == 1)
                                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                                    class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                    height="40">
                                                    @endif
                                                    
                                                    <div class="text-muted small text-nowrap mt-2">
                                                        {{ $message->created_at }}</div>
                                                </div>
                                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                    <div class="font-weight-bold mb-1">{{ $header->name }}</div>
                                                    {{ $message->body }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif

                            </div>
                        </div>
                        @if ($header->name ?? '')

                        {{-- @else --}}
                        <div class="flex-grow-0 py-3 px-4 border-top">
                            @if (session('userid')->role_id == 1)
                                <form action="{{ route('admin.messages.send') }}" method="POST">
                                @elseif (session('userid')->role_id == 2)
                                <form action="{{ route('tutor.messages.send') }}" method="POST">
                                @elseif (session('userid')->role_id == 3)
                                <form action="{{ route('student.messages.send') }}" method="POST">
                                {{-- @elseif (session('userid')->role_id == 4) --}}
                                {{-- <form action="{{ route('parent.messages.send') }}" method="POST"> --}}
                            @endif
                            @csrf
                            <div class="input-group">


                                <input type="hidden" id="receiver_role_id" name="receiver_role_id" placeholder="reole id" value="{{$header->role_id}}">
                                <input type="hidden" id="receiver_id" name="receiver_id" placeholder="receiver id" value="{{$header->id}}">
                                
                                <input type="text" id="message" name="message" class="form-control" placeholder="Type your message here ...">
                               
                                
                                <button type="submit" class="btn btn-sm btn-success ml-1"><span class="fa fa-paper-plane">
                                    </span> Send</i></button>
                                </div>
                                    <span class="text-danger" style="float: left !important;">
                                        @error('message')
                                            {{ "Can't send empty message!" }}
                                        @enderror
                                    </span>
                            </form>

                        </div>
                        @endif

                    </div>
                </div>
            </div>



        </div>
        <!-- content-wrapper ends -->
    @endsection
