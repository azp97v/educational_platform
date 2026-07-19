// ===== GROUP INFO / MANAGEMENT =====

openGroupInfo() {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
this.groupInfoOpen = true;
this.groupInfoEditing = false;
this.groupInfoSubView = null;
this.groupMemberSearch = '';
this.groupInfoNameEditing = false;
this.groupInfoDescEditing = false;
this.groupMediaCount = 0;
this.groupAddMemberSearch = '';
this.groupAddMemberResults = [];
this.groupInfoData = { name: contact.name, description: contact.description || '', avatar_url: contact.avatar_url, members_count: contact._membersCount || 0, only_admins_can_message: false, who_can_send: 'all', who_can_add_members: 'admins', who_can_edit_info: 'admins' };
this.groupInfoIsAdmin = !!contact._isAdmin;
this.groupInfoMembers = [];
this.groupInfoLoading = true;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const infoUrl = baseUrl + '/messaging/group/' + groupId + '/info';
fetch(infoUrl, { headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } })
.then(r => r.json())
.then(d => {
if (d.success) {
this.groupInfoData = { ...d.group, who_can_send: d.group.who_can_send || 'all', who_can_add_members: d.group.who_can_add_members || 'admins', who_can_edit_info: d.group.who_can_edit_info || 'admins' };
this.groupInfoMembers = d.members || [];
this.groupInfoIsAdmin = !!d.isAdmin;
this.groupInviteUrl = d.group.invite_url || '';
this.groupIsAnnouncement = !!(d.group.only_admins_can_message);
this.groupMediaCount = d.group.media_count || 0;
if (this.selectedContact && this.selectedContact._groupId === groupId) {
this.selectedContact._isAdmin = !!d.isAdmin;
this.selectedContact._membersCount = (d.members || []).length;
}
}
}).catch(()=>{}).finally(() => { this.groupInfoLoading = false; });
},

openGroupSettings() { this.openGroupInfo(); },

async saveGroupInfo() {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const settingsUrl = baseUrl + '/messaging/group/' + groupId + '/settings';
try {
const r = await fetch(settingsUrl, {
method: 'PUT',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ name: this.groupInfoNameEdit, description: this.groupInfoDescEdit })
});
const d = await r.json();
if (d.success) {
this.groupInfoData.name = d.group.name;
this.groupInfoData.description = d.group.description;
if (d.group.avatar_url) this.groupInfoData.avatar_url = d.group.avatar_url;
const ci = this.contacts.findIndex(c => c.isGroup && c._groupId === groupId);
if (ci !== -1) { this.contacts[ci].name = d.group.name; this.contacts[ci].description = d.group.description; if (d.group.avatar_url) this.contacts[ci].avatar_url = d.group.avatar_url; }
if (this.selectedContact && this.selectedContact._groupId === groupId) { this.selectedContact.name = d.group.name; }
this.showToast('تم تحديث إعدادات المجموعة', 'success');
this.groupInfoEditing = false;
} else { this.showToast('فشل التحديث', 'error'); }
} catch(e) { this.showToast('خطأ في التحديث', 'error'); }
},

async onGroupAvatarChange(event) {
const file = event.target.files[0];
if (!file) return;
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const settingsUrl = baseUrl + '/messaging/group/' + groupId + '/settings';
const fd = new FormData(); fd.append('avatar', file); fd.append('_method', 'PUT');
try {
const r = await fetch(settingsUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }, body: fd });
const d = await r.json();
if (d.success && d.group.avatar_url) {
this.groupInfoData.avatar_url = d.group.avatar_url;
const ci = this.contacts.findIndex(c => c.isGroup && c._groupId === groupId);
if (ci !== -1) this.contacts[ci].avatar_url = d.group.avatar_url;
if (this.selectedContact && this.selectedContact._groupId === groupId) this.selectedContact.avatar_url = d.group.avatar_url;
this.showToast('تم تغيير صورة المجموعة', 'success');
} else { this.showToast('فشل رفع الصورة', 'error'); }
} catch(e) { this.showToast('خطأ في رفع الصورة', 'error'); }
},

searchGroupAddMember() {
clearTimeout(this._groupAddMemberTimer);
const q = (this.groupAddMemberSearch || '').trim();
if (!q) { this.groupAddMemberResults = []; return; }
this._groupAddMemberTimer = setTimeout(async () => {
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const searchUrl = baseUrl + '/messaging/users/search';
try {
const r = await fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const d = await r.json();
this.groupAddMemberResults = (d.users || d.data || []).slice(0, 8);
} catch(e) { this.groupAddMemberResults = []; }
}, 300);
},

async addGroupMember(user) {
if (this.groupInfoMembers.some(m => m.id === user.id)) { this.showToast('المستخدم عضو بالفعل', 'info'); return; }
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const addUrl = baseUrl + '/messaging/group/' + groupId + '/members';
try {
const r = await fetch(addUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ user_id: user.id }) });
const d = await r.json();
if (d.success) {
this.groupInfoMembers.push(d.member);
this.groupInfoData.members_count = (this.groupInfoData.members_count || 0) + 1;
const ci = this.contacts.findIndex(c => c.isGroup && c._groupId === groupId);
if (ci !== -1) this.contacts[ci]._membersCount = this.groupInfoData.members_count;
if (this.selectedContact && this.selectedContact._groupId === groupId) this.selectedContact._membersCount = this.groupInfoData.members_count;
this.groupAddMemberSearch = ''; this.groupAddMemberResults = [];
this.showToast('تمت الإضافة بنجاح', 'success');
} else { this.showToast(d.message || 'فشل الإضافة', 'error'); }
} catch(e) { this.showToast('خطأ في الإضافة', 'error'); }
},

async removeGroupMember(member) {
if (!confirm('هل تريد إزالة ' + member.name + ' من المجموعة؟')) return;
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const removeUrl = baseUrl + '/messaging/group/' + groupId + '/members/' + member.id;
try {
const r = await fetch(removeUrl, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const d = await r.json();
if (d.success) {
this.groupInfoMembers = this.groupInfoMembers.filter(m => m.id !== member.id);
this.groupInfoData.members_count = Math.max(0, (this.groupInfoData.members_count || 1) - 1);
const ci = this.contacts.findIndex(c => c.isGroup && c._groupId === groupId);
if (ci !== -1) this.contacts[ci]._membersCount = this.groupInfoData.members_count;
if (this.selectedContact && this.selectedContact._groupId === groupId) this.selectedContact._membersCount = this.groupInfoData.members_count;
this.showToast('تمت الإزالة', 'success');
} else { this.showToast(d.message || 'فشل الحذف', 'error'); }
} catch(e) { this.showToast('خطأ في الحذف', 'error'); }
},

async changeGroupMemberRole(member, role) {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const roleUrl = baseUrl + '/messaging/group/' + groupId + '/members/' + member.id + '/role';
try {
const r = await fetch(roleUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ role }) });
const d = await r.json();
if (d.success) {
const m = this.groupInfoMembers.find(m => m.id === member.id);
if (m) { m.role = role; m.isAdmin = role === 'admin'; }
this.showToast(role === 'admin' ? 'تم التعيين كمشرف' : 'تم إلغاء الإشراف', 'success');
} else { this.showToast('فشل تغيير الصلاحية', 'error'); }
} catch(e) { this.showToast('خطأ', 'error'); }
},

async startGroupCall(type) {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
let members = this.groupInfoMembers && this.groupInfoMembers.length ? this.groupInfoMembers : [];
if (!members.length) {
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/info', { headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const d = await r.json();
members = d.members || [];
} catch(e) { members = []; }
}
const participants = members.filter(m => m.id !== this.currentUserId);
if (!participants.length) { this.showToast('لا يوجد أعضاء آخرون للاتصال بهم', 'info'); return; }
// Pass group_id so CallController skips messaging-privacy checks
this._pendingGroupCallId = groupId;
await this.startCall(type, participants);
this._pendingGroupCallId = null;
},

async generateGroupInviteLink() {
const groupId = this.selectedContact?._groupId;
if (!groupId) return;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/invite', {
method: 'POST',
headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
});
const d = await r.json();
if (d.success) { this.groupInviteUrl = d.url; this.showToast('تم إنشاء رابط الدعوة', 'success'); }
else { this.showToast('فشل إنشاء الرابط', 'error'); }
} catch(e) { this.showToast('خطأ في إنشاء الرابط', 'error'); }
},

async revokeGroupInviteLink(fullyRevoke) {
const groupId = this.selectedContact?._groupId;
if (!groupId) return;
if (fullyRevoke && !confirm('هل تريد إلغاء رابط الدعوة؟ لن يعمل الرابط القديم بعد الإلغاء.')) return;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
if (fullyRevoke) {
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/invite', {
method: 'DELETE',
headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
});
const d = await r.json();
if (d.success) { this.groupInviteUrl = ''; this.showToast('تم إلغاء رابط الدعوة', 'success'); }
} catch(e) { this.showToast('خطأ في إلغاء الرابط', 'error'); }
} else {
await this.generateGroupInviteLink();
}
},

copyGroupInviteLink() {
if (!this.groupInviteUrl) return;
try {
navigator.clipboard.writeText(this.groupInviteUrl);
this.showToast('تم نسخ رابط الدعوة', 'success');
} catch(e) {
const ta = document.createElement('textarea');
ta.value = this.groupInviteUrl;
document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
this.showToast('تم نسخ رابط الدعوة', 'success');
}
},

