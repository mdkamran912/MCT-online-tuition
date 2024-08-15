@extends('student.layouts.main')
@section('main-section')
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">

                    <div id="listHeader">
                        <h3>{{$subject->name}}</h3>
                    </div>

                    <table class="table table-bordered table-responsive">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th scope="col">SNo.</th>
                                <th scope="col">Topic</th>
                                <th scope="col">Syllabus</th>



                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $topic)
                                
                            <tr>
                                <th scope="row">{{$loop->iteration}}</th>
                                <th class="text-wrap">{{$topic->name}}</th>
                                <td>
                                    <ul>
                                @foreach ($syllabus as $syllabi)
                                    @if ($syllabi->topic_id == $topic->id)
                                        
                                   
                                
                                
                                        <li>{{$syllabi->name}}</li>
                                        
                                        
                                    
                                @else
                                <ul>
                                        
                                        
                                    </ul>
                                    
                                
                                @endif
                                @endforeach
                            </ul>
                                    
                        </td>
                            
                        </tr>
                        @endforeach
                            
                        </tbody>
                    </table>




                </div>
                <!-- content-wrapper ends -->
                
                @endsection