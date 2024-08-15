@extends('student.layouts.main')
@section('main-section')

<style>
.selectAns {
    border: 1px solid lightgrey;
    padding-top: 10px;
    padding-bottom: 10px;
}

input[type='radio'] {
    accent-color: green;
}

.ck-powered-by {
    display: none;
}

.ck-balloon-panel,
.ck-powered-by-balloon,
.ck-balloon-panel_position_border-side_right {
    border: none !important;
}
</style>
<script src="https://cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>
    <!-- modal -->
    <div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4" id="header">Confirm Submission</h3>
                    </header>

                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="row">
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Questions: {{ $questions->count() }}</label>
                            </div>
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Attempted: <span id="totalAttempted">0</span></label>
                            </div>
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Non Attempted: <span id="totalNonAttempted">0</span></label>
                            </div>
                        </div>


                        <button type="button" id="finalsubmit" class="btn btn-sm btn-success float-right"><span
                                class="fa fa-upload"> </span> Confirm Submit</button>
                        <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" data-dismiss="modal"><span
                                class="fa fa-times"></span> Cancel</button>



                    </form>
                </div>
            </div>
        </div>
    </div>
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

            <div class="mb-4 text-center">
                <input type="hidden" id="testid" name="testid" value="{{ $onlineTest->id }}">
                <h3>{{ $onlineTest->name }}</h3>
                <p>{{ $onlineTest->description }}</p>
                <p id="duration" hidden>{{ $onlineTest->test_duration }}</p>
                <span id="timer"></span>
            </div>



            <div class="contnr">
                {{-- @foreach ($questions as $data) --}}
                <div class="col" >
                    <h3 id="questionBox" style="display:flex"></h3>
                </div>
                <div class="col box" id="optionbox">
                <textarea name="editor1" id="subjectiveAnswer"></textarea>
                </div>
                {{-- <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option2" value="2" data-question-id="" required>
                    <label for="option2"></label>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option3" value="3" data-question-id="" required>
                    <label for="option3"></label>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option4" value="4" data-question-id="" required>
                    <label for="option4"></label>
                </div> --}}
                {{-- @endforeach --}}


                <div class="btn-group">
                    <button id="back" class="pre btn btn-warning" disabled>Previous</button>
                    <button id="next" class="next btn btn-success">Next</button>
                </div>
            </div>
            <div class="mt-5" id="answerSheet" hidden>

                <button id="goback" onclick="showmodal()" class="pre btn btn-warning">Previous</button>
                <button id="goback1" hidden data-toggle="modal" data-target="#openmodal"
                    class="pre btn btn-warning">Previous</button>

                <hr>
                <h3>Answers</h3>
                <div class="ansSec mb-3">
                    <h4>1 &rpar; Which one of the following is not a prime number?</h4>
                    <div class="optionDiv"><input name="option" type="radio" id="" value="a"
                            disabled>&nbsp; 31</label></div>
                    <div class="optionDiv"><input name="option" type="radio" id="" value="b"
                            disabled><label>&nbsp; 61</label></div>
                    <div class="optionDiv"><input name="option" type="radio" id="" value="c"
                            disabled><label>&nbsp; 71</label></div>
                    <div class="optionDiv"><input name="option" type="radio" id="" value="d"
                            disabled><label>&nbsp; 91</label></div>
                </div>
                <div class="ansSec mb-3">
                    <h4>1 &rpar; "What least number must be added to 1056, so that the sum is completely divisible by 23 ?
                    </h4>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 2</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 3</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 18</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 21</label></div>
                </div>
            </div>
        </div>


        <!-- content-wrapper ends -->
{{-- <script>

            // Initialize timer based on local storage or default value
            let timeLimit = localStorage.getItem('remainingTime') || (60 * parseInt(document.getElementById('duration')
                .innerHTML));
            let timer = setInterval(updateTimer, 1000); // Update timer every second
            let remainingTime = timeLimit;

            function updateTimer() {
                const timerDisplay = document.getElementById("timer");
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;

                timerDisplay.textContent = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (remainingTime <= 0) {
                    clearInterval(timer);
                    timerDisplay.textContent = "Time's up!";
                    // Add your logic to handle time's up (e.g., submitting the quiz)
                }

                remainingTime--;

                // Store remaining time in local storage
                localStorage.setItem('remainingTime', remainingTime);
            }

            // Check if timer has already expired
            if (remainingTime <= 0) {
                const timerDisplay = document.getElementById("timer");
                timerDisplay.textContent = "Time's up!";
            }
            // ================ // =======================
            let selectedAnswers = []; // Array to store selected answers for each question

            const quizData = @json($questions); // Convert PHP array to JSON for JavaScript usage

            ///////////////

            let index = 0;
            let prevSelected = 0;
            let correct = 0,
                incorrect = 0,
                total = quizData.length;
            let questionBox = document.getElementById("questionBox");
            let allInputs = document.querySelectorAll("input[type='radio']")
            const loadQuestion = () => {
                if (index === total) {
                    return quizConfirmation();
                }

                reset();
                const data = quizData[index];
                console.log(data);
                questionBox.innerHTML = `${index + 1}.  ${data.question}`;


                // Check if there's a selected answer for this question and pre-select it
                prevSelected = selectedAnswers[index];

                if (prevSelected) {
                    const selectedInput = document.querySelector(
                        `input[name="option"][value="${prevSelected.answer}"][data-question-id="${prevSelected.questionId}"]`
                    );
                    if (selectedInput) {
                        selectedInput.checked = true;
                    }
                }
                qid = document.getElementById('testid').value;
                allInputs[0].value = `${data.id},1,${qid}`;
                allInputs[0].nextElementSibling.innerText = data.option1;

                allInputs[1].value = `${data.id},2,${qid}`;
                allInputs[1].nextElementSibling.innerText = data.option2;

                allInputs[2].value = `${data.id},3,${qid}`;
                allInputs[2].nextElementSibling.innerText = data.option3;

                allInputs[3].value = `${data.id},4,${qid}`;
                allInputs[3].nextElementSibling.innerText = data.option4;
            };


            document.querySelector("#next").addEventListener("click", function() {
                const data = quizData[index];
                var ans = getAnswer();
                if (ans == undefined) {
                    ans = null;
                }
                if (ans == "") {
                    ans = null;
                }
                if (ans !== null) {

                    selectedAnswers[index] = ans; // Store the selected answer in the array
                }

                if (index > (total - 3)) {
                    document.getElementById('next').innerHTML = "Submit"
                } else {
                    document.getElementById('next').innerHTML = "Next"
                }
                if (index < total) {

                    index++;
                }
                document.getElementById('back').disabled = false;
                loadQuestion();

                // Save responses using AJAX
                // saveResponses(selectedAnswers);
            });

            document.querySelector("#finalsubmit").addEventListener("click", function() {
                responses = selectedAnswers;
                // Make an AJAX POST request to the Laravel route
                fetch('{{ route('student.save.responses') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Assuming you have a CSRF token
                        },
                        body: JSON.stringify({
                            responses
                        }),
                    })
                    .then(response => response.json())

                    .then(data => {
                        // console.log(data.message); // You can handle the response message as needed
                        const successMessage = encodeURIComponent(data.message);
        const redirectUrl = `/student/exams?message=${successMessage}`;
        window.location.href = redirectUrl;
                    })
                    .catch(error => {
                        // console.error('Error saving responses:', error);
                    });
            })
            document.querySelector("#back").addEventListener("click", function() {
                const data = quizData[index];
                var ans = getAnswer();
                if (ans == undefined) {
                    ans = null;
                }
                document.getElementById('next').innerHTML = "Next"
                if (ans !== null) {
                    selectedAnswers[index] = ans; // Store the selected answer in the array
                }

                if (index == total) {
                    index--;
                }
                if (index > 0) {
                    index--;
                    loadQuestion();

                    prevSelected = selectedAnswers[index];
                    if (prevSelected) {
                        document.querySelector(`input[name="option"][value="${prevSelected}"]`).checked = true;
                    }
                }

            });


            const getAnswer = () => {
                let ans;
                let questionId;
                allInputs.forEach((inputEl) => {
                    if (inputEl.checked) {
                        ans = inputEl.value;
                    }
                });
                return ans;
                // return ans ? {
                //     questionId,
                //     answer: ans
                // } : null;
            };


            const reset = () => {
                allInputs.forEach(
                    (inputEl) => {
                        inputEl.checked = false;
                    }
                )
            }
            allInputs.forEach(inputEl => {
                inputEl.addEventListener("dblclick", function() {
                    this.checked = false; // Clear the selected response on double-click
                    selectedAnswers[index] = null; // Clear the selected answer for this question
                });
            });

            const quizEnd = () => {
                // console.log(document.getElementsByClassName("container"));
                document.getElementsByClassName("contnr")[0].innerHTML = `
        <div class="col text-center">

            <h3 class="w-100 "> Hii,<br> you've scored ${correct} / ${total} </h3><br><br>
            <img src="/images/icons/submitted.gif" width="130" height="100"><br><br>

             <a class="btn btn-primary" href="testPractice.html">Retake</a>

        </div>`

                // document.getElementById('topSec').hidden=true;
                document.getElementById('answerSheet').hidden = false;
            }

            loadQuestion(index);

            // JavaScript
            document.addEventListener("DOMContentLoaded", function() {
                const openmodalBtn = document.getElementById("openmodalBtn");
                const openmodal = document.getElementById("openmodal");
                const closeButton = openmodal.querySelector(".close");

                openmodalBtn.addEventListener("click", function() {
                    alert('rrr')
                    openmodal.style.display = "block";
                });

                closeButton.addEventListener("click", function() {
                    openmodal.style.display = "none";
                });

                window.addEventListener("click", function(event) {
                    if (event.target === openmodal) {
                        openmodal.style.display = "none";
                    }
                });
            });

            function quizConfirmation() {
                calculateAttemptedQuestions();
                // Get a reference to the button element
                var button = document.getElementById("goback1");

                // Trigger a click event on the button
                button.click();
            }

            function calculateAttemptedQuestions() {
                if (selectedAnswers.length > total) {

                    const secondToLastIndex = selectedAnswers.length - 2; // Index of the second-to-last element

                    if (secondToLastIndex >= 0) {
                        selectedAnswers.splice(secondToLastIndex, 1); // Remove one element at the secondToLastIndex
                    }

                }

                const attemptedQuestions = selectedAnswers.filter(answer => answer !== null);
                const nonAttemptedQuestions = total - attemptedQuestions.length;

                document.querySelector("#openmodal #totalAttempted").textContent = attemptedQuestions.length;
                document.querySelector("#openmodal #totalNonAttempted").textContent = nonAttemptedQuestions;
            }
