@php

$currentRole = auth()->check() ? strtolower((string) auth()->user()->role) : 'student';

$isTeacherRole = $currentRole === 'teacher';

$pickRoute = static function (array $names): ?string {

foreach ($names as $name) {

if (\Illuminate\Support\Facades\Route::has($name)) {

return route($name);

}

}

return null;

};

$pickStatusRouteTemplate = static function (array $names): ?string {

foreach ($names as $name) {

if (\Illuminate\Support\Facades\Route::has($name)) {

return route($name, ['status' => '__STATUS_ID__']);

}

}

return null;

};

$pickMessageRouteTemplate = static function (array $names): ?string {

foreach ($names as $name) {

if (\Illuminate\Support\Facades\Route::has($name)) {

return route($name, ['messageId' => '__MSG_ID__']);

}

}

return null;

};

$sendRoute = $sendRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.send', 'messaging.send', 'student.messaging.send'] : ['student.messaging.send', 'messaging.send', 'teacher.messaging.send']);

$refreshRoute = $refreshRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.refresh', 'messaging.refresh', 'student.messaging.refresh'] : ['student.messaging.refresh', 'messaging.refresh', 'teacher.messaging.refresh']);

$loadRoute = $loadRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.load', 'messaging.load', 'student.messaging.load'] : ['student.messaging.load', 'messaging.load', 'teacher.messaging.load']);

$deltaRoute = $deltaRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.delta', 'messaging.delta', 'student.messaging.delta'] : ['student.messaging.delta', 'messaging.delta', 'teacher.messaging.delta']);

$contactsUnreadRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.contacts-unread', 'messaging.contacts-unread', 'student.messaging.contacts-unread'] : ['student.messaging.contacts-unread', 'messaging.contacts-unread', 'teacher.messaging.contacts-unread']);

$statusHistoryRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.status.history', 'messaging.status.history'] : ['messaging.status.history', 'teacher.messaging.status.history']);

$audioRoute = $audioRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.audio', 'messaging.audio', 'student.messaging.audio'] : ['student.messaging.audio', 'messaging.audio', 'teacher.messaging.audio']);

$fileRoute = $fileRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.file', 'messaging.file', 'student.messaging.file'] : ['student.messaging.file', 'messaging.file', 'teacher.messaging.file']);

$stickersIndexRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.stickers.index', 'messaging.stickers.index', 'student.messaging.stickers.index'] : ['student.messaging.stickers.index', 'messaging.stickers.index', 'teacher.messaging.stickers.index']);
$stickersStoreRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.stickers.store', 'messaging.stickers.store', 'student.messaging.stickers.store'] : ['student.messaging.stickers.store', 'messaging.stickers.store', 'teacher.messaging.stickers.store']);
$gifsSearchRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.gifs.search', 'messaging.gifs.search', 'student.messaging.gifs.search'] : ['student.messaging.gifs.search', 'messaging.gifs.search', 'teacher.messaging.gifs.search']);
$e2eRegisterRoute = $pickRoute($isTeacherRole ? ['teacher.messaging.encryption.register', 'messaging.encryption.register', 'student.messaging.encryption.register'] : ['student.messaging.encryption.register', 'messaging.encryption.register', 'teacher.messaging.encryption.register']) ?? '#';
$e2ePublicKeyBase = null;
foreach (($isTeacherRole ? ['teacher.messaging.encryption.public-key', 'messaging.encryption.public-key', 'student.messaging.encryption.public-key'] : ['student.messaging.encryption.public-key', 'messaging.encryption.public-key', 'teacher.messaging.encryption.public-key']) as $e2eName) {
    if (\Illuminate\Support\Facades\Route::has($e2eName)) {
        $e2ePublicKeyBase = route($e2eName, ['user' => '__UID__']);
        break;
    }
}
$buildStickerRouteTemplate = function (string $action) use ($isTeacherRole) {
    $names = $isTeacherRole
        ? ["teacher.messaging.stickers.{$action}", "messaging.stickers.{$action}", "student.messaging.stickers.{$action}"]
        : ["student.messaging.stickers.{$action}", "messaging.stickers.{$action}", "teacher.messaging.stickers.{$action}"];
    foreach ($names as $name) {
        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, ['sticker' => '__STICKER_ID__']);
        }
    }
    return null;
};
$stickersFavoriteRouteTemplate = $buildStickerRouteTemplate('favorite');
$stickersDestroyRouteTemplate = $buildStickerRouteTemplate('destroy');
$stickersUsedRouteTemplate = $buildStickerRouteTemplate('used');

