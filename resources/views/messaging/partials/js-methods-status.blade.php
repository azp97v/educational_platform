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

addEmojiToStatus(e) {
    this.statusEditor.texts.push({
        content: e,
        textPosX: 50,
        textPosY: 50,
        scale: 2.5,
        rotate: 0,
        fontStyle: 'Segoe UI Emoji',
        textBgStyle: 'none',
        fontSize: 56,
        textColor: '#ffffff',
    });
    this.statusDrawerOpen = false;
    this.statusEditor.activeTextIndex = this.statusEditor.texts.length - 1;
    this.statusFocusState = 0;
},
startStatusTextDrag(e, ti) {
    this.statusEditor.activeTextIndex = ti;
    this.statusEditor.fontSize = this.statusEditor.texts[ti]?.fontSize || 28;
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
            this.statusEditor.fontSize = active.fontSize || 28;
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
    this.statusEditor.fontSize = 28;
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

this.statusEditor = { type: 'text', textContent: '', textColor: '#ffffff', fontStyle: 'Tajawal', fontSize: 28, textPosX: 50, textPosY: 50, rotate: 0, bgColor: this.randomGradient(), textBgStyle: 'none', filterStyle: null, durationHours: 24, privacyType: 'all', mediaFile: null, mediaPreview: null, mediaPosX: 50, mediaPosY: 50, mediaScale: 1, mediaRotate: 0, audioFile: null, texts: [], activeTextIndex: -1 };
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

cycleMediaFit() {
this.statusEditor.mediaFit = this.statusEditor.mediaFit === 'contain' ? 'cover' : 'contain';
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

cycleTextAlign() {
const order = ['center', 'right', 'left'];
const active = this.seActiveText();
if (!active) return;
const idx = order.indexOf(active.textAlign || 'center');
active.textAlign = order[(idx + 1) % order.length];
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

if (this.statusEditor.mediaPosX != null) fd.append('media_pos_x', this.statusEditor.mediaPosX);
if (this.statusEditor.mediaPosY != null) fd.append('media_pos_y', this.statusEditor.mediaPosY);
fd.append('media_scale', this.statusEditor.mediaScale ?? 1);
fd.append('media_rotate', this.statusEditor.mediaRotate ?? 0);
fd.append('media_fit', this.statusEditor.mediaFit || 'contain');

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

mediaPosX: s.mediaPosX ?? 50,
mediaPosY: s.mediaPosY ?? 50,
mediaScale: s.mediaScale ?? 1,
mediaRotate: s.mediaRotate ?? 0,
mediaFit: s.mediaFit || 'contain',

audioFile: null,

editingStatusId: s.id,
texts: this.getStatusTextLayers(s),
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
this.showQuickEmojiBar = false;
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