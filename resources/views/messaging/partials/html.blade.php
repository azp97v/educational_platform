
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
<link rel="apple-touch-icon" href="{{ asset('images/logo/logo.png') }}">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#1a3c6e">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="إجلال">

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

<link rel="stylesheet" href="/css/messaging-app.css?v={{ filemtime(public_path('css/messaging-app.css')) }}">

<!-- Sentry Browser SDK -->
<script
    src="https://js-de.sentry-cdn.com/021ff6ade06b8bf73a6467b845f06dbc.min.js"
    crossorigin="anonymous"
></script>
<script>
if (typeof Sentry !== 'undefined') Sentry.onLoad(function() {
    Sentry.init({
        dsn: "https://021ff6ade06b8bf73a6467b845f06dbc@o4511728095199232.ingest.de.sentry.io/4511728109224016",
        environment: "{{ app()->environment() }}",
        integrations: [
            Sentry.browserTracingIntegration(),
            Sentry.replayIntegration({ maskAllText: false, blockAllMedia: false }),
        ],
        tracesSampleRate: 0.2,
        tracePropagationTargets: ["edu.ejlalmakkah.org.sa"],
        replaysSessionSampleRate: 0.05,
        replaysOnErrorSampleRate: 1.0,
    });
    Sentry.setUser({ id: {{ auth()->id() }}, role: "{{ auth()->user()->role }}" });
});
</script>

</head>

<body>

<div id="app" v-cloak>

<div id="app-inner" style="display:contents;">

<div class="layout">

<div v-if="showSidebar && !isDesktop" class="sidebar-mobile-backdrop" @click="showSidebar = false" style="position:fixed;inset:0;z-index:99;background:rgba(0,0,0,.45);backdrop-filter:blur(2px);"></div>

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

<i v-else-if="contact.isGroup" class="ri-group-fill" style="font-size:20px;color:var(--gold);"></i>

<span v-else>@{{ getAuthorInitial(contact.name) }}</span>

</div>

<i v-if="mutedUntilByContact[String(contact.id)] > Date.now()" class="ri-volume-mute-line" style="position:absolute;top:0;right:0;font-size:12px;color:var(--gold);background:rgba(0,0,0,.65);border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;z-index:5;backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);border:2px solid var(--panel);"></i>

</div>

<div class="c-body">

<div class="c-name"><i v-if="contact.isGroup" class="ri-group-line" style="font-size:12px;color:var(--muted);margin-left:3px;"></i>@{{ contact.name }} <span v-if="settingsChats.showFolderTags && getContactFolderTag(contact.id)" class="folder-tag-badge">@{{ getContactFolderTag(contact.id) }}</span> <span v-if="blockedByContact[String(contact.id)]" class="contact-blocked-badge">محظور @{{ formatBlockedTime(blockedByContact[String(contact.id)]) }}</span></div>

<div class="c-prev" v-if="contact.isGroup && !contact.lastMessage" style="color:var(--muted);font-size:11px;"><i class="ri-group-line"></i> @{{ (contact._membersCount || 0) + ' عضو' }}</div>

<div class="c-prev" v-else-if="!contact.isTyping"><svg class="contact-status-reply-indicator" v-if="contact.lastMessageStatusRefId" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="12" r="3.5" stroke="currentColor" stroke-width="1.8"/><circle cx="8" cy="12" r="1.5" fill="currentColor"/><path d="M14 8.5L18 12L14 15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 12H11.5C9.57 12 8 10.43 8 8.5V7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>@{{ sanitizeDisplayText(contact.lastMessage, t.noMessages) }}</div>
<div class="c-prev c-typing-preview" v-else><span class="c-typing-label">@{{ getTypingLabel(contact, true) }}</span><span class="c-typing-dots"><span></span><span></span><span></span></span></div>

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
<i v-else-if="selectedContact.isGroup" class="ri-group-fill" style="font-size:22px;color:var(--gold);"></i>
<span v-else>@{{ getAuthorInitial(selectedContact.name) }}</span>
</template>

</div>

<div>

<h3 style="cursor:pointer;" @click="selectedContact.isGroup ? openGroupInfo() : (Number(selectedContact.id) !== -1 ? openProfile(selectedContact) : null)">@{{ selectedContact.name }}</h3>

<div class="h-status">

<span v-if="Number(selectedContact.id) === -1" style="font-size:12px;color:var(--muted);">مساحة آمنة — احفظ ما تشاء</span>

<template v-else-if="selectedContact.isGroup">
<span style="font-size:12px;color:var(--muted);cursor:pointer;" @click="openGroupInfo()"><i class="ri-group-line" style="margin-left:3px;"></i>@{{ (selectedContact._membersCount || 0) + ' عضو' }}</span>
</template>

<template v-else>
<span class="dot" :class="{ offline: !selectedContact.isOnline }"></span>

<span>@{{ selectedContact.isTyping ? getTypingLabel(selectedContact) : (selectedContact.isOnline ? t.onlineNow : (t.lastSeen + ' ' + formatLastSeen(selectedContact))) }}</span>
</template>

</div>

</div>

</div>

<div class="h-actions">

<button v-if="Number(selectedContact.id) === -1" class="h-icon-btn" @click="openSavedMedia" title="الوسائط"><i class="ri-image-2-line"></i></button>

<template v-if="Number(selectedContact.id) !== -1">
<button class="h-icon-btn" @click="openSearchPanel" title=""><i class="ri-search-line"></i></button>

<button v-if="!selectedContact.isGroup" class="h-icon-btn" @click="startCall('voice')" title=""><i class="ri-phone-line"></i></button>

<button v-if="!selectedContact.isGroup" class="h-icon-btn" @click="startCall('video')" title=""><i class="ri-vidicon-line"></i></button>

<button v-if="selectedContact.isGroup" class="h-icon-btn" @click="startGroupCall('voice')" title="مكالمة جماعية صوتية"><i class="ri-phone-line"></i></button>

<button v-if="selectedContact.isGroup" class="h-icon-btn" @click="startGroupCall('video')" title="مكالمة جماعية مرئية"><i class="ri-vidicon-line"></i></button>

<button class="h-icon-btn" @click="selectedContact.isGroup ? openGroupInfo() : openProfile(selectedContact)" title=""><i class="ri-information-line"></i></button>
</template>

<button class="h-icon-btn" @click.stop="toggleHeaderMenu" title=""><i class="ri-more-2-fill"></i></button>

<div class="chat-menu" v-if="headerMenuOpen" @click.stop>

<button @mouseenter="headerSub='mute'" @mouseleave="headerSub=null"><i class="ri-volume-mute-line"></i> كتم المحادثة <i class="ri-arrow-left-s-line" style="margin-right:auto"></i></button>

<button v-if="!selectedContact.isGroup" @click="openProfile(selectedContact)"><i class="ri-user-line"></i> ملف المستخدم</button>

<button v-if="selectedContact && selectedContact.isGroup" @click="openGroupInfo()"><i class="ri-group-line"></i> معلومات المجموعة</button>

<button v-if="selectedContact && selectedContact.isGroup" @click="headerMenuOpen=false; startGroupCall('voice')"><i class="ri-phone-line"></i> مكالمة جماعية صوتية</button>

<button v-if="selectedContact && selectedContact.isGroup" @click="headerMenuOpen=false; startGroupCall('video')"><i class="ri-vidicon-line"></i> مكالمة جماعية مرئية</button>

<button v-if="selectedContact && selectedContact.isGroup && selectedContact._isAdmin" @click="headerMenuOpen=false; openGroupSettings()"><i class="ri-settings-3-line"></i> إعدادات المجموعة</button>

<button @click="openWallpaperPicker"><i class="ri-palette-line"></i> خلفية المحادثة</button>

<button @click="openMediaGallery"><i class="ri-image-2-line"></i> وسائط المحادثة</button>

<button @click="openSavedMessages"><i class="ri-bookmark-line"></i> الرسائل المحفوظة</button>

<button v-if="!selectedContact.isGroup" @click="clearChatHistory"><i class="ri-eraser-line"></i> مسح المحادثة</button>

<button v-if="!selectedContact.isGroup" class="danger"><i class="ri-delete-bin-6-line"></i> حذف المحادثة</button>

<button v-if="selectedContact.isGroup" class="danger" @click="headerMenuOpen=false; leaveGroup()"><i class="ri-logout-box-line"></i> مغادرة المجموعة</button>

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
<span class="pinned-list-text">@{{ isStillEncrypted(msg.content) ? '🔒 رسالة محمية' : (msg.content || (msg.messageType === 'image' ? 'صورة' : msg.messageType === 'video' ? 'فيديو' : msg.messageType === 'audio' ? 'رسالة صوتية' : 'مرفق')) }}</span>
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

<button v-if="Number(message.senderId) === Number(currentUserId) && !['audio','video','sticker_static','sticker_animated','gif','call'].includes(message.messageType)" @click="editMessage(message)" :title="t.edit"><i class="ri-edit-2-line"></i></button>

