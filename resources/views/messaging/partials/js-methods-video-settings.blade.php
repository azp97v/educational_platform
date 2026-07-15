// ===== VIDEO EDITOR =====

openVideoEditor(file) {

this.videoEditorFile = file; this.videoEditorUrl = URL.createObjectURL(file);

this.videoEditorOpen = true; this.videoEditorStart = 0; this.videoEditorEnd = 0;

this.videoEditorQuality = '720p'; this.videoEditorMuted = false;
this.videoEditorProgress = 0; this.videoEditorProcessing = false;

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

// iOS / Safari fallback — no captureStream or MediaRecorder
if (!this._mediaRecorderSupported) {
    const rawFile = this.videoEditorFile;
    this.closeVideoEditor();
    this.addPendingAttachment(rawFile);
    this.showToast('متصفحك لا يدعم المعالجة — تم إرفاق الفيديو الأصلي', 'info');
    return;
}

this.videoEditorProcessing = true; this.videoEditorProgress = 0;

const resMap    = { '360p':{w:640,h:360}, '480p':{w:854,h:480}, '720p':{w:1280,h:720}, '1080p':{w:1920,h:1080} };
const bitrateMap = { '360p':500000, '480p':1000000, '720p':2500000, '1080p':5000000 };
const targetRes  = resMap[this.videoEditorQuality] || resMap['720p'];
const targetBitrate = bitrateMap[this.videoEditorQuality] || 2500000;
const muteOutput = this.videoEditorMuted;

const startTime = this.videoEditorStart || 0;
const endTime   = this.videoEditorEnd   || 0;
const hasTrim   = endTime > startTime && endTime > 0;

try {

// ── Load video element ──────────────────────────────────
const video = document.createElement('video');
video.src     = URL.createObjectURL(this.videoEditorFile);
video.muted   = true;   // muted so autoplay is allowed; audio piped via AudioContext
video.preload = 'auto';
video.crossOrigin = 'anonymous';

await new Promise((resolve, reject) => {
    video.onloadedmetadata = resolve;
    video.onerror = reject;
    video.load();
});

// Seek to startTime and wait for seeked event before recording
if (startTime > 0) {
    await new Promise(resolve => {
        video.onseeked = resolve;
        video.currentTime = startTime;
    });
}

const duration    = hasTrim ? (endTime - startTime) : video.duration;
const origW       = video.videoWidth  || targetRes.w;
const origH       = video.videoHeight || targetRes.h;
const scale       = Math.min(targetRes.w / origW, targetRes.h / origH, 1);
// Always use even dimensions — required by most video codecs
const cw = (Math.max(2, Math.round(origW * scale))) & ~1;
const ch = (Math.max(2, Math.round(origH * scale))) & ~1;

// ── Canvas setup ────────────────────────────────────────
const canvas = document.createElement('canvas');
canvas.width  = cw;
canvas.height = ch;
const ctx = canvas.getContext('2d');

const videoStream = canvas.captureStream(30);

// ── Audio wiring ─────────────────────────────────────────
if (!muteOutput) {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
            const audioCtx = new AudioCtx();
            const source   = audioCtx.createMediaElementSource(video);
            const dest     = audioCtx.createMediaStreamDestination();
            source.connect(dest);
            source.connect(audioCtx.destination); // also play through speakers during preview
            const audioTracks = dest.stream.getAudioTracks();
            if (audioTracks.length) videoStream.addTrack(audioTracks[0]);
        }
    } catch (_) { /* audio wiring failed — continue video-only */ }
}

// ── Recorder ────────────────────────────────────────────
const mimeTypes = [
    'video/webm;codecs=vp8,opus',
    'video/webm;codecs=vp9,opus',
    'video/webm;codecs=vp8',
    'video/webm',
];
const mimeType = mimeTypes.find(m => MediaRecorder.isTypeSupported(m)) || '';

const recorder = new MediaRecorder(videoStream, {
    ...(mimeType ? { mimeType } : {}),
    videoBitsPerSecond: targetBitrate,
});

const chunks = [];
recorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };

// ── Frame loop ───────────────────────────────────────────
const fps        = 30;
const totalFrames = Math.max(1, Math.ceil(duration * fps));

const processed = await new Promise((resolve, reject) => {
    recorder.onstop = () => {
        const blob = new Blob(chunks, { type: recorder.mimeType || 'video/webm' });
        resolve(new File([blob], 'video_' + Date.now() + '.webm', { type: 'video/webm' }));
    };
    recorder.onerror = reject;
    recorder.start();

    let frameCount = 0;
    let stopped    = false;
    let animId     = null;

    const drawFrame = () => {
        if (stopped) return;
        const elapsed   = video.currentTime - startTime;
        const progress  = Math.min(elapsed / duration, 1);
        this.videoEditorProgress = Math.round(progress * 99);

        ctx.drawImage(video, 0, 0, cw, ch);
        frameCount++;

        const reachedEnd = hasTrim
            ? video.currentTime >= endTime - 0.05
            : progress >= 0.999;

        if (reachedEnd || frameCount >= totalFrames + 5) {
            stopped = true;
            cancelAnimationFrame(animId);
            recorder.stop();
            video.pause();
        } else {
            animId = requestAnimationFrame(drawFrame);
        }
    };

    video.play().then(() => {
        animId = requestAnimationFrame(drawFrame);
    }).catch(reject);
});

