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

normalizeGroupMessage(msg) {
if (!msg) return null;
const n = {
id: msg.id,
groupId: msg.groupId ?? msg.group_id,
senderId: msg.senderId ?? msg.sender_id,
senderName: msg.senderName ?? msg.sender_name ?? '',
senderAvatar: msg.senderAvatar ?? msg.sender_avatar ?? null,
recipientId: null,
content: msg.content ?? '',
attachmentUrl: msg.attachmentUrl ?? msg.attachment_url ?? null,
attachmentName: msg.attachmentName ?? msg.attachment_name ?? null,
attachmentMime: msg.attachmentMime ?? msg.attachment_type ?? null,
attachmentKind: msg.attachmentKind ?? msg.attachment_kind ?? null,
audioPath: msg.audioPath ?? msg.audio_path ?? null,
audioDuration: msg.audioDuration ?? msg.audio_duration ?? 0,
messageType: msg.messageType ?? msg.message_type ?? 'text',
isEdited: !!(msg.isEdited ?? msg.is_edited ?? false),
replyToId: msg.replyToId ?? msg.reply_to ?? null,
createdAt: msg.createdAt ?? msg.created_at ?? new Date().toISOString(),
isGroup: true,
pending: false,
failed: false,
isPlaying: false,
currentBar: 0,
playbackPosition: 0,
_resumePos: 0,
mediaLoaded: true,
readAt: null,
};
// Derive actual display type from MIME (mirrors normalizeMessage behavior)
n.messageType = this.getMessageType(n);
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

hasConversation: true,

selected: false,

};

this.contacts.unshift(newGroup);

this.showToast(`تم إنشاء قروب "${d.data.name}" بنجاح \u2713`, 'success');

this.selectContact(newGroup);

})

.catch(err => {

this.showToast(`فشل إنشاء القروب: ${err.message || 'خطأ غير متوقع'}`, 'error');

})

.finally(() => {

this.groupCreateLoading = false;

});

},