async toggleGroupAnnouncement() {
const groupId = this.selectedContact?._groupId;
if (!groupId || !this.groupInfoIsAdmin) return;
const newVal = !this.groupIsAnnouncement;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/settings', {
method: 'PUT',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ only_admins_can_message: newVal })
});
const d = await r.json();
if (d.success) {
this.groupIsAnnouncement = newVal;
this.groupInfoData.only_admins_can_message = newVal;
this.showToast(newVal ? 'وضع الإعلانات مُفعَّل — فقط المشرفون يرسلون' : 'وضع الإعلانات مُعطَّل', 'success');
} else { this.showToast('فشل تغيير الإعداد', 'error'); }
} catch(e) { this.showToast('خطأ في الإعداد', 'error'); }
},

async saveGroupName() {
const name = (this.groupInfoNameEdit || '').trim();
if (!name) return;
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/settings', {
method: 'PUT',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ name })
});
const d = await r.json();
if (d.success) {
this.groupInfoData.name = d.group.name;
this.groupInfoNameEdit = d.group.name;
const ci = this.contacts.findIndex(c => c.isGroup && c._groupId === groupId);
if (ci !== -1) this.contacts[ci].name = d.group.name;
if (this.selectedContact && this.selectedContact._groupId === groupId) this.selectedContact.name = d.group.name;
this.showToast('تم تحديث اسم المجموعة', 'success');
this.groupInfoNameEditing = false;
} else { this.showToast('فشل التحديث', 'error'); }
} catch(e) { this.showToast('خطأ في التحديث', 'error'); }
},

async saveGroupDesc() {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/settings', {
method: 'PUT',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ description: this.groupInfoDescEdit })
});
const d = await r.json();
if (d.success) {
this.groupInfoData.description = d.group.description;
this.groupInfoDescEdit = d.group.description || '';
this.showToast('تم تحديث الوصف', 'success');
this.groupInfoDescEditing = false;
} else { this.showToast('فشل التحديث', 'error'); }
} catch(e) { this.showToast('خطأ في التحديث', 'error'); }
},

async saveGroupPermission(key, value) {
const groupId = this.selectedContact?._groupId;
if (!groupId || !this.groupInfoIsAdmin) return;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
try {
const r = await fetch(baseUrl + '/messaging/group/' + groupId + '/permissions', {
method: 'POST',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ [key]: value })
});
const d = await r.json();
if (d.success) {
this.groupInfoData[key] = value;
if (key === 'who_can_send') {
this.groupIsAnnouncement = value === 'admins';
this.groupInfoData.only_admins_can_message = value === 'admins';
if (this.selectedContact) this.selectedContact.only_admins_can_message = value === 'admins';
}
this.showToast('تم تحديث الإعداد', 'success');
} else { this.showToast(d.message || 'فشل التحديث', 'error'); }
} catch(e) { this.showToast('خطأ في التحديث', 'error'); }
},

async deleteGroup() {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
if (!confirm('هل أنت متأكد من حذف المجموعة نهائياً؟ لا يمكن التراجع عن هذا الإجراء.')) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const deleteUrl = baseUrl + '/messaging/group/' + groupId;
try {
const r = await fetch(deleteUrl, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const d = await r.json();
if (d.success) {
this.groupInfoOpen = false;
this.contacts = this.contacts.filter(c => !(c.isGroup && c._groupId === groupId));
if (this.selectedContact && this.selectedContact._groupId === groupId) { this.selectedContact = null; this.messages = []; }
this.showToast('تم حذف المجموعة', 'success');
} else { this.showToast('فشل حذف المجموعة', 'error'); }
} catch(e) { this.showToast('خطأ في الحذف', 'error'); }
},

async leaveGroup() {
const contact = this.selectedContact;
if (!contact || !contact.isGroup) return;
if (!confirm('هل تريد مغادرة هذه المجموعة؟')) return;
const groupId = contact._groupId;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const removeUrl = baseUrl + '/messaging/group/' + groupId + '/members/' + this.currentUserId;
try {
const r = await fetch(removeUrl, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' } });
const d = await r.json();
if (d.success) {
this.groupInfoOpen = false;
this.contacts = this.contacts.filter(c => !(c.isGroup && c._groupId === groupId));
if (this.selectedContact && this.selectedContact._groupId === groupId) {
this.selectedContact = null;
this.messages = [];
}
this.showToast('غادرت المجموعة', 'info');
} else { this.showToast(d.message || 'فشل المغادرة', 'error'); }
} catch(e) { this.showToast('خطأ في المغادرة', 'error'); }
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

if (!contact.isGroup) this.loadWallpaperForContact(contact.id);
else {
// For groups: restore wallpaper from localStorage directly
this.$nextTick(() => this.applyChatThemeVars());
// Sync admin + announcement state from contact payload
this.groupInfoIsAdmin = !!contact._isAdmin;
this.groupIsAnnouncement = !!(contact.only_admins_can_message);
this.groupInviteUrl = '';
this.groupInfoMembers = [];
}

this.$nextTick(() => this.applyChatThemeVars());

// Fetch partner E2E key for individual contacts only
if (!contact.isGroup) this.fetchPartnerE2EKey(contact.id);
else { this.e2eEnabled = false; this.e2ePartnerHasKey = false; }

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

// Handle group message send
if (this.selectedContact && this.selectedContact.isGroup) {
const groupId = this.selectedContact._groupId;
if (!groupId || (!text && !this.pendingAttachments.length)) return;
this.messageInput = '';
this.showEmojiPicker = false;
const baseUrl = window.location.pathname.startsWith('/teacher') ? '/teacher' : '';
const groupSendUrl = @json(
auth()->user()->role === 'teacher'
? (\Illuminate\Support\Facades\Route::has('teacher.messaging.group.send') ? route('teacher.messaging.group.send', ['groupId' => '__GID__']) : null)
: (\Illuminate\Support\Facades\Route::has('student.messaging.group.send') ? route('student.messaging.group.send', ['groupId' => '__GID__']) : null)
);
const groupFileUrl = baseUrl + '/messaging/group/' + groupId + '/file';

const pendingFiles = [...this.pendingAttachments];
// Clear the UI bar immediately but defer URL revocation until uploads complete
this.pendingAttachments = [];

// If files present: send each file, first file gets the text as caption
if (pendingFiles.length > 0) {
for (let i = 0; i < pendingFiles.length; i++) {
const att = pendingFiles[i];
const caption = (i === 0 && text) ? text : '';
const tempId = this.pendingMessageCounter--;
const tempMsg = this.normalizeGroupMessage({ id: tempId, group_id: groupId, senderId: this.currentUserId, senderName: this.userName || '', content: caption || att.name, attachmentMime: att.mime || '', attachmentName: att.name, createdAt: new Date().toISOString(), isGroup: true });
tempMsg.pending = true;
tempMsg.attachmentUrl = att.previewUrl || null;
this.messages.push(tempMsg);
this.normalizeMessageOrder();
this.scrollToBottom(true);
try {
const fd = new FormData();
fd.append('file', att.file);
if (caption) fd.append('content', caption);
if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);
const r = await fetch(groupFileUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }, body: fd });
const d = await r.json();
if (d.success) {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) { this.messages[idx] = this.normalizeGroupMessage(d.message); this.messages[idx].pending = false; }
this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(d.message.id || 0));
} else {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) this.messages[idx].failed = true;
this.showToast(d.message || 'فشل إرسال المرفق', 'error');
}
} catch(_) {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) this.messages[idx].failed = true;
this.showToast('تعذر إرسال المرفق', 'error');
}
}
// Revoke blob URLs now that uploads are done
pendingFiles.forEach(a => { if (a.previewUrl) URL.revokeObjectURL(a.previewUrl); });
this.replyingToMessage = null;
this.updateContactPreview();
this.scrollToBottom(true);
return;
}

// Text-only group message
if (!groupSendUrl) { this.showToast('Group send route not configured.', 'error'); return; }
const tempId = this.pendingMessageCounter--;
const tempMsg = this.normalizeGroupMessage({ id: tempId, group_id: groupId, senderId: this.currentUserId, senderName: this.userName || '', content: text, messageType: 'text', createdAt: new Date().toISOString(), isGroup: true });
tempMsg.pending = true;
this.messages.push(tempMsg);
this.normalizeMessageOrder();
this.scrollToBottom(true);
this.updateContactPreview();
try {
const r = await fetch(groupSendUrl.replace('__GID__', groupId), {
method: 'POST',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
body: JSON.stringify({ content: text, reply_to: this.replyingToMessage?.id || null })
});
const d = await r.json();
if (d.success) {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) { this.messages[idx] = this.normalizeGroupMessage(d.message); this.messages[idx].pending = false; }
this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(d.message.id || 0));
} else {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) this.messages[idx].failed = true;
this.showToast(d.message || 'فشل إرسال الرسالة', 'error');
}
} catch(_) {
const idx = this.messages.findIndex(m => Number(m.id) === Number(tempId));
if (idx !== -1) this.messages[idx].failed = true;
this.showToast('تعذر إرسال الرسالة', 'error');
}
this.replyingToMessage = null;
return;
}

this.messageInput = '';

this.showEmojiPicker = false;

this.showRecordHint = false;

const recipientId = Number(this.selectedContact?.id || 0);

if (!recipientId) return;

