/**
 * Media Service - Handling media recording and rendering
 */

class MediaService {
  constructor() {
    this.mediaRecorder = null;
    this.audioChunks = [];
    this.stream = null;
    this.isRecording = false;
  }

  // Audio Recording
  async startAudioRecording() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      console.error('❌ Audio recording not supported by browser');
      this.showError('التسجيل الصوتي غير مدعوم في هذا المتصفح');
      return false;
    }

    if (typeof MediaRecorder === 'undefined') {
      console.error('❌ MediaRecorder unsupported in this browser');
      this.showError('التسجيل الصوتي غير مدعوم في هذا المتصفح');
      return false;
    }

    try {
      this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      this.mediaRecorder = new MediaRecorder(this.stream, {
        mimeType: 'audio/webm;codecs=opus',
      });

      this.audioChunks = [];
      this.isRecording = true;

      this.mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          this.audioChunks.push(event.data);
        }
      };

      this.mediaRecorder.start();
      return true;
    } catch (error) {
      console.error('❌ Failed to start audio recording:', error);
      this.showError('يرجى السماح بالوصول إلى الميكروفون');
      return false;
    }
  }

  stopAudioRecording() {
    if (!this.mediaRecorder) return null;

    return new Promise((resolve) => {
      this.mediaRecorder.onstop = () => {
        this.isRecording = false;
        const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });

        // Clean up
        this.stream.getTracks().forEach(track => track.stop());
        this.audioChunks = [];
        this.mediaRecorder = null;

        resolve(audioBlob);
      };

      this.mediaRecorder.stop();
    });
  }

  // Media rendering
  renderMediaElement(attachment) {
    if (!attachment || !attachment.attachmentUrl) {
      console.warn('❌ Invalid attachment:', attachment);
      return null;
    }

    // Ensure URL is properly formatted
    let url = attachment.attachmentUrl;
    if (url && !url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('/')) {
      url = '/' + url;
    }

    // Update attachment URL
    attachment.attachmentUrl = url;

    // Get filename for type detection - extract from URL, removing query params
    const name = attachment.attachmentName || '';
    let filename = name;
    if (!filename && url) {
      try {
        // Remove query parameters first
        const urlWithoutQuery = url.split('?')[0];
        const urlParts = urlWithoutQuery.split('/');
        filename = urlParts[urlParts.length - 1] || '';
        filename = decodeURIComponent(filename);
      } catch (e) {
        console.warn('Error extracting filename:', e);
        filename = '';
      }
    }

    const lowerFilename = filename.toLowerCase();

    console.log('📎 Attachment detected:', {
      url: url,
      filename: filename,
      mime: attachment.attachmentMime,
      lowerFilename: lowerFilename
    });

    // Check for image extensions - more flexible regex
    if (/\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?.*)?$/i.test(lowerFilename) ||
        /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)/i.test(url) ||
        attachment.attachmentMime?.startsWith('image/')) {
      console.log('✅ Detected as IMAGE');
      return this.renderImage(attachment);
    }

    // Check for video extensions - more flexible regex
    if (/\.(mp4|mov|avi|mkv|webm|flv|wmv|m4v|3gp)(\?.*)?$/i.test(lowerFilename) ||
        /\.(mp4|mov|avi|mkv|webm|flv|wmv|m4v|3gp)/i.test(url) ||
        attachment.attachmentMime?.startsWith('video/')) {
      console.log('✅ Detected as VIDEO');
      return this.renderVideo(attachment);
    }

    // Check for audio extensions - more flexible regex
    if (/\.(mp3|wav|ogg|m4a|aac|flac|wma)(\?.*)?$/i.test(lowerFilename) ||
        /\.(mp3|wav|ogg|m4a|aac|flac|wma)/i.test(url) ||
        attachment.attachmentMime?.startsWith('audio/')) {
      console.log('✅ Detected as AUDIO');
      return this.renderAudio(attachment);
    }

    // For other files, show file attachment
    console.log('📁 Treating as FILE');
    return this.renderFile(attachment);
  }

  renderImage(attachment) {
    const container = document.createElement('div');
    container.className = 'message-media-container image-media';

    // Create image element with professional styling
    const img = document.createElement('img');
    img.src = attachment.attachmentUrl;
    img.alt = attachment.attachmentName || 'صورة مرفقة';
    img.className = 'message-image';
    img.loading = 'lazy';
    img.style.display = 'block';
    img.style.width = '100%';
    img.style.height = 'auto';
    img.style.maxWidth = '300px';
    img.style.maxHeight = '200px';
    img.style.borderRadius = '12px';
    img.style.objectFit = 'cover';
    img.style.cursor = 'pointer';
    img.style.transition = 'all 0.3s ease';
    img.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';

    // Add hover effects
    img.onmouseenter = () => {
      img.style.transform = 'scale(1.02)';
      img.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.15)';
    };
    img.onmouseleave = () => {
      img.style.transform = 'scale(1)';
      img.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
    };

    console.log('🖼️ Rendering image:', attachment.attachmentUrl);

    img.onload = () => {
      console.log('✅ Image loaded successfully:', attachment.attachmentUrl);
    };

    img.onerror = () => {
      console.error('❌ Failed to load image:', attachment.attachmentUrl);
      // Show error placeholder
      container.innerHTML = `
        <div class="media-error-placeholder">
          <i class="ri-error-warning-line"></i>
          <span>فشل في تحميل الصورة</span>
        </div>
      `;
    };

    img.onclick = () => this.openMediaModal(attachment.attachmentUrl);

    container.appendChild(img);
    return container;
  }

  renderVideo(attachment) {
    const container = document.createElement('div');
    container.className = 'message-media-container video-media';

    // Create video element with professional styling
    const video = document.createElement('video');
    video.src = attachment.attachmentUrl;
    video.controls = true;
    video.muted = true;
    video.playsInline = true;
    video.preload = 'metadata';
    video.className = 'message-video';
    video.style.width = '100%';
    video.style.height = 'auto';
    video.style.maxWidth = '300px';
    video.style.maxHeight = '200px';
    video.style.borderRadius = '12px';
    video.style.objectFit = 'cover';
    video.style.cursor = 'pointer';
    video.style.transition = 'all 0.3s ease';
    video.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';

    // Create overlay with play button
    const overlay = document.createElement('div');
    overlay.className = 'video-overlay';
    overlay.innerHTML = `
      <div class="video-play-button">
        <i class="ri-play-circle-fill"></i>
      </div>
      <div class="video-duration">جاري التحميل...</div>
    `;

    container.appendChild(video);
    container.appendChild(overlay);

    // Handle video events
    video.onloadedmetadata = () => {
      console.log('✅ Video metadata loaded:', attachment.attachmentUrl);
      const duration = overlay.querySelector('.video-duration');
      const minutes = Math.floor(video.duration / 60);
      const seconds = Math.floor(video.duration % 60);
      duration.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    video.onplay = () => {
      overlay.style.opacity = '0';
      overlay.style.pointerEvents = 'none';
    };

    video.onpause = () => {
      overlay.style.opacity = '1';
      overlay.style.pointerEvents = 'auto';
    };

    video.onended = () => {
      overlay.style.opacity = '1';
      overlay.style.pointerEvents = 'auto';
    };

    // Click to play/pause
    overlay.onclick = (event) => {
      event.stopPropagation();
      if (video.paused) {
        video.play();
      } else {
        video.pause();
      }
    };

    // Double click to open modal
    container.ondblclick = () => this.openVideoModal(attachment);

    video.onerror = () => {
      console.error('❌ Failed to load video:', attachment.attachmentUrl);
      container.innerHTML = `
        <div class="media-error-placeholder">
          <i class="ri-error-warning-line"></i>
          <span>فشل في تحميل الفيديو</span>
        </div>
      `;
    };

    return container;
  }

  renderAudio(attachment) {
    const container = document.createElement('div');
    container.className = 'message-media-container audio-media';

    // Create professional audio player
    const audioPlayer = document.createElement('div');
    audioPlayer.className = 'audio-player';
    audioPlayer.innerHTML = `
      <div class="audio-header">
        <div class="audio-icon">
          <i class="ri-music-line"></i>
        </div>
        <div class="audio-info">
          <div class="audio-title">${attachment.attachmentName || 'رسالة صوتية'}</div>
          <div class="audio-duration">جاري التحميل...</div>
        </div>
      </div>
      <div class="audio-controls">
        <audio src="${attachment.attachmentUrl}" controls preload="metadata" class="message-audio"></audio>
      </div>
    `;

    container.appendChild(audioPlayer);

    const audio = audioPlayer.querySelector('.message-audio');

    console.log('🔊 Rendering audio:', attachment.attachmentUrl);

    // Handle audio events
    audio.onloadedmetadata = () => {
      console.log('✅ Audio metadata loaded:', attachment.attachmentUrl);
      const duration = audioPlayer.querySelector('.audio-duration');
      const minutes = Math.floor(audio.duration / 60);
      const seconds = Math.floor(audio.duration % 60);
      duration.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    audio.onplay = () => {
      audioPlayer.classList.add('playing');
    };

    audio.onpause = () => {
      audioPlayer.classList.remove('playing');
    };

    // Double click to open modal
    container.ondblclick = () => this.openAudioModal(attachment);

    audio.onerror = () => {
      console.error('❌ Failed to load audio:', attachment.attachmentUrl);
      container.innerHTML = `
        <div class="media-error-placeholder">
          <i class="ri-error-warning-line"></i>
          <span>فشل في تحميل الصوت</span>
        </div>
      `;
    };

    return container;
  }

  renderFile(attachment) {
    const container = document.createElement('a');
    container.href = attachment.attachmentUrl;
    container.target = '_blank';
    container.rel = 'noopener noreferrer';
    container.className = 'message-file-attachment';

    // Extract file name from URL if attachmentName is not provided
    let name = attachment.attachmentName || 'ملف مرفق';
    if (!attachment.attachmentName && attachment.attachmentUrl) {
      try {
        const urlParts = attachment.attachmentUrl.split('/');
        name = urlParts[urlParts.length - 1] || 'ملف مرفق';
        // Decode URL encoding
        name = decodeURIComponent(name);
      } catch (e) {
        name = 'ملف مرفق';
      }
    }

    container.title = name;

    // Try to get MIME type from attachment or guess from filename
    let mime = attachment.attachmentMime || '';
    if (!mime && name !== 'ملف مرفق') {
      mime = this.guessMimeTypeFromFilename(name);
    }

    const fileType = this.getFileTypeFromMime(mime, name);
    const iconClass = this.getFileIconClass(mime, name);

    container.innerHTML = `
      <div class="file-attachment-icon">
        <i class="${iconClass}"></i>
      </div>
      <div class="file-attachment-info">
        <div class="file-attachment-name">${name}</div>
        <div class="file-attachment-type">${fileType}</div>
      </div>
      <div class="file-attachment-action">
        <i class="ri-download-cloud-line"></i>
      </div>
    `;

    return container;
  }

  guessMimeTypeFromFilename(filename) {
    const lowerName = filename.toLowerCase();

    if (lowerName.endsWith('.pdf')) return 'application/pdf';
    if (lowerName.endsWith('.doc')) return 'application/msword';
    if (lowerName.endsWith('.docx')) return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    if (lowerName.endsWith('.xls')) return 'application/vnd.ms-excel';
    if (lowerName.endsWith('.xlsx')) return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    if (lowerName.endsWith('.ppt')) return 'application/vnd.ms-powerpoint';
    if (lowerName.endsWith('.pptx')) return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    if (/\.(jpg|jpeg)$/i.test(lowerName)) return 'image/jpeg';
    if (lowerName.endsWith('.png')) return 'image/png';
    if (lowerName.endsWith('.gif')) return 'image/gif';
    if (lowerName.endsWith('.webp')) return 'image/webp';
    if (lowerName.endsWith('.svg')) return 'image/svg+xml';
    if (lowerName.endsWith('.mp4')) return 'video/mp4';
    if (lowerName.endsWith('.mov')) return 'video/quicktime';
    if (lowerName.endsWith('.avi')) return 'video/x-msvideo';
    if (lowerName.endsWith('.mkv')) return 'video/x-matroska';
    if (lowerName.endsWith('.webm')) return 'video/webm';
    if (lowerName.endsWith('.mp3')) return 'audio/mpeg';
    if (lowerName.endsWith('.wav')) return 'audio/wav';
    if (lowerName.endsWith('.ogg')) return 'audio/ogg';
    if (lowerName.endsWith('.m4a')) return 'audio/mp4';
    if (lowerName.endsWith('.aac')) return 'audio/aac';
    if (lowerName.endsWith('.zip')) return 'application/zip';
    if (lowerName.endsWith('.rar')) return 'application/x-rar-compressed';
    if (lowerName.endsWith('.7z')) return 'application/x-7z-compressed';
    if (lowerName.endsWith('.txt')) return 'text/plain';

    return 'application/octet-stream';
  }

  getFileTypeFromMime(mime, name) {
    const lowerName = (name || '').toLowerCase();

    // Check MIME types first
    if (mime.includes('pdf') || lowerName.endsWith('.pdf')) return 'PDF';
    if (mime.includes('word') || lowerName.includes('.doc') || lowerName.includes('.docx')) return 'مستند';
    if (mime.includes('sheet') || mime.includes('excel') || lowerName.includes('.xls') || lowerName.includes('.xlsx')) return 'جدول بيانات';
    if (mime.includes('presentation') || lowerName.includes('.ppt') || lowerName.includes('.pptx')) return 'عرض شرائح';
    if (mime.includes('image') || /\.(jpg|jpeg|png|gif|webp|svg|bmp)$/i.test(lowerName)) return 'صورة';
    if (mime.includes('video') || /\.(mp4|mov|avi|mkv|webm|flv)$/i.test(lowerName)) return 'فيديو';
    if (mime.includes('audio') || /\.(mp3|wav|ogg|webm|m4a|aac)$/i.test(lowerName)) return 'صوت';
    if (lowerName.endsWith('.zip') || lowerName.endsWith('.rar') || lowerName.endsWith('.7z')) return 'أرشيف';
    if (lowerName.endsWith('.txt')) return 'نص';

    return 'ملف';
  }

  getFileIconClass(mime, name) {
    const lowerName = (name || '').toLowerCase();

    if (mime.includes('pdf') || lowerName.endsWith('.pdf')) return 'ri-file-pdf-line';
    if (mime.includes('word') || lowerName.includes('.doc') || lowerName.includes('.docx')) return 'ri-file-word-line';
    if (mime.includes('sheet') || mime.includes('excel') || lowerName.includes('.xls') || lowerName.includes('.xlsx')) return 'ri-file-excel-line';
    if (mime.includes('presentation') || lowerName.includes('.ppt') || lowerName.includes('.pptx')) return 'ri-file-pptx-line';
    if (mime.includes('image') || /\.(jpg|jpeg|png|gif|webp|svg|bmp)$/i.test(lowerName)) return 'ri-image-line';
    if (mime.includes('video') || /\.(mp4|mov|avi|mkv|webm|flv)$/i.test(lowerName)) return 'ri-video-line';
    if (mime.includes('audio') || /\.(mp3|wav|ogg|webm|m4a|aac)$/i.test(lowerName)) return 'ri-music-line';
    if (lowerName.endsWith('.zip') || lowerName.endsWith('.rar') || lowerName.endsWith('.7z')) return 'ri-file-zip-line';
    if (lowerName.endsWith('.txt')) return 'ri-file-text-line';

    return 'ri-file-line';
  }

  openMediaModal(url) {
    const modal = document.createElement('div');
    modal.className = 'media-modal';
    modal.innerHTML = `
      <div class="media-modal-content">
        <button class="modal-close" aria-label="إغلاق">×</button>
        <img src="${url}" alt="الصورة" class="modal-image">
      </div>
    `;

    modal.querySelector('.modal-close').onclick = () => modal.remove();
    modal.onclick = (e) => {
      if (e.target === modal) modal.remove();
    };

    document.body.appendChild(modal);
  }

  openVideoModal(attachment) {
    const modal = document.createElement('div');
    modal.className = 'media-modal video-modal';
    modal.innerHTML = `
      <div class="media-modal-content">
        <button class="modal-close" aria-label="إغلاق">×</button>
        <video src="${attachment.attachmentUrl}" controls autoplay class="modal-video" playsinline></video>
        <div class="modal-video-title">${attachment.attachmentName || 'فيديو'}</div>
      </div>
    `;

    modal.querySelector('.modal-close').onclick = () => modal.remove();
    modal.onclick = (e) => {
      if (e.target === modal) modal.remove();
    };

    document.body.appendChild(modal);
  }

  openAudioModal(attachment) {
    const modal = document.createElement('div');
    modal.className = 'media-modal audio-modal';
    modal.innerHTML = `
      <div class="media-modal-content">
        <button class="modal-close" aria-label="إغلاق">×</button>
        <div class="audio-modal-header">
          <div class="audio-modal-icon">
            <i class="ri-music-line"></i>
          </div>
          <div class="audio-modal-info">
            <div class="audio-modal-title">${attachment.attachmentName || 'رسالة صوتية'}</div>
            <div class="audio-modal-meta">اضغط للتشغيل</div>
          </div>
        </div>
        <audio src="${attachment.attachmentUrl}" controls autoplay class="modal-audio"></audio>
      </div>
    `;

    modal.querySelector('.modal-close').onclick = () => modal.remove();
    modal.onclick = (e) => {
      if (e.target === modal) modal.remove();
    };

    document.body.appendChild(modal);
  }

  getFileMimeType(file) {
    return file.type || 'application/octet-stream';
  }

  getFileIcon(mime) {
    if (mime.startsWith('image/')) return 'ri-image-line';
    if (mime.startsWith('video/')) return 'ri-video-line';
    if (mime.startsWith('audio/')) return 'ri-music-line';
    if (mime.includes('pdf')) return 'ri-file-pdf-line';
    if (mime.includes('word') || mime.includes('document')) return 'ri-file-word-line';
    if (mime.includes('sheet') || mime.includes('excel')) return 'ri-file-excel-line';
    return 'ri-file-line';
  }

  showError(message) {
    const alert = document.createElement('div');
    alert.className = 'message-error-alert';
    alert.textContent = message;
    alert.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #dc2626;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      z-index: 9999;
      animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
  }

  showSuccess(message) {
    const alert = document.createElement('div');
    alert.className = 'message-success-alert';
    alert.textContent = message;
    alert.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      z-index: 9999;
      animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 2000);
  }
}

export default new MediaService();