</script> --}}

<script>
    CKEDITOR.replace('editor1');
    CKEDITOR.replace('editor2');

    function getData() {
        //Get data written in first Editor
        var editor_data = CKEDITOR.instances['editor1'].getData();
        //Set data in Second Editor which is written in first Editor
        CKEDITOR.instances['editor2'].setData(editor_data);
    }
</script>
<script>
    // Initialize timer based on local storage or default value
    let timeLimit = localStorage.getItem('remainingTime') || (60 * parseInt(document.getElementById('duration')
        .innerHTML));
    let timer = setInterval(updateTimer, 1000); // Update timer every second
    let remainingTime = timeLimit;

    function updateTimer() {
        const timerDisplay = document.getElementById("timer");
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;

        timerDisplay.textContent = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (remainingTime <= 0) {
            clearInterval(timer);
            timerDisplay.textContent = "Time's up!";
            // Add your logic to handle time's up (e.g., submitting the quiz)
        }

        remainingTime--;

        // Store remaining time in local storage
        localStorage.setItem('remainingTime', remainingTime);
    }

    // Check if timer has already expired
    if (remainingTime <= 0) {
        const timerDisplay = document.getElementById("timer");
        timerDisplay.textContent = "Time's up!";
    }

    let selectedAnswers = []; // Array to store selected answers for each question

    const quizData = @json($questions); // Convert PHP array to JSON for JavaScript usage

    let index = 0;
    let prevSelected = 0;
    let total = quizData.length;
    let questionBox = document.getElementById("questionBox");
    // let textArea = document.getElementById("subjectiveAnswer");
    let textArea = CKEDITOR.instances.subjectiveAnswer;


    const loadQuestion = () => {
        if (index == total) {
            return quizConfirmation();
        }

        reset();
        const data = quizData[index];
        console.log(data);
        questionBox.innerHTML = `${index + 1}.  ${data.question}`;

        // Check if there's a selected answer for this question and pre-fill the text area
        prevSelected = selectedAnswers[index];
        textArea.value = prevSelected || '';
    };

    // document.querySelector("#next").addEventListener("click", function() {
    //     const ans = textArea.getData();

    //     selectedAnswers[index] = ans; // Store the subjective answer in the array

    //     if (index > (total - 3)) {
    //         document.getElementById('next').innerHTML = "Submit";
    //     } else {
    //         document.getElementById('next').innerHTML = "Next";
    //     }

    //     if (index < total) {
    //         index++;
    //     }
    //     document.getElementById('back').disabled = false;
    //     loadQuestion();
    // });
    document.querySelector("#next").addEventListener("click", function() {
        // alert(index)
        const ans = textArea.getData(); // Get textarea data from CKEditor
        const testId = document.getElementById("testid").value;
        const questionId = quizData[index].id;
        const nextQuestionId =  index < total - 1 ? quizData[index + 1].id : null;

        const tempurl = "{{url('student/storeSubjectiveDataInTemporaryTable')}}"

        // Send an AJAX request to store the data in the temporary table
        fetch(tempurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Replace with your CSRF token
            },
            body: JSON.stringify({
                testId,
                questionId,
                answer: ans,
                nextQuestionId,
            }),
        })
        .then(response => response.json())
        .then(data => {

        //    console.log(data.message)
             if (index < total-1) {
                CKEDITOR.instances.subjectiveAnswer.setData(data.nextAnswer|| '');
            }

           selectedAnswers[index] = ans; // Store the subjective answer in the array

            if (index > (total - 3)) {
                document.getElementById('next').innerHTML = "Submit";
            } else {
                document.getElementById('next').innerHTML = "Next";
            }

            if (index < total) {
                index++;
            }
            document.getElementById('back').disabled = false;
            loadQuestion();
           
        })
        .catch(error => {
            // Handle errors if any
        });


    });

    document.querySelector("#finalsubmit").addEventListener("click", function() {

              const testId = document.getElementById("testid").value;

                // responses = selectedAnswers;
                // console.log(selectedAnswers);
                // Make an AJAX POST request to the Laravel route
                fetch('{{ route('student.save.subjective-responses') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Assuming you have a CSRF token
                        },
                        body: JSON.stringify({
                            testId:testId,
                            questionIds:@json($questionIds)
                        }),
                    })
                    .then(response => response.json())

                    .then(data => {
                        // console.log(data.message); // You can handle the response message as needed
                        const successMessage = encodeURIComponent(data.message);
                        const redirectUrl = `/student/exams?message=${successMessage}`;
                        window.location.href = redirectUrl;
                    })
                    .catch(error => {
                        // console.error('Error saving responses:', error);
                    });
    })

    // document.querySelector("#back").addEventListener("click", function() {
    //     selectedAnswers[index] = textArea.getData(); // Store the subjective answer
    //     document.getElementById('next').innerHTML = "Next"
    //     if (index == total) {
    //         index--;
    //     }
    //     if (index > 0) {
    //         index--;
    //         loadQuestion();

    //         prevSelected = selectedAnswers[index];
    //         textArea.value = prevSelected || '';
    //     }
    // });

    document.querySelector("#back").addEventListener("click", function() {
        // alert(index);
        const testId = document.getElementById("testid").value;
        const prevQuestionId = quizData[index - 1].id; // Get the question ID of the previous question

        // Send an AJAX request to retrieve the answer for the previous question from the temporary table
        fetch('{{route("student.getAnswerFromSubjectiveTempTable")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Replace with your CSRF token
            },
            body: JSON.stringify({
                testId,
                questionId: prevQuestionId,
            }),
        })
        .then(response => response.json())
        .then(data => {
            // Set the retrieved answer in CKEditor
            textArea.setData(data.answer || '');
            selectedAnswers[index] = textArea.getData(); // Store the subjective answer
            document.getElementById('next').innerHTML = "Next"
            // if (index == total) {
            //     index--;
            // }
            if (index > 0) {
                index--;
                loadQuestion();
            }
          
        })
        .catch(error => {
            // Handle errors if any
        });




    });


    const reset = () => {
        // Reset any relevant UI elements here
    };

    // ... (other code remains unchanged)

    function quizConfirmation() {
        calculateAttemptedQuestions();
        // Get a reference to the button element
        var button = document.getElementById("goback1");

        // Trigger a click event on the button
        button.click();
    }

    function calculateAttemptedQuestions() {
                if (selectedAnswers.length > total) {

                    const secondToLastIndex = selectedAnswers.length - 2; // Index of the second-to-last element

                    if (secondToLastIndex >= 0) {
                        selectedAnswers.splice(secondToLastIndex, 1); // Remove one element at the secondToLastIndex
                    }

                }

                const attemptedQuestions = selectedAnswers.filter(answer => answer !== null);
                const nonAttemptedQuestions = total - attemptedQuestions.length;

                document.querySelector("#openmodal #totalAttempted").textContent = attemptedQuestions.length;
                document.querySelector("#openmodal #totalNonAttempted").textContent = nonAttemptedQuestions;
            }

    // Initial load of the first question
    loadQuestion();
</script>

@endsection