<button v-if="Number(message.senderId) === Number(currentUserId)" class="d" @click="deleteMessage(message)" :title="t.delete"><i class="ri-delete-bin-6-line"></i></button>

</div>

<div v-if="message.isGroup && Number(message.senderId) !== Number(currentUserId) && message.senderName" style="font-size:11px;font-weight:600;color:var(--accent,var(--gold));margin-bottom:2px;padding-bottom:1px;">@{{ message.senderName }}</div>

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

<div class="txt" v-if="message.content && message.messageType === 'text'" :class="{ 'txt--encrypted-fallback': isStillEncrypted(message.content) }">
  <template v-if="isStillEncrypted(message.content)"><i class="ri-lock-line" style="font-size:13px;opacity:.55;margin-left:4px;"></i><span style="opacity:.55;font-size:13px;">رسالة محمية</span></template>
  <template v-else>@{{ message.content }}</template>
</div>

<div class="sticker-bubble" v-if="(message.messageType === 'sticker_static' || message.messageType === 'sticker_animated') && !brokenMediaByMessageId[message.id]" @click="openStickerViewer(message)">
<img v-if="message.messageType === 'sticker_static'" :src="message.attachmentUrl" :alt="message.attachmentName" class="sticker-bubble-media" v-on:error="markMediaAsBroken(message)">
<video v-else :src="message.attachmentUrl" class="sticker-bubble-media" autoplay loop muted playsinline></video>
</div>

<div class="media gif-bubble" v-if="message.messageType === 'gif'" @click="!brokenMediaByMessageId[message.id] && openMediaModal(message)">
<template v-if="!brokenMediaByMessageId[message.id]">
<div class="media-skeleton" v-if="!message.mediaLoaded"></div>
<img :src="message.attachmentUrl" :alt="message.attachmentName" :class="{ 'media-loaded': message.mediaLoaded }" @load="onMediaLoaded(message)" v-on:error="markMediaAsBroken(message); onMediaLoaded(message)">
</template>
<div v-else style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:160px;height:120px;border-radius:10px;background:var(--panel-2);color:var(--muted);font-size:12px;gap:6px;"><i class="ri-image-off-line" style="font-size:28px;opacity:.5;"></i>ملف غير متاح</div>
</div>

<div class="media" v-if="message.messageType === 'image'" @click="!brokenMediaByMessageId[message.id] && (message.isSensitive && !revealedSensitiveIds.has(message.id) ? revealSensitiveMessage(message.id) : openMediaModal(message))">

<template v-if="!brokenMediaByMessageId[message.id]">
<div class="media-skeleton" v-if="!message.mediaLoaded"></div>

<img :src="message.attachmentUrl" :alt="message.attachmentName"

:class="{ 'media-loaded': message.mediaLoaded, 'sensitive-blur': message.isSensitive && !revealedSensitiveIds.has(message.id) }"

@load="onMediaLoaded(message)"

v-on:error="markMediaAsBroken(message); onMediaLoaded(message)">

<div class="sensitive-overlay" v-if="message.isSensitive && !revealedSensitiveIds.has(message.id)"><i class="ri-eye-off-line"></i><span>محتوى حساس — اضغط للإظهار</span></div>
</template>
<div v-else style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:120px;min-height:80px;border-radius:10px;background:var(--panel-2);color:var(--muted);font-size:12px;gap:6px;padding:16px;"><i class="ri-image-off-line" style="font-size:28px;opacity:.5;"></i>صورة غير متاحة</div>

</div>

<div class="media" v-if="message.messageType === 'video'" @click="!brokenMediaByMessageId[message.id] && (message.isSensitive && !revealedSensitiveIds.has(message.id) ? revealSensitiveMessage(message.id) : openMediaModal(message))">

<template v-if="!brokenMediaByMessageId[message.id]">
<div class="media-skeleton" v-if="!message.mediaLoaded"></div>

<video :src="message.attachmentUrl" preload="metadata" muted

:class="{ 'media-loaded': message.mediaLoaded, 'sensitive-blur': message.isSensitive && !revealedSensitiveIds.has(message.id) }"

@loadedmetadata="onMediaLoaded(message)"

v-on:error="markMediaAsBroken(message); onMediaLoaded(message)"></video>

<div class="sensitive-overlay" v-if="message.isSensitive && !revealedSensitiveIds.has(message.id)"><i class="ri-eye-off-line"></i><span>محتوى حساس — اضغط للإظهار</span></div>
</template>
<div v-else style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:120px;min-height:80px;border-radius:10px;background:var(--panel-2);color:var(--muted);font-size:12px;gap:6px;padding:16px;"><i class="ri-vidicon-off-line" style="font-size:28px;opacity:.5;"></i>فيديو غير متاح</div>

</div>

<div class="media-caption" v-if="(message.messageType === 'image' || message.messageType === 'video') && message.content && message.content !== (message.attachmentName || '')" style="font-size:13.5px;color:var(--text);white-space:pre-wrap;word-break:break-word;padding:4px 2px 2px;">@{{ message.content }}</div>

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

<!-- Typing indicator bubble at bottom of chat -->
<transition name="typing-slide">
<div class="chat-typing-bubble" v-if="selectedContact && selectedContact.isTyping && Number(selectedContact.id) !== -1">
<div class="avatar ctb-avatar" style="width:28px;height:28px;flex-shrink:0;">
<img v-if="selectedContact.avatar_url" :src="selectedContact.avatar_url" v-on:error="handleAvatarError($event, selectedContact)">
<span v-else>@{{ getAuthorInitial(selectedContact.name) }}</span>
</div>
<div class="ctb-content">
<div class="ctb-label">@{{ getTypingLabel(selectedContact) }}</div>
<div class="ctb-dots"><span></span><span></span><span></span></div>
</div>
</div>
</transition>

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

<button @click="clearAllAttachments"><i class="ri-delete-bin-6-line"></i></button>

</div>

<div style="display:flex;flex-wrap:wrap;gap:7px;">

<div v-for="(att, i) in pendingAttachments" :key="i"

style="min-width:110px;max-width:220px;border:1px solid var(--soft-2);border-radius:10px;padding:7px;">

<div style="display:flex;justify-content:space-between;gap:4px;align-items:center;">

<span style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;">@{{ att.name }}</span>

<button v-if="att.editable && !att.uploading" @click="att.file.type.startsWith('video/') ? openVideoEditor(att.file) : openImageEditor(att.file)" style="border:none;background:transparent;color:var(--gold);cursor:pointer;font-size:14px;" title="تعديل"><i class="ri-edit-line"></i></button>

<button @click="removeAttachment(i)" style="border:none;background:transparent;color:#ff9aa5;cursor:pointer;"><i class="ri-close-circle-line"></i></button>

</div>

<div style="margin-top:6px;font-size:11px;color:var(--muted);">@{{ getPendingAttachmentLabel(att) }}</div>

<img v-if="att.previewType === 'image' && att.previewUrl" :src="att.previewUrl" alt="" style="margin-top:6px;width:100%;max-height:90px;object-fit:cover;border-radius:8px;cursor:pointer;" @click="openAttachmentPreview(att)" title="معاينة الصورة">

<video v-else-if="att.previewType === 'video' && att.previewUrl" :src="att.previewUrl" preload="metadata" muted style="margin-top:6px;width:100%;max-height:90px;object-fit:cover;border-radius:8px;background:color-mix(in srgb, var(--panel-2) 88%, transparent);cursor:pointer;" @click="openAttachmentPreview(att)" title="معاينة الفيديو"></video>

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

<div v-if="selectedContact.isGroup && groupIsAnnouncement && !groupInfoIsAdmin" class="blocked-compose-bar" style="background:rgba(var(--gold-rgb,212,175,55),0.08);border-top:1px solid var(--border);">
<i class="ri-megaphone-line" style="color:var(--gold);"></i>
<span style="color:var(--text);">هذه المجموعة في وضع الإعلانات — فقط المشرفون يمكنهم الإرسال</span>
</div>

<template v-if="!contactBlocked && !(selectedContact.isGroup && groupIsAnnouncement && !groupInfoIsAdmin)">
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

<template v-else>
<div class="empty">
<div style="display:flex;flex-direction:column;align-items:center;gap:14px;">
<i class="ri-message-3-line" style="font-size:48px;color:var(--gold);opacity:.4;"></i>
<span>@{{ t.selectChat }}</span>
<button v-if="!isDesktop" class="call-btn" style="background:var(--gold);color:#000;width:auto;border-radius:12px;padding:0 20px;height:44px;font-size:14px;font-weight:700;gap:6px;display:flex;align-items:center;" @click="showSidebar = true">
<i class="ri-contacts-book-line"></i> المحادثات
</button>
</div>
</div>
</template>

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

