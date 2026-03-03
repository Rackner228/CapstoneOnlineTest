//speechToText.js
//supports the speech to text feature for ASCEND which allows the student to respond to the simluated patient vocally
//This file was written with assistance from Google Gemini generative AI services.

//load the microphone once the page is done generating
document.addEventListener('DOMContentLoaded', () => {

    //gets microphone button
    const micBtn = document.getElementById('mic-btn');
    //get's student response field
    const inputField = document.getElementById('user-input');

    //check for browser support
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (!SpeechRecognition) {
        console.warn("Speech Recognition API not supported in this browser.");
        if (micBtn) {
            micBtn.style.display = 'none'; // Hide button if not supported
        }
        return;
    }

    //configure recognition
    const recognition = new SpeechRecognition(); //make listening object
    recognition.continuous = false;
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1; //we only want one possible transcript

    //initalize variable for recording
    let isRecording = false; 

    //when the button is clicked, start/stop the voice recognition
    if(micBtn) {
        micBtn.addEventListener('click', () => {
            if(isRecording){
                recognition.stop();
            }
            else {
                recognition.start();
            }
        });
    }

    //event handlers
    recognition.onstart = function() {
        isRecording = true;
        micBtn.classList.add('recording') //trigger specific css for recordin
    };

    recognition.onend = function() {
        isRecording = false;
        micBtn.classList.remove('recording')
    };

    recognition.onresult = function(event) {
        //get transcript
        const transcript = event.results[0][0].transcript;

        //insert into the input field
        if(inputField) {
            inputField.value = transcript;
            inputField.focus(); //put cursor in textbox automatically
            sendMessage(); //send message automatically after speaking
        }
    };

    recognition.onerror = function(event) {
        console.error("Speech Recognition error", event.error);
        isRecording = false;
        micBtn.classList.remove('recording');

        if (event.error === 'not-allowed') {
            alert("Microphone access blocked. Plase allow permission.");
        }
    };



});