$qrRouteNames = $isTeacherRole ? ['teacher.messaging.qr', 'messaging.qr', 'student.messaging.qr'] : ['student.messaging.qr', 'messaging.qr', 'teacher.messaging.qr'];
$qrRouteTemplate = null;
foreach ($qrRouteNames as $qrRouteName) {
    if (\Illuminate\Support\Facades\Route::has($qrRouteName)) {
        $qrRouteTemplate = route($qrRouteName, ['user' => '__USER_ID__']);
        break;
    }
}

$callsInitiateRoute = $pickRoute($isTeacherRole ? ['teacher.calls.initiate', 'calls.initiate', 'student.calls.initiate'] : ['student.calls.initiate', 'calls.initiate', 'teacher.calls.initiate']);
$callsRouteNames = $isTeacherRole
    ? ['teacher.calls.answer', 'calls.answer', 'student.calls.answer']
    : ['student.calls.answer', 'calls.answer', 'teacher.calls.answer'];
$buildCallRouteTemplate = function (string $action) use ($isTeacherRole) {
    $names = $isTeacherRole
        ? ["teacher.calls.{$action}", "calls.{$action}", "student.calls.{$action}"]
        : ["student.calls.{$action}", "calls.{$action}", "teacher.calls.{$action}"];
    foreach ($names as $name) {
        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, ['call' => '__CALL_ID__']);
        }
    }
    return null;
};
$callsAnswerRouteTemplate = $buildCallRouteTemplate('answer');
$callsRejectRouteTemplate = $buildCallRouteTemplate('reject');
$callsRingRouteTemplate = $buildCallRouteTemplate('ring');
$callsEndRouteTemplate = $buildCallRouteTemplate('end');
$callsIceRouteTemplate = $buildCallRouteTemplate('ice-candidate');
$callsOfferRouteTemplate = $buildCallRouteTemplate('offer');
$callsPeerOfferRouteTemplate = $buildCallRouteTemplate('peer-offer');
$callsPeerAnswerRouteTemplate = $buildCallRouteTemplate('peer-answer');
$callsJoinRouteTemplate = $buildCallRouteTemplate('join');
$callsInviteRouteTemplate = $buildCallRouteTemplate('invite');
$callsHmsTokenRouteTemplate = $buildCallRouteTemplate('hms-token');
$callsPendingRoute = $pickRoute($isTeacherRole ? ['teacher.calls.pending', 'student.calls.pending'] : ['student.calls.pending', 'teacher.calls.pending']);

// TURN server config — set TURN_URL / TURN_USERNAME / TURN_CREDENTIAL in .env for production
$turnIceConfig = [];
$turnUrl = env('TURN_URL');
if ($turnUrl) {
    $turnEntry = ['urls' => array_filter(array_map('trim', explode(',', $turnUrl)))];
    if (env('TURN_USERNAME')) $turnEntry['username'] = env('TURN_USERNAME');
    if (env('TURN_CREDENTIAL')) $turnEntry['credential'] = env('TURN_CREDENTIAL');
    $turnIceConfig[] = $turnEntry;
}

$updateRouteTemplate = $updateRouteTemplate ?? (\Illuminate\Support\Facades\Route::has('student.messages.update')

? route('student.messages.update', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('messages.update')

? route('messages.update', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('teacher.messages.update')

? route('teacher.messages.update', ['message' => '__MESSAGE_ID__'])

: '#')));

$deleteRouteTemplate = $deleteRouteTemplate ?? (\Illuminate\Support\Facades\Route::has('student.messages.destroy')

? route('student.messages.destroy', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('messages.destroy')

? route('messages.destroy', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('teacher.messages.destroy')

? route('teacher.messages.destroy', ['message' => '__MESSAGE_ID__'])

: '#')));

$audioPositionRoute = $audioPositionRoute ?? (\Illuminate\Support\Facades\Route::has('student.messaging.audio-position')

? route('student.messaging.audio-position', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('messaging.audio-position')

? route('messaging.audio-position', ['message' => '__MESSAGE_ID__'])

: (\Illuminate\Support\Facades\Route::has('teacher.messaging.audio-position')

? route('teacher.messaging.audio-position', ['message' => '__MESSAGE_ID__'])

: '#')));