<div class="status-viewer-content" :key="currentStatus ? currentStatus.id : 'no-st'" v-if="currentStatus"
@click="handleStatusContentClick"
@mousedown="startStatusLongPress" @mouseup="endStatusLongPress" @mouseleave="cancelStatusLongPress"
@touchstart.prevent="startStatusLongPress" @touchend="endStatusLongPress" @touchmove.prevent="cancelStatusLongPress"
:style="{background: statusViewerBackground(currentStatus), filter: statusViewerFilter(currentStatus)}">

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

<div class="status-viewer-media-backdrop" v-if="currentStatus.type === 'image' && (currentStatus.content_url || currentStatus.contentUrl)" :style="{ backgroundImage: 'url(' + (currentStatus.contentUrl || ('/storage/' + currentStatus.content_url)) + ')' }"></div>

<img v-if="currentStatus.type === 'image' && (currentStatus.content_url || currentStatus.contentUrl)" class="status-viewer-img"
     :src="(currentStatus.contentUrl || ('/storage/' + currentStatus.content_url))" alt=""
     :style="{ position:'absolute', top:(currentStatus.mediaPosY??50)+'%', left:(currentStatus.mediaPosX??50)+'%', transform:'translate(-50%,-50%) rotate('+(currentStatus.mediaRotate??0)+'deg) scale('+(currentStatus.mediaScale??1)+')', width:'100%', height:'100%', objectFit: currentStatus.mediaFit || 'contain', maxWidth:'none', maxHeight:'none' }"
     @load="statusViewerReady = true; startStatusProgress()">

<template v-else-if="currentStatus.type === 'video' && (currentStatus.content_url || currentStatus.contentUrl)">
  <video class="status-viewer-img" autoplay muted loop playsinline
         :src="(currentStatus.contentUrl || ('/storage/' + currentStatus.content_url))"
         style="object-fit:cover;filter:blur(28px) brightness(.45);pointer-events:none;z-index:1;position:absolute;inset:0;width:100%;height:100%;"></video>
  <video ref="statusViewerVideo" class="status-viewer-img" autoplay playsinline :muted="statusViewerMuted"
         :src="(currentStatus.contentUrl || ('/storage/' + currentStatus.content_url))"
         :style="{ position:'absolute', top:(currentStatus.mediaPosY??50)+'%', left:(currentStatus.mediaPosX??50)+'%', transform:'translate(-50%,-50%) rotate('+(currentStatus.mediaRotate??0)+'deg) scale('+(currentStatus.mediaScale??1)+')', width:'100%', height:'100%', objectFit: currentStatus.mediaFit || 'contain', maxWidth:'none', maxHeight:'none', zIndex: 2 }"
         @loadedmetadata="onStatusVideoReady" @canplay="onStatusVideoReady"></video>
</template>

  <div v-for="(layer, lIdx) in getStatusTextLayers(currentStatus)" :key="'txt-'+lIdx"
       class="status-viewer-text-layer"
       style="position:absolute; white-space:pre-wrap; z-index:10; font-weight:600; max-width:calc(100% - 40px); word-break:break-word; line-height:1.35; width:max-content;"
       :style="{
         fontFamily: layer.isSticker ? undefined : (layer.fontStyle || 'Tajawal'),
         fontSize: layer.isSticker ? undefined : ((layer.fontSize || 42)+'px'),
         color: layer.isSticker ? undefined : ((layer.textBgStyle==='neon') ? '#fff' : (layer.textColor || '#ffffff')),
         textAlign: layer.isSticker ? undefined : (layer.textAlign || 'center'),
         top: (layer.textPosY ?? 50)+'%',
         left: (layer.textPosX ?? 50)+'%',
         transform: 'translate(-50%,-50%) rotate('+(layer.rotate||0)+'deg) scale('+(layer.scale||1)+')',
         textShadow: layer.isSticker ? 'none' : ((layer.textBgStyle||'none')==='none' ? '0 2px 8px rgba(0,0,0,.7)' : ((layer.textBgStyle==='neon') ? '0 0 8px ' + (layer.textColor || '#ffffff') + ', 0 0 16px ' + (layer.textColor || '#ffffff') : 'none')),
         background: layer.isSticker ? 'transparent' : ((layer.textBgStyle==='solid') ? 'rgba(0,0,0,0.75)' : ((layer.textBgStyle==='translucent') ? 'rgba(0,0,0,0.38)' : 'transparent')),
         padding: (!layer.isSticker && (layer.textBgStyle==='solid' || layer.textBgStyle==='translucent')) ? '10px 20px' : '0',
         borderRadius: (!layer.isSticker && (layer.textBgStyle==='solid' || layer.textBgStyle==='translucent')) ? '12px' : '0'
     }">
    <template v-if="!layer.isSticker">
      <span style="white-space:pre-wrap;">@{{ layer.content }}</span>
    </template>
    <template v-else>
      <img v-if="!layer.isVideo" :src="layer.url" style="width:120px;height:auto;pointer-events:none;display:block;" />
      <video v-else :src="layer.url" autoplay loop muted playsinline style="width:120px;height:auto;pointer-events:none;display:block;"></video>
    </template>
  </div>

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
<input class="status-reply-input" v-model="statusReplyText" placeholder="اكتب ردًا على الحالة..." @focus="onReplyInputFocus" @blur="onReplyInputBlur" @keydown.esc.prevent.stop="closeReplyPanel" @keydown.enter.prevent.stop="replyToStatus">
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
<button v-for="e in emojiList" :key="e" @mousedown.prevent @click="sendQuickStatusReaction(e); closeStatusFullEmoji()">@{{ e }}</button>
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
           :style="{ transform: 'translate(-50%,-50%) rotate('+(statusEditor.mediaRotate||0)+'deg) scale('+(statusEditor.mediaScale||1)+')', top: (statusEditor.mediaPosY||50)+'%', left: (statusEditor.mediaPosX||50)+'%', objectFit: statusEditor.mediaFit || 'contain', maxWidth: 'none', maxHeight: 'none' }"
           @pointerdown.prevent.stop="startStatusMediaDrag($event)">
      <template v-else-if="statusEditor.type==='video' && statusEditor.mediaPreview">
        <video class="sc-media sc-bg-video-blur" autoplay muted loop playsinline
               :src="statusEditor.mediaPreview"
               style="object-fit:cover;filter:blur(28px) brightness(.5);pointer-events:none;z-index:1;"></video>
        <video class="sc-media draggable-media" autoplay muted loop playsinline
               :src="statusEditor.mediaPreview"
               @click="handlePreviewBgClick"
               :style="{ transform: 'translate(-50%,-50%) rotate('+(statusEditor.mediaRotate||0)+'deg) scale('+(statusEditor.mediaScale||1)+')', top: (statusEditor.mediaPosY||50)+'%', left: (statusEditor.mediaPosX||50)+'%', objectFit: statusEditor.mediaFit || 'contain', maxWidth: 'none', maxHeight: 'none', zIndex: 2 }"
               @pointerdown.prevent.stop="startStatusMediaDrag($event)"></video>
      </template>
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
               textAlign: t.textAlign || 'center',
               top: (t.textPosY ?? 50)+'%',
               left: (t.textPosX ?? 50)+'%',
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
          <button v-if="statusEditor.type !== 'text' && statusEditor.mediaPreview" class="sc-btn" @click.stop="cycleMediaFit" :title="statusEditor.mediaFit === 'contain' ? 'ملء الشاشة' : 'المقاس الطبيعي'">
            <i :class="statusEditor.mediaFit === 'contain' ? 'ri-fullscreen-line' : 'ri-fullscreen-exit-line'"></i>
          </button>
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
          <button class="sc-btn" @click="cycleTextAlign" title="محاذاة النص">
            <i :class="seActiveText()?.textAlign==='right' ? 'ri-align-right' : (seActiveText()?.textAlign==='left' ? 'ri-align-left' : 'ri-align-center')"></i>
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
                        textAlign: seActiveText()?.textAlign || 'center',
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
<button class="ve2-play-btn" @click="videoEditorMuted = !videoEditorMuted"
        :title="videoEditorMuted ? 'تشغيل الصوت' : 'كتم الصوت'"
        :style="{color: videoEditorMuted ? 'var(--danger,#e53)' : 'inherit'}">
<i :class="videoEditorMuted ? 'ri-volume-mute-fill' : 'ri-volume-up-line'"></i>
</button>
<span class="ve2-time">@{{ formatDuration(veCurrentTime || 0) }} / @{{ formatDuration(videoEditorDuration || 0) }}</span>
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

<!-- Mute indicator + iOS warning -->
<div v-if="videoEditorMuted" style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--danger,#e53);padding:4px 10px;background:rgba(238,85,51,0.08);border-radius:8px;margin-bottom:4px;">
  <i class="ri-volume-mute-fill"></i> سيتم حذف الصوت من الفيديو المُخرَج
</div>
<div v-if="!$data._mediaRecorderSupported" style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--gold);padding:6px 10px;background:var(--gold-soft,rgba(196,150,58,0.1));border-radius:8px;margin-bottom:4px;">
  <i class="ri-information-line"></i> متصفحك لا يدعم المعالجة — سيُرسَل الفيديو الأصلي
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

