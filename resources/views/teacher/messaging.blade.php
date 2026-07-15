@php
$sendRoute = route('teacher.messaging.send');
$refreshRoute = route('teacher.messaging.refresh');
$deltaRoute = route('teacher.messaging.delta');
$audioRoute = route('teacher.messaging.audio');
$fileRoute = route('teacher.messaging.file');
$audioPositionRoute = route('teacher.messaging.audio-position', ['message' => '__MESSAGE_ID__']);
$statusesRoute      = route('teacher.messaging.statuses');
$statusCreateRoute  = route('teacher.messaging.status.create');
$statusViewRoute    = route('teacher.messaging.status.view',    ['status' => '__STATUS_ID__']);
$statusViewersRoute = route('teacher.messaging.status.viewers', ['status' => '__STATUS_ID__']);
$statusDeleteRoute  = route('teacher.messaging.status.delete',  ['status' => '__STATUS_ID__']);
@endphp

@include('messaging-app')
