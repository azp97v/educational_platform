@php
$sendRoute = route('teacher.messaging.send');
$refreshRoute = route('teacher.messaging.refresh');
$deltaRoute = route('teacher.messaging.delta');
$audioRoute = route('teacher.messaging.audio');
$fileRoute = route('teacher.messaging.file');
$audioPositionRoute = route('teacher.messaging.audio-position', ['message' => '__MESSAGE_ID__']);
$statusesRoute      = route('teacher.messaging.statuses');
$statusCreateRoute  = route('teacher.messaging.status.create');
$statusViewRoute    = url('/teacher/messaging/status/__STATUS_ID__/view');
$statusViewersRoute = url('/teacher/messaging/status/__STATUS_ID__/viewers');
$statusDeleteRoute  = url('/teacher/messaging/status/__STATUS_ID__');
@endphp

@include('messaging-app')