{{-- ── Desktop notification position preview (fixed to viewport) ── --}}
<transition name="sn-preview-anim">
    <div v-if="snPreviewVisible"
         class="sn-preview-host"
         :class="[`sn-tc-${snPreviewPos}`, `sn-mode-${settingsNotifications.notifDisplayMode}`]"
         @mouseenter="snToastMouseEnter()"
         @mouseleave="snToastMouseLeave()">
        <div v-for="i in settingsNotifications.notifMaxCount"
             :key="i"
             class="sn-toast-card"
             :style="settingsNotifications.notifDisplayMode === 'stacked' ? {
                 zIndex: 99999 - i,
                 opacity: Math.max(0.12, 1 - (i-1) * 0.18),
                 transform: `translateY(${snPreviewPos.startsWith('bottom') ? -(i-1)*9 : (i-1)*9}px) scale(${1-(i-1)*0.05})`,
                 transitionDelay: `${(i-1)*60}ms`
             } : {
                 opacity: Math.max(0.5, 1 - (i-1) * 0.08)
             }">
            <div class="sn-toast-av"><i class="ri-user-3-fill"></i></div>
            <div class="sn-toast-body">
                <p class="sn-toast-name">@{{ settingsNotifications.showName ? 'أحمد محمد' : '■■■■■■■' }}</p>
                <p class="sn-toast-msg">@{{ settingsNotifications.showText ? 'مرحباً، كيف حالك؟' : '■■■■■■■■■■■' }}</p>
            </div>
            <div class="sn-toast-right">
                <span class="sn-toast-time">الآن</span>
                <span class="sn-toast-close-btn"><i class="ri-close-line"></i></span>
            </div>
        </div>
    </div>
</transition>

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

<div v-if="!includeExcludeMode" style="display:flex;justify-content:flex-end;gap:8px;">

<button class="profile-action-btn" @click="closeFoldersManager">إلغاء</button>

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
<button v-if="messageContextMessage && Number(messageContextMessage.senderId) !== Number(currentUserId) && !selectedContact.isGroup" @click="openProfile(selectedContact); closeMessageContext()">
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
<button v-if="messageContextMessage && Number(messageContextMessage.senderId) === Number(currentUserId) && !['audio','video','sticker_static','sticker_animated','gif','call'].includes(messageContextMessage.messageType)" @click="editMessage(messageContextMessage); closeMessageContext()">
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

<img :src="msg.attachmentUrl" style="max-height:80px;max-width:180px;border-radius:8px;object-fit:cover;" v-on:error="e => e.target.style.display='none'">

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
<div v-for="msg in group.items" :key="msg.id" style="aspect-ratio:1;border-radius:8px;overflow:hidden;cursor:pointer;background:var(--panel-2);display:flex;align-items:center;justify-content:center;" @click="openMediaModal(msg)">
<img :src="msg.attachmentUrl" style="width:100%;height:100%;object-fit:cover;" loading="lazy" v-on:error="e => { e.target.style.display='none'; e.target.parentElement.innerHTML='<i class=\'ri-image-off-line\' style=\'font-size:24px;opacity:.3;color:var(--muted)\'></i>'; }">
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

<!-- Group Info Panel -->
<div class="profile-modal" v-if="groupInfoOpen" @click.self="groupInfoOpen=false" style="z-index:1060;">
<div class="profile-card" style="max-width:460px;padding:0;overflow:hidden;border-radius:16px;">

<!-- Top Bar -->
<div style="display:flex;align-items:center;padding:12px 16px;background:var(--panel);border-bottom:1px solid var(--border);gap:10px;min-height:52px;flex-shrink:0;">
  <button v-if="groupInfoSubView" @click="groupInfoSubView=null" style="background:none;border:none;cursor:pointer;padding:4px;color:var(--text);display:flex;align-items:center;"><i class="ri-arrow-right-line" style="font-size:20px;"></i></button>
  <button v-else @click="groupInfoOpen=false" style="background:none;border:none;cursor:pointer;padding:4px;color:var(--text);display:flex;align-items:center;"><i class="ri-close-line" style="font-size:20px;"></i></button>
  <span style="font-size:16px;font-weight:700;color:var(--text);flex:1;">
    <span v-if="!groupInfoSubView">معلومات المجموعة</span>
    <span v-else-if="groupInfoSubView==='permissions'">صلاحيات المجموعة</span>
    <span v-else-if="groupInfoSubView==='invite'">رابط الدعوة</span>
  </span>
</div>