$statusesRoute       = $statusesRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.statuses', 'messaging.statuses', 'student.messaging.statuses'] : ['student.messaging.statuses', 'messaging.statuses', 'teacher.messaging.statuses']) ?? '#';

$statusCreateRoute   = $statusCreateRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.status.create', 'messaging.status.create', 'student.messaging.status.create'] : ['student.messaging.status.create', 'messaging.status.create', 'teacher.messaging.status.create']) ?? '#';

$statusViewRoute = $statusViewRoute ?? (

$pickStatusRouteTemplate($isTeacherRole

? ['teacher.messaging.status.view', 'messaging.status.view', 'student.messaging.status.view']

: ['student.messaging.status.view', 'messaging.status.view', 'teacher.messaging.status.view']

) ?? '/messaging/status/__STATUS_ID__/view'

);

$statusViewersRoute = $statusViewersRoute ?? (

$pickStatusRouteTemplate($isTeacherRole

? ['teacher.messaging.status.viewers', 'messaging.status.viewers', 'student.messaging.status.viewers']

: ['student.messaging.status.viewers', 'messaging.status.viewers', 'teacher.messaging.status.viewers']

) ?? '/messaging/status/__STATUS_ID__/viewers'

);

$statusDeleteRoute = $statusDeleteRoute ?? (

$pickStatusRouteTemplate($isTeacherRole

? ['teacher.messaging.status.delete', 'messaging.status.delete', 'student.messaging.status.delete']

: ['student.messaging.status.delete', 'messaging.status.delete', 'teacher.messaging.status.delete']

) ?? '/messaging/status/__STATUS_ID__'

);

$statusUpdateRoute = $statusUpdateRoute ?? (

$pickStatusRouteTemplate($isTeacherRole

? ['teacher.messaging.status.update', 'messaging.status.update', 'student.messaging.status.update']

: ['student.messaging.status.update', 'messaging.status.update', 'teacher.messaging.status.update']

) ?? '#'

);

$statusReplyRoute = $statusReplyRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.status.reply', 'messaging.status.reply', 'student.messaging.status.reply'] : ['student.messaging.status.reply', 'messaging.status.reply', 'teacher.messaging.status.reply']) ?? '#';

$statusReactionRoute = $statusReactionRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.status.reaction', 'messaging.status.reaction', 'student.messaging.status.reaction'] : ['student.messaging.status.reaction', 'messaging.status.reaction', 'teacher.messaging.status.reaction']) ?? '#';

$pinRoute = $pinRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.pin', 'messaging.pin', 'student.messaging.pin'] : ['student.messaging.pin', 'messaging.pin', 'teacher.messaging.pin']) ?? '#';

$forwardRoute = $forwardRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.forward', 'messaging.forward', 'student.messaging.forward'] : ['student.messaging.forward', 'messaging.forward', 'teacher.messaging.forward']) ?? '#';

$settingsGetRoute = $settingsGetRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.settings.get', 'messaging.settings.get', 'student.messaging.settings.get'] : ['student.messaging.settings.get', 'messaging.settings.get', 'teacher.messaging.settings.get']) ?? '#';

$settingsSaveRoute = $settingsSaveRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.settings.save', 'messaging.settings.save', 'student.messaging.settings.save'] : ['student.messaging.settings.save', 'messaging.settings.save', 'teacher.messaging.settings.save']) ?? '#';

$pickRouteParam = static function (array $names, array $params) {
    foreach ($names as $name) {
        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, $params);
        }
    }
    return null;
};

$settingsExtraRoutes = [];
foreach ([
    'accountUpdate' => 'messaging.account.update',
    'checkUsername' => 'messaging.account.check-username',
    'checkPhone' => 'messaging.account.check-phone',
    'blockedList' => 'messaging.blocked.list',
    'blockedAdd' => 'messaging.blocked.add',
    'sessionsList' => 'messaging.sessions.list',
    'twoFaRequest' => 'messaging.2fa.request',
    'twoFaConfirm' => 'messaging.2fa.confirm',
    'twoFaDisable' => 'messaging.2fa.disable',
    'foldersList' => 'messaging.folders.list',
    'foldersSave' => 'messaging.folders.save',
    'localeUpdate' => 'messaging.locale.update',
    'frequentContacts' => 'messaging.frequent-contacts',
] as $key => $name) {
    $settingsExtraRoutes[$key] = $pickRoute($isTeacherRole
        ? ["teacher.$name", $name, "student.$name"]
        : ["student.$name", $name, "teacher.$name"]) ?? '#';
}

