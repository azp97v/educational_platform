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