<!-- MAIN VIEW -->
<div v-if="!groupInfoSubView" class="profile-body" style="max-height:85vh;overflow-y:auto;">

  <!-- Avatar banner -->
  <div style="background:linear-gradient(135deg,#0a1628,#1a3a5c);padding:28px 0 48px;text-align:center;position:relative;">
    <div style="display:inline-block;position:relative;">
      <div style="width:90px;height:90px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;border:3px solid rgba(255,255,255,0.2);overflow:hidden;margin:0 auto;">
        <img v-if="groupInfoData.avatar_url" :src="groupInfoData.avatar_url" style="width:100%;height:100%;object-fit:cover;">
        <i v-else class="ri-group-fill" style="font-size:38px;color:#000;"></i>
      </div>
      <label v-if="groupInfoIsAdmin" style="position:absolute;bottom:2px;right:2px;width:28px;height:28px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #0a1628;" title="تغيير الصورة">
        <i class="ri-camera-fill" style="font-size:13px;color:#000;"></i>
        <input type="file" accept="image/*" style="display:none;" @change="onGroupAvatarChange($event)">
      </label>
    </div>
  </div>

  <!-- Name + Description card -->
  <div style="background:var(--panel);padding:16px 20px;margin-top:-28px;border-radius:16px 16px 0 0;position:relative;">

    <!-- Name -->
    <div style="margin-bottom:12px;">
      <div v-if="!groupInfoNameEditing" style="display:flex;align-items:center;gap:8px;" :style="groupInfoIsAdmin ? 'cursor:pointer' : ''" @click="groupInfoIsAdmin && (groupInfoNameEditing=true, groupInfoNameEdit=groupInfoData.name)">
        <div style="font-size:19px;font-weight:700;color:var(--text);flex:1;">@{{ groupInfoData.name }}</div>
        <i v-if="groupInfoIsAdmin" class="ri-pencil-line" style="color:var(--muted);font-size:16px;flex-shrink:0;"></i>
      </div>
      <div v-else style="display:flex;gap:8px;align-items:center;">
        <input v-model="groupInfoNameEdit" maxlength="120" @keyup.enter="saveGroupName()" @keyup.escape="groupInfoNameEditing=false" autofocus style="flex:1;font-size:17px;font-weight:600;border:none;border-bottom:2px solid var(--gold);background:transparent;color:var(--text);outline:none;padding:4px 0;">
        <button @click="saveGroupName()" style="background:var(--gold);border:none;border-radius:8px;padding:5px 12px;cursor:pointer;font-size:12px;font-weight:600;color:#000;">حفظ</button>
        <button @click="groupInfoNameEditing=false" style="background:var(--input-bg);border:1px solid var(--border);border-radius:8px;padding:5px 10px;cursor:pointer;font-size:12px;color:var(--muted);">x</button>
      </div>
    </div>

    <!-- Member count -->
    <div style="font-size:13px;color:var(--muted);margin-bottom:14px;">@{{ (groupInfoData.members_count || groupInfoMembers.length || 0) }} عضو</div>

    <!-- Description -->
    <div style="border-top:1px solid var(--border);padding-top:12px;">
      <div v-if="!groupInfoDescEditing">
        <div style="font-size:12px;color:var(--gold);font-weight:600;margin-bottom:4px;">الوصف</div>
        <div style="display:flex;align-items:flex-start;gap:8px;" :style="groupInfoIsAdmin ? 'cursor:pointer' : ''" @click="groupInfoIsAdmin && (groupInfoDescEditing=true, groupInfoDescEdit=groupInfoData.description||'')">
          <div style="font-size:13px;flex:1;line-height:1.5;" :style="groupInfoData.description ? 'color:var(--text)' : 'color:var(--muted);font-style:italic'">@{{ groupInfoData.description || (groupInfoIsAdmin ? 'أضف وصفاً للمجموعة...' : 'لا يوجد وصف') }}</div>
          <i v-if="groupInfoIsAdmin" class="ri-pencil-line" style="color:var(--muted);font-size:14px;flex-shrink:0;margin-top:2px;"></i>
        </div>
      </div>
      <div v-else>
        <div style="font-size:12px;color:var(--gold);font-weight:600;margin-bottom:6px;">الوصف</div>
        <textarea v-model="groupInfoDescEdit" maxlength="500" placeholder="أضف وصفاً للمجموعة..." @keyup.escape="groupInfoDescEditing=false" style="width:100%;min-height:72px;border:none;border-bottom:2px solid var(--gold);background:transparent;color:var(--text);font-size:13px;padding:4px 0;outline:none;resize:none;box-sizing:border-box;font-family:inherit;direction:rtl;"></textarea>
        <div style="display:flex;gap:8px;margin-top:8px;">
          <button @click="saveGroupDesc()" style="background:var(--gold);border:none;border-radius:8px;padding:6px 14px;cursor:pointer;font-size:12px;font-weight:600;color:#000;">حفظ</button>
          <button @click="groupInfoDescEditing=false" style="background:var(--input-bg);border:1px solid var(--border);border-radius:8px;padding:6px 12px;cursor:pointer;font-size:12px;color:var(--muted);">إلغاء</button>
        </div>
      </div>
    </div>

  </div>

  <!-- Quick Actions -->
  <div style="background:var(--panel);margin-top:8px;padding:14px 16px;">
    <div style="display:flex;justify-content:space-around;">
      <button @click="groupInfoOpen=false; startGroupCall('voice')" style="display:flex;flex-direction:column;align-items:center;gap:5px;background:none;border:none;cursor:pointer;">
        <div style="width:46px;height:46px;border-radius:50%;background:rgba(212,175,55,0.15);display:flex;align-items:center;justify-content:center;"><i class="ri-phone-line" style="font-size:22px;color:var(--gold);"></i></div>
        <span style="font-size:11px;color:var(--muted);">صوتية</span>
      </button>
      <button @click="groupInfoOpen=false; startGroupCall('video')" style="display:flex;flex-direction:column;align-items:center;gap:5px;background:none;border:none;cursor:pointer;">
        <div style="width:46px;height:46px;border-radius:50%;background:rgba(212,175,55,0.15);display:flex;align-items:center;justify-content:center;"><i class="ri-vidicon-line" style="font-size:22px;color:var(--gold);"></i></div>
        <span style="font-size:11px;color:var(--muted);">مرئية</span>
      </button>
      <button @click="groupInfoOpen=false; muteOptionsOpen=true" style="display:flex;flex-direction:column;align-items:center;gap:5px;background:none;border:none;cursor:pointer;">
        <div style="width:46px;height:46px;border-radius:50%;background:rgba(108,108,108,0.12);display:flex;align-items:center;justify-content:center;"><i class="ri-volume-mute-line" style="font-size:22px;color:var(--muted);"></i></div>
        <span style="font-size:11px;color:var(--muted);">كتم</span>
      </button>
      <button @click="groupInfoOpen=false" style="display:flex;flex-direction:column;align-items:center;gap:5px;background:none;border:none;cursor:pointer;">
        <div style="width:46px;height:46px;border-radius:50%;background:rgba(108,108,108,0.12);display:flex;align-items:center;justify-content:center;"><i class="ri-search-line" style="font-size:22px;color:var(--muted);"></i></div>
        <span style="font-size:11px;color:var(--muted);">بحث</span>
      </button>
    </div>
  </div>

  <!-- Settings Rows -->
  <div style="background:var(--panel);margin-top:8px;">
    <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid var(--border);">
      <div style="width:36px;height:36px;border-radius:10px;background:#2196F3;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-image-line" style="color:#fff;font-size:18px;"></i></div>
      <div style="flex:1;">
        <div style="font-size:14px;color:var(--text);">الوسائط والملفات</div>
        <div v-if="groupMediaCount" style="font-size:12px;color:var(--muted);">@{{ groupMediaCount }} ملف</div>
      </div>
      <i class="ri-arrow-left-s-line" style="color:var(--muted);font-size:20px;"></i>
    </div>
    <div class="gip-row" style="display:flex;align-items:center;gap:14px;padding:14px 18px;cursor:pointer;" @click="groupInfoOpen=false; muteOptionsOpen=true">
      <div style="width:36px;height:36px;border-radius:10px;background:#9C27B0;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-notification-line" style="color:#fff;font-size:18px;"></i></div>
      <div style="flex:1;font-size:14px;color:var(--text);">الإشعارات</div>
      <i class="ri-arrow-left-s-line" style="color:var(--muted);font-size:20px;"></i>
    </div>
  </div>

  <!-- Admin Settings -->
  <div v-if="groupInfoIsAdmin" style="background:var(--panel);margin-top:8px;">
    <div style="padding:10px 18px 4px;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">إعدادات المجموعة</div>
    <div @click="groupInfoSubView='permissions'" class="gip-row" style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid var(--border);cursor:pointer;">
      <div style="width:36px;height:36px;border-radius:10px;background:#FF5722;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-shield-check-line" style="color:#fff;font-size:18px;"></i></div>
      <div style="flex:1;">
        <div style="font-size:14px;color:var(--text);">صلاحيات المجموعة</div>
        <div style="font-size:12px;color:var(--muted);">إرسال، إضافة، تعديل</div>
      </div>
      <i class="ri-arrow-left-s-line" style="color:var(--muted);font-size:20px;"></i>
    </div>
    <div @click="groupInfoSubView='invite'" class="gip-row" style="display:flex;align-items:center;gap:14px;padding:14px 18px;cursor:pointer;">
      <div style="width:36px;height:36px;border-radius:10px;background:#4CAF50;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-link" style="color:#fff;font-size:18px;"></i></div>
      <div style="flex:1;">
        <div style="font-size:14px;color:var(--text);">رابط دعوة المجموعة</div>
        <div v-if="groupInviteUrl" style="font-size:12px;color:#4CAF50;">رابط نشط</div>
        <div v-else style="font-size:12px;color:var(--muted);">لا يوجد رابط</div>
      </div>
      <i class="ri-arrow-left-s-line" style="color:var(--muted);font-size:20px;"></i>
    </div>
  </div>
  <div v-else-if="groupInviteUrl" style="background:var(--panel);margin-top:8px;">
    <div @click="groupInfoSubView='invite'" class="gip-row" style="display:flex;align-items:center;gap:14px;padding:14px 18px;cursor:pointer;">
      <div style="width:36px;height:36px;border-radius:10px;background:#4CAF50;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-link" style="color:#fff;font-size:18px;"></i></div>
      <div style="flex:1;font-size:14px;color:var(--text);">رابط دعوة المجموعة</div>
      <i class="ri-arrow-left-s-line" style="color:var(--muted);font-size:20px;"></i>
    </div>
  </div>

  <!-- Members Section -->
  <div style="background:var(--panel);margin-top:8px;">
    <div style="padding:10px 18px 4px;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">الأعضاء (@{{ groupInfoMembers.length }})</div>

    <div v-if="groupInfoMembers.length > 5" style="padding:8px 14px 4px;">
      <div style="display:flex;align-items:center;gap:8px;background:var(--input-bg);border:1px solid var(--border);border-radius:10px;padding:6px 12px;">
        <i class="ri-search-line" style="color:var(--muted);font-size:14px;"></i>
        <input v-model="groupMemberSearch" placeholder="بحث في الأعضاء..." style="border:none;background:transparent;color:var(--text);font-size:13px;outline:none;flex:1;direction:rtl;">
      </div>
    </div>

    <div v-if="groupInfoIsAdmin || groupInfoData.who_can_add_members === 'all'" class="gip-row" style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid var(--border);cursor:pointer;">
      <div style="width:40px;height:40px;border-radius:50%;background:rgba(212,175,55,0.15);border:2px dashed var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-user-add-line" style="color:var(--gold);font-size:18px;"></i></div>
      <span style="font-size:14px;color:var(--gold);font-weight:600;">إضافة عضو</span>
    </div>

    <div v-if="groupInviteUrl" @click="copyGroupInviteLink()" class="gip-row" style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid var(--border);cursor:pointer;">
      <div style="width:40px;height:40px;border-radius:50%;background:rgba(76,175,80,0.15);border:2px dashed #4CAF50;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="ri-link" style="color:#4CAF50;font-size:18px;"></i></div>
      <span style="font-size:14px;color:#4CAF50;font-weight:600;">دعوة عبر رابط</span>
    </div>

    <div v-if="groupInfoLoading" style="text-align:center;padding:20px;color:var(--muted);"><i class="ri-loader-4-line"></i> جاري التحميل...</div>

    <div v-else>
      <div v-for="member in groupInfoMembers.filter(m => !groupMemberSearch || m.name.toLowerCase().includes(groupMemberSearch.toLowerCase()))" :key="member.id" style="display:flex;align-items:center;gap:12px;padding:10px 18px;border-bottom:1px solid var(--border);">
        <div style="position:relative;flex-shrink:0;">
          <img v-if="member.avatar_url" :src="member.avatar_url" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
          <div v-else style="width:42px;height:42px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;color:#000;">@{{ (member.name||'?')[0] }}</div>
          <span v-if="member.isAdmin" style="position:absolute;bottom:-2px;right:-2px;background:var(--gold);border-radius:50%;width:17px;height:17px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--panel);" title="مشرف"><i class="ri-star-fill" style="font-size:9px;color:#000;"></i></span>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:600;color:var(--text);">@{{ member.name }}<span v-if="member.id===currentUserId" style="color:var(--muted);font-weight:400;"> (أنت)</span></div>
          <div style="font-size:12px;color:var(--muted);">@{{ member.isAdmin ? 'مشرف' : 'عضو' }}</div>
        </div>
        <div v-if="groupInfoIsAdmin && member.id !== currentUserId" style="display:flex;gap:4px;">
          <button v-if="!member.isAdmin" @click="changeGroupMemberRole(member, 'admin')" title="ترقية لمشرف" style="background:none;border:1px solid var(--border);border-radius:8px;padding:4px 8px;cursor:pointer;font-size:11px;color:var(--gold);"><i class="ri-star-line"></i></button>
          <button v-else @click="changeGroupMemberRole(member, 'member')" title="إلغاء الإشراف" style="background:none;border:1px solid var(--border);border-radius:8px;padding:4px 8px;cursor:pointer;font-size:11px;color:var(--muted);"><i class="ri-star-fill"></i></button>
          <button @click="removeGroupMember(member)" title="إزالة من المجموعة" style="background:none;border:1px solid #dc3545;border-radius:8px;padding:4px 8px;cursor:pointer;font-size:11px;color:#dc3545;"><i class="ri-user-unfollow-line"></i></button>
        </div>
      </div>
    </div>

    <div v-if="groupInfoIsAdmin" style="padding:10px 14px;border-top:1px solid var(--border);">
      <div style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;">إضافة عضو جديد</div>
      <div style="display:flex;gap:6px;">
        <input v-model="groupAddMemberSearch" @input="searchGroupAddMember()" placeholder="ابحث بالاسم..." style="flex:1;border:1px solid var(--border);border-radius:10px;padding:8px 10px;background:var(--input-bg);color:var(--text);font-size:13px;outline:none;">
        <button v-if="groupAddMemberSearch" @click="groupAddMemberSearch=''; groupAddMemberResults=[];" style="background:var(--input-bg);border:1px solid var(--border);border-radius:10px;padding:6px 10px;cursor:pointer;color:var(--muted);"><i class="ri-close-line"></i></button>
      </div>
      <div v-if="groupAddMemberResults.length" style="background:var(--panel);border:1px solid var(--border);border-radius:10px;margin-top:4px;max-height:150px;overflow-y:auto;">
        <div v-for="u in groupAddMemberResults" :key="u.id" @click="addGroupMember(u)" style="display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;border-bottom:1px solid var(--border);">
          <img v-if="u.avatar_url" :src="u.avatar_url" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
          <div v-else style="width:32px;height:32px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#000;">@{{ (u.name||'?')[0] }}</div>
          <span style="font-size:13px;color:var(--text);">@{{ u.name }}</span>
          <span v-if="groupInfoMembers.some(m=>m.id===u.id)" style="font-size:11px;color:var(--muted);margin-right:auto;">عضو</span>
          <i v-else class="ri-user-add-line" style="margin-right:auto;color:var(--gold);"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Danger Zone -->
  <div style="background:var(--panel);margin-top:8px;padding:16px 18px;display:flex;flex-direction:column;gap:10px;margin-bottom:8px;">
    <button @click="leaveGroup()" style="width:100%;background:none;border:1px solid #dc3545;border-radius:12px;padding:11px;cursor:pointer;font-size:13px;font-weight:600;color:#dc3545;display:flex;align-items:center;justify-content:center;gap:6px;"><i class="ri-logout-box-line"></i> مغادرة المجموعة</button>
    <button v-if="groupInfoIsAdmin" @click="deleteGroup()" style="width:100%;background:#dc3545;border:none;border-radius:12px;padding:11px;cursor:pointer;font-size:13px;font-weight:600;color:#fff;display:flex;align-items:center;justify-content:center;gap:6px;"><i class="ri-delete-bin-6-line"></i> حذف المجموعة نهائياً</button>
  </div>