// Only send text as a standalone message when there are no pending file attachments.
// When files are present, the text becomes the caption for the first file (handled below).
if (text && !this.pendingAttachments.length) {

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
const fileCaption = text; // text is caption for first attachment (may be empty)

for (let _fi = 0; _fi < files.length; _fi++) {
const _att = files[_fi];
const _cap = (_fi === 0 && fileCaption) ? fileCaption : '';
await this.enqueueSend(async () => this.sendFileMessage(_att.file, _cap));
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

async sendFileMessage(file, caption) {

if (!file || !this.selectedContact) return;

const displayContent = (caption && caption.trim()) ? caption.trim() : (file.name || '\u0645\u0631\u0641\u0642');
const msg = this.normalizeMessage({
id: this.pendingMessageCounter--,
sender_id: this.currentUserId,
recipient_id: this.selectedContact.id,
content: displayContent,
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

if (caption && caption.trim()) fd.append('content', caption.trim());

if (this.replyingToMessage?.id) fd.append('reply_to', this.replyingToMessage.id);

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

if (this.selectedContact.isGroup) {
this.selectedContact.lastMessage = (last.senderName ? last.senderName + ': ' : '') + (last.content || '').substring(0, 60);
} else {
this.selectedContact.lastMessage = this.getMessagePreviewText(last);
}

const newTime = last.createdAt || last.created_at || new Date().toISOString();
this.selectedContact.lastMessageTime = newTime;
this.selectedContact.lastSeenAt = newTime;

this.selectedContact.lastMessageStatusRefId = last.statusRefId || null;

const ci = this.contacts.findIndex(c => this.selectedContact.isGroup ? (c.isGroup && c._groupId === this.selectedContact._groupId) : Number(c.id) === Number(this.selectedContact.id));

if (ci !== -1) {

this.contacts[ci].lastMessage = this.selectedContact.lastMessage;

this.contacts[ci].lastMessageTime = newTime;

this.contacts[ci].lastSeenAt = newTime;

this.contacts[ci].lastMessageStatusRefId = this.selectedContact.lastMessageStatusRefId;

} else if (this.selectedContact.lastMessageTime) {

this.contacts.unshift({ ...this.selectedContact });

this.potentialNewContacts = this.potentialNewContacts.filter(c => Number(c.id) !== Number(this.selectedContact.id));

}

},

async loadMessages(initial = false) {

if (!this.selectedContact) return;

// Handle group conversations separately
if (this.selectedContact.isGroup) {
const groupId = this.selectedContact._groupId;
if (!groupId) return;
const groupLoadUrl = @json(
auth()->user()->role === 'teacher'
? (\Illuminate\Support\Facades\Route::has('teacher.messaging.group.load') ? route('teacher.messaging.group.load', ['groupId' => '__GID__']) : null)
: (\Illuminate\Support\Facades\Route::has('student.messaging.group.load') ? route('student.messaging.group.load', ['groupId' => '__GID__']) : null)
);
if (!groupLoadUrl) { this.showToast('Group load route not configured.', 'error'); return; }
try {
const r2 = await fetch(groupLoadUrl.replace('__GID__', groupId), {
method: 'GET', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
});
const d2 = await r2.json();
if (d2.success) {
this.messages = (d2.messages || []).map(m => this.normalizeGroupMessage(m));
this.lastKnownMessageId = this.messages.length ? Math.max(...this.messages.map(m => Number(m.id) || 0)) : 0;
this.scrollToBottom(false);
}
} catch(_) { this.showToast('تعذر تحميل رسائل المجموعة', 'error'); }
return;
}

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

} else {

// Images may still be loading and change scrollHeight; re-apply once after media settles.
// Cancelled by onFeedScroll if the user scrolls manually first.
if (this._scrollRestoreTimer) clearTimeout(this._scrollRestoreTimer);
this._pendingScrollRestore = true;
this._scrollRestoreTimer = setTimeout(() => {
if (this._pendingScrollRestore) {
this._pendingScrollRestore = false;
this.restoreSavedScrollPosition();
}
}, 500);

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
if (this.selectedContact.isGroup) return; // no typing for groups

if (!this.typingPingTimer) {

this.sendTypingState(true);

this.typingPingTimer = setTimeout(() => { this.typingPingTimer = null; }, 2500);

}

if (this.typingStopTimer) clearTimeout(this.typingStopTimer);

this.typingStopTimer = setTimeout(() => this.sendTypingState(false), 3500);

},

getTypingLabel(contact, short = false) {
const mt = contact?.typingMediaType;
if (mt === 'image') return short ? 'يرسل صورة' : 'يرسل صورة...';
if (mt === 'video') return short ? 'يرسل فيديو' : 'يرسل فيديو...';
if (mt === 'audio') return short ? 'يرسل صوتية' : 'يرسل رسالة صوتية...';
if (mt === 'file') return short ? 'يرسل ملفاً' : 'يرسل ملفاً...';
return short ? 'يكتب' : 'يكتب الآن...';
},

sendTypingState(isTyping, mediaType = null) {

if (!this.selectedContact || !this.typingRoute || this.typingRoute === '#') return;
if (this.selectedContact.isGroup) return; // no typing for groups

fetch(this.typingRoute, {

method: 'POST',

headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },

body: JSON.stringify({ recipient_id: Number(this.selectedContact.id), is_typing: !!isTyping, media_type: mediaType || undefined }),

}).catch(() => {});

},

async deltaRefresh() {

if (!this.selectedContact || this.deltaInFlight) return;
if (Number(this.selectedContact.id) === -1) return; // skip delta for saved messages

// Group delta path
if (this.selectedContact.isGroup) {
const groupId = this.selectedContact._groupId;
if (!groupId) return;
const groupDeltaUrl = @json(
auth()->user()->role === 'teacher'
? (\Illuminate\Support\Facades\Route::has('teacher.messaging.group.delta') ? route('teacher.messaging.group.delta', ['groupId' => '__GID__']) : null)
: (\Illuminate\Support\Facades\Route::has('student.messaging.group.delta') ? route('student.messaging.group.delta', ['groupId' => '__GID__']) : null)
);
if (!groupDeltaUrl) return;
this.deltaInFlight = true;
try {
const r2 = await fetch(groupDeltaUrl.replace('__GID__', groupId) + '?after_id=' + (this.lastKnownMessageId || 0), {
method: 'GET', headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
});
const d2 = await r2.json();
if (d2.success && this.selectedContact && this.selectedContact.isGroup && this.selectedContact._groupId === groupId) {
const incoming = d2.messages || [];
let changed = false;
for (const raw of incoming) {
const msg = this.normalizeGroupMessage(raw);
if (!this.messages.some(m => Number(m.id) === Number(msg.id))) {
this.messages.push(msg);
this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(msg.id || 0));
changed = true;
if (Number(msg.senderId) !== Number(this.currentUserId)) {
this.playNotificationTone(this.selectedContact.id);
}
}
}
if (changed) { this.normalizeMessageOrder(); this.scrollToBottom(false); this.updateContactPreview(); }
}
} catch(_) {} finally { this.deltaInFlight = false; }
return;
}

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

this._pendingScrollRestore = false;

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

if (files.length) {
const f = files[0];
const mt = f.type.startsWith('image/') ? 'image' : f.type.startsWith('video/') ? 'video' : f.type.startsWith('audio/') ? 'audio' : 'file';
this.sendTypingState(true, mt);
}

const mediaFiles = files.filter(f => f.type.startsWith('image/') || f.type.startsWith('video/'));
const otherFiles = files.filter(f => !f.type.startsWith('image/') && !f.type.startsWith('video/'));

// Non-media files: add directly
otherFiles.forEach(file => {
if (file.size > 512 * 1024 * 1024) {
this.showToast('الملف كبير جداً (الحد الأقصى 512 MB)', 'error'); return;
}
this.addPendingAttachment(file);
});

if (mediaFiles.length === 0) {
e.target.value = ''; return;
}

// Single media file: open editor as before
if (mediaFiles.length === 1) {
const file = mediaFiles[0];
if (file.size > 512 * 1024 * 1024) {
this.showToast('الملف كبير جداً (الحد الأقصى 512 MB)', 'error');
} else if (file.type.startsWith('video/')) {
this.openVideoEditor(file);
} else {
this.openImageEditor(file);
}
e.target.value = ''; return;
}

// Multiple media files: add all directly with edit button available per attachment
mediaFiles.forEach(file => {
if (file.size > 512 * 1024 * 1024) {
this.showToast(`الملف "${file.name}" كبير جداً (512 MB كحد أقصى)`, 'error'); return;
}
this.addPendingAttachment(file, true);
});
if (mediaFiles.length > 1) {
this.showToast(`تم إضافة ${mediaFiles.length} ملفات — اضغط ✏️ لتعديل أي منها`, 'info');
}

e.target.value = '';

},

openAttachmentPreview(att) {
if (!att?.previewUrl) return;
const type = att.previewType || 'file';
if (type !== 'image' && type !== 'video') return;
// Build a synthetic message object for the media modal
const syntheticMsg = {
id: 'preview_' + Date.now(),
attachmentUrl: att.previewUrl,
attachmentName: att.name || '',
attachmentMime: att.mime || '',
messageType: type,
content: att.name || '',
senderId: this.currentUserId,
createdAt: new Date().toISOString(),
};
this.mediaModal = {
url: att.previewUrl,
type,
name: att.name || '',
caption: '',
isSticker: false,
senderName: this.userName || '',
senderInitial: this.getAuthorInitial(this.userName || ''),
senderAvatar: null,
metaTime: '',
message: syntheticMsg,
index: 0,
};
this.mediaModalList = [syntheticMsg];
this.mediaModalMoreOpen = false;
},

addPendingAttachment(file, editable = false) {

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

editable: editable && (file.type.startsWith('image/') || file.type.startsWith('video/')),

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

audio.preload = 'metadata';

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
                audio.preload = 'metadata';
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
                a2.preload = 'metadata';
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
                a2.preload = 'metadata';
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
a.preload = 'metadata';
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
// إعادة فك تشفير الرسائل المحمّلة سابقاً — يحلّ سباق تحميل الرسائل قبل مفتاح الشريك
if (this.messages && this.messages.length) {
    this._decryptConversationMessages();
}
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
} catch (_) { return 'e2e:__decrypt_failed__'; }
},

// يفحص إذا كان محتوى الرسالة لا يزال بصيغة e2e المشفّرة (لم يُفكّ)
isStillEncrypted(content) {
    return typeof content === 'string' && content.startsWith('e2e:');
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
// Prevent double-save: if sticker is already in library, switch to favorite toggle
if (this.findStickerByUrl(message.attachmentUrl)) {
this.toggleStickerFavoriteFromMessage(message);
return;
}
this.stickerSaveBusy = true;
try {
const res = await fetch(message.attachmentUrl);
const blob = await res.blob();
const isAnimated = message.messageType === 'sticker_animated';
const fd = new FormData();
fd.append('type', isAnimated ? 'animated' : 'static');
fd.append('file', blob, isAnimated ? 'sticker.webm' : 'sticker.png');
// Pass source URL so backend can reference the same file (no disk duplication)
fd.append('source_url', message.attachmentUrl);
const saveRes = await fetch(this.stickersStoreRoute, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken }, body: fd });
const d = await saveRes.json();
if (d.success) {
if (d.already_saved) {
// Sticker was already in library (race condition or second attempt)
if (!this.stickerList.some(s => s.id === d.data.id)) {
this.stickerList = [d.data, ...this.stickerList];
}
this.showToast('الملصق موجود بالفعل في مكتبتك', 'info');
} else {
this.stickerList = [d.data, ...this.stickerList];
this.showToast('تم حفظ الملصق في مكتبتك ✓', 'success');
}
this.stickerViewer = null;
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
// Strip query params; also strip domain so storage/stickers/x.png matches https://domain.com/storage/stickers/x.png
const norm = (u) => {
const bare = String(u).split('?')[0];
try { return new URL(bare).pathname; } catch { return bare; }
};
const normUrl = norm(url);
const match = (s) => norm(s.url) === normUrl;
return this.stickerList.find(match)
|| this.stickerFavorites.find(match)
|| this.stickerRecent.find(match)
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
const blob = await removeBackground(file, {
    publicPath: 'https://staticimgly.com/@imgly/background-removal-data/1.6.0/dist/',
});
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

statusViewerFilter(status) {
    const map = {
        warm: 'sepia(0.4) saturate(1.3) brightness(1.05)',
        cool: 'hue-rotate(180deg) saturate(0.9)',
        bw:   'grayscale(1)',
        soft: 'brightness(1.1) saturate(0.8)',
    };
    return map[status?.filterStyle || status?.filter_style] || '';
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
this.myProfileEditUsername = saved.username || this.settingsAccount?.username || '';
this.myProfileEditPhone = saved.phone || this.currentUserPhone || '';
this.myProfileEditBio = saved.bio || '';
this.myProfileBirthDate = saved.birthDate || '';
this.myProfileAvatar = saved.avatar || this.normalizeAvatarUrl(this.currentUserAvatar) || null;
this.myProfileBannerScale = 1;
this.myProfileOpen = true;
this.accountDrawerOpen = false;
this.fetchMyStatusHistory();

},

async fetchMyStatusHistory() {
const ROUTE = @json($statusHistoryRoute ?? '#');
if (!ROUTE || ROUTE === '#') return;
try {
    const r = await fetch(ROUTE, { headers: { 'Accept': 'application/json' } });
    const j = await r.json();
    if (j.success && Array.isArray(j.data)) {
        this.myStatusHistory = j.data.sort((a, b) => new Date(b.createdAt || b.created_at) - new Date(a.createdAt || a.created_at));
    }
} catch(e) {}
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
this.$nextTick(() => this.scrollToBottom(true));
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
const grid = document.createElement('div');
grid.className = 'media-gallery-grid';
grid.style.cssText = 'display:grid;grid-template-columns:repeat(3,1fr);gap:6px;padding:12px;max-height:60vh;overflow:auto;';
msgs.forEach(m => {
if (m.messageType === 'image' && m.attachmentUrl) {
const wrap = document.createElement('div');
wrap.className = 'mg-item';
wrap.style.cssText = 'aspect-ratio:1;overflow:hidden;border-radius:8px;background:var(--panel-2);cursor:pointer;';
const img = document.createElement('img');
img.src = m.attachmentUrl;
img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
wrap.appendChild(img);
grid.appendChild(wrap);
} else if (m.messageType === 'video' && m.attachmentUrl) {
const wrap = document.createElement('div');
wrap.className = 'mg-item';
wrap.style.cssText = 'aspect-ratio:1;overflow:hidden;border-radius:8px;background:#000;position:relative;cursor:pointer;';
const vid = document.createElement('video');
vid.src = m.attachmentUrl;
vid.preload = 'metadata';
vid.muted = true;
vid.style.cssText = 'width:100%;height:100%;object-fit:cover;';
const playIcon = document.createElement('i');
playIcon.className = 'ri-play-fill';
playIcon.style.cssText = 'position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:28px;text-shadow:0 0 8px rgba(0,0,0,0.6);';
wrap.appendChild(vid);
wrap.appendChild(playIcon);
grid.appendChild(wrap);
} else if (m.messageType === 'audio' && m.attachmentUrl) {
const wrap = document.createElement('div');
wrap.style.cssText = 'grid-column:1/-1;display:flex;align-items:center;gap:8px;padding:8px;border-radius:8px;background:var(--panel-2);';
const micIcon = document.createElement('i');
micIcon.className = 'ri-mic-line';
const aud = document.createElement('audio');
aud.controls = true;
aud.src = m.attachmentUrl;
aud.style.cssText = 'flex:1;height:40px;';
wrap.appendChild(micIcon);
wrap.appendChild(aud);
grid.appendChild(wrap);
}
});
const d = document.createElement('div');
d.className = 'folders-modal';
d.style.cssText = 'z-index:300;';
const card = document.createElement('div');
card.className = 'folders-card';
card.style.cssText = 'width:min(420px,96%);max-height:80vh;';
const head = document.createElement('div');
head.className = 'folders-head';
const titleEl = document.createElement('strong');
titleEl.textContent = 'وسائط الرسائل المحفوظة';
const closeBtn = document.createElement('button');
closeBtn.className = 'h-icon-btn folders-close-btn';
const closeIcon = document.createElement('i');
closeIcon.className = 'ri-close-line';
closeBtn.appendChild(closeIcon);
head.appendChild(titleEl);
head.appendChild(closeBtn);
card.appendChild(head);
card.appendChild(grid);
d.appendChild(card);
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

async openMyStatusViewer(idx) {
this.myProfileViewerIdx = idx;
this.myProfileStatusViewerOpen = true;
this.myProfileViewersList = [];
const s = this.myProfileStatuses[idx];
if (!s) return;
// Fetch real viewers from backend
try {
    const r = await fetch(@json($statusViewersRoute).replace('__STATUS_ID__', s.id), { headers: { 'Accept': 'application/json' } });
    const j = await r.json();
    if (j.success && Array.isArray(j.data)) {
        this.myProfileViewersList = j.data.map(v => ({
            id: v.id,
            name: v.name,
            avatar: v.avatarUrl || null,
            time: v.viewedAtText || '',
            liked: !!v.liked,
        }));
        const realCount = j.data.length;
        [this.myStatusHistory, this.myStatuses].forEach(arr => {
            const found = arr.find(s2 => Number(s2.id) === Number(s?.id));
            if (found) found.viewsCount = realCount;
        });
    }
} catch (_) {}
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
this.callLogs = [];
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
const callType = message.callType === 'video' ? 'video' : 'voice';
this.startCall(callType);
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
if (contact.isGroup) { this.openGroupInfo(); return; }

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

// Recolor dark QR modules with the card's gradient colors
{
    const qrX = holderX + qrHolderPadding;
    const qrY = holderY + qrHolderPadding;
    const imgData = ctx.getImageData(qrX, qrY, size, size);
    const d = imgData.data;
    for (let i = 0; i < d.length; i += 4) {
        if (d[i+3] < 128) continue;
        if ((d[i] + d[i+1] + d[i+2]) / 3 < 80) {
            const px = (i / 4) % size;
            const py = Math.floor((i / 4) / size);
            const t = Math.min(1, ((qrX + px) + (qrY + py)) / (2 * cardSize));
            const col = this.lerpColor(c1, c2, t);
            d[i] = col.r; d[i+1] = col.g; d[i+2] = col.b;
        }
    }
    ctx.putImageData(imgData, qrX, qrY);
}

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
const iceServers = [
{ urls: [
    'stun:stun.l.google.com:19302',
    'stun:stun1.l.google.com:19302',
    'stun:stun2.l.google.com:19302',
    'stun:stun3.l.google.com:19302',
]},
];
if (this.turnIceConfig && this.turnIceConfig.length) {
    // Use env-configured TURN servers (set TURN_URL / TURN_USERNAME / TURN_CREDENTIAL in .env)
    iceServers.push(...this.turnIceConfig);
}
// No public TURN fallback — configure TURN_URL in .env to enable relay for restrictive networks
return {
    iceServers,
    iceCandidatePoolSize: 10,
    bundlePolicy: 'max-bundle',
    rtcpMuxPolicy: 'require',
};
},

callRouteFor(template, callId) {
return template ? template.replace('__CALL_ID__', callId) : null;
},

async postCallAction(url, body) {
if (!url) return null;
try {
const resp = await fetch(url, {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'Accept': 'application/json',
'X-CSRF-TOKEN': this.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
},
body: JSON.stringify(body || {}),
});
if (!resp.ok) console.error('[Call]', url, '→ HTTP', resp.status);
return resp.json().catch(() => null);
} catch (e) {
console.error('[Call]', url, '→ network error', e);
return null;
}
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
pc.oniceconnectionstatechange = () => {
const s = pc.iceConnectionState;
if (s === 'failed') {
pc.restartIce();
}
if (s === 'disconnected') {
this.callConnectionWarning = true;
}
if (s === 'connected' || s === 'completed') {
this.callConnectionWarning = false;
}
};
pc.onconnectionstatechange = () => {
const s = pc.connectionState;
if (s === 'connected') {
this.callConnectionWarning = false;
if (this.callState !== 'in-call') {
this.callState = 'in-call';
this.callStartedAt = this.callStartedAt || Date.now();
}
}
if (s === 'failed') {
this.showToast('تعذّر تأسيس الاتصال — قد تكون الشبكة تحجب المكالمات، حاول من شبكة مختلفة', 'error');
this.endCall();
}
if (s === 'disconnected') {
this.callConnectionWarning = true;
}
};
pc.ontrack = (event) => {
const stream = event.streams[0];
this.upsertParticipantTile(remoteUserId, { stream });
this.remoteStreamsVersion++;
this.$nextTick(() => this.attachParticipantStreams());
if (stream) this.attachSpeakingAnalyser(remoteUserId, stream);
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

const isGroup = contacts.length > 1;
this.callType = type;
this.callContact = contacts[0];
this.isGroupCall = isGroup;
this.callDirection = 'outgoing';
this.callParticipants = contacts.map(c => ({ id: Number(c.id), name: c.name, avatar_url: c.avatar_url || null, stream: null }));
this.callState = 'calling';
this.callMuted = false;
this.cameraOff = false;
this.callMinimized = false;
this.startOutgoingRingtone();

const callPayload = { participant_ids: contacts.map(c => Number(c.id)), type };
if (this._pendingGroupCallId) callPayload.group_id = this._pendingGroupCallId;
const result = await this.postCallAction(this.callsInitiateRoute, callPayload);

if (!result || !result.success) {
this.showToast(result?.error || 'فشل بدء المكالمة', result?.busy ? 'info' : 'error');
this.cleanupCall();
return;
}

this.currentCallId = result.call_id;
console.log('[CALL] initiate OK callId=' + result.call_id + ' type=' + type);

if (!this.isGroupCall) {
if (this.callTimeoutTimer) clearTimeout(this.callTimeoutTimer);
this.callTimeoutTimer = setTimeout(() => {
if (this.callState === 'calling' || this.callState === 'incoming') {
this.showToast('لم يرد المستخدم على المكالمة', 'info');
this.endCall();
}
}, 45000);
// Fallback: if call.ringing event hasn't arrived after 4s, set it ourselves
if (this._callRingingFallbackTimer) clearTimeout(this._callRingingFallbackTimer);
this._callRingingFallbackTimer = setTimeout(() => {
this._callRingingFallbackTimer = null;
if (this.callState === 'calling' && !this.callIsRinging) this.callIsRinging = true;
}, 4000);
}

await this.hmsJoinRoom(this.currentCallId, type);
},

async answerIncomingCall(typeOverride) {
if (!this.currentCallId) return;
if (typeOverride) this.callType = typeOverride;
this.stopIncomingRingtone();
console.log('[CALL] answerIncomingCall callId=' + this.currentCallId + ' type=' + this.callType);

const _answerRes = await this.postCallAction(this.callRouteFor(this.callsAnswerRouteTemplate, this.currentCallId), {});
console.log('[CALL] answer POST result:', _answerRes);
if (!_answerRes?.success) {
console.error('[CALL] answer endpoint failed – aborting call');
this.showToast('تعذّر قبول المكالمة', 'error');
this.cleanupCall();
return;
}

this.callState = 'in-call';
this.callStartedAt = _answerRes.answered_at ? new Date(_answerRes.answered_at).getTime() : Date.now();
this._callNow = Date.now();
if (this._callElapsedTimer) clearInterval(this._callElapsedTimer);
this._callElapsedTimer = setInterval(() => { this._callNow = Date.now(); }, 1000);
this.incomingCallOffer = null;

await this.hmsJoinRoom(this.currentCallId, this.callType);
},

// ── 100ms SDK helpers ──────────────────────────────────────────────────────

async hmsJoinRoom(callId, callType) {
console.log('[HMS] hmsJoinRoom START callId=' + callId + ' type=' + callType + ' dir=' + this.callDirection);
const HMS = window.HMSVideoStore;
if (!HMS?.HMSReactiveStore) {
console.warn('[HMS] SDK not loaded — calls require 100ms SDK');
return;
}
// Always unsubscribe first, then fully leave + recreate fresh instance.
// Reusing the same instance after a failed join/leave puts the SDK in a
// corrupted state that causes "offer WS failed after 3 retries".
if (this._hmsUnsubFns && this._hmsUnsubFns.length) {
this._hmsUnsubFns.forEach(fn => { try { fn(); } catch (_) {} });
this._hmsUnsubFns = [];
}
if (window._hmsActions) {
try { await window._hmsActions.leave(); } catch (_) {}
}
const hmsInstance = new HMS.HMSReactiveStore();
window._hmsStore = hmsInstance.getStore();
window._hmsActions = hmsInstance.getActions();
const hmsActions = window._hmsActions;
const hmsStore = window._hmsStore;

const tokenUrl = this.callRouteFor(this.callsHmsTokenRouteTemplate, callId);
console.log('[HMS] fetching token from', tokenUrl);
const res = await this.postCallAction(tokenUrl, {});
if (!res?.success) {
console.error('[HMS] token fetch FAILED', res);
this.showToast('تعذّر الحصول على رمز المكالمة', 'error');
this.cleanupCall();
return;
}
console.log('[HMS] token OK roomId=' + res.room_id);

// Strategy: join with isAudioMuted:true so HMS uses an empty oscillator track
// instead of calling getUserMedia. This avoids error 3015 (NoDataInTrack) which
// fires when the hardware mic track is temporarily muted right after OS activation.
// After join we call setLocalAudioEnabled(true) to swap in the real mic track.
// If that also fails (mic still not ready), the call stays connected — only audio
// is unavailable. HMS only calls leave() for errors during join(), not afterwards.

// Pre-warm: request mic now so the OS audio session starts before we enable audio post-join.
// Keep stream open during join so HMS gets a track from the already-active session.
let _warmStream = null;
try {
_warmStream = await navigator.mediaDevices.getUserMedia({
    audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
    video: false,
});
const _warmTrack = _warmStream.getAudioTracks()[0];
if (_warmTrack && _warmTrack.muted) {
// Wait for OS to deliver first audio frames (unmute event), up to 2 seconds
await new Promise(resolve => {
const t = setTimeout(resolve, 2000);
_warmTrack.addEventListener('unmute', () => { clearTimeout(t); resolve(); }, { once: true });
});
}
} catch (micErr) {
if (micErr.name === 'NotAllowedError' || micErr.name === 'PermissionDeniedError') {
this.showToast('يرجى السماح للمتصفح بالوصول إلى الميكروفون', 'error');
return;
}
console.warn('[HMS] mic pre-warm:', micErr.name);
}

try {
const userName = this.userName || 'مستخدم';
console.log('[HMS] calling hmsActions.join() roomId=' + res.room_id + ' userName=' + userName);
await hmsActions.join({
authToken: res.token,
userName,
settings: { isAudioMuted: true, isVideoMuted: true },
});
console.log('[HMS] join() RESOLVED – now in HMS room ✓');
try { await hmsActions.unblockAudio(); } catch (_) {}
} catch (err) {
console.error('[HMS] join() ERROR:', err);
this.showToast('تعذّر الاتصال بخادم المكالمات', 'error');
if (_warmStream) _warmStream.getTracks().forEach(t => t.stop());
return;
}

// Enable real audio while warm stream is still open (shared OS capture session → unmuted track)
console.log('[HMS] calling setLocalAudioEnabled(true)...');
try {
await hmsActions.setLocalAudioEnabled(true);
this.callMuted = false;
console.log('[HMS] audio enabled OK ✓');
} catch (audioErr) {
console.warn('[HMS] audio enable FAILED:', audioErr.code, audioErr.message);
this.callMuted = true;
this.showToast('تحذير: الميكروفون غير متاح — اضغط رفع الكتم لإعادة المحاولة', 'info');
} finally {
if (_warmStream) { _warmStream.getTracks().forEach(t => t.stop()); _warmStream = null; }
}

// Enable camera for video calls
if (callType !== 'voice') {
hmsActions.setLocalVideoEnabled(true).catch(e => {
console.warn('[HMS] camera enable:', e.code, e.message);
this.cameraOff = true;
});
}

this._hmsUnsubFns = [];

// Load device list for in-call pickers (non-blocking)
this.loadCallDevices();

// Voice Activity Detection — drive speaking ring on remote avatar
if (HMS.selectSpeakers) {
this._hmsUnsubFns.push(hmsStore.subscribe((speakers) => {
    if (!speakers || !this.callState) { this.remoteAudioLevel = 0; return; }
    const levels = Object.values(speakers);
    this.remoteAudioLevel = levels.length ? Math.max(...levels) : 0;
}, HMS.selectSpeakers));
}

// Poor network quality warning — HMS provides downlinkQuality 0-5 (0=unknown, 1=poor, 5=excellent)
if (HMS.selectConnectionQualities) {
this._hmsUnsubFns.push(hmsStore.subscribe((qualities) => {
    if (!qualities || !this.callState) return;
    const values = Object.values(qualities);
    const hasPoor = values.some(q => q.downlinkQuality > 0 && q.downlinkQuality <= 2);
    if (hasPoor && !this._poorQualityToastAt) {
        this._poorQualityToastAt = Date.now();
        this.showToast('جودة الشبكة ضعيفة — قد يتأثر الصوت', 'info');
    } else if (!hasPoor && this._poorQualityToastAt && Date.now() - this._poorQualityToastAt > 30000) {
        this._poorQualityToastAt = null; // allow re-notification after 30s of good quality
    }
}, HMS.selectConnectionQualities));
}

// Log HMS errors to console for diagnostics (3008 = transient NoDataInTrack, not fatal)
if (HMS.selectErrors) {
let _seenErrCount = 0;
this._hmsUnsubFns.push(hmsStore.subscribe((errors) => {
if (!errors || errors.length <= _seenErrCount) return;
const fresh = errors.slice(_seenErrCount);
_seenErrCount = errors.length;
fresh.forEach(e => console.warn('[HMS] sdk error:', e.code, e.name, e.message || ''));
}, HMS.selectErrors));
}

// Attach local video when local peer track becomes available
this._hmsUnsubFns.push(hmsStore.subscribe((localPeer) => {
if (localPeer?.videoTrack && callType !== 'voice' && this.$refs.localVideo) {
hmsActions.attachVideo(localPeer.videoTrack, this.$refs.localVideo).catch(() => {});
}
}, HMS.selectLocalPeer));

// Attach remote peers' video and transition caller to in-call state
this._hmsUnsubFns.push(hmsStore.subscribe((peers) => {
console.log('[HMS] selectRemotePeers fired: ' + (peers || []).length + ' peers, callState=' + this.callState);
this.$nextTick(() => {
(peers || []).forEach(peer => {
const uid = peer.customerUserId;
if (!uid) return;
this.upsertParticipantTile(uid, { name: peer.name });
if (peer.videoTrack && callType !== 'voice') {
const el = this.$refs['remoteMedia_' + uid];
const target = Array.isArray(el) ? el[0] : el;
if (target) hmsActions.attachVideo(peer.videoTrack, target).catch(() => {});
}
});
});
if (peers && peers.length > 0) {
if (this.callState === 'calling') {
    this.callState = 'in-call';
    this.stopOutgoingRingtone();
    if (this._callRingingFallbackTimer) { clearTimeout(this._callRingingFallbackTimer); this._callRingingFallbackTimer = null; }
}
if (this.callState === 'in-call') {
    if (!this.callStartedAt) this.callStartedAt = Date.now();
    if (!this._callNow) this._callNow = Date.now();
    if (!this._callElapsedTimer) this._callElapsedTimer = setInterval(() => { this._callNow = Date.now(); }, 1000);
    if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
}
} else if ((!peers || peers.length === 0) && this.callState === 'in-call' && this.callStartedAt) {
if (this.isGroupCall) {
    // Group: give 10s grace period before ending — others may still be connecting
    if (!this._groupCallEmptyTimer) {
        console.log('[HMS] group call: all peers left – starting 10s grace period');
        this._groupCallEmptyTimer = setTimeout(() => {
            this._groupCallEmptyTimer = null;
            if (this.callState === 'in-call' && this.isGroupCall) {
                const currentPeers = window._hmsStore?.getState(HMS.selectRemotePeers) || [];
                if (!currentPeers.length) { console.log('[HMS] group grace elapsed – ending call'); this.cleanupCall(); }
            }
        }, 10000);
    }
} else {
    if (this._groupCallEmptyTimer) { clearTimeout(this._groupCallEmptyTimer); this._groupCallEmptyTimer = null; }
    console.log('[HMS] all remote peers left – ending call');
    this.cleanupCall();
}
} else if (peers && peers.length > 0 && this._groupCallEmptyTimer) {
// New peer arrived during grace period — cancel the grace timer
clearTimeout(this._groupCallEmptyTimer);
this._groupCallEmptyTimer = null;
}
}, HMS.selectRemotePeers));

// Connection health indicator + auto-reconnect on disconnect
this._hmsUnsubFns.push(hmsStore.subscribe((isConnected) => {
console.log('[HMS] isConnectedToRoom=' + isConnected + ' callState=' + this.callState);
if (this.callState === 'in-call' || this.callState === 'calling') {
    this.callConnectionWarning = !isConnected;
    if (!isConnected) {
        // Give HMS 10s to self-recover; if still disconnected, trigger a rejoin
        if (!this._hmsReconnectTimer) {
            this._hmsReconnectTimer = setTimeout(async () => {
                this._hmsReconnectTimer = null;
                const stillDisconnected = !window._hmsStore?.getState(HMS.selectIsConnectedToRoom);
                if (stillDisconnected && (this.callState === 'in-call' || this.callState === 'calling') && this.currentCallId) {
                    console.log('[HMS] reconnect: attempting rejoin after 10s disconnect');
                    try { await window._hmsActions?.leave(); } catch (_) {}
                    await this.hmsJoinRoom(this.currentCallId, this.callType);
                }
            }, 10000);
        }
    } else {
        if (this._hmsReconnectTimer) { clearTimeout(this._hmsReconnectTimer); this._hmsReconnectTimer = null; }
    }
}
}, HMS.selectIsConnectedToRoom));
console.log('[HMS] hmsJoinRoom COMPLETE – subscriptions active, callState=' + this.callState);
},

async hmsLeaveRoom() {
if (this._hmsUnsubFns && this._hmsUnsubFns.length) {
this._hmsUnsubFns.forEach(fn => { try { fn(); } catch (_) {} });
this._hmsUnsubFns = [];
}
try {
if (window._hmsActions) await window._hmsActions.leave();
} catch (_) {}
},

// ── end 100ms helpers ──────────────────────────────────────────────────────

// ── Polling fallback for incoming calls ────────────────────────────────────
// Fires every 2 s as a backup when the Pusher/Reverb WebSocket event is missed.
startIncomingCallPoll() {
if (this._callPollTimer || !this.callsPendingRoute) return;
this._callPollTimer = setInterval(async () => {
    // Outgoing call: poll to detect if callee rejected/cancelled (Reverb fallback)
    if (this.callState === 'calling' && this.currentCallId) {
        try {
            const cu = this.callsPendingRoute + '?check_call_id=' + this.currentCallId;
            const cr = await fetch(cu, { headers: { 'Accept': 'application/json' } });
            if (cr.ok) {
                const cd = await cr.json();
                if (cd.cancelled) {
                    this.showToast('تم رفض المكالمة', 'info');
                    this.cleanupCall();
                }
            }
        } catch (_) {}
        return;
    }
    // Incoming call: check whether the caller cancelled it
    if (this.callState === 'incoming' && this.currentCallId) {
        try {
            const cu = this.callsPendingRoute + '?check_call_id=' + this.currentCallId;
            const cr = await fetch(cu, { headers: { 'Accept': 'application/json' } });
            if (cr.ok) { const cd = await cr.json(); if (cd.cancelled) this.cleanupCall(); }
        } catch (_) {}
        return;
    }
    if (this.callState) return; // in-call — skip normal pending check
    try {
        const r = await fetch(this.callsPendingRoute, { headers: { 'Accept': 'application/json' } });
        if (!r.ok) return;
        const data = await r.json();
        if (!data.call) return;
        const callId = Number(data.call.call_id);
        if (callId === Number(this.currentCallId)) return; // Already handling this call
        console.log('[POLL] incoming call found via poll callId=' + callId);
        if (!this.callState) {
            this.callType = data.call.type;
            this.isGroupCall = !!data.call.is_group;
            this.callContact = { id: data.call.caller.id, name: data.call.caller.name, avatar_url: data.call.caller.avatar_url };
            this.callParticipants = [{ id: Number(data.call.caller.id), name: data.call.caller.name, avatar_url: data.call.caller.avatar_url, stream: null }];
            this.currentCallId = callId;
            this.incomingCallOffer = data.call.offer;
            this.callDirection = 'incoming';
            this.callState = 'incoming';
            this.postCallAction(this.callRouteFor(this.callsRingRouteTemplate, callId), {});
            this.maybeShowCallNotification(this.callContact, this.callType);
            this.startIncomingRingtone();
        }
    } catch (_) {}
}, 2000);
},

stopIncomingCallPoll() {
if (this._callPollTimer) { clearInterval(this._callPollTimer); this._callPollTimer = null; }
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
const uid = Number(contact.id);

// If already ringing, ignore
const existing = this.pendingInvites.find(p => Number(p.id) === uid);
if (existing && existing.status === 'ringing') return;

// Set or update status to ringing
if (existing) {
    if (existing._ringTimer) clearTimeout(existing._ringTimer);
    existing.status = 'ringing';
    existing._ringTimer = null;
} else {
    this.pendingInvites.push({ id: uid, name: contact.name, avatar_url: contact.avatar_url || null, status: 'ringing', _ringTimer: null });
}

const invite = this.pendingInvites.find(p => Number(p.id) === uid);
const result = await this.postCallAction(this.callRouteFor(this.callsInviteRouteTemplate, this.currentCallId), {
    user_id: contact.id,
});
if (!result || !result.success) {
    if (invite) invite.status = 'declined';
    this.showToast(result?.message || 'تعذر إضافة المشارك', 'error');
    return;
}
this.isGroupCall = true;
// 30-second ring timeout
if (invite) {
    invite._ringTimer = setTimeout(() => {
        if (invite.status === 'ringing') invite.status = 'timeout';
    }, 30000);
}
this.showToast('جاري استدعاء ' + contact.name + '...', 'info');
},

ringAgain(invite) {
this.addParticipantToCall({ id: invite.id, name: invite.name, avatar_url: invite.avatar_url });
},

async createOfferFor(userId) {
const pc = this.createPeerConnection(userId);
this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
const offer = await pc.createOffer();
await pc.setLocalDescription(offer);
return { sdp: offer.sdp, type: offer.type };
},

cleanupCall() {
if (!this.callState && !this.currentCallId) return; // already cleaned up — prevent double execution
this.stopIncomingCallPoll();
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
if (this._callRingingFallbackTimer) { clearTimeout(this._callRingingFallbackTimer); this._callRingingFallbackTimer = null; }
if (this._hmsReconnectTimer) { clearTimeout(this._hmsReconnectTimer); this._hmsReconnectTimer = null; }
if (this._groupCallEmptyTimer) { clearTimeout(this._groupCallEmptyTimer); this._groupCallEmptyTimer = null; }
if (this._callElapsedTimer) { clearInterval(this._callElapsedTimer); this._callElapsedTimer = null; }
if (this._callDeviceCloseTimer) { clearTimeout(this._callDeviceCloseTimer); this._callDeviceCloseTimer = null; }
// Clear pending invite timers
this.pendingInvites.forEach(p => { if (p._ringTimer) clearTimeout(p._ringTimer); });
this.pendingInvites = [];
this._poorQualityToastAt = null;
this._callNow = 0;
this.remoteAudioLevel = 0;
this.callDeviceHoverOpen = null;
this.callIsRinging = false;
this.stopOutgoingRingtone();
this.stopIncomingRingtone();

// Show call ended summary
if (this.callStartedAt) {
const secs = Math.floor((Date.now() - this.callStartedAt) / 1000);
const m = Math.floor(secs / 60).toString().padStart(2, '0');
const s = (secs % 60).toString().padStart(2, '0');
this.callEndedSummary = { duration: m + ':' + s };
setTimeout(() => { this.callEndedSummary = null; }, 3000);
}

// Stop speaking detection
if (this.speakingTimer) { clearInterval(this.speakingTimer); this.speakingTimer = null; }
Object.values(this.speakingAnalysers).forEach(a => { try { a.disconnect?.(); } catch(_) {} });
this.speakingAnalysers = {};
this.speakingUserId = null;

Object.values(this.peerConnections).forEach(entry => {
try { entry.pc?.close(); } catch (_) {}
});
this.peerConnections = {};

if (this.localStream) {
this.localStream.getTracks().forEach(t => t.stop());
this.localStream = null;
}
if (this.$refs.localVideo) this.$refs.localVideo.srcObject = null;

this.hmsLeaveRoom();

// Save call card to chat log before resetting state
if (this.callContact && this.currentCallId) {
    const cid = Number(this.callContact.id);
    const wasConnected = !!this.callStartedAt;
    const duration = wasConnected ? Math.floor((Date.now() - this.callStartedAt) / 1000) : 0;
    const newLog = {
        id: 'call_real_' + Date.now(),
        contactId: cid,
        contactName: this.callContact.name,
        contactAvatar: this.callContact.avatar_url,
        type: this.callType === 'video' ? 'video' : 'audio',
        direction: this.callDirection || (this.callState === 'incoming' ? 'incoming' : 'outgoing'),
        status: wasConnected ? 'completed' : (this.callDirection === 'incoming' ? 'missed' : 'cancelled'),
        duration,
        timestamp: new Date().toISOString(),
    };
    this.callLogs = [newLog, ...(this.callLogs || [])];
    this.saveCallLogs();
    this.$nextTick(() => this.injectCallMessages());
}

this.callState = null;
this.callDirection = null;
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
this.callMinimized = false;
this.callConnectionWarning = false;
this.pipPos = { bottom: 100, left: 16 };
this.startIncomingCallPoll(); // restart poll so subsequent incoming calls are detected
},

toggleMute() {
this.callMuted = !this.callMuted;
if (window._hmsActions) window._hmsActions.setLocalAudioEnabled(!this.callMuted).catch(() => {});
},

toggleCamera() {
this.cameraOff = !this.cameraOff;
if (window._hmsActions) window._hmsActions.setLocalVideoEnabled(!this.cameraOff).catch(() => {});
},

async switchCamera() {
if (this.callType !== 'video') return;
try {
if (window._hmsActions) {
const devices = await navigator.mediaDevices.enumerateDevices();
const cams = devices.filter(d => d.kind === 'videoinput');
if (cams.length < 2) { this.showToast('لا توجد كاميرا ثانية', 'info'); return; }
const HMS = window.HMSVideoStore;
const localVideoTrackId = window._hmsStore?.getState(HMS.selectLocalVideoTrackID);
if (!localVideoTrackId) return;
const currentTrack = window._hmsStore.getState(HMS.selectVideoTrackByID(localVideoTrackId));
const currentDeviceId = currentTrack?.settings?.deviceId;
const next = cams.find(c => c.deviceId !== currentDeviceId) || cams[0];
await window._hmsActions.switchCamera({ deviceId: next.deviceId });
}
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
if (!this.callStartedAt || !this._callNow) return '';
const secs = Math.floor((this._callNow - this.callStartedAt) / 1000);
const m = Math.floor(secs / 60).toString().padStart(2, '0');
const s = (secs % 60).toString().padStart(2, '0');
return `${m}:${s}`;
},

// ── Call device picker ────────────────────────────────────
async loadCallDevices() {
try {
    const devices = await navigator.mediaDevices.enumerateDevices();
    this.callDevices = {
        audioinput:  devices.filter(d => d.kind === 'audioinput'  && d.deviceId),
        audiooutput: devices.filter(d => d.kind === 'audiooutput' && d.deviceId),
        videoinput:  devices.filter(d => d.kind === 'videoinput'  && d.deviceId),
    };
} catch(_) {}
},
openCallDeviceMenu(type) {
if (this._callDeviceCloseTimer) { clearTimeout(this._callDeviceCloseTimer); this._callDeviceCloseTimer = null; }
this.callDeviceHoverOpen = type;
},
closeCallDeviceMenu() {
this._callDeviceCloseTimer = setTimeout(() => { this.callDeviceHoverOpen = null; }, 200);
},
cancelCallDeviceClose() {
if (this._callDeviceCloseTimer) { clearTimeout(this._callDeviceCloseTimer); this._callDeviceCloseTimer = null; }
},
async setCallAudioInput(deviceId) {
this.callActiveAudioInput = deviceId;
this.callDeviceHoverOpen = null;
try { await window._hmsActions?.setAudioSettings({ deviceId }); } catch(_) {}
},
async setCallAudioOutput(deviceId) {
this.callActiveAudioOutput = deviceId;
this.callDeviceHoverOpen = null;
try { await window._hmsActions?.setAudioOutputDevice(deviceId); } catch(_) {}
},
async setCallVideoInput(deviceId) {
this.callActiveVideoInput = deviceId;
this.callDeviceHoverOpen = null;
try { await window._hmsActions?.setVideoSettings({ deviceId }); } catch(_) {}
},

// ── User-agent parser for sessions panel ─────────────────
parseUserAgent(ua) {
if (!ua) return { name: 'جهاز غير معروف', icon: 'ri-computer-line' };
const s = ua;
let name = 'متصفح غير معروف';
let icon = 'ri-computer-line';
const isMobile = /Mobile|Android|iPhone|iPad/i.test(s);
if (isMobile) icon = 'ri-smartphone-line';
if      (/Edg\//i.test(s))                              { name = 'Microsoft Edge'; }
else if (/OPR\//i.test(s))                              { name = 'Opera'; }
else if (/Chrome\//i.test(s) && !/Chromium/i.test(s))  { name = 'Google Chrome'; icon = isMobile ? 'ri-smartphone-line' : 'ri-chrome-line'; }
else if (/Firefox\//i.test(s))                          { name = 'Firefox'; icon = isMobile ? 'ri-smartphone-line' : 'ri-firefox-line'; }
else if (/Safari\//i.test(s) && !/Chrome/i.test(s))    { name = 'Safari'; icon = isMobile ? 'ri-smartphone-line' : 'ri-safari-line'; }
else if (/MSIE|Trident/i.test(s))                       { name = 'Internet Explorer'; }
if      (/Windows NT/i.test(s)) name += ' — ويندوز';
else if (/Mac OS X/i.test(s))   name += ' — ماك';
else if (/Android/i.test(s))    name += ' — أندرويد';
else if (/iPhone|iPad/i.test(s)) name += ' — iOS';
else if (/Linux/i.test(s))      name += ' — لينكس';
return { name, icon };
},
formatLastActivity(isoStr) {
if (!isoStr) return '';
try {
    const diff = Date.now() - new Date(isoStr).getTime();
    if (diff < 60000)      return 'منذ لحظات';
    if (diff < 3600000)    return 'منذ ' + Math.floor(diff / 60000) + ' دقيقة';
    if (diff < 86400000)   return 'منذ ' + Math.floor(diff / 3600000) + ' ساعة';
    return 'منذ ' + Math.floor(diff / 86400000) + ' يوم';
} catch(_) { return ''; }
},

// ── Ring tone helpers ──────────────────────────────────────
_getRingFreq() {
const freqMap = {default:800,'soft-bell':1200,classic:660,digital:1000,chime:1400,pop:520,ping:2000,gentle:440};
return freqMap[this.callSettings?.ringtone] || 800;
},
_getRingOscType() {
const t = this.callSettings?.ringtone;
if (t === 'digital') return 'square';
if (t === 'pop') return 'triangle';
return 'sine';
},
_playRingBeep(ctx, freq, oscType, startAt, duration) {
try {
const osc = ctx.createOscillator();
const gain = ctx.createGain();
osc.connect(gain); gain.connect(ctx.destination);
osc.type = oscType; osc.frequency.value = freq;
gain.gain.setValueAtTime(0, startAt);
gain.gain.linearRampToValueAtTime(0.18, startAt + 0.07);
gain.gain.setValueAtTime(0.18, startAt + duration - 0.07);
gain.gain.linearRampToValueAtTime(0, startAt + duration);
osc.start(startAt); osc.stop(startAt + duration);
} catch(_) {}
},

// ── Outgoing ringtone (Web Audio API) ──────────────────────
startOutgoingRingtone() {
try {
if (this.outgoingRingtone) this.stopOutgoingRingtone();
const AudioCtx = window.AudioContext || window.webkitAudioContext;
if (!AudioCtx) return;
const ctx = new AudioCtx();
const freq = this._getRingFreq();
const oscType = this._getRingOscType();
const play = () => {
this._playRingBeep(ctx, freq, oscType, ctx.currentTime, 0.45);
};
let running = true;
const loop = () => { if (!running) return; play(); setTimeout(loop, 2000); };
loop();
this.outgoingRingtone = { ctx, stop() { running = false; try { ctx.close(); } catch(_) {} } };
} catch(_) {}
},
stopOutgoingRingtone() {
if (this.outgoingRingtone) { this.outgoingRingtone.stop(); this.outgoingRingtone = null; }
},

// ── Incoming ringtone (Web Audio API) ──────────────────────
startIncomingRingtone() {
try {
if (this.incomingRingtone) this.stopIncomingRingtone();
const AudioCtx = window.AudioContext || window.webkitAudioContext;
if (!AudioCtx) return;
const ctx = new AudioCtx();
const freq = this._getRingFreq();
const oscType = this._getRingOscType();
const play = () => {
// Two quick beeps — classic incoming phone pattern
this._playRingBeep(ctx, freq, oscType, ctx.currentTime, 0.3);
this._playRingBeep(ctx, freq, oscType, ctx.currentTime + 0.4, 0.3);
};
let running = true;
const loop = () => { if (!running) return; play(); setTimeout(loop, 3000); };
loop();
this.incomingRingtone = { ctx, stop() { running = false; try { ctx.close(); } catch(_) {} } };
} catch(_) {}
},
stopIncomingRingtone() {
if (this.incomingRingtone) { this.incomingRingtone.stop(); this.incomingRingtone = null; }
},

// ── Speaking detector ──────────────────────────────────────
startSpeakingDetection() {
if (this.speakingTimer) return;
this.speakingTimer = setInterval(() => {
let loudest = null; let maxVol = 0.02;
Object.entries(this.speakingAnalysers).forEach(([uid, analyser]) => {
const buf = new Uint8Array(analyser.frequencyBinCount);
analyser.getByteFrequencyData(buf);
const vol = buf.reduce((a, b) => a + b, 0) / buf.length / 255;
if (vol > maxVol) { maxVol = vol; loudest = uid; }
});
this.speakingUserId = loudest ? Number(loudest) : null;
}, 250);
},
attachSpeakingAnalyser(userId, stream) {
try {
const AudioCtx = window.AudioContext || window.webkitAudioContext;
if (!AudioCtx || !stream) return;
const ctx = new AudioCtx();
const src = ctx.createMediaStreamSource(stream);
const analyser = ctx.createAnalyser();
analyser.fftSize = 256;
src.connect(analyser);
this.speakingAnalysers[String(userId)] = analyser;
this.startSpeakingDetection();
} catch(_) {}
},

// ── PiP drag ──────────────────────────────────────────────
startPipDrag(e) {
this.pipDragging = true;
const el = this.$refs.localVideo;
if (!el) return;
const rect = el.getBoundingClientRect();
this.pipDragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
const onMove = (ev) => {
if (!this.pipDragging) return;
const vw = window.innerWidth, vh = window.innerHeight;
const w = rect.width, h = rect.height;
const left = Math.min(Math.max(ev.clientX - this.pipDragOffset.x, 0), vw - w);
const top = Math.min(Math.max(ev.clientY - this.pipDragOffset.y, 0), vh - h);
this.pipPos = { left: left, bottom: vh - top - h };
};
const onUp = () => {
this.pipDragging = false;
window.removeEventListener('pointermove', onMove);
window.removeEventListener('pointerup', onUp);
};
window.addEventListener('pointermove', onMove);
window.addEventListener('pointerup', onUp);
el.setPointerCapture(e.pointerId);
},

initCallSignaling() {
if (!window.Echo || !this.currentUserId) return;

const channel = window.Echo.private('user.' + this.currentUserId);

// Log Pusher subscription state so we can diagnose missed call events
channel.subscribed(() => {
    console.log('[PUSHER] private-user.' + this.currentUserId + ' subscribed OK ✓');
});
channel.error((err) => {
    console.error('[PUSHER] private-user.' + this.currentUserId + ' subscription ERROR:', err);
});

channel.listen('.call.initiated', (data) => {
console.log('[CALL] call.initiated received callId=' + data.call_id + ' from=' + data.caller?.id + ' type=' + data.type + ' currentCallState=' + (this.callState || 'idle'));
if (this.callState) {
    // Auto-reject: already on a call
    console.log('[CALL] auto-rejecting: already in state ' + this.callState);
    this.postCallAction(this.callRouteFor(this.callsRejectRouteTemplate, data.call_id), {});
    return;
}
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
this.callDirection = 'incoming';
this.callState = 'incoming';

this.postCallAction(this.callRouteFor(this.callsRingRouteTemplate, data.call_id), {});
this.maybeShowCallNotification(this.callContact, this.callType);
this.startIncomingRingtone();
});

channel.listen('.call.ringing', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
if (this.callState === 'calling') this.callIsRinging = true;
});

channel.listen('.call.answered', (data) => {
console.log('[CALL] call.answered received callId=' + data.call_id + ' myCallId=' + this.currentCallId);
if (Number(data.call_id) !== Number(this.currentCallId)) return;
console.log('[CALL] → switching callState to in-call');
this.callState = 'in-call';
this.stopOutgoingRingtone();
// Sync call start time from server-stamped answered_at for accurate duration across both parties
this.callStartedAt = data.answered_at ? new Date(data.answered_at).getTime() : (this.callStartedAt || Date.now());
this._callNow = Date.now();
if (this._callElapsedTimer) clearInterval(this._callElapsedTimer);
this._callElapsedTimer = setInterval(() => { this._callNow = Date.now(); }, 1000);
if (this.callTimeoutTimer) { clearTimeout(this.callTimeoutTimer); this.callTimeoutTimer = null; }
if (this._callRingingFallbackTimer) { clearTimeout(this._callRingingFallbackTimer); this._callRingingFallbackTimer = null; }
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

// ICE / peer-offer / peer-answer — no longer used (100ms SDK handles media transport)

channel.listen('.call.participant-joined', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
this.isGroupCall = true;
this.upsertParticipantTile(data.user.id, { name: data.user.name, avatar_url: data.user.avatar_url });
// Update pending invite status to 'answered'
const invite = this.pendingInvites.find(p => Number(p.id) === Number(data.user.id));
if (invite) {
    if (invite._ringTimer) { clearTimeout(invite._ringTimer); invite._ringTimer = null; }
    invite.status = 'answered';
    setTimeout(() => {
        this.pendingInvites = this.pendingInvites.filter(p => Number(p.id) !== Number(data.user.id));
    }, 2000);
}
});

channel.listen('.call.participant-left', (data) => {
if (Number(data.call_id) !== Number(this.currentCallId)) return;
this.removeParticipantTile(data.user_id);
});

// Real-time typing indicator via WebSocket (instant, no polling delay)
channel.listen('.user.typing', (data) => {
const uid = Number(data.from_user_id);
const isTyping = !!data.is_typing;
const mediaType = data.media_type || null;
const idx = this.contacts.findIndex(c => Number(c.id) === uid);
if (idx !== -1) {
    this.contacts[idx].isTyping = isTyping;
    this.contacts[idx].typingMediaType = mediaType;
}
if (this.selectedContact && Number(this.selectedContact.id) === uid) {
    this.selectedContact.isTyping = isTyping;
    this.selectedContact.typingMediaType = mediaType;
}
});

// Group messages via WebSocket
channel.listen('.group.message', (data) => {
const groupId = Number(data.group_id);
const msg = this.normalizeGroupMessage(data.message);
if (!msg) return;
// Update contact preview
const cIdx = this.contacts.findIndex(c => c.isGroup && Number(c._groupId) === groupId);
if (cIdx !== -1) {
    this.contacts[cIdx].lastMessage = msg.senderName + ': ' + (msg.content || '').substring(0, 60);
    this.contacts[cIdx].lastMessageTime = msg.createdAt;
    if (!(this.selectedContact && this.selectedContact.isGroup && this.selectedContact._groupId === groupId)) {
        this.contacts[cIdx].unreadCount = (Number(this.contacts[cIdx].unreadCount) || 0) + 1;
    }
}
// Append to messages if this group is currently selected
if (this.selectedContact && this.selectedContact.isGroup && Number(this.selectedContact._groupId) === groupId) {
    if (!this.messages.some(m => Number(m.id) === Number(msg.id))) {
        this.messages.push(msg);
        this.lastKnownMessageId = Math.max(Number(this.lastKnownMessageId || 0), Number(msg.id || 0));
        this.normalizeMessageOrder();
        if (this.isNearBottom()) this.scrollToBottom(false);
    }
}
this.playNotificationTone('group_' + groupId);
});
},