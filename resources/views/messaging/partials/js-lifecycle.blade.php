mounted() {

this.init();

this.loadCallSettings();
this.loadCallLogs(); // pre-load so call cards appear in chat on open
this.callLogsLoaded = true;
this.initCallSignaling();
this.startIncomingCallPoll();

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

// Handle ?section=contacts from dashboard shortcut
const _urlSection = new URLSearchParams(window.location.search).get('section');
if (_urlSection === 'contacts') {
    this.showSidebar = true;
    this.railFilter = 'private';
    history.replaceState({}, '', window.location.pathname);
}

const feed = this.$refs.messagesContainer;

if (feed) feed.style.background = this.chatBackground || '';

// Start SSE instead of delta polling

if (this.selectedContact) {

this.startSSE();

this.scheduleDeltaRefresh();

}

// Sync desktop notification permission state with browser reality
if ('Notification' in window && Notification.permission !== 'granted') {
    this.settingsNotifications.desktopEnabled = false;
    this.settingNotifyEnabled = false;
}

// Background contacts poll for desktop notifications
this.startContactsBgPoll();

// Activity ping: lightweight and presence-friendly

this.pingTimer = setInterval(() => {

fetch('/activity/ping', {

method: 'POST',

headers: { 'X-CSRF-TOKEN': this.csrfToken },

keepalive: true,

}).catch(() => {});

}, 30000);

// Resume SSE when tab becomes visible again

document.addEventListener('visibilitychange', () => {

if (!document.hidden && this.selectedContact && !this.refreshTimer) {

this.startSSE();

this.scheduleDeltaRefresh();

}

// Restart call poll when tab becomes visible so missed incoming calls are caught immediately
if (!document.hidden && !this.callState && !this._callPollTimer) {
this.startIncomingCallPoll();
}

});

// Network offline → show warning immediately without waiting for HMS 10s timeout
window.addEventListener('offline', () => {
if (this.callState === 'in-call' || this.callState === 'calling') {
    this.callConnectionWarning = true;
    console.log('[NET] offline – call connection warning shown');
}
});

// Network online → reconnect immediately instead of waiting for 10s HMS timer
window.addEventListener('online', () => {
console.log('[NET] online – restoring call services');
// Restart poll if not in a call
if (!this.callState && !this._callPollTimer) this.startIncomingCallPoll();
// If we were showing a connection warning during a call, try to rejoin immediately
if (this.callConnectionWarning && (this.callState === 'in-call' || this.callState === 'calling') && this.currentCallId) {
    if (this._hmsReconnectTimer) { clearTimeout(this._hmsReconnectTimer); this._hmsReconnectTimer = null; }
    // Brief pause (500ms) for OS network stack to stabilise before rejoining
    setTimeout(() => {
        if ((this.callState === 'in-call' || this.callState === 'calling') && this.currentCallId) {
            console.log('[NET] immediate rejoin after network restored');
            this.hmsJoinRoom(this.currentCallId, this.callType);
        }
    }, 500);
}
});

// Audio device change (headphones plugged/unplugged) → switch HMS to new default device
if (navigator.mediaDevices?.addEventListener) {
navigator.mediaDevices.addEventListener('devicechange', async () => {
    if ((this.callState !== 'in-call' && this.callState !== 'calling') || !window._hmsActions) return;
    try {
        console.log('[DEVICE] device change detected – switching to new default audio device');
        await window._hmsActions.setAudioSettings({ deviceId: 'default' });
    } catch (e) {
        console.warn('[DEVICE] setAudioSettings failed:', e.message);
    }
});
}

// Tab/window close mid-call → end call on server via keepalive beacon
window.addEventListener('beforeunload', () => {
if (this.currentCallId) {
    const endUrl = this.callRouteFor(this.callsEndRouteTemplate, this.currentCallId);
    // Use URL-encoded body so Laravel's CSRF middleware reads _token from the body
    const body = '_token=' + encodeURIComponent(this.csrfToken);
    navigator.sendBeacon(endUrl, new Blob([body], { type: 'application/x-www-form-urlencoded' }));
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

if (this._contactsBgPollTimer) { clearInterval(this._contactsBgPollTimer); this._contactsBgPollTimer = null; }

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