</div><!-- end main view -->

<!-- PERMISSIONS SUB-VIEW -->
<div v-if="groupInfoSubView==='permissions'" class="profile-body" style="max-height:85vh;overflow-y:auto;padding:8px 0;">

  <div style="background:var(--panel);margin-bottom:8px;">
    <div style="padding:12px 18px 6px;">
      <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px;">من يمكنه إرسال الرسائل</div>
      <div style="font-size:12px;color:var(--muted);">التحكم في من يمكنه إرسال رسائل في المجموعة</div>
    </div>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_send" :checked="groupInfoData.who_can_send==='all'" @change="saveGroupPermission('who_can_send','all')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div>
        <div style="font-size:14px;color:var(--text);">جميع الأعضاء</div>
        <div style="font-size:12px;color:var(--muted);">يمكن لكل عضو إرسال الرسائل</div>
      </div>
    </label>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_send" :checked="groupInfoData.who_can_send==='admins'" @change="saveGroupPermission('who_can_send','admins')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div>
        <div style="font-size:14px;color:var(--text);">المشرفون فقط</div>
        <div style="font-size:12px;color:var(--muted);">وضع الإعلانات — فقط المشرفون يرسلون</div>
      </div>
    </label>
  </div>

  <div style="background:var(--panel);margin-bottom:8px;">
    <div style="padding:12px 18px 6px;">
      <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px;">من يمكنه إضافة أعضاء</div>
      <div style="font-size:12px;color:var(--muted);">من يمكنه دعوة وإضافة أعضاء جدد</div>
    </div>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_add" :checked="groupInfoData.who_can_add_members==='all'" @change="saveGroupPermission('who_can_add_members','all')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div style="font-size:14px;color:var(--text);">جميع الأعضاء</div>
    </label>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_add" :checked="groupInfoData.who_can_add_members==='admins'" @change="saveGroupPermission('who_can_add_members','admins')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div style="font-size:14px;color:var(--text);">المشرفون فقط</div>
    </label>
  </div>

  <div style="background:var(--panel);margin-bottom:8px;">
    <div style="padding:12px 18px 6px;">
      <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px;">من يمكنه تعديل المجموعة</div>
      <div style="font-size:12px;color:var(--muted);">تعديل اسم المجموعة ووصفها وصورتها</div>
    </div>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_edit" :checked="groupInfoData.who_can_edit_info==='all'" @change="saveGroupPermission('who_can_edit_info','all')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div style="font-size:14px;color:var(--text);">جميع الأعضاء</div>
    </label>
    <label style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border);cursor:pointer;">
      <input type="radio" name="who_can_edit" :checked="groupInfoData.who_can_edit_info==='admins'" @change="saveGroupPermission('who_can_edit_info','admins')" style="accent-color:var(--gold);width:18px;height:18px;">
      <div style="font-size:14px;color:var(--text);">المشرفون فقط</div>
    </label>
  </div>

</div><!-- end permissions sub-view -->

<!-- INVITE LINK SUB-VIEW -->
<div v-if="groupInfoSubView==='invite'" class="profile-body" style="max-height:85vh;overflow-y:auto;padding:16px;">

  <div style="text-align:center;margin-bottom:20px;">
    <div style="width:64px;height:64px;border-radius:50%;background:rgba(76,175,80,0.15);border:2px solid #4CAF50;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;"><i class="ri-link" style="font-size:28px;color:#4CAF50;"></i></div>
    <div style="font-size:14px;color:var(--muted);">شارك هذا الرابط مع من تريد دعوته للانضمام للمجموعة</div>
  </div>

  <div v-if="groupInviteUrl" style="margin-bottom:16px;">
    <div style="background:var(--input-bg);border:1px solid var(--border);border-radius:12px;padding:12px 14px;font-size:12px;color:var(--text);word-break:break-all;direction:ltr;text-align:left;line-height:1.6;">@{{ groupInviteUrl }}</div>
  </div>
  <div v-else style="background:var(--input-bg);border:1px dashed var(--border);border-radius:12px;padding:20px;text-align:center;margin-bottom:16px;">
    <div style="font-size:13px;color:var(--muted);">لا يوجد رابط دعوة حالياً</div>
  </div>

  <div style="display:flex;flex-direction:column;gap:10px;">
    <button v-if="groupInviteUrl" @click="copyGroupInviteLink()" style="width:100%;background:var(--gold);border:none;border-radius:12px;padding:12px;cursor:pointer;font-size:14px;font-weight:600;color:#000;display:flex;align-items:center;justify-content:center;gap:8px;"><i class="ri-file-copy-line"></i> نسخ الرابط</button>
    <button v-if="!groupInviteUrl && groupInfoIsAdmin" @click="generateGroupInviteLink()" style="width:100%;background:var(--gold);border:none;border-radius:12px;padding:12px;cursor:pointer;font-size:14px;font-weight:600;color:#000;display:flex;align-items:center;justify-content:center;gap:8px;"><i class="ri-link-m"></i> إنشاء رابط دعوة</button>
    <div v-if="groupInfoIsAdmin && groupInviteUrl" style="display:flex;gap:8px;">
      <button @click="revokeGroupInviteLink(false)" style="flex:1;background:var(--input-bg);border:1px solid var(--border);border-radius:12px;padding:11px;cursor:pointer;font-size:13px;color:var(--text);display:flex;align-items:center;justify-content:center;gap:6px;"><i class="ri-refresh-line"></i> تجديد الرابط</button>
      <button @click="revokeGroupInviteLink(true)" style="background:var(--input-bg);border:1px solid #dc3545;border-radius:12px;padding:11px 14px;cursor:pointer;font-size:13px;color:#dc3545;display:flex;align-items:center;justify-content:center;gap:6px;"><i class="ri-delete-bin-line"></i> إلغاء</button>
    </div>
  </div>

