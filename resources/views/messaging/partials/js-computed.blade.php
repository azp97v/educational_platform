computed: {

remoteVadSpread() {
const l = this.remoteAudioLevel;
if (l < 8) return '3px';
return Math.min(16, Math.round(3 + (l / 100) * 13)) + 'px';
},
remoteVadAlpha() {
const l = this.remoteAudioLevel;
if (l < 8) return '0';
return Math.min(0.75, 0.25 + (l / 100) * 0.5).toFixed(2);
},

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
    const ta = new Date(a.lastMessageTime || a.lastSeenAt || 0).getTime();
    const tb = new Date(b.lastMessageTime || b.lastSeenAt || 0).getTime();
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
return this.myStatusHistory.length ? this.myStatusHistory : (this.myStatuses || []);
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