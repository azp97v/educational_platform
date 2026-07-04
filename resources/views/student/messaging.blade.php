@php
$sendRoute = route('student.messaging.send');
$refreshRoute = route('student.messaging.refresh');
$deltaRoute = route('student.messaging.delta');
$audioRoute = route('student.messaging.audio');
$fileRoute = route('student.messaging.file');
$audioPositionRoute = route('student.messaging.audio-position', ['message' => '__MESSAGE_ID__']);
@endphp

@include('messaging-app')