</div><!-- end invite sub-view -->

</div><!-- profile-card -->
</div><!-- profile-modal -->
<style>.gip-row{transition:background 0.15s;}.gip-row:hover{background:rgba(128,128,128,0.06);}</style>


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
<img v-if="st.type === 'image' && st.contentUrl" :src="st.contentUrl" style="width:100%;height:100%;object-fit:cover;" :style="{opacity: st.isExpired ? 0.55 : 1}">
<div v-else :style="{width:'100%',height:'100%',display:'flex',alignItems:'center',justifyContent:'center',background:st.bgColor || 'var(--panel-2)',fontSize:'28px',color:'#fff',opacity: st.isExpired ? 0.55 : 1}"><i class="ri-image-line"></i></div>
<div v-if="st.isExpired" style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,0.6);border-radius:4px;padding:1px 5px;font-size:9px;color:rgba(255,255,255,0.7);">منتهية</div>
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
<span>@{{ myProfileViewerStatus?.viewsCount ?? myProfileViewerStatus?.views_count ?? 0 }}</span>
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
<span v-if="vw.liked" style="font-size:16px;" title="أعجبه">❤️</span>
<div style="font-size:11px;color:var(--muted);">@{{ vw.time || '' }}</div>
</div>
<div v-if="!myProfileViewersList.length" style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">لا يوجد مشاهدون بعد</div>
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
<div v-for="(m,i) in day.items" :key="i" style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;background:var(--panel-2);display:flex;align-items:center;justify-content:center;" @click="openMediaModal(m)">
<img :src="m.attachmentUrl" style="width:100%;height:100%;object-fit:cover;" loading="lazy" v-on:error="e => { e.target.style.display='none'; e.target.parentElement.innerHTML='<i class=\'ri-image-off-line\' style=\'font-size:24px;opacity:.3;color:var(--muted)\'></i>'; }">
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
<select v-model="callSettings.ringtone" @change="previewTone(callSettings.ringtone); saveCallSettings()" style="min-width:120px;">
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

<!-- Call ended summary flash -->
<div class="call-ended-flash" v-if="callEndedSummary" style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:400;background:var(--panel);border:1px solid var(--theme-border);border-radius:14px;padding:12px 24px;box-shadow:0 12px 40px rgba(0,0,0,.4);display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;animation:call-ended-pop .3s cubic-bezier(.34,1.56,.64,1);">
<i class="ri-phone-fill" style="color:var(--muted);transform:rotate(135deg);"></i>
<span>انتهت المكالمة</span>
<span v-if="callEndedSummary.duration" style="color:var(--gold);">· @{{ callEndedSummary.duration }}</span>
</div>

<!-- Call minimized chip -->
<div class="call-minimized-chip" v-if="callState && callMinimized" @click="callMinimized = false" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:300;background:var(--panel);border:1px solid var(--theme-border);border-radius:999px;padding:10px 20px;box-shadow:0 8px 32px rgba(0,0,0,.4);display:flex;align-items:center;gap:12px;cursor:pointer;animation:incoming-call-slide-in .3s var(--ease-out);">
<div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--gold),var(--gold-2));display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;flex-shrink:0;animation:call-pulse 1.8s ease-in-out infinite;">
<img v-if="callContact && normalizeAvatarUrl(callContact.avatar_url)" :src="normalizeAvatarUrl(callContact.avatar_url)" style="width:100%;height:100%;object-fit:cover;" v-on:error="$event.target.style.display='none'">
<span v-else>@{{ callContact ? getAuthorInitial(callContact.name) : '?' }}</span>
</div>
<div style="display:flex;flex-direction:column;min-width:0;">
<span style="font-size:13px;font-weight:700;">@{{ callContact ? callContact.name : '' }}</span>
<span style="font-size:11px;color:var(--gold);">@{{ callState === 'calling' ? getCallStatusText() : formatCallElapsed() }}</span>
</div>
<button class="call-btn end" style="width:36px;height:36px;font-size:16px;" @click.stop="endCall" title="إنهاء"><i class="ri-phone-fill" style="transform:rotate(135deg)"></i></button>
</div>

<!-- Call overlay -->
<div class="call-overlay" v-show="(callState === 'calling' || callState === 'in-call') && !callMinimized" :class="{ 'is-active': !!callState }" :data-call-state="callState">

<!-- FaceTime-style blurred avatar background -->
<div class="call-bg-blur" v-if="callContact && normalizeAvatarUrl(callContact.avatar_url)" :style="{ backgroundImage: 'url(' + normalizeAvatarUrl(callContact.avatar_url) + ')' }"></div>

<!-- Minimize button -->
<button class="call-minimize-btn" @click="callMinimized = true" title="تصغير"><i class="ri-subtract-line"></i></button>

<div class="call-participants-grid" :class="'count-' + Math.min(callParticipants.length, 4)" v-if="callState === 'in-call' && callType === 'video'">
<div class="call-participant-tile" v-for="p in callParticipants" :key="p.id" :class="{'speaking': speakingUserId === p.id}">
<video :ref="'remoteMedia_' + p.id" autoplay playsinline></video>
<div class="call-participant-label"><i v-if="speakingUserId === p.id" class="ri-mic-fill" style="font-size:11px;color:#4ade80;"></i> @{{ p.name }}</div>
<div class="call-cam-off-placeholder" v-if="p.cameraOff">
<div class="call-avatar" style="width:64px;height:64px;font-size:28px;animation:none;box-shadow:none;">
<span>@{{ getAuthorInitial(p.name) }}</span>
</div>
</div>
</div>
</div>
<audio v-for="p in callParticipants" :key="'a'+p.id" :ref="'remoteMedia_' + p.id" autoplay v-if="callState === 'in-call' && callType !== 'video'"></audio>

<!-- Draggable local video PiP -->
<video ref="localVideo" autoplay playsinline muted
  class="call-pip"
  :style="{ bottom: pipPos.bottom + 'px', left: pipPos.left + 'px' }"
  v-show="callType === 'video' && callState === 'in-call' && !cameraOff"
  @pointerdown.prevent="startPipDrag"
></video>

<div class="call-avatar"
     v-show="!(callType === 'video' && callState === 'in-call')"
     :style="callState === 'in-call' ? { '--vad-spread': remoteVadSpread, '--vad-alpha': remoteVadAlpha } : {}">
<img v-if="callContact && normalizeAvatarUrl(callContact.avatar_url)" :src="normalizeAvatarUrl(callContact.avatar_url)" alt="" v-on:error="$event.target.style.display='none'">
<span v-else>@{{ callContact ? getAuthorInitial(callContact.name) : '?' }}</span>
</div>

<div class="call-name">
@{{ isGroupCall ? (callParticipants.map(p => p.name).join('، ') || callContact?.name) : (callContact ? callContact.name : '') }}
</div>

<div class="call-status">
<span v-if="callState === 'calling'">@{{ getCallStatusText() }}</span>
<span v-else-if="callState === 'in-call' && callConnectionWarning" style="color:#f59e0b;animation:pulse 1s infinite;">يعيد الاتصال...</span>
<span v-else-if="callState === 'in-call'">@{{ formatCallElapsed() }}</span>
</div>

<div class="call-actions">

<!-- ميكروفون + picker -->
<div class="call-btn-group" @mouseenter="openCallDeviceMenu('mic')" @mouseleave="closeCallDeviceMenu()">
<button class="call-btn mute" :class="{active: callMuted}" @click="toggleMute" title="كتم الصوت"><i :class="callMuted ? 'ri-mic-off-line' : 'ri-mic-line'"></i></button>
<div class="call-device-picker" v-show="callDeviceHoverOpen === 'mic' && callDevices.audioinput.length > 0"
     @mouseenter="cancelCallDeviceClose()" @mouseleave="closeCallDeviceMenu()">
<div class="cdp-title"><i class="ri-mic-2-line"></i> الميكروفون</div>
<button v-for="d in callDevices.audioinput" :key="'ai-'+d.deviceId" class="cdp-option"
        :class="{active: callActiveAudioInput ? callActiveAudioInput===d.deviceId : d.deviceId==='default' || callDevices.audioinput.indexOf(d)===0}"
        @click.stop="setCallAudioInput(d.deviceId)">