this.videoEditorProgress = 100;
this.videoEditorProcessing = false;

const previewUrl = URL.createObjectURL(processed);
this.videoEditorPreviewFile = processed;
this.videoEditorPreviewUrl  = previewUrl;
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

showSnPreview(pos) {
    // Cancel any in-flight dismiss work
    clearTimeout(this._snPreviewTimer);
    clearTimeout(this._snPreviewAutoTimer);
    clearTimeout(this._snPreviewDismissDelay);
    if (this._snMoveDismiss) {
        document.removeEventListener('mousemove', this._snMoveDismiss);
        this._snMoveDismiss = null;
    }
    this._snToastHovered = false;
    this.snPreviewPos = pos;
    this.snPreviewVisible = true;

    // After 350ms buffer (lets the hover gesture settle), listen for mouse movement.
    // Any movement dismisses UNLESS the mouse is over the toast card.
    // We debounce 120ms so the user has time to move from the corner button to the toast.
    this._snPreviewTimer = setTimeout(() => {
        this._snMoveDismiss = () => {
            if (this._snToastHovered) return;
            clearTimeout(this._snPreviewDismissDelay);
            this._snPreviewDismissDelay = setTimeout(() => {
                if (this._snToastHovered) return;
                this.snPreviewVisible = false;
                document.removeEventListener('mousemove', this._snMoveDismiss);
                this._snMoveDismiss = null;
            }, 120);
        };
        document.addEventListener('mousemove', this._snMoveDismiss);
    }, 350);
},

snToastMouseEnter() {
    // Mouse entered the toast — freeze it, cancel any pending dismiss
    this._snToastHovered = true;
    clearTimeout(this._snPreviewAutoTimer);
    clearTimeout(this._snPreviewDismissDelay);
    if (this._snMoveDismiss) {
        document.removeEventListener('mousemove', this._snMoveDismiss);
        this._snMoveDismiss = null;
    }
},

snToastMouseLeave() {
    // Mouse left the toast — start 5-second countdown to dissolve
    this._snToastHovered = false;
    clearTimeout(this._snPreviewAutoTimer);
    this._snPreviewAutoTimer = setTimeout(() => {
        this.snPreviewVisible = false;
    }, 5000);
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
    const isGroup = !!(contact?.isGroup || contact?.is_group);
    if (isGroup && !this.settingsNotifications.groups) return;
    if (!isGroup && !this.settingsNotifications.privateChats) return;
    if (typeof document !== 'undefined' && document.hasFocus()) return;
    if (!('Notification' in window) || Notification.permission !== 'granted') return;
    const title = this.settingsNotifications.showName ? (contact?.name || 'رسالة جديدة') : 'رسالة جديدة';
    const body  = this.settingsNotifications.showText  ? (msg.content || 'مرفق جديد')   : 'رسالة جديدة';
    try {
        new Notification(title, { body, icon: contact?.avatar_url || undefined });
    } catch (_) {}
},

startContactsBgPoll() {
    if (this._contactsBgPollTimer) return;
    const ROUTE = @json($contactsUnreadRoute ?? '#');
    if (!ROUTE || ROUTE === '#') return;
    this._prevUnread = {};
    this._contactsBgPollTimer = setInterval(async () => {
        if (!this.settingsNotifications.desktopEnabled) return;
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        try {
            const r = await fetch(ROUTE, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!r.ok) return;
            const d = await r.json();
            if (!d.success) return;
            for (const c of (d.contacts || [])) {
                const selectedId = this.selectedContact ? Number(this.selectedContact.id) : -1;
                if (Number(c.id) === selectedId) continue;
                const prev = this._prevUnread[c.id] || 0;
                if (c.unreadCount > prev && !document.hasFocus()) {
                    const isGrp = !!(c.isGroup || c.is_group);
                    if (isGrp && !this.settingsNotifications.groups) { this._prevUnread[c.id] = c.unreadCount; continue; }
                    if (!isGrp && !this.settingsNotifications.privateChats) { this._prevUnread[c.id] = c.unreadCount; continue; }
                    const title = this.settingsNotifications.showName ? (c.name || 'رسالة جديدة') : 'رسالة جديدة';
                    const body  = this.settingsNotifications.showText  ? (c.lastMessage || 'رسالة جديدة') : 'رسالة جديدة';
                    try { new Notification(title, { body, icon: c.avatar_url || undefined }); } catch (_) {}
                }
                this._prevUnread[c.id] = c.unreadCount;
                const existing = this.contacts.find(ct => Number(ct.id) === Number(c.id));
                if (existing) existing.unreadCount = c.unreadCount;
            }
        } catch (_) {}
    }, 30000);
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