$settingsExtraRoutes['profileUpdate'] = \Illuminate\Support\Facades\Route::has('profile.update') ? route('profile.update') : '#';

$settingsExtraRoutes['usersSearch'] = $pickRoute($isTeacherRole
    ? ['teacher.messaging.users.search', 'messaging.users.search', 'student.messaging.users.search']
    : ['student.messaging.users.search', 'messaging.users.search', 'teacher.messaging.users.search']) ?? '#';

// Routes requiring a path parameter: build with a placeholder token, replaced client-side.
foreach ([
    'blockedRemove' => ['messaging.blocked.remove', 'userId'],
    'sessionsTerminate' => ['messaging.sessions.terminate', 'sessionId'],
    'foldersDelete' => ['messaging.folders.delete', 'folderId'],
] as $key => [$name, $param]) {
    $settingsExtraRoutes[$key] = $pickRouteParam(
        $isTeacherRole ? ["teacher.$name", $name, "student.$name"] : ["student.$name", $name, "teacher.$name"],
        [$param => '__ID__']
    ) ?? '#';
}

$typingRoute = $typingRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.typing', 'messaging.typing', 'student.messaging.typing'] : ['student.messaging.typing', 'messaging.typing', 'teacher.messaging.typing']) ?? '#';

$savedListRoute      = $savedListRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.saved.list', 'messaging.saved.list', 'student.messaging.saved.list'] : ['student.messaging.saved.list', 'messaging.saved.list', 'teacher.messaging.saved.list']) ?? '#';

$savedIdsRoute       = $savedIdsRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.saved.ids', 'messaging.saved.ids', 'student.messaging.saved.ids'] : ['student.messaging.saved.ids', 'messaging.saved.ids', 'teacher.messaging.saved.ids']) ?? '#';

$saveRoute           = $saveRoute ?? (
    $pickMessageRouteTemplate($isTeacherRole
        ? ['teacher.messaging.save', 'messaging.save', 'student.messaging.save']
        : ['student.messaging.save', 'messaging.save', 'teacher.messaging.save']
    ) ?? '/messaging/save/__MSG_ID__'
);

$unsaveRoute         = $unsaveRoute ?? (
    $pickMessageRouteTemplate($isTeacherRole
        ? ['teacher.messaging.unsave', 'messaging.unsave', 'student.messaging.unsave']
        : ['student.messaging.unsave', 'messaging.unsave', 'teacher.messaging.unsave']
    ) ?? '/messaging/save/__MSG_ID__'
);

$wallpaperSetRoute   = $wallpaperSetRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.wallpaper.set', 'messaging.wallpaper.set', 'student.messaging.wallpaper.set'] : ['student.messaging.wallpaper.set', 'messaging.wallpaper.set', 'teacher.messaging.wallpaper.set']);

$wallpaperGetRoute   = $wallpaperGetRoute ?? $pickRoute($isTeacherRole ? ['teacher.messaging.wallpaper.get', 'messaging.wallpaper.get', 'student.messaging.wallpaper.get'] : ['student.messaging.wallpaper.get', 'messaging.wallpaper.get', 'teacher.messaging.wallpaper.get']);

@endphp

@include('messaging.partials.html')

<script>
@include('messaging.partials.js-setup')

createApp({

@include('messaging.partials.js-data')

@include('messaging.partials.js-watch')

@include('messaging.partials.js-computed')

methods: {
@include('messaging.partials.js-methods-core')
@include('messaging.partials.js-methods-groups')
@include('messaging.partials.js-methods-status')
@include('messaging.partials.js-methods-video-settings')
},

@include('messaging.partials.js-lifecycle')

}).mount('#app');

// Pause decorative infinite animations when tab is not visible
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        document.body.classList.add('tab-hidden');
    } else {
        document.body.classList.remove('tab-hidden');
    }
});

</script>

@include('components.account-theme-foot')

</body>

</html>