<i class="ri-check-line cdp-check"></i>
<span>@{{ d.label || ('ميكروفون ' + (callDevices.audioinput.indexOf(d)+1)) }}</span>
</button>
</div>
</div>

<!-- سماعة + picker -->
<div class="call-btn-group" @mouseenter="openCallDeviceMenu('speaker')" @mouseleave="closeCallDeviceMenu()">
<button class="call-btn speaker" :class="{active: speakerMuted}" @click="toggleSpeaker" title="السماعة"><i :class="speakerMuted ? 'ri-volume-mute-line' : 'ri-volume-up-line'"></i></button>
<div class="call-device-picker" v-show="callDeviceHoverOpen === 'speaker' && callDevices.audiooutput.length > 0"
     @mouseenter="cancelCallDeviceClose()" @mouseleave="closeCallDeviceMenu()">
<div class="cdp-title"><i class="ri-speaker-2-line"></i> مكبر الصوت</div>
<button v-for="d in callDevices.audiooutput" :key="'ao-'+d.deviceId" class="cdp-option"
        :class="{active: callActiveAudioOutput ? callActiveAudioOutput===d.deviceId : d.deviceId==='default' || callDevices.audiooutput.indexOf(d)===0}"
        @click.stop="setCallAudioOutput(d.deviceId)">
<i class="ri-check-line cdp-check"></i>
<span>@{{ d.label || ('مكبر ' + (callDevices.audiooutput.indexOf(d)+1)) }}</span>
</button>
</div>
</div>

<!-- إنهاء (لا picker) -->
<button class="call-btn end" @click="endCall" title="إنهاء"><i class="ri-phone-fill" style="transform:rotate(135deg)"></i></button>

<!-- كاميرا + picker -->
<div class="call-btn-group" v-if="callType === 'video'" @mouseenter="openCallDeviceMenu('camera')" @mouseleave="closeCallDeviceMenu()">
<button class="call-btn cam" :class="{active: cameraOff}" @click="toggleCamera" title="الكاميرا"><i :class="cameraOff ? 'ri-camera-off-line' : 'ri-camera-line'"></i></button>
<div class="call-device-picker" v-show="callDeviceHoverOpen === 'camera' && callDevices.videoinput.length > 0"
     @mouseenter="cancelCallDeviceClose()" @mouseleave="closeCallDeviceMenu()">
<div class="cdp-title"><i class="ri-camera-3-line"></i> الكاميرا</div>
<button v-for="d in callDevices.videoinput" :key="'vi-'+d.deviceId" class="cdp-option"
        :class="{active: callActiveVideoInput ? callActiveVideoInput===d.deviceId : d.deviceId==='default' || callDevices.videoinput.indexOf(d)===0}"
        @click.stop="setCallVideoInput(d.deviceId)">
<i class="ri-check-line cdp-check"></i>
<span>@{{ d.label || ('كاميرا ' + (callDevices.videoinput.indexOf(d)+1)) }}</span>
</button>
</div>
</div>

<button class="call-btn switch-cam" @click="switchCamera" title="تبديل الكاميرا" v-if="callType === 'video' && !cameraOff"><i class="ri-camera-switch-line"></i></button>
<button class="call-btn add-participant" @click="openAddParticipant" title="إضافة مشارك" v-if="callState === 'in-call' && callParticipants.length < 5"><i class="ri-user-add-line"></i></button>
</div>

</div>

<!-- Incoming call: floating widget -->
<div class="incoming-call-widget" v-if="callState === 'incoming'">
<button class="incoming-call-close" @click="rejectIncomingCall" title="رفض وإغلاق"><i class="ri-close-line"></i></button>
<div class="incoming-call-avatar">
<img v-if="callContact && normalizeAvatarUrl(callContact.avatar_url)" :src="normalizeAvatarUrl(callContact.avatar_url)" alt="" v-on:error="$event.target.style.display='none'">
<span v-else>@{{ callContact ? getAuthorInitial(callContact.name) : '?' }}</span>
</div>
<div class="incoming-call-info">
<div class="incoming-call-name">@{{ callContact ? callContact.name : '' }}</div>
<div class="incoming-call-status">@{{ (isGroupCall ? 'مكالمة جماعية ' : '') + (callType === 'video' ? 'مكالمة فيديو واردة' : 'مكالمة صوتية واردة') }}</div>
</div>
<div class="incoming-call-actions">
<button class="call-btn end" @click="rejectIncomingCall" title="رفض"><i class="ri-phone-fill" style="transform:rotate(135deg)"></i></button>
<button v-if="callType === 'video'" class="call-btn end" style="background:#1e7e34;" @click="answerIncomingCall('audio')" title="قبول بالصوت فقط"><i class="ri-mic-line"></i></button>
<button class="call-btn end call-answer-ring" style="background:#34C759;" @click="answerIncomingCall()" title="قبول"><i :class="callType === 'video' ? 'ri-vidicon-fill' : 'ri-phone-fill'"></i></button>
</div>
</div>

<!-- Add participant modal -->
<div class="calls-modal-overlay" v-if="showAddParticipant" @click.self="showAddParticipant = false">
<div class="calls-modal-card" style="max-width:420px;">
<div class="calls-header">
<h3><i class="ri-user-add-line"></i> إضافة مشارك</h3>
<button class="h-icon-btn" @click="showAddParticipant = false"><i class="ri-close-line"></i></button>
</div>

<!-- Pending invites section -->
<div v-if="pendingInvites.length" style="padding:10px 14px;border-bottom:1px solid var(--border);">
<div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">الدعوات الجارية</div>
<div v-for="inv in pendingInvites" :key="inv.id" style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-light,rgba(0,0,0,.05));" :style="inv===pendingInvites[pendingInvites.length-1]?'border-bottom:none':''">
  <div class="call-card-avatar" style="width:36px;height:36px;font-size:13px;">
    <img v-if="inv.avatar_url" :src="normalizeAvatarUrl(inv.avatar_url)" alt="">
    <span v-else>@{{ getAuthorInitial(inv.name) }}</span>
  </div>
  <div style="flex:1;min-width:0;">
    <div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@{{ inv.name }}</div>
    <div style="font-size:11px;margin-top:2px;" :style="inv.status==='ringing'?'color:#F59E0B':inv.status==='answered'?'color:#22C55E':inv.status==='declined'?'color:#EF4444':'color:var(--muted)'">
      <span v-if="inv.status==='ringing'"><i class="ri-loader-4-line" style="animation:spin 1s linear infinite;display:inline-block;"></i> جاري الرنين...</span>
      <span v-else-if="inv.status==='answered'"><i class="ri-check-line"></i> انضم</span>
      <span v-else-if="inv.status==='declined'"><i class="ri-close-line"></i> رفض المكالمة</span>
      <span v-else-if="inv.status==='timeout'"><i class="ri-time-line"></i> لم يرد</span>
    </div>
  </div>
  <button v-if="inv.status==='declined'||inv.status==='timeout'" @click="ringAgain(inv)" style="padding:5px 10px;border-radius:8px;border:1px solid var(--gold);background:var(--gold-light,rgba(198,166,117,.15));color:var(--gold-dark,#8D7252);font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
    <i class="ri-phone-line"></i> اتصل مجدداً
  </button>
</div>
</div>

<div class="new-call-search-wrap">
<i class="ri-search-line" style="color:var(--muted);font-size:16px;"></i>
<input class="new-call-search" v-model="addParticipantQuery" placeholder="ابحث بالاسم...">
</div>
<div class="calls-list" style="max-height:260px;">
<template v-for="c in (contacts || []).filter(c => Number(c.id) !== -1 && !callParticipants.some(p => Number(p.id) === Number(c.id)) && !pendingInvites.some(p => Number(p.id) === Number(c.id) && p.status==='ringing') && (!addParticipantQuery || c.name.toLowerCase().includes(addParticipantQuery.toLowerCase())))" :key="c.id">
<div class="call-card" @click="addParticipantToCall(c)">
<div class="call-card-avatar">
<img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="">
<span v-else>@{{ getAuthorInitial(c.name) }}</span>
</div>
<div class="call-card-body">
<div class="call-card-name">@{{ c.name }}</div>
<div class="call-card-meta" style="font-size:11px;color:var(--muted);">انقر للدعوة</div>
</div>
<i class="ri-phone-line" style="color:var(--gold);font-size:16px;opacity:.7;"></i>
</div>
</template>
<div v-if="(contacts||[]).filter(c=>Number(c.id)!==-1&&!callParticipants.some(p=>Number(p.id)===Number(c.id))&&!pendingInvites.some(p=>Number(p.id)===Number(c.id)&&p.status==='ringing')).length===0" style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">
  لا يوجد مزيد من جهات الاتصال لإضافتها
</div>
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
<script src="/js/hms.umd.js"></script>

<script>
</script>
