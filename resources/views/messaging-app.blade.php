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

<!doctype html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

@include('components.account-theme-head')

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Messaging</title>

<link rel="icon" type="image/png" href="{{ asset('images/logo/logo.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

<link rel="stylesheet" href="/css/messaging-app.css?v={{ filemtime(public_path('css/messaging-app.css')) }}">

</head>

<body>

<div id="app" v-cloak>

<div id="app-inner" style="display:contents;">

<div class="layout">

<aside class="sidebar" :class="{ show: showSidebar }">

<div class="s-head">

<div class="s-top">

<button class="back-btn" @click="goBackToDashboard" title="">

<i class="ri-arrow-left-line"></i>

</button>

<div class="s-title">@{{ t.title }}</div>

</div>

<div class="search">

<button class="new-chat-btn" :title="t.newConversation" @click="openNewConversationModal"><i class="ri-chat-new-line"></i></button>

<input type="text" v-model="searchQuery" :placeholder="t.search">

<i class="ri-search-line"></i>

</div>

</div>

<div class="sidebar-body" :class="{ 'sidebar-body-top-tabs': settingsChats.tabsPosition === 'top' }">

<nav class="nav-rail" :class="{ 'nav-rail-top': settingsChats.tabsPosition === 'top' }">

<button class="rail-btn" @click="accountDrawerOpen=true"><i class="ri-menu-line"></i><span></span></button>

<button class="rail-btn" :class="{active: railFilter==='all'}" @click="railFilter='all'"><i class="ri-message-2-line"></i><span>All</span></button>

<button class="rail-btn" :class="{active: railFilter==='unread'}" @click="railFilter='unread'"><i class="ri-mail-unread-line"></i><span>غير مقروءة</span></button>

<button class="rail-btn" :class="{active: railFilter==='private'}" @click="railFilter='private'"><i class="ri-user-3-line"></i><span>خاصة</span></button>

<button class="rail-btn" :class="{active: railFilter==='groups'}" @click="railFilter='groups'"><i class="ri-folder-3-line"></i><span>مجموعات</span></button>

<button class="rail-btn" :class="{active: railFilter==='channels'}" @click="railFilter='channels'"><i class="ri-megaphone-line"></i><span>قنوات</span></button>

<button class="rail-btn" v-for="folder in foldersConfig" :key="folder.id" :class="{active: railFilter===folder.id}" @click="railFilter=folder.id" :title="folder.name"><i :class="folder.icon || 'ri-folder-3-line'" :style="{color: railFilter===folder.id ? '' : folder.color}"></i><span>@{{ folder.name }}</span></button>

<button class="rail-btn" @click="openFoldersManager"><i class="ri-equalizer-2-line"></i><span>Edit</span></button>

</nav>

<div class="sidebar-main">

<div class="status-bar">

<div class="status-list">

<!-- My Status -->

<div class="status-item" @click="openMyStatusEntry">

<div class="status-avatar-wrap">

<div class="status-avatar my-status" :class="{ 'has-status': (myStatuses || []).length > 0 }">

<img v-if="normalizeAvatarUrl(currentUserAvatar)" :src="normalizeAvatarUrl(currentUserAvatar)" alt="" v-on:error="handleAvatarError($event, {avatar_url: currentUserAvatar})">

<span v-else>@{{ getAuthorInitial(userName) }}</span>

</div>

<div class="status-add-badge">@{{ (myStatuses || []).length > 0 ? myStatuses.length : '+' }}</div>

</div>

<span class="status-name"></span>

</div>

<!-- Contacts' statuses -->

<div class="status-item" v-for="cs in contactStatuses" :key="'st-'+cs.user_id" @click="openStatusViewer(cs)">

<div class="status-avatar-wrap">

<div class="status-avatar" :class="{ viewed: cs.all_viewed }">

<img v-if="cs.user_avatar" :src="normalizeAvatarUrl(cs.user_avatar)" :alt="cs.user_name" v-on:error="handleAvatarError($event, cs)">

<span v-else>@{{ getAuthorInitial(cs.user_name) }}</span>

</div>

</div>

<span class="status-name">@{{ cs.user_name }}</span>

</div>

</div>

</div>            <div class="contacts">

<template v-if="filteredContactsByRail.length">

<article class="contact" v-for="contact in filteredContactsByRail" :key="contact.id"

:class="{ active: selectedContact && selectedContact.id === contact.id }" @click="selectedContact && Number(selectedContact.id)===Number(contact.id) ? null : selectContact(contact)">

<div style="position:relative;flex-shrink:0;">

<div class="avatar" :class="{ 'contact-has-status': hasAnyStatus(contact.id), 'contact-unseen-status': hasUnseenStatus(contact.id) }" @click="hasAnyStatus(contact.id) && ($event.stopPropagation(), openStatusByContactId(contact.id))">

<i v-if="Number(contact.id) === -1" class="ri-bookmark-fill" style="font-size:20px;color:var(--gold);"></i>

<img v-else-if="contact.avatar_url" :src="normalizeAvatarUrl(contact.avatar_url)" :alt="contact.name" v-on:error="handleAvatarError($event, contact)">

<span v-else>@{{ getAuthorInitial(contact.name) }}</span>

</div>

<i v-if="mutedUntilByContact[String(contact.id)] > Date.now()" class="ri-volume-mute-line" style="position:absolute;top:0;right:0;font-size:12px;color:var(--gold);background:rgba(0,0,0,.65);border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;z-index:5;backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);border:2px solid var(--panel);"></i>

</div>

<div class="c-body">

<div class="c-name">@{{ contact.name }} <span v-if="settingsChats.showFolderTags && getContactFolderTag(contact.id)" class="folder-tag-badge">@{{ getContactFolderTag(contact.id) }}</span> <span v-if="blockedByContact[String(contact.id)]" class="contact-blocked-badge">محظور @{{ formatBlockedTime(blockedByContact[String(contact.id)]) }}</span></div>

<div class="c-prev"><svg class="contact-status-reply-indicator" v-if="contact.lastMessageStatusRefId" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="12" r="3.5" stroke="currentColor" stroke-width="1.8"/><circle cx="8" cy="12" r="1.5" fill="currentColor"/><path d="M14 8.5L18 12L14 15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 12H11.5C9.57 12 8 10.43 8 8.5V7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>@{{ sanitizeDisplayText(contact.lastMessage, t.noMessages) }}</div>

</div>

<div class="c-meta">

<div class="c-time">@{{ formatContactTime(contact.lastMessageTime) }}</div>

<div class="badge" v-if="contact.unreadCount > 0">@{{ contact.unreadCount }}</div>

</div>

</article>

</template>

<div v-else class="empty">@{{ t.noChats }}</div>

</div>

</div>

</div>

</aside>

<div class="splitter" v-if="isDesktop" :class="{dragging:isResizing}" @mousedown.prevent="startResize"></div>

<main class="chat">

<template v-if="selectedContact">

<header class="hbar">

<div class="h-user">

<button class="m-toggle" @click="showSidebar = true"><i class="ri-menu-line"></i></button>

<div class="avatar" style="width:50px;height:50px;">

<template v-if="Number(selectedContact.id) === -1">
<div class="saved-avatar"><i class="ri-bookmark-fill"></i></div>
</template>
<template v-else>
<img v-if="selectedContact.avatar_url" :src="normalizeAvatarUrl(selectedContact.avatar_url)" :alt="selectedContact.name" v-on:error="handleAvatarError($event, selectedContact)">
<span v-else>@{{ getAuthorInitial(selectedContact.name) }}</span>
</template>

</div>

<div>

<h3 style="cursor:pointer;" @click="Number(selectedContact.id) !== -1 ? openProfile(selectedContact) : null">@{{ selectedContact.name }}</h3>

<div class="h-status">

<span v-if="Number(selectedContact.id) === -1" style="font-size:12px;color:var(--muted);">مساحة آمنة — احفظ ما تشاء</span>

<template v-else>
<span class="dot" :class="{ offline: !selectedContact.isOnline }"></span>

<span>@{{ selectedContact.isTyping ? 'يكتب الآن...' : (selectedContact.isOnline ? t.onlineNow : (t.lastSeen + ' ' + formatLastSeen(selectedContact))) }}</span>
</template>

</div>

</div>

</div>

<div class="h-actions">

<button v-if="Number(selectedContact.id) === -1" class="h-icon-btn" @click="openSavedMedia" title="الوسائط"><i class="ri-image-2-line"></i></button>

<template v-if="Number(selectedContact.id) !== -1">
<button class="h-icon-btn" @click="openSearchPanel" title=""><i class="ri-search-line"></i></button>

<button class="h-icon-btn" @click="startCall('voice')" title=""><i class="ri-phone-line"></i></button>

<button class="h-icon-btn" @click="startCall('video')" title=""><i class="ri-vidicon-line"></i></button>

<button class="h-icon-btn" @click="openProfile(selectedContact)" title=""><i class="ri-information-line"></i></button>
</template>

<button class="h-icon-btn" @click.stop="toggleHeaderMenu" title=""><i class="ri-more-2-fill"></i></button>

<div class="chat-menu" v-if="headerMenuOpen" @click.stop>

<button @mouseenter="headerSub='mute'" @mouseleave="headerSub=null"><i class="ri-volume-mute-line"></i> كتم المحادثة <i class="ri-arrow-left-s-line" style="margin-right:auto"></i></button>

<button @click="openProfile(selectedContact)"><i class="ri-user-line"></i> ملف المستخدم</button>

<button @click="openWallpaperPicker"><i class="ri-palette-line"></i> خلفية المحادثة</button>

<button @click="openMediaGallery"><i class="ri-image-2-line"></i> وسائط المحادثة</button>

<button @click="openSavedMessages"><i class="ri-bookmark-line"></i> الرسائل المحفوظة</button>

<button @click="clearChatHistory"><i class="ri-eraser-line"></i> مسح المحادثة</button>

<button class="danger"><i class="ri-delete-bin-6-line"></i> حذف المحادثة</button>

<div class="chat-menu-sub" v-if="headerSub==='mute'" @mouseenter="headerSub='mute'" @mouseleave="headerSub=null">

<button @click="headerMenuOpen=false; headerSub=null; muteOptionsOpen=true"><i class="ri-timer-line"></i> @{{ contactMuted ? 'تعديل الكتم' : 'كتم المحادثة' }}</button>

<button v-if="contactMuted" @click="unmuteContact"><i class="ri-volume-up-line"></i> إلغاء الكتم</button>

<button @click="headerMenuOpen=false; headerSub=null; tonePickerOpen=true"><i class="ri-music-line"></i> نغمة الإشعارات</button>

<button @click="toggleSoundEnabled"><i class="ri-volume-up-line"></i> @{{ perContactSoundEnabled ? 'تعطيل الصوت' : 'تفعيل الصوت' }}</button>

</div>

</div>

</div>

</header>

<!-- E2E Encryption Info Card -->
<div class="e2e-info-banner" v-if="selectedContact && !e2eInfoDismissed">
  <div class="e2e-info-inner">
    <i class="ri-lock-2-fill e2e-icon"></i>
    <div class="e2e-text">
      <span>الرسائل والمكالمات <strong>مشفرة</strong> — لا يمكن لأحد خارج هذه المحادثة قراءتها أو الاستماع إليها.</span>
      <span v-if="e2eEnabled" class="e2e-status-badge active"><i class="ri-shield-check-fill"></i> تشفير طرف-لطرف نشط</span>
      <span v-else class="e2e-status-badge"><i class="ri-shield-line"></i> TLS مشفّر بالنقل</span>
    </div>
    <button class="e2e-dismiss" @click="e2eInfoDismissed=true; localStorage.setItem('e2e_info_dismissed','1')" title="إغلاق">
      <i class="ri-close-line"></i>
    </button>
  </div>
</div>

<!-- Pinned messages banner -->
<div class="pinned-banner" v-if="pinnedMessagesForCurrentChat.length" @click="pinnedListOpen = !pinnedListOpen">
<i class="ri-pushpin-2-fill"></i>
<span class="pinned-banner-text" v-if="!pinnedListOpen">@{{ pinnedMessagesForCurrentChat[pinnedMessagesForCurrentChat.length - 1].content || 'رسالة مثبتة' }}</span>
<span class="pinned-banner-text" v-else>@{{ pinnedMessagesForCurrentChat.length }} رسالة مثبتة</span>
<span class="pinned-banner-count" v-if="pinnedMessagesForCurrentChat.length > 1">@{{ pinnedMessagesForCurrentChat.length }}</span>
<i class="ri-arrow-down-s-line pinned-banner-chevron" :class="{open: pinnedListOpen}"></i>
</div>
<div class="pinned-list-dropdown" v-if="pinnedListOpen && pinnedMessagesForCurrentChat.length" @click.self="pinnedListOpen = false">
<div class="pinned-list-card">
<div class="pinned-list-item" v-for="msg in pinnedMessagesForCurrentChat" :key="'pin-'+msg.id" @click="jumpToPinnedMessage(msg); pinnedListOpen = false">
<i class="ri-pushpin-2-fill"></i>
<span class="pinned-list-text">@{{ msg.content || (msg.messageType === 'image' ? 'صورة' : msg.messageType === 'video' ? 'فيديو' : msg.messageType === 'audio' ? 'رسالة صوتية' : 'مرفق') }}</span>
<button class="pinned-list-unpin" @click.stop="messageContextMessage = msg; contextPin()"><i class="ri-close-line"></i></button>
</div>
</div>
</div>

<!-- Voice Player Bar -->
<div class="voice-player-bar" v-if="voicePlayerMessage" :key="'vpb-' + voicePlayerMessage.id" @click="onVoicePlayerBarClick">
  <div class="vpb-inner">
    <div class="vpb-left">
      <button class="vpb-btn" @click="playPrevVoice" :title="t.prev || 'السابق'"><i class="ri-skip-back-line"></i></button>
      <button class="vpb-btn vpb-play-btn" @click="toggleAudioPlayback(voicePlayerMessage)">
        <i :class="voicePlayerMessage.isPlaying ? 'ri-pause-fill' : 'ri-play-fill'"></i>
      </button>
      <button class="vpb-btn" @click="playNextVoice" :title="t.next || 'التالي'"><i class="ri-skip-forward-line"></i></button>
      <div class="vpb-meta">
        <div class="vpb-sender">@{{ voicePlayerMessage.senderName || '---' }}</div>
        <div class="vpb-sub">
          <span class="vpb-time">@{{ formatMessageTime(voicePlayerMessage.created_at) }}</span>
          <span class="vpb-sep">·</span>
          <span class="vpb-pos">@{{ formatDuration(voicePlayerPosition) }} / @{{ formatDuration(voicePlayerMessage.audioDuration || messageDuration(voicePlayerMessage)) }}</span>
        </div>
      </div>
    </div>
    <div class="vpb-right">
      <button class="vpb-btn vpb-speed-btn" @click="cycleVoiceSpeed" :title="'سرعة التشغيل'">@{{ voiceSpeed }}x</button>
      <button class="vpb-btn" @click="voicePlayerMuted = !voicePlayerMuted; activeAudioElement && (activeAudioElement.muted = voicePlayerMuted)" :title="voicePlayerMuted ? 'إلغاء كتم' : 'كتم'">
        <i :class="voicePlayerMuted ? 'ri-volume-mute-line' : 'ri-volume-up-line'"></i>
      </button>
      <button class="vpb-btn vpb-close-btn" @click="closeVoicePlayer" title="إغلاق"><i class="ri-close-line"></i></button>
    </div>
  </div>
  <div class="vpb-track"
       @click.stop
       @pointerdown.prevent.stop="beginVoicePlayerSeek($event)"
       @pointermove.prevent="moveVoicePlayerSeek($event)"
       @pointerup.prevent="endVoicePlayerSeek($event)"
       @pointercancel.prevent="endVoicePlayerSeek($event)">
    <div class="vpb-track-fill" :style="{width: voicePlayerPercent + '%'}"></div>
  </div>
</div>

<section class="feed" ref="messagesContainer" @scroll="onFeedScroll">

<transition name="day-fade">

<div class="day-floating" v-if="floatingDayLabel">@{{ floatingDayLabel }}</div>

</transition>

<template v-if="messages.length">

<template v-for="(message, idx) in sortedMessages" :key="message.id">

<div class="day" v-if="showDayDivider(idx)">@{{ dayLabel(message.createdAt) }}</div>

<div class="day unread" :id="`unread-separator-${selectedContact ? selectedContact.id : ''}`" v-if="shouldShowUnreadDivider(idx)">

@{{ t.unreadMessages }} (@{{ unreadIncomingCount() }})

</div>

<div class="row"

:id="`message-${message.id}`"

:class="[Number(message.senderId) === Number(currentUserId) ? 'mine' : 'other', { 'reply-focus': Number(highlightedMessageId) === Number(message.id), 'has-reply': !!message.replyTo, 'is-reply-target': replyingToMessage?.id === message.id, pending: !!message.pending, 'tools-open': toolsMessageId === message.id }]"

:style="{ animationDelay: ((idx % 8) * 18) + 'ms' }"

@dblclick="settingsChats.doubleClickAction === 'react' ? toggleReaction(message, '❤️') : replyToMessage(message)"

@mouseenter="onMessageHoverEnter(message.id)"

@mouseleave="onMessageHoverLeave(message.id)"

@contextmenu.prevent="openMessageContext($event, message)"

@touchstart.passive="onMessageTouchStart(message.id, $event, message)"

@touchend="onMessageTouchEnd"

@touchcancel="onMessageTouchEnd">

<div class="bubble">

<div class="acts">

<button @click="replyToMessage(message)" :title="t.reply"><i class="ri-corner-up-right-line"></i></button>

<button v-if="Number(message.senderId) === Number(currentUserId) && !['audio','video','sticker_static','sticker_animated','gif'].includes(message.messageType)" @click="editMessage(message)" :title="t.edit"><i class="ri-edit-2-line"></i></button>

<button v-if="Number(message.senderId) === Number(currentUserId)" class="d" @click="deleteMessage(message)" :title="t.delete"><i class="ri-delete-bin-6-line"></i></button>

</div>

<div class="reply" v-if="message.replyTo" @click="jumpToRepliedMessage(message.replyTo)" :class="Number(message.replyTo.senderId) === Number(currentUserId) ? 'reply-mine' : 'reply-other'">

<strong><svg class="reply-inline-icon" viewBox="0 0 12 12" fill="none"><path d="M3.5 5L2.5 6L3.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.5 6H8.5C8.77614 6 9 5.77614 9 5.5V4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/></svg> @{{ message.replyTo.senderName || t.previousMessage }}</strong>

<small>@{{ getMessagePreviewText(message.replyTo) }}</small>

</div>

<div class="status-reply-card" v-if="message.statusRefId" @click="jumpToStatusFromMessage(message)">
<div class="status-reply-card-header">
<div class="status-reply-card-avatar">
<img v-if="getStatusPreview(message.statusRefId)?.userAvatar && !brokenStatusAvatars.includes(Number(message.statusRefId))" :src="getStatusPreview(message.statusRefId)?.userAvatar" alt="" v-on:error="onStatusAvatarError($event, message.statusRefId)">
<span v-else>@{{ getAuthorInitial(getStatusPreview(message.statusRefId)?.userName) || 'S' }}</span>
</div>
<div class="status-reply-card-info">
<span class="status-reply-card-name">
@{{ getStatusPreview(message.statusRefId)?.userName || 'حالة' }}
<svg class="status-reply-indicator" viewBox="0 0 22 22" fill="none"><circle cx="7.5" cy="11" r="3" stroke="currentColor" stroke-width="1.7"/><circle cx="7.5" cy="11" r="1.3" fill="currentColor"/><path d="M13 8L16 11L13 14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 11H10.5C8.85 11 7.5 9.65 7.5 8V7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
</span>
<span class="status-reply-card-label">رد على حالتك</span>
</div>
<span class="status-reply-card-time">@{{ formatMessageTime(message) }}</span>
</div>
<div class="status-reply-card-preview" v-if="getStatusPreview(message.statusRefId)?.type === 'image' || getStatusPreview(message.statusRefId)?.type === 'video'">
<img :src="getStatusPreview(message.statusRefId)?.contentUrl" alt="">
<div class="status-reply-card-overlay" v-if="getStatusPreview(message.statusRefId)?.textContent">
<span>@{{ getStatusPreview(message.statusRefId)?.textContent }}</span>
</div>
</div>
<div class="status-reply-card-preview is-text" v-else-if="getStatusPreview(message.statusRefId)?.textContent" :style="{ background: getStatusPreview(message.statusRefId)?.bgColor || 'linear-gradient(135deg,#1a1a2e,#16213e)' }">
@{{ getStatusPreview(message.statusRefId)?.textContent?.substring(0, 60) }}
</div>
<div class="status-reply-card-preview is-expired" v-else>
<svg class="status-reply-preview-icon" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none" opacity=".3"/><path d="M12 8v5M12 15.5v.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".3"/></svg>
<span>انتهت الحالة</span>
</div>
</div>

<div class="txt" v-if="message.content && message.messageType === 'text'">@{{ message.content }}</div>

<div class="sticker-bubble" v-if="message.messageType === 'sticker_static' || message.messageType === 'sticker_animated'" @click="openStickerViewer(message)">
<img v-if="message.messageType === 'sticker_static'" :src="message.attachmentUrl" :alt="message.attachmentName" class="sticker-bubble-media" v-on:error="markMediaAsBroken(message)">
<video v-else :src="message.attachmentUrl" class="sticker-bubble-media" autoplay loop muted playsinline></video>
</div>

<div class="media gif-bubble" v-if="message.messageType === 'gif'" @click="openMediaModal(message)">
<div class="media-skeleton" v-if="!message.mediaLoaded"></div>
<img :src="message.attachmentUrl" :alt="message.attachmentName" :class="{ 'media-loaded': message.mediaLoaded }" @load="onMediaLoaded(message)" v-on:error="markMediaAsBroken(message); onMediaLoaded(message)">
</div>

<div class="media" v-if="message.messageType === 'image'" @click="message.isSensitive && !revealedSensitiveIds.has(message.id) ? revealSensitiveMessage(message.id) : openMediaModal(message)">

<div class="media-skeleton" v-if="!message.mediaLoaded"></div>

<img :src="message.attachmentUrl" :alt="message.attachmentName"

:class="{ 'media-loaded': message.mediaLoaded, 'sensitive-blur': message.isSensitive && !revealedSensitiveIds.has(message.id) }"

@load="onMediaLoaded(message)"

v-on:error="markMediaAsBroken(message); onMediaLoaded(message)">

<div class="sensitive-overlay" v-if="message.isSensitive && !revealedSensitiveIds.has(message.id)"><i class="ri-eye-off-line"></i><span>محتوى حساس — اضغط للإظهار</span></div>

</div>

<div class="media" v-if="message.messageType === 'video'" @click="message.isSensitive && !revealedSensitiveIds.has(message.id) ? revealSensitiveMessage(message.id) : openMediaModal(message)">

<div class="media-skeleton" v-if="!message.mediaLoaded"></div>

<video :src="message.attachmentUrl" preload="metadata" muted

:class="{ 'media-loaded': message.mediaLoaded, 'sensitive-blur': message.isSensitive && !revealedSensitiveIds.has(message.id) }"

@loadedmetadata="onMediaLoaded(message)"

v-on:error="markMediaAsBroken(message); onMediaLoaded(message)"></video>

<div class="sensitive-overlay" v-if="message.isSensitive && !revealedSensitiveIds.has(message.id)"><i class="ri-eye-off-line"></i><span>محتوى حساس — اضغط للإظهار</span></div>

</div>

<div class="audio" v-if="message.messageType === 'audio'" :class="{ 'is-playing': message.isPlaying }">

<template v-if="message.attachmentUrl">

<button class="play" :class="{ 'is-playing': message.isPlaying }" @click="toggleAudioPlayback(message)">

<i :class="message.isPlaying ? 'ri-pause-line' : 'ri-play-line'"></i>

</button>

<div class="wave" :data-mid="message.id"

@pointerdown.prevent.stop="beginWaveSeek(message, $event)"
@pointermove.prevent.stop="moveWaveSeek(message, $event)"
@pointerup.prevent.stop="endWaveSeek(message, $event)"
@pointercancel.prevent.stop="endWaveSeek(message, $event)">

<div v-for="bar in 22" :key="bar" class="bar" :class="{ on: message.currentBar >= bar }"

:style="{ height: ((bar % 7) * 3 + 6) + 'px' }"></div>

</div>

<span class="audio-time">@{{ formatDuration(message.playbackPosition || 0) }} / @{{ formatDuration(message.audioDuration || 0) }}</span>

</template>

<template v-else>

<i class="ri-error-warning-line"></i>

<span>@{{ t.attachment }}</span>

</template>

</div>

<div class="file" v-if="message.messageType === 'file'">

<i :class="getFileIcon(message.attachmentMime)"></i>

<div style="min-width:0;flex:1;">

<div class="file-name">@{{ message.attachmentName || t.attachment }}</div>

<a v-if="message.attachmentUrl" :href="message.attachmentUrl" target="_blank" rel="noopener noreferrer">@{{ t.download }}</a>

<span v-else style="font-size:12px;color:var(--muted);">@{{ t.attachment }}</span>

</div>

</div>

<div class="call-card-bubble" v-if="message.messageType === 'call'" @click.stop="callFromMessage(message)">
<div class="call-bubble-icon">
<i :class="message.callType === 'video' ? 'ri-vidicon-line' : 'ri-phone-line'"></i>
</div>
<div class="call-bubble-info">
<div class="call-bubble-dir">@{{ message.callDirection === 'incoming' ? 'واردة' : 'صادرة' }}</div>
<div class="call-bubble-status" :class="{ missed: message.callStatus === 'missed' }">
<i class="ri-arrow-down-line" v-if="message.callDirection === 'incoming' && message.callStatus === 'missed'"></i>
<i class="ri-arrow-up-line" v-else-if="message.callDirection === 'outgoing' && message.callStatus === 'missed'"></i>
<i class="ri-arrow-down-line" v-else-if="message.callDirection === 'incoming'"></i>
<i class="ri-arrow-up-line" v-else></i>
@{{ message.callStatus === 'missed' ? (message.callDirection === 'outgoing' ? 'مكالمة ملغاة' : 'مكالمة فائتة') : (message.callDuration ? formatDuration(message.callDuration) : 'مكتملة') }}
</div>
</div>
</div>

<div class="meta">

<span v-if="message.isEdited">@{{ t.edited }}</span>

<i v-if="isMessageSaved(message)" class="ri-bookmark-fill" style="font-size:11px;color:var(--gold);opacity:.85;" title="محفوظة"></i>

<span>@{{ formatMessageTime(message.createdAt) }}</span>

<span class="pending-indicator" v-if="message.pending && !message.failed"></span>

<button v-if="message.failed" class="msg-retry" @click.stop="retryFailedMessage(message)" :title="t.retry || 'إعادة المحاولة'">

<i class="ri-error-warning-line"></i>

<span>@{{ t.retry || 'إعادة المحاولة' }}</span>

</button>

<span v-if="Number(message.senderId) === Number(currentUserId) && !message.pending && !message.failed"

class="ticks"

:class="message.readAt ? 'two' : 'one'">

<i :class="message.readAt ? 'ri-check-double-line' : 'ri-check-line'"></i>

</span>

</div>

<!-- Reaction bar + picker -->

<div class="reactions-bar" v-if="getMessageReactions(message.id).length">

<button v-for="r in getMessageReactions(message.id)" :key="r.emoji"

class="reaction-chip" :class="{ mine: r.myReaction }"

@click="toggleReaction(message, r.emoji)">

<span class="rc">@{{ r.emoji }}</span>

<span class="rn">@{{ r.count }}</span>

</button>

</div>

</div>

</div>

</template>

</template>

<div v-else class="empty">@{{ t.startChatPrompt }}</div>

</section>

<div class="float-actions" :style="replyingToMessage ? { bottom: '210px' } : {}">

<button class="jump-latest" v-show="showJumpToLatest || unreadRemainingCount > 0" @click="handleFloatingJump" :title="unreadRemainingCount > 0 ? t.unreadMessages : t.jumpToLatest">

<i class="ri-arrow-down-line"></i>

<transition name="count-drop" mode="out-in">

<span class="unread-count" v-if="unreadRemainingCount > 0" :key="unreadRemainingCount">@{{ unreadRemainingCount }}</span>

</transition>

</button>

</div>

<footer class="composer">

<transition name="reply-chip">
<div class="top-chip" v-if="replyingToMessage" :class="Number(replyingToMessage.senderId) === Number(currentUserId) ? 'reply-mine' : 'reply-other'"
@pointerdown="replySwipeY=0; replySwipeStart=$event.clientY; replySwiping=false"
@pointermove="if(replySwipeStart){ const dy=$event.clientY-replySwipeStart; if(dy>5)replySwiping=true; if(replySwiping&&dy>0)replySwipeY=dy }"
@pointerup="if(replySwiping&&replySwipeY>30){replyingToMessage=null}; replySwipeY=0; replySwipeStart=0; replySwiping=false"
@pointerleave="replySwipeY=0; replySwipeStart=0; replySwiping=false"
:style="replySwipeY>0&&replySwiping ? {transform:'translateY('+Math.min(replySwipeY,100)+'px)',opacity:Math.max(0.1,1-replySwipeY/100)} : {}">

<div class="chip-head">

<span>@{{ t.replyTo }} @{{ replyingToMessage.senderName || (Number(replyingToMessage.senderId) === Number(currentUserId) ? t.you : selectedContact.name) }}</span>

<button @click="replyingToMessage = null"><i class="ri-close-line"></i></button>

</div>

<div class="chip-text clickable" @click="jumpToRepliedMessage(replyingToMessage)">

@{{ getMessagePreviewText(replyingToMessage) }}

</div>

</div>

</transition>

<div class="top-chip" v-if="pendingAttachments.length">

<div class="chip-head">

<span>@{{ t.attachments }} (@{{ pendingAttachments.length }})</span>

<label v-if="settingsChats.allowSensitiveContent" style="display:flex;align-items:center;gap:4px;font-size:11px;cursor:pointer;margin-inline-end:auto;">
<input type="checkbox" v-model="pendingAttachmentsSensitive" style="cursor:pointer;">
محتوى +18
</label>

<button @click="clearAllAttachments"><i class="ri-delete-bin-6-line"></i></button>

</div>

<div style="display:flex;flex-wrap:wrap;gap:7px;">

<div v-for="(att, i) in pendingAttachments" :key="i"

style="min-width:110px;max-width:220px;border:1px solid var(--soft-2);border-radius:10px;padding:7px;">

<div style="display:flex;justify-content:space-between;gap:6px;align-items:center;">

<span style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@{{ att.name }}</span>

<button @click="removeAttachment(i)" style="border:none;background:transparent;color:#ff9aa5;cursor:pointer;"><i class="ri-close-circle-line"></i></button>

</div>

<div style="margin-top:6px;font-size:11px;color:var(--muted);">@{{ getPendingAttachmentLabel(att) }}</div>

<img v-if="att.previewType === 'image' && att.previewUrl" :src="att.previewUrl" alt="" style="margin-top:6px;width:100%;max-height:90px;object-fit:cover;border-radius:8px;">

<video v-else-if="att.previewType === 'video' && att.previewUrl" :src="att.previewUrl" preload="metadata" muted style="margin-top:6px;width:100%;max-height:90px;object-fit:cover;border-radius:8px;background:color-mix(in srgb, var(--panel-2) 88%, transparent);"></video>

<div v-if="att.uploading || att.progress > 0" style="margin-top:8px;">

<div style="height:6px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden;">

<div :style="`height:100%;width:${Math.max(2, Math.min(100, Number(att.progress || 0)))}%;background:linear-gradient(90deg,var(--gold),var(--gold-2));transition:width .2s ease;`"></div>

</div>

<div style="margin-top:4px;font-size:11px;color:var(--muted);">@{{ Math.round(att.progress || 0) }}%</div>

</div>

</div>

</div>

</div>

<div class="recording-hint" v-if="isRecording && !isRecordingLocked">

<span>@{{ t.recordingNow }} @{{ formatDuration(recordingDurationSec) }}</span>

<span class="lock-gesture"><i class="ri-lock-line"></i> @{{ t.slideToLock }}</span>

</div>

<div class="lock-chip" v-if="isRecordingLocked">

<div class="lock-row">

<strong>@{{ t.lockedRecording }} @{{ formatDuration(recordingDurationSec) }}</strong>

<span v-if="isRecordingPaused">@{{ t.paused }}</span>

</div>

<div class="lock-actions">

<button class="d" @click="cancelRecording"><i class="ri-delete-bin-6-line"></i> @{{ t.delete }}</button>

<button @click="togglePauseResumeRecording">

<i :class="isRecordingPaused ? 'ri-play-line' : 'ri-pause-line'"></i>

@{{ isRecordingPaused ? t.resume : t.pause }}

</button>

<button @click="finishLockedRecording"><i class="ri-send-plane-fill"></i> @{{ t.sendNow }}</button>

</div>

</div>

<div class="recording-hint" v-if="showRecordHint && !isRecording && !isRecordingLocked">

<span><i class="ri-mic-line"></i> @{{ t.holdToRecord }}</span>

<span class="lock-gesture"><i class="ri-lock-line"></i> @{{ t.slideToLock }}</span>

</div>

<div class="row-compose" v-if="selectedContact">

<div class="blocked-compose-bar" v-if="contactBlocked && Number(selectedContact.id) !== -1">
<i class="ri-forbid-line"></i>
<span>تم حظر هذا المستخدم</span>
<button class="unblock-inline-btn" @click="unblockContact">رفع الحظر</button>
<button class="delete-chat-inline-btn" @click="deleteChatWithContact">حذف الدردشة</button>
</div>

<template v-if="!contactBlocked">
<div class="lock-float" v-if="isRecording && !isRecordingLocked"><i class="ri-lock-line"></i></div>

<transition name="swap" mode="out-in">

<button class="action-btn"

v-if="showSendAction"

key="send-action"

@click="sendMessage"

:title="t.sendNow">

<i class="ri-send-plane-fill"></i>

</button>

<button class="action-btn mic-action-btn"

v-else

key="mic-action"

:class="{ recording: isRecording }"

:disabled="isRecordingLocked"

:title="t.voice"

@click.prevent="showRecordHintBriefly"

@pointerdown.prevent="startHoldRecording"

@pointermove.prevent="onHoldRecordingMove"

@pointerup.prevent="onHoldRecordingEnd"

@pointercancel.prevent="onHoldRecordingEnd"

@contextmenu.prevent>

<i class="ri-mic-line"></i>

</button>

</transition>

<div class="ibox" ref="composerMain">

<button class="ibtn" :title="t.emoji" @click="toggleEmojiPicker(); ensureEmojiData()" @mouseenter="onEmojiHoverEnter" @mouseleave="onEmojiHoverLeave"><i class="ri-emotion-happy-line"></i></button>

<textarea ref="messageInput" v-model="messageInput" :placeholder="t.writeMessage" :spellcheck="settingsChats.spellcheckEnabled" @input="autoResizeTextarea(); notifyTyping()" @keydown.enter="handleComposeEnter" :style="{ height: textareaHeight + 'px', maxHeight: '180px', overflowY: textareaHeight >= 180 ? 'auto' : 'hidden' }"></textarea>

<div class="picker-panel" v-if="showEmojiPicker" @mouseenter="onEmojiHoverEnter" @mouseleave="onEmojiHoverLeave">

<div class="picker-tabs">
<button :class="{active: pickerActiveTab === 'emoji'}" @click="switchPickerTab('emoji')"><i class="ri-emotion-happy-line"></i> إيموجي</button>
<button :class="{active: pickerActiveTab === 'gif'}" @click="switchPickerTab('gif')"><i class="ri-film-line"></i> GIF</button>
<button :class="{active: pickerActiveTab === 'sticker'}" @click="switchPickerTab('sticker')"><i class="ri-sticky-note-line"></i> ملصقات</button>
</div>

<div class="picker-body" v-if="pickerActiveTab === 'emoji'">
<!-- Category icon bar (hidden during search) -->
<div class="emoji-cat-bar" v-if="!emojiSearchQuery">
  <button class="emoji-cat-icon" :class="{active: emojiActiveCatId==='recent'}" @click="scrollToEmojiCat('recent')" v-if="emojiRecentList.length" title="الأخيرة"><i class="ri-time-line"></i></button>
  <button class="emoji-cat-icon" v-for="cat in emojiCategoriesData" :key="'bar-'+cat.id" :class="{active: emojiActiveCatId===cat.id}" @click="scrollToEmojiCat(cat.id)" :title="cat.label"><i :class="cat.icon || 'ri-emotion-happy-line'"></i></button>
</div>
<input class="picker-search" type="text" v-model="emojiSearchQuery" @input="emojiActiveCatId=''" placeholder="بحث (بالإنجليزية)... مثل crying">
<div class="picker-scroll" ref="emojiPickerScroll" @scroll.passive="onEmojiPickerScroll">
<div class="emoji-cat" data-cat-id="recent" v-if="!emojiSearchQuery && emojiRecentList.length">
<div class="emoji-cat-label">الأخيرة</div>
<div class="emoji-grid">
<button v-for="(e, idx) in emojiRecentList" :key="'recent-'+idx" @click="addEmojiChar(e)">@{{ e }}</button>
</div>
</div>
<div class="emoji-cat" :data-cat-id="cat.id" v-for="cat in filteredEmojiCategories" :key="cat.id">
<div class="emoji-cat-label">@{{ cat.label }}</div>
<div class="emoji-grid">
<button v-for="(e, idx) in cat.emojis" :key="cat.id+'-'+idx" @click="addEmojiChar(e.c)">@{{ e.c }}</button>
</div>
</div>
<div class="picker-empty" v-if="emojiCategoriesLoaded && filteredEmojiCategories.length === 0">لا توجد نتائج</div>
</div>
</div>

<div class="picker-body" v-if="pickerActiveTab === 'gif'">
<input class="picker-search" type="text" v-model="gifSearchQuery" @input="onGifSearchInput" placeholder="ابحث عن GIF...">
<div class="picker-scroll gif-scroll">
<div class="gif-loading" v-if="gifLoading"><i class="ri-loader-4-line spin"></i></div>
<div class="gif-grid" v-else-if="gifResults.length">
<img v-for="g in gifResults" :key="g.id" :src="g.previewUrl" @click="sendGif(g)" loading="lazy">
</div>
<div class="picker-empty" v-else-if="gifSearchedOnce && !gifLoading">لا توجد نتائج</div>
<div class="picker-empty" v-else>اكتب كلمة للبحث عن GIF</div>
</div>
</div>

<div class="picker-body" v-if="pickerActiveTab === 'sticker'">
<div class="sticker-create-row">
<button class="sticker-create-btn" @click="openCreateStickerModal('image')"><i class="ri-image-add-line"></i> ملصق من صورة</button>
<button class="sticker-create-btn" @click="openCreateStickerModal('video')"><i class="ri-video-add-line"></i> ملصق متحرك من فيديو</button>
</div>
<div class="picker-scroll">
<div class="sticker-loading" v-if="stickersLoading"><i class="ri-loader-4-line spin"></i></div>
<template v-else>
<div class="emoji-cat" v-if="stickerFavorites.length">
<div class="emoji-cat-label">المفضلة</div>
<div class="sticker-grid">
<div class="sticker-item" v-for="s in stickerFavorites" :key="'fav-'+s.id">
<img v-if="s.type === 'static'" :src="s.url" @click="sendSticker(s)">
<video v-else :src="s.url" autoplay loop muted playsinline @click="sendSticker(s)"></video>
</div>
</div>
</div>
<div class="emoji-cat" v-if="stickerRecent.length">
<div class="emoji-cat-label">الأخيرة</div>
<div class="sticker-grid">
<div class="sticker-item" v-for="s in stickerRecent" :key="'recent-'+s.id">
<img v-if="s.type === 'static'" :src="s.url" @click="sendSticker(s)">
<video v-else :src="s.url" autoplay loop muted playsinline @click="sendSticker(s)"></video>
</div>
</div>
</div>
<div class="emoji-cat" v-if="stickerList.length">
<div class="emoji-cat-label">ملصقاتي</div>
<div class="sticker-grid">
<div class="sticker-item" v-for="s in stickerList" :key="s.id">
<img v-if="s.type === 'static'" :src="s.url" @click="sendSticker(s)">
<video v-else :src="s.url" autoplay loop muted playsinline @click="sendSticker(s)"></video>
<button class="sticker-fav-btn" :class="{active: stickerFavorites.some(f => f.id === s.id)}" @click.stop="toggleStickerFavorite(s)"><i class="ri-star-fill"></i></button>
<button class="sticker-del-btn" @click.stop="deleteSticker(s)"><i class="ri-close-line"></i></button>
</div>
</div>
</div>
<div class="picker-empty" v-if="!stickerList.length && !stickerFavorites.length && !stickerRecent.length">لا توجد ملصقات بعد — أنشئ أول ملصق لك</div>
</template>
</div>
</div>

</div>

</div>

<button class="attach-btn" :title="t.attach" @click="openFilePicker"><i class="ri-attachment-2"></i></button>

<input ref="fileInput" type="file" style="display:none" multiple @change="handleFileSelect">

</template>

</div>

</footer>

</template>

<template v-else><div class="empty">@{{ t.selectChat }}</div></template>

</main>

</div>

<!-- Isolated sticker viewer: separate from photo/video gallery -->
<div class="sticker-viewer-overlay" v-if="stickerViewer" @click.self="closeStickerViewer">
<div class="sticker-viewer-card">
<button class="h-icon-btn sticker-viewer-close" @click="closeStickerViewer"><i class="ri-close-line"></i></button>
<div class="sticker-viewer-media">
<img v-if="stickerViewer.messageType === 'sticker_static'" :src="stickerViewer.attachmentUrl" :alt="stickerViewer.attachmentName">
<video v-else :src="stickerViewer.attachmentUrl" autoplay loop muted playsinline></video>
</div>
<div class="sticker-viewer-actions">
<button v-if="Number(stickerViewer.senderId) === Number(currentUserId) || findStickerByUrl(stickerViewer.attachmentUrl)" class="profile-action-btn" @click="toggleStickerFavoriteFromMessage(stickerViewer)">
<i :class="isStickerFavoritedByUrl(stickerViewer.attachmentUrl) ? 'ri-star-fill' : 'ri-star-line'"></i>
@{{ isStickerFavoritedByUrl(stickerViewer.attachmentUrl) ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}
</button>
<button v-else class="profile-action-btn primary" :disabled="stickerSaveBusy" @click="saveStickerToMyLibrary(stickerViewer)">
<i class="ri-download-2-line"></i> حفظ الملصق في مكتبتي
</button>
</div>
</div>
</div>

<div class="media-modal" v-if="mediaModal" @click.self="closeMediaModal">

<div class="media-box media-box--wide">

<header>

<div class="media-meta">

<span class="media-meta-avatar">
  <img v-if="mediaModal.senderAvatar" :src="mediaModal.senderAvatar" v-on:error="$event.target.style.display='none'; $event.target.nextSibling.style.display='flex'" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
  <span v-else style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">@{{ mediaModal.senderInitial || '?' }}</span>
</span>

<div class="media-meta-text">

<span class="media-meta-name">@{{ mediaModal.senderName || t.previousMessage }}</span>

<span class="media-meta-time">@{{ mediaModal.metaTime || '' }}</span>

</div>

</div>

<div class="media-header-actions">
<button class="ibtn" title="رد" @click="mediaModalReply"><i class="ri-reply-line"></i></button>
<button v-if="mediaModal.isSticker" class="ibtn" title="مفضلة" @click="mediaModalToggleStickerFavorite"><i :class="isStickerFavoritedByUrl(mediaModal.url) ? 'ri-star-fill' : 'ri-star-line'"></i></button>
<button v-else class="ibtn" :title="isMessageSaved(mediaModal.message) ? 'إزالة من المحفوظات' : 'حفظ'" @click="mediaModalSave"><i :class="isMessageSaved(mediaModal.message) ? 'ri-bookmark-fill' : 'ri-bookmark-line'"></i></button>
<button class="ibtn" :title="mediaModal.message?.isPinned ? 'إلغاء التثبيت' : 'تثبيت'" @click="mediaModalPin"><i :class="mediaModal.message?.isPinned ? 'ri-pushpin-2-fill' : 'ri-pushpin-2-line'"></i></button>
<button class="ibtn" title="إعادة توجيه" @click="mediaModalForward"><i class="ri-share-forward-line"></i></button>
<button class="ibtn" title="تحميل" @click="downloadMediaModalItem"><i class="ri-download-2-line"></i></button>
<template v-if="mediaModal.type === 'image'">
<button class="ibtn" title="تكبير" @click="mediaZoomIn"><i class="ri-zoom-in-line"></i></button>
<button class="ibtn" title="تصغير" @click="mediaZoomOut"><i class="ri-zoom-out-line"></i></button>
<button class="ibtn" title="إعادة تعيين" @click="mediaZoomReset"><i class="ri-fullscreen-exit-line"></i></button>
</template>
<div class="media-modal-more" style="position:relative;">
<button class="ibtn" title="المزيد" @click.stop="mediaModalMoreOpen = !mediaModalMoreOpen"><i class="ri-more-2-fill"></i></button>
<div class="chat-menu" v-if="mediaModalMoreOpen" @click.stop style="position:absolute;top:42px;left:0;z-index:10;min-width:150px;">
<button v-if="mediaModal.message && Number(mediaModal.message.senderId) === Number(currentUserId)" class="danger" @click="mediaModalDelete"><i class="ri-delete-bin-line"></i> حذف</button>
</div>
</div>
<button class="ibtn" @click="closeMediaModal"><i class="ri-close-line"></i></button>
</div>

</header>

<button class="media-nav-arrow media-nav-prev" v-if="mediaModal.index > 0" @click="mediaModalPrev"><i class="ri-arrow-right-s-line"></i></button>
<button class="media-nav-arrow media-nav-next" v-if="mediaModal.index < mediaModalList.length - 1" @click="mediaModalNext"><i class="ri-arrow-left-s-line"></i></button>

<main>

<div v-if="mediaModal.type === 'image'" class="media-zoom-wrap"
     @wheel.prevent="mediaZoomWheel"
     @dblclick.prevent="mediaDblClickZoom"
     @mousedown.prevent="mediaZoomPanStart"
     @mousemove.prevent="mediaZoomPanMove"
     @mouseup.prevent="mediaZoomPanEnd"
     @mouseleave="mediaZoomPanEnd"
     @touchstart.passive="mediaZoomTouchStart"
     @touchmove.passive="mediaZoomTouchMove"
     @touchend.passive="mediaZoomTouchEnd"
     :style="{cursor: mediaImgZoom > 1 ? 'grab' : 'default'}">
  <img :src="mediaModal.url" :alt="mediaModal.name || ''"
       :style="{
         transform: 'translate('+mediaImgPanX+'px,'+mediaImgPanY+'px) scale('+mediaImgZoom+')',
         transformOrigin: 'center center',
         transition: mediaZoomTransition ? 'transform .12s ease' : 'none'
       }">
  <div class="media-zoom-level" v-if="mediaImgZoom !== 1">@{{ Math.round(mediaImgZoom * 100) + '%' }}</div>
</div>

<video v-else controls autoplay :key="mediaModal.url" :src="mediaModal.url" @play="onVideoPlay" @pause="onVideoPause" @ended="onVideoPause"></video>

</main>

<div class="media-caption-row" v-if="mediaModal.caption || mediaModal.isSticker">
<span class="media-caption-text" v-if="mediaModal.caption">@{{ mediaModal.caption }}</span>
<button class="media-fav-toggle-btn" v-if="mediaModal.isSticker" @click="mediaModalToggleStickerFavorite">
@{{ isStickerFavoritedByUrl(mediaModal.url) ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}
</button>
</div>

<div class="media-filmstrip" v-if="mediaModalList.length > 1">
<button v-for="(item, i) in mediaModalList" :key="item.id" class="media-thumb" :class="{active: i === mediaModal.index}" @click="showMediaModalAt(i)">
<img v-if="mediaModalKindOf(item) === 'image'" :src="item.attachmentUrl" loading="lazy">
<video v-else muted preload="metadata" :src="item.attachmentUrl + '#thumb'" @loadedmetadata="cacheMediaDuration(item, $event)"></video>
<span class="media-thumb-badge" v-if="item.messageType === 'gif'">GIF</span>
<span class="media-thumb-badge" v-else-if="mediaModalKindOf(item) === 'video' && mediaDurationCache[item.id]">@{{ formatMediaDuration(mediaDurationCache[item.id]) }}</span>
</button>
</div>

</div>

</div>

<div class="confirm-modal" v-if="deleteTargetMessage" @click.self="deleteTargetMessage = null">

<div class="confirm-card">

<div class="confirm-title">@{{ t.delete }}</div>

<div class="confirm-text">@{{ t.deleteConfirm }}</div>

<div class="confirm-actions">

<button @click="deleteTargetMessage = null">@{{ t.cancel }}</button>

<button class="danger" @click="confirmDeleteMessage">@{{ t.delete }}</button>

</div>

</div>

</div>

<div class="edit-modal" v-if="editTargetMessage" @click.self="closeEditModal">

<div class="edit-card">

<div class="confirm-title">@{{ t.edit }}</div>

<div class="confirm-text">@{{ t.editPrompt }}</div>

<textarea v-model="editInputText" :placeholder="t.writeMessage"></textarea>

<div class="edit-actions">

<button @click="closeEditModal">@{{ t.cancel }}</button>

<button class="save" @click="confirmEditMessage">@{{ t.save }}</button>

</div>

</div>

</div>

<div class="new-chat-modal" v-if="newConversationModal" @click.self="closeNewConversationModal">

<div class="new-chat-card">

<div class="confirm-title">@{{ t.newConversation }}</div>

<div class="confirm-text" v-if="newConversationStep === 'mode'">@{{ t.chooseConversationType }}</div>

<div class="confirm-text" v-else-if="newConversationStep === 'chat'">@{{ t.pickPersonForChat }}</div>

<div class="confirm-text" v-else-if="newConversationStep === 'group'">@{{ t.pickPeopleForGroup }}</div>

<div class="confirm-text" v-else>@{{ t.groupSetupHint }}</div>

<div class="new-chat-actions" v-if="newConversationStep === 'mode'">

<button class="primary" @click="newConversationStep = 'chat'"><i class="ri-chat-1-line"></i> @{{ t.startChat }}</button>

<button @click="newConversationStep = 'group'"><i class="ri-group-line"></i> @{{ t.startGroup }}</button>

</div>

<div v-else-if="newConversationStep === 'chat' || newConversationStep === 'group'" style="margin-top:8px;">

<div style="position:relative;margin-bottom:8px;">

<i class="ri-search-line" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:15px;pointer-events:none;"></i>

<input type="text" v-model="newChatSearchQuery" :placeholder="t.searchPlaceholder || 'ابحث عن شخص…'" style="width:100%;height:38px;border-radius:10px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);padding:0 34px 0 10px;outline:none;font-family:inherit;font-size:14px;">

</div>

<div class="new-chat-list">

<template v-if="newConversationStep === 'chat'">

<div class="new-chat-item" v-for="contact in newChatSearchResults" :key="`new-${contact.id}`" @click="startDirectConversation(contact)">

<div class="nci-avatar-wrap">

<img v-if="contact.avatar_url" :src="contact.avatar_url" alt="" class="nci-avatar" v-on:error="e=>e.target.style.display='none'">

<div v-else class="nci-avatar nci-initials">@{{ getAuthorInitial(contact.name) }}</div>

<span v-if="contact.isOnline" class="nci-online-dot"></span>

</div>

<div class="nci-meta">

<span class="nci-name">@{{ contact.name }}</span>

<span v-if="contact.lastSeenLabel" class="nci-lastseen">@{{ contact.lastSeenLabel }}</span>

</div>

<i class="ri-arrow-left-up-line" style="margin-right:auto;opacity:.5;font-size:14px;flex-shrink:0;"></i>

</div>

<div v-if="newChatSearchLoading" class="confirm-text" style="text-align:center;">...</div>

<div v-else-if="!newChatSearchResults.length" class="confirm-text">@{{ t.noNewContacts }}</div>

</template>

<template v-else>

<label class="new-chat-item" v-for="contact in newChatSearchResults" :key="`grp-${contact.id}`">

<div class="nci-avatar-wrap">

<img v-if="contact.avatar_url" :src="contact.avatar_url" alt="" class="nci-avatar" v-on:error="e=>e.target.style.display='none'">

<div v-else class="nci-avatar nci-initials">@{{ getAuthorInitial(contact.name) }}</div>

<span v-if="contact.isOnline" class="nci-online-dot"></span>

</div>

<div class="nci-meta">

<span class="nci-name">@{{ contact.name }}</span>

<span v-if="contact.lastSeenLabel" class="nci-lastseen">@{{ contact.lastSeenLabel }}</span>

</div>

<input type="checkbox" :value="contact.id" v-model="groupDraftSelection" style="flex-shrink:0;">

</label>

<div v-if="newChatSearchLoading" class="confirm-text" style="text-align:center;">...</div>

<div v-else-if="!newChatSearchResults.length" class="confirm-text">@{{ t.noNewContacts }}</div>

</template>

</div>

</div>

<div v-else style="display:grid;gap:10px;margin-top:10px;">

<input type="text" v-model.trim="groupDraftName" :placeholder="t.groupNamePlaceholder" style="width:100%;height:42px;border-radius:10px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);padding:0 12px;outline:none;">

<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;border:1px solid var(--theme-border);border-radius:10px;padding:10px;background:var(--panel-2);">

<span style="color:var(--muted);font-size:13px;">@{{ t.groupPhotoOptional }}</span>

<input type="file" ref="groupAvatarInput" accept="image/*" @change="onGroupAvatarChange" style="max-width:210px;">

</div>

<div v-if="groupDraftAvatarPreview" style="display:flex;justify-content:center;">

<img :src="groupDraftAvatarPreview" alt="" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:1px solid var(--theme-border-strong);">

</div>

</div>

<div class="new-chat-actions" v-if="newConversationStep !== 'mode'">

<button @click="newConversationStep = 'mode'">@{{ t.back }}</button>

<button class="primary" v-if="newConversationStep === 'group'" @click="createGroupDraft">@{{ t.next }}</button>

<button class="primary" v-else-if="newConversationStep === 'group-settings'" @click="submitGroupDraft" :disabled="groupCreateLoading">

<span v-if="groupCreateLoading"></span>

<span v-else>@{{ t.createGroup }}</span>

</button>

<button class="primary" v-else @click="closeNewConversationModal">@{{ t.cancel }}</button>

</div>

</div>

</div>

<!-- Toast container -->

<!-- == STATUS VIEWER == -->

<div class="status-viewer-overlay" v-if="statusViewerOpen">
<div class="status-tap-zone left" @click="nextStatus"></div>
<div class="status-tap-zone right" @click="prevStatus"></div>

<div class="status-progress-bars" v-show="!statusLongPressActive">

<div v-for="(s, i) in (statusViewerContact ? statusViewerContact.statuses : [])" :key="i" class="status-prog-seg">

<div class="status-prog-fill" :style="{width: i < statusViewerIndex ? '100%' : (i === statusViewerIndex ? statusViewerProgress + '%' : '0%')}"></div>

</div>

</div>

<div class="status-viewer-header" v-show="!statusLongPressActive">

<div class="status-viewer-avatar">

<img v-if="statusViewerContact && statusViewerContact.user_avatar" :src="normalizeAvatarUrl(statusViewerContact.user_avatar)" alt="" v-on:error="handleAvatarError($event, statusViewerContact)">

<span v-else>@{{ statusViewerContact ? getAuthorInitial(statusViewerContact.user_name) : '' }}</span>

</div>

<div class="status-viewer-meta">

<div class="status-viewer-name" style="cursor:pointer;" @click="statusViewerContact && openProfile({id: statusViewerContact.user_id, name: statusViewerContact.user_name, avatar_url: statusViewerContact.user_avatar || null, isOnline:false, lastSeenAt:null})">@{{ statusViewerContact ? statusViewerContact.user_name : '' }}</div>

<div class="status-viewer-time">@{{ currentStatus ? formatMessageTime(currentStatus.created_at || currentStatus.createdAt) : '' }}</div>

</div>

<button class="status-viewer-close" @click="closeStatusViewer"><i class="ri-close-line"></i></button>

</div>

<div class="status-viewer-content" :key="currentStatus ? currentStatus.id : 'no-st'" v-if="currentStatus"
@click="handleStatusContentClick"
@mousedown="startStatusLongPress" @mouseup="endStatusLongPress" @mouseleave="cancelStatusLongPress"
@touchstart.prevent="startStatusLongPress" @touchend="endStatusLongPress" @touchmove.prevent="cancelStatusLongPress"
:style="{background: statusViewerBackground(currentStatus)}">

<div class="status-viewer-media-backdrop" v-if="(currentStatus.content_url || currentStatus.contentUrl)" :style="{ backgroundImage: 'url(' + (currentStatus.contentUrl || ('/storage/' + currentStatus.content_url)) + ')' }"></div>

<img v-if="currentStatus.type === 'image' && (currentStatus.content_url || currentStatus.contentUrl)" class="status-viewer-img" :src="(currentStatus.contentUrl || ('/storage/' + currentStatus.content_url))" alt="" @load="statusViewerReady = true; startStatusProgress()">

<video v-else-if="currentStatus.type === 'video' && (currentStatus.content_url || currentStatus.contentUrl)" ref="statusViewerVideo" class="status-viewer-img" autoplay playsinline :muted="statusViewerMuted" :src="(currentStatus.contentUrl || ('/storage/' + currentStatus.content_url))" @loadedmetadata="onStatusVideoReady" @canplay="onStatusVideoReady"></video>

  <div v-for="(layer, lIdx) in getStatusTextLayers(currentStatus)" :key="'txt-'+lIdx"
       class="status-viewer-text-layer"
       style="position:absolute; white-space:pre-wrap; text-align:center; z-index:10; font-weight:600; max-width:90%; word-break:break-word;"
       :style="{
         fontFamily: layer.fontStyle || 'Tajawal',
         fontSize: (layer.fontSize || 42)+'px',
         color: (layer.textBgStyle==='neon') ? '#fff' : (layer.textColor || '#ffffff'),
         top: (layer.textPosY ?? 50)+'%',
         left: (layer.textPosX ?? 50)+'%',
         transform: 'translate(-50%,-50%) rotate('+(layer.rotate||0)+'deg) scale('+(layer.scale||1)+')',
         textShadow: (layer.textBgStyle||'none')==='none' ? '0 2px 8px rgba(0,0,0,.7)' : ((layer.textBgStyle==='neon') ? '0 0 8px ' + (layer.textColor || '#ffffff') + ', 0 0 16px ' + (layer.textColor || '#ffffff') : 'none'),
         background: (layer.textBgStyle==='solid') ? 'rgba(0,0,0,0.75)' : ((layer.textBgStyle==='translucent') ? 'rgba(0,0,0,0.38)' : 'transparent'),
         padding: (layer.textBgStyle==='solid' || layer.textBgStyle==='translucent') ? '10px 20px' : '0',
         borderRadius: (layer.textBgStyle==='solid' || layer.textBgStyle==='translucent') ? '12px' : '0'
     }">@{{ layer.content }}</div>

</div>

<button class="status-nav-btn prev" @click="prevStatus" v-if="statusViewerIndex > 0" v-show="!statusLongPressActive"><i class="ri-arrow-right-s-line"></i></button>

<button class="status-nav-btn next" @click="nextStatus" v-show="!statusLongPressActive"><i class="ri-arrow-left-s-line"></i></button>

<button class="status-nav-btn pause" @click="toggleStatusPlayback" v-show="!statusLongPressActive"><i :class="statusPaused ? 'ri-play-line' : 'ri-pause-line'"></i></button>

<div class="status-viewer-footer" v-show="!statusLongPressActive">
<!-- Reply section: only for OTHER users' statuses -->
<template v-if="statusViewerContact && Number(statusViewerContact.user_id) !== Number(currentUserId)">
<!-- 8 reaction emojis: shown only when input is focused -->
<div class="svf-emoji-bar" v-show="showQuickEmojiBar">
<button class="sro-react-btn" v-for="e in statusEmojiList" :key="e" @mousedown.prevent @click="sendQuickStatusReaction(e)">@{{ e }}</button>
</div>
<div class="svf-reply-wrap">
<input class="status-reply-input" v-model="statusReplyText" placeholder="اكتب ردًا على الحالة..." @focus="onReplyInputFocus" @blur="onReplyInputBlur" @keydown.enter.prevent.stop="replyToStatus">
<button class="svf-like-btn" @mousedown.prevent @click="toggleStatusLike" :class="{ liked: statusLiked, 'like-animating': statusLikeAnimating }" title="إعجاب"><i class="ri-heart-3-fill" v-if="statusLiked"></i><i class="ri-heart-3-line" v-else></i></button>
<button class="svf-emoji-btn" @mousedown.prevent @click="toggleFullEmoji" title="مكتبة الإيموجي"><i class="ri-emotion-happy-line"></i></button>
<button class="svf-send-btn" @mousedown.prevent @click="replyToStatus"><i class="ri-send-plane-fill"></i></button>
<button class="svf-icon-btn" @click="statusViewerMuted = !statusViewerMuted" :title="statusViewerMuted ? 'تشغيل الصوت' : 'كتم الصوت'">
<i :class="statusViewerMuted ? 'ri-volume-mute-line' : 'ri-volume-up-line'"></i>
</button>
</div>
</template>
<!-- My status controls: only for OWN statuses -->
<template v-else-if="statusViewerContact && Number(statusViewerContact.user_id) === Number(currentUserId)">
<div class="svf-my-controls">
<button class="svf-icon-btn" @click="statusViewerMuted = !statusViewerMuted">
<i :class="statusViewerMuted ? 'ri-volume-mute-line' : 'ri-volume-up-line'"></i>
</button>
<button class="svf-icon-btn" @click="openStatusViewers" title="المشاهدون"><i class="ri-eye-line"></i></button>
<button class="svf-icon-btn" @click="openStatusEditor" title="نشر حالة جديدة"><i class="ri-add-circle-line"></i></button>
<button class="svf-icon-btn" @click="editCurrentStatus" title="تعديل"><i class="ri-edit-2-line"></i></button>
<button class="svf-icon-btn danger" @click="deleteCurrentStatus" title="حذف"><i class="ri-delete-bin-6-line"></i></button>
</div>
</template>
</div>
<!-- Full emoji library picker (shown when emoji button is clicked) -->
<div class="status-emoji-picker" v-if="showStatusFullEmoji">
<div class="sep-head">
<span>اختر رد فعل</span>
<button class="sep-close" @click="closeStatusFullEmoji"><i class="ri-close-line"></i></button>
</div>
<div class="sep-grid">
<button v-for="e in emojiList" :key="e" @mousedown.prevent @click="sendQuickStatusReaction(e); closeStatusFullEmoji()" v-html="e"></button>
</div>
</div>
<!-- Dim overlay when reply input is focused or full emoji picker is open -->
<div class="status-reply-dim" v-if="statusReplyFocused || showStatusFullEmoji" @click="closeReplyPanel"></div>

</div>

<div class="folders-modal" v-if="statusViewersOpen" @click.self="closeStatusViewers">

<div class="folders-card" style="width:min(420px,100%);max-height:78vh;display:flex;flex-direction:column;">

<div class="folders-head">

<strong>مشاهدو الحالة</strong>

<button class="h-icon-btn" @click="closeStatusViewers"><i class="ri-close-line"></i></button>

</div>

<div style="padding:10px 14px;color:var(--muted);font-size:12px;">من شاهد حالتك</div>

<div style="overflow:auto;padding:0 12px 12px;">

<div v-if="!statusViewersList.length" style="text-align:center;color:var(--muted);padding:24px;">لا يوجد مشاهدون حتى الآن</div>

<div v-for="v in statusViewersList" :key="v.id" style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid var(--theme-border);border-radius:10px;margin-bottom:8px;background:var(--panel-2);cursor:pointer;" @click="openViewerProfile(v)">

<div class="avatar" :class="{ 'contact-has-status': hasAnyStatus(v.id) }" style="width:44px;height:44px;" @click="hasAnyStatus(v.id) && ($event.stopPropagation(), openViewerStatus(v))">

<img v-if="v.avatarUrl" :src="normalizeAvatarUrl(v.avatarUrl)" alt="" v-on:error="handleAvatarError($event, v)">

<span v-else>@{{ getAuthorInitial(v.name) }}</span>

</div>

<div style="display:grid;gap:2px;flex:1;">

<strong style="font-size:13px;">@{{ v.name }}</strong>

<span style="font-size:11px;color:var(--muted);">@{{ v.viewedAtText || v.viewedAt }}</span>

</div>

<i v-if="v.liked" class="ri-heart-3-fill" style="color:#e74c3c;font-size:18px;" title="أعجب بهذه الحالة"></i>

</div>

</div>

</div>

</div>

<!-- == STATUS CREATOR (Rebuild) == -->
<div class="sc-overlay" v-if="statusEditorOpen" @click.self="closeStatusEditor">
  <div class="sc-canvas-wrap" :class="{'is-typing': statusFocusState === 2}" @click.self="closeStatusEditor">
    <div class="sc-canvas" ref="statusPreviewPhone" dir="ltr"
         @touchstart.passive="startFilterSwipe"
         @touchmove.passive="moveFilterSwipe"
         @touchend="endFilterSwipe"
         @mousedown="startFilterSwipeMouse">
      
      <!-- Background & Media -->
      <div class="sc-bg" :style="{background: (statusEditor.bgColor||'').includes('gradient') ? statusEditor.bgColor + ' center/cover no-repeat' : statusEditor.bgColor, filter: currentEditorFilter}"></div>
      <div v-if="statusEditor.type==='image' && statusEditor.mediaPreview" class="sc-bg-blur"
           :style="{backgroundImage: 'url(' + statusEditor.mediaPreview + ')', filter: currentEditorFilter + ' blur(25px)'}"></div>
      <img v-if="statusEditor.type==='image' && statusEditor.mediaPreview"
           class="sc-media draggable-media" :src="statusEditor.mediaPreview" alt=""
           @click="handlePreviewBgClick"
           :style="{ transform: 'translate(-50%,-50%) rotate('+(statusEditor.mediaRotate||0)+'deg) scale('+(statusEditor.mediaScale||1)+')', top: (statusEditor.mediaPosY||50)+'%', left: (statusEditor.mediaPosX||50)+'%' }"
           @pointerdown.prevent.stop="startStatusMediaDrag($event)">
      <video v-else-if="statusEditor.type==='video' && statusEditor.mediaPreview"
             class="sc-media draggable-media" autoplay muted loop playsinline
             :src="statusEditor.mediaPreview"
             @click="handlePreviewBgClick"
             :style="{ transform: 'translate(-50%,-50%) rotate('+(statusEditor.mediaRotate||0)+'deg) scale('+(statusEditor.mediaScale||1)+')', top: (statusEditor.mediaPosY||50)+'%', left: (statusEditor.mediaPosX||50)+'%' }"
             @pointerdown.prevent.stop="startStatusMediaDrag($event)"></video>
      <div v-else class="sc-media-placeholder" @click="handlePreviewBgClick">
        <span v-if="!statusEditor.texts.length">انقر لكتابة نص، أو اسحب لتغيير الفلاتر</span>
      </div>

      <!-- Alignment guides -->
      <div class="se2-mag-guide se2-mag-guide-cx" :class="{show: statusAlignGuides.cx}"></div>
      <div class="se2-mag-guide se2-mag-guide-cy" :class="{show: statusAlignGuides.cy}"></div>

      <!-- Text Layers -->
      <template v-for="(t, ti) in statusEditor.texts" :key="t._key || ti">
        <div class="sc-text-layer"
             v-show="statusFocusState !== 2 || ti !== statusEditor.activeTextIndex"
             :class="{'dragging': statusIsDragging && ti === statusEditor.activeTextIndex}"
             :style="{
               fontFamily: t.fontStyle || 'Tajawal',
               fontSize: (t.fontSize || 42)+'px',
               color: (t.textBgStyle==='neon') ? '#fff' : (t.textColor || '#ffffff'),
               fontWeight: 600,
               top: (t.textPosY || 50)+'%',
               left: (t.textPosX || 50)+'%',
               transform: 'translate(-50%,-50%) rotate('+(t.rotate||0)+'deg) scale('+(t.scale||1)+')',
               textShadow: (t.textBgStyle||'none')==='none' ? '0 2px 8px rgba(0,0,0,.7)' : ((t.textBgStyle==='neon') ? '0 0 8px ' + (t.textColor || '#ffffff') + ', 0 0 16px ' + (t.textColor || '#ffffff') : 'none'),
               background: (t.textBgStyle==='solid') ? 'rgba(0,0,0,0.75)' : ((t.textBgStyle==='translucent') ? 'rgba(0,0,0,0.38)' : 'transparent'),
               padding: (t.textBgStyle==='solid' || t.textBgStyle==='translucent') ? '10px 20px' : '0',
               borderRadius: (t.textBgStyle==='solid' || t.textBgStyle==='translucent') ? '12px' : '0',
               zIndex: ti === statusEditor.activeTextIndex ? 10 : 2
             }"
             @pointerdown.prevent.stop="startStatusTextDrag($event, ti)">
          <template v-if="!t.isSticker">
            <span style="white-space: pre-wrap;">@{{ t.content || '' }}</span>
          </template>
          <template v-else>
            <img v-if="!t.isVideo" :src="t.url" style="width: 120px; height: auto; pointer-events: none;" />
            <video v-else :src="t.url" autoplay loop muted playsinline style="width: 120px; height: auto; pointer-events: none;" />
          </template>
        </div>
      </template>

      <!-- Filter swipe hint -->
      <transition name="se2-hint">
        <div v-if="filterSwipeHint" class="sc-filter-hint">@{{ filterSwipeHint }}</div>
      </transition>

      <!-- Trash Bin -->
      <div class="sc-trash-bin" :class="{show: statusIsDragging, hover: statusTrashHover}">
          <i class="ri-delete-bin-line"></i>
      </div>
    </div> <!-- /sc-canvas -->

    
    <!-- Sticker / Emoji Drawer -->
    <transition name="se2-drawer">
        <div class="sc-drawer-overlay" v-if="statusStickerDrawerOpen" @click.self="statusStickerDrawerOpen = false">
        <div class="sc-drawer" @touchstart="drawerTouchStart" @touchmove="drawerTouchMove" @touchend="drawerTouchEnd">
            <div class="sc-drawer-header">
                <div class="sc-drawer-handle"></div>
                <span>الرسومات والملصقات</span>
            </div>
            <div class="sc-drawer-body">
                <div v-if="stickersLoading" style="text-align:center; padding:20px; color:#fff;">جاري التحميل...</div>
                <div v-else-if="!stickerList.length && !stickerFavorites.length" style="text-align:center; padding:20px; color:#999;">لا توجد ملصقات محفوظة</div>
                <div v-else class="sc-sticker-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 15px;">
                    <template v-for="sticker in [...stickerFavorites, ...stickerList.filter(s => !stickerFavorites.find(f => f.id === s.id))]">
                        <img v-if="sticker.type !== 'animated'" :src="sticker.url" :key="'img'+sticker.id" @click="addCustomStickerToStatus(sticker)" style="width: 100%; aspect-ratio: 1; object-fit: contain; cursor: pointer; border-radius: 8px;">
                        <video v-else :src="sticker.url" :key="'vid'+sticker.id" @click="addCustomStickerToStatus(sticker)" autoplay loop muted playsinline style="width: 100%; aspect-ratio: 1; object-fit: contain; cursor: pointer; border-radius: 8px;"></video>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </transition>
    <!-- Floating UI (Main) -->
    <div class="sc-ui-layer" v-if="statusFocusState < 2" @click.self="closeStatusEditor">
      <!-- Top Bar -->
      <div class="sc-top-bar">
        <button class="sc-btn sc-close-btn" @click="closeStatusEditor"><i class="ri-close-line"></i></button>
        <div class="sc-top-tools">
          <button class="sc-btn" @click.stop="openStatusStickerDrawer"><i class="ri-emoji-sticker-line"></i></button>
          <button class="sc-btn" @click.stop="autoPickStatusBg"><i class="ri-palette-line"></i></button>
          <button class="sc-btn" @click.stop="triggerStatusMedia"><i class="ri-image-add-line"></i></button>
          <button class="sc-btn" @click.stop="statusDrawerOpen = true"><i class="ri-emotion-line"></i></button>
          <button class="sc-btn sc-text-btn" @click.stop="handlePreviewBgClick">Aa</button>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="sc-bottom-bar">
        <div class="sc-settings-group">
          <button class="sc-pill-btn" @click="statusEditor.durationHours = statusEditor.durationHours === 24 ? 72 : (statusEditor.durationHours === 72 ? 168 : 24)">
            ⏱ @{{ statusEditor.durationHours === 24 ? 'يوم' : (statusEditor.durationHours === 72 ? '3 أيام' : 'أسبوع') }}
          </button>
        </div>
        <button class="sc-send-btn" @click="publishStatus" :disabled="statusPublishing">
          <i v-if="statusPublishing" class="ri-loader-4-line" style="animation:spin 1s linear infinite;"></i>
          <i v-else class="ri-send-plane-fill"></i>
        </button>
      </div>
    </div>

    
    <!-- Creative Drawer (Bottom Sheet) -->
    <transition name="se2-drawer">
      <div class="sc-drawer-overlay" v-if="statusDrawerOpen" @click.self="statusDrawerOpen = false">
        <div class="sc-drawer" @touchstart="drawerTouchStart" @touchmove="drawerTouchMove" @touchend="drawerTouchEnd">
          <div class="sc-drawer-header" @click="statusDrawerOpen = false">
            <div class="sc-drawer-handle"></div>
          </div>
          <div class="sc-drawer-body">
            <div class="sc-emoji-grid">
              <button v-for="e in ['😀','😂','🥰','😎','🥺','🤔','😳','😭','😤','🤬','😈','👻','💀','👽','🤖','💩','🤡','😺','😻','🙈','🙉','🙊','🐒','🐶','🐱','🐭','🐹','🐰','🦊','🐻']"
                      :key="e" class="sc-emoji-btn" @click="addEmojiToStatus(e)">
                @{{ e }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Typing Overlay 2.0 -->
    <transition name="sc-fade">
    <div class="sc-typing-overlay" v-if="statusFocusState === 2 && seActiveText()" @click.self="handleTypingOverlayBgClick">
      <div class="sc-typing-header">
        <div class="sc-typing-tools">
          <button class="sc-btn" @click="cycleTextBgStyle">
            <i :class="seActiveText()?.textBgStyle==='none' ? 'ri-font-size' : (seActiveText()?.textBgStyle==='translucent' ? 'ri-contrast-fill' : 'ri-contrast-2-fill')"></i>
          </button>
          <button class="sc-btn" @click="cycleTextFontStyle">
            <span style="font-family: serif; font-size: 18px; font-weight: bold;">A</span>
          </button>
        </div>
        <button class="sc-typing-done" @click="handleTypingOverlayBgClick">تم</button>
      </div>

      <div class="sc-typing-body" @click.self="handleTypingOverlayBgClick">
          <!-- Vertical Font Size Slider -->
          <div class="sc-font-slider-wrap">
            <input type="range" class="sc-font-slider" min="16" max="100" step="1" dir="ltr"
                   v-model.number="statusEditor.fontSize" @input="syncActiveTextProp('fontSize', statusEditor.fontSize)">
          </div>
          <textarea v-model="statusEditor.texts[statusEditor.activeTextIndex].content"
                    class="sc-typing-textarea" dir="auto" ref="typingOverlayTextarea"
                    placeholder="اكتب شيئاً..."
                    :style="{
                        fontSize: (statusEditor.fontSize || seActiveText()?.fontSize || 42) + 'px',
                        fontFamily: seActiveText()?.fontStyle || 'Tajawal',
                        color: (seActiveText()?.textBgStyle==='neon') ? '#fff' : (seActiveText()?.textColor || '#ffffff'),
                        textShadow: (seActiveText()?.textBgStyle==='none') ? '0 2px 8px rgba(0,0,0,.7)' : ((seActiveText()?.textBgStyle==='neon') ? '0 0 10px ' + (seActiveText()?.textColor || '#ffffff') + ', 0 0 20px ' + (seActiveText()?.textColor || '#ffffff') : 'none'),
                        background: (seActiveText()?.textBgStyle==='solid') ? 'rgba(0,0,0,0.75)' : ((seActiveText()?.textBgStyle==='translucent') ? 'rgba(0,0,0,0.38)' : 'transparent'),
                        padding: (seActiveText()?.textBgStyle==='solid' || seActiveText()?.textBgStyle==='translucent') ? '10px 20px' : '0',
                        borderRadius: (seActiveText()?.textBgStyle==='solid' || seActiveText()?.textBgStyle==='translucent') ? '12px' : '0'
                    }"></textarea>
      </div>

      <div class="sc-typing-colors">
        <button v-for="c in ['#FFFFFF','#000000','#FF3B30','#FF9500','#FFCC00','#34C759','#5AC8FA','#007AFF','#5856D6','#FF2D55','#A2845E','#8E8E93']"
                :key="c" class="sc-color-dot" :class="{active: seActiveText()?.textColor===c}"
                :style="{background:c}" @click="statusEditor.texts[statusEditor.activeTextIndex].textColor = c; statusEditor.textColor = c;"></button>
      </div>
    </div>
    </transition>
  </div>

  <input ref="statusMediaInput" type="file" style="display:none" accept="image/*,video/*" @change="onStatusMediaSelect">
  <input ref="statusAudioInput" type="file" style="display:none" accept="audio/*" @change="onStatusAudioSelect">
</div>

<!-- == VIDEO EDITOR (PREMIUM) == -->
<div class="ve2-overlay" v-if="videoEditorOpen" @click.self="closeVideoEditor">
<div class="ve2-card">
<!-- Header -->
<div class="ve2-header">
<div style="display:flex;align-items:center;gap:10px;">
<div class="ve2-header-icon"><i class="ri-movie-2-line"></i></div>
<div>
<div style="font-weight:700;font-size:15px;color:var(--text)">محرر الفيديو</div>
<div style="font-size:11px;color:var(--muted)">@{{ videoEditorFile ? videoEditorFile.name : '' }}</div>
</div>
</div>
<button class="h-icon-btn" @click="closeVideoEditor"><i class="ri-close-line"></i></button>
</div>

<!-- Video Preview -->
<div class="ve2-video-wrap">
<video ref="vePreview" :src="videoEditorUrl" preload="metadata"
       @loadedmetadata="onVeMetadata" @timeupdate="onVeTimeUpdate"
       style="max-width:100%;max-height:260px;border-radius:12px;background:#000;"></video>
<!-- Playback controls overlay -->
<div class="ve2-playback-bar">
<button class="ve2-play-btn" @click="toggleVePlayback">
<i :class="veIsPlaying ? 'ri-pause-fill' : 'ri-play-fill'"></i>
</button>
<span class="ve2-time">@{{ formatDuration(veCurrentTime || 0) }} / @{{ formatDuration(videoEditorDuration || 0) }}</span>
<button class="ve2-play-btn" @click="veFullscreen" v-if="false">
<i class="ri-fullscreen-line"></i>
</button>
</div>
</div>

<!-- Timeline Trimmer -->
<div class="ve2-section">
<div class="ve2-section-label">✂️ قص الفيديو</div>
<div class="ve2-timeline-wrap">
<!-- Timeline bar -->
<div class="ve2-timeline" ref="veTimeline"
     @pointerdown="veTimelineClick($event)">
<!-- Active zone -->
<div class="ve2-active-zone"
     :style="{left: ((videoEditorStart/Math.max(videoEditorDuration,.01))*100)+'%',
              width: (((videoEditorEnd-videoEditorStart)/Math.max(videoEditorDuration,.01))*100)+'%'}">
</div>
<!-- Left handle (start) -->
<div class="ve2-handle start"
     :style="{left: ((videoEditorStart/Math.max(videoEditorDuration,.01))*100)+'%'}"
     @pointerdown.stop="startVeHandleDrag('start', $event)">
<div class="ve2-handle-inner"></div>
</div>
<!-- Right handle (end) -->
<div class="ve2-handle end"
     :style="{left: ((videoEditorEnd/Math.max(videoEditorDuration,.01))*100)+'%'}"
     @pointerdown.stop="startVeHandleDrag('end', $event)">
<div class="ve2-handle-inner"></div>
</div>
<!-- Playhead -->
<div class="ve2-playhead"
     :style="{left: ((veCurrentTime/Math.max(videoEditorDuration,.01))*100)+'%'}">
</div>
</div>
<!-- Time labels -->
<div class="ve2-time-row">
<span>@{{ formatDuration(videoEditorStart) }}</span>
<span style="color:var(--gold);font-weight:600;">@{{ formatDuration(videoEditorEnd - videoEditorStart) }} مقطوعة</span>
<span>@{{ formatDuration(videoEditorEnd) }}</span>
</div>
</div>
</div>

<!-- Quality Selection -->
<div class="ve2-section">
<div class="ve2-section-label">📹 جودة الإخراج</div>
<div class="ve2-quality-row">
<div v-for="q in [
   {val:'360p', label:'360p', desc:'جودة منخفضة', size:'~3MB/دق'},
   {val:'480p', label:'480p', desc:'جودة متوسطة', size:'~6MB/دق'},
   {val:'720p', label:'720p', desc:'جودة عالية', size:'~15MB/دق'},
   {val:'1080p',label:'1080p',desc:'جودة ممتازة', size:'~30MB/دق'}
]" :key="q.val"
class="ve2-quality-card" :class="{active: videoEditorQuality===q.val}"
@click="videoEditorQuality=q.val">
<div class="ve2-qc-res">@{{ q.label }}</div>
<div class="ve2-qc-desc">@{{ q.desc }}</div>
<div class="ve2-qc-size">@{{ q.size }}</div>
</div>
</div>
<div class="ve2-size-estimate">
الحجم التقريبي: <strong style="color:var(--gold)">@{{ estimatedVideoSize }}</strong>
</div>
</div>

<!-- Progress bar (when processing) -->
<div class="ve2-progress-section" v-if="videoEditorProcessing">
<div class="ve2-section-label">⏳ جاري المعالجة والإرسال...</div>
<div class="ve2-progress-track">
<div class="ve2-progress-fill" :style="{width: videoEditorProgress+'%'}"></div>
</div>
<div style="text-align:center;font-size:13px;color:var(--muted);margin-top:6px;">@{{ Math.round(videoEditorProgress) }}%</div>
</div>

<!-- Actions -->
<div class="ve2-actions">
<button class="ve2-btn cancel" @click="closeVideoEditor" :disabled="videoEditorProcessing">
<i class="ri-close-line"></i> إلغاء
</button>
<button class="ve2-btn primary" @click="processAndSendVideo" :disabled="videoEditorProcessing">
<i v-if="videoEditorProcessing" class="ri-loader-4-line" style="animation:spin 1s linear infinite;"></i>
<i v-else class="ri-send-plane-fill"></i>
@{{ videoEditorProcessing ? 'جاري الإرسال...' : 'معالجة وإرسال' }}
</button>
</div>
</div>
</div>

<!-- == VIDEO PROCESSING PREVIEW == -->
<div class="ve2-overlay" v-if="videoEditorPreviewOpen" @click.self="closeVideoPreview">
<div class="ve2-card" style="max-width:700px;">
<div class="ve2-header">
<div class="ve2-header-icon"><i class="ri-eye-line"></i></div>
<span style="font-weight:700;">معاينة الفيديو المعالج</span>
<button class="h-icon-btn" @click="closeVideoPreview"><i class="ri-close-line"></i></button>
</div>
<div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
<video :src="videoEditorPreviewUrl" controls style="width:100%;max-height:400px;border-radius:12px;background:#000;"></video>
<div style="display:flex;gap:8px;font-size:13px;color:var(--muted);">
<span>الجودة: @{{ videoEditorQuality }}</span>
<span>الحجم: @{{ videoEditorPreviewFile ? (videoEditorPreviewFile.size / 1024).toFixed(1) + ' KB' : '...' }}</span>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
<button class="ve2-btn cancel" @click="closeVideoPreview">إلغاء</button>
<button class="ve2-btn cancel" @click="changeVideoQuality">تغيير الجودة</button>
<button class="ve2-btn primary" @click="acceptProcessedVideo"><i class="ri-check-line"></i> قبول وإرسال</button>
</div>
</div>
</div>
</div>

<!-- == IMAGE EDITOR (PREMIUM) == -->
<div class="ve2-overlay" v-if="imageEditorOpen" @click.self="closeImageEditor">
<div class="ve2-card">
<!-- Header -->
<div class="ve2-header">
<div style="display:flex;align-items:center;gap:10px;">
<div class="ve2-header-icon" style="background:linear-gradient(135deg,var(--gold),var(--gold-2));">
<i class="ri-image-edit-line"></i>
</div>
<div>
<div style="font-weight:700;font-size:15px;color:var(--text)">محرر الصورة</div>
<div style="font-size:11px;color:var(--muted)">@{{ imageEditorFile ? imageEditorFile.name : '' }}</div>
</div>
</div>
<button class="h-icon-btn" @click="closeImageEditor"><i class="ri-close-line"></i></button>
</div>

<!-- Image Preview -->
<div class="ie2-preview-wrap">
<img v-if="imageEditorUrl" :src="imageEditorUrl" ref="imagePreviewEl"
     :style="{
       filter: `brightness(${imageEditorBrightness}%) contrast(${imageEditorContrast}%) saturate(${imageEditorSaturate}%) ${imageEditorFilter}`,
       transform: `rotate(${imageEditorRotate}deg) scaleX(${imageEditorFlipH?-1:1}) scaleY(${imageEditorFlipV?-1:1})`,
       maxWidth: '100%', maxHeight: '280px', objectFit: 'contain',
       transition: 'filter .15s, transform .15s',
       borderRadius: '12px',
     }"
     alt="">
</div>

<!-- Tools Tabs -->
<div class="ie2-tabs">
<button v-for="tab in [{id:'adjust',label:'ضبط',icon:'ri-contrast-2-line'},{id:'filters',label:'فلاتر',icon:'ri-magic-line'},{id:'rotate',label:'تدوير',icon:'ri-rotate-lock-line'}]"
        :key="tab.id" class="ie2-tab" :class="{active: imageEditorTab===tab.id}"
        @click="imageEditorTab=tab.id">
<i :class="tab.icon"></i> @{{ tab.label }}
</button>
</div>

<!-- Adjust Tab -->
<div class="ie2-tab-content" v-if="imageEditorTab==='adjust' || !imageEditorTab">
<div class="ie2-slider-row">
<div class="ie2-slider-label"><i class="ri-sun-line"></i> السطوع</div>
<input type="range" min="50" max="200" step="2" v-model.number="imageEditorBrightness">
<span class="ie2-slider-val">@{{ imageEditorBrightness }}%</span>
</div>
<div class="ie2-slider-row">
<div class="ie2-slider-label"><i class="ri-contrast-2-line"></i> التباين</div>
<input type="range" min="50" max="200" step="2" v-model.number="imageEditorContrast">
<span class="ie2-slider-val">@{{ imageEditorContrast }}%</span>
</div>
<div class="ie2-slider-row">
<div class="ie2-slider-label"><i class="ri-palette-line"></i> الإشباع</div>
<input type="range" min="0" max="300" step="5" v-model.number="imageEditorSaturate">
<span class="ie2-slider-val">@{{ imageEditorSaturate }}%</span>
</div>
<div class="ie2-slider-row" v-if="false">
<div class="ie2-slider-label"><i class="ri-blur-off-line"></i> الضبابية</div>
<input type="range" min="0" max="10" step="0.5" v-model.number="imageEditorBlur">
<span class="ie2-slider-val">@{{ imageEditorBlur }}px</span>
</div>
</div>

<!-- Filters Tab -->
<div class="ie2-tab-content" v-else-if="imageEditorTab==='filters'">
<div class="ie2-filters-grid">
<div v-for="f in [
  {id:'none',label:'بلا فلتر',css:''},
  {id:'grayscale',label:'أبيض وأسود',css:'grayscale(100%)'},
  {id:'sepia',label:'سيبيا',css:'sepia(80%)'},
  {id:'warm',label:'دافئ',css:'sepia(30%) saturate(150%)'},
  {id:'cold',label:'بارد',css:'hue-rotate(200deg) saturate(130%)'},
  {id:'vivid',label:'نابض',css:'saturate(200%) contrast(110%)'},
  {id:'fade',label:'باهت',css:'brightness(120%) saturate(60%)'},
  {id:'sharp',label:'حاد',css:'contrast(130%) saturate(120%)'},
  {id:'matte',label:'ميت',css:'brightness(110%) saturate(70%) contrast(90%)'},
]" :key="f.id"
class="ie2-filter-card" :class="{active: imageEditorFilter===f.css}"
@click="imageEditorFilter=f.css">
<div class="ie2-filter-preview" :style="{filter: f.css + ` brightness(${imageEditorBrightness}%) contrast(${imageEditorContrast}%)`}">
<img v-if="imageEditorUrl" :src="imageEditorUrl" alt="" style="width:100%;height:100%;object-fit:cover;">
</div>
<div class="ie2-filter-label">@{{ f.label }}</div>
</div>
</div>
</div>

<!-- Rotate Tab -->
<div class="ie2-tab-content" v-else-if="imageEditorTab==='rotate'">
<div class="ie2-rotate-grid">
<button class="ie2-rot-btn" @click="imageEditorRotate=(imageEditorRotate-90+360)%360">
<i class="ri-anticlockwise-2-line"></i><span>90° لليسار</span>
</button>
<button class="ie2-rot-btn" @click="imageEditorRotate=(imageEditorRotate+90)%360">
<i class="ri-clockwise-2-line"></i><span>90° لليمين</span>
</button>
<button class="ie2-rot-btn" :class="{active:imageEditorFlipH}" @click="imageEditorFlipH=!imageEditorFlipH">
<i class="ri-flip-horizontal-line"></i><span>عكس أفقي</span>
</button>
<button class="ie2-rot-btn" :class="{active:imageEditorFlipV}" @click="imageEditorFlipV=!imageEditorFlipV">
<i class="ri-flip-vertical-line"></i><span>عكس رأسي</span>
</button>
</div>
<button class="ie2-reset-btn" @click="resetImageEditor">
<i class="ri-refresh-line"></i> إعادة ضبط
</button>
</div>

<!-- Actions -->
<div class="ve2-actions">
<button class="ve2-btn cancel" @click="closeImageEditor">إلغاء</button>
<button class="ve2-btn secondary" @click="resetImageEditor">
<i class="ri-refresh-line"></i> إعادة
</button>
<button class="ve2-btn primary" @click="sendEditedImage">
<i class="ri-send-plane-fill"></i> إرسال
</button>
</div>
</div>
</div>

<!-- == DELETE STATUS MODAL == -->
<div class="confirm-modal" v-if="deleteStatusTarget" @click.self="deleteStatusTarget=null; statusPaused && toggleStatusPlayback()">
<div class="confirm-card">
<div class="confirm-title" style="color:var(--danger,#e74c3c);"><i class="ri-delete-bin-6-line"></i> حذف الحالة</div>
<div class="confirm-text">هل تريد حذف هذه الحالة؟ لا يمكن التراجع عن هذا الإجراء.</div>
<div class="confirm-actions">
<button @click="deleteStatusTarget=null; statusPaused && toggleStatusPlayback()">إلغاء</button>
<button class="danger" @click="confirmDeleteStatus">حذف</button>
</div>
</div>
</div>
<div class="toast-wrap" v-if="toastMessage">

<div class="toast-msg" :class="toastType">@{{ toastMessage }}</div>

</div>

<!-- Search panel -->

<div class="search-panel" v-if="searchPanelOpen" @click.self="closeSearchPanel">

<div class="search-panel-head">

<button class="h-icon-btn" @click="closeSearchPanel"><i class="ri-close-line"></i></button>

<h3>بحث في المحادثة</h3>

</div>

<div style="padding:12px 16px;">

<input id="search-panel-input" class="search-panel-input" v-model="searchPanelQuery"

@input="onSearchInput" placeholder="ابحث عن رسالة...">

</div>

<div class="search-panel-results">

<div v-if="searchPanelLoading" style="text-align:center;padding:20px;color:var(--muted);"><i class="ri-loader-4-line"></i> جاري البحث...</div>

<div v-else-if="searchPanelResults.length === 0 && searchPanelQuery.length >= 2" style="text-align:center;padding:20px;color:var(--muted);">لا توجد نتائج</div>

<div class="search-result-item" v-for="msg in searchPanelResults" :key="msg.id" @click="jumpToSearchResult(msg)">

<div class="search-result-name">@{{ msg.sender?.name || msg.sender_name || msg.senderName || '' }}</div>

<div class="search-result-content">@{{ msg.content || msg.text || t.attachment }}</div>

<div class="search-result-time">@{{ formatMessageTime(msg.created_at || msg.createdAt) }}</div>

</div>

</div>

</div>

@include('partials.messaging-settings-panel')


<div class="folders-modal" v-if="foldersManagerOpen" @click.self="closeFoldersManager">

<div class="folders-card">

<div class="folders-head">

<strong>المجلدات</strong>

<button class="h-icon-btn" @click="closeFoldersManager"><i class="ri-close-line"></i></button>

</div>

<div class="folders-list">

<div class="folders-row" v-for="(f, i) in foldersConfig" :key="f.id">

<div style="display:flex;align-items:center;gap:10px;">

<i :class="f.icon || 'ri-folder-3-line'" :style="{color: f.color || 'var(--gold)', fontSize: '20px'}"></i>

<div>

<div style="font-weight:700;">@{{ f.name }}</div>

<div style="font-size:12px;color:var(--muted);">@{{ countFolderChats(f) }} محادثة</div>

</div>

</div>

<div style="display:flex;gap:6px;">

<button class="h-icon-btn" @click="editFolder(i)"><i class="ri-edit-line"></i></button>

<button class="h-icon-btn" @click="removeFolder(i)"><i class="ri-delete-bin-6-line"></i></button>

</div>

</div>

</div>

<div class="folders-editor">

<input type="text" v-model.trim="folderDraft.name" placeholder="اسم المجلد" class="folder-name-input">

<div class="qr-section-label" style="padding:4px 0 0;">الأيقونة</div>
<div class="picker-grid">

<button v-for="ic in folderIconChoices" :key="ic" :class="{active: folderDraft.icon===ic}" @click="folderDraft.icon=ic"><i :class="ic"></i></button>

</div>

<div class="qr-section-label" style="padding:4px 0 0;">لون المجلد</div>
<div class="folder-color-row">
<button v-for="c in folderColorChoices" :key="c" class="folder-color-swatch" :class="{active: folderDraft.color===c}" :style="{background:c}" @click="folderDraft.color=c"></button>
</div>

<div class="folder-chat-rows">
<button class="folder-chat-row-btn" @click="openIncludeExclude('include')">
<i class="ri-folder-add-line"></i>
<span>المحادثات المضمنة</span>
<span class="folder-chat-row-count">@{{ folderDraft.includeIds.length }}</span>
<i class="ri-arrow-left-s-line"></i>
</button>
<button class="folder-chat-row-btn" @click="openIncludeExclude('exclude')">
<i class="ri-folder-reduce-line"></i>
<span>المحادثات المستبعدة</span>
<span class="folder-chat-row-count">@{{ folderDraft.excludeIds.length }}</span>
<i class="ri-arrow-left-s-line"></i>
</button>
</div>

<div v-if="includeExcludeMode" class="folder-include-exclude-panel">

<strong>@{{ includeExcludeMode === 'exclude' ? 'المحادثات المستبعدة' : 'المحادثات المضمنة' }}</strong>

<input type="text" v-model.trim="includeExcludeSearch" placeholder="بحث" class="picker-search" style="margin:0;">

<div style="max-height:220px;overflow:auto;display:grid;gap:6px;">

<label v-for="c in includeExcludeCandidates" :key="`fd-${c.id}`" class="folder-chat-pick-row">

<span>@{{ c.name }}</span>

<input type="checkbox" :checked="isFolderChatSelected(c.id)" @change="toggleFolderChat(c.id)">

</label>

</div>

<div style="display:flex;justify-content:flex-end;">

<button class="profile-action-btn" @click="includeExcludeMode=null">تم</button>

</div>

</div>

<div style="display:flex;justify-content:flex-end;gap:8px;">

<button class="profile-action-btn" @click="resetFolderDraft">إلغاء</button>

<button class="profile-action-btn primary" @click="saveFolderDraft">حفظ</button>

</div>

</div>

</div>

</div>

<div class="account-drawer" v-if="accountDrawerOpen" @click.stop>

<div class="acc-head">

<div class="avatar" style="width:62px;height:62px;overflow:hidden;padding:0;flex-shrink:0;">
  <img v-if="normalizeAvatarUrl(currentUserAvatar)" :src="normalizeAvatarUrl(currentUserAvatar)" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;" v-on:error="$event.target.style.display='none'">
  <span v-else>@{{ userInitial }}</span>
</div>

<div style="min-width:0;flex:1;">

<div style="font-size:22px;font-style:italic;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@{{ userName }}</div>

<div style="display:flex;align-items:center;gap:6px;margin-top:3px;cursor:pointer;" @click="openStatusEditor">
  <div style="width:22px;height:22px;border-radius:50%;overflow:hidden;flex-shrink:0;border:1.5px solid var(--gold);">
    <img v-if="normalizeAvatarUrl(currentUserAvatar)" :src="normalizeAvatarUrl(currentUserAvatar)" alt="" style="width:100%;height:100%;object-fit:cover;" v-on:error="$event.target.style.display='none'">
    <span v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:10px;background:var(--panel-2);color:var(--gold);">@{{ userInitial }}</span>
  </div>
  <span style="color:var(--gold);font-size:13px;">✨ Set Emoji Status</span>
</div>

</div>

<button class="h-icon-btn" style="margin-right:auto" @click="accountDrawerOpen=false"><i class="ri-arrow-left-s-line"></i></button>

</div>

<div class="acc-list">

<button @click="openMyProfile"><i class="ri-user-line"></i> ملفي الشخصي</button>

<button @click="openNewConversationModal"><i class="ri-group-line"></i> مجموعة جديدة</button>

<button @click="openNewChannelModal"><i class="ri-megaphone-line"></i> قناة جديدة</button>

<button @click="openContacts"><i class="ri-contacts-line"></i> جهات الاتصال</button>

<button @click="openCalls"><i class="ri-phone-line"></i> المكالمات</button>

<button @click="openSavedChat"><i class="ri-bookmark-line"></i> الرسائل المحفوظة</button>

<button @click="openSettings"><i class="ri-settings-3-line"></i> الإعدادات</button>

<div style="display:flex;align-items:center;gap:12px;padding:10px 16px;">
<i class="ri-moon-line" style="font-size:18px;color:var(--gold);"></i>
<div style="flex:1;display:flex;flex-direction:column;">
<span style="font-size:14px;font-weight:600;color:var(--text);">الوضع الليلي</span>
<span style="font-size:11px;color:var(--muted);">@{{ isDarkMode ? 'مفعل' : 'غير مفعل' }}</span>
</div>
<span @click="toggleDark" :style="{display:'inline-flex',alignItems:'center',cursor:'pointer',flexShrink:0,width:'42px',height:'22px',borderRadius:'11px',background:isDarkMode ? 'var(--gold)' : 'rgba(255,255,255,.15)',transition:'background .25s',padding:'2px',boxSizing:'border-box',verticalAlign:'middle'}">
<span :style="{display:'block',width:'18px',height:'18px',borderRadius:'50%',background:isDarkMode ? '#000' : '#fff',transition:'margin .25s,background .25s',boxShadow:'0 1px 3px rgba(0,0,0,.3)',marginInlineStart:isDarkMode ? '20px' : '0'}"></span>
</span>
</div>

</div>

</div>

<div class="message-context" v-if="messageContextOpen"
     :style="{top: messageContextY+'px', left: messageContextX+'px'}">
<!-- Quick reactions row -->
<div class="ctx-react">
<button v-for="e in ['\u{1F44D}','\u2764\uFE0F','\u{1F602}','\u{1F62E}','\u{1F622}','\u{1F525}']" :key="e" @click="reactFromContext(e)">@{{ e }}</button>
</div>
<!-- Action list -->
<div class="ctx-list">
<button @click="contextReply">
<i class="ri-reply-line"></i> رد على الرسالة
</button>
<button v-if="messageContextMessage && Number(messageContextMessage.senderId) !== Number(currentUserId)" @click="openProfile(selectedContact); closeMessageContext()">
<i class="ri-user-line"></i> الملف الشخصي
</button>
<button @click="contextCopy">
<i class="ri-file-copy-line"></i> نسخ
</button>
<button @click="contextForward">
<i class="ri-share-forward-line"></i> إعادة توجيه
</button>
<button @click="contextPin">
<i :class="messageContextMessage?.isPinned ? 'ri-pushpin-2-fill' : 'ri-pushpin-2-line'"></i> @{{ messageContextMessage?.isPinned ? 'إلغاء التثبيت' : 'تثبيت الرسالة' }}
</button>
<button @click="contextSave">
<i :class="isMessageSaved(messageContextMessage) ? 'ri-bookmark-fill' : 'ri-bookmark-line'"></i> @{{ isMessageSaved(messageContextMessage) ? 'إزالة من المحفوظات' : 'حفظ الرسالة' }}
</button>
<button v-if="messageContextMessage && Number(messageContextMessage.senderId) === Number(currentUserId) && !['audio','video','sticker_static','sticker_animated','gif'].includes(messageContextMessage.messageType)" @click="editMessage(messageContextMessage); closeMessageContext()">
<i class="ri-edit-2-line"></i> تعديل
</button>
<template v-if="messageContextMessage && (messageContextMessage.messageType === 'sticker_static' || messageContextMessage.messageType === 'sticker_animated')">
<button v-if="Number(messageContextMessage.senderId) === Number(currentUserId) || findStickerByUrl(messageContextMessage.attachmentUrl)" @click="toggleStickerFavoriteFromMessage(messageContextMessage); closeMessageContext()">
<i :class="isStickerFavoritedByUrl(messageContextMessage.attachmentUrl) ? 'ri-star-fill' : 'ri-star-line'"></i> @{{ isStickerFavoritedByUrl(messageContextMessage.attachmentUrl) ? 'حذف من المفضلة' : 'حفظ في المفضلة' }}
</button>
<button v-else @click="saveStickerToMyLibrary(messageContextMessage); closeMessageContext()">
<i class="ri-download-2-line"></i> حفظ الملصق في مكتبتي
</button>
</template>
<button class="danger" @click="contextDelete">
<i class="ri-delete-bin-line"></i> حذف
</button>
</div></div>

<div class="folders-modal" v-if="forwardPickerOpen" @click.self="closeForwardPicker">
<div class="folders-card" style="width:min(460px,96%);max-height:82vh;display:flex;flex-direction:column;">
<div class="folders-head">
<strong>إعادة توجيه</strong>
<button class="h-icon-btn" @click="closeForwardPicker"><i class="ri-close-line"></i></button>
</div>
<div style="overflow:auto;padding:12px;display:grid;gap:8px;">
<label v-for="contact in forwardContacts" :key="'fw-'+contact.id" style="display:flex;align-items:center;gap:10px;border:1px solid var(--theme-border);border-radius:8px;background:var(--panel-2);padding:10px;cursor:pointer;">
<input type="checkbox" :value="contact.id" v-model="forwardSelection">
<div class="avatar" style="width:34px;height:34px;">
<i v-if="Number(contact.id) === -1" class="ri-bookmark-fill" style="font-size:18px;color:var(--gold);"></i>
<img v-else-if="contact.avatar_url" :src="normalizeAvatarUrl(contact.avatar_url)" alt="" v-on:error="handleAvatarError($event, contact)">
<span v-else>@{{ getAuthorInitial(contact.name) }}</span>
</div>
<span style="flex:1;font-weight:700;color:var(--text);">@{{ contact.name }}</span>
</label>
</div>
<div style="padding:12px;border-top:1px solid var(--theme-border);display:flex;gap:8px;justify-content:flex-end;">
<button class="profile-action-btn" @click="closeForwardPicker">إلغاء</button>
<button class="profile-action-btn primary" @click="submitForward" :disabled="!forwardSelection.length">إرسال</button>
</div>
</div>
</div>

<div class="folders-modal" v-if="wallpaperPickerOpen" @click.self="wallpaperPickerOpen=false">

<div class="folders-card" style="width:min(520px,96%);position:relative;" @click="wallpaperPickerMenuOpen=false">

<div class="folders-head">

<strong>ثيم المحادثة</strong>

<div style="display:flex;gap:4px;">

<button class="h-icon-btn" @click.stop="wallpaperPickerMenuOpen=!wallpaperPickerMenuOpen" title="خيارات"><i class="ri-more-2-fill"></i></button>

<button class="h-icon-btn" @click="wallpaperPickerOpen=false"><i class="ri-close-line"></i></button>

</div>

</div>

<!-- Three-dot menu -->
<div class="chat-menu" v-if="wallpaperPickerMenuOpen" @click.stop style="position:absolute;top:52px;right:60px;z-index:10;">
<button @click.stop="showCustomMute=!showCustomMute"><i class="ri-settings-3-line"></i> مخصص</button>
<div v-if="showCustomMute" @click.stop style="padding:8px 10px;border-top:1px solid var(--theme-border);">
<div style="display:flex;gap:4px;margin-bottom:6px;">
<select v-model.number="muteCustomDays" style="flex:1;padding:4px 6px;border-radius:6px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:11px;">
<option value="0">0 يوم</option><option v-for="d in 30" :key="d" :value="d">@{{ d }}</option>
</select>
<select v-model.number="muteCustomHours" style="flex:1;padding:4px 6px;border-radius:6px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:11px;">
<option v-for="h in 23" :key="h-1" :value="h-1">@{{ h-1 }}س</option>
</select>
<select v-model.number="muteCustomMinutes" style="flex:1;padding:4px 6px;border-radius:6px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:11px;">
<option v-for="m in [0,5,10,15,20,25,30,35,40,45,50,55]" :key="m" :value="m">@{{ m }}د</option>
</select>
</div>
<button class="profile-action-btn primary" style="padding:4px 10px;font-size:11px;width:100%;" @click.stop="wallpaperPickerMenuOpen=false; muteCustom()">تطبيق</button>
</div>
<button @click.stop="wallpaperPickerMenuOpen=false; tonePickerOpen=true"><i class="ri-music-line"></i> نغمة الإشعارات</button>
</div>

<div style="padding:14px;">

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;">

<div v-for="(th,i) in chatThemeDefs" :key="th.id" @click="selectChatTheme(th.id)"

class="wallpaper-item"

:class="{active: activeChatTheme?.id===th.id}"

:style="{background: th.wp>=0 ? wallpapers[th.wp] : (th.id === '' ? 'radial-gradient(circle at bottom left, var(--theme-gold-soft), transparent 36%)' : 'var(--panel-2)')}">

<!-- Bubble preview -->
<div style="position:absolute;inset:6px;display:flex;flex-direction:column;gap:2px;pointer-events:none;padding:4px;">

<div style="display:flex;justify-content:flex-end;">

<div :style="{background: th.id ? (isDarkMode ? th.vars['--th-accent'] : (th.varsLight['--th-accent'] || th.vars['--th-accent'])) : 'var(--gold)', borderRadius:'6px 2px 6px 6px', padding:'5px 8px', width:'60%', fontSize:'8px', color:'rgba(255,255,255,.9)', opacity:'.85', minHeight:'12px'}"></div>

</div>

<div style="display:flex;justify-content:flex-start;">

<div :style="{background: th.id ? (isDarkMode ? th.vars['--th-bubble-tint'] : (th.varsLight['--th-bubble-tint'] || th.vars['--th-bubble-tint'])) : 'var(--theme-gold-soft)', borderRadius:'2px 6px 6px 6px', padding:'5px 8px', width:'45%', fontSize:'8px', color:'var(--text)', opacity:'.6', minHeight:'12px'}"></div>

</div>

</div>

<!-- Name overlay -->
<div class="wp-label" style="position:absolute;bottom:0;right:0;left:0;padding:6px 8px;border-radius:0 0 12px 12px;background:linear-gradient(transparent,rgba(0,0,0,.7));">@{{ th.name }}</div>

</div>

</div>

</div>

</div>

</div>

<!-- Mute options modal -->
<div class="folders-modal" v-if="muteOptionsOpen" @click.self="muteOptionsOpen=false">
<div class="folders-card" style="width:min(380px,96%);">
<div class="folders-head"><strong>كتم المحادثة</strong><button class="h-icon-btn" @click="muteOptionsOpen=false"><i class="ri-close-line"></i></button></div>
<div style="padding:14px;display:flex;flex-direction:column;gap:6px;">
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===15}" @click="muteForMinutes(15)"><i class="ri-timer-line"></i> 15 دقيقة<i v-if="contactMuteMinutes===15" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===30}" @click="muteForMinutes(30)"><i class="ri-timer-line"></i> 30 دقيقة<i v-if="contactMuteMinutes===30" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===60}" @click="muteForMinutes(60)"><i class="ri-timer-line"></i> 1 ساعة<i v-if="contactMuteMinutes===60" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===120}" @click="muteForMinutes(120)"><i class="ri-timer-line"></i> 2 ساعة<i v-if="contactMuteMinutes===120" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===240}" @click="muteForMinutes(240)"><i class="ri-timer-line"></i> 4 ساعات<i v-if="contactMuteMinutes===240" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes===480}" @click="muteForMinutes(480)"><i class="ri-timer-line"></i> 8 ساعات<i v-if="contactMuteMinutes===480" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<button class="mute-opt-btn" :class="{active:contactMuteMinutes>0 && ![15,30,60,120,240,480].includes(contactMuteMinutes)}" @click="showCustomMute=!showCustomMute"><i class="ri-settings-3-line"></i> مخصص<i v-if="contactMuteMinutes>0 && ![15,30,60,120,240,480].includes(contactMuteMinutes)" class="ri-check-line" style="margin-right:auto;color:var(--gold);"></i></button>
<div v-if="showCustomMute" style="display:flex;gap:6px;padding:8px 0;align-items:center;">
<select v-model.number="muteCustomDays" style="flex:1;padding:6px 8px;border-radius:8px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:13px;">
<option value="0">0 يوم</option><option v-for="d in 30" :key="d" :value="d">@{{ d }} @{{ d===1?'يوم':'أيام' }}</option>
</select>
<select v-model.number="muteCustomHours" style="flex:1;padding:6px 8px;border-radius:8px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:13px;">
<option v-for="h in 23" :key="h-1" :value="h-1">@{{ h-1 }} ساعة</option>
</select>
<select v-model.number="muteCustomMinutes" style="flex:1;padding:6px 8px;border-radius:8px;border:1px solid var(--theme-border);background:var(--panel-2);color:var(--text);font-size:13px;">
<option v-for="m in [0,5,10,15,20,25,30,35,40,45,50,55]" :key="m" :value="m">@{{ m }} دقيقة</option>
</select>
<button class="profile-action-btn primary" style="padding:6px 12px;font-size:12px;" @click="muteCustom">تطبيق</button>
</div>
<div style="border-top:1px solid var(--theme-border);margin-top:4px;padding-top:8px;">
<button class="mute-opt-btn danger" @click="muteForever"><i class="ri-forbid-line"></i> كتم للأبد</button>
</div>
</div>
</div>
</div>

<!-- Tone / notification settings modal -->
<div class="folders-modal" v-if="tonePickerOpen" @click.self="tonePickerOpen=false">
<div class="folders-card" style="width:min(420px,96%);max-height:90vh;display:flex;flex-direction:column;">
<div class="folders-head"><strong>نغمة الإشعارات</strong><button class="h-icon-btn" @click="tonePickerOpen=false"><i class="ri-close-line"></i></button></div>
<div style="padding:14px;flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:12px;">

<!-- Tones list -->
<div style="display:grid;gap:6px;">
<div v-for="t in toneList" :key="t.id"
@click="previewTone(t.id); selectedToneTemp=t.id"
class="tone-item"
:class="{active: (selectedToneTemp||perContactTone)===t.id}" style="cursor:pointer;border-radius:10px;padding:8px 10px;display:flex;align-items:center;gap:10px;border:1px solid var(--theme-border);background:var(--panel-2);transition:.15s;">
<div style="width:36px;height:36px;border-radius:999px;display:flex;align-items:center;justify-content:center;background:var(--theme-gold-soft);font-size:16px;flex-shrink:0;">
<i :class="tonePreviewId===t.id ? 'ri-loader-4-line spin' : 'ri-music-2-fill'" style="color:var(--gold);"></i>
</div>
<div style="flex:1;font-weight:600;font-size:14px;">@{{ t.label }}</div>
<div v-if="(selectedToneTemp||perContactTone)===t.id" style="font-size:12px;color:var(--gold);">✓</div>
</div>
</div>

<!-- Add custom tone -->
<button class="tone-add-btn" @click="triggerToneUpload">
<i class="ri-add-line"></i> إضافة نغمة مخصصة
</button>
<input ref="toneFileInput" type="file" accept="audio/mpeg,audio/mp3,audio/ogg,audio/wav" style="display:none;" @change="onToneFileSelected">

<div v-if="customTonePreviewUrl" style="border-radius:10px;padding:10px 12px;border:1px solid var(--theme-border);background:var(--panel-2);display:flex;flex-direction:column;gap:8px;">
<div style="display:flex;align-items:center;gap:8px;">
<i class="ri-file-music-line" style="color:var(--gold);font-size:20px;"></i>
<div style="flex:1;font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;direction:ltr;">@{{ customToneFileName || 'نغمة مخصصة' }}</div>
</div>
<audio :src="customTonePreviewUrl" controls style="width:100%;height:40px;border-radius:6px;"></audio>
</div>
<div v-else style="border-radius:10px;padding:12px;border:1px dashed var(--theme-border);background:var(--panel-2);font-size:12px;color:var(--muted);text-align:center;line-height:1.8;">
Right click on any short voice note or MP3 file in chat<br>
and select "Save for Notifications". It will appear here.
</div>

<!-- Volume -->
<div style="display:flex;align-items:center;gap:10px;padding:4px 0;">
<i class="ri-volume-down-line" style="color:var(--muted);font-size:16px;"></i>
<input type="range" min="0" max="100" v-model.number="tonePickerVolume" style="flex:1;height:4px;accent-color:var(--gold);">
<i class="ri-volume-up-line" style="color:var(--muted);font-size:16px;"></i>
<span style="font-size:12px;color:var(--muted);min-width:30px;text-align:left;">@{{ tonePickerVolume }}%</span>
</div>

</div>

<!-- Save / Cancel -->
<div style="padding:12px 14px;border-top:1px solid var(--theme-border);display:flex;gap:8px;justify-content:flex-end;">
<button class="profile-action-btn" @click="tonePickerOpen=false">إلغاء</button>
<button class="profile-action-btn primary" @click="saveToneSettings">حفظ</button>
</div>
</div>
</div>

<!-- Saved messages panel -->

<div class="folders-modal" v-if="savedMessagesOpen" @click.self="savedMessagesOpen=false">

<div class="folders-card" style="width:min(620px,96%);max-height:88vh;display:flex;flex-direction:column;">

<div class="folders-head">

<div style="display:flex;align-items:center;gap:10px;">

<i class="ri-bookmark-3-fill" style="color:var(--gold);font-size:20px;"></i>

<strong></strong>

<span v-if="savedMessagesList.length" style="background:var(--gold);color:#000;border-radius:999px;padding:1px 8px;font-size:12px;font-weight:700;">@{{ savedMessagesList.length }}</span>

</div>

<button class="h-icon-btn" @click="savedMessagesOpen=false"><i class="ri-close-line"></i></button>

</div>

<!-- Loading -->

<div v-if="savedMessagesLoading" style="padding:50px;text-align:center;color:var(--muted);">

<i class="ri-loader-4-line" style="font-size:32px;animation:spin 1s linear infinite;display:block;margin-bottom:8px;"></i>

<span></span>

</div>

<!-- Empty state -->

<div v-else-if="savedMessagesList.length===0" style="padding:60px 20px;text-align:center;color:var(--muted);">

<i class="ri-bookmark-line" style="font-size:64px;display:block;margin-bottom:12px;opacity:.25;"></i>

<div style="font-size:16px;font-weight:600;margin-bottom:6px;"></div>

<div style="font-size:13px;opacity:.7;"></div>

</div>

<!-- List -->

<div v-else style="flex:1;overflow:auto;">

<div v-for="msg in savedMessagesList" :key="msg.savedId"

class="saved-msg-row">

<!-- Avatar -->

<div class="saved-msg-avatar">

<img v-if="msg.senderAvatar" :src="msg.senderAvatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;" v-on:error="handleAvatarError($event, null)">

<div v-else style="width:36px;height:36px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;color:#000;font-size:15px;">@{{ (msg.senderName||'?')[0] }}</div>

</div>

<!-- Content -->

<div style="flex:1;min-width:0;">

<div style="display:flex;align-items:baseline;gap:8px;margin-bottom:3px;">

<span style="font-size:13px;font-weight:600;color:var(--text);">@{{ msg.senderName }}</span>

<span style="font-size:11px;color:var(--muted);">@{{ formatMessageTime(msg.createdAt) }}</span>

</div>

<!-- Image preview -->

<div v-if="msg.messageType==='image' && msg.attachmentUrl" style="margin-bottom:4px;">

<img :src="msg.attachmentUrl" style="max-height:80px;max-width:180px;border-radius:8px;object-fit:cover;">

</div>

<!-- Audio/video/doc icon -->

<div v-else-if="msg.messageType==='audio'" style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gold);">

<i class="ri-music-2-fill"></i><span>رسالة صوتية</span>

</div>

<div v-else-if="msg.messageType==='video'" style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);">

<i class="ri-video-line"></i><span>مقطع فيديو</span>

</div>

<!-- Text content -->

<div v-if="msg.content" style="font-size:14px;color:var(--text);white-space:pre-wrap;word-break:break-word;max-height:60px;overflow:hidden;text-overflow:ellipsis;">@{{ msg.content }}</div>

<div v-else-if="!msg.messageType||msg.messageType==='text'" style="font-size:13px;color:var(--muted);"></div>

</div>

<!-- Actions -->

<div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0;">

<button class="h-icon-btn" style="font-size:13px;gap:4px;" title="" @click="jumpToSavedMessage(msg)">

<i class="ri-corner-up-right-line"></i>

</button>

<button class="h-icon-btn" style="font-size:13px;color:var(--muted);" title="" @click="unsaveMessageById(msg.id)">

<i class="ri-bookmark-fill" style="color:var(--gold);"></i>

</button>

</div>

</div>

</div>

</div>

</div>

<!-- Media gallery panel -->

<div class="folders-modal" v-if="mediaGalleryOpen" @click.self="mediaGalleryOpen=false">

<div class="folders-card" style="width:min(640px,100%);max-height:88vh;display:flex;flex-direction:column;">

<div class="folders-head">

<strong style="display:flex;align-items:center;gap:8px;"><i class="ri-image-2-line" style="color:var(--gold);font-size:18px;"></i> وسائط المحادثة</strong>

<button class="h-icon-btn" @click="mediaGalleryOpen=false"><i class="ri-close-line"></i></button>

</div>

<div style="display:flex;gap:6px;padding:10px 12px;border-bottom:1px solid var(--theme-border);flex-wrap:wrap;">

<button v-for="tab in [['images','صور'],['videos','فيديو'],['audio','صوتيات'],['links','روابط']]"
:key="tab[0]" @click="mediaGalleryTab=tab[0]"

:style="mediaGalleryTab===tab[0]?'background:var(--gold);color:#000;border-color:var(--gold);font-weight:700;':''"

style="border:1px solid var(--theme-border);border-radius:20px;padding:5px 14px;cursor:pointer;font-size:13px;font-weight:500;background:var(--panel-2);color:var(--text);transition:.15s;">

@{{ tab[1] }}

</button>

</div>

<div style="flex:1;overflow:auto;padding:12px;">

<div v-if="mediaGalleryTab==='images'">

<template v-if="galleryImagesByMonth.length">
<div v-for="group in galleryImagesByMonth" :key="group.key" style="margin-bottom:20px;">
<div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;padding:6px 0 8px;border-bottom:1px solid var(--theme-border);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
<span>@{{ group.label }}</span>
<span style="font-size:11px;background:var(--panel-2);border-radius:20px;padding:2px 8px;border:1px solid var(--theme-border);">@{{ group.items.length }}</span>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;">
<div v-for="msg in group.items" :key="msg.id" style="aspect-ratio:1;border-radius:8px;overflow:hidden;cursor:pointer;background:var(--panel-2);" @click="openMediaModal(msg)">
<img :src="msg.attachmentUrl" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
</div>
</div>
</div>
</template>

<div v-if="galleryImages.length===0" style="text-align:center;padding:40px;color:var(--muted);"><i class="ri-image-line" style="font-size:52px;opacity:.3;display:block;margin-bottom:10px;"></i><span>لا توجد صور</span></div>

</div>

<div v-if="mediaGalleryTab==='videos'">

<template v-if="galleryVideosByMonth.length">
<div v-for="group in galleryVideosByMonth" :key="group.key" style="margin-bottom:20px;">
<div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;padding:6px 0 8px;border-bottom:1px solid var(--theme-border);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
<span>@{{ group.label }}</span>
<span style="font-size:11px;background:var(--panel-2);border-radius:20px;padding:2px 8px;border:1px solid var(--theme-border);">@{{ group.items.length }}</span>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;">
<div v-for="msg in group.items" :key="msg.id" style="aspect-ratio:1;border-radius:8px;overflow:hidden;cursor:pointer;background:var(--panel-2);position:relative;" @click="openMediaModal(msg)">
<video :src="msg.attachmentUrl + '#t=0.001'" style="width:100%;height:100%;object-fit:cover;" muted preload="metadata"></video>
<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.2);"><i class="ri-play-circle-fill" style="font-size:36px;color:rgba(255,255,255,.85);"></i></div>
</div>
</div>
</div>
</template>

<div v-if="galleryVideos.length===0" style="text-align:center;padding:40px;color:var(--muted);"><i class="ri-vidicon-line" style="font-size:52px;opacity:.3;display:block;margin-bottom:10px;"></i><span>لا توجد فيديوهات</span></div>

</div>

<div v-if="mediaGalleryTab==='audio'" style="display:grid;gap:8px;">

<div v-for="msg in galleryAudio" :key="msg.id" class="audio" style="padding:8px 10px;">

<button class="play" @click="toggleAudioPlayback(msg)">

<i :class="msg.isPlaying ? 'ri-pause-line' : 'ri-play-line'"></i>

</button>

<div class="wave" :data-mid="msg.id"

@pointerdown.prevent.stop="beginWaveSeek(msg, $event)"
@pointermove.prevent.stop="moveWaveSeek(msg, $event)"
@pointerup.prevent.stop="endWaveSeek(msg, $event)"
@pointercancel.prevent.stop="endWaveSeek(msg, $event)">

<div v-for="bar in 22" :key="bar" class="bar" :class="{ on: msg.currentBar >= bar }"

:style="{ height: ((bar % 7) * 3 + 6) + 'px' }"></div>

</div>

<span class="audio-time" style="min-width:70px;">@{{ formatDuration(msg.playbackPosition || 0) }} / @{{ formatDuration(msg.audioDuration || 0) }}</span>

<div style="font-size:12px;color:var(--muted);min-width:0;flex-shrink:1;">@{{ msg.attachmentName || 'رسالة صوتية' }}</div>

</div>

<div v-if="galleryAudio.length===0" style="text-align:center;padding:40px;color:var(--muted);"><i class="ri-music-2-line" style="font-size:52px;opacity:.3;display:block;margin-bottom:10px;"></i><span>لا توجد ملفات صوتية</span></div>

</div>

<div v-if="mediaGalleryTab==='links'" style="display:grid;gap:8px;">

<a v-for="msg in galleryLinks" :key="msg.id" :href="extractLink(msg.content)" target="_blank" rel="noopener noreferrer" style="border:1px solid var(--theme-border);border-radius:10px;padding:12px 16px;background:var(--panel-2);display:block;text-decoration:none;">

<div style="font-size:11px;color:var(--muted);margin-bottom:4px;">@{{ formatMessageTime(msg.createdAt) }}</div>

<div style="font-size:13px;color:var(--gold);word-break:break-all;">@{{ extractLink(msg.content) }}</div>

</a>

<div v-if="galleryLinks.length===0" style="text-align:center;padding:40px;color:var(--muted);"><i class="ri-links-line" style="font-size:52px;opacity:.3;display:block;margin-bottom:10px;"></i><span>لا توجد روابط</span></div>

</div>

</div>

</div>

</div>

<!-- Premium profile modal (Telegram style) -->
<div class="profile-modal" v-if="profileModalContact" @click.self="closeProfile">
<div class="profile-card">
<!-- Banner -->
<div class="profile-banner" ref="profileBanner" :style="(profileModalContact.avatar_url ? 'background:var(--theme-page-bg,#0d0d1a)' : (profileModalContact._bannerStyle || 'background:linear-gradient(135deg,#17130c,#5b4124)')) + ';height:' + (200 * profileBannerScale) + 'px'">
<img v-if="profileModalContact.avatar_url" class="profile-banner-img" ref="profileBannerImg" :src="profileModalContact.avatar_url" alt="" v-on:error="handleAvatarError($event, profileModalContact)">
<button class="profile-close" @click="closeProfile"><i class="ri-close-line"></i></button>

<!-- Avatar -->
<div class="profile-avatar-wrap" ref="profileAvatarWrap" :style="{ bottom: (-40 * profileBannerScale) + 'px', transform: 'translateX(-50%) scale(' + Math.max(0.55, profileBannerScale) + ')', opacity: Math.max(0, (profileBannerScale - 0.3) / 0.7) }">
<div class="profile-avatar-inner">
<img v-if="profileModalContact.avatar_url" :src="profileModalContact.avatar_url" alt="" v-on:error="handleAvatarError($event, profileModalContact)">
<div v-else class="profile-initials">@{{ getAuthorInitial(profileModalContact.name) }}</div>
</div>
<div class="profile-online-dot" v-if="profileModalContact.isOnline"></div>
</div>
</div>
<!-- Body -->
<div class="profile-body" @scroll="onProfileBodyScroll">
<div class="profile-name">@{{ profileModalContact.name }}</div>

<div class="profile-username-row">
<div class="profile-username" v-if="profileModalContact.username" style="cursor:pointer;text-decoration:underline;text-decoration-color:var(--gold);text-underline-offset:3px;text-decoration-style:dotted;" @click="copyToClipboard('@' + profileModalContact.username)" title="انسخ اسم المستخدم">@{{ '@' + profileModalContact.username }}</div>
</div>

<div class="profile-status-text">
<span v-if="profileModalContact.isOnline" style="color:#2ecc71;">● متصل الآن</span>
<span v-else-if="profileModalContact.lastSeenAt">آخر ظهور: @{{ formatLastSeen(profileModalContact) }}</span>
<span v-else style="color:var(--muted)">غير متصل</span>
</div>

<!-- Action buttons -->
<div class="profile-actions">
<button class="profile-action-btn" @click="closeProfile()">
<i class="ri-message-3-line"></i>
<span>رسالة</span>
</button>
<button class="profile-action-btn" @click="closeProfile(); startCall('voice')">
<i class="ri-phone-line"></i>
<span>اتصال</span>
</button>
<button class="profile-action-btn" @click="closeProfile(); startCall('video')">
<i class="ri-vidicon-line"></i>
<span>فيديو</span>
</button>
<div>
<button class="profile-action-btn" @click.stop="profileMoreOpen = !profileMoreOpen">
<i class="ri-more-2-fill"></i>
<span>المزيد</span>
</button>
<div class="profile-fixed-overlay" v-if="profileMoreOpen" @click.self="profileMoreOpen = false">
<div class="profile-fixed-menu">
<button @click="profileMoreOpen = false; profileAutoDeleteOpen = true"><i class="ri-timer-line"></i> حذف تلقائي</button>
<button @click="profileMoreOpen = false; profileExportOpen = true"><i class="ri-export-line"></i> تصدير الدردشة</button>
<button @click="profileMoreOpen = false; profileAddFolderOpen = true"><i class="ri-folder-add-line"></i> إضافة إلى مجلد</button>
<div class="profile-more-divider"></div>
<button v-if="!contactBlockedInProfile" @click="profileMoreOpen = false; profileBlockConfirm = true"><i class="ri-user-unfollow-line"></i> حظر المستخدم</button>
<button v-else @click="profileMoreOpen = false; unblockContactInProfile()"><i class="ri-user-follow-line"></i> فك الحظر</button>
</div>
</div>
</div>
</div>

<!-- Info: bio / phone -->
<div class="profile-info-section">
<div class="profile-info-row" v-if="profileModalContact.phone || profileModalContact.role">
<i class="ri-phone-line"></i>
<div>
<div class="profile-info-label">@{{ profileModalContact.role === 'teacher' ? 'معلم' : 'طالب' }}</div>
<div class="profile-info-val">@{{ profileModalContact.phone || profileModalContact.name }}</div>
</div>
</div>
<div class="profile-info-row">
<i class="ri-user-line"></i>
<div style="flex:1;">
<div class="profile-info-label">الاسم الكامل</div>
<div class="profile-info-val">@{{ profileModalContact.name }}</div>
</div>
<button class="profile-qr-icon" @click="qrModalOpen = true; qrModalContact = normalizeProfileContact(profileModalContact); $nextTick(() => { doGenerateQrCode(); })" title="رمز QR"><i class="ri-qr-code-line"></i></button>
</div>
<div class="profile-info-row" v-if="profileModalContact.lastSeenAt">
<i class="ri-time-line"></i>
<div>
<div class="profile-info-label">آخر ظهور</div>
<div class="profile-info-val">@{{ formatLastSeen(profileModalContact) }}</div>
</div>
</div>
</div>

<!-- Section divider -->
<div class="profile-section-divider"></div>

<!-- Media sections -->
<div class="profile-section-title"><i class="ri-attachment-line"></i> الوسائط المشتركة</div>
<div class="profile-media-vert">
<div class="profile-media-row" v-if="profileMediaImages.length" @click="openProfileMediaModal('images')">
<i class="ri-image-line"></i>
<span class="profile-media-label">الصور</span>
<span class="profile-media-count">@{{ profileMediaImages.length }}</span>
</div>
<div class="profile-media-row" v-if="profileMediaVideos.length" @click="openProfileMediaModal('videos')">
<i class="ri-vidicon-line"></i>
<span class="profile-media-label">الفيديو</span>
<span class="profile-media-count">@{{ profileMediaVideos.length }}</span>
</div>
<div class="profile-media-row" v-if="profileMediaAudio.length" @click="openProfileMediaModal('audio')">
<i class="ri-mic-line"></i>
<span class="profile-media-label">الصوتيات</span>
<span class="profile-media-count">@{{ profileMediaAudio.length }}</span>
</div>
<div class="profile-media-row" v-if="profileMediaFiles.length" @click="openProfileMediaModal('files')">
<i class="ri-file-line"></i>
<span class="profile-media-label">الملفات</span>
<span class="profile-media-count">@{{ profileMediaFiles.length }}</span>
</div>
</div>
<div class="profile-section-divider"></div>

<!-- Action list: share, edit nickname, delete, block -->
<div class="profile-actions-list">
<div class="profile-action-item" @click="profileShareChatOpen = true">
<i class="ri-share-forward-line"></i>
<div class="action-text">
<div class="action-title">مشاركة الدردشة</div>
<div class="action-sub">إرسال إلى مستخدم آخر</div>
</div>
</div>
<div class="profile-action-item" @click="profileNicknameDraft = contactNickname; profileNicknameEdit = true">
<i class="ri-edit-line"></i>
<div class="action-text">
<div class="action-title">تعديل الاسم</div>
<div class="action-sub">@{{ contactNickname ? 'الاسم الحالي: ' + contactNickname : 'تعيين اسم محلي لهذه الدردشة' }}</div>
</div>
</div>
<div class="profile-action-item" @click="deleteContact()">
<i class="ri-user-unfollow-line"></i>
<div class="action-text">
<div class="action-title">حذف الجهة</div>
<div class="action-sub">إزالة من قائمة جهات الاتصال</div>
</div>
</div>
<div class="profile-action-item danger" @click="profileBlockConfirm = true">
<i class="ri-forbid-line"></i>
<div class="action-text">
<div class="action-title">حظر المستخدم</div>
<div class="action-sub">منع المستخدم من إرسال الرسائل</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- ════ My Profile (sidebar) ════ -->
<div class="profile-modal" v-if="myProfileOpen" @click.self="myProfileOpen = false">
<div class="profile-card" style="max-width:420px;">
<div class="profile-banner" ref="myProfileBanner" :style="myBannerStyle">
<div class="profile-avatar-wrap" ref="myProfileAvatarWrap" :style="myAvatarWrapStyle">
<button class="profile-banner-change-btn" @click="triggerMyAvatarUpload" title="تغيير الصورة"><i class="ri-camera-line"></i></button>
<img v-if="myProfileAvatar" :src="myProfileAvatar" alt="" v-on:error="handleAvatarError($event, {avatar_url: myProfileAvatar})">
<div v-else class="profile-initials" :style="myAvatarInitialsStyle">@{{ getAuthorInitial(myProfileDisplayName) }}</div>
</div>
<div style="position:absolute;top:8px;right:8px;display:flex;gap:6px;">
<button class="profile-close" style="position:static;" @click="myProfileOpen = false"><i class="ri-close-line"></i></button>
<button class="profile-close" style="position:static;font-size:18px;" @click="myProfileEditOpen = true" title="تعديل"><i class="ri-pencil-line"></i></button>
</div>
</div>
<div class="profile-body" ref="myProfileBody" style="padding-top:60px;" @scroll="onMyProfileScroll">
<div class="profile-name" style="margin-top:0;">@{{ myProfileDisplayName }}</div>

<!-- Online Status -->
<div style="display:flex;align-items:center;justify-content:center;gap:6px;margin:4px 0 12px;">
<span class="dot" style="background:#2ecc71;width:10px;height:10px;"></span>
<span style="font-size:13px;color:var(--muted);">متصل</span>
</div>

<!-- Info Section -->
<div class="profile-info-section" style="margin-top:8px;">
<!-- Phone row -->
<div class="profile-info-row">
<i class="ri-phone-line"></i>
<div style="flex:1;">
<div class="profile-info-label">رقم الهاتف</div>
<div class="profile-info-val">@{{ currentUserPhone || 'غير محدد' }}</div>
</div>
</div>
<!-- Username row + QR -->
<div class="profile-info-row" style="margin-bottom:0;">
<i class="ri-at-line"></i>
<div style="flex:1;">
<div class="profile-info-label">اسم المستخدم</div>
<div class="profile-info-val">@{{ myProfileDisplayUsername }}</div>
</div>
<button class="profile-qr-icon" @click="qrModalOpen = true; qrModalContact = {id:currentUserId,name:myProfileDisplayName,avatar_url:myProfileAvatar}; $nextTick(() => { doGenerateQrCode(); })" title="رمز QR" style="flex-shrink:0;width:48px;height:48px;border-radius:10px;border:1px solid var(--theme-border);background:var(--panel-2);display:flex;align-items:center;justify-content:center;font-size:22px;cursor:pointer;margin-right:8px;">
<i class="ri-qr-code-line" style="color:var(--gold);"></i>
</button>
</div>

<!-- Birth Date + Age -->
<div class="profile-info-row">
<i class="ri-cake-line"></i>
<div style="flex:1;">
<div class="profile-info-label">تاريخ الميلاد</div>
<div class="profile-info-val">@{{ myProfileBirthDateDisplay }} (@{{ myProfileAge }} سنة)</div>
</div>
</div>
</div>

<!-- Status History Section -->
<div style="margin-top:16px;border-top:1px solid var(--theme-border);padding-top:12px;">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
<strong style="font-size:14px;color:var(--text);"><i class="ri-history-line"></i> سجل الحالات</strong>
</div>
<!-- Smart date scroll -->
<div ref="myProfileStatusDate" style="text-align:center;font-size:11px;color:var(--muted);height:16px;opacity:0;transition:opacity .15s;"></div>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;max-height:220px;overflow-y:auto;" @scroll="onMyStatusScroll">
<div v-for="(st, si) in myProfileStatuses" :key="si" style="position:relative;aspect-ratio:9/16;border-radius:8px;overflow:hidden;background:var(--panel-2);cursor:pointer;" @click="openMyStatusViewer(si)">
<img v-if="st.type === 'image' && st.contentUrl" :src="st.contentUrl" style="width:100%;height:100%;object-fit:cover;">
<div v-else :style="{width:'100%',height:'100%',display:'flex',alignItems:'center',justifyContent:'center',background:st.bgColor || 'var(--panel-2)',fontSize:'28px',color:'#fff'}"><i class="ri-image-line"></i></div>
<div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.7));padding:4px 6px;display:flex;align-items:center;justify-content:space-between;direction:ltr;">
<span style="font-size:10px;color:#fff;">@{{ st.duration || '0:00' }}</span>
<button style="width:22px;height:22px;border-radius:50%;border:none;background:var(--gold);color:#000;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;" @click.stop="openMyStatusViewer(si)"><i class="ri-play-fill"></i></button>
</div>
</div>
<div v-if="!myProfileStatuses.length" style="grid-column:1/-1;text-align:center;padding:20px;color:var(--muted);font-size:13px;">
<i class="ri-camera-line" style="display:block;font-size:24px;margin-bottom:6px;"></i>
لا توجد حالات بعد
</div>
</div>
</div>

</div>
</div>
</div>

<!-- My Profile Edit Sub-modal -->
<div class="profile-sub-overlay" v-if="myProfileEditOpen" @click.self="myProfileEditOpen = false">
<div class="profile-sub-card" style="position:relative;">
<div class="folders-head">
<strong><i class="ri-pencil-line"></i> تعديل الملف الشخصي</strong>
<button class="h-icon-btn" @click="myProfileEditOpen = false"><i class="ri-close-line"></i></button>
</div>
<div style="padding:12px;display:flex;flex-direction:column;gap:10px;">
<div class="profile-info-row">
<i class="ri-user-line"></i>
<div style="flex:1;">
<div class="profile-info-label">الاسم الكامل</div>
<input class="my-profile-input" v-model="myProfileEditName" placeholder="اسمك" maxlength="50">
</div>
</div>
<div class="profile-info-row">
<i class="ri-at-line"></i>
<div style="flex:1;">
<div class="profile-info-label">اسم المستخدم</div>
<input class="my-profile-input" v-model="myProfileEditUsername" placeholder="@username" maxlength="30" dir="ltr">
</div>
</div>
<div class="profile-info-row">
<i class="ri-phone-line"></i>
<div style="flex:1;">
<div class="profile-info-label">رقم الهاتف</div>
<input class="my-profile-input" v-model="myProfileEditPhone" placeholder="رقم الهاتف" maxlength="20" dir="ltr">
</div>
</div>
<div class="profile-info-row">
<i class="ri-cake-line"></i>
<div style="flex:1;">
<div class="profile-info-label">تاريخ الميلاد</div>
<input class="my-profile-input" type="date" v-model="myProfileBirthDate" maxlength="10">
</div>
</div>
<div class="profile-info-row">
<i class="ri-information-line"></i>
<div style="flex:1;">
<div class="profile-info-label">نبذة عني</div>
<textarea class="my-profile-textarea" v-model="myProfileEditBio" placeholder="اكتب نبذة عن نفسك..." rows="2" maxlength="200"></textarea>
</div>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
<button class="profile-action-btn primary" @click="saveMyProfileEdit">حفظ</button>
<button class="profile-action-btn" @click="myProfileEditOpen = false" style="background:transparent;border:1px solid var(--theme-border);">إلغاء</button>
</div>
</div>
</div>
</div>

<!-- My Profile Status Viewer -->
<div class="profile-modal" v-if="myProfileStatusViewerOpen" @click.self="myProfileStatusViewerOpen = false">
<div class="profile-card" style="max-width:580px;background:var(--dark);">
<div class="profile-banner" style="height:auto;min-height:400px;background:var(--darker);display:flex;align-items:center;justify-content:center;position:relative;">
<!-- Navigation arrows -->
<button class="h-icon-btn" style="position:absolute;left:8px;top:50%;transform:translateY(-50%);z-index:10;color:#fff;font-size:28px;background:rgba(0,0,0,0.3);border-radius:50%;width:40px;height:40px;" @click="prevMyProfileStatus"><i class="ri-arrow-right-s-line"></i></button>
<button class="h-icon-btn" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);z-index:10;color:#fff;font-size:28px;background:rgba(0,0,0,0.3);border-radius:50%;width:40px;height:40px;" @click="nextMyProfileStatus"><i class="ri-arrow-left-s-line"></i></button>
<!-- Status content -->
<div style="width:100%;max-width:320px;aspect-ratio:9/16;border-radius:12px;overflow:hidden;position:relative;">
<img v-if="myProfileViewerStatus?.type === 'image' && myProfileViewerStatus?.contentUrl" :src="myProfileViewerStatus.contentUrl" style="width:100%;height:100%;object-fit:cover;">
<div v-else :style="{width:'100%',height:'100%',display:'flex',alignItems:'center',justifyContent:'center',background:myProfileViewerStatus?.bgColor || 'var(--panel-2)',color:'#fff',fontSize:'40px'}"><i class="ri-image-line"></i></div>
<!-- Date/time -->
<div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.8));padding:12px;color:#fff;">
<div style="font-size:12px;opacity:0.8;">@{{ myProfileViewerStatusDate }}</div>
<!-- Eye icon + views -->
<div style="display:flex;align-items:center;gap:6px;margin-top:4px;font-size:13px;">
<i class="ri-eye-line"></i>
<span>@{{ myProfileViewerStatus?.viewCount || 0 }}</span>
<span style="font-size:12px;opacity:0.7;cursor:pointer;" @click.stop="openMyStatusViewers">مشاهدة الكل</span>
</div>
</div>
</div>
<!-- Close + Edit header -->
<button class="profile-close" @click="myProfileStatusViewerOpen = false" style="color:#fff;"><i class="ri-close-line"></i></button>
<button class="profile-close" style="right:50px;color:#fff;font-size:20px;" @click="myProfileStatusViewerOpen = false; myProfileEditMode = true" title="تعديل"><i class="ri-pencil-line"></i></button>
</div>
<!-- Status counter -->
<div style="text-align:center;padding:8px;color:var(--muted);font-size:12px;">@{{ myProfileViewerIdx + 1 }} / @{{ myProfileStatuses.length }}</div>
</div>
</div>

<!-- Viewers list for my profile status -->
<div class="profile-sub-overlay" v-if="myProfileViewersOpen" @click.self="myProfileViewersOpen = false">
<div class="profile-sub-card" style="position:relative;">
<div class="folders-head">
<strong>المشاهدون</strong>
<button class="h-icon-btn" @click="myProfileViewersOpen = false"><i class="ri-close-line"></i></button>
</div>
<div style="padding:12px;display:grid;gap:6px;max-height:300px;overflow:auto;">
<div v-for="(vw, vi) in myProfileViewersList" :key="vi" style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;background:var(--panel-2);">
<div class="avatar" style="width:34px;height:34px;">
<img v-if="vw.avatar" :src="vw.avatar" alt="">
<span v-else>@{{ getAuthorInitial(vw.name) }}</span>
</div>
<div style="flex:1;font-size:13px;font-weight:600;color:var(--text);">@{{ vw.name }}</div>
<div style="font-size:11px;color:var(--muted);">@{{ vw.time || '' }}</div>
</div>
<div v-if="!myProfileViewersList.length" style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">لا يوجد مشاهدون</div>
</div>
</div>
</div>

<!-- ════ Auto-delete messages modal ════ -->
<div class="profile-sub-overlay" v-if="profileAutoDeleteOpen" @click.self="profileAutoDeleteOpen = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-timer-line"></i> حذف تلقائي للرسائل</h3>
<div class="auto-delete-text">
Auto-delete messages<br>
Automatically delete new messages for you and the contact after a certain period of time.<br><br>
You can also set a default self-destruct timer for all your chats in Settings.
</div>
<div class="auto-delete-options">
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===0}" @click="profileAutoDeleteDuration=0">إيقاف</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===86400}" @click="profileAutoDeleteDuration=86400">يوم</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===172800}" @click="profileAutoDeleteDuration=172800">يومان</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===259200}" @click="profileAutoDeleteDuration=259200">3 أيام</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===432000}" @click="profileAutoDeleteDuration=432000">5 أيام</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===604800}" @click="profileAutoDeleteDuration=604800">أسبوع</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===1209600}" @click="profileAutoDeleteDuration=1209600">أسبوعان</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===2592000}" @click="profileAutoDeleteDuration=2592000">شهر</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===5184000}" @click="profileAutoDeleteDuration=5184000">شهران</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===7776000}" @click="profileAutoDeleteDuration=7776000">3 أشهر</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===15552000}" @click="profileAutoDeleteDuration=15552000">6 أشهر</button>
<button class="auto-delete-opt" :class="{active:profileAutoDeleteDuration===31536000}" @click="profileAutoDeleteDuration=31536000">سنة</button>
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileAutoDeleteOpen = false; $nextTick(() => { profileMoreOpen = true })">إلغاء</button>
<button class="sub-btn-primary" @click="saveAutoDelete()">تأكيد</button>
</div>
</div>
</div>

<!-- ════ Export chat history modal ════ -->
<div class="profile-sub-overlay" v-if="profileExportOpen" @click.self="profileExportOpen = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-export-line"></i> تصدير الدردشة</h3>
<div class="export-check-list">
<div class="export-check-item" @click="profileExportPhotos = !profileExportPhotos">
<input type="checkbox" v-model="profileExportPhotos">
<i class="ri-image-line"></i>
<label>الصور</label>
</div>
<div class="export-check-item" @click="profileExportVideos = !profileExportVideos">
<input type="checkbox" v-model="profileExportVideos">
<i class="ri-vidicon-line"></i>
<label>الفيديو</label>
</div>
<div class="export-check-item" @click="profileExportVoice = !profileExportVoice">
<input type="checkbox" v-model="profileExportVoice">
<i class="ri-mic-line"></i>
<label>الرسائل الصوتية</label>
</div>
<div class="export-check-item" @click="profileExportVideoMsg = !profileExportVideoMsg">
<input type="checkbox" v-model="profileExportVideoMsg">
<i class="ri-video-line"></i>
<label>رسائل الفيديو</label>
</div>
<div class="export-check-item" @click="profileExportStickers = !profileExportStickers">
<input type="checkbox" v-model="profileExportStickers">
<i class="ri-emoji-sticker-line"></i>
<label>الملصقات</label>
</div>
<div class="export-check-item" @click="profileExportGifs = !profileExportGifs">
<input type="checkbox" v-model="profileExportGifs">
<i class="ri-file-gif-line"></i>
<label>الجيفز</label>
</div>
<div class="export-check-item" @click="profileExportFiles = !profileExportFiles">
<input type="checkbox" v-model="profileExportFiles">
<i class="ri-file-line"></i>
<label>الملفات</label>
</div>
</div>
<div class="export-size-row">
<label>أقصى حجم:</label>
<input type="range" min="0" max="4000" step="50" v-model.number="profileExportMaxSize">
<span>@{{ profileExportMaxSize }} MB</span>
</div>
<div class="export-info-text">
Format: HTML, Path: Downloads\Telegram Desktop<br>
From: the oldest message, to: present
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileExportOpen = false; $nextTick(() => { profileMoreOpen = true })">إلغاء</button>
<button class="sub-btn-primary" @click="doExportChat()">تصدير</button>
</div>
</div>
</div>

<!-- ════ Add to folder modal ════ -->
<div class="profile-sub-overlay" v-if="profileAddFolderOpen" @click.self="profileAddFolderOpen = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-folder-add-line"></i> إضافة إلى مجلد</h3>
<div class="folder-select-grid">
<!-- Built-in folders (informational) -->
<div class="folder-select-item active" style="opacity:.7;cursor:default;">
<i class="ri-mail-unread-line"></i>
<span>غير مقروء</span>
<i v-if="contactUnreadInProfile" class="ri-check-line folder-check"></i>
</div>
<div class="folder-select-item active" style="opacity:.7;cursor:default;">
<i class="ri-user-3-line"></i>
<span>خاصة</span>
<i v-if="contactPrivateInProfile" class="ri-check-line folder-check"></i>
</div>
<div class="folder-select-item active" style="opacity:.7;cursor:default;">
<i class="ri-group-line"></i>
<span>مجموعات</span>
<i v-if="contactGroupInProfile" class="ri-check-line folder-check"></i>
</div>
<!-- Divider -->
<div style="height:1px;background:rgba(255,255,255,.06);margin:8px 0;"></div>
<!-- User-created folders -->
<template v-if="foldersConfig.length">
<div class="folder-select-item" v-for="(folder, fi) in foldersConfig" :key="fi" :class="{active: folder.includeIds.includes(String(profileModalContact.id))}" @click="toggleFolderInclude(folder, fi)">
<i :class="folder.icon || 'ri-folder-3-line'"></i>
<span>@{{ folder.name }}</span>
<i v-if="folder.includeIds.includes(String(profileModalContact.id))" class="ri-check-line folder-check"></i>
</div>
</template>
<template v-else>
<div style="text-align:center;padding:16px;color:var(--muted);font-size:13px;">
<i class="ri-folder-open-line" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4;"></i>
لا توجد مجلدات مخصصة بعد.
</div>
</template>
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileAddFolderOpen = false; $nextTick(() => { profileMoreOpen = true })">إغلاق</button>
</div>
</div>
</div>

<!-- ════ Block user confirm modal ════ -->
<div class="profile-sub-overlay" v-if="profileBlockConfirm" @click.self="profileBlockConfirm = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-user-unfollow-line"></i> حظر المستخدم</h3>
<div class="block-warning">
<i class="ri-error-warning-line"></i>
<p>هل أنت متأكد من حظر @{{ profileModalContact?.name }}؟</p>
<div class="block-hint">بعد الحظر، لن يتمكن المستخدم من إرسال رسائل إليك. الرسائل السابقة التي أرسلها لك لن تكون متاحة بعد فك الحظر.</div>
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileBlockConfirm = false">إلغاء</button>
<button class="sub-btn-danger" @click="doBlockUser()">حظر</button>
</div>
</div>
</div>

<!-- ════ Delete contact confirm modal ════ -->
<div class="profile-sub-overlay" v-if="profileDeleteConfirm" @click.self="profileDeleteConfirm = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-user-unfollow-line"></i> حذف الجهة</h3>
<div class="block-warning">
<i class="ri-delete-bin-line" style="color:var(--gold);"></i>
<p>هل أنت متأكد من حذف @{{ profileModalContact?.name }} من جهات الاتصال؟</p>
<div class="block-hint">سيتم إزالة المستخدم من قائمة جهات الاتصال الخاصة بك. يمكنك إضافته مرة أخرى لاحقًا.</div>
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileDeleteConfirm = false">إلغاء</button>
<button class="sub-btn-danger" @click="executeDeleteContact()">حذف</button>
</div>
</div>
</div>

<!-- ════ Edit nickname modal ════ -->
<div class="profile-sub-overlay" v-if="profileNicknameEdit" @click.self="profileNicknameEdit = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-edit-line"></i> تعديل الاسم المحلي</h3>
<div class="nickname-hint">هذا الاسم يظهر لك فقط ولن يراه المستخدم الآخر. يستخدم لتمييز جهات الاتصال.</div>
<input class="nickname-input" v-model="profileNicknameDraft" placeholder="أدخل اسمًا لهذه الدردشة..." maxlength="50" @keydown.enter.prevent="saveNickname()">
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileNicknameEdit = false">إلغاء</button>
<button class="sub-btn-primary" @click="saveNickname()">حفظ</button>
</div>
</div>
</div>

<!-- ════ Share chat modal ════ -->
<div class="profile-sub-overlay" v-if="profileShareChatOpen" @click.self="profileShareChatOpen = false">
<div class="profile-sub-card" style="position:relative;">
<h3><i class="ri-share-forward-line"></i> تحويل إلى...</h3>
<input class="share-search-input" v-model="profileShareQuery" placeholder="ابحث عن مستخدم..." @keydown.esc="profileShareChatOpen = false">
<div class="share-contact-list">
<template v-for="c in filteredContactsForShare" :key="c.id">
<div class="share-contact-item" :class="{selected: profileShareSelected.includes(c.id)}" @click="toggleShareSelection(c.id)">
<div class="share-contact-avatar">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="" v-on:error="handleAvatarError($event, c)">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
</div>
<span class="share-contact-name">@{{ c.name }}</span>
<i v-if="profileShareSelected.includes(c.id)" class="ri-checkbox-circle-fill" style="color:var(--gold);font-size:18px;margin-right:auto;"></i>
</div>
</template>
<div v-if="!filteredContactsForShare.length" style="text-align:center;padding:24px;color:var(--muted);font-size:13px;">لا توجد نتائج</div>
</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="profileShareChatOpen = false">إلغاء</button>
<button class="sub-btn-primary" @click="doForwardToSelected()" :disabled="!profileShareSelected.length"><i class="ri-send-plane-fill"></i> إرسال @{{ profileShareSelected.length ? '('+profileShareSelected.length+')' : '' }}</button>
</div>
</div>
</div>

<!-- ════ Shared groups modal (placeholder) ════ -->
<!-- ════ Profile Media Modal (dedicated per type) ════ -->
<div class="qr-modal-overlay" v-if="profileMediaModalOpen" @click.self="profileMediaModalOpen = false; profileMediaMenuOpen = false; profileMediaCalendarOpen = false; profileMediaCalendarFullOpen = false; voicePlayerExternalSource = null">
<div class="qr-modal-card" style="max-width:700px;">
<div class="qr-header">
<h3><i :class="profileMediaModalType==='images'?'ri-image-line':profileMediaModalType==='videos'?'ri-vidicon-line':profileMediaModalType==='audio'?'ri-mic-line':'ri-file-line'"></i> @{{ profileMediaModalType==='images'?'الصور':profileMediaModalType==='videos'?'الفيديو':profileMediaModalType==='audio'?'الصوتيات':'الملفات' }}</h3>
<div class="qr-header-actions" style="position:relative;">
<button class="h-icon-btn" v-if="profileMediaModalType==='images'||profileMediaModalType==='videos'" @click.stop="profileMediaMenuOpen = !profileMediaMenuOpen"><i class="ri-more-2-fill"></i></button>
<div v-if="profileMediaMenuOpen" class="profile-media-three-dot-menu" @click.stop>
<div class="profile-media-menu-item" @click.stop="initProfileCalendarView(); profileMediaMenuOpen = false; profileMediaCalendarFullOpen = true"><i class="ri-calendar-line"></i> التقويم</div>
</div>
<button class="h-icon-btn" @click="profileMediaModalOpen = false; profileMediaMenuOpen = false; profileMediaCalendarFullOpen = false; voicePlayerExternalSource = null"><i class="ri-close-line"></i></button>
</div>
</div>
<!-- Day-grouped default view (detailed dates) -->
<div v-if="!profileMediaCalendarFullOpen && !profileMediaCalendarOpen" style="padding:16px;max-height:60vh;overflow-y:auto;">
<template v-if="profileMediaModalType==='images'">
<div v-if="getProfileMediaByType('images').length">
<div v-for="day in getProfileMediaCalendarDays()" :key="day.date" style="margin-bottom:18px;">
<div style="font-size:13px;font-weight:600;color:var(--gold);margin-bottom:8px;padding:4px 0;border-bottom:1px solid rgba(255,255,255,.06);">@{{ day.label }}</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;">
<div v-for="(m,i) in day.items" :key="i" style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;background:var(--panel-2);" @click="openMediaModal(m)">
<img :src="m.attachmentUrl" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
</div>
</div>
</div>
</div>
<div v-else style="text-align:center;padding:40px;color:var(--muted);font-size:14px;">لا توجد صور</div>
</template>
<template v-if="profileMediaModalType==='videos'">
<div v-if="getProfileMediaByType('videos').length">
<div v-for="day in getProfileMediaCalendarDays()" :key="day.date" style="margin-bottom:18px;">
<div style="font-size:13px;font-weight:600;color:var(--gold);margin-bottom:8px;padding:4px 0;border-bottom:1px solid rgba(255,255,255,.06);">@{{ day.label }}</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;">
<div v-for="(m,i) in day.items" :key="i" style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;background:var(--panel-2);position:relative;" @click="openMediaModal(m)">
<video :src="m.attachmentUrl + '#t=0.001'" style="width:100%;height:100%;object-fit:cover;" preload="metadata" muted></video>
<i class="ri-play-circle-fill" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:32px;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,.5);"></i>
</div>
</div>
</div>
</div>
<div v-else style="text-align:center;padding:40px;color:var(--muted);font-size:14px;">لا توجد فيديوهات</div>
</template>
<template v-if="profileMediaModalType==='audio'">
<div v-if="getProfileMediaByType('audio').length">
<div v-for="day in getProfileMediaCalendarDays()" :key="day.date" style="margin-bottom:18px;">
<div style="font-size:13px;font-weight:600;color:var(--gold);margin-bottom:8px;padding:4px 0;border-bottom:1px solid rgba(255,255,255,.06);">@{{ day.label }}</div>
<div style="display:grid;gap:8px;">
<div v-for="(m,i) in day.items" :key="i" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:var(--panel-2);cursor:pointer;" @click="toggleAudioPlayback(m)">
<i :class="m.isPlaying ? 'ri-pause-circle-fill' : 'ri-play-circle-fill'" style="font-size:24px;color:var(--gold);flex-shrink:0;"></i>
<div style="flex:1;min-width:0;">
<div style="font-size:13px;">@{{ m.senderName || 'رسالة صوتية' }}</div>
<div style="font-size:11px;color:var(--muted);">@{{ formatDuration(m.audioDuration || 0) }}</div>
</div>
<div style="font-size:11px;color:var(--muted);flex-shrink:0;">@{{ formatMessageTime(m.createdAt) }}</div>
</div>
</div>
</div>
</div>
<div v-else style="text-align:center;padding:40px;color:var(--muted);font-size:14px;">لا توجد صوتيات</div>
</template>
<template v-if="profileMediaModalType==='files'">
<div v-if="getProfileMediaByType('files').length">
<div v-for="day in getProfileMediaCalendarDays()" :key="day.date" style="margin-bottom:18px;">
<div style="font-size:13px;font-weight:600;color:var(--gold);margin-bottom:8px;padding:4px 0;border-bottom:1px solid rgba(255,255,255,.06);">@{{ day.label }}</div>
<div style="display:grid;gap:8px;">
<div v-for="(m,i) in day.items" :key="i" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:var(--panel-2);cursor:pointer;" @click="openMediaModal(m)">
<i class="ri-file-line" style="font-size:18px;color:var(--gold);flex-shrink:0;"></i>
<div style="flex:1;">
<div style="font-size:13px;">@{{ m.attachmentUrl?.split('/')?.pop()?.substring(0,30) || 'ملف' }}</div>
<div style="font-size:11px;color:var(--muted);">@{{ formatMessageTime(m.createdAt) }}</div>
</div>
</div>
</div>
</div>
</div>
<div v-else style="text-align:center;padding:40px;color:var(--muted);font-size:14px;">لا توجد ملفات</div>
</template>
</div>
<!-- Full calendar view (from 3-dot menu) -->
<div v-if="profileMediaCalendarFullOpen" style="padding:16px;max-height:60vh;overflow-y:auto;">
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;justify-content:space-between;">
<button class="h-icon-btn" @click="profileMediaCalendarFullOpen = false" style="flex-shrink:0;"><i class="ri-arrow-right-line"></i></button>
<div style="display:flex;align-items:center;gap:8px;">
<button class="h-icon-btn" @click="profileCalendarPrevMonth" style="font-size:14px;"><i class="ri-arrow-left-s-line"></i></button>
<span style="font-size:14px;font-weight:600;color:var(--gold);min-width:120px;text-align:center;">@{{ profileCalendarYear }} - @{{ String(profileCalendarMonth+1).padStart(2,'0') }}</span>
<button class="h-icon-btn" @click="profileCalendarNextMonth" style="font-size:14px;"><i class="ri-arrow-right-s-line"></i></button>
</div>
<button class="h-icon-btn" @click="profileMediaModalOpen = false; profileMediaMenuOpen = false; profileMediaCalendarFullOpen = false; voicePlayerExternalSource = null"><i class="ri-close-line"></i></button>
</div>
<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;font-size:11px;color:var(--muted);margin-bottom:6px;">
<div>ح</div><div>ن</div><div>ث</div><div>ر</div><div>خ</div><div>ج</div><div>س</div>
</div>
<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;">
<div v-for="d in profileCalendarFirstDay(profileCalendarYear, profileCalendarMonth)" :key="'pad-'+d" style="aspect-ratio:1;"></div>
<div v-for="day in profileCalendarDaysInMonth(profileCalendarYear, profileCalendarMonth)" :key="'d-'+day"
style="aspect-ratio:1;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:13px;cursor:pointer;"
:class="{
'has-media': getProfileMediaCalendarDaysSet(profileMediaModalType).has(profileCalendarYear+'-'+String(profileCalendarMonth+1).padStart(2,'0')+'-'+String(day).padStart(2,'0')),
'selected': false
}"
:style="getProfileMediaCalendarDaysSet(profileMediaModalType).has(profileCalendarYear+'-'+String(profileCalendarMonth+1).padStart(2,'0')+'-'+String(day).padStart(2,'0')) ? 'background:var(--gold);color:#000;font-weight:700;' : ''"
@click="profileMediaCalendarFullOpen = false; profileMediaCalendarOpen = true; profileMediaCalendarDay = {year:profileCalendarYear, month:profileCalendarMonth, day}; profileMediaMenuOpen = false">
@{{ day }}
</div>
</div>
<div style="margin-top:10px;font-size:11px;color:var(--muted);text-align:center;">الأيام المميزة تحتوي على وسائط — اضغط ليوم لعرض وسائطه</div>
</div>
<!-- Day-filtered view (from calendar day click) -->
<div v-if="profileMediaCalendarOpen" style="padding:16px;max-height:60vh;overflow-y:auto;">
<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
<button class="h-icon-btn" @click="profileMediaCalendarOpen = false"><i class="ri-arrow-right-line"></i></button>
<span style="font-size:14px;font-weight:600;color:var(--gold);">@{{ profileMediaCalendarDayLabel }}</span>
</div>
<div style="display:grid;gap:8px;">
<div v-for="m in getProfileMediaByType(profileMediaModalType).filter(item => { const d = new Date(item.createdAt || item.created_at); return !Number.isNaN(d.getTime()) && d.toLocaleDateString('en-CA') === profileMediaCalendarDayStr; })" :key="m.id">
<template v-if="profileMediaModalType==='images'||profileMediaModalType==='videos'">
<div v-if="profileMediaModalType==='images'" style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;background:var(--panel-2);max-width:200px;" @click="openMediaModal(m)">
<img :src="m.attachmentUrl" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
</div>
<div v-if="profileMediaModalType==='videos'" style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;background:var(--panel-2);max-width:200px;position:relative;" @click="openMediaModal(m)">
<video :src="m.attachmentUrl + '#t=0.001'" style="width:100%;height:100%;object-fit:cover;" preload="metadata" muted></video>
<i class="ri-play-circle-fill" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:32px;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,.5);"></i>
</div>
</template>
<template v-if="profileMediaModalType==='audio'">
<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:var(--panel-2);cursor:pointer;" @click="toggleAudioPlayback(m)">
<i :class="m.isPlaying ? 'ri-pause-circle-fill' : 'ri-play-circle-fill'" style="font-size:24px;color:var(--gold);flex-shrink:0;"></i>
<div style="flex:1;min-width:0;">
<div style="font-size:13px;">@{{ m.senderName || 'رسالة صوتية' }}</div>
<div style="font-size:11px;color:var(--muted);">@{{ formatDuration(m.audioDuration || 0) }}</div>
</div>
</div>
</template>
<template v-if="profileMediaModalType==='files'">
<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:var(--panel-2);cursor:pointer;" @click="openMediaModal(m)">
<i class="ri-file-line" style="font-size:18px;color:var(--gold);flex-shrink:0;"></i>
<div style="flex:1;">
<div style="font-size:13px;">@{{ m.attachmentUrl?.split('/')?.pop()?.substring(0,30) || 'ملف' }}</div>
</div>
</div>
</template>
</div>
</div>
<div v-if="getProfileMediaByType(profileMediaModalType).filter(item => { const d = new Date(item.createdAt || item.created_at); return !Number.isNaN(d.getTime()) && d.toLocaleDateString('en-CA') === profileMediaCalendarDayStr; }).length === 0" style="text-align:center;padding:40px;color:var(--muted);font-size:14px;">لا توجد وسائط لهذا اليوم</div>
</div>
</div>
</div>

<!-- Create sticker modal -->
<div class="qr-modal-overlay" v-if="createStickerModalOpen" @click.self="closeCreateStickerModal()">
<div class="qr-modal-card sticker-create-modal">
<div class="qr-header">
<h3>إنشاء ملصق جديد</h3>
<button class="h-icon-btn" @click="closeCreateStickerModal()"><i class="ri-close-line"></i></button>
</div>

<div class="sticker-create-mode-tabs">
<button :class="{active: createStickerMode === 'image'}" @click="createStickerMode = 'image'">من صورة</button>
<button :class="{active: createStickerMode === 'video'}" @click="createStickerMode = 'video'">من فيديو (متحرك)</button>
</div>

<div v-if="createStickerMode === 'image'" class="sticker-create-body">
<label class="sticker-upload-box" v-if="!stickerImageOriginalUrl">
<input type="file" accept="image/*" @change="handleStickerImageFile" hidden>
<i class="ri-upload-cloud-2-line"></i>
<span v-if="!stickerImageProcessing">اضغط لرفع صورة — ستُزال الخلفية تلقائياً</span>
<span v-else><i class="ri-loader-4-line spin"></i> جارٍ إزالة الخلفية...</span>
</label>
<template v-else>
<div class="sticker-create-error" v-if="stickerImageError">@{{ stickerImageError }}</div>
<div class="sticker-choice-row" v-if="!stickerImageProcessing">
<label class="sticker-choice-card" :class="{active: stickerImageChoice === 'removed'}" v-if="stickerImagePreviewUrl">
<input type="radio" value="removed" v-model="stickerImageChoice" hidden>
<img :src="stickerImagePreviewUrl" class="sticker-preview-img">
<span>بدون خلفية</span>
</label>
<label class="sticker-choice-card" :class="{active: stickerImageChoice === 'original'}">
<input type="radio" value="original" v-model="stickerImageChoice" hidden>
<img :src="stickerImageOriginalUrl" class="sticker-preview-img">
<span>الصورة الأصلية</span>
</label>
</div>
<div class="sticker-loading" v-else><i class="ri-loader-4-line spin"></i></div>
</template>
<div class="sticker-create-actions">
<button class="btn-secondary" v-if="stickerImageOriginalUrl" @click="stickerImageOriginalUrl=null; stickerImagePreviewUrl=null; stickerImageBlob=null; stickerImageError=''">اختيار صورة أخرى</button>
<button class="btn-primary" :disabled="!stickerImageOriginalUrl || stickerImageProcessing" @click="confirmSaveImageSticker">حفظ الملصق</button>
</div>
</div>

<div v-if="createStickerMode === 'video'" class="sticker-create-body">
<label class="sticker-upload-box" v-if="!stickerVideoUrl">
<input type="file" accept="video/*" @change="handleStickerVideoFile" hidden>
<i class="ri-upload-cloud-2-line"></i>
<span>اضغط لرفع فيديو قصير (سيتم اقتطاع 3 ثوانٍ كحد أقصى)</span>
</label>
<div class="sticker-video-trim-wrap" v-else>
<video ref="stickerTrimVideo" :src="stickerVideoUrl" class="sticker-trim-video" muted playsinline></video>
<div class="sticker-trim-row">
<label>البداية: @{{ videoTrimStart.toFixed(1) }}ث</label>
<input type="range" min="0" :max="stickerVideoDuration" step="0.1" v-model.number="videoTrimStart">
</div>
<div class="sticker-trim-row">
<label>النهاية: @{{ videoTrimEnd.toFixed(1) }}ث</label>
<input type="range" min="0" :max="stickerVideoDuration" step="0.1" v-model.number="videoTrimEnd">
</div>
<div class="sticker-trim-hint">المدة القصوى المسموحة: 3 ثوانٍ</div>
</div>
<div class="sticker-create-error" v-if="stickerVideoError">@{{ stickerVideoError }}</div>
<div class="sticker-create-actions">
<button class="btn-secondary" v-if="stickerVideoUrl" @click="stickerVideoUrl=null; stickerVideoFile=null">اختيار فيديو آخر</button>
<button class="btn-primary" :disabled="!stickerVideoUrl || stickerVideoProcessing" @click="confirmVideoTrim">
<i v-if="stickerVideoProcessing" class="ri-loader-4-line spin"></i> حفظ الملصق المتحرك
</button>
</div>
</div>
</div>
</div>

<!-- QR code modal -->
<div class="qr-modal-overlay" v-if="qrModalOpen" @click.self="qrModalOpen=false">
<div class="qr-modal-card qr-modal-card--v2">
<div class="qr-header">
<h3>الحصول على رمز QR</h3>
<button class="h-icon-btn" @click="qrModalOpen=false"><i class="ri-close-line"></i></button>
</div>

<div class="qr-canvas-wrap">
<canvas ref="qrComposedCanvas" class="qr-composed-canvas"></canvas>
</div>

<div class="qr-section-label">اختر الخلفية</div>
<div class="qr-bg-options">
<div v-for="(bg, i) in qrBgOptions" :key="i"
class="qr-bg-option" :class="{active: qrBgIndex===i}"
@click="qrBgIndex=i; renderComposedQr()">
<div class="qr-opt-swatch" :style="{background: bg}">
  <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="width:60%;height:60%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);opacity:0.45;" fill="rgba(255,255,255,0.9)">
    <rect x="1" y="1" width="7" height="7"/><rect x="12" y="1" width="7" height="7"/><rect x="1" y="12" width="7" height="7"/>
    <rect x="3" y="3" width="3" height="3" fill="rgba(0,0,0,0.4)"/><rect x="14" y="3" width="3" height="3" fill="rgba(0,0,0,0.4)"/><rect x="3" y="14" width="3" height="3" fill="rgba(0,0,0,0.4)"/>
    <rect x="12" y="12" width="2" height="2"/><rect x="15" y="12" width="2" height="2"/><rect x="12" y="15" width="2" height="2"/><rect x="15" y="15" width="2" height="2"/>
    <rect x="10" y="5" width="1" height="2"/><rect x="10" y="8" width="2" height="1"/><rect x="5" y="10" width="1" height="2"/><rect x="8" y="10" width="2" height="1"/>
  </svg>
</div>
</div>
</div>

<div class="qr-section-label">الجودة</div>
<div class="qr-quality-row">
<span :class="{active: qrQualityIndex===0}">عادية</span>
<input type="range" min="0" max="2" step="1" v-model.number="qrQualityIndex" @input="renderComposedQr" class="qr-slider-plain">
<span :class="{active: qrQualityIndex===1}">عالية</span>
<span :class="{active: qrQualityIndex===2}">عالية جداً</span>
</div>

<div class="qr-section-label">حجم الخط</div>
<input type="range" min="11" max="22" v-model.number="qrFontSize" @input="renderComposedQr" class="qr-slider-plain">

<div class="qr-toggle-row">
<span>الصورة الشخصية</span>
<button class="sp-toggle" :class="{on: qrShowProfilePhoto}" @click="qrShowProfilePhoto=!qrShowProfilePhoto; renderComposedQr()"></button>
</div>
<div class="qr-toggle-row">
<span>خلفية شفافة</span>
<button class="sp-toggle" :class="{on: qrTransparentBg}" @click="qrTransparentBg=!qrTransparentBg; renderComposedQr()"></button>
</div>

<div style="display:flex;gap:8px;">
<button class="qr-btn primary" style="flex:1;" @click="copyComposedQr"><i class="ri-file-copy-line"></i> نسخ</button>
<button class="qr-btn secondary" style="flex:1;" @click="downloadComposedQr"><i class="ri-download-2-line"></i> تحميل</button>
</div>
</div>
</div>

<!-- ════ Contacts Modal ════ -->
<div class="calls-modal-overlay" v-if="contactsOpen" @click.self="contactsOpen = false">
<div class="calls-modal-card" style="max-width:480px;">
<div class="calls-header">
<div class="calls-header-left">
<h3><i class="ri-contacts-line"></i> جهات الاتصال</h3>
</div>
<button class="h-icon-btn" @click="contactsOpen = false"><i class="ri-close-line"></i></button>
</div>
<div class="new-call-search-wrap">
<i class="ri-search-line" style="color:var(--muted);font-size:16px;"></i>
<input class="new-call-search" v-model="contactsQuery" placeholder="ابحث بالاسم أو رقم الهاتف..." autofocus>
</div>
<div class="calls-list">
<template v-for="c in filteredContactsForContacts" :key="c.id">
<div class="new-call-contact" @click="selectContact(c); contactsOpen = false">
<div class="new-call-contact-avatar">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="" v-on:error="handleAvatarError($event, c)">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
</div>
<div class="new-call-contact-info">
<div class="new-call-contact-name">@{{ c.name }}</div>
<div class="call-card-meta">
<i class="ri-phone-line" style="font-size:11px;"></i>
<span>@{{ c.phone || c.username || '—' }}</span>
<span style="margin-right:auto;font-size:11px;color:var(--muted);">@{{ formatContactLastSeen(c) }}</span>
</div>
</div>
</div>
</template>
<div v-if="!filteredContactsForContacts.length" class="calls-empty">
<i class="ri-contacts-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
@{{ contactsQuery ? 'لا توجد نتائج' : 'لا توجد جهات اتصال' }}
</div>
</div>
<div class="new-call-actions">
<button class="sub-btn-cancel" @click="contactsOpen = false">إغلاق</button>
</div>
</div>
</div>

<!-- ════ Calls Modal ════ -->
<div class="calls-modal-overlay" v-if="callsOpen" @click.self="closeCalls">
<div class="calls-modal-card">
<div class="calls-header">
<div class="calls-header-left">
<h3><i class="ri-phone-line"></i> المكالمات</h3>
</div>
<div class="calls-header-right" style="position:relative;">
<button class="h-icon-btn" @click.stop="callsMenuOpen = !callsMenuOpen"><i class="ri-more-2-fill"></i></button>
<div v-if="callsMenuOpen" class="calls-three-dot-menu" @click.stop>
<div class="calls-menu-item" @click="callsMenuOpen = false; callsSettingsOpen = true"><i class="ri-settings-3-line"></i> إعدادات المكالمات</div>
<div class="calls-menu-item" @click="toggleCallsSelectMode"><i class="ri-delete-bin-line"></i> @{{ callsSelectMode ? 'إلغاء التحديد' : 'تحديد' }}</div>
<div class="calls-menu-item danger" @click="callsMenuOpen = false; callsDeleteConfirm = true"><i class="ri-delete-bin-7-line"></i> حذف التاريخ</div>
</div>
<button class="h-icon-btn" @click="closeCalls"><i class="ri-close-line"></i></button>
</div>
</div>
<div class="calls-start-btn" @click="openNewCallModal">
<i class="ri-link" style="font-size:16px;"></i>
<span>بدء مكالمة جديدة</span>
</div>
<div class="calls-tabs">
<button :class="{active: callsTab === 'all'}" @click="callsTab = 'all'">الكل</button>
<button :class="{active: callsTab === 'missed'}" @click="callsTab = 'missed'">الفائتة</button>
</div>
<div class="calls-list" ref="callsList" @scroll="onCallsScroll">
<div class="calls-select-bar" v-if="callsSelectMode">
<button class="calls-select-all" @click="toggleSelectAllCalls"><i :class="callsSelectedDelete.length === displayedCalls.length ? 'ri-checkbox-line' : 'ri-checkbox-blank-line'"></i> تحديد الكل</button>
<button class="calls-delete-selected" :disabled="!callsSelectedDelete.length" @click="deleteSelectedCalls"><i class="ri-delete-bin-line"></i> حذف</button>
</div>
<template v-for="call in displayedCalls" :key="call.id">
<div class="call-card" @click="navigateToCallMessage(call)">
<div class="call-card-avatar" style="position:relative;flex-shrink:0;">
<img v-if="call.contactAvatar" :src="normalizeAvatarUrl(call.contactAvatar)" alt="" v-on:error="handleAvatarError($event, {avatar_url: call.contactAvatar})">
<span v-else>@{{ getAuthorInitial(call.contactName) }}</span>
<span class="call-attempt-badge" v-if="call.attempts > 1">@{{ '(' + call.attempts + ')' }}</span>
</div>
<div class="call-card-body">
<div class="call-card-name">@{{ call.contactName }}</div>
<div class="call-card-meta">
<i :class="call.direction === 'outgoing' ? 'ri-arrow-right-up-line' : 'ri-arrow-left-down-line'"
:style="{color: call.status === 'missed' ? '#e74c3c' : 'var(--muted)', fontSize: '14px'}"></i>
<span>@{{ formatCallTime(call.timestamp) }}</span>
</div>
</div>
<div class="call-card-type" @click.stop="startCallFromLog(call)" style="flex-shrink:0;">
<i :class="call.type === 'video' ? 'ri-vidicon-line' : 'ri-phone-line'" style="font-size:20px;color:var(--gold);cursor:pointer;"></i>
</div>
<div class="call-card-select" v-if="callsSelectMode" @click.stop="toggleCallSelect(call.id)">
<i :class="callsSelectedDelete.includes(call.id) ? 'ri-checkbox-circle-fill' : 'ri-checkbox-blank-circle-line'"
:style="{color: callsSelectedDelete.includes(call.id) ? 'var(--gold)' : 'var(--muted)', fontSize:'20px'}"></i>
</div>
</div>
</template>
<div v-if="!displayedCalls.length" class="calls-empty">
<i class="ri-phone-line" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i>
<span>@{{ callsTab === 'missed' ? 'لا توجد مكالمات فائتة' : 'لا توجد مكالمات' }}</span>
</div>
</div>
</div>
</div>

<!-- ════ New Call Modal ════ -->
<div class="calls-modal-overlay" v-if="newCallModalOpen" @click.self="closeNewCallModal">
<div class="calls-modal-card" style="max-width:480px;">
<div class="calls-header">
<h3><i class="ri-phone-line"></i> بدء مكالمة</h3>
<button class="h-icon-btn" @click="closeNewCallModal"><i class="ri-close-line"></i></button>
</div>
<div class="new-call-search-wrap">
<i class="ri-search-line" style="color:var(--muted);font-size:16px;"></i>
<input class="new-call-search" v-model="newCallQuery" placeholder="ابحث بالاسم أو اسم المستخدم..." @keydown.esc="closeNewCallModal">
</div>
<div class="new-call-chips" v-if="newCallSelected.length">
<div class="new-call-chip" v-for="c in newCallSelectedContacts" :key="c.id" @click="toggleNewCallSelect(c.id)">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
<span>@{{ c.name }}</span>
<i class="ri-close-line"></i>
</div>
</div>
<div class="new-call-list">
<div v-for="c in filteredNewCallContacts" :key="c.id" class="new-call-contact" :class="{selected: newCallSelected.includes(c.id)}" @click="toggleNewCallSelect(c.id)">
<div class="new-call-contact-avatar">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="" v-on:error="handleAvatarError($event, c)">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
</div>
<div class="new-call-contact-info">
<div class="new-call-contact-name">@{{ c.name }}</div>
<div class="new-call-contact-username" v-if="c.username" style="font-size:11px;color:var(--muted);">@{{ '@' + c.username }}</div>
</div>
<i v-if="newCallSelected.includes(c.id)" class="ri-checkbox-circle-fill" style="color:var(--gold);font-size:20px;margin-right:auto;"></i>
<i v-else class="ri-checkbox-blank-circle-line" style="color:var(--muted);font-size:20px;margin-right:auto;"></i>
</div>
<div v-if="!filteredNewCallContacts.length" class="calls-empty" style="padding:40px;">لا توجد نتائج</div>
</div>
<div class="new-call-actions">
<button class="sub-btn-cancel" @click="closeNewCallModal">إلغاء</button>
<button class="sub-btn-primary" :disabled="!newCallSelected.length" @click="startNewCallFromLog"><i class="ri-phone-line"></i> اتصال @{{ newCallSelected.length ? '('+newCallSelected.length+')' : '' }}</button>
</div>
</div>
</div>

<!-- ════ Calls Delete Confirm ════ -->
<div class="profile-sub-overlay calls-sub-overlay" v-if="callsDeleteConfirm" @click.self="callsDeleteConfirm = false">
<div class="profile-sub-card">
<h3 style="color:#e74c3c;"><i class="ri-delete-bin-7-line"></i> حذف سجل المكالمات</h3>
<div class="block-warning-text" style="margin:16px 0;">سيتم حذف سجل المكالمات بالكامل. هذا الإجراء لا يمكن التراجع عنه.</div>
<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="callsDeleteConfirm = false">إلغاء</button>
<button class="sub-btn-primary" style="background:#e74c3c;" @click="deleteAllCalls"><i class="ri-delete-bin-line"></i> حذف الكل</button>
</div>
</div>
</div>

<!-- ════ Calls Settings Modal ════ -->
<div class="profile-sub-overlay calls-sub-overlay" v-if="callsSettingsOpen" @click.self="callsSettingsOpen = false">
<div class="profile-sub-card" style="max-width:440px;text-align:right;" @vue:mounted="loadCallSettingsDevices">
<h3><i class="ri-settings-3-line"></i> إعدادات المكالمات</h3>

<div class="sp-info-card" style="margin-top:14px;">
<div class="sp-info-row">
<i class="ri-notification-3-line sp-info-icon"></i>
<span style="flex:1;font-size:14px;color:var(--text);">نغمة الرنين</span>
<select v-model="callSettings.ringtone" @change="saveCallSettings" style="min-width:120px;">
<option v-for="t in toneList" :key="t.id" :value="t.id">@{{ t.label }}</option>
</select>
</div>
<div class="sp-info-row">
<i class="ri-mic-line sp-info-icon"></i>
<span style="flex:1;font-size:14px;color:var(--text);">الميكروفون</span>
<button v-if="micPermissionState !== 'granted'" class="sub-btn-cancel" style="padding:6px 12px;font-size:12px;" @click="requestCallMediaPermission('microphone')">
@{{ micPermissionState === 'denied' ? 'غير مسموح — اضغط للسماح' : 'طلب الإذن' }}
</button>
<select v-else v-model="callSettings.micDeviceId" @change="saveCallSettings" style="min-width:140px;max-width:160px;">
<option v-for="d in callAudioInputs" :key="d.deviceId" :value="d.deviceId">@{{ d.label || 'ميكروفون' }}</option>
</select>
</div>
<div class="sp-info-row">
<i class="ri-camera-line sp-info-icon"></i>
<span style="flex:1;font-size:14px;color:var(--text);">الكاميرا</span>
<button v-if="cameraPermissionState !== 'granted'" class="sub-btn-cancel" style="padding:6px 12px;font-size:12px;" @click="requestCallMediaPermission('camera')">
@{{ cameraPermissionState === 'denied' ? 'غير مسموح — اضغط للسماح' : 'طلب الإذن' }}
</button>
<select v-else v-model="callSettings.cameraDeviceId" @change="saveCallSettings" style="min-width:140px;max-width:160px;">
<option v-for="d in callVideoInputs" :key="d.deviceId" :value="d.deviceId">@{{ d.label || 'كاميرا' }}</option>
</select>
</div>
<div class="sp-info-row" v-if="callAudioOutputs.length">
<i class="ri-volume-up-line sp-info-icon"></i>
<span style="flex:1;font-size:14px;color:var(--text);">مكبر الصوت</span>
<select v-model="callSettings.speakerDeviceId" @change="saveCallSettings" style="min-width:140px;max-width:160px;">
<option v-for="d in callAudioOutputs" :key="d.deviceId" :value="d.deviceId">@{{ d.label || 'مكبر صوت' }}</option>
</select>
</div>
</div>

<div class="sp-info-card" style="margin-top:10px;">
<div class="sp-info-row">
<i class="ri-wifi-line sp-info-icon"></i>
<div style="flex:1;">
<div style="font-size:14px;color:var(--text);">وضع البيانات المنخفضة</div>
<div style="font-size:12px;color:var(--muted);margin-top:2px;">تقليل جودة الفيديو لتوفير الإنترنت</div>
</div>
<button class="sp-toggle" :class="{on: callSettings.lowDataMode}" @click="callSettings.lowDataMode = !callSettings.lowDataMode; saveCallSettings()"></button>
</div>
<div class="sp-info-row">
<i class="ri-notification-off-line sp-info-icon"></i>
<div style="flex:1;">
<div style="font-size:14px;color:var(--text);">عدم الإزعاج</div>
<div style="font-size:12px;color:var(--muted);margin-top:2px;">رفض كل المكالمات الواردة تلقائياً</div>
</div>
<button class="sp-toggle" :class="{on: callSettings.doNotDisturb}" @click="callSettings.doNotDisturb = !callSettings.doNotDisturb; saveCallSettings()"></button>
</div>
</div>

<div class="profile-sub-actions">
<button class="sub-btn-cancel" @click="callsSettingsOpen = false">إغلاق</button>
</div>
</div>
</div>

<!-- Call overlay -->

<div class="call-overlay" v-show="callState === 'calling' || callState === 'in-call'" :class="{ 'is-active': !!callState }" :data-call-state="callState">

<div class="call-participants-grid" :class="'count-' + Math.min(callParticipants.length, 4)" v-if="callState === 'in-call' && callType === 'video'">
<div class="call-participant-tile" v-for="p in callParticipants" :key="p.id">
<video :ref="'remoteMedia_' + p.id" autoplay playsinline></video>
<div class="call-participant-label">@{{ p.name }}</div>
</div>
</div>
<audio v-for="p in callParticipants" :key="'a'+p.id" :ref="'remoteMedia_' + p.id" autoplay v-if="callState === 'in-call' && callType !== 'video'"></audio>

<video ref="localVideo" autoplay playsinline muted style="position:absolute;bottom:100px;left:16px;width:96px;height:130px;object-fit:cover;border-radius:12px;z-index:2;box-shadow:0 8px 24px rgba(0,0,0,.4);" v-show="callType === 'video' && callState === 'in-call' && !cameraOff"></video>

<div class="call-avatar" v-show="!(callType === 'video' && callState === 'in-call')">

<img v-if="callContact && callContact.avatar_url" :src="callContact.avatar_url" alt="" v-on:error="handleAvatarError($event, callContact)">

<span v-else>@{{ callContact ? getAuthorInitial(callContact.name) : '?' }}</span>

</div>

<div class="call-name">
@{{ isGroupCall ? (callParticipants.map(p => p.name).join('، ') || callContact?.name) : (callContact ? callContact.name : '') }}
</div>

<div class="call-status">

<span v-if="callState === 'calling'">@{{ getCallStatusText() }}</span>

<span v-else-if="callState === 'in-call'">@{{ formatCallElapsed() }}</span>

</div>

<div class="call-actions">

<button class="call-btn mute" :class="{active: callMuted}" @click="toggleMute" title="كتم الصوت"><i :class="callMuted ? 'ri-mic-off-line' : 'ri-mic-line'"></i></button>

<button class="call-btn speaker" :class="{active: speakerMuted}" @click="toggleSpeaker" title="السماعة"><i :class="speakerMuted ? 'ri-volume-mute-line' : 'ri-volume-up-line'"></i></button>

<button class="call-btn end" @click="endCall" title="إنهاء"><i class="ri-phone-fill" style="transform:rotate(135deg)"></i></button>

<button class="call-btn cam" :class="{active: cameraOff}" @click="toggleCamera" title="الكاميرا" v-if="callType === 'video'"><i :class="cameraOff ? 'ri-camera-off-line' : 'ri-camera-line'"></i></button>

<button class="call-btn switch-cam" @click="switchCamera" title="تبديل الكاميرا" v-if="callType === 'video' && !cameraOff"><i class="ri-camera-switch-line"></i></button>

<button class="call-btn add-participant" @click="openAddParticipant" title="إضافة مشارك" v-if="callState === 'in-call' && callParticipants.length < 5"><i class="ri-user-add-line"></i></button>

</div>

</div>

<!-- Incoming call: small closable floating widget (Google Meet style) -->
<div class="incoming-call-widget" v-if="callState === 'incoming'">
<button class="incoming-call-close" @click="rejectIncomingCall" title="إغلاق"><i class="ri-close-line"></i></button>
<div class="incoming-call-avatar">
<img v-if="callContact && callContact.avatar_url" :src="callContact.avatar_url" alt="" v-on:error="handleAvatarError($event, callContact)">
<span v-else>@{{ callContact ? getAuthorInitial(callContact.name) : '?' }}</span>
</div>
<div class="incoming-call-info">
<div class="incoming-call-name">@{{ callContact ? callContact.name : '' }}</div>
<div class="incoming-call-status">@{{ (isGroupCall ? 'مكالمة جماعية ' : '') + (callType === 'video' ? 'مكالمة فيديو واردة' : 'مكالمة صوتية واردة') }}</div>
</div>
<div class="incoming-call-actions">
<button class="call-btn end" @click="rejectIncomingCall" title="رفض"><i class="ri-phone-fill" style="transform:rotate(135deg)"></i></button>
<button class="call-btn end" style="background:#34C759;" @click="answerIncomingCall" title="قبول"><i class="ri-phone-fill"></i></button>
</div>
</div>

<!-- Add participant modal -->
<div class="calls-modal-overlay" v-if="showAddParticipant" @click.self="showAddParticipant = false">
<div class="calls-modal-card" style="max-width:420px;">
<div class="calls-header">
<h3><i class="ri-user-add-line"></i> إضافة مشارك</h3>
<button class="h-icon-btn" @click="showAddParticipant = false"><i class="ri-close-line"></i></button>
</div>
<div class="new-call-search-wrap">
<i class="ri-search-line" style="color:var(--muted);font-size:16px;"></i>
<input class="new-call-search" v-model="addParticipantQuery" placeholder="ابحث بالاسم...">
</div>
<div class="calls-list" style="max-height:320px;">
<template v-for="c in (contacts || []).filter(c => Number(c.id) !== -1 && !callParticipants.some(p => Number(p.id) === Number(c.id)) && (!addParticipantQuery || c.name.toLowerCase().includes(addParticipantQuery.toLowerCase())))" :key="c.id">
<div class="call-card" @click="addParticipantToCall(c)">
<div class="call-card-avatar">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
</div>
<div class="call-card-body">
<div class="call-card-name">@{{ c.name }}</div>
</div>
</div>
</template>
</div>
</div>
</div>

</div>

</div>

<script type="importmap">
{
    "imports": {
        "onnxruntime-web": "https://cdn.jsdelivr.net/npm/onnxruntime-web@1.21.0-dev.20250206-d981b153d3/dist/ort.bundle.min.mjs",
        "onnxruntime-web/webgpu": "https://cdn.jsdelivr.net/npm/onnxruntime-web@1.21.0-dev.20250206-d981b153d3/dist/ort.webgpu.bundle.min.mjs"
    }
}
</script>
<script src="/js/vue.global.prod.js"></script>
<script src="/js/pusher.min.js"></script>
<script src="/js/echo.iife.js"></script>

<script>
</script>

<script>
try {
window.Echo = new Echo.default({
    broadcaster: 'reverb',
    key: @json(config('broadcasting.connections.reverb.key')),
    wsHost: @json(config('broadcasting.connections.reverb.options.host')),
    wsPort: @json((int) config('broadcasting.connections.reverb.options.port')),
    wssPort: @json((int) config('broadcasting.connections.reverb.options.port')),
    forceTLS: @json(config('broadcasting.connections.reverb.options.scheme') === 'https'),
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    },
});
} catch (e) { window.Echo = null; console.warn('[Echo] WebSocket init failed:', e); }

const { createApp } = Vue;

const safeLocalJson = (key, fallback) => {
try {
const raw = localStorage.getItem(key);
if (!raw) return fallback;
const parsed = JSON.parse(raw);
return parsed && typeof parsed === 'object' ? parsed : fallback;
} catch (_) {
localStorage.removeItem(key);
return fallback;
}
};

createApp({

data() {

return {

t: {

title: '\u0627\u0644\u0645\u0631\u0627\u0633\u0644\u0629',

search: '\u0627\u0628\u062d\u062b \u0641\u064a \u062c\u0647\u0627\u062a \u0627\u0644\u0627\u062a\u0635\u0627\u0644',

noMessages: '\u0644\u0627 \u062a\u0648\u062c\u062f \u0631\u0633\u0627\u0626\u0644',

noChats: '\u0644\u0627 \u062a\u0648\u062c\u062f \u0645\u062d\u0627\u062f\u062b\u0627\u062a',

onlineNow: '\u0646\u0634\u0637 \u0627\u0644\u0622\u0646',

lastSeen: '\u0622\u062e\u0631 \u0638\u0647\u0648\u0631',

unavailable: '\u063a\u064a\u0631 \u0645\u062a\u0627\u062d',

reply: '\u0631\u062f',

edit: '\u062a\u0639\u062f\u064a\u0644',

delete: '\u062d\u0630\u0641',

previousMessage: '\u0631\u0633\u0627\u0644\u0629 \u0633\u0627\u0628\u0642\u0629',

attachment: '\u0645\u0631\u0641\u0642',

download: '\u062a\u0646\u0632\u064a\u0644',

retry: 'إعادة المحاولة',

sendFailed: 'تعذر إرسال الرسالة. اضغط لإعادة المحاولة',

uploadFailed: 'تعذر رفع المرفق. اضغط لإعادة المحاولة',

rateLimited: 'أنت ترسل بسرعة كبيرة، انتظر لحظة ثم أعد المحاولة',

retryUnavailable: 'تعذر إعادة الإرسال تلقائياً، أعد كتابة الرسالة',

edited: '\u062a\u0645 \u0627\u0644\u062a\u0639\u062f\u064a\u0644',

startChatPrompt: '\u0627\u0628\u062f\u0623 \u0627\u0644\u0645\u062d\u0627\u062f\u062b\u0629 \u0627\u0644\u0622\u0646',

replyTo: '\u0631\u062f \u0639\u0644\u0649',

you: '\u0623\u0646\u062a',

attachments: '\u0627\u0644\u0645\u0631\u0641\u0642\u0627\u062a',

writeMessage: '\u0627\u0643\u062a\u0628 \u0631\u0633\u0627\u0644\u0629...',

emoji: '\u0625\u064a\u0645\u0648\u062c\u064a',

attach: '\u0625\u0631\u0641\u0627\u0642',

voice: '\u0631\u0633\u0627\u0644\u0629 \u0635\u0648\u062a\u064a\u0629',

selectChat: '\u0627\u062e\u062a\u0631 \u0645\u062d\u0627\u062f\u062b\u0629 \u0645\u0646 \u0627\u0644\u0642\u0627\u0626\u0645\u0629',

jumpToLatest: '\u0627\u0644\u0639\u0648\u062f\u0629 \u0644\u0622\u062e\u0631 \u0631\u0633\u0627\u0644\u0629',

sendNow: '\u0625\u0631\u0633\u0627\u0644',

recordingNow: '\u062a\u0633\u062c\u064a\u0644 \u062c\u0627\u0631\u064d',

slideToLock: '\u0627\u0633\u062d\u0628 \u0644\u0623\u0639\u0644\u0649 \u0644\u0644\u0642\u0641\u0644',

lockedRecording: '\u062a\u0633\u062c\u064a\u0644 \u0645\u0642\u0641\u0644',

paused: '\u0645\u062a\u0648\u0642\u0641',

pause: '\u0625\u064a\u0642\u0627\u0641',

resume: '\u0627\u0633\u062a\u0626\u0646\u0627\u0641',

holdToRecord: '\u0627\u0636\u063a\u0637 \u0645\u0637\u0648\u0644\u0627 \u0644\u0644\u062a\u0633\u062c\u064a\u0644\u060c \u0627\u0633\u062d\u0628 \u0644\u0623\u0639\u0644\u0649 \u0644\u0644\u0642\u0641\u0644',

unreadMessages: '\u0631\u0633\u0627\u0626\u0644 \u063a\u064a\u0631 \u0645\u0642\u0631\u0648\u0621\u0629',

deleteConfirm: '\u0647\u0644 \u0623\u0646\u062a \u0645\u062a\u0623\u0643\u062f \u0645\u0646 \u062d\u0630\u0641 \u0647\u0630\u0647 \u0627\u0644\u0631\u0633\u0627\u0644\u0629\u061f',

cancel: '\u0625\u0644\u063a\u0627\u0621',

save: '\u062d\u0641\u0638',

editPrompt: '\u0639\u062f\u0644 \u0627\u0644\u0631\u0633\u0627\u0644\u0629 \u0628\u0634\u0643\u0644 \u0623\u0648\u0636\u062d'

,

newConversation: '\u062f\u0631\u062f\u0634\u0629 \u062c\u062f\u064a\u062f\u0629',

chooseConversationType: '\u0627\u062e\u062a\u0631 \u0646\u0648\u0639 \u0627\u0644\u062f\u0631\u062f\u0634\u0629',

startChat: '\u062f\u0631\u062f\u0634\u0629 \u0641\u0631\u062f\u064a\u0629',

startGroup: '\u0642\u0631\u0648\u0628 \u062c\u062f\u064a\u062f',

pickPersonForChat: '\u0627\u062e\u062a\u0631 \u0634\u062e\u0635\u0627\u064b \u0644\u0628\u062f\u0621 \u0645\u062d\u0627\u062f\u062b\u0629',

pickPeopleForGroup: '\u0627\u062e\u062a\u0631 \u0623\u0634\u062e\u0627\u0635\u0627\u064b \u0644\u0642\u0631\u0648\u0628 \u062c\u062f\u064a\u062f',

noNewContacts: '\u0644\u0627 \u064a\u0648\u062c\u062f \u0623\u0634\u062e\u0627\u0635 \u062c\u062f\u062f \u062d\u0627\u0644\u064a\u0627\u064b',

createGroup: '\u0625\u0646\u0634\u0627\u0621 \u0627\u0644\u0642\u0631\u0648\u0628',

back: '\u0631\u062c\u0648\u0639',

next: '\u0627\u0644\u062a\u0627\u0644\u064a',

groupSetupHint: '\u0623\u062f\u062e\u0644 \u0628\u064a\u0627\u0646\u0627\u062a \u0627\u0644\u0642\u0631\u0648\u0628',

groupNamePlaceholder: '\u0627\u0633\u0645 \u0627\u0644\u0642\u0631\u0648\u0628',

groupPhotoOptional: '\u0635\u0648\u0631\u0629 \u0627\u0644\u0642\u0631\u0648\u0628 (\u0627\u062e\u062a\u064a\u0627\u0631\u064a)'

},

contacts: [],

messages: [],

selectedContact: null,

searchQuery: '',

messageInput: '',

textareaHeight: 44,

showSidebar: window.innerWidth > 1080,

showEmojiPicker: false,

emojiPickerPinned: false,

emojiHoverTimer: null,

pickerActiveTab: 'emoji',
emojiCategoriesData: [],
emojiCategoriesLoaded: false,
emojiActiveCatId: 'smileys',
emojiSearchQuery: '',
emojiRecentList: JSON.parse(localStorage.getItem('emoji_recent_v1') || '[]'),
gifSearchQuery: '',
gifResults: [],
gifLoading: false,
gifSearchTimer: null,
gifSearchedOnce: false,
stickerList: [],
stickerFavorites: [],
stickerRecent: [],
stickersLoaded: false,
stickersLoading: false,
createStickerModalOpen: false,
createStickerMode: 'image',
stickerImageFile: null,
stickerImageBlob: null,
stickerImagePreviewUrl: null,
stickerImageOriginalUrl: null,
stickerImageOriginalBlob: null,
stickerImageChoice: 'removed',
stickerImageProcessing: false,
stickerImageError: '',
stickerVideoFile: null,
stickerVideoUrl: null,
stickerVideoDuration: 0,
videoTrimStart: 0,
videoTrimEnd: 3,
stickerVideoProcessing: false,
stickerVideoError: '',

emojiList: [

'&#128512;','&#128513;','&#128514;','&#128516;','&#128525;','&#128526;','&#129392;','&#129303;',

'&#128077;','&#128079;','&#128293;','&#10084;&#65039;','&#128155;','&#128153;','&#128591;','&#128588;',

'&#127881;','&#127775;','&#10024;','&#128640;','&#127919;','&#128170;','&#128161;','&#128064;',

'&#129312;','&#128515;','&#128521;','&#128578;','&#128579;','&#128557;','&#128546;','&#128545;'

],

csrfToken: document.querySelector('meta[name="csrf-token"]').content,

currentUserAvatar: @json(Auth::user()->avatar_url),

currentUserRole: @json(Auth::user()->role),

currentUserPhone: @json(Auth::user()->phone),

savedListRoute: @json($savedListRoute),

savedIdsRoute: @json($savedIdsRoute),

saveRouteTemplate: @json($saveRoute),

wallpaperSetRoute: @json($wallpaperSetRoute),

wallpaperGetRoute: @json($wallpaperGetRoute),

statusUpdateRoute: @json($statusUpdateRoute),

statusReplyRoute: @json($statusReplyRoute),

statusReactionRoute: @json($statusReactionRoute),

pinRoute: @json($pinRoute),

forwardRoute: @json($forwardRoute),

settingsGetRoute: @json($settingsGetRoute),

settingsSaveRoute: @json($settingsSaveRoute),

settingsExtraRoutes: @json($settingsExtraRoutes),

typingRoute: @json($typingRoute),

unsaveRouteTemplate: @json($unsaveRoute),

currentUserId: {{ Auth::id() }},

mediaRecorder: null,

recordedChunks: [],

isRecording: false,

isRecordingLocked: false,

isRecordingPaused: false,

recordingStartTime: null,

recordingAccumulatedMs: 0,

recordingDurationSec: 0,

recordingTimer: null,

holdStartY: null,

holdTimer: null,

holdActive: false,

recordStopMode: null,

recordingStream: null,

pendingAttachments: [],

pendingAttachmentsSensitive: false,

revealedSensitiveIds: new Set(),

replyingToMessage: null,

replySwipeStart: 0,
replySwipeY: 0,
replySwiping: false,

showJumpToLatest: false,

pinnedListOpen: false,
stickerViewer: null,
stickerSaveBusy: false,

e2eInfoDismissed: localStorage.getItem('e2e_info_dismissed') === '1',
e2eEnabled: false,
e2ePartnerHasKey: false,
mediaModal: null,
mediaModalList: [],
mediaModalMoreOpen: false,
mediaDurationCache: {},
mediaImgZoom: 1,
mediaImgPanX: 0,
mediaImgPanY: 0,
mediaIsPanning: false,
mediaPanStartX: 0,
mediaPanStartY: 0,
mediaPanImgStartX: 0,
mediaPanImgStartY: 0,
mediaZoomTransition: false,
mediaPinchDist: 0,
mediaPinchZoomStart: 1,

highlightedMessageId: null,

highlightTimer: null,

activeAudioMessageId: null,

activeAudioElement: null,

audioPlayers: {},

isWaveSeeking: false,

_waveDragRatio: null,
_waveDragMessageId: null,

waveSeekingMessageId: null,

waveSeekWasPlaying: false,

isVoicePlayerSeeking: false,

activeVideoElement: null,

unreadReadIds: [],

unreadRemainingCount: 0,

initialUnreadSnapshot: 0,

showRecordHint: false,

recordHintTimer: null,

deleteTargetMessage: null,

editTargetMessage: null,

editInputText: '',

forwardPickerOpen: false,

forwardSourceMessage: null,

forwardSelection: [],

floatingDayLabel: '',

floatingDayTimer: null,

floatingDayIndex: -1,

isFeedScrolling: false,

scrollEndTimer: null,

lastFeedScrollTop: 0,

refreshTimer: null,

pingTimer: null,

resizeHandler: null,

globalClickHandler: null,

pendingMessageCounter: -1,

sendQueue: Promise.resolve(),

updateRouteTemplate: @json($updateRouteTemplate),

deleteRouteTemplate: @json($deleteRouteTemplate),

audioPositionRoute: @json($audioPositionRoute),

qrRouteTemplate: @json($qrRouteTemplate),

callsInitiateRoute: @json($callsInitiateRoute),
callsAnswerRouteTemplate: @json($callsAnswerRouteTemplate),
callsRejectRouteTemplate: @json($callsRejectRouteTemplate),
callsRingRouteTemplate: @json($callsRingRouteTemplate),
callsEndRouteTemplate: @json($callsEndRouteTemplate),
callsIceRouteTemplate: @json($callsIceRouteTemplate),
callsOfferRouteTemplate: @json($callsOfferRouteTemplate),
callsPeerOfferRouteTemplate: @json($callsPeerOfferRouteTemplate),
callsPeerAnswerRouteTemplate: @json($callsPeerAnswerRouteTemplate),
callsJoinRouteTemplate: @json($callsJoinRouteTemplate),
callsInviteRouteTemplate: @json($callsInviteRouteTemplate),

stickersIndexRoute: @json($stickersIndexRoute),
stickersStoreRoute: @json($stickersStoreRoute),
stickersFavoriteRouteTemplate: @json($stickersFavoriteRouteTemplate),
stickersDestroyRouteTemplate: @json($stickersDestroyRouteTemplate),
stickersUsedRouteTemplate: @json($stickersUsedRouteTemplate),
gifsSearchRoute: @json($gifsSearchRoute),

// Group calls: مرجعيّات اتصالات WebRTC متعددة (mesh) لكل مشارك
peerConnections: {},
remoteStreamsVersion: 0,
callParticipants: [],
isGroupCall: false,
showAddParticipant: false,
addParticipantQuery: '',
speakerMuted: false,
availableAudioOutputs: [],
selectedAudioOutput: null,

// Call settings
callSettings: {
ringtone: 'default',
micDeviceId: '',
cameraDeviceId: '',
speakerDeviceId: '',
lowDataMode: false,
doNotDisturb: false,
},
callAudioInputs: [],
callVideoInputs: [],
callAudioOutputs: [],
micPermissionState: 'unknown',
cameraPermissionState: 'unknown',

positionCache: {},

// Delta refresh state

lastKnownMessageId: 0,

lastDeltaTime: null,

deltaRetryCount: 0,

deltaInFlight: false,

deltaInterval: 2000,

typingPingTimer: null,

typingStopTimer: null,

historyPage: 1,

historyLastPage: 1,

historyLoading: false,

activeLoadRecipientId: null,

eventSource: null,

sseReconnectTimer: null,

newConversationModal: false,

newConversationStep: 'mode',

groupDraftSelection: [],

potentialNewContacts: [],

newChatSearchQuery: '',

newChatSearchResults: [],

newChatSearchLoading: false,

brokenMediaByMessageId: {},

groupDraftName: '',

groupDraftAvatarFile: null,

groupDraftAvatarPreview: '',

activeUploadCount: 0,

groupCreateLoading: false,

toastMessage: '',

toastType: 'success',

_toastTimer: null,

sseFailCount: 0,

// Search

searchPanelOpen: false,

searchPanelQuery: '',

searchPanelResults: [],

searchPanelLoading: false,

searchPanelTimer: null,

// Reactions

reactionPickerMessageId: null,

reactionPickerTimer: null,

reactionsMap: {}, // { messageId: [{emoji, count, myReaction}] }

reactionEmojis: ['👍','❤️','😂','😮','😢','🔥','👏','😍','🤔','🎉','💯','🙏'],

// Settings panel

settingsPanelOpen: false,

settingSoundEnabled: true,

settingNotifyEnabled: true,

selectedTone: localStorage.getItem('messaging_selected_tone') || 'default',

toneByContact: safeLocalJson('conv_tones', {}),

soundEnabledByContact: safeLocalJson('conv_sound_enabled', {}),

toneVolumeByContact: safeLocalJson('conv_tone_volume', {}),

mutedUntilByContact: {},

blockedByContact: (() => { try { return JSON.parse(localStorage.getItem('messaging_blocked') || '{}'); } catch(_) { return {}; } })(),

wallpaperPickerMenuOpen: false,

muteOptionsOpen: false,

showCustomMute: false,

tonePickerOpen: false,

toneList: [{id:'default',label:'افتراضي'},{id:'soft-bell',label:'رنين ناعم'},{id:'classic',label:'كلاسيكي'},{id:'digital',label:'رقمي'},{id:'chime',label:'جرس'},{id:'pop',label:'بوب'},{id:'ping',label:'تنبيه'},{id:'gentle',label:'ناعم'}],

tonePreviewId: null,

selectedToneTemp: null,

tonePickerVolume: 100,

customToneFile: null,

customTonePreviewUrl: null,

customToneFileName: '',

muteCustomDays: 0,

muteCustomHours: 0,

muteCustomMinutes: 0,

lastCustomMute: safeLocalJson('conv_last_custom_mute', {}),

headerMenuOpen: false,

headerSub: null,

railFilter: 'all',

toolsMessageId: null,

toolsHoverTimer: null,

toolsTouchTimer: null,

isDesktop: window.innerWidth > 1080,

isResizing: false,

sidebarWidth: Number(localStorage.getItem('messaging_sidebar_width') || 760),

accountDrawerOpen: false,

messageContextOpen: false,

messageContextX: 0,

messageContextY: 0,

messageContextMessage: null,

wallpaperPickerOpen: false,

wallpapers: [

// شفق - Dusk teal

'radial-gradient(ellipse at top left,#0d4f4f 0%,#071a1a 50%,#000 100%)',

// نجوم - Starry (uses repeating dots)

'radial-gradient(ellipse at center,#0a0e1a 0%,#050810 100%)',

// ميش متدرج - Mesh gradient

'linear-gradient(135deg,#17130c 0%,#2a2114 42%,#0f0d09 100%)',

// ليل - Night

'linear-gradient(180deg,#02050f 0%,#070d1f 60%,#030810 100%)',

// زمرد - Emerald

'radial-gradient(ellipse at bottom right,#0a2e14 0%,#051208 50%,#020a04 100%)',

// خريشات - Crosshatch

'repeating-linear-gradient(0deg,transparent,transparent 28px,rgba(180,140,60,.07) 28px,rgba(180,140,60,.07) 29px),repeating-linear-gradient(90deg,transparent,transparent 28px,rgba(180,140,60,.07) 28px,rgba(180,140,60,.07) 29px),linear-gradient(180deg,#060c18,#0a1020)',

// غروب - Sunset red

'radial-gradient(ellipse at bottom left,#2d0a0a 0%,#1a0505 50%,#0a0202 100%)',

// Extra: Deep blue

'linear-gradient(145deg,#0f0d09,#20180d,#0f0d09)',

// Extra: Purple night

'radial-gradient(ellipse at top,#120920 0%,#06040e 60%,#020108 100%)',

// Extra: Gold dark

'linear-gradient(150deg,#120e00,#1f1800,#120e00)',

// Extra: Steel

'linear-gradient(130deg,#080f18,#0d1a28,#05090f)',

// Extra: Charcoal

'radial-gradient(ellipse at center,#181818,#0a0a0a,#040404)',

],

activeWallpaper: Number(localStorage.getItem('messaging_wallpaper_idx') || -1),

wallpaperNames: ['شفق','نجوم','ميش متدرج','ليل','زمرد','خريشات','غروب','أزرق','بنفسجي','ذهبي','فولاذي','جمري'],

conversationWallpapers: safeLocalJson('conv_wallpapers', {}),

conversationThemes: safeLocalJson('conv_themes', {}),

chatThemeDefs: [
{ id:'', name:'افتراضي', wp:-1, vars:{}, varsLight:{} },
{ id:'mesh', name:'ميش متدرج', wp:2, vars:{'--th-bubble-tint':'rgba(90,70,40,0.25)','--th-accent':'#c9a45a','--th-hover':'rgba(201,164,90,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(138,109,48,0.13)','--th-accent':'#8a6d30','--th-hover':'rgba(138,109,48,0.07)'} },
{ id:'stars', name:'نجوم', wp:1, vars:{'--th-bubble-tint':'rgba(80,100,180,0.25)','--th-accent':'#6d8fd4','--th-hover':'rgba(109,143,212,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(60,80,160,0.12)','--th-accent':'#4a6cb0','--th-hover':'rgba(74,108,176,0.06)'} },
{ id:'twilight', name:'شفق', wp:0, vars:{'--th-bubble-tint':'rgba(30,140,130,0.25)','--th-accent':'#3aa89e','--th-hover':'rgba(58,168,158,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(20,120,110,0.12)','--th-accent':'#208a80','--th-hover':'rgba(32,138,128,0.06)'} },
{ id:'sunset', name:'غروب', wp:6, vars:{'--th-bubble-tint':'rgba(180,60,60,0.25)','--th-accent':'#d46a5a','--th-hover':'rgba(212,106,90,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(200,80,60,0.12)','--th-accent':'#b04a3a','--th-hover':'rgba(176,74,58,0.06)'} },
{ id:'doodles', name:'خربشات', wp:5, vars:{'--th-bubble-tint':'rgba(140,100,200,0.25)','--th-accent':'#9b7fd6','--th-hover':'rgba(155,127,214,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(120,80,180,0.12)','--th-accent':'#7a5cb8','--th-hover':'rgba(122,92,184,0.06)'} },
{ id:'emerald', name:'زمرد', wp:4, vars:{'--th-bubble-tint':'rgba(40,150,70,0.25)','--th-accent':'#4caf64','--th-hover':'rgba(76,175,100,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(30,130,60,0.12)','--th-accent':'#2d8e48','--th-hover':'rgba(45,142,72,0.06)'} },
{ id:'night', name:'ليل', wp:3, vars:{'--th-bubble-tint':'rgba(60,70,140,0.3)','--th-accent':'#7a8ad4','--th-hover':'rgba(122,138,212,0.08)'}, varsLight:{'--th-bubble-tint':'rgba(40,50,120,0.15)','--th-accent':'#4a5ab0','--th-hover':'rgba(74,90,176,0.06)'} },
],

savedMessagesOpen: false,

savedMessagesList: [],

savedMessagesLoading: false,

savedMessageIds: [],

mediaGalleryOpen: false,

mediaGalleryTab: 'images',

foldersManagerOpen: false,

foldersConfig: [],

folderDraft: { editIndex: -1, id: null, name: '', icon: 'ri-folder-3-line', color: '#C6A675', includeIds: [], excludeIds: [] },
folderColorChoices: ['#C6A675', '#2ecc71', '#3498db', '#9b59b6', '#e74c3c', '#f1c40f', '#1abc9c', '#e67e22'],

includeExcludeMode: null,

includeExcludeSearch: '',

folderIconChoices: ['ri-folder-3-line','ri-bug-line','ri-book-open-line','ri-bit-coin-line','ri-gamepad-line','ri-lightbulb-line','ri-thumb-up-line','ri-music-2-line','ri-brush-line','ri-plane-line','ri-football-line','ri-star-line','ri-graduation-cap-line','ri-telegram-line','ri-user-3-line','ri-group-2-line','ri-chat-3-line','ri-robot-line','ri-vip-crown-line','ri-home-4-line','ri-heart-3-line','ri-briefcase-4-line','ri-notification-3-line','ri-speaker-line'],

// Profile modal

profileModalContact: null,

profileMoreOpen: false,

profileAutoDeleteOpen: false,
profileAutoDeleteDuration: 0,

profileExportOpen: false,
profileExportPhotos: true,
profileExportVideos: true,
profileExportVoice: true,
profileExportVideoMsg: true,
profileExportStickers: true,
profileExportGifs: true,
profileExportFiles: true,
profileExportMaxSize: 2000,

profileAddFolderOpen: false,
profileDeleteConfirm: false,
profileBlockConfirm: false,
profileNicknameEdit: false,
profileNicknameDraft: '',
profileShareChatOpen: false,
profileShareQuery: '',
profileShareSelected: [],
profileSharedGroups: [],

nicknameByContact: safeLocalJson('conv_nicknames', {}),

profileBannerScale: 1,
profileMediaModalOpen: false,
profileMediaModalType: '',
profileMediaCalendarOpen: false,
profileMediaCalendarFullOpen: false,
profileMediaCalendarDay: null,
profileMediaMenuOpen: false,

myProfileOpen: false,
myProfileEditName: '',
myProfileEditUsername: '',
myProfileEditPhone: '',
myProfileEditBio: '',
myProfileAvatar: null,
myProfileEditOpen: false,
myProfileBirthDate: '',
myProfileBannerScale: 1,
myProfileStatusViewerOpen: false,
myProfileViewerIdx: 0,
myProfileViewersOpen: false,
myProfileViewersList: [],

contactsOpen: false,
contactsQuery: '',

callsOpen: false,
callLogs: [],
callLogsLoaded: false,
callsTab: 'all',
callsDisplayCount: 20,
callsMenuOpen: false,
callsSelectMode: false,
callsSelectedDelete: [],
newCallModalOpen: false,
newCallQuery: '',
newCallSelected: [],
callsDeleteConfirm: false,
callsSettingsOpen: false,

qrModalOpen: false,

qrModalContact: null,

qrImageUrl: null,
qrQualityIndex: 1,
qrFontSize: 16,
qrShowProfilePhoto: true,
qrTransparentBg: false,
qrRawImageCache: null,

qrBgIndex: 0,

qrBgOptions: [

'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)',

'linear-gradient(135deg,#0f766e,#14b8a6,#0d9488)',

'linear-gradient(135deg,#7c2d12,#f59e0b,#d97706)',

'linear-gradient(135deg,#3a2a16,#c6a475,#a37c4e)',

'linear-gradient(135deg,#4c1d95,#7c3aed,#a78bfa)',

'linear-gradient(135deg,#831843,#ec4899,#f472b6)',

'linear-gradient(135deg,#065666,#0891b2,#22d3ee)',

'linear-gradient(135deg,#166534,#22c55e,#86efac)',

'linear-gradient(135deg,#422006,#a16207,#fbbf24)',

'linear-gradient(135deg,#111827,#374151,#6b7280)',

'linear-gradient(135deg,#1e1b4b,#3730a3,#6366f1)',

'linear-gradient(135deg,#2d1a0e,#78350f,#b45309)',

],

// Call state

callState: null, // null | 'calling' | 'in-call' | 'incoming'
callIsRinging: false,
callTimeoutTimer: null,

callType: null,  // 'audio' | 'video'

callContact: null,

currentCallId: null,

localStream: null,

incomingCallOffer: null,

callMuted: false,

cameraOff: false,

callStartedAt: null,

// Status and stories
statusEditorOpen: false, statusDrawerOpen: false,
statusFocusState: 0,

statusViewerOpen: false,

statusStickerDrawerOpen: false,
        statusPublishing: false,

statusViewersList: [],

statusViewersOpen: false,

statusViewerContact: null,

statusViewerIndex: 0,

statusViewerProgress: 0,

statusViewerTimer: null,

statusViewerMuted: false,

statusViewerReady: false,

statusViewerDurationMs: 5000,

statusPaused: false,

showStatusFullEmoji: false,

statusReplyText: '',

statusReplyFocused: false,

showQuickEmojiBar: false,

profileOpenedFromViewers: false,

statusLiked: false,

statusLikeAnimating: false,

recentStatusEmojis: (() => { try { const d = JSON.parse(localStorage.getItem('recentStatusEmojis') || '[]'); if (!Array.isArray(d)) return []; const txt = document.createElement('textarea'); return d.map(e => { txt.innerHTML = e; return txt.value || e; }); } catch (_) { return []; }})(),

statusLongPressActive: false,
statusLongPressTimer: null,
statusLongPressFired: false,

myStatuses: [],

contactStatuses: [],

statusPreviewCache: {},

brokenStatusAvatars: [],

statusEditor: {

type: 'text',

textContent: '',
textColor: '#ffffff',
fontStyle: 'Tajawal',
fontSize: 28,
textPosX: 50,
textPosY: 50,
rotate: 0,
textBgStyle: 'none',
bgColor: 'linear-gradient(135deg, var(--theme-surface, #17130c), var(--theme-surface-2, #241b12), var(--theme-gold-dark, #5b4124))',
filterStyle: null,
audioUrl: null,
durationHours: 24,
privacyType: 'all',
mediaFile: null,
mediaPreview: null,
audioFile: null,
texts: [],
activeTextIndex: -1,
},

statusTextSelected: false,
statusIsDragging: false,
statusTrashHover: false,
statusContextMenu: false,
statusAlignGuides: { cx: false, cy: false, ex: false, ey: false },
statusPickerTab: null,

statusEditorFonts: [
'Tajawal', 'Lalezar', 'Amiri', '"Aref Ruqaa"', 'Rakkas', '"Reem Kufi"', '"Courier New", monospace', 'Georgia, serif'
],

statusAutoBgPalette: [
'linear-gradient(135deg,#1f1c2c,#928dab)',
'linear-gradient(135deg,#0f2027,#203a43,#2c5364)',
'linear-gradient(135deg,#c6a475,#7a4e2d)',
'linear-gradient(135deg,#2d1b69,#11998e)',
'linear-gradient(135deg,#3a1c71,#d76d77,#ffaf7b)',
'linear-gradient(135deg,#0b486b,#f56217)',
'linear-gradient(135deg,#1a2a6c,#b21f1f,#fdbb2d)',
'linear-gradient(135deg,#16222a,#3a6073)',
'linear-gradient(135deg,#5f2c82,#49a09d)',
'linear-gradient(135deg,#231557,#44107a,#ff1361,#fff800)',
],

statusEditorFilters: [

{ id: null,   label: 'أصلي' },

{ id: 'warm', label: 'دافئ',  css: 'sepia(0.4) saturate(1.3) brightness(1.05)' },

{ id: 'cool', label: 'بارد',  css: 'hue-rotate(180deg) saturate(0.9)' },

{ id: 'bw',   label: 'أبيض وأسود', css: 'grayscale(1)' },

{ id: 'soft', label: 'ناعم',  css: 'blur(0.4px) brightness(1.1) saturate(0.8)' },

{ id: 'vivid',label: 'زاهي',  css: 'saturate(1.7) contrast(1.1)' },

],

filterSwipeHint: null,

_filterSwipeX: null,

_filterSwipeTimer: null,

_filterSwipeMouseDown: false,

statusViewersOpen: false,

statusViewersList: [],

// Video editor

videoEditorOpen: false,

videoEditorFile: null,
videoEditorUrl: null,
videoEditorDuration: 0,
videoEditorStart: 0,
videoEditorEnd: 0,
videoEditorQuality: '720p',
videoEditorProcessing: false,
videoEditorProgress: 0,
videoEditorPreviewUrl: null,
videoEditorPreviewFile: null,
videoEditorPreviewOpen: false,
veIsPlaying: false,
veCurrentTime: 0,
veDragHandle: null,
imageEditorOpen: false,
imageEditorFile: null,
imageEditorUrl: null,
imageEditorBrightness: 100,
imageEditorContrast: 100,
imageEditorSaturate: 100,
imageEditorFilter: '',
imageEditorBlur: 0,
imageEditorTab: 'adjust',
imageEditorRotate: 0,
imageEditorFlipH: false,
imageEditorFlipV: false,
deleteStatusTarget: null,

ffmpegLoaded: false,

// Voice player bar

voicePlayerMessage: null,

voicePlayerPosition: 0,

voiceSpeed: 1,

voicePlayerMuted: false,

voicePlayerExternalSource: null, // external audio messages array for prev/next navigation (profile media etc.)
_isDark: document.documentElement.getAttribute('data-theme') !== 'light',

// Settings sections

settingsSection: 'main_menu', // 'main_menu' | 'account' | 'privacy' | 'notifications' | 'media' | 'mic' | 'battery' | 'chats' | 'language' | 'about'

settingsEdit: false,

settingsEditName: '',

settingsEditUsername: '',
settingsEditPhone: '',
usernameCheckState: 'idle',
usernameCheckMessage: '',
usernameCheckTimer: null,
phoneCheckState: 'idle',
phoneCheckMessage: '',
phoneCheckTimer: null,

settingsPrivacy: {

lastSeenFor: 'all',       // all | contacts | nobody

profilePhotoFor: 'all',

messageFrom: 'all',

callFrom: 'all',

phoneVisibleFor: 'contacts',

forwardedMessagesFor: 'all',

hideOnlineStatus: false,

autoDeleteDays: 0,

frequentContactsEnabled: true,

deleteAccountAfterMonths: 0,

},

settingsNotifications: {

soundEnabled: true,

previewEnabled: true,

badgeEnabled: true,

desktopEnabled: false,

volume: 100,

},

settingsMedia: {

autoDownloadImages: true,

autoDownloadVideos: false,

autoDownloadFiles: false,

quality: '720p',

wifiOnly: true,

},

settingsSecurity: {

pinEnabled: false,

pin: '',

pinConfirm: '',

twoFaEnabled: false,

},

settingsExtraRoutes: @json($settingsExtraRoutes ?? []),

settingsAccount: { name: '', username: '', bio: '', phone: '', birthday: '', avatar_url: null, locale: 'ar', member_since: '', email_masked: '' },

settingsEditBio: '',

settingsEditBirthday: '',

settingsBlockedList: [],

settingsBlockedLoading: false,

settingsBlockUserIdInput: '',

settingsFrequentContacts: [],

settingsSessionsList: [],

settingsSessionsLoading: false,

settings2FAStep: 'idle', // idle | code-sent

settings2FACode: '',

settings2FADisablePassword: '',

settingsFoldersList: [],

settingsFoldersLoading: false,

settingsFolderDraft2: { id: null, name: '', icon: 'ri-folder-3-line' },

folderChatPickerId: null,

settingsLanguageChoice: 'ar',

settingsChats: { sendWithEnter: true, reduceMotion: false, defaultTheme: '', fontFamily: 'default', autoNightMode: false, doubleClickAction: 'reply', allowSensitiveContent: false, nameColor: '', tabsPosition: 'left', spellcheckEnabled: true, showFolderTags: false, showUnreadInTitle: false },

baseDocumentTitle: document.title,

darkModeMediaQuery: null,

settingsMicDevices: [],

settingsMicDeviceId: '',

micTestActive: false,
_micTestStream: null,
_micTestRaf: null,

            currentUserEmail: @json(Auth::user()->email ?? ''),

            userName: @json(Auth::user()->name ?? 'User'),

};

},

watch: {

totalUnreadCount() {
    this.updateDocumentTitle();
},

newChatSearchQuery(val) {
    if (this._newChatSearchTimer) clearTimeout(this._newChatSearchTimer);
    this._newChatSearchTimer = setTimeout(() => this.fetchNewChatUsers(val), 280);
},

chatBackground(newBg) {

this.$nextTick(() => {

const feed = this.$refs.messagesContainer;

if (!feed) return;

if (this.activeChatTheme?.wp >= 0) {

feed.style.background = this.wallpapers[this.activeChatTheme.wp] || '';

} else {

feed.style.background = newBg || '';

}

});

},

statusViewerMuted(val) {

const v = this.$refs.statusViewerVideo;

if (v) v.muted = !!val;

},

statusViewerOpen(val) {

if (!val) {

this.statusViewersOpen = false;

this.statusViewersList = [];

}

},

muteOptionsOpen(val) {

if (!val) return;

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

const saved = this.lastCustomMute[cid];

if (saved) {

this.muteCustomDays = saved.days ?? 0;

this.muteCustomHours = saved.hours ?? 0;

this.muteCustomMinutes = saved.minutes ?? 0;

} else {

this.muteCustomDays = 0;

this.muteCustomHours = 0;

this.muteCustomMinutes = 0;

}

},

showCustomMute(val) {

if (!val || !this.selectedContact) return;

const cid = String(this.selectedContact.id);

const saved = this.lastCustomMute[cid];

if (saved) {

this.muteCustomDays = saved.days ?? 0;

this.muteCustomHours = saved.hours ?? 0;

this.muteCustomMinutes = saved.minutes ?? 0;

}

},

},

computed: {

usernameCooldownDaysLeft() {
if (!this.settingsAccount.username_changed_at) return 0;
const changed = new Date(this.settingsAccount.username_changed_at);
const nextAllowed = new Date(changed.getTime() + 30 * 24 * 60 * 60 * 1000);
const diff = Math.ceil((nextAllowed - new Date()) / (24 * 60 * 60 * 1000));
return Math.max(0, diff);
},

pinnedMessagesForCurrentChat() {
return this.messages.filter(m => m.isPinned);
},

filteredEmojiCategories() {
const q = this.emojiSearchQuery.trim().toLowerCase();
if (!q) return this.emojiCategoriesData;
return this.emojiCategoriesData
.map(cat => ({ ...cat, emojis: cat.emojis.filter(e => e.k.some(k => k.includes(q))) }))
.filter(cat => cat.emojis.length > 0);
},

filteredContacts() {

if (!this.searchQuery.trim()) return this.contacts;

const q = this.searchQuery.toLowerCase();

return this.contacts.filter(c => (c.name || '').toLowerCase().includes(q));

},

currentStatus() {

if (!this.statusViewerContact) return null;

return (this.statusViewerContact.statuses || [])[this.statusViewerIndex] || null;

},

statusEmojiList() {

const defaults = ['😍','😂','😮','👏','🔥','❤️','🤩','💯'];
const recent = Array.isArray(this.recentStatusEmojis) ? this.recentStatusEmojis : [];
const txt = document.createElement('textarea');
const decoded = recent.map(e => { txt.innerHTML = e; return txt.value || e; });
const all = [...decoded, ...defaults];
const seen = new Set();
return all.filter(e => { if (seen.has(e)) return false; seen.add(e); return true; }).slice(0, 8);
},

sortedMessages() {
if (!this.messages || !this.messages.length) return [];
return [...this.messages].sort((a, b) => {
    const ta = new Date(a.createdAt || a.created_at || 0).getTime();
    const tb = new Date(b.createdAt || b.created_at || 0).getTime();
    return ta - tb;
});
},

filteredMessages() {
return this.sortedMessages;
},

estimatedVideoSize() {

if (!this.videoEditorDuration) return '--';

const dur = Math.max(0.1, this.videoEditorEnd - this.videoEditorStart);

const bitrateMap = { '360p': 500, '480p': 1000, '720p': 2500, '1080p': 5000 };

const kbps = bitrateMap[this.videoEditorQuality] || 2000;

const sizeKb = (kbps * dur) / 8;

return sizeKb > 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : Math.round(sizeKb) + ' KB';

},

currentEditorFilter() {

const f = (this.statusEditorFilters || []).find(x => x.id === this.statusEditor.filterStyle);

return f && f.css ? f.css : '';

},

canSendMessage() {

return !!(this.selectedContact && (this.messageInput.trim() || this.pendingAttachments.length));

},

showSendAction() {

return this.canSendMessage;

},

availableNewContacts() {

const q = this.searchQuery.trim().toLowerCase();

const all = [...this.contacts, ...this.potentialNewContacts]

.filter(c => Number(c.id) !== Number(this.currentUserId));

const uniqueById = [];

const seen = new Set();

for (const item of all) {

const key = Number(item.id);

if (!key || seen.has(key)) continue;

seen.add(key);

uniqueById.push(item);

}

return uniqueById.filter(c => !q || String(c.name || '').toLowerCase().includes(q));

},

filteredContactsByRail() {

const savedCount = (this.savedMessagesList || []).length;
const lastSavedAt = this.savedMessagesList.length ? this.savedMessagesList[0].createdAt || this.savedMessagesList[0].created_at || null : null;
const savedEntry = { id: -1, name: 'الرسائل المحفوظة', avatar_url: null, isGroup: false, hasConversation: true, unreadCount: 0, username: 'saved', lastMessage: savedCount ? `${savedCount} رسالة محفوظة` : 'مساحة آمنة — احفظ ما تشاء', lastSeenAt: lastSavedAt, isSaved: true };

const all = (this.filteredContacts || []).filter(c => !!c.hasConversation);

let result;

if (this.railFilter === 'unread') result = all.filter(c => Number(c.unreadCount || 0) > 0);

else if (this.railFilter === 'groups') result = all.filter(c => !!c.isGroup);

else if (this.railFilter === 'private') result = all.filter(c => !c.isGroup);

else {

const folder = (this.foldersConfig || []).find(f => f.id === this.railFilter);

if (folder) {

result = all.filter(c => {

const id = Number(c.id);

const inc = (folder.includeIds || []);

const exc = (folder.excludeIds || []);

const inScope = inc.length ? inc.includes(id) : true;

return inScope && !exc.includes(id);

});

} else result = all;

}

if (!this.searchQuery.trim()) result = [savedEntry, ...result];

result.sort((a, b) => {
    const pa = a.isPinned ? 1 : 0;
    const pb = b.isPinned ? 1 : 0;
    if (pa !== pb) return pb - pa;
    const ta = new Date(a.lastSeenAt || 0).getTime();
    const tb = new Date(b.lastSeenAt || 0).getTime();
    return tb - ta;
});

return result;

},

includeExcludeCandidates() {

const q = String(this.includeExcludeSearch || '').trim().toLowerCase();

return (this.contacts || []).filter(c => !q || String(c.name || '').toLowerCase().includes(q));

},

userInitial() {

return this.getAuthorInitial(this.userName);

},

contactNickname() {

const id = this.profileModalContact?.id;
return id ? (this.nicknameByContact[String(id)] || '') : '';

},

profileMediaImages() {

return (this.messages || []).filter(m => m.messageType === 'image' && m.attachmentUrl);

},

profileMediaVideos() {

return (this.messages || []).filter(m => m.messageType === 'video' && m.attachmentUrl);

},

profileMediaAudio() {

return (this.messages || []).filter(m => m.messageType === 'audio' && m.attachmentUrl);

},

profileMediaFiles() {

return (this.messages || []).filter(m => m.messageType === 'file' && m.attachmentUrl);

},

filteredContactsForShare() {

const saved = { id: -1, name: 'الرسائل المحفوظة', avatar_url: null, isGroup: false, username: 'saved' };
let list = [...(this.contacts || []), saved];
const q = (this.profileShareQuery || '').trim().toLowerCase();
if (q) {
list = list.filter(c => (c.name || '').toLowerCase().includes(q));
}
return list.filter(c => Number(c.id) !== Number(this.currentUserId));

},

forwardContacts() {
const saved = { id: -1, name: 'الرسائل المحفوظة' };
return [saved, ...(this.contacts || []).filter(c => Number(c.id) !== Number(this.currentUserId))];
},

galleryImages() {

return (this.messages || []).filter(m => m.messageType === 'image' && m.attachmentUrl);

},

galleryVideos() {

return (this.messages || []).filter(m => m.messageType === 'video' && m.attachmentUrl);

},

galleryAudio() {

return (this.messages || []).filter(m => m.messageType === 'audio' && m.attachmentUrl);

},

galleryLinks() {

return (this.messages || []).filter(m => m.content && /https?:\/\//.test(m.content));

},

galleryImagesByMonth() { return this._groupGalleryByMonth(this.galleryImages); },
galleryVideosByMonth() { return this._groupGalleryByMonth(this.galleryVideos); },

savedMessages() {

return (this.messages || []).filter(m => this.savedMessageIds.includes(Number(m.id)));

},

chatBackground() {

if (!this.selectedContact) return this.wallpapers[this.activeWallpaper];

const cw = this.conversationWallpapers[String(this.selectedContact.id)];

return (cw !== undefined && cw >= 0) ? this.wallpapers[cw] : (this.wallpapers[this.activeWallpaper] || '');

},

mutedContactsCount() {
    const now = Date.now();
    return Object.values(this.mutedUntilByContact || {}).filter(t => t > now).length;
},

totalUnreadCount() {
    return (this.contacts || []).reduce((sum, c) => sum + Number(c.unreadCount || 0), 0);
},

activeChatTheme() {

if (!this.selectedContact) return null;

const themeId = this.conversationThemes[String(this.selectedContact.id)] || this.settingsChats.defaultTheme;

if (!themeId) return null;

return this.chatThemeDefs.find(t => t.id === themeId) || null;

},

isDarkMode() {

return this._isDark;

},

perContactTone() {

if (!this.selectedContact) return this.selectedTone;

return this.toneByContact[String(this.selectedContact.id)] || this.selectedTone;

},

perContactSoundEnabled() {

if (!this.selectedContact) return this.settingSoundEnabled;

const val = this.soundEnabledByContact[String(this.selectedContact.id)];

return val !== undefined ? val : this.settingSoundEnabled;

},

contactMuted() {

if (!this.selectedContact) return false;

const until = this.mutedUntilByContact[String(this.selectedContact.id)];

if (!until) return false;

return Date.now() < until;

},

contactBlocked() {

if (!this.selectedContact) return false;

return !!this.blockedByContact[String(this.selectedContact.id)];

},

contactBlockedInProfile() {

if (!this.profileModalContact) return false;

return !!this.blockedByContact[String(this.profileModalContact.id)];

},

contactUnreadInProfile() {

if (!this.profileModalContact) return false;
return Number(this.profileModalContact.unreadCount || 0) > 0;

},

contactPrivateInProfile() {

if (!this.profileModalContact) return false;
return !this.profileModalContact.isGroup;

},

contactGroupInProfile() {

if (!this.profileModalContact) return false;
return !!this.profileModalContact.isGroup;

},

blockedMapByContact() {

return (cid) => !!this.blockedByContact[String(cid)];

},

filteredContactsForContacts() {
const q = (this.contactsQuery || '').trim().toLowerCase();
let list = (this.contacts || []).filter(c => Number(c.id) !== Number(this.currentUserId) && !!c.hasConversation);
if (!q) return list;
return list.filter(c => {
const name = (c.name || '').toLowerCase();
const phone = String(c.phone || c.username || '').toLowerCase();
return name.includes(q) || phone.includes(q);
});
},

contactMuteMinutes() {

if (!this.selectedContact) return 0;

const until = this.mutedUntilByContact[String(this.selectedContact.id)];

if (!until) return 0;

const remaining = Math.round((until - Date.now()) / 60000);

return remaining > 0 ? remaining : 0;

},

voicePlayerPercent() {

const elDur = this.activeAudioElement?.duration;
const audioDur = Number.isFinite(elDur) && elDur > 0 && elDur < 1e8 ? elDur : 0;
const total = Number(this.voicePlayerMessage?.audioDuration || audioDur || 0);

if (!total) return 0;

return Math.min(100, (this.voicePlayerPosition / total) * 100);

},

profileMediaCalendarDayStr() {

if (!this.profileMediaCalendarDay) return '';
const { year, month, day } = this.profileMediaCalendarDay;
return year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');

},

profileMediaCalendarDayLabel() {

if (!this.profileMediaCalendarDay) return '';
const { year, month, day } = this.profileMediaCalendarDay;
const d = new Date(year, month, day);
if (Number.isNaN(d.getTime())) return '';
const today = new Date(); const todayS = today.toLocaleDateString('en-CA');
const yesterday = new Date(today); yesterday.setDate(yesterday.getDate() - 1);
const dateStr = d.toLocaleDateString('en-CA');
if (dateStr === todayS) return 'اليوم';
if (dateStr === yesterday.toLocaleDateString('en-CA')) return 'أمس';
return d.toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

},

// Calls computed
displayedCalls() {
const tab = this.callsTab === 'missed' ? 'missed' : null;
let logs = this.callLogs || [];
if (tab) logs = logs.filter(c => c.status === 'missed');
return logs.slice(0, this.callsDisplayCount);
},

filteredNewCallContacts() {
let list = [...(this.contacts || [])].filter(c => Number(c.id) !== Number(this.currentUserId));
const q = (this.newCallQuery || '').trim().toLowerCase();
if (q) {
list = list.filter(c => {
const name = (c.name || '').toLowerCase();
const uname = (c.username || '').toLowerCase();
return name.includes(q) || uname.includes(q);
});
}
return list;
},

newCallSelectedContacts() {
return (this.contacts || []).filter(c => this.newCallSelected.includes(c.id));
},

myProfileDisplayName() {
const saved = safeLocalJson('messaging_my_profile', {});
return saved.name || this.userName || 'User';
},

myProfileDisplayUsername() {
const saved = safeLocalJson('messaging_my_profile', {});
const uname = saved.username || '';
const phone = this.currentUserPhone || '';
const raw = saved.username || saved.phone || this.currentUserPhone || '';
if (raw.startsWith('@')) return raw;
return raw ? '@' + raw : '@user';
},

myProfileJoinDate() {
const saved = safeLocalJson('messaging_my_profile', {});
const birth = saved.birthDate || this.myProfileBirthDate;
if (birth) {
const d = new Date(birth);
if (!Number.isNaN(d.getTime())) {
return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
}
}
// Fallback: show account creation date
return new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
},

myProfileAge() {
const saved = safeLocalJson('messaging_my_profile', {});
const birth = saved.birthDate || this.myProfileBirthDate;
if (!birth) return '—';
const d = new Date(birth);
if (Number.isNaN(d.getTime())) return '—';
const now = new Date();
let age = now.getFullYear() - d.getFullYear();
const m = now.getMonth() - d.getMonth();
if (m < 0 || (m === 0 && now.getDate() < d.getDate())) age--;
return age >= 0 ? age : 0;
},

myProfileStatuses() {
return this.myStatuses || [];
},

myProfileViewerStatus() {
const list = this.myProfileStatuses;
return list[this.myProfileViewerIdx] || null;
},

myProfileViewerStatusDate() {
const s = this.myProfileViewerStatus;
if (!s) return '';
const d = new Date(s.createdAt || s.created_at || Date.now());
if (Number.isNaN(d.getTime())) return '';
return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
},

myBannerStyle() {
const s = this.myProfileBannerScale;
const h = 100 + Math.round(80 * s);
return { height: h + 'px', background: 'radial-gradient(ellipse at center,var(--gold-2),var(--dark))', transition: 'height .05s linear' };
},

myAvatarWrapStyle() {
const s = this.myProfileBannerScale;
const bottom = -40 + Math.round(10 * s) + 'px';
const scale = 0.55 + 0.45 * s;
const opacity = Math.max(0, (s - 0.3) / 0.7);
return { bottom, left: '50%', transform: 'translateX(-50%) scale(' + scale + ')', opacity, transition: 'bottom .05s linear, opacity .05s linear, transform .05s linear' };
},

myAvatarInitialsStyle() {
return { width: 100 + 'px', height: 100 + 'px', fontSize: 36 + 'px' };
},

myProfileBirthDateDisplay() {
const saved = safeLocalJson('messaging_my_profile', {});
const birth = saved.birthDate || this.myProfileBirthDate;
if (!birth) return 'غير محدد';
const d = new Date(birth);
if (Number.isNaN(d.getTime())) return 'غير محدد';
return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
},

},

methods: {

getProfileMediaByType(type) {

const map = { images:'image', videos:'video', audio:'audio', files:'file' };
const t = map[type] || type;
return (this.messages || []).filter(m => m.messageType === t && m.attachmentUrl);

},

getProfileMediaCalendarDays() {

const items = this.getProfileMediaByType(this.profileMediaModalType);
if (!items.length) return [];
const groups = {};
items.forEach(m => {
const d = new Date(m.createdAt || m.created_at);
if (Number.isNaN(d.getTime())) return;
const key = d.toLocaleDateString('en-CA');
if (!groups[key]) groups[key] = { date: key, items: [], label: '' };
groups[key].items.push(m);
});
const today = new Date(); const todayS = today.toLocaleDateString('en-CA');
const yesterday = new Date(today); yesterday.setDate(yesterday.getDate()-1); const yesterdayS = yesterday.toLocaleDateString('en-CA');
return Object.keys(groups).sort().reverse().map(key => {
let label = '';
if (key === todayS) label = 'اليوم';
else if (key === yesterdayS) label = 'أمس';
else {
const d = new Date(key + 'T00:00:00');
label = d.toLocaleDateString('ar-SA', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
}
return { date: key, items: groups[key].items, label };
});

},

getProfileMediaMonthGroups(type) {
const items = this.getProfileMediaByType(type);
if (!items.length) return [];
const groups = {};
items.forEach(m => {
const d = new Date(m.createdAt || m.created_at);
if (Number.isNaN(d.getTime())) return;
const key = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0');
if (!groups[key]) {
const monthName = d.toLocaleDateString('ar-SA', { year:'numeric', month:'long' });
groups[key] = { key, label: monthName, items: [] };
}
groups[key].items.push(m);
});
return Object.keys(groups).sort().reverse().map(k => groups[k]);
},

getProfileMediaCalendarDaysSet(type) {
const items = this.getProfileMediaByType(type);
const days = new Set();
items.forEach(m => {
const d = new Date(m.createdAt || m.created_at);
if (!Number.isNaN(d.getTime())) {
days.add(d.toLocaleDateString('en-CA'));
}
});
return days;
},

profileCalendarMonthOffset: 0,

openProfileCalendarFull() {
this.profileMediaMenuOpen = false;
this.profileMediaCalendarFullOpen = !this.profileMediaCalendarFullOpen;
},

profileCalendarYear: null,
profileCalendarMonth: null,

initProfileCalendarView() {
const now = new Date();
this.profileCalendarYear = now.getFullYear();
this.profileCalendarMonth = now.getMonth();
this.profileCalendarMonthOffset = 0;
},

profileCalendarDaysInMonth(year, month) {
return new Date(year, month + 1, 0).getDate();
},

profileCalendarFirstDay(year, month) {
return new Date(year, month, 1).getDay();
},

profileCalendarPrevMonth() {
this.profileCalendarMonthOffset--;
if (this.profileCalendarMonth === 0) {
this.profileCalendarMonth = 11;
this.profileCalendarYear--;
} else {
this.profileCalendarMonth--;
}
},

profileCalendarNextMonth() {
this.profileCalendarMonthOffset++;
if (this.profileCalendarMonth === 11) {
this.profileCalendarMonth = 0;
this.profileCalendarYear++;
} else {
this.profileCalendarMonth++;
}
},

normalizeAttachmentUrl(url) {

if (!url) return '';

const s = String(url).trim().replace(/\\/g, '/');

if (!s) return '';

if (/^(https?:)?\/\//i.test(s) || s.startsWith('/') || s.startsWith('blob:') || s.startsWith('data:')) return s;

if (s.startsWith('messaging/attachments/')) return `/storage/message-attachment/message_attachments/${s.split('/').pop()}`;

if (s.startsWith('messaging/audio/')) return `/storage/message-audio/message_audio/${s.split('/').pop()}`;

return `/storage/${s.replace(/^storage\//, '').replace(/^\//, '')}`;

},

normalizeAvatarUrl(url) {

if (!url) return '';

const s = String(url).trim();

if (!s) return '';

if (/^(https?:)?\/\//i.test(s) || s.startsWith('/')) return s;

return `/storage/${s.replace(/^storage\//, '').replace(/^\//, '')}`;

},

isMojibakeText(value) {

if (!value) return false;

const text = String(value);

if (text.includes("\uFFFD")) return true;

if (text.includes("\u0637") || text.includes("\u0622\u00a2") || text.includes("\u00e2\u20ac")) return true;

const weird = (text.match(/[^\x00-\x7F\u0600-\u06FF0-9a-zA-Z\s.,!?()\-_:;\/@#%&*+"'`~]/g) || []).length;

return weird > Math.max(8, Math.floor(text.length * 0.35));

},

sanitizeDisplayText(value, fallback = '') {

if (!value) return fallback;

const text = String(value).trim();

if (!text) return fallback;

return this.isMojibakeText(text) ? fallback : text;

},

handleAvatarError(e, entity) {

e.target.onerror = null;

e.target.src = '/images/default-avatar.svg';

if (entity && Object.prototype.hasOwnProperty.call(entity, 'avatar_url')) {

entity.avatar_url = null;

}

},

normalizeMessage(msg) {

const normalize = (raw) => {

if (!raw) return null;

const senderId = raw.senderId ?? raw.sender_id ?? raw.sender?.id ?? raw.user_id ?? null;

const recipientId = raw.recipientId ?? raw.recipient_id ?? raw.recipient?.id ?? null;

return {

...raw,

senderId,

recipientId,

senderName: raw.senderName ?? raw.sender_name,

attachmentUrl: this.normalizeAttachmentUrl(raw.attachmentUrl ?? raw.attachment_url ?? raw.audioUrl ?? raw.url),

attachmentName: raw.attachmentName ?? raw.attachment_name,

attachmentMime: raw.attachmentMime ?? raw.attachment_type,

attachmentKind: raw.attachmentKind ?? raw.attachment_kind ?? null,

isEdited: !!(raw.isEdited ?? raw.is_edited ?? false),

readAt: raw.readAt ?? raw.read_at,

isPinned: !!(raw.isPinned ?? raw.is_pinned ?? false),

forwardedFromMessageId: raw.forwardedFromMessageId ?? raw.forwarded_from_message_id ?? null,
audioPosition: Number(raw.audioPosition ?? raw.audio_position ?? 0),
isSensitive: !!(raw.isSensitive ?? raw.is_sensitive ?? false),

senderAvatar: raw.senderAvatar ?? raw.sender_avatar ?? raw.authorAvatar ?? raw.author_avatar ?? raw.sender?.avatar_url ?? null,

};

};

const n = {

...normalize(msg),

replyTo: normalize(msg.replyTo ?? msg.repliedMessage ?? null),

isPlaying: false,

currentBar: 0,

playbackPosition: 0,

_resumePos: Number((msg.audioPosition ?? msg.audio_position) || 0),

mediaLoaded: false,

failed: false,

};

n.playbackPosition = Number(n.audioPosition || n._resumePos || 0);

const statusRefMatch = String(n.content || '').match(/\[\[status:(\d+)\]\]/i);

n.statusRefId = statusRefMatch ? Number(statusRefMatch[1]) : null;

if (statusRefMatch) {

n.content = String(n.content || '').replace(/\[\[status:\d+\]\]\s*/i, '');

}

if (n.replyTo && n.replyTo.content) {

n.replyTo.content = String(n.replyTo.content).replace(/\[\[status:\d+\]\]\s*/i, '');

}

if (n.attachmentMime && n.attachmentMime.startsWith('audio/')) {
const uploadedDur = Number(n.duration || 0);
n.audioDuration = uploadedDur > 0 ? Math.round(uploadedDur) : this.extractAudioDuration(n.content);
}

n.messageType = this.getMessageType(n);

// Reply previews need their own type so audio/image/file previews render instead of falling through to empty text.

if (n.replyTo) n.replyTo.messageType = this.getMessageType(n.replyTo);

// Mark non-image/video messages as already "loaded" to skip skeleton

if (n.messageType !== 'image' && n.messageType !== 'video') n.mediaLoaded = true;

return n;

},

onMediaLoaded(message) {

if (message) message.mediaLoaded = true;

if (this.isNearBottom()) this.scrollToBottom(false);

},

extractAudioDuration(content) {

if (!content) return 0;

const m = String(content).match(/(\d+(\.\d+)?)/);

return m ? Math.round(parseFloat(m[1])) : 0;

},

isLikelyVoiceAttachment(message) {

if (!message) return false;

const mime = String(message.attachmentMime || '').toLowerCase();

const name = String(message.attachmentName || '').toLowerCase();

const url = String(message.attachmentUrl || '').toLowerCase();

const content = String(message.content || '').toLowerCase();

const hasDurationHint = Number(message.duration || 0) > 0 || this.extractAudioDuration(content) > 0;

const voiceHint = /audio|voice|record|audio_message|opus/.test(`${name} ${content} ${url}`);

const webmLike = mime === 'video/webm' || /\.webm(\?.*)?$/i.test(name) || /\.webm(\?.*)?$/i.test(url);

return mime.startsWith('audio/') || (webmLike && (voiceHint || hasDurationHint));

},

getMessageType(m) {

if (m.attachmentKind === 'sticker_static' || m.attachmentKind === 'sticker_animated' || m.attachmentKind === 'gif') return m.attachmentKind;

const mime = (m.attachmentMime || '').toLowerCase();

const url = String(m.attachmentUrl || '').toLowerCase();

const name = String(m.attachmentName || '').toLowerCase();

if (url.includes('/storage/message-audio/') || url.includes('/message_audio/')) return 'audio';

if (mime.startsWith('video/')) return 'video';

if (mime.startsWith('image/')) return 'image';

if (mime.startsWith('audio/')) return 'audio';

if (this.isLikelyVoiceAttachment(m)) return 'audio';

if (/\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?.*)?$/i.test(url) || /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?.*)?$/i.test(name)) {

return 'image';

}

if (this.isLikelyVoiceAttachment(m)) return 'audio';

if (/\.(mp4|mov|mkv|webm|avi|flv|wmv|m4v|3gp)(\?.*)?$/i.test(url) || /\.(mp4|mov|mkv|webm|avi|flv|wmv|m4v|3gp)(\?.*)?$/i.test(name)) {

return 'video';

}

if (/\.(mp3|wav|ogg|m4a|aac|opus|flac|wma)(\?.*)?$/i.test(url) || /\.(mp3|wav|ogg|m4a|aac|opus|flac|wma)(\?.*)?$/i.test(name)) {

return 'audio';

}

if (/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|rar|7z)(\?.*)?$/i.test(url) || /\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|rar|7z)(\?.*)?$/i.test(name)) {

return 'file';

}

if (url || name || mime) {

return 'file';

}

return 'text';

},

markMediaAsBroken(message) {

const id = Number(message?.id || 0);

if (!id) return;

// Keep original type rendering; only mark internally for diagnostics.

this.brokenMediaByMessageId = { ...this.brokenMediaByMessageId, [id]: true };

},

getFileIcon(mime) {

if (!mime) return 'ri-file-line';

if (mime.includes('pdf')) return 'ri-file-pdf-line';

if (mime.includes('word') || mime.includes('document')) return 'ri-file-word-2-line';

if (mime.includes('sheet') || mime.includes('excel')) return 'ri-file-excel-2-line';

if (mime.includes('presentation') || mime.includes('powerpoint')) return 'ri-file-ppt-2-line';

if (mime.includes('zip') || mime.includes('rar')) return 'ri-file-zip-line';

return 'ri-file-line';

},

getMessagePreviewText(message) {

if (!message) return this.t.attachment;

        const content = this.sanitizeDisplayText(String(message.content || '').replace(/\[\[status:\d+\]\]\s*/i, ''));

const hasAttachment = !!(message.attachmentName || message.attachmentUrl || message.attachmentMime);

if (content) {

const normalizedContent = content.trim().toLowerCase();

const normalizedAttachmentName = String(message.attachmentName || '').trim().toLowerCase();

const looksLikeFileName = /\.[a-z0-9]{2,6}(\?.*)?$/i.test(normalizedContent);

const isAttachmentEcho = !!(hasAttachment && (normalizedContent === normalizedAttachmentName || looksLikeFileName));

if (!isAttachmentEcho) return content;

}

const type = this.getMessageType(message);

if (type === 'image') return '\u0635\u0648\u0631\u0629 \u0645\u0631\u0641\u0642\u0629';

if (type === 'video') return '\u0641\u064a\u062f\u064a\u0648 \u0645\u0631\u0641\u0642';

if (type === 'audio') return '\u0631\u0633\u0627\u0644\u0629 \u0635\u0648\u062a\u064a\u0629';

const mime = String(message.attachmentMime || '').toLowerCase();

const name = String(message.attachmentName || '').toLowerCase();

const url = String(message.attachmentUrl || '').toLowerCase();

if (mime.includes('pdf') || name.includes('.pdf') || /\.(pdf)(\?.*)?$/.test(url)) return '\u0645\u0644\u0641 PDF';

if (mime.includes('word') || name.includes('.doc') || name.includes('.docx') || /\.(doc|docx)(\?.*)?$/.test(url)) return '\u0645\u0644\u0641 Word';

if (mime.includes('excel') || name.includes('.xls') || name.includes('.xlsx') || /\.(xls|xlsx)(\?.*)?$/.test(url)) return '\u0645\u0644\u0641 Excel';

if (mime.includes('presentation') || name.includes('.ppt') || name.includes('.pptx') || /\.(ppt|pptx)(\?.*)?$/.test(url)) return '\u0645\u0644\u0641 \u0639\u0631\u0636 \u062a\u0642\u062f\u064a\u0645\u064a';

if (message.attachmentName) return this.t.attachment;

return this.t.attachment;

},

getPendingAttachmentLabel(item) {

const type = item?.previewType || this.getMessageType({

attachmentMime: item?.mime,

attachmentName: item?.name,

attachmentUrl: item?.previewUrl,

});

if (type === 'image') return '\u0635\u0648\u0631\u0629 \u0645\u0631\u0641\u0642\u0629';

if (type === 'video') return '\u0641\u064a\u062f\u064a\u0648 \u0645\u0631\u0641\u0642';

if (type === 'audio') return '\u0631\u0633\u0627\u0644\u0629 \u0635\u0648\u062a\u064a\u0629';

return '\u0645\u0644\u0641 \u0645\u0631\u0641\u0642';

},

formatDuration(sec) {

const s = Math.max(0, Math.round(Number(sec || 0)));

const m = Math.floor(s / 60);

const r = s % 60;

return `${m}:${String(r).padStart(2, '0')}`;

},

formatContactTime(value) {

if (!value) return '';

const d = new Date(value);

if (Number.isNaN(d.getTime())) return '';

const now = new Date();

const nowDay = new Date(now.getFullYear(), now.getMonth(), now.getDate());

const msgDay = new Date(d.getFullYear(), d.getMonth(), d.getDate());

const dayDiff = Math.floor((nowDay - msgDay) / 86400000);

if (dayDiff <= 0) {

return d.toLocaleTimeString('ar-SA-u-ca-gregory', { hour: 'numeric', minute: '2-digit', hour12: true });

}

if (dayDiff === 1) return '\u0623\u0645\u0633';

return d.toLocaleDateString('ar-SA-u-ca-gregory', { month: 'short', day: 'numeric' });

},

formatLastSeen(contact) {

if (!contact) return this.t.unavailable;

if (contact.isOnline) return this.t.onlineNow;

const raw = contact.lastSeenAt || contact.last_seen_at;

if (raw) {

// Parse - handle both ISO strings and timestamps

const d = new Date(raw);

if (!Number.isNaN(d.getTime())) {

const ts = d.getTime();

// Reject clearly invalid timestamps (before 2020)

if (ts < Date.UTC(2020, 0, 1)) {

return this.sanitizeDisplayText(contact.lastSeen || contact.last_seen, '') || this.t.unavailable;

}

const diff = Math.max(0, Date.now() - ts);

const secs = Math.floor(diff / 1000);

const mins = Math.floor(diff / 60000);

const hrs  = Math.floor(diff / 3600000);

const days = Math.floor(diff / 86400000);

if (secs < 90)  return 'منذ لحظات';

if (mins < 60)  return `منذ ${mins} دقيقة`;

if (hrs  < 24)  return `منذ ${hrs} ساعة`;

if (days === 1) return 'أمس';

if (days <= 6)  return `منذ ${days} أيام`;

// Gregorian date (explicitly force gregory calendar)

const opts = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true, timeZone: 'Asia/Riyadh' };

return d.toLocaleString('ar-SA-u-ca-gregory', opts);

}

}

const legacy = this.sanitizeDisplayText(contact.lastSeen || contact.last_seen, '');

return legacy || this.t.unavailable;

},

showToast(msg, type = 'success', duration = 3200) {

this.toastMessage = msg;

this.toastType = type;

if (this._toastTimer) clearTimeout(this._toastTimer);

this._toastTimer = setTimeout(() => { this.toastMessage = ''; }, duration);

},

async fetchNewChatUsers(q) {

const url = this.settingsExtraRoutes?.usersSearch;

if (!url || url === '#') return;

this.newChatSearchLoading = true;

try {

const params = new URLSearchParams();

if (q) params.set('q', q);

const res = await fetch(`${url}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });

if (!res.ok) return;

this.newChatSearchResults = await res.json();

} catch(e) {

console.warn('user search failed', e);

} finally {

this.newChatSearchLoading = false;

}

},

openNewConversationModal() {

this.newConversationModal = true;

this.newConversationStep = 'mode';

this.groupDraftSelection = [];

this.groupDraftName = '';

this.groupDraftAvatarFile = null;

this.groupDraftAvatarPreview = '';

this.newChatSearchQuery = '';

this.newChatSearchResults = [];

this.fetchNewChatUsers('');

},

closeNewConversationModal() {

this.newConversationModal = false;

this.newConversationStep = 'mode';

this.groupDraftSelection = [];

this.groupDraftName = '';

this.groupDraftAvatarFile = null;

this.groupDraftAvatarPreview = '';

this.newChatSearchQuery = '';

this.newChatSearchResults = [];

},

startDirectConversation(contact) {

if (!contact) return;

this.closeNewConversationModal();

const normalized = {

id: contact.id,

name: contact.name,

avatar_url: this.normalizeAvatarUrl(contact.avatar_url),

lastMessage: '',

lastMessageTime: null,

lastMessageStatusRefId: null,

unreadCount: 0,

isOnline: !!contact.isOnline,

lastSeenAt: contact.lastSeenAt || null,

lastSeen: this.t.unavailable,

};

this.selectContact(normalized);

},

createGroupDraft() {

if (!this.groupDraftSelection.length) return;

this.newConversationStep = 'group-settings';

},

onGroupAvatarChange(event) {

const file = event?.target?.files?.[0] || null;

this.groupDraftAvatarFile = file;

if (this.groupDraftAvatarPreview && this.groupDraftAvatarPreview.startsWith('blob:')) {

URL.revokeObjectURL(this.groupDraftAvatarPreview);

}

this.groupDraftAvatarPreview = file ? URL.createObjectURL(file) : '';

},

submitGroupDraft() {

if (!this.groupDraftSelection.length || !this.groupDraftName.trim()) {

this.showToast('أدخل اسم القروب واختر مشاركًا واحدًا على الأقل', 'error');

return;

}

if (this.groupCreateLoading) return;

this.groupCreateLoading = true;

const routeUrl = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.group') ? route('teacher.messaging.group') : null)

: (\Illuminate\Support\Facades\Route::has('student.messaging.group') ? route('student.messaging.group') : (\Illuminate\Support\Facades\Route::has('messaging.group') ? route('messaging.group') : null))

);

if (!routeUrl) {

this.groupCreateLoading = false;

this.showToast('Group creation route is not configured yet.', 'error');

return;

}

const fd = new FormData();

fd.append('name', this.groupDraftName.trim());

this.groupDraftSelection.forEach(id => fd.append('participant_ids[]', id));

if (this.groupDraftAvatarFile) fd.append('avatar', this.groupDraftAvatarFile);

fetch(routeUrl, {

method: 'POST',

headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },

body: fd,

}).then(async r => {

const d = await r.json();

if (!d.success) throw new Error(d.message || 'فشل الإنشاء');

return d;

})

.then(d => {

this.closeNewConversationModal();

// Add the new group to the contacts list and select it

const newGroup = {

id: `group_${d.data.id}`,

_groupId: d.data.id,

name: d.data.name,

avatar_url: d.data.avatarUrl ? this.normalizeAvatarUrl(d.data.avatarUrl) : null,

lastMessage: '',

lastMessageTime: null,

lastMessageStatusRefId: null,

unreadCount: 0,

isOnline: false,

lastSeenAt: null,

isGroup: true,

};

this.contacts.unshift(newGroup);

this.showToast(`تم إنشاء قروب "${d.data.name}" بنجاح \u2713`, 'success');

})

.catch(err => {

this.showToast(`فشل إنشاء القروب: ${err.message || 'خطأ غير متوقع'}`, 'error');

})

.finally(() => {

this.groupCreateLoading = false;

});

},

getMessageAuthorName(message) {

if (Number(message?.senderId) === Number(this.currentUserId)) return this.t.you;

const sender = this.sanitizeDisplayText(message?.senderName || '');

return sender || this.selectedContact?.name || this.t.previousMessage;

},

getAuthorInitial(name) {

const text = this.sanitizeDisplayText(name || '');

        return text ? Array.from(text)[0].toUpperCase() : '?';

},

formatMediaMetaTime(value) {

if (!value) return '';

const d = new Date(value);

if (Number.isNaN(d.getTime())) return '';

const n = new Date();

const n0 = new Date(n.getFullYear(), n.getMonth(), n.getDate());

const d0 = new Date(d.getFullYear(), d.getMonth(), d.getDate());

const dayDiff = Math.floor((n0 - d0) / 86400000);

const time = d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });

if (dayDiff === 0) return `${this.dayLabel(d)} - ${time}`;

if (dayDiff === 1) return `\u0623\u0645\u0633 - ${time}`;

return `${d.toLocaleDateString('ar-SA-u-ca-gregory', { month: 'short', day: 'numeric' })} - ${time}`;

},

formatBlockedTime(ts) {

if (!ts) return '';
const n = Date.now();
const diff = Math.floor((n - ts) / 86400000);
if (diff === 0) return 'منذ اليوم';
if (diff === 1) return 'منذ يوم';
if (diff < 7) return 'منذ ' + diff + ' أيام';
if (diff < 30) return 'منذ ' + Math.floor(diff / 7) + ' أسابيع';
return 'منذ ' + Math.floor(diff / 30) + ' شهر';

},

formatContactLastSeen(contact) {
if (!contact) return '';
const ts = contact.lastSeenAt || contact.last_seen_at || null;
if (!ts) return contact.isOnline ? 'متصل الآن' : '';
if (contact.isOnline) return 'متصل الآن';
const d = new Date(ts);
if (Number.isNaN(d.getTime())) return '';
const now = new Date();
const diff = now - d;
const days = Math.floor(diff / 86400000);
if (days === 0) {
const hours = Math.floor(diff / 3600000);
if (hours === 0) {
const mins = Math.floor(diff / 60000);
return mins <= 1 ? 'منذ لحظات' : 'منذ ' + mins + ' دقيقة';
}
return 'منذ ' + hours + ' ساعات';
}
if (days === 1) return 'آخر ظهور أمس ' + d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });
if (days < 7) return 'آخر ظهور ' + d.toLocaleDateString('ar-SA', { weekday: 'long' }) + ' ' + d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });
return 'آخر ظهور ' + d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'numeric', day: 'numeric' });
},

formatMessageTime(value) {

if (!value) return '';

const d = new Date(value);

if (Number.isNaN(d.getTime())) return '';

return d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: 'Asia/Riyadh' });

},

dayLabel(value) {

if (!value) return '\u0627\u0644\u064a\u0648\u0645';

const d = new Date(value);

if (Number.isNaN(d.getTime())) return '\u0627\u0644\u064a\u0648\u0645';

// Compare calendar dates in Riyadh timezone
const tz = 'Asia/Riyadh';
const fmt = new Intl.DateTimeFormat('en-CA', { timeZone: tz, year: 'numeric', month: '2-digit', day: '2-digit' });
const todayStr = fmt.format(new Date());
const dStr = fmt.format(d);
if (dStr === todayStr) return '\u0627\u0644\u064a\u0648\u0645';
const todayMs = new Date(todayStr).getTime();
const dMs = new Date(dStr).getTime();
const diff = Math.floor((todayMs - dMs) / 86400000);
if (diff === 1) return '\u0623\u0645\u0633';

return d.toLocaleDateString('ar-SA-u-ca-gregory', { weekday: 'long', month: 'short', day: 'numeric', timeZone: tz });

},

showDayDivider(idx) {

if (idx === 0) return true;

return this.dayLabel(this.messages[idx - 1]?.createdAt) !== this.dayLabel(this.messages[idx]?.createdAt);

},

firstUnreadIncomingIndex() {

if (this.unreadRemainingCount <= 0) return -1;

const byReadAt = this.messages.findIndex(m => Number(m.recipientId) === Number(this.currentUserId) && !m.readAt);

if (byReadAt !== -1) return byReadAt;

if (this.initialUnreadSnapshot > 0) {

let seenIncoming = 0;

for (let i = this.messages.length - 1; i >= 0; i -= 1) {

const m = this.messages[i];

if (Number(m.recipientId) !== Number(this.currentUserId)) continue;

seenIncoming += 1;

if (seenIncoming >= this.initialUnreadSnapshot) return i;

}

}

return -1;

},

shouldShowUnreadDivider(idx) {

if (this.unreadRemainingCount <= 0) return false;

const first = this.firstUnreadIncomingIndex();

return first !== -1 && idx === first;

},

unreadIncomingCount() {

return this.messages.filter(m => Number(m.recipientId) === Number(this.currentUserId) && !m.readAt).length;

},

rebuildUnreadTracker() {

this.unreadReadIds = [];

this.unreadRemainingCount = this.unreadIncomingCount() || this.initialUnreadSnapshot || 0;

},

updateUnreadProgress() {

const c = this.$refs.messagesContainer;

if (!c || !this.messages.length || this.unreadRemainingCount <= 0) return;

const cRect = c.getBoundingClientRect();

let changed = false;

this.messages.forEach(m => {

if (Number(m.recipientId) !== Number(this.currentUserId) || m.readAt) return;

if (this.unreadReadIds.includes(m.id)) return;

const el = document.getElementById(`message-${m.id}`);

if (!el) return;

const r = el.getBoundingClientRect();

const seen = r.top < (cRect.bottom - 30) && r.bottom > (cRect.top + 30);

if (seen) {

this.unreadReadIds.push(m.id);

changed = true;

}

});

if (changed) {

const baseline = this.unreadIncomingCount() || this.initialUnreadSnapshot || 0;

this.unreadRemainingCount = Math.max(0, baseline - this.unreadReadIds.length);

if (this.unreadRemainingCount === 0) {

this.initialUnreadSnapshot = 0;

if (this.selectedContact) this.selectedContact.unreadCount = 0;

}

}

},

jumpToUnread() {

if (!this.selectedContact || this.unreadRemainingCount <= 0) return;

const el = document.getElementById(`unread-separator-${this.selectedContact.id}`);

if (el) el.scrollIntoView({ block: 'center', behavior: 'smooth' });

},

handleFloatingJump() {

if (this.unreadRemainingCount > 0) {

this.jumpToUnread();

return;

}

this.scrollToBottom(true);

},

showRecordHintBriefly() {

if (this.isRecording || this.holdActive || this.isRecordingLocked || this.showSendAction) return;

this.showRecordHint = true;

if (this.recordHintTimer) clearTimeout(this.recordHintTimer);

this.recordHintTimer = setTimeout(() => { this.showRecordHint = false; }, 2100);

},

selectionStorageKey() {

return `messaging_selected_contact_id_${window.location.pathname}`;

},

scrollStorageKey(contactId) {

return `messaging_scroll_${window.location.pathname}_${contactId}`;

},

scrollStorageVersion() {

return 2;

},

saveCurrentScrollPosition() {

if (!this.selectedContact) return;

const c = this.$refs.messagesContainer;

if (!c) return;

const payload = {

v: this.scrollStorageVersion(),

top: c.scrollTop,

fromBottom: Math.max(0, c.scrollHeight - c.scrollTop - c.clientHeight),

savedAt: Date.now(),

};

localStorage.setItem(this.scrollStorageKey(this.selectedContact.id), JSON.stringify(payload));

},

restoreSavedScrollPosition() {

if (!this.selectedContact) return false;

const raw = localStorage.getItem(this.scrollStorageKey(this.selectedContact.id));

if (!raw) return false;

try {

const saved = JSON.parse(raw);

if (Number(saved?.v || 1) !== this.scrollStorageVersion()) {

localStorage.removeItem(this.scrollStorageKey(this.selectedContact.id));

return false;

}

const c = this.$refs.messagesContainer;

if (!c) return false;

const fromBottom = Number(saved?.fromBottom ?? NaN);

const top = Number(saved?.top ?? NaN);

if (Number.isFinite(fromBottom)) {

c.scrollTop = Math.max(0, c.scrollHeight - c.clientHeight - fromBottom);

return true;

}

if (Number.isFinite(top)) {

c.scrollTop = Math.max(0, Math.min(top, c.scrollHeight));

return true;

}

} catch (_) {}

return false;

},

selectContact(contact) {

if (Number(contact.id) === -1) { this.openSavedChat(); return; }

this.saveCurrentScrollPosition();

this.stopAndResetAllAudioPlayers();

// Stop any running delta timer for the previous contact

if (this.refreshTimer) { clearTimeout(this.refreshTimer); this.refreshTimer = null; }

this.sendTypingState(false);

this.selectedContact = contact;

this.activeLoadRecipientId = Number(contact.id);

this.messages = [];

this.lastFeedScrollTop = 0;

// Reset delta state for new contact

this.lastKnownMessageId = 0;

this.lastDeltaTime = null;

this.deltaRetryCount = 0;

this.deltaInFlight = false;

this.initialUnreadSnapshot = Number(contact?.unreadCount || 0);

this.rebuildUnreadTracker();

this.replyingToMessage = null;

this.pendingAttachments = [];

this.messageInput = '';

this.showEmojiPicker = false;

this.showRecordHint = false;

if (this.recordHintTimer) { clearTimeout(this.recordHintTimer); this.recordHintTimer = null; }

localStorage.setItem(this.selectionStorageKey(), String(contact.id));

localStorage.setItem('messaging_selected_contact_id', String(contact.id));

this.cancelRecording(true);

if (window.innerWidth <= 1080) this.showSidebar = false;

this.loadWallpaperForContact(contact.id);

this.$nextTick(() => this.applyChatThemeVars());

// Fetch partner E2E key for this contact
this.fetchPartnerE2EKey(contact.id);

// Full load for initial contact switch, then start SSE

return this.loadMessages(true).then(() => {

this.startSSE();

this.scheduleDeltaRefresh();

this.injectCallMessages();

});

},

openConversation(contact) {

return this.selectContact(contact) || Promise.resolve();

},

resolveMessageUrl(tpl, id) { return tpl.replace('__MESSAGE_ID__', String(id)); },

compareMessages(a, b) {

const ad = Date.parse(a?.createdAt || a?.created_at || 0) || 0;

const bd = Date.parse(b?.createdAt || b?.created_at || 0) || 0;

if (ad !== bd) return ad - bd;

return Number(a?.id || 0) - Number(b?.id || 0);

},

normalizeMessageOrder() {

this.messages = [...(this.messages || [])].sort((a, b) => this.compareMessages(a, b));

},

enqueueSend(taskFn) {

this.sendQueue = (this.sendQueue || Promise.resolve())

.then(() => taskFn())

.catch(() => {});

return this.sendQueue;

},

markMessageFailed(pendingId, reason) {

const idx = this.messages.findIndex(m => Number(m.id) === Number(pendingId));

if (idx === -1) return;

this.messages[idx].pending = false;

this.messages[idx].failed = true;

if (reason) this.showToast(reason, 'error');

},

async retryFailedMessage(message) {

if (!message || !message.failed) return;

const retry = message._retry;

// Remove the failed bubble; the resend path re-creates a fresh optimistic one.

this.messages = this.messages.filter(m => Number(m.id) !== Number(message.id));

if (retry && retry.kind === 'text') {

const recipientId = Number(retry.recipientId || this.selectedContact?.id || 0);

if (!recipientId) return;

const pendingId = this.pendingMessageCounter--;

const pendingMessage = this.normalizeMessage({

id: pendingId,

sender_id: this.currentUserId,

recipient_id: recipientId,

content: retry.text,

created_at: new Date().toISOString(),

read_at: null,

});

pendingMessage.pending = true;

pendingMessage.messageType = 'text';

pendingMessage._retry = retry;

this.messages.push(pendingMessage);

this.normalizeMessageOrder();

this.scrollToBottom(true);

await this.enqueueSend(async () => {

try {

const r = await fetch(@json($sendRoute), {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ recipient_id: recipientId, content: retry.text, reply_to: retry.replyToId || null }),

});

if (r.status === 429) { this.markMessageFailed(pendingId, this.t.rateLimited || 'أنت ترسل بسرعة كبيرة، انتظر لحظة ثم أعد المحاولة'); return; }

const d = await r.json();

if (d.success) this.replacePendingWithServerMessage(pendingId, d.data, recipientId);

else this.markMessageFailed(pendingId, d.message || (this.t.sendFailed || 'تعذر إرسال الرسالة'));

} catch (_) {

this.markMessageFailed(pendingId, this.t.sendFailed || 'تعذر إرسال الرسالة. تحقق من اتصالك');

}

});

this.updateContactPreview();

} else if (retry && retry.kind === 'file' && retry.file) {

await this.enqueueSend(async () => this.sendFileMessage(retry.file));

} else {

this.showToast(this.t.retryUnavailable || 'تعذر إعادة الإرسال، أعد كتابة الرسالة', 'warning');

}

},

resolveServerMessagePayload(payload, recipientId) {

return {

...payload,

senderId: payload?.senderId ?? payload?.sender_id ?? this.currentUserId,

recipientId: payload?.recipientId ?? payload?.recipient_id ?? recipientId,

};

},

replacePendingWithServerMessage(pendingId, payload, recipientId) {

const pendingIdx = this.messages.findIndex(m => Number(m.id) === Number(pendingId));

if (pendingIdx === -1) return;

const pendingMessage = this.messages[pendingIdx];

if (pendingMessage && pendingMessage.replyTo && !payload.replyTo) {

payload.replyTo = pendingMessage.replyTo;

}

const msgType = pendingMessage ? pendingMessage.messageType : null;

const normalized = this.normalizeMessage(this.resolveServerMessagePayload(payload, recipientId));

normalized.pending = false;

if (msgType && normalized.messageType !== msgType) {
    normalized.messageType = msgType;
}

// Preserve plaintext from optimistic bubble (server stores ciphertext when E2E is on)
if (normalized.content?.startsWith('e2e:') && pendingMessage?.content && !pendingMessage.content.startsWith('e2e:')) {
normalized.content = pendingMessage.content;
}

if (String(pendingMessage?.attachmentUrl || '').startsWith('blob:') && pendingMessage.attachmentUrl !== normalized.attachmentUrl) {

URL.revokeObjectURL(pendingMessage.attachmentUrl);

}

this.messages.splice(pendingIdx, 1);

const existingIdx = this.messages.findIndex(m => Number(m.id) === Number(normalized.id));

if (existingIdx !== -1) this.messages.splice(existingIdx, 1, normalized);

else this.messages.push(normalized);

this.normalizeMessageOrder();

},

handleComposeEnter(e) {
    const sendWithEnter = this.settingsChats.sendWithEnter !== false;
    const wantsSend = sendWithEnter ? !e.shiftKey : e.ctrlKey;
    if (!wantsSend) return; // allow default behavior (newline)
    e.preventDefault();
    this.sendMessage();
},

async sendMessage() {

if (!this.canSendMessage) return;

const text = this.messageInput.trim();

this.messageInput = '';

this.showEmojiPicker = false;

this.showRecordHint = false;

const recipientId = Number(this.selectedContact?.id || 0);

if (!recipientId) return;

if (text) {

const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: recipientId,
content: text,
replyTo: this.replyingToMessage || null,
created_at: new Date().toISOString(),
read_at: null,
messageType: 'text',
});

this.messages.push(msg);
this.normalizeMessageOrder();
this.scrollToBottom(true);

// Saved messages: store locally
if (Number(recipientId) === -1) {
this.saveSavedMessage(msg);
this.replyingToMessage = null;
this.pendingAttachments = [];
this.updateContactPreview();
this.scrollToBottom(true);
return;
}

msg.pending = true;
msg._retry = { kind: 'text', recipientId, text, replyToId: this.replyingToMessage?.id || null };

await this.enqueueSend(async () => {

try {

const r = await fetch(@json($sendRoute), {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({

recipient_id: recipientId,

content: await this._e2eEncrypt(text),

reply_to: this.replyingToMessage?.id || null,

}),

});

if (r.status === 429) {

this.markMessageFailed(msg.id, this.t.rateLimited || 'أنت ترسل بسرعة كبيرة، انتظر لحظة ثم أعد المحاولة');

return;

}

const d = await r.json();

if (d.success) this.replacePendingWithServerMessage(msg.id, d.data, recipientId);

else this.markMessageFailed(msg.id, d.message || (this.t.sendFailed || 'تعذر إرسال الرسالة'));

} catch (_) {

this.markMessageFailed(msg.id, this.t.sendFailed || 'تعذر إرسال الرسالة. تحقق من اتصالك');

}

});

}

if (this.pendingAttachments.length) {

const files = [...this.pendingAttachments];

for (const f of files) {

await this.enqueueSend(async () => this.sendFileMessage(f.file));

}

this.clearAllAttachments();

}

this.replyingToMessage = null;

this.updateContactPreview();

this.scrollToBottom(true);

},

goBackToDashboard() {

if (window.history.length > 1) {

window.history.back();

return;

}

window.location.href = @json(route('student.index'));

},

async sendAudioBlob(blob, duration) {

if (!blob || !this.selectedContact) return;

const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: this.selectedContact.id,
content: '\u0631\u0633\u0627\u0644\u0629 \u0635\u0648\u062a\u064a\u0629',
messageType: 'audio',
attachment_type: 'audio/webm',
duration: Math.round(duration || 0),
replyTo: this.replyingToMessage || null,
created_at: new Date().toISOString(),
read_at: null,
});

this.messages.push(msg);
this.scrollToBottom(true);

// Saved messages: store audio as data URI
if (Number(this.selectedContact.id) === -1) {
const reader = new FileReader();
reader.onload = (e) => {
msg.attachmentUrl = e.target.result;
msg.attachmentType = 'audio/webm';
msg.pending = false;
this.saveSavedMessage(msg);
this.replyingToMessage = null;
this.scrollToBottom(true);
};
reader.readAsDataURL(blob);
return;
}

msg.pending = true;

const fd = new FormData();

fd.append('recipient_id', this.selectedContact.id);

fd.append('audio', blob, 'audio_message.webm');

fd.append('duration', duration || 1);

if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);

try {

const r = await fetch(@json($audioRoute), { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });

if (r.status === 429) {

this.markMessageFailed(msg.id, this.t.rateLimited || 'أنت ترسل بسرعة كبيرة، انتظر لحظة ثم أعد المحاولة');

return;

}

const d = await r.json();

if (d.success) {

this.replacePendingWithServerMessage(msg.id, d.data, this.selectedContact.id);

this.replyingToMessage = null;

this.updateContactPreview();

this.scrollToBottom(true);

} else {

this.markMessageFailed(msg.id, (d && d.message) || (this.t.uploadFailed || 'تعذر إرسال الرسالة الصوتية'));

}

} catch (_) {

this.markMessageFailed(msg.id, this.t.uploadFailed || 'تعذر إرسال الرسالة الصوتية. تحقق من اتصالك');

}

},

async sendFileMessage(file) {

if (!file || !this.selectedContact) return;

const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: this.selectedContact.id,
content: file.name || '\u0645\u0631\u0641\u0642',
attachment_name: file.name || '\u0645\u0631\u0641\u0642',
attachment_type: file.type || 'application/octet-stream',
replyTo: this.replyingToMessage || null,
created_at: new Date().toISOString(),
read_at: null,
});

    msg.messageType = this.getMessageType(msg);
if (file.type?.startsWith('image/') || file.type?.startsWith('video/')) {
msg.attachmentUrl = URL.createObjectURL(file);
}

// Saved messages: store file as data URI
if (Number(this.selectedContact.id) === -1) {
const reader = new FileReader();
reader.onload = (e) => {
msg.attachmentUrl = e.target.result;
msg.pending = false;
this.saveSavedMessage(msg);
this.scrollToBottom(true);
};
reader.readAsDataURL(file);
return;
}

msg.pending = true;
msg._retry = { kind: 'file', file };

this.messages.push(msg);
this.scrollToBottom(true);

const fd = new FormData();

fd.append('recipient_id', this.selectedContact.id);

fd.append('file', file);

if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);
if (this.pendingAttachmentsSensitive) fd.append('is_sensitive', '1');

const itemIndex = this.pendingAttachments.findIndex(a => a.file === file);

if (itemIndex !== -1) {

this.pendingAttachments[itemIndex].uploading = true;

this.pendingAttachments[itemIndex].progress = 1;

}

this.activeUploadCount += 1;

try {

const d = await this.uploadWithProgress(@json($fileRoute), fd, itemIndex);

if (d.success) {

this.replacePendingWithServerMessage(msg.id, d.data, this.selectedContact.id);

} else {

this.markMessageFailed(msg.id, (d && d.message) || (this.t.uploadFailed || 'تعذر رفع المرفق'));

}

} catch (_) {

this.markMessageFailed(msg.id, this.t.uploadFailed || 'تعذر رفع المرفق. تحقق من اتصالك');

} finally {

if (itemIndex !== -1 && this.pendingAttachments[itemIndex]) {

this.pendingAttachments[itemIndex].uploading = false;

this.pendingAttachments[itemIndex].progress = 100;

}

this.activeUploadCount = Math.max(0, this.activeUploadCount - 1);

}

},

uploadWithProgress(url, formData, pendingIndex = -1) {

return new Promise((resolve, reject) => {

const xhr = new XMLHttpRequest();

xhr.open('POST', url, true);

xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);

xhr.setRequestHeader('Accept', 'application/json');

xhr.upload.onprogress = (event) => {

if (!event.lengthComputable || pendingIndex === -1 || !this.pendingAttachments[pendingIndex]) return;

const p = Math.max(1, Math.min(99, Math.round((event.loaded / event.total) * 100)));

this.pendingAttachments[pendingIndex].progress = p;

};

xhr.onload = () => {

try {

const json = JSON.parse(xhr.responseText || '{}');

if (pendingIndex !== -1 && this.pendingAttachments[pendingIndex]) {

this.pendingAttachments[pendingIndex].progress = 100;

}

resolve(json);

} catch (e) {

reject(e);

}

};

xhr.onerror = () => reject(new Error('upload failed'));

xhr.send(formData);

});

},

updateContactPreview() {

if (!this.selectedContact || !this.messages.length) return;

const last = this.messages[this.messages.length - 1];

this.selectedContact.lastMessage = this.getMessagePreviewText(last);

this.selectedContact.lastMessageTime = last.createdAt || last.created_at || this.selectedContact.lastMessageTime;

this.selectedContact.lastMessageStatusRefId = last.statusRefId || null;

const ci = this.contacts.findIndex(c => Number(c.id) === Number(this.selectedContact.id));

if (ci !== -1) {

this.contacts[ci].lastMessage = this.selectedContact.lastMessage;

this.contacts[ci].lastMessageTime = this.selectedContact.lastMessageTime;

this.contacts[ci].lastMessageStatusRefId = this.selectedContact.lastMessageStatusRefId;

} else if (this.selectedContact.lastMessageTime) {

this.contacts.unshift({ ...this.selectedContact });

this.potentialNewContacts = this.potentialNewContacts.filter(c => Number(c.id) !== Number(this.selectedContact.id));

}

},

async loadMessages(initial = false) {

if (!this.selectedContact) return;

this.stopSSE(); // Stop SSE during full load

const requestRecipientId = Number(this.selectedContact.id);

const c = this.$refs.messagesContainer;

const previousOffsetFromBottom = c ? Math.max(0, c.scrollHeight - c.scrollTop - c.clientHeight) : 0;

const ctl = new AbortController();

const t = setTimeout(() => ctl.abort(), 12000);

let d = { success: false };

try {

const loadBaseUrl = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.load') ? route('teacher.messaging.load') : null)

: (\Illuminate\Support\Facades\Route::has('messaging.load')

? route('messaging.load')

: (\Illuminate\Support\Facades\Route::has('student.messaging.load') ? route('student.messaging.load') : null))

);

if (!loadBaseUrl) {

this.showToast('Messaging load route is not configured.', 'error');

return;

}

const r = await fetch(`${loadBaseUrl}?recipient_id=${requestRecipientId}&page=1`, {

method: 'GET',

headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },

signal: ctl.signal,

});

d = await r.json();

} catch (_) {

this.showToast('تعذر تحميل الرسائل حاليا', 'error');

} finally {

clearTimeout(t);

}

if (!d.success) return;

if (!this.selectedContact || Number(this.selectedContact.id) !== requestRecipientId) return;

if (d.data?.contact && this.selectedContact && Number(this.selectedContact.id) === Number(d.data.contact.id)) {

this.selectedContact.isOnline = !!d.data.contact.isOnline;

this.selectedContact.isTyping = !!d.data.contact.isTyping;

this.selectedContact.lastSeen = this.sanitizeDisplayText(d.data.contact.lastSeen, this.selectedContact.lastSeen || this.t.unavailable);

this.selectedContact.lastSeenAt = d.data.contact.lastSeenAt || this.selectedContact.lastSeenAt;

const contactIdx = this.contacts.findIndex(c => Number(c.id) === Number(this.selectedContact.id));

if (contactIdx !== -1) {

this.contacts[contactIdx].isOnline = this.selectedContact.isOnline;

this.contacts[contactIdx].isTyping = this.selectedContact.isTyping;

this.contacts[contactIdx].lastSeen = this.selectedContact.lastSeen;

this.contacts[contactIdx].lastSeenAt = this.selectedContact.lastSeenAt;

}

}

const wasNearBottom = this.isNearBottom();

const playbackState = new Map(this.messages.map(m => [Number(m.id), {

isPlaying: m.isPlaying,

currentBar: m.currentBar || 0,

playbackPosition: m.playbackPosition || 0,

_resumePos: m._resumePos || 0,

mediaLoaded: !!m.mediaLoaded,

}]));

const rawMessages = Array.isArray(d?.data?.messages) ? d.data.messages : [];

this.historyPage = Number(d?.data?.pagination?.current_page || 1);

this.historyLastPage = Number(d?.data?.pagination?.last_page || 1);

const orderedMessages = [...rawMessages].reverse();

this.messages = orderedMessages.map(raw => {

const msg = this.normalizeMessage(raw);

const old = playbackState.get(Number(msg.id));

if (old) {

msg.isPlaying = !!old.isPlaying;

msg.currentBar = old.currentBar;

msg.playbackPosition = old.playbackPosition;

msg._resumePos = old._resumePos;

if (old.mediaLoaded) msg.mediaLoaded = true;

}

return msg;

});

this.normalizeMessageOrder();

this._decryptConversationMessages();

// Set delta tracking state after initial load

if (this.messages.length) {

this.lastKnownMessageId = Math.max(...this.messages.filter(m => m.id > 0).map(m => m.id), 0);

} else {

this.lastKnownMessageId = 0;

}

this.lastDeltaTime = new Date().toISOString();

this.deltaRetryCount = 0;

this.rebuildUnreadTracker();

this.$nextTick(() => {

if (initial) {

if (!this.restoreSavedScrollPosition()) {

this.scrollToUnreadOrBottom();

}

} else if (wasNearBottom) {

this.scrollToBottom(false);

} else {

const box = this.$refs.messagesContainer;

if (box) box.scrollTop = Math.max(0, box.scrollHeight - box.clientHeight - previousOffsetFromBottom);

}

const box = this.$refs.messagesContainer;

this.lastFeedScrollTop = box ? Number(box.scrollTop || 0) : 0;

this.onFeedScroll();

this.updateUnreadProgress();

this.updateFloatingDayIndicator(false);

});

// SSE and delta polling are started by the caller (selectContact)

},

async loadOlderMessages() {

if (!this.selectedContact || this.historyLoading) return;

if (Number(this.historyPage) >= Number(this.historyLastPage)) return;

this.historyLoading = true;

const requestRecipientId = Number(this.selectedContact.id);

const nextPage = Number(this.historyPage) + 1;

const box = this.$refs.messagesContainer;

const prevHeight = box ? box.scrollHeight : 0;

try {

const loadBaseUrl = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.load') ? route('teacher.messaging.load') : null)

: (\Illuminate\Support\Facades\Route::has('messaging.load')

? route('messaging.load')

: (\Illuminate\Support\Facades\Route::has('student.messaging.load') ? route('student.messaging.load') : null))

);

if (!loadBaseUrl) return;

const r = await fetch(`${loadBaseUrl}?recipient_id=${requestRecipientId}&page=${nextPage}`, {

method: 'GET',

headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },

});

const d = await r.json();

if (!d?.success || !this.selectedContact || Number(this.selectedContact.id) !== requestRecipientId) return;

const rawMessages = Array.isArray(d?.data?.messages) ? d.data.messages : [];

const ordered = [...rawMessages].reverse().map(raw => this.normalizeMessage(raw));

const existingIds = new Set(this.messages.map(m => Number(m.id)));

const prepend = ordered.filter(m => !existingIds.has(Number(m.id)));

if (prepend.length) {

this.messages = [...prepend, ...this.messages];

this.normalizeMessageOrder();

this.historyPage = Number(d?.data?.pagination?.current_page || nextPage);

this.historyLastPage = Number(d?.data?.pagination?.last_page || this.historyLastPage);

this.$nextTick(() => {

const newBox = this.$refs.messagesContainer;

if (!newBox) return;

const newHeight = newBox.scrollHeight;

newBox.scrollTop = Math.max(0, newHeight - prevHeight + newBox.scrollTop);

});

}

} catch (_) {

// silent

} finally {

this.historyLoading = false;

}

},

/**

* Delta refresh: only fetch new/updated/deleted messages since lastKnownMessageId.

* This replaces the old full-refresh polling and reduces server load by ~95%.

*/

autoResizeTextarea() {
    const el = this.$refs.messageInput;
    if (!el) return;
    el.style.height = '44px';
    const scrollH = el.scrollHeight;
    this.textareaHeight = Math.min(180, Math.max(44, scrollH));
},

notifyTyping() {

if (!this.selectedContact || !this.typingRoute || this.typingRoute === '#') return;
if (Number(this.selectedContact.id) === -1) return; // skip for saved messages

if (!this.typingPingTimer) {

this.sendTypingState(true);

this.typingPingTimer = setTimeout(() => { this.typingPingTimer = null; }, 2500);

}

if (this.typingStopTimer) clearTimeout(this.typingStopTimer);

this.typingStopTimer = setTimeout(() => this.sendTypingState(false), 3500);

},

sendTypingState(isTyping) {

if (!this.selectedContact || !this.typingRoute || this.typingRoute === '#') return;

fetch(this.typingRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ recipient_id: Number(this.selectedContact.id), is_typing: !!isTyping }),

}).catch(() => {});

},

async deltaRefresh() {

if (!this.selectedContact || this.deltaInFlight) return;
if (Number(this.selectedContact.id) === -1) return; // skip delta for saved messages

const baseUrl = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.delta') ? route('teacher.messaging.delta') : null)

: (\Illuminate\Support\Facades\Route::has('messaging.delta')

? route('messaging.delta')

: (\Illuminate\Support\Facades\Route::has('student.messaging.delta') ? route('student.messaging.delta') : null))

);

if (!baseUrl) return;

const recipientId = Number(this.selectedContact.id);

this.deltaInFlight = true;

try {

const qs = new URLSearchParams({

recipient_id: String(recipientId),

since_id: String(Math.max(0, Number(this.lastKnownMessageId || 0))),

mark_read: '1',

});

if (this.lastDeltaTime) qs.set('last_sync', this.lastDeltaTime);

const r = await fetch(`${baseUrl}?${qs.toString()}`, {

method: 'GET',

headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },

});

if (!r.ok) throw new Error('delta_failed');

const d = await r.json();

if (!d?.success || !this.selectedContact || Number(this.selectedContact.id) !== recipientId) return;

const incoming = Array.isArray(d?.data?.new) ? d.data.new : [];

let changed = false;

for (const raw of incoming) {

const msg = this.normalizeMessage(raw);

if (!this.messages.some(m => Number(m.id) === Number(msg.id))) {

if (msg.content?.startsWith('e2e:') && window._e2ePartnerKey) {
this._e2eDecrypt(msg.content, window._e2ePartnerKey).then(plain => {
const idx = this.messages.findIndex(m => Number(m.id) === Number(msg.id));
if (idx !== -1) this.messages[idx].content = plain;
});
}

this.messages.push(msg);

this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(msg.id || 0));

changed = true;

// Play notification tone for incoming messages
if (this.selectedContact && Number(msg.senderId) !== Number(this.currentUserId)) {
this.playNotificationTone(this.selectedContact.id);
this.maybeShowDesktopNotification(this.selectedContact, msg);
}

}

const updated = Array.isArray(d?.data?.updated) ? d.data.updated : [];

for (const patch of updated) {

const idx = this.messages.findIndex(m => Number(m.id) === Number(patch.id));

if (idx >= 0) {

const prev = this.messages[idx]; this.messages[idx] = { ...prev, ...patch, isEdited: !!(patch.isEdited ?? prev.isEdited), isPlaying: prev.isPlaying, currentBar: prev.currentBar, playbackPosition: prev.playbackPosition, _resumePos: prev._resumePos, audioPosition: patch.audioPosition ?? prev.audioPosition ?? 0 };

changed = true;

}

}

const deleted = Array.isArray(d?.data?.deleted) ? d.data.deleted.map(Number) : [];

if (deleted.length) {

const deletedSet = new Set(deleted);

this.messages = this.messages.filter(m => !deletedSet.has(Number(m.id)));

changed = true;

}

const newlyRead = Array.isArray(d?.data?.newly_read) ? d.data.newly_read : [];

for (const item of newlyRead) {

const msg = this.messages.find(m => Number(m.id) === Number(item.id));

if (msg) {

msg.readAt = item.readAt || msg.readAt || new Date().toISOString();

changed = true;

}

}

const reactionChanges = Array.isArray(d?.data?.reactions) ? d.data.reactions : [];

for (const item of reactionChanges) {

const msg = this.messages.find(m => Number(m.id) === Number(item.id));

if (msg) {

msg.reactions = item.reactions || [];

changed = true;

}

}

if (changed) {

this.normalizeMessageOrder();

this.updateContactPreview();

this.rebuildUnreadTracker();

this.$nextTick(() => {

if (this.isNearBottom()) this.scrollToBottom(false);

this.updateUnreadProgress();

this.updateFloatingDayIndicator(false);

});

}

if (d?.data?.contact && this.selectedContact && Number(this.selectedContact.id) === Number(d.data.contact.id)) {

this.selectedContact.isOnline = !!d.data.contact.isOnline;

this.selectedContact.isTyping = !!d.data.contact.isTyping;

this.selectedContact.lastSeenAt = d.data.contact.lastSeenAt || this.selectedContact.lastSeenAt;

this.selectedContact.lastSeen = this.selectedContact.isOnline ? this.t.onlineNow : this.formatLastSeen(this.selectedContact);

}

if (d?.data?.server_time) this.lastDeltaTime = d.data.server_time;

this.deltaRetryCount = 0;

} // closes for (const raw of incoming)

} catch (_) {

this.deltaRetryCount = Number(this.deltaRetryCount || 0) + 1;

} finally {

this.deltaInFlight = false;

}

},

startSSE() {

// Warning: SSE disabled: php artisan serve is single-threaded.

// An open EventSource permanently occupies the only PHP thread,

// causing ALL image/audio/video requests to time out.

// Delta polling (scheduleDeltaRefresh) handles new messages instead.

if (this.eventSource) {

this.eventSource.close();

this.eventSource = null;

}

// Kick off delta polling immediately

this.scheduleDeltaRefresh();

},

stopSSE() {

if (this.eventSource) {

this.eventSource.close();

this.eventSource = null;

}

if (this.sseReconnectTimer) {

clearTimeout(this.sseReconnectTimer);

this.sseReconnectTimer = null;

}

},

/**

* Returns the adaptive polling interval based on user activity.

* 2s when active + near bottom, 3s when active + scrolled up, 8s when tab hidden.

*/

getAdaptiveDeltaInterval() {

if (document.hidden) return 8000;

if (!this.isNearBottom()) return 3000;

return 2000;

},

scheduleDeltaRefresh() {

if (!this.selectedContact) return;

if (this.refreshTimer) clearTimeout(this.refreshTimer);

this.refreshTimer = setTimeout(() => {

this.deltaRefresh();

this.scheduleDeltaRefresh();

}, Math.max(3500, this.getAdaptiveDeltaInterval() * 2));

},

scrollToUnreadOrBottom() {

this.$nextTick(() => {

const doScroll = () => {

const firstUnreadIdx = this.firstUnreadIncomingIndex();

if (firstUnreadIdx !== -1 && this.selectedContact) {

const el = document.getElementById(`unread-separator-${this.selectedContact.id}`);

if (el) {

el.scrollIntoView({ block: 'center', behavior: 'smooth' });

return;

}

}

this.scrollToBottom(false);

};

doScroll();

setTimeout(doScroll, 120);

setTimeout(doScroll, 420);

});

},

scrollToBottom(smooth = true) {

this.$nextTick(() => {

const c = this.$refs.messagesContainer;

if (!c) return;

c.scrollTo({ top: c.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });

this.showJumpToLatest = false;

});

},

isNearBottom() {

const c = this.$refs.messagesContainer;

if (!c) return true;

return (c.scrollHeight - c.scrollTop - c.clientHeight) < 90;

},

onFeedScroll() {

const box = this.$refs.messagesContainer;

const currentTop = box ? Number(box.scrollTop || 0) : 0;

const isScrollingUp = currentTop < this.lastFeedScrollTop;

this.lastFeedScrollTop = currentTop;

this.isFeedScrolling = true;

if (this.scrollEndTimer) clearTimeout(this.scrollEndTimer);

this.scrollEndTimer = setTimeout(() => {

this.isFeedScrolling = false;

this.floatingDayLabel = '';

this.floatingDayIndex = -1;

}, 900);

this.showJumpToLatest = !this.isNearBottom();
if (this.isNearBottom() && this.unreadRemainingCount > 0) {
    this.unreadRemainingCount = 0;
    this.initialUnreadSnapshot = 0;
    if (this.selectedContact) this.selectedContact.unreadCount = 0;
}

this.saveCurrentScrollPosition();

this.updateUnreadProgress();

if (isScrollingUp) {

this.updateFloatingDayIndicator(true);

if (currentTop < 120) this.loadOlderMessages();

} else {

this.floatingDayLabel = '';

this.floatingDayIndex = -1;

}

},

updateFloatingDayIndicator(fromScroll = false) {

if (!fromScroll || !this.isFeedScrolling) return;

const c = this.$refs.messagesContainer;

if (!c || !this.messages.length) return;

const cRect = c.getBoundingClientRect();

let closestIdx = -1;

let closestTop = Number.POSITIVE_INFINITY;

this.messages.forEach((m, idx) => {

const el = document.getElementById(`message-${m.id}`);

if (!el) return;

const r = el.getBoundingClientRect();

const dist = Math.abs(r.top - cRect.top);

if (r.bottom >= cRect.top && r.top <= cRect.bottom && dist < closestTop) {

closestTop = dist;

closestIdx = idx;

}

});

if (closestIdx === -1) return;

const label = this.dayLabel(this.messages[closestIdx]?.createdAt);

if (!label || (label === this.floatingDayLabel && this.floatingDayIndex === closestIdx)) return;

this.floatingDayLabel = label;

this.floatingDayIndex = closestIdx;

if (this.floatingDayTimer) clearTimeout(this.floatingDayTimer);

this.floatingDayTimer = setTimeout(() => {

this.floatingDayLabel = '';

this.floatingDayIndex = -1;

}, 1400);

},

replyToMessage(message) {

this.replyingToMessage = message;

this.$nextTick(() => this.$refs.messageInput?.focus());

},

async editMessage(message) {

this.editTargetMessage = message;

this.editInputText = message.content || '';

},

closeEditModal() {

this.editTargetMessage = null;

this.editInputText = '';

},

async confirmEditMessage() {

const message = this.editTargetMessage;

if (!message) return;

const text = this.editInputText.trim();

if (!text || text === message.content) { this.closeEditModal(); return; }

const r = await fetch(this.resolveMessageUrl(this.updateRouteTemplate, message.id), {

method: 'PUT',

headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ content: text }),

});

const d = await r.json();

this.closeEditModal();

if (d.success) {

const i = this.messages.findIndex(m => m.id === message.id);

if (i !== -1) {

const merged = this.normalizeMessage({

...this.messages[i],

...d.data,

senderId: this.messages[i].senderId,

recipientId: this.messages[i].recipientId,

isEdited: true,

});

this.messages.splice(i, 1, merged);

}

}

},

async deleteMessage(message) {

this.deleteTargetMessage = message;

},

async confirmDeleteMessage() {

const message = this.deleteTargetMessage;

if (!message) return;

const r = await fetch(this.resolveMessageUrl(this.deleteRouteTemplate, message.id), {

method: 'DELETE',

headers: { 'X-CSRF-TOKEN': this.csrfToken },

});

const d = await r.json();

this.deleteTargetMessage = null;

if (d.success) {

this.messages = this.messages.filter(m => m.id !== message.id);

this.rebuildUnreadTracker();

}

},

openFilePicker() { this.$refs.fileInput?.click(); },

handleFileSelect(e) {

const files = Array.from(e.target.files || []);

files.forEach(file => {

if (file.size > 512 * 1024 * 1024) {

this.showToast('الملف كبير جداً (الحد الأقصى 512 MB)', 'error'); return;

}

// Open media editor for video and image files
if (file.type.startsWith('video/')) {
this.openVideoEditor(file);
return;
}
if (file.type.startsWith('image/')) {
this.openImageEditor(file);
return;
}
this.addPendingAttachment(file);

});

e.target.value = '';

},

addPendingAttachment(file) {

var pt = this.getMessageType({ attachmentMime: file.type, attachmentName: file.name });

const previewType = pt;

this.pendingAttachments.push({

file,

name: file.name,

mime: file.type || '',

previewType,

previewUrl: (previewType === 'image' || previewType === 'video') ? URL.createObjectURL(file) : null,

progress: 0,

uploading: false,

});

},

removeAttachment(i) {

const item = this.pendingAttachments[i];

if (item?.previewUrl) URL.revokeObjectURL(item.previewUrl);

this.pendingAttachments.splice(i, 1);

},

clearAllAttachments() {

this.pendingAttachments.forEach(i => { if (i.previewUrl) URL.revokeObjectURL(i.previewUrl); });

this.pendingAttachments = [];
this.pendingAttachmentsSensitive = false;

},

startHoldRecording(event) {

if (this.showSendAction || this.isRecording || this.holdActive) return;

if (event.target?.setPointerCapture) event.target.setPointerCapture(event.pointerId);

this.holdActive = true;

this.holdStartY = event.clientY;

this.holdTimer = setTimeout(() => {

this.beginRecording();

this.holdTimer = null;

}, 700);

},

onHoldRecordingMove(event) {

if (!this.holdActive || !this.isRecording || this.isRecordingLocked) return;

if ((this.holdStartY - event.clientY) > 70) this.isRecordingLocked = true;

},

onHoldRecordingEnd() {

if (this.holdTimer) {

clearTimeout(this.holdTimer);

this.holdTimer = null;

this.holdActive = false;

return;

}

this.holdActive = false;

if (this.isRecording && !this.isRecordingLocked) this.finishRecording('send');

},

async beginRecording() {

try {

const preferredMicId = localStorage.getItem('messaging_mic_device_id');
try {
this.recordingStream = await navigator.mediaDevices.getUserMedia({ audio: preferredMicId ? { deviceId: { ideal: preferredMicId } } : true });
} catch (innerErr) {
if (innerErr && (innerErr.name === 'OverconstrainedError' || innerErr.name === 'ConstraintNotSatisfiedError')) {
this.recordingStream = await navigator.mediaDevices.getUserMedia({ audio: true });
} else {
throw innerErr;
}
}

this.recordedChunks = [];

this.recordingAccumulatedMs = 0;

this.recordingDurationSec = 0;

this.recordingStartTime = Date.now();

this.recordStopMode = null;

this.isRecording = true;

this.isRecordingPaused = false;

this.mediaRecorder = new MediaRecorder(this.recordingStream, { mimeType: 'audio/webm;codecs=opus' });

this.mediaRecorder.ondataavailable = ev => { if (ev.data.size > 0) this.recordedChunks.push(ev.data); };

this.mediaRecorder.onstop = async () => {

const blob = new Blob(this.recordedChunks, { type: 'audio/webm' });

const dur = Math.max(1, Math.round(this.recordingAccumulatedMs / 1000));

if (this.recordStopMode === 'send') await this.sendAudioBlob(blob, dur);

this.resetRecordingState();

};

this.mediaRecorder.start();

this.startRecordingTimer();

} catch (err) {

this.showToast(this.describeMediaError(err), 'error');

this.resetRecordingState();

}

},

startRecordingTimer() {

clearInterval(this.recordingTimer);

this.recordingTimer = setInterval(() => {

if (!this.isRecording || this.isRecordingPaused) return;

this.recordingAccumulatedMs = this.recordingAccumulatedMs + (Date.now() - this.recordingStartTime);

this.recordingStartTime = Date.now();

this.recordingDurationSec = Math.max(1, Math.round(this.recordingAccumulatedMs / 1000));

}, 200);

},

togglePauseResumeRecording() {

if (!this.mediaRecorder || !this.isRecordingLocked || !this.isRecording) return;

if (this.isRecordingPaused) {

this.mediaRecorder.resume();

this.isRecordingPaused = false;

this.recordingStartTime = Date.now();

} else {

this.recordingAccumulatedMs = this.recordingAccumulatedMs + (Date.now() - this.recordingStartTime);

this.recordingDurationSec = Math.max(1, Math.round(this.recordingAccumulatedMs / 1000));

this.mediaRecorder.pause();

this.isRecordingPaused = true;

}

},

finishLockedRecording() {

this.finishRecording('send');

},

cancelRecording(silent = false) {

if (!this.isRecording && !this.holdActive) return;

if (silent) {

this.resetRecordingState();

return;

}

this.finishRecording('discard');

},

finishRecording(mode = 'send') {

if (!this.mediaRecorder || !this.isRecording) {

this.resetRecordingState();

return;

}

if (!this.isRecordingPaused) {

this.recordingAccumulatedMs = this.recordingAccumulatedMs + (Date.now() - this.recordingStartTime);

}

this.recordingDurationSec = Math.max(1, Math.round(this.recordingAccumulatedMs / 1000));

this.recordStopMode = mode;

try {

this.mediaRecorder.stop();

} catch (_) {

this.resetRecordingState();

}

},

resetRecordingState() {

clearInterval(this.recordingTimer);

this.recordingTimer = null;

this.isRecording = false;

this.isRecordingLocked = false;

this.isRecordingPaused = false;

this.holdActive = false;

this.holdStartY = null;

if (this.holdTimer) {

clearTimeout(this.holdTimer);

this.holdTimer = null;

}

if (this.recordingStream) {

this.recordingStream.getTracks().forEach(t => t.stop());

this.recordingStream = null;

}

this.mediaRecorder = null;

this.recordedChunks = [];

this.recordingAccumulatedMs = 0;

this.recordingDurationSec = 0;

this.recordingStartTime = null;

this.recordStopMode = null;

},

stopActiveVideo() {

if (!this.activeVideoElement) return;

try { this.activeVideoElement.pause(); } catch (_) {}

this.activeVideoElement = null;

},

stopActiveAudio(resetProgress = false) {

if (!this.activeAudioElement || this.activeAudioMessageId === null) return;

if (this._seekCheckTimer) { clearTimeout(this._seekCheckTimer); this._seekCheckTimer = null; }

try { this.activeAudioElement.pause(); } catch (_) {}

const activeId = Number(this.activeAudioMessageId);

const msg = this.messages.find(m => Number(m.id) === activeId);

if (msg) {

msg.isPlaying = false;

msg.currentBar = resetProgress ? 0 : msg.currentBar;

const ct = this.activeAudioElement.currentTime; msg.playbackPosition = resetProgress ? 0 : (ct !== null && ct !== undefined && !Number.isNaN(ct) ? ct : (msg.playbackPosition ?? msg._resumePos ?? 0));

if (!resetProgress) {

this.saveAudioResumePosition(msg, msg.playbackPosition || 0);

this.saveAudioPositionToDb(msg.id, msg.playbackPosition || 0);

}

}

if (resetProgress) this.activeAudioElement.currentTime = 0;

this.activeAudioElement = null;

this.activeAudioMessageId = null;

},

getAudioResumeStorageKey(message) {

const messageId = Number(message?.id || 0);

return `audio_resume_${messageId}`;

},

sameAudioMessage(message) {

return !!message && this.activeAudioElement && Number(this.activeAudioMessageId) === Number(message.id);

},

getAudioPlaybackPosition(message, audio, fallback = 0) {

const values = [
Number(audio?.currentTime),
Number(message?.playbackPosition),
Number(message?._resumePos),
Number(message?.audioPosition),
Number(fallback),
];

for (const value of values) {

if (Number.isFinite(value) && value > 0) return value;

}

return 0;

},

getSeekClientX(event) {

const touch = event?.touches?.[0] || event?.changedTouches?.[0];

const x = Number(touch?.clientX ?? event?.clientX);

return Number.isFinite(x) ? x : null;

},

saveAudioResumePosition(message, seconds) {

if (!message?.id) return;

const value = Math.max(0, Number(seconds || 0));

this.positionCache[message.id] = value;

message._resumePos = value;

message.audioPosition = value;

try { localStorage.setItem(`audio_resume_${message.id}`, String(value)); } catch(_) {}
try { sessionStorage.setItem(`audio_resume_${message.id}`, String(value)); } catch(_) {}

const now = Date.now();

this._lastAudioDbSave = this._lastAudioDbSave || {};

if (now - (this._lastAudioDbSave[message.id] || 0) >= 3000) {

this._lastAudioDbSave[message.id] = now;

this.saveAudioPositionToDb(message.id, value);

}

},

loadAudioResumePosition(message) {

if (!message?.id) return 0;

let v = 0;

v = Number(this.positionCache?.[message.id] || 0);

if (Number.isFinite(v) && v > 0) return v;

try { const s = localStorage.getItem(`audio_resume_${message.id}`); v = Number(s || 0); } catch(_) {}

if (Number.isFinite(v) && v > 0) return v;

try { const s = sessionStorage.getItem(`audio_resume_${message.id}`); v = Number(s || 0); } catch(_) {}

if (Number.isFinite(v) && v > 0) return v;

v = Number(message._resumePos || 0);

if (Number.isFinite(v) && v > 0) return v;

v = Number(message.audioPosition || 0);

if (Number.isFinite(v) && v > 0) return v;

return 0;

},

async saveAudioPositionToDb(messageId, position) {

if (!this.audioPositionRoute || this.audioPositionRoute === '#') return;

try {

const url = this.resolveMessageUrl(this.audioPositionRoute, messageId);

await fetch(url, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ position }),

});

} catch (_) {}

},

clampPlayableTime(audio, requested = 0, fallbackDuration = 0) {

const dur = (audio?.duration !== undefined && Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8) ? audio.duration : null;

const effectiveDur = dur || fallbackDuration || 0;

const base = Math.max(0, Number(requested || 0));

if (effectiveDur <= 0) return base;

return Math.max(0, Math.min(base, effectiveDur - 0.12));

},

stopAndResetAllAudioPlayers() {

Object.values(this.audioPlayers).forEach(audio => {

try { audio.pause(); } catch (_) {}

});

this.audioPlayers = {};

this.activeAudioElement = null;

this.activeAudioMessageId = null;

this.messages.forEach(m => {

m.isPlaying = false;

m.currentBar = 0;

m.playbackPosition = 0;

});

},

getAudioPlayer(message) {

if (!message?.id || !message.attachmentUrl) return null;

if (this.audioPlayers[message.id]) return this.audioPlayers[message.id];

const audio = new Audio(message.attachmentUrl);

audio.preload = 'auto';

try { audio.load(); } catch (_) {}

audio.onloadedmetadata = () => {

    message.isPlaying = !audio.paused;

    const dur = Number(audio.duration);

    if (Number.isFinite(dur) && dur > 0 && dur < 1e8) {
        message.audioDuration = Math.round(dur);
    }

};

audio.ondurationchange = () => {
    const dur = Number(audio.duration);
    if (Number.isFinite(dur) && dur > 0 && dur < 1e8) {
        message.audioDuration = Math.round(dur);
    }
};

audio.onplay = () => { message.isPlaying = true; };

audio.ontimeupdate = () => {

    const realDur = Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;
    const effectiveDur = realDur || message.audioDuration || 0;

    const ct = audio.currentTime || 0;

    if (realDur) {
        message.audioDuration = Math.round(realDur);
    }

    if (effectiveDur > 0) {
        message.currentBar = Math.max(0, Math.min(22, Math.round((ct / effectiveDur) * 22)));
        message.playbackPosition = ct;
        this.saveAudioResumePosition(message, ct);
    } else {
        message.playbackPosition = ct;
    }

    if (this.voicePlayerMessage && Number(this.voicePlayerMessage.id) === Number(message.id)) {
        this.voicePlayerPosition = ct;
    }

};

audio.onpause = () => {

message.isPlaying = false;

message.playbackPosition = this.getAudioPlaybackPosition(message, audio, message.playbackPosition);

this.saveAudioResumePosition(message, message.playbackPosition);

};

audio.onseeked = () => {

const realDur = Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;
const effectiveDur = realDur || message.audioDuration || 0;

const pos = audio.currentTime || 0;

message.playbackPosition = pos;

if (effectiveDur > 0) {

message.currentBar = Math.max(0, Math.min(22, Math.round((pos / effectiveDur) * 22)));

}

this.saveAudioResumePosition(message, pos);

};

audio.onended = () => {

message.playbackPosition = 0;

message.currentBar = 0;

message.isPlaying = false;

this.saveAudioResumePosition(message, 0);

try { audio.currentTime = 0; } catch (_) {}

// Auto-play next voice message
if (message) {
    const src = this.voicePlayerExternalSource || this.messages || [];
    const idx = src.findIndex(m => Number(m.id) === Number(message.id));
    let foundNext = false;
    if (idx !== -1) {
        for (let j = idx + 1; j < src.length; j++) {
            if (src[j] && src[j].messageType === 'audio' && src[j].attachmentUrl) {
                const id = Number(src[j].id);
                if (this.audioPlayers[id]) {
                    try { this.audioPlayers[id].currentTime = 0; } catch (_) {}
                }
                this.voicePlayerMessage = src[j];
                this.voicePlayerPosition = 0;
                this.toggleAudioPlayback(src[j]);
                foundNext = true;
                break;
            }
        }
    }
    if (!foundNext) {
        this.voicePlayerPosition = 0;
        this.voicePlayerMessage = null;
    }
}

};

this.audioPlayers[message.id] = audio;

return audio;

},

seekAudioTo(message, seconds) {

const audio = this.getAudioPlayer(message);

const realDur = audio?.duration !== undefined && Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;

const effectiveDur = realDur || message.audioDuration || 0;

if (effectiveDur <= 0) return;

const val = Math.max(0, Math.min(effectiveDur - 0.12, Number(seconds || 0)));

message.playbackPosition = val;

message.currentBar = Math.max(0, Math.min(22, Math.round((val / effectiveDur) * 22)));

this.saveAudioResumePosition(message, val);

if (this.voicePlayerMessage && Number(this.voicePlayerMessage.id) === Number(message.id)) {

this.voicePlayerPosition = val;

}

        if (audio) {

const applySeek = () => {
    if (this._seekCheckTimer) { clearTimeout(this._seekCheckTimer); this._seekCheckTimer = null; }
    try {
        audio.currentTime = val;
    } catch (_) {}
    const checkAfter = () => {
        if (Math.abs(audio.currentTime - val) > 1.5 && val > 0.5) {
            if (!audio.seekable || audio.seekable.length === 0) {
                const onReady = () => {
                    try { audio.currentTime = val; } catch (_) {}
                    audio.removeEventListener('canplaythrough', onReady);
                };
                audio.preload = 'auto';
                audio.addEventListener('canplaythrough', onReady, { once: true });
                try { audio.load(); } catch (_) {}
            }
        }
    };
    this._seekCheckTimer = setTimeout(checkAfter, 250);
};

if (audio.readyState >= 1) applySeek();

else audio.addEventListener('loadedmetadata', applySeek, { once: true });

}

},

seekAudioByPointer(message, event) {

const waveEl = event.currentTarget;

if (!waveEl) return;

const rect = waveEl.getBoundingClientRect();

const clientX = this.getSeekClientX(event);

if (clientX === null) return;

const x = Math.max(0, Math.min(rect.right - clientX, rect.width));

const ratio = rect.width ? (x / rect.width) : 0;

const audio = this.getAudioPlayer(message);

const realDur = audio?.duration !== undefined && Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;

const effectiveDur = realDur || message.audioDuration || 0;

if (effectiveDur <= 0) {

if (audio) {

message._pendingSeekRatio = ratio;

message.currentBar = Math.max(0, Math.min(22, Math.round(ratio * 22)));

}

return;

}

this.seekAudioTo(message, effectiveDur * ratio);

},

beginWaveSeek(message, event) {



if (this.isWaveSeeking && Number(this.waveSeekingMessageId) === Number(message.id)) return;

this.waveSeekWasPlaying = !!message.isPlaying;

if (this.activeAudioElement && Number(this.activeAudioMessageId) !== Number(message.id)) {

this.stopActiveAudio(false);

} else if (this.activeAudioElement && !this.activeAudioElement.paused) {

try { this.activeAudioElement.pause(); } catch (_) {}

message.isPlaying = false;

}

this.isWaveSeeking = true;

this.waveSeekingMessageId = message.id;

if (event.currentTarget?.setPointerCapture) {

try { event.currentTarget.setPointerCapture(event.pointerId); } catch (_) {}

}

this._updateWaveBars(message, event);

},

moveWaveSeek(message, event) {

if (!this.isWaveSeeking || this.waveSeekingMessageId !== message.id) return;

this._updateWaveBars(message, event);

},

_updateWaveBars(message, event) {

const waveEl = event.currentTarget;

if (!waveEl) return;

const rect = waveEl.getBoundingClientRect();

const clientX = this.getSeekClientX(event);

if (clientX === null) return;

const x = Math.max(0, Math.min(rect.right - clientX, rect.width));

const ratio = rect.width ? (x / rect.width) : 0;

this._waveDragRatio = ratio;
this._waveDragMessageId = message.id;



const el = this.$el.querySelector('.wave[data-mid="' + message.id + '"]');
if (el) {
    const bars = el.querySelectorAll('.bar');
    const count = bars.length;
    const active = Math.max(0, Math.min(count - 1, Math.round(ratio * (count - 1))));
    bars.forEach((b, i) => { b.classList.toggle('on', i <= active); });
}

},

async endWaveSeek(message, event = null) {



if (!this.isWaveSeeking) return;

if (this._seekCheckTimer) { clearTimeout(this._seekCheckTimer); this._seekCheckTimer = null; }

if (event?.currentTarget?.releasePointerCapture && event.pointerId !== undefined) {
try { event.currentTarget.releasePointerCapture(event.pointerId); } catch (_) {}
}

this.isWaveSeeking = false;
this.waveSeekingMessageId = null;

if (message?.attachmentUrl && this.waveSeekWasPlaying) {

const effectiveDur = message.audioDuration || 10;
let targetPos = 0;
const ratio = this._waveDragRatio;
this._waveDragRatio = null;
this._waveDragMessageId = null;
if (typeof ratio === 'number' && effectiveDur > 0) {
    targetPos = effectiveDur * Math.max(0, Math.min(1, ratio));
    message._pendingSeekRatio = null;
} else {
    targetPos = Number(message.playbackPosition || 0);
}
if (targetPos > 0.05) {



this._seekAndPlayAudio(message, targetPos);
}

}

this.waveSeekWasPlaying = false;

        },



        _seekAndPlayAudio(message, targetPos) {

if (!message?.attachmentUrl || targetPos <= 0.05) return;

const effectiveDur = message.audioDuration || 10;



message.playbackPosition = targetPos;
message.currentBar = Math.max(0, Math.min(22, Math.round((targetPos / effectiveDur) * 22)));
this.saveAudioResumePosition(message, targetPos);
message.isPlaying = false;

if (this.voicePlayerMessage && Number(this.voicePlayerMessage.id) === Number(message.id)) {
    this.voicePlayerPosition = targetPos;
}

const onEnded = () => {
    message.playbackPosition = 0; message.currentBar = 0; message.isPlaying = false;
    this.saveAudioResumePosition(message, 0);
};
const onTimeUpdate = (a) => {
    const ct = a.currentTime;
    const d = a.duration || effectiveDur;
    message.playbackPosition = ct;
    message.currentBar = Math.max(0, Math.min(22, Math.round((ct / d) * 22)));
    if (this.voicePlayerMessage && Number(this.voicePlayerMessage.id) === Number(message.id)) {
        this.voicePlayerPosition = ct;
    }
};

const setupAndPlay = (audioEl) => {
    delete this.audioPlayers[message.id];
    this.activeAudioElement = null;
    this.activeAudioMessageId = null;
    audioEl.onended = onEnded;
    audioEl.ontimeupdate = () => onTimeUpdate(audioEl);
    try { audioEl.currentTime = targetPos; } catch (_) {}
    audioEl.addEventListener('seeked', () => {
        
        if (targetPos > 0.5 && Math.abs(audioEl.currentTime - targetPos) > 1.5 && !(audioEl.src.startsWith('blob:'))) {
            
            try { audioEl.pause(); audioEl.src = ''; } catch (_) {}
            fetch(message.attachmentUrl).then(r => r.blob()).then(blob => {
                
                const blobUrl = URL.createObjectURL(blob);
                const a2 = new Audio(blobUrl);
                a2.preload = 'auto';
                a2.onended = onEnded;
                a2.ontimeupdate = () => onTimeUpdate(a2);
                a2.onloadedmetadata = () => {
                    try { a2.currentTime = targetPos; } catch (_) {}
                    a2.play().then(() => {
                        message.isPlaying = true;
                        this.activeAudioElement = a2;
                        this.activeAudioMessageId = message.id;
                        this.audioPlayers[message.id] = a2;
                    }).catch(() => { message.isPlaying = false; });
                };
            }).catch(() => {
                try { audioEl.play(); message.isPlaying = true; } catch (_) {}
            });
        } else {
            audioEl.play().then(() => {
                message.isPlaying = true;
                this.activeAudioElement = audioEl;
                this.activeAudioMessageId = message.id;
                this.audioPlayers[message.id] = audioEl;
            }).catch(() => { message.isPlaying = false; });
        }
    }, { once: true });
    setTimeout(() => {
        if (!message.isPlaying && audioEl.paused && !audioEl.src.startsWith('blob:')) {
            
            try { audioEl.pause(); audioEl.src = ''; } catch (_) {}
            fetch(message.attachmentUrl).then(r => r.blob()).then(blob => {
                const blobUrl = URL.createObjectURL(blob);
                const a2 = new Audio(blobUrl);
                a2.preload = 'auto';
                a2.onended = onEnded;
                a2.ontimeupdate = () => onTimeUpdate(a2);
                a2.onloadedmetadata = () => {
                    try { a2.currentTime = targetPos; } catch (_) {}
                    a2.play().then(() => {
                        message.isPlaying = true;
                        this.activeAudioElement = a2;
                        this.activeAudioMessageId = message.id;
                        this.audioPlayers[message.id] = a2;
                    }).catch(() => {});
                };
            }).catch(() => {});
        }
    }, 600);
};

const a = new Audio(message.attachmentUrl);
a.preload = 'auto';
a.onloadedmetadata = () => { try { a.currentTime = targetPos; } catch (_) {} };
setupAndPlay(a);

},

async playAudioMessage(message, explicitPosition = null) {

if (!message || !message.attachmentUrl) return;

    this.stopActiveVideo();

    const isSamePaused = this.activeAudioElement && !this.activeAudioElement.paused === false && Number(this.activeAudioMessageId) === Number(message.id);

    if (this.activeAudioElement && Number(this.activeAudioMessageId) !== Number(message.id)) {

        this.stopActiveAudio(false);

    }

    const audio = this.getAudioPlayer(message);

    if (!audio) return;

    this.activeAudioElement = audio;

    this.activeAudioMessageId = message.id;

    if (isSamePaused && explicitPosition === null && typeof message._pendingSeekRatio !== 'number') {

        message.playbackPosition = audio.currentTime;
        message.isPlaying = true;

        try { await audio.play(); } catch (_) { message.isPlaying = false; }
        return;

    }

    const resolveStart = () => {

        const resolveRealDur = audio.duration !== undefined && Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;
        const resolveDur = resolveRealDur || message.audioDuration || 0;

        let startAt = explicitPosition !== null && explicitPosition !== undefined
            ? Number(explicitPosition || 0)
            : this.getAudioPlaybackPosition(message, audio, this.loadAudioResumePosition(message));

        if (typeof message._pendingSeekRatio === 'number' && resolveDur > 0) {

            startAt = resolveDur * Math.max(0, Math.min(1, message._pendingSeekRatio));

            message._pendingSeekRatio = null;

        }

        // Only treat as "finished" (restart from 0) if the requested position is at/past
        // the real track duration — not merely close to the safety-margin clamp ceiling below.
        const effectiveDuration = Number.isFinite(resolveRealDur) && resolveRealDur > 0 ? resolveRealDur : message.audioDuration || 0;
        if (effectiveDuration > 0 && startAt >= effectiveDuration) {
            startAt = 0;
        } else {
            startAt = this.clampPlayableTime(audio, startAt, message.audioDuration);
        }

        return startAt;

    };

    const playNow = async () => {

        const startAt = resolveStart();

        const doSeek = () => {
            return new Promise(resolveSeek => {
                const attemptSeek = () => {
                    try { audio.currentTime = startAt; } catch (_) {}
                    setTimeout(() => {
                        if (startAt > 0.5 && Math.abs(audio.currentTime - startAt) > 1.5) {
                            if (!audio.seekable || audio.seekable.length === 0) {
                                audio.preload = 'auto';
                                audio.addEventListener('canplaythrough', () => {
                                    try { audio.currentTime = startAt; } catch (_) {}
                                    resolveSeek();
                                }, { once: true });
                                try { audio.load(); } catch (_) { resolveSeek(); }
                            } else {
                                try { audio.currentTime = startAt; } catch (_) {}
                                resolveSeek();
                            }
                        } else {
                            resolveSeek();
                        }
                    }, 300);
                };
                if (audio.readyState >= 1) { attemptSeek(); }
                else { audio.addEventListener('loadedmetadata', attemptSeek, { once: true }); }
            });
        };

        if (startAt > 0.1) { await doSeek(); }

        message.playbackPosition = startAt;

        const playNowRealDur = audio.duration !== undefined && Number.isFinite(audio.duration) && audio.duration > 0 && audio.duration < 1e8 ? audio.duration : null;
        const playNowDur = playNowRealDur || message.audioDuration || 0;
        if (playNowDur > 0) {

            message.currentBar = Math.max(0, Math.min(22, Math.round((startAt / playNowDur) * 22)));

        }

        this.saveAudioResumePosition(message, startAt);

        try {
            await audio.play();
            message.isPlaying = true;
            this.openVoicePlayer(message);
        } catch (_) {
            message.isPlaying = false;
        }

    };

    if (audio.readyState >= 1) await playNow();
    else audio.addEventListener('loadedmetadata', playNow, { once: true });

},

        async toggleAudioPlayback(message) {



if (!message || !message.attachmentUrl) return;

    if (this.sameAudioMessage(message) && this.activeAudioElement && !this.activeAudioElement.paused) {

        const pos = this.getAudioPlaybackPosition(message, this.activeAudioElement);

        this.saveAudioResumePosition(message, pos);
        message.playbackPosition = pos;

        try { this.activeAudioElement.pause(); } catch (_) {}

        message.isPlaying = false;

        this.saveAudioPositionToDb(message.id, pos);

        return;

    }

    const savedPos = this.loadAudioResumePosition(message);

    if (savedPos > 0 && !(Number(message.playbackPosition) > 0)) {
        message.playbackPosition = savedPos;
        message._resumePos = savedPos;
        message.audioPosition = savedPos;
    }

    await this.playAudioMessage(message);


},

stopAudioPlayback(message) {

if (!message) return;

if (Number(this.activeAudioMessageId) === Number(message.id)) this.stopActiveAudio(false);

else message.isPlaying = false;

},

// Voice player bar

openVoicePlayer(message) {

if (!message || message.messageType !== 'audio') return;

this.voicePlayerMessage = message;

const audio = this.getAudioPlayer(message);

if (audio) {

this.voicePlayerPosition = audio.currentTime || message.playbackPosition || 0;

if (this.voicePlayerMuted) audio.muted = true;

audio.playbackRate = this.voiceSpeed;

}

},

closeVoicePlayer() {

if (this.voicePlayerMessage) this.voicePlayerMessage.isPlaying = false;

this.stopActiveAudio(true);

this.voicePlayerMessage = null;

this.voicePlayerPosition = 0;

this.voicePlayerMuted = false;

this.voicePlayerExternalSource = null;

const prev = this.messages.find(m => m.isPlaying);

if (prev) { prev.isPlaying = false; prev.currentBar = 0; prev.playbackPosition = 0; }

},

playPrevVoice() {

if (!this.voicePlayerMessage) return;

const src = this.voicePlayerExternalSource || this.messages;

const idx = src.findIndex(m => Number(m.id) === Number(this.voicePlayerMessage.id));

if (idx === -1 && this.voicePlayerExternalSource) {
for (let j = this.messages.length - 1; j >= 0; j--) {
if (this.messages[j]?.messageType === 'audio' && this.messages[j]?.attachmentUrl) {
this.voicePlayerMessage = this.messages[j];
this.toggleAudioPlayback(this.messages[j]);
return;
}
}
return;
}

for (let j = idx - 1; j >= 0; j--) {

if (src[j]?.messageType === 'audio' && src[j]?.attachmentUrl) {

this.voicePlayerMessage = src[j];

this.toggleAudioPlayback(src[j]);

return;

}

}

},

playNextVoice() {

if (!this.voicePlayerMessage) return;

const src = this.voicePlayerExternalSource || this.messages;

const idx = src.findIndex(m => Number(m.id) === Number(this.voicePlayerMessage.id));

if (idx === -1 && this.voicePlayerExternalSource) {
for (let j = 0; j < this.messages.length; j++) {
if (this.messages[j]?.messageType === 'audio' && this.messages[j]?.attachmentUrl) {
this.voicePlayerMessage = this.messages[j];
this.toggleAudioPlayback(this.messages[j]);
return;
}
}
return;
}

for (let j = idx + 1; j < src.length; j++) {

if (src[j]?.messageType === 'audio' && src[j]?.attachmentUrl) {

this.voicePlayerMessage = src[j];

this.toggleAudioPlayback(src[j]);

return;

}

}

},

cycleVoiceSpeed() {

const speeds = [0.5, 1, 1.5, 2];

const i = speeds.indexOf(this.voiceSpeed);

this.voiceSpeed = speeds[(i + 1) % speeds.length];

if (this.activeAudioElement) this.activeAudioElement.playbackRate = this.voiceSpeed;

},

seekVoicePlayerBar(e) {

const message = this.voicePlayerMessage;

if (!message) return;

const rect = e.currentTarget.getBoundingClientRect();

const clientX = this.getSeekClientX(e);

if (clientX === null) return;

const ratio = Math.max(0, Math.min(1, (rect.right - clientX) / rect.width));

const dur = message.audioDuration || 10;

const t = Math.max(0, Math.min(dur - 0.12, ratio * dur));

this.voicePlayerPosition = t;

message.playbackPosition = t;

message.currentBar = Math.max(0, Math.min(22, Math.round((t / dur) * 22)));

message._pendingSeekRatio = ratio;

this.saveAudioResumePosition(message, t);

},

beginVoicePlayerSeek(event) {

if (!this.voicePlayerMessage) return;

if (this.isVoicePlayerSeeking) return;

const audio = this.activeAudioElement || this.getAudioPlayer(this.voicePlayerMessage);

if (audio && !audio.paused) {

try { audio.pause(); } catch (_) {}

}

this.isVoicePlayerSeeking = true;

if (event.currentTarget?.setPointerCapture) {

try { event.currentTarget.setPointerCapture(event.pointerId); } catch (_) {}

}

this.seekVoicePlayerBar(event);

},

moveVoicePlayerSeek(event) {

if (!this.isVoicePlayerSeeking) return;

this.seekVoicePlayerBar(event);

},

async endVoicePlayerSeek(event) {

if (!this.isVoicePlayerSeeking) return;

this.seekVoicePlayerBar(event);

if (event?.currentTarget?.releasePointerCapture && event.pointerId !== undefined) {

try { event.currentTarget.releasePointerCapture(event.pointerId); } catch (_) {}

}

this.isVoicePlayerSeeking = false;

const message = this.voicePlayerMessage;

if (message?.attachmentUrl) {

const effectiveDur = message.audioDuration || 10;

let targetPos = 0;
if (typeof message._pendingSeekRatio === 'number' && effectiveDur > 0) {
    targetPos = effectiveDur * Math.max(0, Math.min(1, message._pendingSeekRatio));
    message._pendingSeekRatio = null;
} else {
    targetPos = Number(message.playbackPosition || 0);
}

this._seekAndPlayAudio(message, targetPos);

}

},

// Standalone profile media audio player (no voice bar)


messageDuration(msg) {

return Number(msg?.audioDuration || 0);

},

onVoicePlayerBarClick(e) {

let el = e.target;
while (el && el !== e.currentTarget) {
if (el.tagName === 'BUTTON') return;
if (el.classList?.contains('vpb-track')) return;
el = el.parentNode;
}
this.scrollToAndHighlightVoice(this.voicePlayerMessage);

},

scrollToAndHighlightVoice(message) {

if (!message) return;

const id = message.id || message._id;

if (!id) return;

this.$nextTick(() => {

const row = document.getElementById(`message-${id}`);

if (row) {

row.scrollIntoView({ behavior: 'smooth', block: 'center' });

row.classList.remove('reply-focus');

void row.offsetWidth;

row.classList.add('reply-focus');

setTimeout(() => row.classList.remove('reply-focus'), 2000);

}

});

},

mediaModalKindOf(message) {
const rawType = this.getMessageType(message);
return (rawType === 'sticker_static' || rawType === 'gif') ? 'image' : (rawType === 'sticker_animated' ? 'video' : rawType);
},

buildMediaModalEntry(message) {
const type = this.mediaModalKindOf(message);
const senderName = this.getMessageAuthorName(message);
const rawAvatar = message.senderAvatar ?? message.sender_avatar ?? message.authorAvatar ?? message.author_avatar
    ?? (Number(message.senderId) === Number(this.currentUserId) ? this.currentUserAvatar
        : (this.contacts.find(c => Number(c.id) === Number(message.senderId))?.avatar_url))
    ?? null;
return {
message,
type,
url: message.attachmentUrl,
name: message.attachmentName || '',
caption: (message.content && !['sticker', 'gif', 'sticker.png', 'sticker.webm'].includes(message.content)) ? message.content : '',
isSticker: message.messageType === 'sticker_static' || message.messageType === 'sticker_animated',
senderName,
senderInitial: this.getAuthorInitial(senderName),
senderAvatar: rawAvatar ? this.normalizeAvatarUrl(rawAvatar) : null,
metaTime: this.formatMediaMetaTime(message.createdAt || message.created_at),
};
},

openMediaModal(message) {

const type = this.mediaModalKindOf(message);

if (!['image', 'video'].includes(type) || !message.attachmentUrl) return;

this.stopActiveAudio(false);

const list = this.messages.filter(m => ['image', 'video'].includes(this.mediaModalKindOf(m)) && m.attachmentUrl && m.messageType !== 'sticker_static' && m.messageType !== 'sticker_animated');
const index = Math.max(0, list.findIndex(m => m.id === message.id));

this.mediaModalList = list;
this.mediaModal = { ...this.buildMediaModalEntry(message), index };
this.mediaModalMoreOpen = false;

},

showMediaModalAt(index) {
if (!this.mediaModalList || index < 0 || index >= this.mediaModalList.length) return;
this.stopActiveVideo();
this.stopActiveAudio(false);
const message = this.mediaModalList[index];
this.mediaModal = { ...this.buildMediaModalEntry(message), index };
this.mediaModalMoreOpen = false;
},

mediaModalPrev() { if (this.mediaModal) this.showMediaModalAt(this.mediaModal.index - 1); },
mediaModalNext() { if (this.mediaModal) this.showMediaModalAt(this.mediaModal.index + 1); },

cacheMediaDuration(message, event) {
const d = event?.target?.duration;
if (d && isFinite(d)) this.mediaDurationCache[message.id] = d;
},

formatMediaDuration(seconds) {
if (!seconds || !isFinite(seconds)) return '';
const m = Math.floor(seconds / 60);
const s = Math.round(seconds % 60);
return `${m}:${String(s).padStart(2, '0')}`;
},

async downloadMediaModalItem() {
if (!this.mediaModal?.url) return;
try {
const res = await fetch(this.mediaModal.url);
const blob = await res.blob();
const a = document.createElement('a');
a.href = URL.createObjectURL(blob);
a.download = this.mediaModal.name || 'media';
document.body.appendChild(a);
a.click();
a.remove();
} catch (_) {
this.showToast('تعذر تحميل الملف', 'error');
}
},

mediaModalReply() { this.messageContextMessage = this.mediaModal?.message; this.contextReply(); this.closeMediaModal(); },
mediaModalForward() { this.messageContextMessage = this.mediaModal?.message; this.contextForward(); },
mediaModalPin() { this.messageContextMessage = this.mediaModal?.message; this.contextPin(); },
mediaModalSave() { this.messageContextMessage = this.mediaModal?.message; this.contextSave(); },
mediaModalDelete() { this.messageContextMessage = this.mediaModal?.message; this.contextDelete(); this.mediaModalMoreOpen = false; this.closeMediaModal(); },

async mediaModalToggleStickerFavorite() {
if (!this.mediaModal?.message) return;
await this.toggleStickerFavoriteFromMessage(this.mediaModal.message);
},

closeMediaModal() {

this.stopActiveVideo();

this.mediaModal = null;

this.mediaModalList = [];

this.mediaModalMoreOpen = false;

this.mediaZoomReset();

},

mediaZoomIn() {
this.mediaZoomTransition = true;
const next = Math.min(this.mediaImgZoom * 1.5, 10);
this.mediaImgZoom = next;
setTimeout(() => { this.mediaZoomTransition = false; }, 150);
},

mediaZoomOut() {
this.mediaZoomTransition = true;
const next = Math.max(this.mediaImgZoom / 1.5, 0.25);
this.mediaImgZoom = next;
if (next <= 1) { this.mediaImgPanX = 0; this.mediaImgPanY = 0; }
setTimeout(() => { this.mediaZoomTransition = false; }, 150);
},

mediaZoomReset() {
this.mediaImgZoom = 1;
this.mediaImgPanX = 0;
this.mediaImgPanY = 0;
this.mediaIsPanning = false;
this.mediaZoomTransition = true;
setTimeout(() => { this.mediaZoomTransition = false; }, 150);
},

mediaZoomWheel(e) {
this.mediaZoomTransition = true;
const delta = e.deltaY > 0 ? 0.9 : 1.1;
const next = Math.min(Math.max(this.mediaImgZoom * delta, 0.25), 10);
this.mediaImgZoom = next;
if (next <= 1) { this.mediaImgPanX = 0; this.mediaImgPanY = 0; }
setTimeout(() => { this.mediaZoomTransition = false; }, 150);
},

mediaDblClickZoom() {
this.mediaZoomTransition = true;
if (this.mediaImgZoom > 1.5) {
this.mediaZoomReset();
} else {
this.mediaImgZoom = 3;
}
setTimeout(() => { this.mediaZoomTransition = false; }, 150);
},

mediaZoomPanStart(e) {
if (this.mediaImgZoom <= 1) return;
this.mediaIsPanning = true;
this.mediaPanStartX = e.clientX;
this.mediaPanStartY = e.clientY;
this.mediaPanImgStartX = this.mediaImgPanX;
this.mediaPanImgStartY = this.mediaImgPanY;
e.target.closest('.media-zoom-wrap').style.cursor = 'grabbing';
},

mediaZoomPanMove(e) {
if (!this.mediaIsPanning) return;
const dx = e.clientX - this.mediaPanStartX;
const dy = e.clientY - this.mediaPanStartY;
this.mediaImgPanX = this.mediaPanImgStartX + dx;
this.mediaImgPanY = this.mediaPanImgStartY + dy;
},

mediaZoomPanEnd() {
this.mediaIsPanning = false;
const wrap = document.querySelector('.media-zoom-wrap');
if (wrap) wrap.style.cursor = 'grab';
},

mediaZoomTouchStart(e) {
if (e.touches.length === 2) {
this.mediaPinchDist = Math.hypot(
e.touches[0].clientX - e.touches[1].clientX,
e.touches[0].clientY - e.touches[1].clientY
);
this.mediaPinchZoomStart = this.mediaImgZoom;
}
},

mediaZoomTouchMove(e) {
if (e.touches.length === 2) {
e.preventDefault();
const dist = Math.hypot(
e.touches[0].clientX - e.touches[1].clientX,
e.touches[0].clientY - e.touches[1].clientY
);
const scale = dist / this.mediaPinchDist;
this.mediaImgZoom = Math.min(Math.max(this.mediaPinchZoomStart * scale, 0.25), 10);
} else if (e.touches.length === 1 && this.mediaImgZoom > 1) {
const t = e.touches[0];
if (this.mediaIsPanning) {
const dx = t.clientX - this.mediaPanStartX;
const dy = t.clientY - this.mediaPanStartY;
this.mediaImgPanX = this.mediaPanImgStartX + dx;
this.mediaImgPanY = this.mediaPanImgStartY + dy;
} else {
this.mediaIsPanning = true;
this.mediaPanStartX = t.clientX;
this.mediaPanStartY = t.clientY;
this.mediaPanImgStartX = this.mediaImgPanX;
this.mediaPanImgStartY = this.mediaImgPanY;
}
}
},

mediaZoomTouchEnd() {
this.mediaIsPanning = false;
},

onVideoPlay(event) {

this.stopActiveAudio(false);

if (this.activeVideoElement && this.activeVideoElement !== event.target) {

try { this.activeVideoElement.pause(); } catch (_) {}

}

this.activeVideoElement = event.target;

},

onVideoPause(event) {

if (this.activeVideoElement === event.target) this.activeVideoElement = null;

},

findReplyTargetId(replyTo) {

const directId = Number(replyTo?.id || replyTo?.messageId || replyTo?.message_id || 0);

if (directId && this.messages.some(m => Number(m.id) === directId)) return directId;

if (replyTo?.content) {

const byContent = [...this.messages].reverse().find(m => (m.content || '') === replyTo.content);

if (byContent) return Number(byContent.id);

}

if (replyTo?.attachmentName) {

const byAttachment = [...this.messages].reverse().find(m => (m.attachmentName || '') === replyTo.attachmentName);

if (byAttachment) return Number(byAttachment.id);

}

return null;

},

jumpToPinnedMessage(msg) {
const el = document.getElementById(`message-${msg.id}`);
if (!el) return;
el.scrollIntoView({ block: 'center', behavior: 'smooth' });
this.highlightedMessageId = Number(msg.id);
if (this.highlightTimer) clearTimeout(this.highlightTimer);
this.highlightTimer = setTimeout(() => { this.highlightedMessageId = null; }, 1300);
},

async jumpToRepliedMessage(replyTo) {

let targetId = this.findReplyTargetId(replyTo);

if (!targetId && this.selectedContact) {

await this.loadMessages(false);

targetId = this.findReplyTargetId(replyTo);

}

if (!targetId) return;

const el = document.getElementById(`message-${targetId}`);

if (!el) return;

el.scrollIntoView({ block: 'center', behavior: 'smooth' });

this.highlightedMessageId = Number(targetId);

if (this.highlightTimer) clearTimeout(this.highlightTimer);

this.highlightTimer = setTimeout(() => { this.highlightedMessageId = null; }, 1300);

},

toggleEmojiPicker() {

if (this.showEmojiPicker && this.emojiPickerPinned) {
this.showEmojiPicker = false;
this.emojiPickerPinned = false;
if (this.emojiHoverTimer) { clearTimeout(this.emojiHoverTimer); this.emojiHoverTimer = null; }
return;
}

this.showEmojiPicker = true;
this.emojiPickerPinned = true;

if (this.emojiHoverTimer) {

clearTimeout(this.emojiHoverTimer);

this.emojiHoverTimer = null;

}

},

onEmojiHoverEnter() {

if (this.emojiHoverTimer) {

clearTimeout(this.emojiHoverTimer);

this.emojiHoverTimer = null;

}

if (!this.showEmojiPicker) {

this.pickerActiveTab = 'emoji';

}

this.showEmojiPicker = true;

},

onEmojiHoverLeave() {

if (this.emojiPickerPinned) return;

if (this.emojiHoverTimer) clearTimeout(this.emojiHoverTimer);

this.emojiHoverTimer = setTimeout(() => {

this.showEmojiPicker = false;

this.emojiHoverTimer = null;

}, 220);

},

addEmoji(entity) {

const div = document.createElement('div');

div.innerHTML = entity;

this.messageInput += div.textContent || '';

this.$nextTick(() => this.$refs.messageInput?.focus());

},

async ensureEmojiData() {
if (this.emojiCategoriesLoaded) return;
try {
const res = await fetch('{{ asset('js/emoji-data.json') }}');
this.emojiCategoriesData = await res.json();
this.emojiCategoriesLoaded = true;
} catch (_) {
this.emojiCategoriesData = [];
}
},

switchPickerTab(tab) {
this.pickerActiveTab = tab;
if (tab === 'emoji') this.ensureEmojiData();
if (tab === 'sticker') this.ensureStickersLoaded();
},

addEmojiChar(char) {
this.messageInput += char;
this.emojiRecentList = [char, ...this.emojiRecentList.filter(c => c !== char)].slice(0, 24);
localStorage.setItem('emoji_recent_v1', JSON.stringify(this.emojiRecentList));
this.$nextTick(() => this.$refs.messageInput?.focus());
},

scrollToEmojiCat(catId) {
const scroll = this.$refs.emojiPickerScroll;
if (!scroll) return;
const target = scroll.querySelector(`[data-cat-id="${catId}"]`);
if (target) { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
this.emojiActiveCatId = catId;
},

onEmojiPickerScroll() {
const scroll = this.$refs.emojiPickerScroll;
if (!scroll) return;
const scrollTop = scroll.scrollTop;
const cats = scroll.querySelectorAll('[data-cat-id]');
let active = null;
for (const cat of cats) {
if (cat.offsetTop - scroll.offsetTop <= scrollTop + 10) active = cat.dataset.catId;
}
if (active && active !== this.emojiActiveCatId) this.emojiActiveCatId = active;
},

onGifSearchInput() {
if (this.gifSearchTimer) clearTimeout(this.gifSearchTimer);
this.gifSearchTimer = setTimeout(() => this.doSearchGifs(), 400);
},

async doSearchGifs() {
const q = this.gifSearchQuery.trim();
this.gifSearchedOnce = true;
if (!q) { this.gifResults = []; return; }
this.gifLoading = true;
try {
const res = await fetch(`${this.gifsSearchRoute}?q=${encodeURIComponent(q)}`);
const d = await res.json();
this.gifResults = d.success ? (d.data || []) : [];
} catch (_) {
this.gifResults = [];
} finally {
this.gifLoading = false;
}
},

async sendGif(gif) {
if (!gif?.fullUrl || !this.selectedContact) return;
this.showEmojiPicker = false;

const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: this.selectedContact.id,
content: 'gif',
attachment_name: 'gif.gif',
attachment_type: 'image/gif',
attachmentKind: 'gif',
attachmentUrl: gif.fullUrl,
replyTo: this.replyingToMessage || null,
created_at: new Date().toISOString(),
read_at: null,
});
msg.messageType = 'gif';
msg.pending = true;
this.messages.push(msg);
this.scrollToBottom(true);

const fd = new FormData();
fd.append('recipient_id', this.selectedContact.id);
fd.append('kind', 'gif');
fd.append('gif_url', gif.fullUrl);
if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);

try {
const res = await fetch(@json($fileRoute), { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await res.json();
if (d.success) {
this.replacePendingWithServerMessage(msg.id, d.data, this.selectedContact.id);
} else {
this.markMessageFailed(msg.id, d.message || 'تعذر إرسال GIF');
}
} catch (_) {
this.markMessageFailed(msg.id, 'تعذر إرسال GIF. تحقق من اتصالك');
}
},

async ensureStickersLoaded() {
if (this.stickersLoaded || this.stickersLoading) return;
this.stickersLoading = true;
try {
const res = await fetch(this.stickersIndexRoute);
const d = await res.json();
if (d.success) {
this.stickerList = d.data.stickers || [];
this.stickerFavorites = d.data.favorites || [];
this.stickerRecent = d.data.recent || [];
}
this.stickersLoaded = true;
} catch (_) {
} finally {
this.stickersLoading = false;
}
},

async sendSticker(sticker) {
if (!sticker || !this.selectedContact) return;
this.showEmojiPicker = false;

const kind = sticker.type === 'animated' ? 'sticker_animated' : 'sticker_static';
const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: this.selectedContact.id,
content: 'sticker',
attachment_name: 'sticker',
attachment_type: sticker.type === 'animated' ? 'video/webm' : 'image/png',
attachmentKind: kind,
attachmentUrl: sticker.url,
replyTo: this.replyingToMessage || null,
created_at: new Date().toISOString(),
read_at: null,
});
msg.messageType = kind;
msg.pending = true;
this.messages.push(msg);
this.scrollToBottom(true);

const fd = new FormData();
fd.append('recipient_id', this.selectedContact.id);
fd.append('kind', kind);
fd.append('sticker_id', sticker.id);
if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);

try {
const res = await fetch(@json($fileRoute), { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await res.json();
if (d.success) {
this.replacePendingWithServerMessage(msg.id, d.data, this.selectedContact.id);
this.stickerRecent = [sticker, ...this.stickerRecent.filter(s => s.id !== sticker.id)].slice(0, 12);
if (this.stickersUsedRouteTemplate) {
const usedUrl = this.stickersUsedRouteTemplate.replace('__STICKER_ID__', sticker.id);
fetch(usedUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken } }).catch(() => {});
}
} else {
this.markMessageFailed(msg.id, d.message || 'تعذر إرسال الملصق');
}
} catch (_) {
this.markMessageFailed(msg.id, 'تعذر إرسال الملصق. تحقق من اتصالك');
}
},

openStickerViewer(message) {
if (!message.attachmentUrl) return;
this.stickerViewer = message;
this.ensureStickersLoaded();
},

closeStickerViewer() {
this.stickerViewer = null;
},

// ── E2E Encryption ─────────────────────────────────────────────────────────
async initE2E() {
if (!window.crypto?.subtle) return;
try {
const db = await this._openE2EDB();
let keyPair = await this._idbGet(db, 'keyPair');
if (!keyPair) {
keyPair = await window.crypto.subtle.generateKey(
{ name: 'ECDH', namedCurve: 'P-256' }, true, ['deriveKey']
);
await this._idbPut(db, 'keyPair', keyPair);
}
window._e2eKeyPair = keyPair;
const jwk = await window.crypto.subtle.exportKey('jwk', keyPair.publicKey);
await fetch(@json($e2eRegisterRoute), {
method: 'POST',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
body: JSON.stringify({ public_key: JSON.stringify(jwk) }),
}).catch(() => {});
} catch (_) {}
},

async fetchPartnerE2EKey(userId) {
if (!userId || !window._e2eKeyPair) { this.e2eEnabled = false; this.e2ePartnerHasKey = false; return; }
try {
const r = await fetch((@json($e2ePublicKeyBase ?? '')).replace('__UID__', userId));
const d = await r.json();
if (!d.public_key) { this.e2eEnabled = false; this.e2ePartnerHasKey = false; return; }
const jwk = JSON.parse(d.public_key);
const importedKey = await window.crypto.subtle.importKey(
'jwk', jwk, { name: 'ECDH', namedCurve: 'P-256' }, false, []
);
window._e2ePartnerKey = importedKey;
this.e2ePartnerHasKey = true;
this.e2eEnabled = true;
} catch (_) { this.e2eEnabled = false; this.e2ePartnerHasKey = false; }
},

async _e2eEncrypt(text) {
if (!this.e2eEnabled || !window._e2eKeyPair || !window._e2ePartnerKey) return text;
try {
const sharedKey = await window.crypto.subtle.deriveKey(
{ name: 'ECDH', public: window._e2ePartnerKey },
window._e2eKeyPair.privateKey,
{ name: 'AES-GCM', length: 256 }, false, ['encrypt']
);
const iv = window.crypto.getRandomValues(new Uint8Array(12));
const enc = new TextEncoder();
const ct = await window.crypto.subtle.encrypt({ name: 'AES-GCM', iv }, sharedKey, enc.encode(text));
const ivB64 = btoa(String.fromCharCode(...iv));
const ctB64 = btoa(String.fromCharCode(...new Uint8Array(ct)));
return `e2e:${ivB64}:${ctB64}`;
} catch (_) { return text; }
},

async _e2eDecrypt(content, senderKey) {
if (!content?.startsWith('e2e:') || !window._e2eKeyPair) return content;
try {
const parts = content.split(':');
if (parts.length < 3) return content;
const iv = new Uint8Array(atob(parts[1]).split('').map(c => c.charCodeAt(0)));
const ct = new Uint8Array(atob(parts.slice(2).join(':')).split('').map(c => c.charCodeAt(0)));
const sharedKey = await window.crypto.subtle.deriveKey(
{ name: 'ECDH', public: senderKey },
window._e2eKeyPair.privateKey,
{ name: 'AES-GCM', length: 256 }, false, ['decrypt']
);
const plain = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv }, sharedKey, ct);
return new TextDecoder().decode(plain);
} catch (_) { return '[رسالة مشفرة — يتعذر فكّ التشفير]'; }
},

_openE2EDB() {
return new Promise((res, rej) => {
const req = indexedDB.open('iglal_e2e', 1);
req.onupgradeneeded = e => e.target.result.createObjectStore('keys');
req.onsuccess = e => res(e.target.result);
req.onerror = rej;
});
},
_idbGet(db, key) {
return new Promise((res, rej) => {
const tx = db.transaction('keys', 'readonly');
const req = tx.objectStore('keys').get(key);
req.onsuccess = e => res(e.target.result);
req.onerror = rej;
});
},
_idbPut(db, key, val) {
return new Promise((res, rej) => {
const tx = db.transaction('keys', 'readwrite');
tx.objectStore('keys').put(val, key);
tx.oncomplete = res;
tx.onerror = rej;
});
},
async _decryptConversationMessages() {
if (!window._e2ePartnerKey || !window._e2eKeyPair) return;
await Promise.all(this.messages.map(async (msg, i) => {
if (msg.content?.startsWith('e2e:')) {
this.messages[i].content = await this._e2eDecrypt(msg.content, window._e2ePartnerKey);
}
}));
},

// ── /E2E ───────────────────────────────────────────────────────────────────

async saveStickerToMyLibrary(message) {
if (this.stickerSaveBusy) return;
this.stickerSaveBusy = true;
try {
const res = await fetch(message.attachmentUrl);
const blob = await res.blob();
const isAnimated = message.messageType === 'sticker_animated';
const fd = new FormData();
fd.append('type', isAnimated ? 'animated' : 'static');
fd.append('file', blob, isAnimated ? 'sticker.webm' : 'sticker.png');
const saveRes = await fetch(this.stickersStoreRoute, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await saveRes.json();
if (d.success) {
this.stickerList = [d.data, ...this.stickerList];
this.showToast('تم حفظ الملصق في مكتبتك', 'success');
} else {
this.showToast(d.message || 'تعذر حفظ الملصق', 'error');
}
} catch (_) {
this.showToast('تعذر حفظ الملصق. تحقق من اتصالك', 'error');
} finally {
this.stickerSaveBusy = false;
}
},

findStickerByUrl(url) {
if (!url) return null;
const norm = (u) => String(u).split('?')[0];
return this.stickerList.find(s => norm(s.url) === norm(url))
|| this.stickerFavorites.find(s => norm(s.url) === norm(url))
|| this.stickerRecent.find(s => norm(s.url) === norm(url))
|| null;
},

isStickerFavoritedByUrl(url) {
const sticker = this.findStickerByUrl(url);
if (!sticker) return false;
return this.stickerFavorites.some(s => s.id === sticker.id);
},

async toggleStickerFavoriteFromMessage(message) {
await this.ensureStickersLoaded();
const sticker = this.findStickerByUrl(message?.attachmentUrl);
if (!sticker) {
this.showToast('تعذر العثور على الملصق في مكتبتك', 'error');
return;
}
await this.toggleStickerFavorite(sticker);
},

async toggleStickerFavorite(sticker) {
const url = this.stickersFavoriteRouteTemplate.replace('__STICKER_ID__', sticker.id);
try {
const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken } });
const d = await res.json();
if (d.success) {
if (d.isFavorite) {
this.stickerFavorites = [...this.stickerFavorites, sticker];
} else {
this.stickerFavorites = this.stickerFavorites.filter(s => s.id !== sticker.id);
}
}
} catch (_) {}
},

async deleteSticker(sticker) {
const url = this.stickersDestroyRouteTemplate.replace('__STICKER_ID__', sticker.id);
try {
const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken } });
const d = await res.json();
if (d.success) {
this.stickerList = this.stickerList.filter(s => s.id !== sticker.id);
this.stickerFavorites = this.stickerFavorites.filter(s => s.id !== sticker.id);
this.stickerRecent = this.stickerRecent.filter(s => s.id !== sticker.id);
}
} catch (_) {}
},

openCreateStickerModal(mode) {
this.createStickerMode = mode;
this.createStickerModalOpen = true;
this.stickerImageFile = null;
this.stickerImageBlob = null;
this.stickerImagePreviewUrl = null;
this.stickerImageOriginalUrl = null;
this.stickerImageOriginalBlob = null;
this.stickerImageChoice = 'removed';
this.stickerImageError = '';
this.stickerVideoFile = null;
this.stickerVideoUrl = null;
this.stickerVideoError = '';
this.videoTrimStart = 0;
this.videoTrimEnd = 3;
},

closeCreateStickerModal() {
this.createStickerModalOpen = false;
if (this.stickerVideoUrl) URL.revokeObjectURL(this.stickerVideoUrl);
this.stickerVideoUrl = null;
},

async handleStickerImageFile(event) {
const file = event.target.files?.[0];
if (!file) return;
this.stickerImageFile = file;
this.stickerImageError = '';
this.stickerImageProcessing = true;
this.stickerImagePreviewUrl = null;
this.stickerImageOriginalUrl = URL.createObjectURL(file);
this.stickerImageOriginalBlob = file;
this.stickerImageChoice = 'removed';

try {
const { removeBackground } = await import('{{ asset('js/background-removal/background-removal.js') }}');
const blob = await removeBackground(file);
this.stickerImageBlob = blob;
this.stickerImagePreviewUrl = URL.createObjectURL(blob);
} catch (err) {
this.stickerImageError = 'تعذرت إزالة الخلفية تلقائياً. يمكنك حفظ الملصق بالصورة الأصلية بدلاً من ذلك.';
this.stickerImageChoice = 'original';
} finally {
this.stickerImageProcessing = false;
}
},

async confirmSaveImageSticker() {
const useOriginal = this.stickerImageChoice === 'original';
const blobToSave = useOriginal ? this.stickerImageOriginalBlob : this.stickerImageBlob;
if (!blobToSave) return;
const fd = new FormData();
fd.append('type', 'static');
fd.append('file', blobToSave, useOriginal ? 'sticker.png' : 'sticker.png');
try {
const res = await fetch(this.stickersStoreRoute, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await res.json();
if (d.success) {
this.stickerList = [d.data, ...this.stickerList];
this.closeCreateStickerModal();
this.showToast('تم إنشاء الملصق بنجاح', 'success');
} else {
this.stickerImageError = d.message || 'تعذر حفظ الملصق';
}
} catch (_) {
this.stickerImageError = 'تعذر حفظ الملصق. تحقق من اتصالك';
}
},

handleStickerVideoFile(event) {
const file = event.target.files?.[0];
if (!file) return;
this.stickerVideoFile = file;
this.stickerVideoError = '';
if (this.stickerVideoUrl) URL.revokeObjectURL(this.stickerVideoUrl);
this.stickerVideoUrl = URL.createObjectURL(file);
this.$nextTick(() => {
const v = this.$refs.stickerTrimVideo;
if (!v) return;
v.onloadedmetadata = () => {
this.stickerVideoDuration = v.duration || 0;
this.videoTrimStart = 0;
this.videoTrimEnd = Math.min(3, this.stickerVideoDuration);
};
});
},

async confirmVideoTrim() {
const video = this.$refs.stickerTrimVideo;
if (!video) return;
this.stickerVideoProcessing = true;
this.stickerVideoError = '';

try {
const duration = Math.min(3, this.videoTrimEnd - this.videoTrimStart);
if (duration <= 0) {
this.stickerVideoError = 'حدد جزءاً صالحاً من الفيديو (حتى 3 ثوانٍ).';
this.stickerVideoProcessing = false;
return;
}

const canvas = document.createElement('canvas');
canvas.width = video.videoWidth || 320;
canvas.height = video.videoHeight || 320;
const ctx = canvas.getContext('2d');
const stream = canvas.captureStream(25);
const recorder = new MediaRecorder(stream, { mimeType: 'video/webm' });
const chunks = [];
recorder.ondataavailable = (e) => { if (e.data.size) chunks.push(e.data); };

const recordingDone = new Promise(resolve => { recorder.onstop = resolve; });

video.currentTime = this.videoTrimStart;
await new Promise(resolve => { video.onseeked = resolve; });

recorder.start();
video.muted = true;
await video.play();

const drawFrame = () => {
if (video.currentTime >= this.videoTrimStart + duration || video.paused) {
video.pause();
recorder.stop();
return;
}
ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
requestAnimationFrame(drawFrame);
};
drawFrame();

await recordingDone;
const blob = new Blob(chunks, { type: 'video/webm' });

const fd = new FormData();
fd.append('type', 'animated');
fd.append('file', blob, 'sticker.webm');
const res = await fetch(this.stickersStoreRoute, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await res.json();
if (d.success) {
this.stickerList = [d.data, ...this.stickerList];
this.closeCreateStickerModal();
this.showToast('تم إنشاء الملصق المتحرك بنجاح', 'success');
} else {
this.stickerVideoError = d.message || 'تعذر حفظ الملصق';
}
} catch (err) {
this.stickerVideoError = 'تعذرت معالجة الفيديو في هذا المتصفح.';
} finally {
this.stickerVideoProcessing = false;
}
},

init() {

this.loadFoldersConfig();

try {

const mutedRaw = localStorage.getItem('messaging_muted_until_by_contact');

this.mutedUntilByContact = mutedRaw ? JSON.parse(mutedRaw) : {};

} catch (_) {

this.mutedUntilByContact = {};

}

this.contacts = (@json(($contactsJson ?? []), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) || []).map(contact => ({

...contact,

avatar_url: this.normalizeAvatarUrl(contact.avatar_url),

lastMessage: this.sanitizeDisplayText(contact.lastMessage, ''),

lastSeen: this.sanitizeDisplayText(contact.lastSeen, this.t.unavailable),

lastSeenAt: contact.lastSeenAt || contact.last_seen_at || null,

}));

this.potentialNewContacts = (@json(($newContactsJson ?? []), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) || []).map(contact => ({

...contact,

avatar_url: this.normalizeAvatarUrl(contact.avatar_url),

lastSeenAt: contact.lastSeenAt || contact.last_seen_at || null,

}));

this.messages = [];

this.selectedContact = null;

this.loadSavedMessageIds();

// One-time cleanup for legacy scroll payloads saved before formula/version fix.

this.contacts.forEach(contact => {

const key = this.scrollStorageKey(contact.id);

const raw = localStorage.getItem(key);

if (!raw) return;

try {

const saved = JSON.parse(raw);

if (Number(saved?.v || 1) !== this.scrollStorageVersion()) {

localStorage.removeItem(key);

}

} catch (_) {

localStorage.removeItem(key);

}

});

const scopedStoredId = localStorage.getItem(this.selectionStorageKey());

const storedId = scopedStoredId || localStorage.getItem('messaging_selected_contact_id');

if (!storedId) return;

const saved = this.contacts.find(c => String(c.id) === String(storedId));

if (saved) this.selectContact(saved);

},

// Search

openSearchPanel() {

this.searchPanelOpen = true;

this.$nextTick(() => document.getElementById('search-panel-input')?.focus());

},

closeSearchPanel() {

this.searchPanelOpen = false;

this.searchPanelQuery = '';

this.searchPanelResults = [];

},

onSearchInput() {

if (this.searchPanelTimer) clearTimeout(this.searchPanelTimer);

const q = this.searchPanelQuery.trim();

if (q.length < 2) { this.searchPanelResults = []; return; }

this.searchPanelLoading = true;

const searchUrl = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.search') ? route('teacher.messaging.search') : null)

: (\Illuminate\Support\Facades\Route::has('messaging.search')

? route('messaging.search')

: (\Illuminate\Support\Facades\Route::has('student.messaging.search') ? route('student.messaging.search') : null))

);

if (!searchUrl) { this.searchPanelLoading = false; return; }

this.searchPanelTimer = setTimeout(async () => {

try {

const r = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {

headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },

});

const d = await r.json();

this.searchPanelResults = d.success ? (d.data || []) : [];

} catch (_) {}

this.searchPanelLoading = false;

}, 380);

},

async jumpToSearchResult(msg) {

const targetId = Number(msg?.id || msg?.messageId || msg?.message_id || 0);

const senderId = msg.sender_id ?? msg.senderId;

const recipientId = msg.recipient_id ?? msg.recipientId;

const contactId = Number(senderId) === Number(this.currentUserId) ? Number(recipientId) : Number(senderId);

const contact = this.contacts.find(c => Number(c.id) === contactId) || this.potentialNewContacts.find(c => Number(c.id) === contactId);

if (!contact || !targetId) return;

this.closeSearchPanel();

if (!this.selectedContact || Number(this.selectedContact.id) !== Number(contact.id)) {

await this.openConversation(contact);

} else if (!this.messages.some(m => Number(m.id) === targetId)) {

await this.loadMessages(false);

}

await this.jumpToMessageById(targetId, { loadOlder: true });

},

async jumpToMessageById(targetId, options = {}) {

const id = Number(targetId || 0);

if (!id) return false;

const findAndFocus = () => {

const el = document.getElementById(`message-${id}`);

if (!el) return false;

el.scrollIntoView({ block: 'center', behavior: 'smooth' });

this.highlightedMessageId = id;

if (this.highlightTimer) clearTimeout(this.highlightTimer);

this.highlightTimer = setTimeout(() => { this.highlightedMessageId = null; }, 1800);

return true;

};

await this.$nextTick();

if (findAndFocus()) return true;

if (options.loadOlder) {

let safety = 0;

while (Number(this.historyPage) < Number(this.historyLastPage) && safety < 20) {

safety += 1;

await this.loadOlderMessages();

await this.$nextTick();

if (findAndFocus()) return true;

}

}

this.showToast('لم يتم العثور على الرسالة في المحادثة المحملة', 'warning');

return false;

},

// Reactions

getMessageReactions(messageId) {

return this.reactionsMap[messageId] || [];

},

openReactionPicker(messageId) {

this.reactionPickerMessageId = messageId;

},

closeReactionPicker() {

this.reactionPickerMessageId = null;

},

onMessageHoverEnter(messageId) {

// Acts buttons now show via CSS :hover; no JS timer needed

if (this.toolsHoverTimer) { clearTimeout(this.toolsHoverTimer); this.toolsHoverTimer = null; }

},

onMessageHoverLeave(messageId) {

if (this.toolsHoverTimer) {

clearTimeout(this.toolsHoverTimer);

this.toolsHoverTimer = null;

}

if (this.toolsMessageId === messageId) this.toolsMessageId = null;

},

openMessageTools(messageId) {

this.toolsMessageId = messageId;

},

onMessageTouchStart(messageId, event, message) {

if (this.toolsTouchTimer) clearTimeout(this.toolsTouchTimer);

this.toolsTouchTimer = setTimeout(() => {

this.toolsMessageId = messageId;

const touch = event?.touches?.[0];

if (touch) this.openMessageContext({ clientX: touch.clientX, clientY: touch.clientY }, message);

}, 520);

},

onMessageTouchEnd() {

if (this.toolsTouchTimer) {

clearTimeout(this.toolsTouchTimer);

this.toolsTouchTimer = null;

}

},

toggleHeaderMenu() {

this.headerMenuOpen = !this.headerMenuOpen;

},

openWallpaperPicker() {

this.wallpaperPickerOpen = true;

this.headerMenuOpen = false;

this.showCustomMute = false;

this.wallpaperPickerMenuOpen = false;

},

openTonePicker() {

this.selectedToneTemp = this.perContactTone;

const cid = this.selectedContact ? String(this.selectedContact.id) : null;

if (cid && this.toneVolumeByContact[cid] !== undefined) {
this.tonePickerVolume = this.toneVolumeByContact[cid];
} else {
this.tonePickerVolume = safeLocalJson('messaging_tone_volume', 100);
}

// Show saved custom tone preview if applicable
this.customTonePreviewUrl = null;
this.customToneFileName = '';
if (cid && this.perContactTone === '__custom__') {
const savedUri = localStorage.getItem('conv_custom_tone_' + cid);
if (savedUri) {
this.customTonePreviewUrl = savedUri;
this.customToneFileName = 'نغمة مخصصة';
}
}

this.tonePickerOpen = true;

},

toggleSoundEnabled() {

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

const current = this.soundEnabledByContact[cid];

const newVal = current !== undefined ? !current : !this.settingSoundEnabled;

this.soundEnabledByContact[cid] = newVal;

localStorage.setItem('conv_sound_enabled', JSON.stringify(this.soundEnabledByContact));

this.showToast(newVal ? 'تم تفعيل الصوت' : 'تم كتم الصوت', 'info', 1800);

},

unmuteContact() {

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

delete this.mutedUntilByContact[cid];

localStorage.setItem('messaging_muted_until_by_contact', JSON.stringify(this.mutedUntilByContact));

this.headerSub = null;

this.headerMenuOpen = false;

this.showToast('تم إلغاء الكتم', 'info', 1800);

},

muteForMinutes(minutes) {

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

const until = Date.now() + (Number(minutes) * 60 * 1000);

this.mutedUntilByContact[cid] = until;

localStorage.setItem('messaging_muted_until_by_contact', JSON.stringify(this.mutedUntilByContact));

this.closeMuteMenus();

this.showToast(`تم الكتم لمدة ${minutes} دقيقة`, 'info', 1800);

},

muteCustom() {

if (!this.selectedContact) return;

const totalMin = (Number(this.muteCustomDays) * 1440) + (Number(this.muteCustomHours) * 60) + Number(this.muteCustomMinutes);

if (totalMin <= 0) { this.showToast('الرجاء تحديد مدة', 'error', 1800); return; }

const cid = String(this.selectedContact.id);

// Save last custom mute per contact
this.lastCustomMute[cid] = { days: this.muteCustomDays, hours: this.muteCustomHours, minutes: this.muteCustomMinutes };
localStorage.setItem('conv_last_custom_mute', JSON.stringify(this.lastCustomMute));

const until = Date.now() + (totalMin * 60 * 1000);

this.mutedUntilByContact[cid] = until;

localStorage.setItem('messaging_muted_until_by_contact', JSON.stringify(this.mutedUntilByContact));

this.closeMuteMenus();

this.showToast(`تم الكتم لمدة ${totalMin} دقيقة`, 'info', 1800);

},

muteForever() {

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

this.mutedUntilByContact[cid] = Number.MAX_SAFE_INTEGER;

localStorage.setItem('messaging_muted_until_by_contact', JSON.stringify(this.mutedUntilByContact));

this.closeMuteMenus();

this.showToast('تم الكتم للأبد', 'info', 1800);

},

closeMuteMenus() {

this.muteOptionsOpen = false;

this.showCustomMute = false;

this.headerSub = null;

this.headerMenuOpen = false;

this.wallpaperPickerMenuOpen = false;

},

previewTone(toneId) {

this.tonePreviewId = toneId;

// Play a short audio preview using Web Audio API

try {

const ctx = new (window.AudioContext || window.webkitAudioContext)();

if (ctx.state === 'suspended') ctx.resume();

const osc = ctx.createOscillator();

const gain = ctx.createGain();

osc.connect(gain);

gain.connect(ctx.destination);

const baseFreq = {default:800,'soft-bell':1200,classic:660,digital:1000,chime:1400,pop:520,ping:2000,gentle:440}[toneId] || 800;

osc.frequency.value = baseFreq;

osc.type = toneId==='digital'?'square':toneId==='soft-bell'||toneId==='chime'?'sine':toneId==='pop'?'triangle':toneId==='gentle'?'sine':'sine';

gain.gain.setValueAtTime(this.tonePickerVolume/100 * 0.3, ctx.currentTime);

gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);

osc.start(ctx.currentTime);

osc.stop(ctx.currentTime + 0.5);

osc.onended = () => { this.tonePreviewId = null; };

setTimeout(() => { if (this.tonePreviewId === toneId) this.tonePreviewId = null; }, 600);

} catch(_) { this.tonePreviewId = null; }

},

triggerToneUpload() {

this.$refs.toneFileInput?.click();

},

onToneFileSelected(e) {

const file = e.target.files?.[0];

if (!file) return;

// Validate size (max 500KB)

const MAX_SIZE = 500 * 1024;

if (file.size > MAX_SIZE) {

this.showToast('الملف كبير جداً. الحد الأقصى 500 كيلوبايت', 'error', 2500);

return;

}

const MAX_DUR = 4;

const objUrl = URL.createObjectURL(file);

const audio = new Audio(objUrl);

audio.preload = 'metadata';

const metaTimeout = setTimeout(() => {
if (!this.customTonePreviewUrl) {
this.customToneFile = file;
this.customToneFileName = file.name;
this.customTonePreviewUrl = objUrl;
this.showToast('تم اختيار النغمة', 'success', 1500);
}
}, 3000);

audio.onloadedmetadata = () => {

clearTimeout(metaTimeout);

const dur = audio.duration;

if (Number.isFinite(dur) && dur > MAX_DUR) {

this.showToast('يتم قص النغمة إلى 4 ثوانٍ...', 'info', 2000);

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
fetch(objUrl)
.then(r => r.arrayBuffer())
.then(buf => audioCtx.decodeAudioData(buf))
.then((decoded) => {

const trimDur = Math.min(decoded.duration, MAX_DUR);
const sr = decoded.sampleRate;
const chCount = decoded.numberOfChannels;
const trimLen = Math.round(trimDur * sr);

// Slice PCM data directly from decoded buffer
const channelsData = [];
for (let ch = 0; ch < chCount; ch++) {
channelsData.push(decoded.getChannelData(ch).slice(0, trimLen));
}

// Encode multi-channel PCM to WAV
const bufSize = 44 + trimLen * 2 * chCount;
const wavBuf = new ArrayBuffer(bufSize);
const v = new DataView(wavBuf);
const w = (o, s) => { for (let i = 0; i < s.length; i++) v.setUint8(o + i, s.charCodeAt(i)); };
w(0, 'RIFF'); v.setUint32(4, 36 + trimLen * 2 * chCount, true);
w(8, 'WAVE'); w(12, 'fmt ');
v.setUint32(16, 16, true); v.setUint16(20, 1, true);
v.setUint16(22, chCount, true); v.setUint32(24, sr, true);
v.setUint32(28, sr * 2 * chCount, true); v.setUint16(32, 2 * chCount, true);
v.setUint16(34, 16, true);
w(36, 'data'); v.setUint32(40, trimLen * 2 * chCount, true);
for (let i = 0; i < trimLen; i++) {
for (let ch = 0; ch < chCount; ch++) {
const s = Math.max(-1, Math.min(1, channelsData[ch][i]));
v.setInt16(44 + (i * chCount + ch) * 2, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
}
}

const blob = new Blob([wavBuf], { type: 'audio/wav' });
const dataUrlReader = new FileReader();
dataUrlReader.onload = (ev2) => {
this.customToneFile = new File([blob], file.name.replace(/\.[^.]+$/, '.wav'), { type: 'audio/wav' });
this.customToneFileName = file.name + ' (مقصصة)';
this.customTonePreviewUrl = ev2.target.result;
};
dataUrlReader.readAsDataURL(blob);

URL.revokeObjectURL(objUrl);

}).catch(() => {
clearTimeout(metaTimeout);
this.customToneFile = file;
this.customToneFileName = file.name;
this.customTonePreviewUrl = objUrl;
});

} else {
this.customToneFile = file;
this.customToneFileName = file.name;
this.customTonePreviewUrl = objUrl;
this.showToast('تم اختيار النغمة', 'success', 1500);
}
};

audio.onerror = () => {
clearTimeout(metaTimeout);
this.customToneFile = file;
this.customToneFileName = file.name;
this.customTonePreviewUrl = objUrl;
this.showToast('تم اختيار النغمة', 'success', 1500);
};

},

saveToneSettings() {

if (!this.selectedContact) return;

const cid = String(this.selectedContact.id);

// Persist volume per-contact
this.toneVolumeByContact[cid] = this.tonePickerVolume;
localStorage.setItem('conv_tone_volume', JSON.stringify(this.toneVolumeByContact));
localStorage.setItem('messaging_tone_volume', JSON.stringify(this.tonePickerVolume));

// Save per-contact tone

if (this.customToneFile) {

const reader = new FileReader();

reader.onload = (ev) => {

const dataUrl = ev.target.result;

this.toneByContact[cid] = '__custom__';

localStorage.setItem('conv_custom_tone_' + cid, dataUrl);

localStorage.setItem('conv_tones', JSON.stringify(this.toneByContact));

this.selectedTone = '__custom__';

this.selectedToneTemp = '__custom__';

this.customTonePreviewUrl = dataUrl;

this.customToneFile = null;

this.showToast('تم حفظ النغمة المخصصة', 'success', 1500);

};

reader.readAsDataURL(this.customToneFile);

} else {

const chosenTone = this.selectedToneTemp || this.toneByContact[cid] || this.selectedTone;

this.toneByContact[cid] = chosenTone;

localStorage.setItem('conv_tones', JSON.stringify(this.toneByContact));

this.selectedTone = chosenTone;

localStorage.setItem('messaging_selected_tone', chosenTone);

this.cleanupTonePicker();

this.showToast('تم حفظ إعدادات النغمة', 'success', 1500);

}

},

getNotificationVolume(contactId) {

const cid = String(contactId);

if (this.toneVolumeByContact[cid] !== undefined) return this.toneVolumeByContact[cid];

const g = safeLocalJson('messaging_tone_volume', null);

return g !== null ? g : 100;

},

playNotificationTone(contactId) {

const cid = String(contactId);

// Check mute
const until = this.mutedUntilByContact[cid];
if (until && Date.now() < until) return;

// Check sound enabled
const soundVal = this.soundEnabledByContact[cid];
const soundEnabled = soundVal !== undefined ? soundVal : this.settingSoundEnabled;
if (!soundEnabled) return;

const tone = this.toneByContact[cid] || localStorage.getItem('messaging_selected_tone') || 'default';

const volume = this.getNotificationVolume(contactId) / 100;

if (tone === '__custom__') {

const dataUri = localStorage.getItem('conv_custom_tone_' + cid);

if (dataUri) {

try {

const audio = new Audio(dataUri);

audio.volume = volume * 0.5;

audio.play().catch(() => {});

} catch (_) {}

}

return;

}

try {

const ctx = new (window.AudioContext || window.webkitAudioContext)();

if (ctx.state === 'suspended') ctx.resume();

const osc = ctx.createOscillator();

const gain = ctx.createGain();

osc.connect(gain);

gain.connect(ctx.destination);

const baseFreq = {default:800,'soft-bell':1200,classic:660,digital:1000,chime:1400,pop:520,ping:2000,gentle:440}[tone] || 800;

osc.frequency.value = baseFreq;

osc.type = tone==='digital'?'square':tone==='soft-bell'||tone==='chime'?'sine':tone==='pop'?'triangle':tone==='gentle'?'sine':'sine';

gain.gain.setValueAtTime(volume * 0.25, ctx.currentTime);

gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);

osc.start(ctx.currentTime);

osc.stop(ctx.currentTime + 0.5);

} catch(_) {}

},

cleanupTonePicker() {

this.tonePickerOpen = false;

this.customToneFile = null;

this.customTonePreviewUrl = null;

this.customToneFileName = '';

this.selectedToneTemp = null;

this.tonePreviewId = null;

},

applyChatThemeVars() {

const feed = this.$refs.messagesContainer;

if (!feed) return;

const theme = this.activeChatTheme;

const isDark = document.documentElement.getAttribute('data-theme') !== 'light';

if (theme && theme.id) {

if (isDark) {

for (const [key, val] of Object.entries(theme.vars || {})) feed.style.setProperty(key, val);

for (const key of Object.keys(theme.varsLight || {})) {

if (!(key in (theme.vars||{}))) feed.style.removeProperty(key);

}

} else {

for (const [key, val] of Object.entries(theme.varsLight || theme.vars || {})) feed.style.setProperty(key, val);

for (const key of Object.keys(theme.vars || {})) {

if (!(key in (theme.varsLight||{}))) feed.style.removeProperty(key);

}

}

if (theme.wp >= 0) feed.style.background = this.wallpapers[theme.wp] || '';

} else {

feed.style.removeProperty('background');

['--th-bubble-tint','--th-accent','--th-hover'].forEach(k => feed.style.removeProperty(k));

}

},

selectChatTheme(themeId) {

if (!this.selectedContact) return;

const theme = this.chatThemeDefs.find(t => t.id === themeId);

if (theme) {

if (theme.id) {

this.conversationThemes[String(this.selectedContact.id)] = theme.id;

this.conversationWallpapers[String(this.selectedContact.id)] = theme.wp;

} else {

delete this.conversationThemes[String(this.selectedContact.id)];

delete this.conversationWallpapers[String(this.selectedContact.id)];

}

localStorage.setItem('conv_themes', JSON.stringify(this.conversationThemes));

localStorage.setItem('conv_wallpapers', JSON.stringify(this.conversationWallpapers));

this.persistWallpaper(this.selectedContact.id, theme.id || 'default');

}

this.applyChatThemeVars();

this.wallpaperPickerOpen = false;

},

applyWallpaper(index) {

const feed = this.$refs.messagesContainer;

if (index === -1) {

// Reset to default

if (this.selectedContact) {

delete this.conversationThemes[String(this.selectedContact.id)];

delete this.conversationWallpapers[String(this.selectedContact.id)];

localStorage.setItem('conv_themes', JSON.stringify(this.conversationThemes));

localStorage.setItem('conv_wallpapers', JSON.stringify(this.conversationWallpapers));

this.persistWallpaper(this.selectedContact.id, 'default');

} else {

this.activeWallpaper = 0;

localStorage.setItem('messaging_wallpaper_idx', '0');

}

if (feed) feed.style.removeProperty('background');

this.wallpaperPickerOpen = false;

return;

}

const bg = this.wallpapers[index] || this.wallpapers[0];

if (this.selectedContact) {

this.conversationWallpapers[String(this.selectedContact.id)] = index;

delete this.conversationThemes[String(this.selectedContact.id)];

localStorage.setItem('conv_wallpapers', JSON.stringify(this.conversationWallpapers));

localStorage.setItem('conv_themes', JSON.stringify(this.conversationThemes));

this.persistWallpaper(this.selectedContact.id, 'idx:' + index);

} else {

this.activeWallpaper = index;

localStorage.setItem('messaging_wallpaper_idx', String(index));

}

if (feed) {

feed.style.background = bg;

['--th-bubble-tint','--th-accent','--th-hover'].forEach(k => feed.style.removeProperty(k));

}

this.wallpaperPickerOpen = false;

},

async persistWallpaper(contactId, wallpaperKey) {

if (!this.wallpaperSetRoute || this.wallpaperSetRoute === '#' || !contactId) return;

try {

const res = await fetch(this.wallpaperSetRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ contact_id: contactId, wallpaper_key: wallpaperKey }),

});

if (!res.ok) throw new Error('wallpaper_save_failed');

} catch (_) { this.showToast('تعذر حفظ الخلفية على الخادم', 'error', 1600); }

},

async loadWallpaperForContact(contactId) {

if (!this.wallpaperGetRoute || this.wallpaperGetRoute === '#' || !contactId) return;

try {

const url = this.wallpaperGetRoute + (this.wallpaperGetRoute.includes('?') ? '&' : '?') + 'contact_id=' + encodeURIComponent(contactId);

const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });

if (!res.ok) return;

const j = await res.json().catch(() => null);

const key = j?.data?.wallpaper_key;

const customGradient = j?.data?.custom_gradient;

if (!key || key === 'default') {

delete this.conversationWallpapers[String(contactId)];

} else if (customGradient) {

const idx = this.wallpapers.findIndex(w => String(w) === String(customGradient));

if (idx >= 0) this.conversationWallpapers[String(contactId)] = idx;

} else if (key.startsWith('idx:')) {

const idx = parseInt(key.slice(4), 10);

if (!Number.isNaN(idx)) this.conversationWallpapers[String(contactId)] = idx;

}

localStorage.setItem('conv_wallpapers', JSON.stringify(this.conversationWallpapers));

// Apply immediately if this is still the open conversation.

if (this.selectedContact && Number(this.selectedContact.id) === Number(contactId)) {

const feed = this.$refs.messagesContainer;

const savedIdx = this.conversationWallpapers[String(contactId)];

if (feed) feed.style.background = (savedIdx != null) ? (this.wallpapers[savedIdx] || '') : '';

this.$nextTick(() => this.applyChatThemeVars());

}

} catch (_) {}

},

openSavedMessages() {

this.savedMessagesOpen = true;

this.headerMenuOpen = false;

this.loadSavedMessages();

},

async loadSavedMessages() {

this.savedMessagesLoading = true;

try {

const res = await fetch(this.savedListRoute, { headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });

const data = await res.json();

if (data.success) {

this.savedMessagesList = data.data || [];

this.savedMessageIds = this.savedMessagesList.map(m => Number(m.id));

}

} catch (e) { console.warn('loadSavedMessages error', e); }

finally { this.savedMessagesLoading = false; }

},

async loadSavedMessageIds() {

if (!this.savedIdsRoute || this.savedIdsRoute === '#') return;

try {

const res = await fetch(this.savedIdsRoute, { headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });

const data = await res.json();

if (data.success) this.savedMessageIds = (data.ids || []).map(id => Number(id)).filter(Boolean);

} catch (e) { console.warn('loadSavedMessageIds error', e); }

},

async saveMessageById(messageId) {

const id = Number(messageId || 0);

if (!id) return false;

const url = String(this.saveRouteTemplate || '').replace('__MSG_ID__', id);

try {

const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });

const data = await res.json();

if (data.success) {

if (!this.savedMessageIds.includes(id)) this.savedMessageIds.push(id);

this.showToast('تم الحفظ', 'success');

return true;

}

this.showToast(data.message || 'تعذر حفظ الرسالة', 'error');

} catch (e) { console.warn('saveMessage error', e); this.showToast('تعذر حفظ الرسالة', 'error'); }

return false;

},

async unsaveMessageById(messageId) {

const id = Number(messageId || 0);

if (!id) return false;

const url = String(this.unsaveRouteTemplate || this.saveRouteTemplate || '').replace('__MSG_ID__', id);

try {

const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });

const data = await res.json();

if (data.success) {

this.savedMessageIds = this.savedMessageIds.filter(savedId => Number(savedId) !== id);

this.savedMessagesList = this.savedMessagesList.filter(m => Number(m.id) !== id);

this.showToast('تمت الإزالة من المحفوظات', 'info');

return true;

}

this.showToast(data.message || 'تعذر إزالة الرسالة من المحفوظات', 'error');

} catch (e) { console.warn('unsaveMessage error', e); this.showToast('تعذر إزالة الرسالة من المحفوظات', 'error'); }

return false;

},

async jumpToSavedMessage(msg) {

this.savedMessagesOpen = false;

const senderId = msg.senderId ?? msg.sender_id;

const recipientId = msg.recipientId ?? msg.recipient_id;

const contactId = Number(senderId) === Number(this.currentUserId) ? Number(recipientId) : Number(senderId);

const contact = this.contacts.find(c => Number(c.id) === Number(contactId)) || this.potentialNewContacts.find(c => Number(c.id) === Number(contactId));

if (contact && (!this.selectedContact || Number(this.selectedContact.id) !== Number(contact.id))) {

await this.openConversation(contact);

}

await this.jumpToMessageById(Number(msg.id), { loadOlder: true });

},

openMediaGallery() { this.mediaGalleryOpen = true; this.mediaGalleryTab = 'images'; this.headerMenuOpen = false; },

_groupGalleryByMonth(items) {
    const groups = [];
    const map = {};
    const tz = 'Asia/Riyadh';
    const sorted = [...items].sort((a, b) => new Date(b.createdAt || b.created_at || 0) - new Date(a.createdAt || a.created_at || 0));
    for (const m of sorted) {
        const d = new Date(m.createdAt || m.created_at || 0);
        const key = d.toLocaleDateString('en-CA', { year: 'numeric', month: '2-digit', timeZone: tz }).substring(0, 7);
        if (!map[key]) {
            const label = d.toLocaleDateString('ar-SA-u-ca-gregory', { year: 'numeric', month: 'long', timeZone: tz });
            map[key] = { key, label, items: [] };
            groups.push(map[key]);
        }
        map[key].items.push(m);
    }
    return groups;
},

extractLink(text) {

const m = String(text || '').match(/https?:\/\/[^\s]+/);

return m ? m[0] : '';

},

openStatusByContactId(contactId) {

const group = (this.contactStatuses || []).find(s => Number(s.user_id) === Number(contactId));

if (group) this.openStatusViewer(group);

},

hasAnyStatus(contactId) {

const id = Number(contactId);

return (this.contactStatuses || []).some(s => Number(s.user_id) === id);

},

hasUnseenStatus(contactId) {

const id = Number(contactId);

const group = (this.contactStatuses || []).find(s => Number(s.user_id) === id);

return !!group && !group.all_viewed;

},  getStatusTextLayers(status) {
      if (!status) return [];
      let layers = [];
      if (status.text_layers) {
          try {
              const parsed = typeof status.text_layers === 'string' ? JSON.parse(status.text_layers) : status.text_layers;
              if (Array.isArray(parsed)) layers = parsed;
          } catch(e) {}
      }
      if (layers.length === 0 && (status.text_content || status.textContent)) {
          layers.push({
              content: status.text_content || status.textContent,
              fontStyle: status.font_style || status.fontStyle,
              fontSize: status.font_size || status.fontSize || 42,
              textColor: status.text_color || status.textColor || '#ffffff',
              textPosX: status.textPosX ?? 50,
              textPosY: status.textPosY ?? 50,
              rotate: status.textRotate ?? 0,
              textBgStyle: status.textBgStyle ?? 'none'
          });
      }
      return layers;
  },

statusViewerBackground(status) {

const media = status?.contentUrl || (status?.content_url ? ('/storage/' + status.content_url) : null);

  let base = status?.bg_color || status?.bgColor || '#000';

if (media && (status?.type === 'image' || status?.type === 'video')) {
    return `linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.35)), url('${media}') center/cover no-repeat`;
}

if (base.includes('gradient') && !base.includes('center/cover')) {
    return base + ' center/cover no-repeat';
}

return base;

},

onStatusVideoReady(e) {

const dur = Number(e?.target?.duration || 0);

if (dur > 0) this.statusViewerDurationMs = Math.max(3000, Math.round(dur * 1000));

this.statusViewerReady = true;

this.startStatusProgress();

},

openStatusStickerPicker() {

const stickers = ['❤️','😂','🔥','👏','💯','🤩','🥳','😘','😍','🤗'];
const pick = stickers[Math.floor(Math.random() * stickers.length)];

this.sendQuickStatusReaction(pick);

},

toggleFullEmoji() {

this.showStatusFullEmoji = !this.showStatusFullEmoji;

if (this.showStatusFullEmoji) {

this.statusReplyFocused = false;
this.showQuickEmojiBar = false;

this.statusPaused = true;

if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }

const v = this.$refs.statusViewerVideo;

if (v && !v.paused) v.pause();

} else {

this.statusPaused = false;

const v = this.$refs.statusViewerVideo;

if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});

if (this.statusViewerReady) this.startStatusProgress(false);

}

},

closeStatusFullEmoji() {

this.showStatusFullEmoji = false;

this.statusPaused = false;

const v = this.$refs.statusViewerVideo;

if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});

if (this.statusViewerReady) this.startStatusProgress(false);

},

handleStatusContentClick() {
if (this.statusLongPressFired) {
    this.statusLongPressFired = false;
    return;
}
this.toggleStatusPlayback();
},

startStatusLongPress() {
if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }
this.statusLongPressFired = false;
this.statusLongPressTimer = setTimeout(() => {
    this.statusLongPressActive = true;
    this.statusLongPressFired = true;
    this.statusPaused = true;
    const v = this.$refs.statusViewerVideo;
    if (v && !v.paused) v.pause();
}, 300);
},

endStatusLongPress() {
if (this.statusLongPressTimer) { clearTimeout(this.statusLongPressTimer); this.statusLongPressTimer = null; }
if (this.statusLongPressActive) {
    this.statusLongPressActive = false;
    this.statusPaused = false;
    const v = this.$refs.statusViewerVideo;
    if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});
    if (this.statusViewerReady) this.startStatusProgress(false);
}
},

cancelStatusLongPress() {
if (this.statusLongPressTimer) { clearTimeout(this.statusLongPressTimer); this.statusLongPressTimer = null; }
if (this.statusLongPressActive) {
    this.statusLongPressActive = false;
    this.statusPaused = false;
    const v = this.$refs.statusViewerVideo;
    if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});
    if (this.statusViewerReady) this.startStatusProgress(false);
}
},

toggleStatusPlayback() {

this.statusPaused = !this.statusPaused;

const v = this.$refs.statusViewerVideo;

if (this.currentStatus?.type === 'video' && v) {

if (this.statusPaused) v.pause(); else v.play().catch(() => {});

}

if (this.statusPaused) {

if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }

} else if (this.statusViewerReady) {

this.startStatusProgress(false);

}

},

jumpToStatusFromMessage(message) {

const id = Number(message?.statusRefId || 0);

if (!id) return;

const allGroups = [...(this.contactStatuses || []), { user_id: this.currentUserId, user_name: this.userName, user_avatar: this.currentUserAvatar, statuses: this.myStatuses || [], all_viewed: false }];

for (const g of allGroups) {

const idx = (g.statuses || []).findIndex(s => Number(s.id) === id);

if (idx !== -1) {

this.openStatusViewer(g);

this.statusViewerIndex = idx;

this.statusViewerProgress = 0;

if (this.currentStatus?.type === 'text') { this.statusViewerReady = true; this.startStatusProgress(); }

return;

}

}

this.showToast('الحالة غير متاحة الآن', 'warning');

},

startResize(e) {

if (!this.isDesktop) return;

this.isResizing = true;

const onMove = (ev) => {

// RTL fix: sidebar is on the RIGHT, measure from right

const max = Math.min(window.innerWidth * 0.65, 1100);

const min = 360;

const rightOffset = window.innerWidth - ev.clientX;

const width = Math.max(min, Math.min(max, rightOffset));

this.sidebarWidth = width;

document.documentElement.style.setProperty('--sidebar-width', `${width}px`);

};

const onUp = () => {

this.isResizing = false;

localStorage.setItem('messaging_sidebar_width', String(this.sidebarWidth));

window.removeEventListener('mousemove', onMove);

window.removeEventListener('mouseup', onUp);

};

window.addEventListener('mousemove', onMove);

window.addEventListener('mouseup', onUp);

},

openMessageContext(event, message) {

this.messageContextMessage = message;

if (message?.messageType === 'sticker_static' || message?.messageType === 'sticker_animated') {
this.ensureStickersLoaded();
}

this.messageContextOpen = true;

this.messageContextX = Math.max(8, Math.min(window.innerWidth - 290, Number(event?.clientX || 0)));

this.messageContextY = Math.max(8, Math.min(window.innerHeight - 320, Number(event?.clientY || 0)));

this.toolsMessageId = message?.id || null;

},

closeMessageContext() {

this.messageContextOpen = false;

this.messageContextMessage = null;

},

reactFromContext(emoji) {

if (!this.messageContextMessage) return;

this.toggleReaction(this.messageContextMessage, emoji);

this.messageContextOpen = false;

},

contextReply() {

if (!this.messageContextMessage) return;

this.replyToMessage(this.messageContextMessage);

this.messageContextOpen = false;

},

async contextCopy() {

const text = String(this.messageContextMessage?.content || '');

if (!text) return;

try { await navigator.clipboard.writeText(text); } catch (_) {}

this.messageContextOpen = false;

},

contextDelete() {

if (!this.messageContextMessage) return;

this.deleteMessage(this.messageContextMessage);

this.messageContextOpen = false;

},

async contextPin() {

if (!this.messageContextMessage) return;

if (!this.pinRoute || this.pinRoute === '#') { this.showToast('مسار التثبيت غير مفعّل', 'error'); return; }

const msg = this.messageContextMessage;

const previous = !!msg.isPinned;

msg.isPinned = !previous;

try {

const res = await fetch(this.pinRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ message_id: msg.id, pinned: msg.isPinned }),

});

const data = await res.json().catch(() => ({}));

if (!res.ok || !data.success) throw new Error('pin_failed');

msg.isPinned = !!data.data?.isPinned;

this.showToast(msg.isPinned ? 'تم تثبيت الرسالة' : 'تم إلغاء التثبيت', 'success', 1800);

} catch (_) {

msg.isPinned = previous;

this.showToast('تعذر تحديث التثبيت', 'error');

}

this.messageContextOpen = false;

},

contextForward() {

if (!this.messageContextMessage) return;

if (!this.forwardRoute || this.forwardRoute === '#') { this.showToast('مسار إعادة التوجيه غير مفعّل', 'error'); return; }

this.forwardSourceMessage = this.messageContextMessage;

this.forwardSelection = [];

this.forwardPickerOpen = true;

this.messageContextOpen = false;

},

closeForwardPicker() {

this.forwardPickerOpen = false;

this.forwardSourceMessage = null;

this.forwardSelection = [];

},

async submitForward() {

if (!this.forwardSourceMessage || !this.forwardSelection.length) return;

const ids = this.forwardSelection.map(Number);

// Handle saved messages (id=-1) locally
const savedIdx = ids.indexOf(-1);
if (savedIdx !== -1) {
ids.splice(savedIdx, 1);
const src = this.forwardSourceMessage;
const copy = { ...src, id: this.pendingMessageCounter--, created_at: new Date().toISOString(), recipient_id: -1 };
this.saveSavedMessage(copy);
}
if (!ids.length) {
this.closeForwardPicker();
return;
}

try {

const res = await fetch(this.forwardRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ message_id: this.forwardSourceMessage.id, recipient_ids: ids }),

});

const data = await res.json().catch(() => ({}));

if (!res.ok || !data.success) throw new Error('forward_failed');

const created = Array.isArray(data.data?.messages) ? data.data.messages : [];

for (const raw of created) {

const msg = this.normalizeMessage(raw);

if (this.selectedContact && Number(msg.recipientId) === Number(this.selectedContact.id) && !this.messages.some(m => Number(m.id) === Number(msg.id))) {

this.messages.push(msg);

this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(msg.id || 0));

}

}

this.normalizeMessageOrder();

this.updateContactPreview();

this.showToast('تمت إعادة التوجيه', 'success');

this.closeForwardPicker();

} catch (_) {

this.showToast('فشلت إعادة التوجيه', 'error');

}

},

async contextSave() {

const msg = this.messageContextMessage;

if (!msg?.id) return;

if (this.isMessageSaved(msg)) {

await this.unsaveMessageById(msg.id);

} else {

await this.saveMessageById(msg.id);

}

this.messageContextOpen = false;

},

isMessageSaved(message) {

const id = Number(message?.id || 0);

return !!id && this.savedMessageIds.includes(id);

},

toggleDark() {

if (typeof window.toggleThemeUniversal === 'function') window.toggleThemeUniversal();
this._isDark = document.documentElement.getAttribute('data-theme') !== 'light';

},

clearChatHistory() {

if (!this.selectedContact) return;

this.messages = [];

this.headerMenuOpen = false;

this.showToast('تم مسح الرسائل المعروضة', 'info');

},

openFoldersManager() {

this.foldersManagerOpen = true;

if (!this.foldersConfig.length) this.loadFoldersConfig();

},

closeFoldersManager() {

this.foldersManagerOpen = false;

this.includeExcludeMode = null;

this.includeExcludeSearch = '';

},

loadFoldersConfig() {

try {

const raw = localStorage.getItem('messaging_folders_config_v1');

const parsed = raw ? JSON.parse(raw) : null;

this.foldersConfig = Array.isArray(parsed) ? parsed : [];

} catch (_) {

this.foldersConfig = [];

}

},

persistFoldersConfig() {

localStorage.setItem('messaging_folders_config_v1', JSON.stringify(this.foldersConfig || []));

},

editFolder(index) {

const f = this.foldersConfig[index];

if (!f) return;

this.folderDraft = {

editIndex: index,

id: f.id,

name: f.name,

icon: f.icon || 'ri-folder-3-line',

color: f.color || '#C6A675',

includeIds: [...(f.includeIds || [])],

excludeIds: [...(f.excludeIds || [])],

};

},

removeFolder(index) {

const f = this.foldersConfig[index];

if (!f) return;

if (this.railFilter === f.id) this.railFilter = 'all';

this.foldersConfig.splice(index, 1);

this.persistFoldersConfig();

},

resetFolderDraft() {

this.folderDraft = { editIndex: -1, id: null, name: '', icon: 'ri-folder-3-line', color: '#C6A675', includeIds: [], excludeIds: [] };

this.includeExcludeMode = null;

},

saveFolderDraft() {

const name = String(this.folderDraft.name || '').trim();

if (!name) {

this.showToast('ادخل اسم المجلد', 'error');

return;

}

const payload = {

id: this.folderDraft.id || `fld_${Date.now()}`,

name,

icon: this.folderDraft.icon || 'ri-folder-3-line',

color: this.folderDraft.color || '#C6A675',

includeIds: [...(this.folderDraft.includeIds || [])],

excludeIds: [...(this.folderDraft.excludeIds || [])],

};

if (this.folderDraft.editIndex >= 0) this.foldersConfig.splice(this.folderDraft.editIndex, 1, payload);

else this.foldersConfig.push(payload);

this.persistFoldersConfig();

this.railFilter = payload.id;

this.resetFolderDraft();

this.showToast('تم حفظ المجلد', 'success');

},

openIncludeExclude(mode) {

this.includeExcludeMode = mode;

},

toggleFolderChat(contactId) {

const id = Number(contactId);

if (!id) return;

const key = this.includeExcludeMode === 'exclude' ? 'excludeIds' : 'includeIds';

const list = [...(this.folderDraft[key] || [])];

const idx = list.indexOf(id);

if (idx === -1) list.push(id); else list.splice(idx, 1);

this.folderDraft[key] = list;

},

isFolderChatSelected(contactId) {

const id = Number(contactId);

const key = this.includeExcludeMode === 'exclude' ? 'excludeIds' : 'includeIds';

return (this.folderDraft[key] || []).includes(id);

},

countFolderChats(folder) {

const all = this.contacts || [];

const inc = folder?.includeIds || [];

const exc = folder?.excludeIds || [];

return all.filter(c => (inc.length ? inc.includes(Number(c.id)) : true) && !exc.includes(Number(c.id))).length;

},

async toggleReaction(message, emoji) {

this.closeReactionPicker();

const msgId = message.id;

const map = this.reactionsMap[msgId] || [];

// Snapshot for rollback if the server rejects the change.

const prevSnapshot = map.map(r => ({ ...r }));

const existing = map.find(r => r.emoji === emoji);

const myPrev = map.find(r => r.myReaction);

// Optimistic update

let updated = map.filter(r => r.emoji !== emoji);

if (!existing || !existing.myReaction) {

if (myPrev) {

updated = updated.filter(r => r.emoji !== myPrev.emoji);

const prev = map.find(r => r.emoji === myPrev.emoji);

if (prev && prev.count > 1) updated.push({ emoji: myPrev.emoji, count: prev.count - 1, myReaction: false });

}

const cur = map.find(r => r.emoji === emoji);

updated.push({ emoji, count: (cur?.count || 0) + 1, myReaction: true });

} else {

if (existing.count > 1) updated.push({ emoji, count: existing.count - 1, myReaction: false });

}

this.reactionsMap = { ...this.reactionsMap, [msgId]: updated };

const reactionRoute = @json(

auth()->user()->role === 'teacher'

? (\Illuminate\Support\Facades\Route::has('teacher.messaging.reaction') ? route('teacher.messaging.reaction') : null)

: (\Illuminate\Support\Facades\Route::has('messaging.reaction')

? route('messaging.reaction')

: (\Illuminate\Support\Facades\Route::has('student.messaging.reaction') ? route('student.messaging.reaction') : null))

);

if (!reactionRoute) return;

try {

const res = await fetch(reactionRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ message_id: msgId, emoji }),

});

if (!res.ok) throw new Error('reaction_failed_' + res.status);

const payload = await res.json().catch(() => null);

const serverReactions = payload?.data?.reactions;

if (Array.isArray(serverReactions)) {

// Reconcile with the authoritative server state.

this.reactionsMap = { ...this.reactionsMap, [msgId]: serverReactions };

}

} catch (_) {

// Roll back the optimistic update so the UI never shows a reaction the server refused.

this.reactionsMap = { ...this.reactionsMap, [msgId]: prevSnapshot };

}

},

// Settings

openSettings() { this.headerMenuOpen = false; this.settingsPanelOpen = true; this.settingsSection = 'main_menu'; this.loadMessagingSettings(); },

openSettingsSection(id) {
    this.settingsSection = id;
    if (id === 'privacy' && !this.settingsBlockedList.length) this.loadBlockedUsers();
    if (id === 'privacy' && !this.settingsSessionsList.length) this.loadActiveSessions();
    if (id === 'privacy' && this.settingsPrivacy.frequentContactsEnabled && !this.settingsFrequentContacts.length) this.loadFrequentContacts();
    if (id === 'chats' && !this.settingsFoldersList.length) this.loadSettingsFolders();
    if (id === 'mic' && !this.settingsMicDevices.length) this.loadMicDevices();
    if (id === 'calls') this.loadCallSettingsDevices();
},

closeSettings() {
    this.saveMessagingSettings();
    this.settingsPanelOpen = false;
    this.headerMenuOpen = false;
    this.settingsSection = 'main_menu';
    if (this.micTestActive) this.stopMicTest();
},

backFromSettingsOrClose() {
    if (this.settingsSection && this.settingsSection !== 'main_menu') { this.settingsSection = 'main_menu'; return; }
    this.closeSettings();
},

normalizeProfileContact(contact = {}) {

const id = contact.id ?? contact.user_id ?? contact.senderId ?? contact.sender_id ?? this.currentUserId;

const name = this.sanitizeDisplayText(contact.name ?? contact.user_name ?? contact.senderName ?? contact.sender_name ?? this.userName ?? 'User', 'User');

const avatar = contact.avatar_url ?? contact.user_avatar ?? contact.avatarUrl ?? contact.senderAvatar ?? null;

const bannerGradients = [
    'background: linear-gradient(135deg, var(--theme-surface, #17130c), var(--theme-surface-2, #241b12), var(--theme-gold-dark, #5b4124))',
    'background: linear-gradient(135deg,#4a351f,#c6a475)',
    'background: linear-gradient(135deg,#17130c,#2a2114,#5b4124)',
    'background: linear-gradient(135deg,#17130c,#6f4f2a)',
];
const idx = (name || 'U').charCodeAt(0) % bannerGradients.length;
const bannerStyle = bannerGradients[Number.isNaN(idx) ? 0 : idx];

return {

...contact,

id,

name,

username: contact.username ?? contact.userName ?? null,

avatar_url: this.normalizeAvatarUrl(avatar),

isOnline: !!(contact.isOnline ?? contact.is_online ?? false),

lastSeenAt: contact.lastSeenAt ?? contact.last_seen_at ?? null,

role: contact.role ?? contact.user_role ?? null,

phone: contact.phone ?? null,

_bannerStyle: bannerStyle,

};

},

openMyProfile() {

const saved = safeLocalJson('messaging_my_profile', {});
this.myProfileEditName = saved.name || this.userName || 'User';
this.myProfileEditUsername = saved.username || '';
this.myProfileEditPhone = saved.phone || this.currentUserPhone || '';
this.myProfileEditBio = saved.bio || '';
this.myProfileBirthDate = saved.birthDate || '';
this.myProfileAvatar = saved.avatar || null;
this.myProfileBannerScale = 1;
this.myProfileOpen = true;
this.accountDrawerOpen = false;

},

openContacts() {
this.contactsQuery = '';
this.contactsOpen = true;
this.accountDrawerOpen = false;
},

openSavedChat() {
this.accountDrawerOpen = false;
const savedContact = {
id: -1,
name: 'الرسائل المحفوظة',
avatar_url: null,
isGroup: false,
hasConversation: true,
unreadCount: 0,
username: 'saved',
};
this.messages = this.loadSavedMessages();
this.selectedContact = savedContact;
this.activeLoadRecipientId = -1;
this.lastFeedScrollTop = 0;
this.lastKnownMessageId = 0;
this.lastDeltaTime = null;
this.replyingToMessage = null;
this.pendingAttachments = [];
this.messageInput = '';
this.showEmojiPicker = false;
this.cancelRecording(true);
if (window.innerWidth <= 1080) this.showSidebar = false;
this.$nextTick(() => this.scrollFeedToBottom());
},

loadSavedMessages() {
try {
const raw = localStorage.getItem('messaging_saved_messages');
return raw ? JSON.parse(raw) : [];
} catch (_) { return []; }
},

saveSavedMessage(msg) {
const msgs = this.loadSavedMessages();
msgs.push(msg);
try {
localStorage.setItem('messaging_saved_messages', JSON.stringify(msgs));
} catch (_) {
this.showToast('تعذر حفظ الرسالة — السعة التخزينية ممتلئة', 'error');
}
this.messages = msgs;
this.$nextTick(() => this.scrollToBottom(true));
},

openSavedMedia() {
if (Number(this.selectedContact?.id) !== -1) return;
const msgs = this.loadSavedMessages();
const images = msgs.filter(m => m.messageType === 'image');
const videos = msgs.filter(m => m.messageType === 'video');
const audio = msgs.filter(m => m.messageType === 'audio');
const files = msgs.filter(m => m.messageType === 'file' && m.attachmentUrl);
if (!images.length && !videos.length && !audio.length && !files.length) {
this.showToast('لا توجد وسائط محفوظة بعد', 'info');
return;
}
// Simple media gallery display inline
let html = '<div class="media-gallery-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;padding:12px;max-height:60vh;overflow:auto;">';
msgs.forEach(m => {
if (m.messageType === 'image' && m.attachmentUrl) {
html += '<div class="mg-item" style="aspect-ratio:1;overflow:hidden;border-radius:8px;background:var(--panel-2);cursor:pointer;"><img src="' + m.attachmentUrl + '" style="width:100%;height:100%;object-fit:cover;"></div>';
} else if (m.messageType === 'video' && m.attachmentUrl) {
html += '<div class="mg-item" style="aspect-ratio:1;overflow:hidden;border-radius:8px;background:#000;position:relative;cursor:pointer;"><video src="' + m.attachmentUrl + '" preload="metadata" muted style="width:100%;height:100%;object-fit:cover;"></video><i class="ri-play-fill" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:28px;text-shadow:0 0 8px rgba(0,0,0,0.6);"></i></div>';
} else if (m.messageType === 'audio' && m.attachmentUrl) {
html += '<div style="grid-column:1/-1;display:flex;align-items:center;gap:8px;padding:8px;border-radius:8px;background:var(--panel-2);"><i class="ri-mic-line"></i><audio controls src="' + m.attachmentUrl + '" style="flex:1;height:40px;"></audio></div>';
}
});
html += '</div>';
const d = document.createElement('div');
d.className = 'folders-modal';
d.style.cssText = 'z-index:300;';
d.innerHTML = '<div class="folders-card" style="width:min(420px,96%);max-height:80vh;"><div class="folders-head"><strong>وسائط الرسائل المحفوظة</strong><button class="h-icon-btn folders-close-btn"><i class="ri-close-line"></i></button></div>' + html + '</div></div>';
d.addEventListener('click', function(e) { if (e.target === d) d.remove(); });
d.querySelector('.folders-card').addEventListener('click', function(e) { e.stopPropagation(); });
d.querySelector('.folders-close-btn').addEventListener('click', function() { d.remove(); });
document.body.appendChild(d);
},

saveMyProfileEdit() {
if (this.myProfileEditName.trim()) {
const saved = safeLocalJson('messaging_my_profile', {});
const data = {
name: this.myProfileEditName.trim(),
username: this.myProfileEditUsername.trim(),
phone: this.myProfileEditPhone || this.currentUserPhone || '',
bio: this.myProfileEditBio.trim(),
birthDate: this.myProfileBirthDate,
avatar: saved.avatar || this.myProfileAvatar
};
try { localStorage.setItem('messaging_my_profile', JSON.stringify(data)); } catch (_) {}
this.userName = data.name;
this.showToast('تم حفظ الملف الشخصي', 'success');
}
this.myProfileEditOpen = false;
},

triggerMyAvatarUpload() {
let inp = document.getElementById('_myAvatarInput');
if (!inp) {
inp = document.createElement('input');
inp.id = '_myAvatarInput';
inp.type = 'file';
inp.accept = 'image/*';
inp.style.display = 'none';
inp.onchange = (e) => {
const file = e.target.files?.[0];
if (file) {
const reader = new FileReader();
reader.onload = (ev) => {
this.myProfileAvatar = ev.target.result;
try { localStorage.setItem('messaging_my_avatar', this.myProfileAvatar); } catch (_) {}
};
reader.readAsDataURL(file);
}
};
document.body.appendChild(inp);
}
inp.click();
},

loadMyProfileData() {
const saved = safeLocalJson('messaging_my_profile', {});
this.myProfileEditName = saved.name || this.userName || 'User';
this.myProfileEditUsername = saved.username || '';
this.myProfileEditBio = saved.bio || '';
this.myProfileBirthDate = saved.birthDate || '';
this.myProfileAvatar = saved.avatar || null;
},

onMyProfileScroll(e) {
const el = e.currentTarget || e.target;
const maxScroll = 140;
const scroll = Math.min(el.scrollTop, maxScroll);
const scale = Math.max(0, 1 - scroll / maxScroll);
this.myProfileBannerScale = scale;
},

onMyStatusScroll(e) {
const el = e.target;
const dateEl = this.$refs.myProfileStatusDate;
if (!dateEl) return;
const items = (this.myProfileStatuses || []);
if (!items.length) { dateEl.style.opacity = '0'; return; }
// Find the first visible item's date
const cardEls = el.children;
let shownDate = '';
for (let i = 0; i < cardEls.length; i++) {
const rect = cardEls[i].getBoundingClientRect();
if (rect.top < el.getBoundingClientRect().bottom && rect.bottom > el.getBoundingClientRect().top) {
const st = items[i];
if (st) {
const d = new Date(st.createdAt || st.created_at || 0);
if (!Number.isNaN(d.getTime())) {
const monthName = d.toLocaleDateString('ar-SA', { month: 'long' });
const day = d.getDate();
const year = d.getFullYear();
const now = new Date();
shownDate = (d.getFullYear() === now.getFullYear() ? monthName + ' ' + day : monthName + ' ' + day + '، ' + year);
}
}
break;
}
}
if (shownDate) {
dateEl.textContent = shownDate;
dateEl.style.opacity = '1';
} else {
dateEl.style.opacity = '0';
}
// Auto-hide after scroll stops
if (this._myStatusScrollTimer) clearTimeout(this._myStatusScrollTimer);
this._myStatusScrollTimer = setTimeout(() => { if (dateEl) dateEl.style.opacity = '0'; }, 1500);
},

openMyStatusViewer(idx) {
this.myProfileViewerIdx = idx;
this.myProfileStatusViewerOpen = true;
// Build viewers list from status data
const s = this.myProfileStatuses[idx];
if (s) {
this.myProfileViewersList = (s.viewers || []).map(v => ({
name: v.name || v.user_name || 'مستخدم',
avatar: v.avatar || v.user_avatar || null,
time: v.viewedAt || v.viewed_at || ''
}));
} else {
this.myProfileViewersList = [];
}
},

closeMyStatusViewer() {
this.myProfileStatusViewerOpen = false;
},

prevMyProfileStatus() {
if (this.myProfileViewerIdx > 0) {
this.myProfileViewerIdx--;
this.openMyStatusViewer(this.myProfileViewerIdx);
}
},

nextMyProfileStatus() {
if (this.myProfileViewerIdx < (this.myProfileStatuses.length - 1)) {
this.myProfileViewerIdx++;
this.openMyStatusViewer(this.myProfileViewerIdx);
}
},

openMyStatusViewers() {
this.myProfileViewersOpen = true;
},

openCalls() {
if (!this.callLogsLoaded) {
this.loadCallLogs();
this.callLogsLoaded = true;
}
this.callsOpen = true;
this.accountDrawerOpen = false;
},

closeCalls() {
this.callsOpen = false;
this.callsMenuOpen = false;
this.callsSelectMode = false;
this.callsSelectedDelete = [];
this.callsDisplayCount = 20;
},

loadCallLogs() {
let raw;
try { raw = localStorage.getItem('messaging_calls'); } catch (_) {}
if (raw) {
try { this.callLogs = JSON.parse(raw); } catch (_) { this.callLogs = []; }
}
if (!this.callLogs || !this.callLogs.length) {
const now = Date.now();
const sampleContacts = (this.contacts || []).filter(c => Number(c.id) !== Number(this.currentUserId));
const sampleCalls = [];
sampleContacts.slice(0, 12).forEach((c, i) => {
const hrs = i * 2;
sampleCalls.push({
id: 'call_' + i,
contactId: c.id,
contactName: c.name,
contactAvatar: c.avatar_url,
type: i % 3 === 0 ? 'video' : 'audio',
direction: i % 2 === 0 ? 'outgoing' : 'incoming',
status: i === 2 || i === 5 || i === 9 ? 'missed' : 'completed',
duration: 30 + Math.floor(Math.random() * 300),
timestamp: new Date(now - hrs * 3600000 - Math.floor(Math.random() * 1800000)).toISOString(),
attempts: i === 5 ? 3 : i === 2 ? 2 : 1,
});
});
this.callLogs = sampleCalls;
this.saveCallLogs();
}
},

saveCallLogs() {
try { localStorage.setItem('messaging_calls', JSON.stringify(this.callLogs)); } catch (_) {}
},

formatCallTime(ts) {
if (!ts) return '';
const d = new Date(ts);
if (Number.isNaN(d.getTime())) return '';
const now = new Date();
const diff = now - d;
const days = Math.floor(diff / 86400000);
if (days === 0) return 'اليوم ' + d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });
if (days === 1) return 'أمس ' + d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });
return d.toLocaleDateString('ar-SA', { month: 'short', day: 'numeric' }) + ' ' + d.toLocaleTimeString('ar-SA', { hour: 'numeric', minute: '2-digit', hour12: true });
},

onCallsScroll(e) {
const el = e.target;
if (!el) return;
if (el.scrollTop + el.clientHeight >= el.scrollHeight - 100) {
if (this.callsDisplayCount < (this.callLogs || []).length) {
this.callsDisplayCount += 20;
}
}
},

openNewCallModal() {
this.newCallModalOpen = true;
this.newCallQuery = '';
this.newCallSelected = [];
},

closeNewCallModal() {
this.newCallModalOpen = false;
this.newCallQuery = '';
this.newCallSelected = [];
},

toggleNewCallSelect(id) {
const idx = this.newCallSelected.indexOf(id);
if (idx === -1) this.newCallSelected.push(id);
else this.newCallSelected.splice(idx, 1);
},

startNewCallFromLog(type) {
if (!this.newCallSelected.length) return;
const selectedContacts = this.newCallSelected.map(id => this.contacts.find(c => c.id === id)).filter(Boolean);
if (!selectedContacts.length) return;
this.closeNewCallModal();
this.closeCalls();
this.startCall(type || 'voice', selectedContacts);
},

formatDuration(seconds) {
if (!seconds || seconds < 1) return 'لحظة';
const m = Math.floor(seconds / 60);
const s = Math.floor(seconds % 60);
return (m ? m + ' د ' : '') + s + ' ث';
},

callFromMessage(message) {
if (!message) return;
const contact = this.contacts.find(c => Number(c.id) === Number(message.senderId));
if (contact) {
this.selectContact(contact);
this.$nextTick(() => this.scrollToMessage(message.id));
} else {
this.showToast('لم يتم العثور على المستخدم', 'error');
}
},

injectCallMessages() {
if (!this.selectedContact || Number(this.selectedContact.id) === -1) return;
const cid = Number(this.selectedContact.id);
const callLogs = this.callLogs || [];
const callMsgs = callLogs
.filter(cl => Number(cl.contactId) === cid)
.map(cl => ({
id: Number('9' + String(cl.id).replace(/\D/g, '').slice(0, 14) || Date.now()),
messageType: 'call',
callType: cl.type || 'audio',
callDirection: cl.direction || 'outgoing',
callStatus: cl.status || 'missed',
callDuration: cl.duration || 0,
senderId: cl.direction === 'outgoing' ? this.currentUserId : cid,
createdAt: cl.timestamp || new Date().toISOString(),
content: '',
}));
if (!callMsgs.length) return;
const existingIds = new Set(this.messages.map(m => Number(m.id)));
const newMsgs = callMsgs.filter(m => !existingIds.has(Number(m.id)));
if (newMsgs.length) {
this.messages = [...this.messages, ...newMsgs].sort((a, b) =>
new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime()
);
}
},

toggleCallsSelectMode() {
this.callsSelectMode = !this.callsSelectMode;
if (!this.callsSelectMode) this.callsSelectedDelete = [];
this.callsMenuOpen = false;
},

toggleCallSelect(id) {
const idx = this.callsSelectedDelete.indexOf(id);
if (idx === -1) this.callsSelectedDelete.push(id);
else this.callsSelectedDelete.splice(idx, 1);
},

toggleSelectAllCalls() {
if (this.callsSelectedDelete.length === this.displayedCalls.length) {
this.callsSelectedDelete = [];
} else {
this.callsSelectedDelete = this.displayedCalls.map(c => c.id);
}
},

deleteSelectedCalls() {
if (!this.callsSelectedDelete.length) return;
this.callLogs = (this.callLogs || []).filter(c => !this.callsSelectedDelete.includes(c.id));
this.saveCallLogs();
this.callsSelectedDelete = [];
if (!this.callLogs.length) this.callsSelectMode = false;
},

deleteAllCalls() {
this.callLogs = [];
this.saveCallLogs();
this.callsDeleteConfirm = false;
this.callsSelectMode = false;
this.callsSelectedDelete = [];
},

startCallFromLog(call) {
if (!call) return;
const contact = this.contacts.find(c => Number(c.id) === Number(call.contactId));
if (contact) {
this.closeCalls();
this.startCall(call.type === 'video' ? 'video' : 'audio');
}
},

navigateToCallMessage(call) {
const msg = (this.messages || []).find(m => {
if (m.messageType !== 'call') return false;
const contactId = Number(call.contactId);
const msgContactId = Number(m.senderId) === Number(this.currentUserId) ? Number(m.contactId) : Number(m.senderId);
return msgContactId === contactId;
});
if (msg) {
this.closeCalls();
this.$nextTick(() => {
const id = msg.id || msg._id;
if (id) {
const row = document.getElementById('message-' + id);
if (row) row.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
});
} else if (call.contactId) {
this.closeCalls();
this.selectContactById(call.contactId);
}
},

selectContactById(id) {
const contact = this.contacts.find(c => Number(c.id) === Number(id));
if (contact) this.selectContact(contact);
},

openNewChannelModal() {

this.accountDrawerOpen = false;

this.newConversationModal = true;

this.newConversationStep = 'group-setup';

this.groupDraftName = 'New Channel';

this.groupDraftSelection = [];

},

// Profile modal

openProfile(contact) {

if (!contact) return;

this.profileModalContact = this.normalizeProfileContact(contact);

},

copyToClipboard(text) {

if (!text) return;
try {
navigator.clipboard.writeText(text.replace(/^@/, ''));
this.showToast('تم نسخ اسم المستخدم', 'success');
} catch (_) {
const ta = document.createElement('textarea');
ta.value = text.replace(/^@/, '');
document.body.appendChild(ta);
ta.select();
document.execCommand('copy');
document.body.removeChild(ta);
this.showToast('تم نسخ اسم المستخدم', 'success');
}

},

saveAutoDelete() {

const id = this.profileModalContact?.id;
if (!id) return;
const key = 'conv_auto_delete_' + id;
if (this.profileAutoDeleteDuration > 0) {
localStorage.setItem(key, String(this.profileAutoDeleteDuration));
} else {
localStorage.removeItem(key);
}
this.profileAutoDeleteOpen = false;
this.showToast(this.profileAutoDeleteDuration > 0 ? 'تم تفعيل الحذف التلقائي' : 'تم إيقاف الحذف التلقائي', 'success');

},

doExportChat() {

this.profileExportOpen = false;
const types = [];
if (this.profileExportPhotos) types.push('image');
if (this.profileExportVideos) types.push('video');
if (this.profileExportVoice) types.push('audio');
if (this.profileExportVideoMsg) types.push('video_message');
if (this.profileExportStickers) types.push('sticker');
if (this.profileExportGifs) types.push('gif');
if (this.profileExportFiles) types.push('file');
const maxSize = this.profileExportMaxSize;
// Trigger server-side export
fetch('/messaging/export', {
method: 'POST',
headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''},
body: JSON.stringify({
contact_id: this.profileModalContact?.id,
types,
max_size_mb: maxSize,
})
}).then(r => r.json()).then(d => {
this.showToast(d.success ? 'تم بدء التصدير' : 'فشل التصدير', d.success ? 'success' : 'error');
}).catch(() => {
this.showToast('حدث خطأ أثناء التصدير', 'error');
});

},

toggleFolderInclude(folder, index) {

const cid = String(this.profileModalContact?.id);
if (!cid) return;
const idx = folder.includeIds.indexOf(cid);
if (idx > -1) {
folder.includeIds.splice(idx, 1);
} else {
folder.includeIds.push(cid);
}
// Remove from exclude if present
const exIdx = folder.excludeIds.indexOf(cid);
if (exIdx > -1) folder.excludeIds.splice(exIdx, 1);
this.foldersConfig[index] = {...folder};
localStorage.setItem('messaging_folders_config_v1', JSON.stringify(this.foldersConfig));

},

doBlockUser() {

const id = this.profileModalContact?.id;
if (!id) return;
this.blockedByContact[String(id)] = Date.now();
localStorage.setItem('messaging_blocked', JSON.stringify(this.blockedByContact));
this.profileBlockConfirm = false;
this.closeProfile();
this.showToast('تم حظر المستخدم', 'success');

},

unblockContact() {

const id = this.selectedContact?.id;
if (!id) return;
delete this.blockedByContact[String(id)];
localStorage.setItem('messaging_blocked', JSON.stringify(this.blockedByContact));
this.showToast('تم رفع الحظر', 'success');

},

unblockContactInProfile() {

const id = this.profileModalContact?.id;
if (!id) return;
delete this.blockedByContact[String(id)];
localStorage.setItem('messaging_blocked', JSON.stringify(this.blockedByContact));
this.profileMoreOpen = false;
this.showToast('تم رفع الحظر', 'success');

},

deleteChatWithContact() {

if (!confirm('هل أنت متأكد من حذف هذه الدردشة؟ سيتم حذف جميع الرسائل.')) return;
const id = this.selectedContact?.id;
if (!id) return;
if (this.nicknameByContact[String(id)]) {
delete this.nicknameByContact[String(id)];
localStorage.setItem('conv_nicknames', JSON.stringify(this.nicknameByContact));
}
if (this.blockedByContact[String(id)]) {
delete this.blockedByContact[String(id)];
localStorage.setItem('messaging_blocked', JSON.stringify(this.blockedByContact));
}
this.selectedContact = null;
this.messages = [];
this.showToast('تم حذف الدردشة', 'success');

},

saveNickname() {

const id = this.profileModalContact?.id;
if (!id) return;
const name = (this.profileNicknameDraft || '').trim();
if (name) {
this.nicknameByContact[String(id)] = name;
} else {
delete this.nicknameByContact[String(id)];
}
localStorage.setItem('conv_nicknames', JSON.stringify(this.nicknameByContact));
this.profileNicknameEdit = false;
this.showToast(name ? 'تم حفظ الاسم المحلي' : 'تم إزالة الاسم المحلي', 'success');

},

toggleShareSelection(id) {

const idx = this.profileShareSelected.indexOf(id);
if (idx > -1) {
this.profileShareSelected.splice(idx, 1);
} else {
this.profileShareSelected.push(id);
}

},

doForwardToSelected() {

if (!this.profileShareSelected.length) return;
const ids = [...this.profileShareSelected];
this.profileShareChatOpen = false;
this.profileShareSelected = [];
// Handle saved messages locally
const savedIdx = ids.indexOf(-1);
if (savedIdx !== -1) {
ids.splice(savedIdx, 1);
this.showToast('تم حفظ الرسالة', 'success');
}
if (!ids.length) return;
// Emit forward event to the parent chat view
fetch('/messaging/forward', {
method: 'POST',
headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''},
body: JSON.stringify({
from_contact_id: this.profileModalContact?.id,
to_contact_ids: ids
})
}).then(r => r.json()).then(d => {
this.showToast(d.success ? 'تم تحويل الدردشة' : 'فشل التحويل', d.success ? 'success' : 'error');
if (d.success && ids.length === 1) {
this.selectContact(this.contacts.find(c => Number(c.id) === Number(ids[0])));
}
}).catch(() => {
this.showToast('حدث خطأ', 'error');
});

},

onProfileBodyScroll(e) {

const el = e.currentTarget || e.target;
const maxScroll = 160;
const scroll = Math.min(el.scrollTop, maxScroll);
this.profileBannerScale = 1 - (scroll / maxScroll) * 0.5;

},

openProfileMediaModal(type) {

this.profileMoreOpen = false;
this.profileMediaModalType = type;
this.profileMediaModalOpen = true;
this.profileMediaCalendarOpen = false;
this.profileMediaCalendarFullOpen = false;
this.profileMediaMenuOpen = false;
this.profileBannerScale = 1;
this.voicePlayerExternalSource = type === 'audio' ? this.getProfileMediaByType('audio') : null;

},

openProfileMediaCalendar() {

this.profileMediaMenuOpen = false;
this.profileMediaCalendarOpen = true;

},

forwardToContact(contact) {

if (!contact?.id) return;
this.profileShareChatOpen = false;
this.closeProfile();
this.selectContact(contact);

},

deleteContact() {

this.profileDeleteConfirm = true;

},

executeDeleteContact() {

this.profileDeleteConfirm = false;
this.closeProfile();
const id = this.profileModalContact?.id;
if (id && this.nicknameByContact[String(id)]) {
delete this.nicknameByContact[String(id)];
localStorage.setItem('conv_nicknames', JSON.stringify(this.nicknameByContact));
}
this.showToast('تم حذف الجهة', 'success');

},

closeProfile() {

this.profileModalContact = null;
this.profileBannerScale = 1;

if (this.profileOpenedFromViewers) {

this.profileOpenedFromViewers = false;

this.statusViewersOpen = true;

}

},

doDownloadQrCode() {
if (!this.qrImageUrl) return;
const link = document.createElement('a');
link.download = `qr-${(this.qrModalContact?.name || 'profile').replace(/\s+/g,'_')}.png`;
link.href = this.qrImageUrl;
link.click();
},
doGenerateQrCode() {
if (!this.qrModalContact || !this.qrRouteTemplate) return;
this.qrImageUrl = this.qrRouteTemplate.replace('__USER_ID__', this.qrModalContact.id);
this.qrRawImageCache = null;
this.$nextTick(() => this.renderComposedQr());
},

parseGradientStops(cssGradient) {
const hexes = (cssGradient.match(/#[0-9a-fA-F]{3,6}/g) || ['#C4963A', '#A07A28']);
return hexes.length > 1 ? hexes : [hexes[0], hexes[0]];
},

hexToRgb(hex) {
let h = hex.replace('#', '');
if (h.length === 3) h = h.split('').map(c => c + c).join('');
const num = parseInt(h, 16);
return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
},

lerpColor(c1, c2, t) {
return {
r: Math.round(c1.r + (c2.r - c1.r) * t),
g: Math.round(c1.g + (c2.g - c1.g) * t),
b: Math.round(c1.b + (c2.b - c1.b) * t),
};
},

async loadImageEl(src) {
return new Promise((resolve, reject) => {
const img = new Image();
img.crossOrigin = 'anonymous';
img.onload = () => resolve(img);
img.onerror = reject;
img.src = src;
});
},

async renderComposedQr() {
if (!this.qrImageUrl || !this.$refs.qrComposedCanvas) return;

if (!this.qrRawImageCache || this.qrRawImageCache.src !== this.qrImageUrl) {
try {
this.qrRawImageCache = await this.loadImageEl(this.qrImageUrl);
} catch (_) {
return;
}
}

const qualitySizes = [320, 460, 600];
const size = qualitySizes[this.qrQualityIndex] || 460;
const qrHolderPadding = Math.round(size * 0.07);
const qrHolderSize = size + qrHolderPadding * 2;
const cardPadding = Math.round(size * 0.12);
const cardSize = qrHolderSize + cardPadding * 2;
// نطبّق معامل تصحيح يساوي نسبة دقة الكانفاس الفعلية إلى عرض العرض الثابت على الشاشة (340px)
// لكي يبقى حجم الخط المرئي ثابتاً عند تغيير الجودة فقط، ويتأثر فقط بمؤشر حجم الخط نفسه
const displayScaleFactor = cardSize / 340;
const renderedFontSize = Math.round(this.qrFontSize * displayScaleFactor);
const labelHeight = renderedFontSize + Math.round(24 * displayScaleFactor);

const canvas = this.$refs.qrComposedCanvas;
canvas.width = cardSize;
canvas.height = cardSize + labelHeight;
const ctx = canvas.getContext('2d');
ctx.clearRect(0, 0, canvas.width, canvas.height);

const stops = this.parseGradientStops(this.qrBgOptions[this.qrBgIndex] || '#C4963A');
const c1 = this.hexToRgb(stops[0]);
const c2 = this.hexToRgb(stops[stops.length - 1]);

// خلفية البطاقة: التدرج المختار، أو شفافة بحسب الخيار
if (!this.qrTransparentBg) {
const grad = ctx.createLinearGradient(0, 0, cardSize, cardSize);
grad.addColorStop(0, stops[0]);
grad.addColorStop(1, stops[stops.length - 1]);
ctx.fillStyle = grad;
this.roundRectPath(ctx, 0, 0, cardSize, cardSize, 24);
ctx.fill();
}

// حامل أبيض ثابت حول الكود نفسه — يضمن بقاء تباين الكود الأسود/الأبيض الأصلي سليماً وقابلاً للمسح دون أي تعديل على بكسلاته
const holderX = cardPadding, holderY = cardPadding;
ctx.fillStyle = '#ffffff';
this.roundRectPath(ctx, holderX, holderY, qrHolderSize, qrHolderSize, 16);
ctx.fill();

ctx.imageSmoothingEnabled = false;
ctx.drawImage(this.qrRawImageCache, holderX + qrHolderPadding, holderY + qrHolderPadding, size, size);
ctx.imageSmoothingEnabled = true;

// الشعار/الصورة الشخصية في المنتصف
const avatarSrc = this.qrShowProfilePhoto && this.qrModalContact?.avatar_url ? this.qrModalContact.avatar_url : '{{ asset('images/logo/logo.png') }}';
try {
const logoImg = await this.loadImageEl(avatarSrc);
const logoSize = Math.round(size * 0.2);
const cx = cardSize / 2, cy = holderY + qrHolderSize / 2;
ctx.save();
ctx.beginPath();
ctx.arc(cx, cy, logoSize / 2 + 6, 0, Math.PI * 2);
ctx.fillStyle = '#fff';
ctx.fill();
ctx.beginPath();
ctx.arc(cx, cy, logoSize / 2, 0, Math.PI * 2);
ctx.closePath();
ctx.clip();
ctx.drawImage(logoImg, cx - logoSize / 2, cy - logoSize / 2, logoSize, logoSize);
ctx.restore();
} catch (_) {}

// اسم المستخدم أسفل الكود
const label = this.qrModalContact ? (this.qrModalContact.username ? '@' + this.qrModalContact.username : this.qrModalContact.name) : '';
if (label) {
ctx.font = `700 ${renderedFontSize}px Tajawal, sans-serif`;
ctx.textAlign = 'center';
ctx.textBaseline = 'middle';
const midColor = this.lerpColor(c1, c2, 0.5);
ctx.fillStyle = this.qrTransparentBg ? `rgb(${midColor.r},${midColor.g},${midColor.b})` : `rgb(${midColor.r},${midColor.g},${midColor.b})`;
ctx.fillText(label, cardSize / 2, cardSize + labelHeight / 2);
}
},

roundRectPath(ctx, x, y, w, h, r) {
ctx.beginPath();
ctx.moveTo(x + r, y);
ctx.arcTo(x + w, y, x + w, y + h, r);
ctx.arcTo(x + w, y + h, x, y + h, r);
ctx.arcTo(x, y + h, x, y, r);
ctx.arcTo(x, y, x + w, y, r);
ctx.closePath();
},

async copyComposedQr() {
const canvas = this.$refs.qrComposedCanvas;
if (!canvas) return;
canvas.toBlob(async (blob) => {
try {
if (navigator.clipboard && window.ClipboardItem) {
await navigator.clipboard.write([new ClipboardItem({ 'image/png': blob })]);
this.showToast('تم نسخ الصورة', 'success');
} else {
const link = document.createElement('a');
link.download = 'qr-code.png';
link.href = canvas.toDataURL('image/png');
link.click();
this.showToast('تم التحميل (النسخ غير متاح في هذا المتصفح)', 'info');
}
} catch (_) {
this.showToast('فشل النسخ', 'error');
}
}, 'image/png');
},

downloadComposedQr() {
const canvas = this.$refs.qrComposedCanvas;
if (!canvas) return;
const link = document.createElement('a');
link.download = `qr-${(this.qrModalContact?.name || 'profile').replace(/\s+/g, '_')}.png`;
link.href = canvas.toDataURL('image/png');
link.click();
this.showToast('تم تحميل صورة QR', 'success');
},

doCopyQrCode() {
if (!this.qrModalContact) return;
const text = `${window.location.origin}/u/${this.qrModalContact.id}`;
if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(() => {
        this.showToast('تم نسخ رابط البطاقة', 'success');
    }).catch(() => {
        this.showToast('فشل النسخ', 'error');
    });
} else {
    const ta = document.createElement('textarea');
    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); this.showToast('تم النسخ', 'success'); } catch(_) { this.showToast('فشل النسخ', 'error'); }
    document.body.removeChild(ta);
}
},

getCallStatusText() {

const icon = this.callType === 'video' ? '📹' : '📞';
return icon + ' ' + (this.callIsRinging ? 'يرن...' : 'جاري الاتصال...');

},

// Calls

rtcIceServers() {
return { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };
},

callRouteFor(template, callId) {
return template ? template.replace('__CALL_ID__', callId) : null;
},

async postCallAction(url, body) {
if (!url) return null;
const resp = await fetch(url, {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
},
body: JSON.stringify(body || {}),
});
return resp.json().catch(() => null);
},

getPeerEntry(userId) {
const key = String(userId);
if (!this.peerConnections[key]) {
this.peerConnections[key] = { pc: null, pendingCandidates: [] };
}
return this.peerConnections[key];
},

upsertParticipantTile(userId, patch) {
const idx = this.callParticipants.findIndex(p => Number(p.id) === Number(userId));
if (idx === -1) {
this.callParticipants.push({ id: Number(userId), name: '', avatar_url: null, stream: null, ...patch });
} else {
this.callParticipants[idx] = { ...this.callParticipants[idx], ...patch };
}
},

removeParticipantTile(userId) {
this.callParticipants = this.callParticipants.filter(p => Number(p.id) !== Number(userId));
},

createPeerConnection(remoteUserId) {
const pc = new RTCPeerConnection(this.rtcIceServers());
pc.onicecandidate = (event) => {
if (event.candidate && this.currentCallId) {
this.postCallAction(this.callRouteFor(this.callsIceRouteTemplate, this.currentCallId), {
to_user_id: remoteUserId,
candidate: event.candidate.toJSON(),
});
}
};
pc.ontrack = (event) => {
this.upsertParticipantTile(remoteUserId, { stream: event.streams[0] });
this.remoteStreamsVersion++;
this.$nextTick(() => this.attachParticipantStreams());
};
const entry = this.getPeerEntry(remoteUserId);
entry.pc = pc;
return pc;
},

attachParticipantStreams() {
this.callParticipants.forEach(p => {
const el = this.$refs['remoteMedia_' + p.id];
const target = Array.isArray(el) ? el[0] : el;
if (target && p.stream && target.srcObject !== p.stream) {
target.srcObject = p.stream;
if (this.callSettings.speakerDeviceId && typeof target.setSinkId === 'function') {
target.setSinkId(this.callSettings.speakerDeviceId).catch(() => {});
}
}
});
},

describeMediaError(err) {
switch (err && err.name) {
case 'NotAllowedError':
case 'PermissionDeniedError':
return 'تم رفض إذن الوصول للميكروفون/الكاميرا — يرجى منح الإذن من إعدادات المتصفح لهذا الموقع';
case 'NotFoundError':
case 'DevicesNotFoundError':
return 'لم يتم العثور على ميكروفون أو كاميرا متاحة على هذا الجهاز';
case 'NotReadableError':
case 'TrackStartError':
return 'الميكروفون أو الكاميرا مستخدمة الآن من برنامج آخر';
case 'OverconstrainedError':
case 'ConstraintNotSatisfiedError':
return 'الجهاز المحدد في الإعدادات غير متاح حالياً';
default:
return 'تعذر الوصول إلى الميكروفون أو الكاميرا';
}
},

async startLocalMedia(type) {
const buildConstraints = (useSavedDevices) => {
const audioConstraint = (useSavedDevices && this.callSettings.micDeviceId)
? { deviceId: { ideal: this.callSettings.micDeviceId } }
: true;
let videoConstraint = false;
if (type === 'video') {
videoConstraint = (useSavedDevices && this.callSettings.cameraDeviceId) ? { deviceId: { ideal: this.callSettings.cameraDeviceId } } : {};
if (this.callSettings.lowDataMode) {
videoConstraint = { ...videoConstraint, width: { ideal: 320 }, height: { ideal: 240 }, frameRate: { ideal: 15 } };
}
}
return { audio: audioConstraint, video: videoConstraint };
};
try {
this.localStream = await navigator.mediaDevices.getUserMedia(buildConstraints(true));
return true;
} catch (e) {
if (e && (e.name === 'OverconstrainedError' || e.name === 'ConstraintNotSatisfiedError')) {
try {
this.localStream = await navigator.mediaDevices.getUserMedia(buildConstraints(false));
return true;
} catch (e2) {
this.showToast(this.describeMediaError(e2), 'error');
return false;
}
}
this.showToast(this.describeMediaError(e), 'error');
return false;
}
},

loadCallSettings() {
try {
const saved = JSON.parse(localStorage.getItem('messaging_call_settings') || '{}');
this.callSettings = { ...this.callSettings, ...saved };
} catch (_) {}
},

saveCallSettings() {
localStorage.setItem('messaging_call_settings', JSON.stringify(this.callSettings));
},

async checkMediaPermission(name) {
try {
if (!navigator.permissions || !navigator.permissions.query) return 'unknown';
const status = await navigator.permissions.query({ name });
return status.state; // 'granted' | 'denied' | 'prompt'
} catch (_) {
return 'unknown';
}
},

async loadCallSettingsDevices() {
this.micPermissionState = await this.checkMediaPermission('microphone');
this.cameraPermissionState = await this.checkMediaPermission('camera');

// إن كان الإذن ممنوحاً فعلاً، نحصل على أسماء حقيقية للأجهزة (المسمّيات تظهر فقط بعد منح الإذن)
if (this.micPermissionState === 'granted') {
try {
const probe = await navigator.mediaDevices.getUserMedia({ audio: true });
probe.getTracks().forEach(t => t.stop());
} catch (_) {}
}

try {
const devices = await navigator.mediaDevices.enumerateDevices();
this.callAudioInputs = devices.filter(d => d.kind === 'audioinput');
this.callVideoInputs = devices.filter(d => d.kind === 'videoinput');
this.callAudioOutputs = devices.filter(d => d.kind === 'audiooutput');
if (!this.callSettings.micDeviceId && this.callAudioInputs.length) {
this.callSettings.micDeviceId = this.callAudioInputs[0].deviceId;
}
} catch (_) {}
},

async requestCallMediaPermission(kind) {
try {
const stream = await navigator.mediaDevices.getUserMedia(kind === 'camera' ? { video: true } : { audio: true });
stream.getTracks().forEach(t => t.stop());
} catch (e) {
this.showToast(this.describeMediaError(e), 'error');
}
await this.loadCallSettingsDevices();
},

async startCall(type, contactsOverride) {
const contacts = contactsOverride || (this.selectedContact ? [this.selectedContact] : []);
if (!contacts.length) return;

const invalid = contacts.find(c => Number(c.id) === -1 || c.canCall === false);
if (invalid) { this.showToast('لا يمكنك الاتصال بهذا المستخدم بسبب إعدادات الخصوصية', 'error'); return; }

if (!(await this.startLocalMedia(type))) return;

const isGroup = contacts.length > 1;
this.callType = type;
this.callContact = contacts[0];
this.isGroupCall = isGroup;
this.callParticipants = contacts.map(c => ({ id: Number(c.id), name: c.name, avatar_url: c.avatar_url || null, stream: null }));
this.callState = 'calling';
this.callMuted = false;
this.cameraOff = false;

const result = await this.postCallAction(this.callsInitiateRoute, {
participant_ids: contacts.map(c => Number(c.id)),
type,
});

if (!result || !result.success) {
this.showToast('فشل بدء المكالمة', 'error');
this.cleanupCall();
return;
}

this.currentCallId = result.call_id;

if (!this.isGroupCall) {
if (this.callTimeoutTimer) clearTimeout(this.callTimeoutTimer);
this.callTimeoutTimer = setTimeout(() => {
if (this.callState === 'calling' || this.callState === 'incoming') {
this.showToast('لم يرد المستخدم على المكالمة', 'info');
this.endCall();
}
}, 45000);
}

for (const contact of contacts) {
const pc = this.createPeerConnection(contact.id);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
const offer = await pc.createOffer();
await pc.setLocalDescription(offer);
await this.postCallAction(this.callRouteFor(this.callsOfferRouteTemplate, this.currentCallId), {
to_user_id: contact.id,
offer: { sdp: offer.sdp, type: offer.type },
});
}

if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
},

async answerIncomingCall() {
if (!this.incomingCallOffer || !this.currentCallId) return;

const callerId = this.callContact.id;
if (!(await this.startLocalMedia(this.callType))) { this.rejectIncomingCall(); return; }

const pc = this.createPeerConnection(callerId);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;

await pc.setRemoteDescription(new RTCSessionDescription(this.incomingCallOffer));
const entry = this.getPeerEntry(callerId);
entry.pendingCandidates.forEach(c => pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {}));
entry.pendingCandidates = [];

const answer = await pc.createAnswer();
await pc.setLocalDescription(answer);

await this.postCallAction(this.callRouteFor(this.callsAnswerRouteTemplate, this.currentCallId), {
answer: { sdp: answer.sdp, type: answer.type },
});

this.callState = 'in-call';
this.callStartedAt = Date.now();
this.incomingCallOffer = null;

if (this.isGroupCall) {
const joinResult = await this.postCallAction(this.callRouteFor(this.callsJoinRouteTemplate, this.currentCallId), {});
await this.connectToExistingParticipants(joinResult?.existing_participant_ids || []);
}
},

/**
 * بعد الانضمام لمكالمة جماعية جارية: ننشئ اتصالاً مستقلاً (mesh) موجّهاً
 * لكل مشارك موجود فعلاً غير المتصل الأصلي (الذي تم الاتصال به أعلاه).
 */
async connectToExistingParticipants(existingIds) {
for (const id of existingIds) {
if (this.peerConnections[String(id)]?.pc) continue;
const pc = this.createPeerConnection(id);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
const offer = await pc.createOffer();
await pc.setLocalDescription(offer);
await this.postCallAction(this.callRouteFor(this.callsPeerOfferRouteTemplate, this.currentCallId), {
to_user_id: id,
offer: { sdp: offer.sdp, type: offer.type },
});
}
},

async rejectIncomingCall() {
if (this.currentCallId) {
await this.postCallAction(this.callRouteFor(this.callsRejectRouteTemplate, this.currentCallId), {});
}
this.cleanupCall();
},

async endCall() {
if (this.currentCallId) {
await this.postCallAction(this.callRouteFor(this.callsEndRouteTemplate, this.currentCallId), {});
}
this.cleanupCall();
},

async openAddParticipant() {
if (!this.isGroupCall && this.callParticipants.length <= 1) {
this.isGroupCall = true;
}
this.showAddParticipant = true;
this.addParticipantQuery = '';
},

async addParticipantToCall(contact) {
if (!this.currentCallId) return;
const result = await this.postCallAction(this.callRouteFor(this.callsInviteRouteTemplate, this.currentCallId), {
user_id: contact.id,
});
if (!result || !result.success) {
this.showToast(result?.message || 'تعذر إضافة المشارك', 'error');
return;
}
this.isGroupCall = true;
this.upsertParticipantTile(contact.id, { name: contact.name, avatar_url: contact.avatar_url || null, stream: null });
await this.postCallAction(this.callRouteFor(this.callsOfferRouteTemplate, this.currentCallId), {
to_user_id: contact.id,
offer: await this.createOfferFor(contact.id),
});
this.showAddParticipant = false;
this.showToast('تمت دعوة ' + contact.name, 'success');
},

async createOfferFor(userId) {
const pc = this.createPeerConnection(userId);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
const offer = await pc.createOffer();
await pc.setLocalDescription(offer);
return { sdp: offer.sdp, type: offer.type };
},

cleanupCall() {
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
this.callIsRinging = false;
Object.values(this.peerConnections).forEach(entry => {
try { entry.pc?.close(); } catch (_) {}
});
this.peerConnections = {};

if (this.localStream) {
this.localStream.getTracks().forEach(t => t.stop());
this.localStream = null;
}
if (this.$refs.localVideo) this.$refs.localVideo.srcObject = null;

this.callState = null;
this.callType = null;
this.callContact = null;
this.currentCallId = null;
this.incomingCallOffer = null;
this.callMuted = false;
this.cameraOff = false;
this.callStartedAt = null;
this.callParticipants = [];
this.isGroupCall = false;
this.showAddParticipant = false;
},

toggleMute() {
if (!this.localStream) return;
this.callMuted = !this.callMuted;
this.localStream.getAudioTracks().forEach(t => t.enabled = !this.callMuted);
},

toggleCamera() {
if (!this.localStream) return;
this.cameraOff = !this.cameraOff;
this.localStream.getVideoTracks().forEach(t => t.enabled = !this.cameraOff);
},

async switchCamera() {
if (!this.localStream || this.callType !== 'video') return;
const videoTrack = this.localStream.getVideoTracks()[0];
if (!videoTrack) return;
const currentFacing = videoTrack.getSettings().facingMode;
const nextFacing = currentFacing === 'environment' ? 'user' : 'environment';
try {
const newStream = await navigator.mediaDevices.getUserMedia({ audio: false, video: { facingMode: nextFacing } });
const newTrack = newStream.getVideoTracks()[0];
Object.values(this.peerConnections).forEach(entry => {
const sender = entry.pc?.getSenders().find(s => s.track && s.track.kind === 'video');
sender?.replaceTrack(newTrack);
});
this.localStream.removeTrack(videoTrack);
videoTrack.stop();
this.localStream.addTrack(newTrack);
if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
} catch (_) {
this.showToast('تعذر تبديل الكاميرا', 'error');
}
},

async toggleSpeaker() {
this.speakerMuted = !this.speakerMuted;
const apply = (el) => { if (el) el.muted = this.speakerMuted; };
apply(this.$refs.localVideo);
this.callParticipants.forEach(p => {
const el = this.$refs['remoteMedia_' + p.id];
apply(Array.isArray(el) ? el[0] : el);
});
},

formatCallElapsed() {
if (!this.callStartedAt) return '';
const secs = Math.floor((Date.now() - this.callStartedAt) / 1000);
const m = Math.floor(secs / 60).toString().padStart(2, '0');
const s = (secs % 60).toString().padStart(2, '0');
return `${m}:${s}`;
},

initCallSignaling() {
if (!window.Echo || !this.currentUserId) return;

const channel = window.Echo.private('user.' + this.currentUserId);

channel.listen('.call.initiated', (data) => {
if (this.callState) return; // already in/starting a call
if (this.callSettings.doNotDisturb) {
this.postCallAction(this.callRouteFor(this.callsRejectRouteTemplate, data.call_id), {});
return;
}
this.callType = data.type;
this.isGroupCall = !!data.is_group;
this.callContact = { id: data.caller.id, name: data.caller.name, avatar_url: data.caller.avatar_url };
this.callParticipants = [{ id: Number(data.caller.id), name: data.caller.name, avatar_url: data.caller.avatar_url, stream: null }];
this.currentCallId = data.call_id;
this.incomingCallOffer = data.offer;
this.callState = 'incoming';

this.postCallAction(this.callRouteFor(this.callsRingRouteTemplate, data.call_id), {});
this.maybeShowCallNotification(this.callContact, this.callType);
});

channel.listen('.call.ringing', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
if (this.callState === 'calling') this.callIsRinging = true;
});

channel.listen('.call.answered', async (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
const entry = this.getPeerEntry(data.from_user_id);
if (!entry.pc) return;
await entry.pc.setRemoteDescription(new RTCSessionDescription(data.answer));
entry.pendingCandidates.forEach(c => entry.pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {}));
entry.pendingCandidates = [];
this.callState = 'in-call';
this.callStartedAt = this.callStartedAt || Date.now();
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
});

channel.listen('.call.rejected', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
if (!this.isGroupCall) {
this.showToast('تم رفض المكالمة', 'info');
this.cleanupCall();
}
});

channel.listen('.call.ended', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
if (data.status === 'missed' && this.callState === 'calling') {
this.showToast('لم يرد المستخدم', 'error');
}
this.cleanupCall();
});

channel.listen('.call.ice-candidate', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
const entry = this.getPeerEntry(data.from_user_id);
if (entry.pc && entry.pc.remoteDescription) {
entry.pc.addIceCandidate(new RTCIceCandidate(data.candidate)).catch(() => {});
} else {
entry.pendingCandidates.push(data.candidate);
}
});

// مكالمات جماعية: مشارك جديد انضمّ — ننشئ اتصالاً مستقلاً موجّهاً له
channel.listen('.call.participant-joined', async (data) => {
if (Number(data.call_id) !== Number(this.currentCallId) || !this.localStream) return;
this.isGroupCall = true;
this.upsertParticipantTile(data.user.id, { name: data.user.name, avatar_url: data.user.avatar_url, stream: null });
const pc = this.createPeerConnection(data.user.id);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
const offer = await pc.createOffer();
await pc.setLocalDescription(offer);
await this.postCallAction(this.callRouteFor(this.callsPeerOfferRouteTemplate, this.currentCallId), {
to_user_id: data.user.id,
offer: { sdp: offer.sdp, type: offer.type },
});
});

// مكالمات جماعية: استقبال عرض من مشارك آخر موجود فعلاً (mesh)
channel.listen('.call.peer-offer', async (data) => {
if (Number(data.call_id) !== Number(this.currentCallId) || !this.localStream) return;
const pc = this.createPeerConnection(data.from_user_id);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
const entry = this.getPeerEntry(data.from_user_id);
entry.pendingCandidates.forEach(c => pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {}));
entry.pendingCandidates = [];
const answer = await pc.createAnswer();
await pc.setLocalDescription(answer);
await this.postCallAction(this.callRouteFor(this.callsPeerAnswerRouteTemplate, this.currentCallId), {
to_user_id: data.from_user_id,
answer: { sdp: answer.sdp, type: answer.type },
});
});

channel.listen('.call.peer-answer', async (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
const entry = this.getPeerEntry(data.from_user_id);
if (!entry.pc) return;
await entry.pc.setRemoteDescription(new RTCSessionDescription(data.answer));
entry.pendingCandidates.forEach(c => entry.pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {}));
entry.pendingCandidates = [];
});


channel.listen('.call.participant-left', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
const entry = this.peerConnections[String(data.user_id)];
if (entry?.pc) { try { entry.pc.close(); } catch (_) {} }
delete this.peerConnections[String(data.user_id)];
this.removeParticipantTile(data.user_id);
});
},

// ===== STATUS SYSTEM =====

openMyStatusEntry() {

if (Array.isArray(this.myStatuses) && this.myStatuses.length > 0) {

this.openStatusViewer({

user_id: this.currentUserId,

user_name: this.userName || 'Me',

user_avatar: this.currentUserAvatar || null,

statuses: this.myStatuses,

all_viewed: false,

});

return;

}

this.openStatusEditor();

},


handleGestureStart(e, targetObj, isText=false) {
    if (!this.statusEditorOpen) return;
    
    // Prevent filter swipe
    e.stopPropagation();

    const frame = this.$refs.statusPreviewPhone;
    if (!frame) return;
    const rect = frame.getBoundingClientRect();
    
    // Initialize state
    let activated = false;
    const startTouches = e.touches ? Array.from(e.touches) : [e];
    
    // For single touch pan
    const startClientX = startTouches[0].clientX;
    const startClientY = startTouches[0].clientY;
    
    // Depending on object, grab initial positions
    const startPosX = isText ? (targetObj.textPosX || 50) : (targetObj.mediaPosX || 50);
    const startPosY = isText ? (targetObj.textPosY || 50) : (targetObj.mediaPosY || 50);
    
    // For pinch/rotate
    let initialDist = 0;
    let initialAngle = 0;
    let initialScale = isText ? (targetObj.scale || 1) : (targetObj.mediaScale || 1);
    let initialRotation = isText ? (targetObj.rotate || 0) : (targetObj.mediaRotate || 0);
    
    if (startTouches.length === 2) {
        const dx = startTouches[1].clientX - startTouches[0].clientX;
        const dy = startTouches[1].clientY - startTouches[0].clientY;
        initialDist = Math.hypot(dx, dy);
        initialAngle = Math.atan2(dy, dx) * (180 / Math.PI);
        this.statusIsPinching = true;
    } else {
        this.statusIsPinching = false;
    }

    const move = (ev) => {
        ev.preventDefault(); // Prevent scrolling
        const touches = ev.touches ? Array.from(ev.touches) : [ev];
        
        if (touches.length === 2) {
            // Pinch / Rotate
            const dx = touches[1].clientX - touches[0].clientX;
            const dy = touches[1].clientY - touches[0].clientY;
            const dist = Math.hypot(dx, dy);
            const angle = Math.atan2(dy, dx) * (180 / Math.PI);
            
            if (initialDist > 0) {
                const scaleDelta = dist / initialDist;
                const newScale = initialScale * scaleDelta;
                const angleDelta = angle - initialAngle;
                const newRotation = initialRotation + angleDelta;
                
                if (isText) {
                    targetObj.scale = Math.max(0.2, Math.min(newScale, 5));
                    targetObj.rotate = newRotation;
                } else {
                    targetObj.mediaScale = Math.max(0.2, Math.min(newScale, 5));
                    targetObj.mediaRotate = newRotation;
                }
            }
        } else if (touches.length === 1 && !this.statusIsPinching) {
            // Pan
            const ddx = touches[0].clientX - startClientX;
            const ddy = touches[0].clientY - startClientY;
            if (!activated && Math.hypot(ddx, ddy) < 5) return;
            activated = true;
            if (isText) this.statusIsDragging = true;
            
            const dxPercent = (ddx / Math.max(1, rect.width)) * 100;
            const dyPercent = (ddy / Math.max(1, rect.height)) * 100;
            
            let x = startPosX + dxPercent;
            let y = startPosY + dyPercent;
            
            if (isText) {
                if (y > 85 && Math.abs(x - 50) < 15) {
                    this.statusTrashHover = true;
                    x = 50; y = 90;
                } else {
                    this.statusTrashHover = false;
                }
                if (!this.statusTrashHover) {
                    const cx = Math.abs(x - 50) < 4; const cy = Math.abs(y - 50) < 4;
                    if (cx) x = 50; if (cy) y = 50;
                    this.statusAlignGuides = { cx, cy, ex:false, ey:false };
                } else {
                    this.statusAlignGuides = { cx: false, cy: false, ex: false, ey: false };
                }
                targetObj.textPosX = Math.max(5, Math.min(95, x));
                targetObj.textPosY = Math.max(5, Math.min(95, y));
            } else {
                targetObj.mediaPosX = x;
                targetObj.mediaPosY = y;
            }
        }
    };

    const up = (ev) => {
        window.removeEventListener('pointermove', move);
        window.removeEventListener('pointerup', up);
        window.removeEventListener('touchmove', move);
        window.removeEventListener('touchend', up);
        
        if (isText) {
            this.statusIsDragging = false;
            this.statusAlignGuides = { cx: false, cy: false, ex: false, ey: false };
            if (this.statusTrashHover) {
                const ti = this.statusEditor.texts.indexOf(targetObj);
                if (ti > -1) this.statusEditor.texts.splice(ti, 1);
                this.statusEditor.activeTextIndex = -1;
                this.statusTextSelected = false;
                this.statusTrashHover = false;
                if (navigator.vibrate) navigator.vibrate(50);
            }
            if (activated) {
                this._justDraggedText = Date.now();
            } else if (!this.statusIsPinching) {
                this._justDraggedText = Date.now();
                this.statusFocusState = 2;
                this.$nextTick(() => {
                    if (this.$refs.typingOverlayTextarea) this.$refs.typingOverlayTextarea.focus();
                });
            }
        }
        this.statusIsPinching = false;
    };

    window.addEventListener('pointermove', move, {passive: false});
    window.addEventListener('pointerup', up);
    window.addEventListener('touchmove', move, {passive: false});
    window.addEventListener('touchend', up);
},
startStatusMediaDrag(e) {
    this.statusTextSelected = false;
    this.handleGestureStart(e, this.statusEditor, false);
},
drawerTouchStart(e) {
    this._drawerStartY = e.touches[0].clientY;
},
drawerTouchMove(e) {
    if (!this._drawerStartY) return;
    const dy = e.touches[0].clientY - this._drawerStartY;
    if (dy > 50) {
        this.statusStickerDrawerOpen = false;
        this._drawerStartY = null;
    }
},
drawerTouchEnd() {
    this._drawerStartY = null;
},
openStatusStickerDrawer() {
    this.ensureStickersLoaded();
    this.statusStickerDrawerOpen = true;
},
addCustomStickerToStatus(sticker) {
    this.statusEditor.texts.push({
        isSticker: true,
        url: sticker.url,
        isVideo: sticker.type === 'animated',
        textPosX: 50,
        textPosY: 50,
        scale: 1,
        rotate: 0
    });
    this.statusStickerDrawerOpen = false;
    this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
    this.statusFocusState = 0;
},
addStickerToStatus(emoji) {
    this.statusEditor.texts.push({
        content: emoji,
        textPosX: 50,
        textPosY: 50,
        scale: 2,
        rotate: 0,
        fontStyle: 'Segoe UI Emoji',
        textBgStyle: 'none'
    });
    this.statusStickerDrawerOpen = false;
    this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
    this.statusFocusState = 0;
},
startStatusTextDrag(e, ti) {
    this.statusEditor.activeTextIndex = ti;
    this.statusTextSelected = true;
    this.handleGestureStart(e, this.statusEditor.texts[ti], true);
},
startStatusMediaDrag(e) {
    this.handleGestureStart(e, this.statusEditor, false);
},

handleTypingOverlayBgClick(e) {
    if (this.statusFocusState === 2) {
        this.statusFocusState = 1;
        
        // Clean up empty text layer
        const ti = this.statusEditor.activeTextIndex;
        if (ti >= 0) {
            const content = this.statusEditor.texts[ti].content;
            if (!content || !content.trim()) {
                this.statusEditor.texts.splice(ti, 1);
                this.statusEditor.activeTextIndex = -1;
                this.statusTextSelected = false;
            }
        }
    }
},

handlePreviewBgClick(e) {
    if (Date.now() - (this._justBlurredText || 0) < 200) {
        return;
    }
    if (Date.now() - (this._justDraggedText || 0) < 300) {
        return; // Prevent background click from triggering right after dropping text or tapping it
    }
    
    const frame = this.$refs.statusPreviewPhone;
    if (!frame) return;
    const rect = frame.getBoundingClientRect();
    const x = (e.clientX - rect.left) / Math.max(1, rect.width) * 100;
    const y = (e.clientY - rect.top) / Math.max(1, rect.height) * 100;
    
    this.statusContextMenu = false;

    // Check if there is currently an empty active text
    if (this.statusEditor.activeTextIndex >= 0) {
        const active = this.statusEditor.texts[this.statusEditor.activeTextIndex];
        if (active && !active.content?.trim()) {
            // Move existing empty text
            active.textPosX = Math.round(x);
            active.textPosY = Math.round(y);
            this.statusFocusState = 2;
            this.statusTextSelected = true;
            this.$nextTick(() => {
                if (this.$refs.typingOverlayTextarea) this.$refs.typingOverlayTextarea.focus();
            });
            return;
        }
    }
    
    // Deselect current
    this.statusTextSelected = false;
    
    // Create new layer
    const layer = {
      _key: Date.now() + '_' + Math.random().toString(36).slice(2,6),
      content: '',
      fontStyle: 'Tajawal',
      fontSize: 28,
      textColor: '#ffffff',
      textPosX: 50,
      textPosY: 50,
      rotate: 0,
      textBgStyle: 'none',
    };
    this.statusEditor.texts.push(layer);
    this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
    this.statusTextSelected = true;
    this.statusFocusState = 2; // Enter typing mode
    
    this.$nextTick(() => {
        if (this.$refs.typingOverlayTextarea) this.$refs.typingOverlayTextarea.focus();
    });
},

handleTextBlur(ti) {
    this._justBlurredText = Date.now();
    if (this.statusFocusState === 2 && this.statusEditor.activeTextIndex === ti) {
        this.statusFocusState = 1;
        if (!this.statusEditor.texts[ti].content?.trim()) {
            this.statusEditor.texts.splice(ti, 1);
            this.statusEditor.activeTextIndex = -1;
            this.statusTextSelected = false;
        }
    }
},

duplicateActiveTextLayer() {
    const active = this.seActiveText();
    if (!active) return;
    const clone = JSON.parse(JSON.stringify(active));
    clone._key = Date.now() + '_' + Math.random().toString(36).slice(2,6);
    clone.textPosY = Math.min(100, clone.textPosY + 5); // offset slightly
    this.statusEditor.texts.push(clone);
    this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
    this.statusContextMenu = false;
},

async loadStatuses() {

try {

const r = await fetch(@json($statusesRoute), { headers: {'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json'} });

const j = await r.json();

if (j.success) { this.myStatuses = (j.data.my_statuses || []).sort((a,b) => new Date(a.created_at||a.createdAt) - new Date(b.created_at||b.createdAt)); this.contactStatuses = (j.data.contact_statuses || []).map(c => ({...c, statuses: (c.statuses||[]).sort((a,b) => new Date(a.created_at||a.createdAt) - new Date(b.created_at||b.createdAt))})); this.populateStatusPreviewCache(); }

} catch(e) {}

},

populateStatusPreviewCache() {
const all = [...(this.myStatuses || []), ...(this.contactStatuses || []).flatMap(g => (g.statuses || []).map(s => ({...s, userName: g.user_name, userAvatar: g.user_avatar})))];
for (const s of all) {
if (s.id) this.statusPreviewCache[Number(s.id)] = s;
}
},
 
openStatusEditor() {

if (this.statusViewerOpen && !this.statusPaused) this.toggleStatusPlayback();

this.statusEditorOpen = true;

this.statusTextSelected = false;

this.statusEditor = { type: 'text', textContent: '', textColor: '#ffffff', fontStyle: 'Tajawal', fontSize: 28, textPosX: 50, textPosY: 50, rotate: 0, bgColor: this.randomGradient(), textBgStyle: 'none', filterStyle: null, durationHours: 24, privacyType: 'all', mediaFile: null, mediaPreview: null, audioFile: null, texts: [], activeTextIndex: -1 };
this.statusAlignGuides = { cx: false, cy: false, ex: false, ey: false };
this.statusContextMenu = false;

},

toggleStatusPicker(tab) {
if (this.statusPickerTab === tab) { this.statusPickerTab = null; return; }
this.statusPickerTab = tab;
if (tab === 'emoji') this.ensureEmojiData();
if (tab === 'sticker') this.ensureStickersLoaded();
},

autoPickStatusBg() {
    this.statusEditor.bgColor = this.randomGradient();
},
randomGradient() {
    const presets = [
        'linear-gradient(135deg, #FF9A9E 0%, #FECFEF 99%, #FECFEF 100%)',
        'linear-gradient(120deg, #f6d365 0%, #fda085 100%)',
        'linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%)',
        'linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%)',
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)',
        'linear-gradient(135deg, #96fbc4 0%, #f9f586 100%)',
        'linear-gradient(135deg, #2af598 0%, #009efd 100%)',
        'linear-gradient(135deg, #cd9cf2 0%, #f6f3ff 100%)'
    ];
    let newBg;
    do {
        newBg = presets[Math.floor(Math.random() * presets.length)];
    } while (this.statusEditor && this.statusEditor.bgColor === newBg);
    return newBg;
},

onStatusTextClick() {
const hasText = this.statusEditor.textContent || this.statusEditor.texts.length;
if (!hasText) return;
if (this.statusTextSelected) {
    this.statusContextMenu = !this.statusContextMenu;
} else {
    this.statusTextSelected = true;
    this.statusContextMenu = false;
}
},

selectStatusText() {
const hasText = this.statusEditor.textContent || this.statusEditor.texts.length;
if (hasText) { this.statusTextSelected = true; this.statusContextMenu = false; }
},

deselectStatusText() {
this.statusTextSelected = false;
this.statusFocusState = 0;
this.statusContextMenu = false;
this.statusAlignGuides = { cx: false, cy: false };
},

seActiveText() {
const idx = this.statusEditor.activeTextIndex;
if (idx >= 0 && idx < this.statusEditor.texts.length) return this.statusEditor.texts[idx];
return null;
},

syncTextContentToActive() {
const active = this.seActiveText();
if (active) active.content = this.statusEditor.textContent;
},

addTextLayer() {
const txt = this.statusEditor.textContent?.trim();
if (!txt) return;
const layer = {
  _key: Date.now() + '_' + Math.random().toString(36).slice(2,6),
  content: txt,
  fontStyle: this.statusEditor.fontStyle,
  fontSize: this.statusEditor.fontSize,
  textColor: this.statusEditor.textColor,
  textPosX: 50,
  textPosY: 50,
  rotate: 0,
  textBgStyle: this.statusEditor.textBgStyle,
};
this.statusEditor.texts.push(layer);
this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
this.statusEditor.textContent = '';
this.statusTextSelected = true;
this.statusContextMenu = false;
this.autoPickStatusBg();
},

removeActiveTextLayer() {
const idx = this.statusEditor.activeTextIndex;
if (idx < 0 || idx >= this.statusEditor.texts.length) return;
this.statusEditor.texts.splice(idx, 1);
if (this.statusEditor.texts.length === 0) {
  this.statusEditor.activeTextIndex = -1;
  this.statusTextSelected = false;
} else {
  this.statusEditor.activeTextIndex = Math.min(idx, this.statusEditor.texts.length - 1);
  this.loadActiveTextToFlat();
}
},

removeStatusTextLayer(idx) {
if (idx < 0 || idx >= this.statusEditor.texts.length) return;
this.statusEditor.texts.splice(idx, 1);
if (this.statusEditor.texts.length === 0) {
  this.statusEditor.activeTextIndex = -1;
  this.statusTextSelected = false;
} else {
  this.statusEditor.activeTextIndex = Math.min(idx, this.statusEditor.texts.length - 1);
  this.loadActiveTextToFlat();
}
},

loadActiveTextToFlat() {
const active = this.seActiveText();
if (!active) return;
this.statusEditor.textContent = active.content || '';
this.statusEditor.fontStyle = active.fontStyle || 'Tajawal';
this.statusEditor.fontSize = active.fontSize || 28;
this.statusEditor.textColor = active.textColor || '#ffffff';
this.statusEditor.textPosX = active.textPosX || 50;
this.statusEditor.textPosY = active.textPosY || 50;
this.statusEditor.rotate = active.rotate || 0;
this.statusEditor.textBgStyle = active.textBgStyle || 'none';
},

selectStatusTextLayer(ti) {
if (ti < 0 || ti >= this.statusEditor.texts.length) return;
this.statusEditor.activeTextIndex = ti;
this.loadActiveTextToFlat();
this.statusTextSelected = true;
if (this.statusFocusState !== 2) {
    this.statusFocusState = 1;
}
this.statusContextMenu = false;
},

resetActiveTextRotate() {
this.statusContextMenu = false;
const active = this.seActiveText();
if (active) {
  active.rotate = 0;
  this.statusEditor.rotate = 0;
}
},

startStatusRotateDrag(e) {
const active = this.seActiveText();
if (!active) return;
const frame = this.$refs.statusPreviewPhone;
if (!frame) return;
const rect = frame.getBoundingClientRect();
const cx = rect.left + rect.width / 2;
const cy = rect.top + rect.height / 2;
const startAngle = Math.atan2(e.clientY - cy, e.clientX - cx) * (180 / Math.PI);
const startRotate = active.rotate || 0;

const move = (ev) => {
  const angle = Math.atan2(ev.clientY - cy, ev.clientX - cx) * (180 / Math.PI);
  const delta = angle - startAngle;
  const newRotate = startRotate + delta;
  active.rotate = newRotate;
  this.statusEditor.rotate = newRotate;
};

const up = () => {
  window.removeEventListener('pointermove', move);
  window.removeEventListener('pointerup', up);
  window.removeEventListener('pointercancel', up);
};

window.addEventListener('pointermove', move);
window.addEventListener('pointerup', up);
window.addEventListener('pointercancel', up);
},

syncActiveTextProp(key, val) {
const active = this.seActiveText();
if (active) active[key] = val;
},

startHandleDrag(mode, e) {
    const self = this;
    const active = self.seActiveText();
    if (!active) return;
    
    // Get text element center
    const textEl = e.target.closest('.se2-phone-text');
    if (!textEl) return;
    const rect = textEl.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;

    const startX = e.clientX;
    const startY = e.clientY;
    const startSize = active.fontSize || 28;
    const startAngle = Math.atan2(startY - cy, startX - cx) * (180 / Math.PI);
    const startRotate = active.rotate || 0;
    
    // Use initial distance for scale
    const startDist = Math.hypot(startX - cx, startY - cy);

    self.statusFocusState = 3; // dragging/resizing

    const onMove = (mv) => {
        // Rotation
        const currentAngle = Math.atan2(mv.clientY - cy, mv.clientX - cx) * (180 / Math.PI);
        let newRotate = startRotate + (currentAngle - startAngle);
        
        // Snap to nearest 90 degrees if within 5 degrees
        const snapTarget = Math.round(newRotate / 90) * 90;
        if (Math.abs(newRotate - snapTarget) < 5) {
            newRotate = snapTarget;
        }
        active.rotate = newRotate;
        self.statusEditor.rotate = newRotate;

        // Scale (resize)
        const currentDist = Math.hypot(mv.clientX - cx, mv.clientY - cy);
        const ratio = currentDist / Math.max(startDist, 1);
        self.statusEditor.fontSize = Math.max(12, Math.min(120, startSize * ratio));
        active.fontSize = self.statusEditor.fontSize;
    };
    
    const onUp = () => {
        self.statusFocusState = 2; // back to selected
        window.removeEventListener('pointermove', onMove);
        window.removeEventListener('pointerup', onUp);
        window.removeEventListener('pointercancel', onUp);
    };
    window.addEventListener('pointermove', onMove);
    window.addEventListener('pointerup', onUp);
    window.addEventListener('pointercancel', onUp);
},

cycleTextBgStyle() {
const order = ['none', 'translucent', 'solid', 'neon'];
const idx = order.indexOf(this.statusEditor.textBgStyle || 'none');
this.statusEditor.textBgStyle = order[(idx + 1) % order.length];
const active = this.seActiveText();
if (active) active.textBgStyle = this.statusEditor.textBgStyle;
},

cycleTextFontStyle() {
const fonts = this.statusEditorFonts || ['Tajawal', 'Lalezar', 'Amiri', '"Aref Ruqaa"', 'Rakkas', '"Reem Kufi"'];
const active = this.seActiveText();
if (!active) return;
const idx = fonts.indexOf(active.fontStyle || 'Tajawal');
const nextFont = fonts[(idx + 1) % fonts.length];
active.fontStyle = nextFont;
this.statusEditor.fontStyle = nextFont;
},

insertEmojiIntoStatus(emoji) {
this.statusEditor.textContent = (this.statusEditor.textContent || '') + emoji;
const active = this.seActiveText();
if (active) active.content = this.statusEditor.textContent;
this.autoPickStatusBg();
this.statusTextSelected = true;
},

useGifAsStatusBackground(gif) {
if (!gif?.fullUrl) return;
this.statusEditor.type = 'video';
this.statusEditor.mediaPreview = gif.fullUrl;
this.statusEditor.mediaFile = null;
this.showEmojiPicker = false;
},

useStickerAsStatusBackground(sticker) {
if (!sticker?.url) return;
this.statusEditor.type = sticker.type === 'animated' ? 'video' : 'image';
this.statusEditor.mediaPreview = sticker.url;
this.statusEditor.mediaFile = null;
this.showEmojiPicker = false;
},

startFilterSwipe(e) {
if (this.statusIsDragging || (e.touches && e.touches.length > 1)) return;
if (e.target.closest('.sc-text-layer') || e.target.closest('.draggable-media')) return;
if (e.target.closest('.se2-phone-text') || e.target.closest('.se2-typing-overlay') || e.target.closest('.se2-handle') || e.target.closest('.se2-context-popup')) return;
this._filterSwipeX = e.touches ? e.touches[0].clientX : null;
},

moveFilterSwipe(e) {
if (this.statusIsDragging) return;
if (this._filterSwipeX === null) return;
const dx = (e.touches ? e.touches[0].clientX : 0) - this._filterSwipeX;
if (Math.abs(dx) < 30) return;
this._applyFilterStep(dx < 0 ? 1 : -1);
this._filterSwipeX = e.touches ? e.touches[0].clientX : null;
},

endFilterSwipe() {
this._filterSwipeX = null;
},

startFilterSwipeMouse(e) {
if (e.target.closest('.se2-phone-text') || e.target.closest('.se2-typing-overlay') || e.target.closest('.se2-handle') || e.target.closest('.se2-context-popup')) return;
const startX = e.clientX;
const self = this;
const onMove = (mv) => {
  const dx = mv.clientX - startX;
  if (Math.abs(dx) > 40) {
    self._applyFilterStep(dx < 0 ? 1 : -1);
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  }
};
const onUp = () => {
  document.removeEventListener('mousemove', onMove);
  document.removeEventListener('mouseup', onUp);
};
document.addEventListener('mousemove', onMove);
document.addEventListener('mouseup', onUp);
},

_applyFilterStep(dir) {
const filters = this.statusEditorFilters;
const cur = filters.findIndex(f => f.id === this.statusEditor.filterStyle);
const next = (cur + dir + filters.length) % filters.length;
this.statusEditor.filterStyle = filters[next].id;
this.filterSwipeHint = filters[next].label;
if (this._filterSwipeTimer) clearTimeout(this._filterSwipeTimer);
this._filterSwipeTimer = setTimeout(() => { this.filterSwipeHint = null; }, 1200);
},

closeStatusEditor() {

if (this.statusEditor && this.statusEditor.mediaPreview) { try { URL.revokeObjectURL(this.statusEditor.mediaPreview); } catch (_) {} this.statusEditor.mediaPreview = null; }

this.statusEditorOpen = false; this.statusPublishing = false;

if (this.statusPaused) this.toggleStatusPlayback();

},

triggerStatusMedia() { this.$refs.statusMediaInput && this.$refs.statusMediaInput.click(); },

triggerStatusAudio() { this.$refs.statusAudioInput && this.$refs.statusAudioInput.click(); },

onStatusMediaSelect(e) {

const f = e.target.files[0]; if (!f) return;

if (this.statusEditor.mediaPreview) { try { URL.revokeObjectURL(this.statusEditor.mediaPreview); } catch (_) {} }

this.statusEditor.mediaFile = f;

this.statusEditor.type = f.type.startsWith('video/') ? 'video' : 'image';

this.statusEditor.mediaPreview = URL.createObjectURL(f);

if (this.statusEditor.type === 'video') {

const v = document.createElement('video');

v.preload = 'metadata';

v.src = this.statusEditor.mediaPreview;

v.onloadedmetadata = () => {

this.statusEditor.mediaDurationSec = Math.max(1, Math.round(Number(v.duration || 0)));

};

} else {

this.statusEditor.mediaDurationSec = null;

}

},

onStatusAudioSelect(e) { const f = e.target.files[0]; if (!f) return; this.statusEditor.audioFile = f; },

onStatusTextInput(e) { this.statusEditor.textContent = e.target.innerText; },

onStatusTextBlur(e) { this.statusEditor.textContent = e.target.innerText; },

async publishStatus() {

if (!this.statusEditor.textContent && !this.statusEditor.texts.length && !this.statusEditor.mediaFile) { this.showToast('أضف نصاً أو صورة', 'error'); return; }

this.statusPublishing = true;

const fd = new FormData();

fd.append('type', this.statusEditor.type);

const allTextContent = this.statusEditor.texts.map(t => t.content).filter(Boolean).join('\n');
const textStr = this.statusEditor.textContent || allTextContent;
if (textStr) fd.append('text_content', textStr);
if (this.statusEditor.texts.length) fd.append('text_objects', JSON.stringify(this.statusEditor.texts));

fd.append('text_color', this.statusEditor.textColor);

fd.append('font_style', this.statusEditor.fontStyle);

fd.append('font_size', this.statusEditor.fontSize);

fd.append('text_pos_x', this.statusEditor.textPosX || 50);

fd.append('text_pos_y', this.statusEditor.textPosY || 50);

fd.append('bg_color', this.statusEditor.bgColor);

if (this.statusEditor.filterStyle) fd.append('filter_style', this.statusEditor.filterStyle);

fd.append('duration_hours', this.statusEditor.durationHours);

if (this.statusEditor.mediaDurationSec) fd.append('media_duration_sec', this.statusEditor.mediaDurationSec);

fd.append('privacy_type', this.statusEditor.privacyType);

if (this.statusEditor.mediaFile) fd.append('media', this.statusEditor.mediaFile);

if (this.statusEditor.audioFile) fd.append('audio', this.statusEditor.audioFile);

try {

if (this.statusEditor.editingStatusId) {

const r = await fetch(this.statusUpdateRoute.replace('__STATUS_ID__', this.statusEditor.editingStatusId), {

method: 'POST',

headers: {'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json'},

body: fd

});

const j = await r.json().catch(() => ({}));

if (j.success) { this.showToast('تم تحديث الحالة', 'success'); this.closeStatusEditor(); this.loadStatuses(); }

else { this.showToast(j.message || 'فشل تحديث الحالة', 'error'); }

} else {

const r = await fetch(@json($statusCreateRoute), { method: 'POST', headers: {'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json'}, body: fd });

const j = await r.json();

if (j.success) { this.showToast('تم نشر الحالة', 'success'); this.closeStatusEditor(); this.loadStatuses(); }

else { this.showToast('فشل النشر', 'error'); }

}

} catch(e) { this.showToast('خطأ في الاتصال', 'error'); }

this.statusPublishing = false;

},

openStatusViewer(contactStatus) {

this.statusViewerContact = contactStatus; this.statusViewerIndex = 0; this.statusViewerProgress = 0;

this.statusViewerOpen = true;

this.statusViewerMuted = false;

this.statusPaused = false;

this.showStatusFullEmoji = false;

this.statusReplyFocused = false;

this.showQuickEmojiBar = false;

this.statusViewerReady = false;

this.statusLiked = this.currentStatus?.myReaction === '❤️';

this.statusViewerDurationMs = Number((this.currentStatus?.mediaDurationSec || 0) * 1000) || (this.currentStatus?.type === 'video' ? 15000 : 5000);

if (this.currentStatus?.type === 'text') {

this.statusViewerReady = true;

this.startStatusProgress();

}

this.markStatusViewed();

},

closeStatusViewer() {

this.statusViewerOpen = false;

this.statusPaused = false;

this.showStatusFullEmoji = false;

this.statusReplyFocused = false;

this.showQuickEmojiBar = false;

this.statusLongPressActive = false;

this.statusLongPressFired = false;

if (this.statusLongPressTimer) { clearTimeout(this.statusLongPressTimer); this.statusLongPressTimer = null; }

if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }

},

startStatusProgress(resetProgress = true) {

if (this.statusViewerTimer) clearInterval(this.statusViewerTimer);

if (!this.statusViewerReady || this.statusPaused || this.showStatusFullEmoji || this.statusViewersOpen) return;

if (resetProgress) this.statusViewerProgress = 0;

const tick = 100; const totalMs = Math.max(1500, Number(this.statusViewerDurationMs || 5000));

this.statusViewerTimer = setInterval(() => {

this.statusViewerProgress += (tick / totalMs) * 100;

if (this.statusViewerProgress >= 100) this.nextStatus();

}, tick);

},

nextStatus() {

const statuses = this.statusViewerContact ? this.statusViewerContact.statuses : [];

if (this.statusViewerIndex < statuses.length - 1) {

this.statusViewerIndex++; this.statusViewerProgress = 0;

this.showStatusFullEmoji = false;

this.statusLiked = this.currentStatus?.myReaction === '❤️';

this.statusViewerReady = false;

this.statusViewerDurationMs = Number((this.currentStatus?.mediaDurationSec || 0) * 1000) || (this.currentStatus?.type === 'video' ? 15000 : 5000);

if (this.currentStatus?.type === 'text') {

this.statusViewerReady = true;

this.startStatusProgress();

}

this.markStatusViewed();

} else { this.closeStatusViewer(); }

},

prevStatus() {

if (this.statusViewerIndex > 0) {

this.statusViewerIndex--; this.statusViewerProgress = 0;

this.showStatusFullEmoji = false;

this.statusLiked = this.currentStatus?.myReaction === '❤️';

this.statusViewerReady = false;

this.statusViewerDurationMs = Number((this.currentStatus?.mediaDurationSec || 0) * 1000) || (this.currentStatus?.type === 'video' ? 15000 : 5000);

if (this.currentStatus?.type === 'text') {

this.statusViewerReady = true;

this.startStatusProgress();

}

this.markStatusViewed();

} else if (this.currentStatus) {

this.statusViewerProgress = 0;

this.statusViewerReady = false;

this.statusViewerDurationMs = Number((this.currentStatus?.mediaDurationSec || 0) * 1000) || (this.currentStatus?.type === 'video' ? 15000 : 5000);

if (this.currentStatus?.type === 'text') {

this.statusViewerReady = true;

this.startStatusProgress();

}

const video = this.$refs.statusViewerVideo;

if (video) { try { video.currentTime = 0; } catch(_) {} }

}

},

async markStatusViewed() {

const s = this.currentStatus; if (!s) return;

try {

await fetch(@json($statusViewRoute).replace('__STATUS_ID__', s.id), { method: 'POST', headers: {'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json'} });

if (this.statusViewerContact && Number(this.statusViewerContact.user_id) !== Number(this.currentUserId)) {

const all = this.statusViewerContact.statuses || [];

const done = this.statusViewerIndex >= (all.length - 1);

if (done) {

this.statusViewerContact.all_viewed = true;

const idx = this.contactStatuses.findIndex(x => Number(x.user_id) === Number(this.statusViewerContact.user_id));

if (idx !== -1) this.contactStatuses[idx].all_viewed = true;

}

}

} catch(e) {}

},

async openStatusViewers() {

const s = this.currentStatus; if (!s) return;

if (!this.statusPaused) this.toggleStatusPlayback();

if (this.profileModalContact && this.profileOpenedFromViewers) {

this.profileModalContact = null;

this.profileOpenedFromViewers = false;

}

try {

if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }

const r = await fetch(@json($statusViewersRoute).replace('__STATUS_ID__', s.id), { headers: {'Accept': 'application/json'} });

const j = await r.json();

if (j.success) { this.statusViewersList = j.data; this.statusViewersOpen = true; }

} catch(e) {}

},

closeStatusViewers() {

this.statusViewersOpen = false;

if (this.statusViewerOpen && this.statusViewerReady) this.startStatusProgress(false);

if (this.statusViewerOpen && this.statusPaused) this.toggleStatusPlayback();

},

openViewerProfile(v) {

if (!v || !v.id) return;

this.profileModalContact = this.normalizeProfileContact(v);

this.statusViewersOpen = false;

this.profileOpenedFromViewers = true;

},

openViewerStatus(v) {

if (!v || !v.id) return;

const group = (this.contactStatuses || []).find(s => Number(s.user_id) === Number(v.id));

if (group) { this.statusViewersOpen = false; this.openStatusViewer(group); }

},

editCurrentStatus() {

const s = this.currentStatus;

if (!s) return;

if (!this.statusPaused) this.toggleStatusPlayback();

this.statusEditorOpen = true;

this.statusEditor = {

type: s.type || 'text',

textContent: s.text_content || s.textContent || '',

textColor: s.text_color || s.textColor || '#ffffff',

fontStyle: s.font_style || s.fontStyle || 'Tajawal',

fontSize: Number(s.font_size || s.fontSize || 28),

textPosX: s.textPosX ?? 50, textPosY: s.textPosY ?? 50, rotate: s.textRotate ?? 0, textBgStyle: s.textBgStyle ?? 'none', bgColor: s.bg_color || s.bgColor || 'linear-gradient(135deg, var(--theme-surface, #17130c), var(--theme-surface-2, #241b12), var(--theme-gold-dark, #5b4124))',

filterStyle: s.filter_style || s.filterStyle || null,

durationHours: Number(s.duration_hours || s.durationHours || 24),

privacyType: 'all',

mediaFile: null,

mediaPreview: s.contentUrl || null,

audioFile: null,

editingStatusId: s.id, texts: this.getStatusTextLayers(s), activeTextIndex: -1,
texts: (() => {
    if (s.text_layers) {
        try {
            return typeof s.text_layers === 'string' ? JSON.parse(s.text_layers) : s.text_layers;
        } catch(e) { return []; }
    }
    return [];
})(),
activeTextIndex: -1,
};

},

async deleteCurrentStatus() {
const s = this.currentStatus;
if (!s) return;
if (!this.statusPaused) this.toggleStatusPlayback();
this.deleteStatusTarget = s;
},


removeStatusFromList(id) {
this.myStatuses = (this.myStatuses || []).filter(s => Number(s.id) !== Number(id));
},
async confirmDeleteStatus() {
const s = this.deleteStatusTarget;
if (!s) return;
this.deleteStatusTarget = null;
try {
const tpl = @json($statusDeleteRoute);
const url = tpl.replace('__STATUS_ID__', s.id);
const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const data = await res.json().catch(() => ({}));
if (data && data.success) {
this.removeStatusFromList(s.id);
this.closeStatusViewer();
this.showToast('تم حذف الحالة بنجاح', 'success');
} else {
this.showToast((data && data.message) || 'تعذر حذف الحالة', 'error');
}
} catch (_) {
this.showToast('فشل حذف الحالة', 'error');
}
},
onReplyInputFocus() {
this.statusReplyFocused = true;
this.showStatusFullEmoji = false;
this.showQuickEmojiBar = true;
this.statusPaused = true;
if (this.statusViewerTimer) { clearInterval(this.statusViewerTimer); this.statusViewerTimer = null; }
const v = this.$refs.statusViewerVideo;
if (v && !v.paused) v.pause();
},
onReplyInputBlur() {
this.statusReplyFocused = false;
if (this.showStatusFullEmoji) return;
this.statusPaused = false;
const v = this.$refs.statusViewerVideo;
if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});
if (this.statusViewerReady) this.startStatusProgress(false);
},
closeReplyPanel() {
this.statusReplyFocused = false;
this.showStatusFullEmoji = false;
this.showQuickEmojiBar = false;
this.statusPaused = false;
const v = this.$refs.statusViewerVideo;
if (v && this.currentStatus?.type === 'video') v.play().catch(() => {});
if (this.statusViewerReady) this.startStatusProgress(false);
},
async toggleStatusLike() {
if (!this.statusViewerContact) return;
const statusId = Number(this.currentStatus?.id || 0);
const recipientId = Number(this.statusViewerContact.user_id);
if (!recipientId || recipientId === Number(this.currentUserId)) return;
try {
const r = await fetch(this.statusReactionRoute, {
method: 'POST',
headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
body: JSON.stringify({ status_id: statusId, emoji: '❤️' }),
});
const j = await r.json();
const newLiked = j?.data?.liked ?? !this.statusLiked;
if (newLiked && !this.statusLiked) {
this.statusLikeAnimating = true;
setTimeout(() => { this.statusLikeAnimating = false; }, 600);
}
this.statusLiked = newLiked;
if (this.currentStatus) {
this.currentStatus.myReaction = newLiked ? '❤️' : null;
this.statusPreviewCache[Number(this.currentStatus.id)] = { id: this.currentStatus.id, type: this.currentStatus.type, contentUrl: this.currentStatus.contentUrl, textContent: this.currentStatus.textContent, bgColor: this.currentStatus.bgColor, userName: this.statusViewerContact?.user_name || 'حالة', userAvatar: this.statusViewerContact?.user_avatar || null };
}
} catch (_) {}
},
async replyToStatus(e) {
const val = String(this.statusReplyText || (e && e.target ? e.target.value : '') || '').trim();
if (!val || !this.statusViewerContact) return;
const statusId = Number(this.currentStatus?.id || 0);
const recipientId = Number(this.statusViewerContact.user_id || 0);
if (!statusId || !recipientId) return;
try {
const r = await fetch(this.statusReplyRoute, {
method: 'POST',
headers: {'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json'},
body: JSON.stringify({ status_id: statusId, content: val }),
});
const j = await r.json();
 if (j.success || j.id) {
this.statusReplyText = '';
if (e && e.target) e.target.value = '';
if (this.currentStatus) {
const cs = this.currentStatus;
this.statusPreviewCache[Number(cs.id)] = { id: cs.id, type: cs.type, contentUrl: cs.contentUrl, textContent: cs.textContent, bgColor: cs.bgColor, userName: this.statusViewerContact?.user_name || 'حالة', userAvatar: this.statusViewerContact?.user_avatar || null };
}
this.showToast('تم إرسال الرد ✓', 'success');
this.closeReplyPanel();
} else { this.showToast('فشل الإرسال', 'error'); }
} catch (_) { this.showToast('خطأ في الاتصال', 'error'); }
},

async sendQuickStatusReaction(emoji) {

if (!this.statusViewerContact) return;

const statusId = Number(this.currentStatus?.id || 0);

const recipientId = Number(this.statusViewerContact.user_id);

if (!recipientId || recipientId === Number(this.currentUserId)) return;

const txt = document.createElement('textarea');
txt.innerHTML = emoji;
const decoded = txt.value || emoji;

try {

const reply = await fetch(this.statusReplyRoute, {

method: 'POST',

headers: {'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json'},

body: JSON.stringify({ status_id: statusId, content: decoded }),

});

const j = await reply.json();

if (!j.success && !j.id) { this.showToast('فشل الإرسال', 'error'); return; }

try {

await fetch(this.statusReactionRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ status_id: statusId, emoji: decoded }),

});

} catch (_) {}

 this.trackRecentStatusEmoji(decoded);

if (this.currentStatus) {
this.currentStatus.myReaction = decoded;
const cs = this.currentStatus;
this.statusPreviewCache[Number(cs.id)] = { id: cs.id, type: cs.type, contentUrl: cs.contentUrl, textContent: cs.textContent, bgColor: cs.bgColor, userName: this.statusViewerContact?.user_name || 'حالة', userAvatar: this.statusViewerContact?.user_avatar || null };
}

if (decoded === '❤️') {

this.statusLiked = true;

this.statusLikeAnimating = true;

setTimeout(() => { this.statusLikeAnimating = false; }, 600);

}

this.showToast(decoded + ' ✓', 'success');

this.closeReplyPanel();

} catch (_) { this.showToast('خطأ في الاتصال', 'error'); }

},

trackRecentStatusEmoji(emoji) {
const txt = document.createElement('textarea');
txt.innerHTML = emoji;
const cleaned = txt.value || emoji;
this.recentStatusEmojis = [cleaned, ...this.recentStatusEmojis.filter(e => e !== cleaned)].slice(0, 8);
try { localStorage.setItem('recentStatusEmojis', JSON.stringify(this.recentStatusEmojis)); } catch (_) {}
},

onStatusAvatarError(e, statusId) {
const id = Number(statusId);
if (id && !this.brokenStatusAvatars.includes(id)) { this.brokenStatusAvatars.push(id); }
},

getStatusPreview(statusId) {
const id = Number(statusId);
if (!id) return null;
if (this.statusPreviewCache[id]) return this.statusPreviewCache[id];
const allGroups = [...(this.contactStatuses || []), { user_id: this.currentUserId, user_name: this.userName, user_avatar: this.currentUserAvatar, statuses: this.myStatuses || [], all_viewed: false }];
for (const g of allGroups) {
const s = (g.statuses || []).find(s => Number(s.id) === id);
if (s) {
return { ...s, userName: g.user_name, userAvatar: g.user_avatar };
}
}
return null;
},

// ===== VIDEO EDITOR =====

openVideoEditor(file) {

this.videoEditorFile = file; this.videoEditorUrl = URL.createObjectURL(file);

this.videoEditorOpen = true; this.videoEditorStart = 0; this.videoEditorEnd = 0;

this.videoEditorQuality = '720p'; this.videoEditorProgress = 0; this.videoEditorProcessing = false;

},

closeVideoEditor() {
this.videoEditorOpen = false;
this.videoEditorPreviewOpen = false;
if (this.videoEditorUrl) { URL.revokeObjectURL(this.videoEditorUrl); this.videoEditorUrl = null; }
if (this.videoEditorPreviewUrl) { URL.revokeObjectURL(this.videoEditorPreviewUrl); this.videoEditorPreviewUrl = null; }
this.videoEditorFile = null;
this.videoEditorPreviewFile = null;
},
closeVideoPreview() {
this.videoEditorPreviewOpen = false;
if (this.videoEditorPreviewUrl) { URL.revokeObjectURL(this.videoEditorPreviewUrl); this.videoEditorPreviewUrl = null; }
this.videoEditorPreviewFile = null;
this.closeVideoEditor();
},
acceptProcessedVideo() {
if (this.videoEditorPreviewFile) this.addPendingAttachment(this.videoEditorPreviewFile);
if (this.videoEditorPreviewUrl) { URL.revokeObjectURL(this.videoEditorPreviewUrl); this.videoEditorPreviewUrl = null; }
this.videoEditorPreviewFile = null;
this.videoEditorPreviewOpen = false;
this.closeVideoEditor();
},
openImageEditor(file) {
this.imageEditorFile = file;
this.imageEditorUrl = URL.createObjectURL(file);
this.imageEditorOpen = true;
this.imageEditorBrightness = 100;
this.imageEditorContrast = 100;
this.imageEditorSaturate = 100;
this.imageEditorRotate = 0;
this.imageEditorFlipH = false;
this.imageEditorFlipV = false;
},
closeImageEditor() {
this.imageEditorOpen = false;
if (this.imageEditorUrl) { URL.revokeObjectURL(this.imageEditorUrl); this.imageEditorUrl = null; }
this.imageEditorFile = null;
},
resetImageEditor() {
this.imageEditorBrightness = 100;
this.imageEditorContrast = 100;
this.imageEditorSaturate = 100;
this.imageEditorRotate = 0;
this.imageEditorFlipH = false;
this.imageEditorFlipV = false;
},
async sendEditedImage() {
if (!this.imageEditorFile) { this.closeImageEditor(); return; }
const img = this.$refs.imagePreviewEl;
if (!img) { this.addPendingAttachment(this.imageEditorFile); this.closeImageEditor(); return; }
try {
const canvas = document.createElement('canvas');
const rotate = this.imageEditorRotate;
const rad = (rotate * Math.PI) / 180;
const sw = img.naturalWidth, sh = img.naturalHeight;
const cos = Math.abs(Math.cos(rad)), sin = Math.abs(Math.sin(rad));
canvas.width = Math.round(sw * cos + sh * sin);
canvas.height = Math.round(sw * sin + sh * cos);
const ctx = canvas.getContext('2d');
ctx.filter = `brightness(${this.imageEditorBrightness}%) contrast(${this.imageEditorContrast}%) saturate(${this.imageEditorSaturate}%) ${this.imageEditorFilter || ''}`.trim();
ctx.translate(canvas.width/2, canvas.height/2);
ctx.rotate(rad);
ctx.scale(this.imageEditorFlipH ? -1 : 1, this.imageEditorFlipV ? -1 : 1);
ctx.drawImage(img, -sw/2, -sh/2, sw, sh);
const blob = await new Promise(res => canvas.toBlob(res, 'image/jpeg', 0.92));
const editedFile = new File([blob], this.imageEditorFile.name.replace(/\.[^.]+$/, '_edited.jpg'), { type: 'image/jpeg' });
this.addPendingAttachment(editedFile);
} catch (e) {
this.addPendingAttachment(this.imageEditorFile);
}
this.closeImageEditor();
},

onVeMetadata(e) { this.videoEditorDuration = e.target.duration; this.videoEditorEnd = e.target.duration; },

onVeStartChange() {

if (this.videoEditorStart >= this.videoEditorEnd) this.videoEditorStart = Math.max(0, this.videoEditorEnd - 1);

if (this.$refs.vePreview) this.$refs.vePreview.currentTime = this.videoEditorStart;

},

onVeEndChange() {

if (this.videoEditorEnd <= this.videoEditorStart) this.videoEditorEnd = Math.min(this.videoEditorDuration, this.videoEditorStart + 1);

},

async processAndSendVideo() {

if (!this.videoEditorFile) return;

this.videoEditorProcessing = true; this.videoEditorProgress = 0;

const resMap = { '360p': { w: 640, h: 360 }, '480p': { w: 854, h: 480 }, '720p': { w: 1280, h: 720 }, '1080p': { w: 1920, h: 1080 } };
const bitrateMap = { '360p': 500000, '480p': 1000000, '720p': 2500000, '1080p': 5000000 };
const targetRes = resMap[this.videoEditorQuality] || resMap['720p'];
const targetBitrate = bitrateMap[this.videoEditorQuality] || 2500000;

const startTime = this.videoEditorStart || 0;
const endTime = this.videoEditorEnd || 0;
const hasTrim = endTime > 0 && endTime > startTime;
const totalDuration = hasTrim ? (endTime - startTime) : 0;

try {

const video = document.createElement('video');
video.src = URL.createObjectURL(this.videoEditorFile);
video.muted = false;
video.currentTime = startTime || 0;
video.preload = 'auto';

await new Promise((resolve, reject) => {
    video.onloadedmetadata = () => {
        if (hasTrim && !endTime) this.videoEditorEnd = video.duration;
        resolve();
    };
    video.onerror = reject;
    video.load();
});

const origW = video.videoWidth || targetRes.w;
const origH = video.videoHeight || targetRes.h;
const scale = Math.min(targetRes.w / origW, targetRes.h / origH, 1);
const cw = Math.round(origW * scale);
const ch = Math.round(origH * scale);

const canvas = document.createElement('canvas');
canvas.width = cw;
canvas.height = ch;
const ctx = canvas.getContext('2d');

const videoStream = canvas.captureStream(30);
let combinedStream = videoStream;

try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const source = audioCtx.createMediaElementSource(video);
    const dest = audioCtx.createMediaStreamDestination();
    source.connect(dest);
    if (dest.stream.getAudioTracks().length) {
        videoStream.addTrack(dest.stream.getAudioTracks()[0]);
    }
    combinedStream = videoStream;
} catch (_) {}

const recorder = new MediaRecorder(combinedStream, {
    mimeType: MediaRecorder.isTypeSupported('video/webm;codecs=vp8,opus') ? 'video/webm;codecs=vp8,opus' : 'video/webm;codecs=vp8',
    videoBitsPerSecond: targetBitrate,
});

const chunks = [];
recorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };

const processed = await new Promise((resolve, reject) => {
    recorder.onstop = () => {
        const blob = new Blob(chunks, { type: recorder.mimeType || 'video/webm' });
        resolve(new File([blob], 'video_' + Date.now() + '.webm', { type: 'video/webm' }));
    };
    recorder.onerror = reject;

    recorder.start();

    const fps = 30;
    const frameMs = Math.round(1000 / fps);
    const totalFrames = hasTrim ? Math.ceil(totalDuration * fps) : Math.ceil(video.duration * fps);
    let frameCount = 0;
    let stopped = false;
    let timer = null;

    const drawFrame = () => {
        if (stopped) return;
        if (frameCount >= totalFrames || (hasTrim && video.currentTime >= endTime && frameCount > 0)) {
            stopped = true;
            clearInterval(timer);
            recorder.stop();
            video.pause();
            return;
        }

        ctx.drawImage(video, 0, 0, cw, ch);
        frameCount++;
        this.videoEditorProgress = Math.min(99, Math.round((frameCount / totalFrames) * 100));
    };

    video.play();
    video.addEventListener('playing', () => {
        timer = setInterval(drawFrame, frameMs);
    }, { once: true });
});

this.videoEditorProgress = 100;
this.videoEditorProcessing = false;

const previewUrl = URL.createObjectURL(processed);
this.videoEditorPreviewFile = processed;
this.videoEditorPreviewUrl = previewUrl;
this.videoEditorPreviewOpen = true;

this.showToast('تمت المعالجة — راجع النتيجة', 'success');

} catch(err) {

console.error('Video processing error:', err);
this.videoEditorProcessing = false;
const rawFile = this.videoEditorFile;
this.closeVideoEditor();
if (rawFile) { this.addPendingAttachment(rawFile); this.showToast('سيتم إرسال الفيديو بدون معالجة', 'info'); }

}

},

// ===== SETTINGS METHODS =====

async loadMessagingSettings() {

if (!this.settingsGetRoute || this.settingsGetRoute === '#') return;

try {

const res = await fetch(this.settingsGetRoute, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });

const data = await res.json().catch(() => ({}));

if (!res.ok || !data.success || !data.data) return;

if (data.data.privacy) this.settingsPrivacy = { ...this.settingsPrivacy, ...data.data.privacy };

if (data.data.notifications) this.settingsNotifications = { ...this.settingsNotifications, ...data.data.notifications };

this.settingSoundEnabled = this.settingsNotifications.soundEnabled !== false;
this.settingNotifyEnabled = this.settingsNotifications.desktopEnabled !== false;

if (data.data.media) this.settingsMedia = { ...this.settingsMedia, ...data.data.media };

if (data.data.security) this.settingsSecurity = { ...this.settingsSecurity, ...data.data.security, pin: '', pinConfirm: '' };

if (data.data.chats) {
    this.settingsChats = { ...this.settingsChats, ...data.data.chats };
    this.applyReduceMotion(!!this.settingsChats.reduceMotion);
    this.applyChatFont();
    this.updateDocumentTitle();
    if (this.settingsChats.autoNightMode && !this.darkModeMediaQuery) {
        this.darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.darkModeMediaQuery.addEventListener('change', this.handleSystemThemeChange);
    }
}

if (data.data.account) {
    this.settingsAccount = { ...this.settingsAccount, ...data.data.account };
    this.settingsEditName = this.settingsAccount.name || '';
    this.settingsEditUsername = this.settingsAccount.username || '';
    this.settingsEditPhone = this.settingsAccount.phone || '';
    this.settingsEditBio = this.settingsAccount.bio || '';
    this.settingsEditBirthday = this.settingsAccount.birthday || '';
    this.settingsLanguageChoice = this.settingsAccount.locale || 'ar';
}

} catch (_) {}

},

async saveMessagingSettings() {

if (!this.settingsSaveRoute || this.settingsSaveRoute === '#') return;

const security = { ...this.settingsSecurity };

delete security.pin;

delete security.pinConfirm;

try {

this.settingSoundEnabled = this.settingsNotifications.soundEnabled !== false;
this.settingNotifyEnabled = this.settingsNotifications.desktopEnabled !== false;

await fetch(this.settingsSaveRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({

privacy: this.settingsPrivacy,

notifications: this.settingsNotifications,

media: this.settingsMedia,

security,

chats: {
    defaultWallpaper: this.activeWallpaper >= 0 ? `idx:${this.activeWallpaper}` : 'default',
    sendWithEnter: this.settingsChats.sendWithEnter,
    reduceMotion: this.settingsChats.reduceMotion,
    defaultTheme: this.settingsChats.defaultTheme,
    fontFamily: this.settingsChats.fontFamily,
    autoNightMode: this.settingsChats.autoNightMode,
    doubleClickAction: this.settingsChats.doubleClickAction,
    allowSensitiveContent: this.settingsChats.allowSensitiveContent,
    nameColor: this.settingsChats.nameColor || '',
    tabsPosition: this.settingsChats.tabsPosition || 'left',
    spellcheckEnabled: this.settingsChats.spellcheckEnabled !== false,
    showFolderTags: this.settingsChats.showFolderTags || false,
    showUnreadInTitle: this.settingsChats.showUnreadInTitle || false,
},

}),

});

} catch (_) {}

},

async uploadAvatarFile(e) {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!this.settingsExtraRoutes?.profileUpdate || this.settingsExtraRoutes.profileUpdate === '#') {
        this.showToast('غير متاح حالياً', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('name', this.settingsAccount.name || this.userName);
    fd.append('email', this.currentUserEmail || '');
    fd.append('phone', this.settingsAccount.phone || '');
    fd.append('bio', this.settingsAccount.bio || '');
    fd.append('avatar_url', file);

    try {
        const res = await fetch(this.settingsExtraRoutes.profileUpdate, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'text/html' },
            body: fd,
        });
        if (!res.ok) throw new Error('upload_failed');
        await this.loadMessagingSettings();
        this.myProfileAvatar = this.normalizeAvatarUrl(this.settingsAccount.avatar_url);
        this.currentUserAvatar = this.settingsAccount.avatar_url;
        const me = this.contacts.find(c => Number(c.id) === Number(this.currentUserId));
        if (me) me.avatar_url = this.settingsAccount.avatar_url;
        this.showToast('تم تحديث صورة الحساب', 'success');
    } catch (_) {
        this.showToast('تعذّر تحديث الصورة', 'error');
    }

    e.target.value = '';
},

onUsernameInput() {
const value = this.settingsEditUsername.trim();
if (this.usernameCheckTimer) clearTimeout(this.usernameCheckTimer);
if (!value || value === this.settingsAccount.username) { this.usernameCheckState = 'idle'; this.usernameCheckMessage = ''; return; }
if (value.length < 3) { this.usernameCheckState = 'unavailable'; this.usernameCheckMessage = 'الحد الأدنى 3 أحرف'; return; }
this.usernameCheckState = 'checking';
this.usernameCheckTimer = setTimeout(async () => {
try {
const url = this.settingsExtraRoutes.checkUsername + '?username=' + encodeURIComponent(value);
const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
const d = await res.json();
this.usernameCheckState = d.available ? 'available' : 'unavailable';
this.usernameCheckMessage = d.available ? '' : (d.reason || 'غير متاح');
} catch (_) { this.usernameCheckState = 'idle'; }
}, 450);
},

onPhoneInput() {
const value = this.settingsEditPhone.trim();
if (this.phoneCheckTimer) clearTimeout(this.phoneCheckTimer);
if (!value || value === this.settingsAccount.phone) { this.phoneCheckState = 'idle'; this.phoneCheckMessage = ''; return; }
if (value.length < 6) { this.phoneCheckState = 'unavailable'; this.phoneCheckMessage = 'رقم قصير جداً'; return; }
this.phoneCheckState = 'checking';
this.phoneCheckTimer = setTimeout(async () => {
try {
const url = this.settingsExtraRoutes.checkPhone + '?phone=' + encodeURIComponent(value);
const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
const d = await res.json();
this.phoneCheckState = d.available ? 'available' : 'unavailable';
this.phoneCheckMessage = d.available ? '' : (d.reason || 'غير متاح');
} catch (_) { this.phoneCheckState = 'idle'; }
}, 450);
},

async saveAccountInfo() {

const name = this.settingsEditName.trim();

if (!name) { this.showToast('الاسم مطلوب', 'error'); return; }

if (!this.settingsExtraRoutes || !this.settingsExtraRoutes.accountUpdate || this.settingsExtraRoutes.accountUpdate === '#') return;

try {

const res = await fetch(this.settingsExtraRoutes.accountUpdate, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
    body: JSON.stringify({
        name,
        username: this.settingsEditUsername.trim() || null,
        phone: this.settingsEditPhone.trim() || null,
        bio: this.settingsEditBio.trim() || null,
        birthday: this.settingsEditBirthday || null,
    }),
});

const data = await res.json().catch(() => ({}));

if (!res.ok || !data.success) {
    this.showToast(data.message || 'فشل حفظ المعلومات', 'error');
    return;
}

this.settingsAccount = { ...this.settingsAccount, ...data.data };
this.userName = this.settingsAccount.name;
this.showToast('تم حفظ المعلومات', 'success');
this.settingsEdit = false;

} catch (_) { this.showToast('فشل حفظ المعلومات', 'error'); }

},

async loadBlockedUsers() {
    if (!this.settingsExtraRoutes?.blockedList || this.settingsExtraRoutes.blockedList === '#') return;
    this.settingsBlockedLoading = true;
    try {
        const res = await fetch(this.settingsExtraRoutes.blockedList, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) this.settingsBlockedList = data.data || [];
    } catch (_) {}
    this.settingsBlockedLoading = false;
},

async blockUserById() {
    const id = parseInt(this.settingsBlockUserIdInput, 10);
    if (!id) { this.showToast('أدخل معرّف مستخدم صحيح', 'error'); return; }
    try {
        const res = await fetch(this.settingsExtraRoutes.blockedAdd, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ user_id: id }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) { this.showToast(data.message || 'فشل الحظر', 'error'); return; }
        this.settingsBlockUserIdInput = '';
        this.showToast('تم حظر المستخدم', 'success');
        this.loadBlockedUsers();
    } catch (_) { this.showToast('فشل الحظر', 'error'); }
},

async unblockUserById(userId) {
    try {
        const url = this.settingsExtraRoutes.blockedRemove.replace('__ID__', userId);
        const res = await fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsBlockedList = this.settingsBlockedList.filter(u => u.id !== userId);
            this.showToast('تم فك الحظر', 'success');
        }
    } catch (_) {}
},

async loadFrequentContacts() {
    if (!this.settingsExtraRoutes?.frequentContacts || this.settingsExtraRoutes.frequentContacts === '#') return;
    try {
        const res = await fetch(this.settingsExtraRoutes.frequentContacts, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) this.settingsFrequentContacts = data.data || [];
    } catch (_) {}
},

async loadActiveSessions() {
    if (!this.settingsExtraRoutes?.sessionsList || this.settingsExtraRoutes.sessionsList === '#') return;
    this.settingsSessionsLoading = true;
    try {
        const res = await fetch(this.settingsExtraRoutes.sessionsList, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) this.settingsSessionsList = data.data || [];
    } catch (_) {}
    this.settingsSessionsLoading = false;
},

async terminateSessionById(sessionId) {
    try {
        const url = this.settingsExtraRoutes.sessionsTerminate.replace('__ID__', sessionId);
        const res = await fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsSessionsList = this.settingsSessionsList.filter(s => s.id !== sessionId);
            this.showToast('تم إنهاء الجلسة', 'success');
        } else {
            this.showToast(data.message || 'تعذّر إنهاء الجلسة', 'error');
        }
    } catch (_) {}
},

async request2FACode() {
    try {
        const res = await fetch(this.settingsExtraRoutes.twoFaRequest, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settings2FAStep = 'code-sent';
            this.showToast('تم إرسال رمز التحقق إلى بريدك', 'success');
        } else {
            this.showToast(data.message || 'فشل إرسال الرمز', 'error');
        }
    } catch (_) { this.showToast('فشل إرسال الرمز', 'error'); }
},

async confirm2FACode() {
    if (!this.settings2FACode.trim()) { this.showToast('أدخل رمز التحقق', 'error'); return; }
    try {
        const res = await fetch(this.settingsExtraRoutes.twoFaConfirm, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ otp: this.settings2FACode.trim() }),
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsSecurity.twoFaEnabled = true;
            this.settings2FAStep = 'idle';
            this.settings2FACode = '';
            this.showToast('تم تفعيل التحقق بخطوتين', 'success');
        } else {
            this.showToast(data.message || 'رمز غير صحيح', 'error');
        }
    } catch (_) { this.showToast('رمز غير صحيح', 'error'); }
},

async disable2FAConfirm() {
    if (!this.settings2FADisablePassword) { this.showToast('أدخل كلمة المرور', 'error'); return; }
    try {
        const res = await fetch(this.settingsExtraRoutes.twoFaDisable, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ password: this.settings2FADisablePassword }),
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsSecurity.twoFaEnabled = false;
            this.settings2FADisablePassword = '';
            this.showToast('تم تعطيل التحقق بخطوتين', 'success');
        } else {
            this.showToast(data.message || 'كلمة المرور غير صحيحة', 'error');
        }
    } catch (_) { this.showToast('فشل التعطيل', 'error'); }
},

async loadSettingsFolders() {
    if (!this.settingsExtraRoutes?.foldersList || this.settingsExtraRoutes.foldersList === '#') return;
    this.settingsFoldersLoading = true;
    try {
        const res = await fetch(this.settingsExtraRoutes.foldersList, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) this.settingsFoldersList = data.data || [];
    } catch (_) {}
    this.settingsFoldersLoading = false;
},

async saveSettingsFolder() {
    const name = this.settingsFolderDraft2.name.trim();
    if (!name) { this.showToast('ادخل اسم المجلد', 'error'); return; }
    try {
        const res = await fetch(this.settingsExtraRoutes.foldersSave, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ id: this.settingsFolderDraft2.id, name, icon: this.settingsFolderDraft2.icon }),
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsFolderDraft2 = { id: null, name: '', icon: 'ri-folder-3-line' };
            this.showToast('تم حفظ المجلد', 'success');
            this.loadSettingsFolders();
        } else {
            this.showToast(data.message || 'فشل حفظ المجلد', 'error');
        }
    } catch (_) { this.showToast('فشل حفظ المجلد', 'error'); }
},

getContactFolderTag(contactId) {
    const id = Number(contactId);
    const folder = (this.settingsFoldersList || []).find(f => (f.include_ids || []).includes(id));
    return folder ? folder.name : null;
},

toggleFolderChatPicker(folder) {
    this.folderChatPickerId = this.folderChatPickerId === folder.id ? null : folder.id;
},

async toggleFolderChatInclude(folder, contactId) {
    const id = Number(contactId);
    const current = Array.isArray(folder.include_ids) ? [...folder.include_ids] : [];
    const idx = current.indexOf(id);
    if (idx === -1) current.push(id); else current.splice(idx, 1);
    folder.include_ids = current;

    try {
        await fetch(this.settingsExtraRoutes.foldersSave, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ id: folder.id, name: folder.name, icon: folder.icon, include_ids: current }),
        });
    } catch (_) {}
},

async deleteSettingsFolder(folderId) {
    try {
        const url = this.settingsExtraRoutes.foldersDelete.replace('__ID__', folderId);
        const res = await fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            this.settingsFoldersList = this.settingsFoldersList.filter(f => f.id !== folderId);
            this.showToast('تم حذف المجلد', 'success');
        }
    } catch (_) {}
},

async changeSettingsLanguage(locale) {
    this.settingsLanguageChoice = locale;
    try {
        await fetch(this.settingsExtraRoutes.localeUpdate, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ locale }),
        });
        this.showToast('تم حفظ تفضيل اللغة', 'success');
    } catch (_) {}
},

revealSensitiveMessage(messageId) {
    this.revealedSensitiveIds.add(messageId);
},

async requestDesktopNotifications() {
    if (!('Notification' in window)) {
        this.showToast('متصفحك لا يدعم إشعارات سطح المكتب', 'error');
        return;
    }
    const permission = await Notification.requestPermission();
    this.settingsNotifications.desktopEnabled = permission === 'granted';
    this.settingNotifyEnabled = this.settingsNotifications.desktopEnabled;
    if (permission !== 'granted') this.showToast('تم رفض إذن الإشعارات', 'error');
    this.saveMessagingSettings();
},

maybeShowDesktopNotification(contact, msg) {
    if (!this.settingsNotifications.desktopEnabled) return;
    if (typeof document !== 'undefined' && document.hasFocus()) return;
    if (!('Notification' in window) || Notification.permission !== 'granted') return;
    const body = this.settingsNotifications.previewEnabled ? (msg.content || 'مرفق جديد') : 'رسالة جديدة';
    try {
        new Notification(contact?.name || 'رسالة جديدة', { body, icon: contact?.avatar_url || undefined });
    } catch (_) {}
},

maybeShowCallNotification(contact, type) {
    if (typeof document !== 'undefined' && document.hasFocus()) return;
    if (!('Notification' in window) || Notification.permission !== 'granted') return;
    try {
        const n = new Notification(contact?.name || 'مكالمة واردة', {
            body: type === 'video' ? 'مكالمة فيديو واردة' : 'مكالمة صوتية واردة',
            icon: contact?.avatar_url || undefined,
            tag: 'incoming-call',
            requireInteraction: true,
        });
        n.onclick = () => { window.focus(); n.close(); };
    } catch (_) {}
},

updateGlobalVolume(val) {
    this.settingsNotifications.volume = Number(val);
    localStorage.setItem('messaging_tone_volume', JSON.stringify(Number(val)));
    this.saveMessagingSettings();
},

updateDocumentTitle() {
    if (!this.settingsChats.showUnreadInTitle || !this.totalUnreadCount) {
        document.title = this.baseDocumentTitle;
        return;
    }
    document.title = `(${this.totalUnreadCount}) ${this.baseDocumentTitle}`;
},

async exportMyData() {
    try {
        const res = await fetch(this.settingsGetRoute.replace('/settings', '/export'), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken } });
        if (!res.ok) throw new Error('export_failed');
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'my-messages.json';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
        this.showToast('تم تصدير بياناتك', 'success');
    } catch (_) {
        this.showToast('تعذّر تصدير البيانات', 'error');
    }
},

clearLocalCache() {
    const keysToKeep = ['app-theme', 'theme'];
    Object.keys(localStorage)
        .filter(k => k.startsWith('messaging_') || k.startsWith('conv_'))
        .filter(k => !keysToKeep.includes(k))
        .forEach(k => localStorage.removeItem(k));
    this.showToast('تم مسح الكاش المحلي', 'success');
},

toggleSendWithEnter() {
    this.settingsChats.sendWithEnter = !this.settingsChats.sendWithEnter;
    this.saveMessagingSettings();
},

applyReduceMotion(on) {
    document.documentElement.classList.toggle('reduce-motion', !!on);
},

toggleReduceMotion() {
    this.settingsChats.reduceMotion = !this.settingsChats.reduceMotion;
    this.applyReduceMotion(this.settingsChats.reduceMotion);
    this.saveMessagingSettings();
},

applyChatFont() {
    const fonts = {
        default: "'Tajawal', sans-serif",
        serif: "Georgia, 'Times New Roman', serif",
        mono: "'Courier New', monospace",
        rounded: "'Comic Sans MS', 'Segoe UI', sans-serif",
    };
    document.documentElement.style.setProperty('--chat-font', fonts[this.settingsChats.fontFamily] || fonts.default);
},

toggleAutoNightMode() {
    this.settingsChats.autoNightMode = !this.settingsChats.autoNightMode;
    if (this.settingsChats.autoNightMode) {
        if (!this.darkModeMediaQuery) {
            this.darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            this.darkModeMediaQuery.addEventListener('change', this.handleSystemThemeChange);
        }
        this.handleSystemThemeChange(this.darkModeMediaQuery);
    } else if (this.darkModeMediaQuery) {
        this.darkModeMediaQuery.removeEventListener('change', this.handleSystemThemeChange);
        this.darkModeMediaQuery = null;
    }
    this.saveMessagingSettings();
},

handleSystemThemeChange(e) {
    const wantsDark = e.matches;
    const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
    if (wantsDark !== isDark) this.toggleDark();
},

async loadMicDevices() {
    try {
        await navigator.mediaDevices.getUserMedia({ audio: true }).then(s => s.getTracks().forEach(t => t.stop()));
        const devices = await navigator.mediaDevices.enumerateDevices();
        this.settingsMicDevices = devices.filter(d => d.kind === 'audioinput');
        const saved = localStorage.getItem('messaging_mic_device_id');
        if (saved && this.settingsMicDevices.some(d => d.deviceId === saved)) {
            this.settingsMicDeviceId = saved;
        } else if (this.settingsMicDevices.length) {
            this.settingsMicDeviceId = this.settingsMicDevices[0].deviceId;
        }
    } catch (_) {
        this.showToast('تعذّر الوصول إلى الميكروفون', 'error');
    }
},

selectMicDevice(deviceId) {
    this.settingsMicDeviceId = deviceId;
    localStorage.setItem('messaging_mic_device_id', deviceId);
    this.showToast('تم اختيار الميكروفون', 'success');
},

async startMicTest() {
    try {
        const constraint = this.settingsMicDeviceId ? { audio: { deviceId: { ideal: this.settingsMicDeviceId } } } : { audio: true };
        const stream = await navigator.mediaDevices.getUserMedia(constraint);
        this._micTestStream = stream;
        this.micTestActive = true;
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const analyser = ctx.createAnalyser();
        analyser.fftSize = 256;
        ctx.createMediaStreamSource(stream).connect(analyser);
        const data = new Uint8Array(analyser.frequencyBinCount);
        const N = 28;
        const self = this;
        const tick = () => {
            if (!self.micTestActive) return;
            analyser.getByteTimeDomainData(data);
            const step = Math.floor(data.length / N);
            for (let i = 0; i < N; i++) {
                const val = Math.abs(data[i * step] - 128) / 128;
                const h = Math.max(3, Math.round(val * 44));
                const el = self.$refs['mb' + (i + 1)];
                const bar = Array.isArray(el) ? el[0] : el;
                if (bar) bar.style.height = h + 'px';
            }
            self._micTestRaf = requestAnimationFrame(tick);
        };
        this._micTestCtx = ctx;
        this._micTestRaf = requestAnimationFrame(tick);
    } catch (_) {
        this.showToast('تعذّر الوصول إلى الميكروفون', 'error');
    }
},

stopMicTest() {
    this.micTestActive = false;
    if (this._micTestRaf) { cancelAnimationFrame(this._micTestRaf); this._micTestRaf = null; }
    if (this._micTestStream) { this._micTestStream.getTracks().forEach(t => t.stop()); this._micTestStream = null; }
    if (this._micTestCtx) { this._micTestCtx.close().catch(() => {}); this._micTestCtx = null; }
},

savePIN() {

if (this.settingsSecurity.pin.length < 4) { this.showToast('PIN يجب أن يكون 4 أرقام على الأقل', 'error'); return; }

if (this.settingsSecurity.pin !== this.settingsSecurity.pinConfirm) { this.showToast('PIN غير متطابق', 'error'); return; }

this.settingsSecurity.pinEnabled = true;

this.showToast('تم تفعيل القفل بنجاح', 'success');

this.settingsSecurity.pin = ''; this.settingsSecurity.pinConfirm = '';

this.saveMessagingSettings();

},

toggleVePlayback() {
const v = this.$refs.vePreview;
if (!v) return;
if (v.paused) { v.play(); this.veIsPlaying = true; }
else { v.pause(); this.veIsPlaying = false; }
},
onVeTimeUpdate(e) {
this.veCurrentTime = e.target.currentTime;
},
startVeHandleDrag(handle, e) {
e.preventDefault();
this.veDragHandle = handle;
this.veDragStartX = e.clientX;
this.veDragStartVal = handle === 'start' ? this.videoEditorStart : this.videoEditorEnd;
const onMove = (ev) => {
    if (!this.$refs.veTimeline) return;
    const rect = this.$refs.veTimeline.getBoundingClientRect();
    const dx = ev.clientX - rect.left;
    const ratio = Math.max(0, Math.min(1, dx / rect.width));
    const t = ratio * this.videoEditorDuration;
    if (this.veDragHandle === 'start') {
        this.videoEditorStart = Math.max(0, Math.min(t, this.videoEditorEnd - 0.5));
        if (this.$refs.vePreview) this.$refs.vePreview.currentTime = this.videoEditorStart;
    } else {
        this.videoEditorEnd = Math.min(this.videoEditorDuration, Math.max(t, this.videoEditorStart + 0.5));
    }
};
const onUp = () => {
    this.veDragHandle = null;
    document.removeEventListener('pointermove', onMove);
    document.removeEventListener('pointerup', onUp);
};
document.addEventListener('pointermove', onMove);
document.addEventListener('pointerup', onUp);
},
veTimelineClick(e) {
if (this.veDragHandle) return;
if (!this.$refs.veTimeline) return;
const rect = this.$refs.veTimeline.getBoundingClientRect();
const ratio = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
const t = ratio * this.videoEditorDuration;
if (this.$refs.vePreview) {
    this.$refs.vePreview.currentTime = t;
    this.veCurrentTime = t;
}
},

},

mounted() {

this.init();

this.loadCallSettings();
this.initCallSignaling();

this.initE2E();

this.loadSavedMessages();

this.loadMessagingSettings();

this.loadSettingsFolders();

this.resizeHandler = () => {

this.showSidebar = window.innerWidth > 1080;

this.isDesktop = window.innerWidth > 1080;

};

window.addEventListener('resize', this.resizeHandler);

document.documentElement.style.setProperty('--sidebar-width', `${this.sidebarWidth}px`);

const feed = this.$refs.messagesContainer;

if (feed) feed.style.background = this.chatBackground || '';

// Start SSE instead of delta polling

if (this.selectedContact) {

this.startSSE();

this.scheduleDeltaRefresh();

}

// Activity ping: lightweight and presence-friendly

this.pingTimer = setInterval(() => {

fetch('/activity/ping', {

method: 'POST',

headers: { 'X-CSRF-TOKEN': this.csrfToken },

keepalive: true,

}).catch(() => {});

}, 12000);

// Resume SSE when tab becomes visible again

document.addEventListener('visibilitychange', () => {

if (!document.hidden && this.selectedContact && !this.refreshTimer) {

this.startSSE();

this.scheduleDeltaRefresh();

}

});

// Esc key handler: close any open modal or panel.

this.escHandler = (e) => {

if (document.fullscreenElement || document.webkitFullscreenElement) return;

if (e.key === 'Escape') {
if (this.showEmojiPicker)       { this.showEmojiPicker = false; this.emojiPickerPinned = false; return; }
if (this.stickerViewer)         { this.closeStickerViewer(); return; }
if (this.mediaModal)            { this.closeMediaModal(); return; }
if (this.deleteTargetMessage)   { this.deleteTargetMessage = null; return; }
if (this.editTargetMessage)     { this.closeEditModal(); return; }
// Calls sub-modals
if (this.callsSettingsOpen)     { this.callsSettingsOpen = false; return; }
if (this.callsDeleteConfirm)    { this.callsDeleteConfirm = false; return; }
if (this.newCallModalOpen)      { this.closeNewCallModal(); return; }
if (this.callsMenuOpen)         { this.callsMenuOpen = false; return; }
if (this.callsOpen)             { this.closeCalls(); return; }
// My Profile sub-modals
if (this.myProfileEditOpen)     { this.myProfileEditOpen = false; return; }
if (this.myProfileViewersOpen)  { this.myProfileViewersOpen = false; return; }
if (this.myProfileStatusViewerOpen) { this.myProfileStatusViewerOpen = false; return; }
// My Profile
if (this.myProfileOpen)         { this.myProfileOpen = false; return; }
// Profile media sub-modals
if (this.profileMediaMenuOpen)  { this.profileMediaMenuOpen = false; return; }
if (this.profileMediaCalendarFullOpen) { this.profileMediaCalendarFullOpen = false; return; }
if (this.profileMediaCalendarOpen) { this.profileMediaCalendarOpen = false; return; }
if (this.profileMediaModalOpen) { this.profileMediaModalOpen = false; this.voicePlayerExternalSource = null; return; }
if (this.qrModalOpen)          { this.qrModalOpen = false; return; }
// Profile sub-modals (layer by layer)
if (this.profileShareChatOpen)  { this.profileShareChatOpen = false; return; }
if (this.profileNicknameEdit)   { this.profileNicknameEdit = false; return; }
if (this.profileDeleteConfirm)  { this.profileDeleteConfirm = false; return; }
if (this.profileBlockConfirm)   { this.profileBlockConfirm = false; return; }
if (this.profileAddFolderOpen)  { this.profileAddFolderOpen = false; return; }
if (this.profileExportOpen)     { this.profileExportOpen = false; return; }
if (this.profileAutoDeleteOpen) { this.profileAutoDeleteOpen = false; return; }
if (this.profileMoreOpen)       { this.profileMoreOpen = false; return; }
if (this.profileModalContact)   { this.closeProfile(); return; }
if (this.statusViewersOpen)     { this.closeStatusViewers(); return; }
if (this.searchPanelOpen)       { this.closeSearchPanel(); return; }
if (this.settingsPanelOpen)     { this.backFromSettingsOrClose(); return; }
if (this.newConversationModal)  { this.closeNewConversationModal(); return; }
if (this.foldersManagerOpen)    { this.closeFoldersManager(); return; }
if (this.wallpaperPickerOpen)   { this.wallpaperPickerOpen = false; return; }
if (this.accountDrawerOpen)     { this.accountDrawerOpen = false; return; }
if (this.messageContextOpen)    { this.messageContextOpen = false; return; }
if (this.callState)             { this.endCall(); return; }
if (this.deleteStatusTarget)    { this.deleteStatusTarget = null; if (this.statusPaused) this.toggleStatusPlayback(); return; }
if (this.statusEditorOpen)      { this.closeStatusEditor(); return; }
if (this.statusViewerOpen)      { this.closeStatusViewer(); return; }
if (this.videoEditorOpen)       { this.closeVideoEditor(); return; }
if (this.settingsSection)       { this.settingsSection = null; return; }
return;
}

if (e.key === 'ArrowRight' && this.mediaModal) {
e.preventDefault(); this.mediaModalPrev(); return;
}

if (e.key === 'ArrowLeft' && this.mediaModal) {
e.preventDefault(); this.mediaModalNext(); return;
}

if (e.key === 'ArrowRight' && this.statusViewerOpen) {
e.preventDefault(); this.prevStatus(); return;
}

if (e.key === 'ArrowLeft' && this.statusViewerOpen) {
e.preventDefault();
const statuses = this.statusViewerContact ? this.statusViewerContact.statuses : [];
if (this.statusViewerIndex < statuses.length - 1) this.nextStatus();
return;
}

};

document.addEventListener('keydown', this.escHandler);

document.addEventListener('fullscreenchange', () => {
    const fs = !!(document.fullscreenElement || document.webkitFullscreenElement);
    document.body.classList.toggle('is-fullscreen', fs);
});
document.addEventListener('webkitfullscreenchange', () => {
    document.body.classList.toggle('is-fullscreen', !!document.webkitFullscreenElement);
});

// Setup Intersection Observer for lazy media loading.

this.mediaObserver = new IntersectionObserver((entries) => {

entries.forEach(entry => {

if (!entry.isIntersecting) return;

const el = entry.target;

const lazySrc = el.dataset.lazySrc;

if (lazySrc) {

el.src = lazySrc;

delete el.dataset.lazySrc;

this.mediaObserver.unobserve(el);

}

});

}, { rootMargin: '200px 0px', threshold: 0.01 });

this.globalClickHandler = (event) => {

const t = event.target;

if (!t.closest('.row')) this.toolsMessageId = null;

if (!t.closest('.h-actions')) this.headerMenuOpen = false;

if (!t.closest('.message-context')) this.messageContextOpen = false;

if (!t.closest('.account-drawer') && !t.closest('.nav-rail .rail-btn')) this.accountDrawerOpen = false;

if (!t.closest('.reaction-picker') && !t.closest('.reaction-add-btn')) this.reactionPickerMessageId = null;

if (!t.closest('.media-modal-more')) this.mediaModalMoreOpen = false;

if (!t.closest('.pinned-banner') && !t.closest('.pinned-list-dropdown')) this.pinnedListOpen = false;

if (this.emojiPickerPinned && !t.closest('.picker-panel') && !t.closest('.ibtn[title]')) {
this.showEmojiPicker = false;
this.emojiPickerPinned = false;
}

};

document.addEventListener('click', this.globalClickHandler, true);

// Load statuses on init.

this.loadStatuses();

// Watch data-theme changes to reapply per-chat theme vars.

this.themeObserver = new MutationObserver(() => { this._isDark = document.documentElement.getAttribute('data-theme') !== 'light'; this.$nextTick(() => this.applyChatThemeVars()); });

this.themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

},
unmounted() {
this.saveCurrentScrollPosition();

if (this.resizeHandler) window.removeEventListener('resize', this.resizeHandler);

if (this.refreshTimer) { clearTimeout(this.refreshTimer); this.refreshTimer = null; }

if (this.pingTimer) clearInterval(this.pingTimer);

if (this.highlightTimer) clearTimeout(this.highlightTimer);

if (this.floatingDayTimer) clearTimeout(this.floatingDayTimer);

this.stopSSE();

if (this.scrollEndTimer) clearTimeout(this.scrollEndTimer);

this.stopAndResetAllAudioPlayers();

this.stopActiveVideo();

this.resetRecordingState();

if (this.globalClickHandler) document.removeEventListener('click', this.globalClickHandler, true);
if (this.escHandler) document.removeEventListener('keydown', this.escHandler);
if (this.mediaObserver) this.mediaObserver.disconnect();
if (this.themeObserver) this.themeObserver.disconnect();
},



}).mount('#app');

</script>

@include('components.account-theme-foot')

</body>

</html>



