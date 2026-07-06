/**
 * Telegram-Style Messaging App
 * Modern, Clean, Professional
 */

import db from './db.js';
import api from './api.js';
import media from './media.js';

class TelegramMessagingApp {
  constructor() {
    this.currentChat = null;
    this.contacts = [];
    this.recordingStartTime = null;
    this.isRecording = false;
    this.recordingHoldTimer = null;
    this.recordingHoldDelay = 700;
    this.recordingHoldTriggered = false;
    this.audioPressActive = false;

    this.els = {
      chatsList: document.getElementById('chatsList'),
      messagesContainer: document.getElementById('messagesContainer'),
      chatName: document.getElementById('chatName'),
      chatStatus: document.getElementById('chatStatus'),
      messageInput: document.getElementById('messageInput'),
      sendBtn: document.getElementById('sendBtn'),
      micBtn: document.getElementById('micBtn'),
      attachBtn: document.getElementById('attachBtn'),
      fileInput: document.getElementById('fileInput'),
      searchInput: document.getElementById('searchInput'),
      headerAvatar: document.getElementById('headerAvatar'),
    };

    this.init();
  }

  async init() {
    await db.ready;
    this.setupEvents();

    // Check authentication first
    if (!window.messagingConfig.isAuthenticated) {
      console.error('❌ User not authenticated');
      this.showError('يجب تسجيل الدخول أولاً');
      return;
    }

    console.log('🚀 Initializing messaging app');
    console.log('📊 window.messagingConfig:', window.messagingConfig);
    await this.loadChats();

    // Load initial messages if there's a selected contact
    if (window.messagingConfig.selectedContactId) {
      console.log('[messaging] Selected contact ID:', window.messagingConfig.selectedContactId);
      const rawContacts = window.messagingConfig.contacts || [];
      const parsedContacts = typeof rawContacts === 'string' ? JSON.parse(rawContacts) : rawContacts;
      const contacts = Array.isArray(parsedContacts) ? parsedContacts : Object.values(parsedContacts || {});

      const selectedContact = contacts.find(c => c.id == window.messagingConfig.selectedContactId);
      if (selectedContact) {
        console.log('[messaging] Opening chat with:', selectedContact);
        await this.openChat(selectedContact);
      }
    } else if (window.messagingConfig.initialMessages) {
      // Load initial messages if available
      console.log('💬 Loading initial messages');
      const initialMessages = typeof window.messagingConfig.initialMessages === 'string'
        ? JSON.parse(window.messagingConfig.initialMessages)
        : window.messagingConfig.initialMessages;

      if (initialMessages.length > 0) {
        console.log('📨 Rendering', initialMessages.length, 'messages');
        this.renderMessages(initialMessages);
      }
    }

    console.log('✓ Telegram Messaging App Ready');
  }

  setupEvents() {
    // Send message
    this.els.sendBtn.addEventListener('click', () => this.sendMessage());
    this.els.messageInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.sendMessage();
      }
    });

    // Auto-resize textarea
    this.els.messageInput.addEventListener('input', () => this.autoResizeInput());

    // Audio (long-press to start, click to stop when active)
    this.setupAudioRecordingHoldEvents();

    // File
    this.els.attachBtn.addEventListener('click', () => this.els.fileInput.click());
    this.els.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));

    // Search
    this.els.searchInput.addEventListener('input', (e) => this.filterChats(e.target.value));

    // Auto-read on scroll (Telegram style)
    this.els.messagesContainer.addEventListener('scroll', () => this.autoReadMessagesOnScroll());
    this.els.messagesContainer.addEventListener('wheel', () => this.autoReadMessagesOnScroll());

    // Fix attachments button (for teachers only)
    const fixBtn = document.getElementById('fixAttachmentsBtn');
    if (fixBtn) {
      fixBtn.addEventListener('click', () => this.fixOldAttachments());
    }
  }

  setupAudioRecordingHoldEvents() {
    if (!this.els.micBtn) return;

    const startPress = (e) => {
      if (this.isRecording || this.els.micBtn.disabled) return;
      if (e && typeof e.preventDefault === 'function') e.preventDefault();

      this.audioPressActive = true;
      this.recordingHoldTriggered = false;
      this.clearRecordingHoldTimer();

      this.recordingHoldTimer = setTimeout(async () => {
        if (!this.audioPressActive || this.isRecording) return;
        this.recordingHoldTriggered = true;
        await this.toggleAudioRecording();
      }, this.recordingHoldDelay);
    };

    const endPress = (e) => {
      if (e && typeof e.preventDefault === 'function') e.preventDefault();
      this.audioPressActive = false;
      this.clearRecordingHoldTimer();
    };

    const clickHandler = async (e) => {
      // Keep stop action immediate while recording
      if (this.isRecording) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        await this.toggleAudioRecording();
        return;
      }

      // Ignore short clicks when not recording
      if (e && typeof e.preventDefault === 'function') e.preventDefault();
    };

    this.els.micBtn.addEventListener('pointerdown', startPress);
    this.els.micBtn.addEventListener('pointerup', endPress);
    this.els.micBtn.addEventListener('pointerleave', endPress);
    this.els.micBtn.addEventListener('pointercancel', endPress);
    this.els.micBtn.addEventListener('click', clickHandler);

    // Fallbacks
    this.els.micBtn.addEventListener('touchstart', startPress, { passive: false });
    this.els.micBtn.addEventListener('touchend', endPress, { passive: false });
    this.els.micBtn.addEventListener('touchcancel', endPress, { passive: false });
    this.els.micBtn.addEventListener('mousedown', startPress);
    this.els.micBtn.addEventListener('mouseup', endPress);
    this.els.micBtn.addEventListener('mouseleave', endPress);

    window.addEventListener('blur', () => {
      this.audioPressActive = false;
      this.clearRecordingHoldTimer();
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.audioPressActive = false;
        this.clearRecordingHoldTimer();
      }
    });
  }

  clearRecordingHoldTimer() {
    if (this.recordingHoldTimer) {
      clearTimeout(this.recordingHoldTimer);
      this.recordingHoldTimer = null;
    }
  }

  autoReadMessagesOnScroll() {
    if (!this.currentChat) return;

    const scrollTop = this.els.messagesContainer.scrollTop;
    const containerHeight = this.els.messagesContainer.clientHeight;

    // Get all unread received messages
    const unreadMessages = this.els.messagesContainer.querySelectorAll('.message.received.unread');

    unreadMessages.forEach(msgEl => {
      const rect = msgEl.getBoundingClientRect();
      const containerRect = this.els.messagesContainer.getBoundingClientRect();

      // Check if message is in viewport (with some tolerance)
      const isInViewport = rect.top >= containerRect.top - 50 && rect.bottom <= containerRect.bottom + 50;

      if (isInViewport) {
        const messageId = msgEl.dataset.messageId;
        if (messageId) {
          // Mark as read
          this.markMessageAsRead(messageId);
          msgEl.classList.remove('unread');

          // Update the separator if all unread messages are now read
          const remainingUnread = this.els.messagesContainer.querySelectorAll('.message.received.unread');
          if (remainingUnread.length === 0) {
            const separator = this.els.messagesContainer.querySelector('.unread-separator');
            if (separator) {
              separator.remove();
            }
          }
        }
      }
    });
  }

  markMessageAsRead(messageId) {
    if (!this.currentChat) return;

    // Debounce to avoid multiple API calls
    if (!this.readMessageQueue) this.readMessageQueue = {};
    if (this.readMessageQueue[messageId]) return;

    this.readMessageQueue[messageId] = true;

    api.markAsRead(this.currentChat.id).catch(err => {
      console.error('Failed to mark as read:', err);
      delete this.readMessageQueue[messageId];
    });
  }

  async loadChats() {
    try {
      console.log('[messaging] Loading chats, window.messagingConfig:', window.messagingConfig);
      const rawContacts = window.messagingConfig.contacts || [];
      console.log('📋 Raw contacts:', rawContacts);

      const parsedContacts = typeof rawContacts === 'string' ? JSON.parse(rawContacts) : rawContacts;
      const contactsArray = Array.isArray(parsedContacts) ? parsedContacts : Object.values(parsedContacts || {});
      console.log('📋 Normalized contacts:', contactsArray);

      if (!contactsArray || contactsArray.length === 0) {
        console.log('⚠️ No contacts found, showing empty state');
        this.els.chatsList.innerHTML = `
          <div style="padding: 20px; text-align: center; color: #999;">
            <p>لا توجد محادثات</p>
            <small>تحقق من اتصالك بالإنترنت أو أعد تحميل الصفحة</small>
          </div>
        `;
        return;
      }

      console.log('✅ Found', parsedContacts.length, 'contacts');

      this.contacts = Array.isArray(parsedContacts) ? parsedContacts : Object.values(parsedContacts || {});

      // Save to DB
      await Promise.all(this.contacts.map(c => db.saveContact(c)));

      // Render
      this.renderChats(this.contacts);
      console.log('✅ Chats rendered successfully');
    } catch (error) {
      console.error('❌ Failed to load chats:', error);
      this.els.chatsList.innerHTML = `
        <div style="padding: 20px; text-align: center; color: #ff6b6b;">
          <p>خطأ في تحميل المحادثات</p>
          <small>${error.message}</small>
        </div>
      `;
    }
  }

  renderChats(contacts = this.contacts) {
    if (!Array.isArray(contacts)) contacts = [];
    console.log('[messaging] Rendering', contacts.length, 'chats');
    this.els.chatsList.innerHTML = '';

    contacts.forEach((contact, index) => {
      console.log(`[messaging] Rendering contact ${index + 1}:`, contact);
      const el = document.createElement('div');
      el.className = `chat-item ${contact.selected ? 'active' : ''}`;
      el.dataset.contactId = contact.id;

      const avatar = document.createElement('div');
      avatar.className = `chat-avatar ${contact.isOnline ? 'online' : ''}`;

      // Check if contact has avatar
      if (contact.avatar_url) {
        const img = document.createElement('img');
        img.src = `/storage/${contact.avatar_url}`;
        img.alt = contact.name;
        img.onerror = () => {
          // Fallback to initial if image fails to load
          avatar.innerHTML = '';
          avatar.textContent = contact.name.charAt(0);
        };
        avatar.appendChild(img);
      } else {
        avatar.textContent = contact.name.charAt(0);
      }

      const info = document.createElement('div');
      info.className = 'chat-info';

      const headerRow = document.createElement('div');
      headerRow.className = 'chat-header-row';

      const name = document.createElement('div');
      name.className = 'chat-name';
      name.textContent = contact.name;

      const time = document.createElement('div');
      time.className = 'chat-time';
      const msgTimeValue = contact.lastMessageTime || contact.last_message_time || contact.lastMessageTimeString || null;
      const msgTime = msgTimeValue ? this.parseDateTime(msgTimeValue) : null;
      time.textContent = msgTime ? this.formatTime(msgTime) : '';

      headerRow.appendChild(name);
      headerRow.appendChild(time);

      const preview = document.createElement('div');
      preview.className = 'chat-preview';
      preview.textContent = contact.lastMessage || 'لا توجد رسائل';

      info.appendChild(headerRow);
      info.appendChild(preview);

      const badge = document.createElement('div');
      badge.className = 'chat-badge';

      if (contact.unreadCount > 0) {
        const unread = document.createElement('div');
        unread.className = 'unread-count';
        unread.textContent = contact.unreadCount > 99 ? '99+' : contact.unreadCount;
        badge.appendChild(unread);
      }

      el.appendChild(avatar);
      el.appendChild(info);
      el.appendChild(badge);

      el.addEventListener('click', () => this.openChat(contact));
      this.els.chatsList.appendChild(el);
    });

    console.log('✅ All chats rendered');
  }

  findContactById(contactId) {
    return this.contacts.find(c => Number(c.id) === Number(contactId));
  }

  updateContactPreview(contactId, updates = {}) {
    if (!Array.isArray(this.contacts)) return;

    this.contacts.forEach((contact) => {
      if (Number(contact.id) === Number(contactId)) {
        Object.assign(contact, updates);
        if (updates.selected !== undefined) {
          contact.selected = updates.selected;
        }
      } else if (updates.selected) {
        contact.selected = false;
      }
    });

    if (this.currentChat && Number(this.currentChat.id) === Number(contactId)) {
      Object.assign(this.currentChat, updates);
    }

    this.updateChatItemDOM(contactId, updates);
  }

  updateChatItemDOM(contactId, updates = {}) {
    const chatEl = this.els.chatsList.querySelector(`.chat-item[data-contact-id="${contactId}"]`);
    if (!chatEl) return;

    const preview = chatEl.querySelector('.chat-preview');
    if (updates.lastMessage !== undefined && preview) {
      preview.textContent = updates.lastMessage || 'لا توجد رسائل';
    }

    const timeEl = chatEl.querySelector('.chat-time');
    if (updates.lastMessageTime !== undefined && timeEl) {
      const msgTime = this.parseDateTime(updates.lastMessageTime);
      timeEl.textContent = msgTime ? this.formatTime(msgTime) : '';
    }

    if (updates.unreadCount !== undefined) {
      const badge = chatEl.querySelector('.chat-badge');
      let unreadEl = badge?.querySelector('.unread-count');
      if (updates.unreadCount > 0) {
        if (!badge) {
          const newBadge = document.createElement('div');
          newBadge.className = 'chat-badge';
          chatEl.appendChild(newBadge);
        }
        if (!unreadEl) {
          unreadEl = document.createElement('div');
          unreadEl.className = 'unread-count';
          chatEl.querySelector('.chat-badge').appendChild(unreadEl);
        }
        unreadEl.textContent = updates.unreadCount > 99 ? '99+' : updates.unreadCount;
      } else if (unreadEl) {
        unreadEl.remove();
      }
    }

    if (updates.selected !== undefined) {
      chatEl.classList.toggle('active', Boolean(updates.selected));
      if (!updates.selected) {
        chatEl.classList.remove('active');
      }
    }
  }

  async openChat(contact, options = {}) {
    try {
      const storedContact = this.findContactById(contact.id) || contact;
      this.currentChat = storedContact;

      this.updateContactPreview(storedContact.id, { selected: true });

      // Update active state
      document.querySelectorAll('.chat-item').forEach(el => {
        el.classList.toggle('active', el.dataset.contactId == storedContact.id);
      });

      // Update header
      this.els.chatName.textContent = contact.name;

      // Update header avatar
      this.els.headerAvatar.innerHTML = '';
      if (contact.avatar_url) {
        const img = document.createElement('img');
        img.src = `/storage/${contact.avatar_url}`;
        img.alt = contact.name;
        img.onerror = () => {
          this.els.headerAvatar.textContent = contact.name.charAt(0);
        };
        this.els.headerAvatar.appendChild(img);
      } else {
        this.els.headerAvatar.textContent = contact.name.charAt(0);
      }

      this.updateStatus(contact);

      // Load messages
      const messages = await this.loadMessages(contact.id);

      if (messages.length > 0) {
        const lastMessage = messages[messages.length - 1];
        this.updateContactPreview(contact.id, {
          lastMessage: lastMessage.content || 'مرفق',
          lastMessageTime: lastMessage.createdAt || lastMessage.created_at || lastMessage.timestamp,
          unreadCount: 0,
          selected: true,
        });
      }

      this.renderMessages(messages, { forceBottom: options.forceBottom });

      // Mark as read (but don't scroll yet - renderMessages will handle scrolling)
      await api.markAsRead(contact.id);

      // Focus input
      this.els.messageInput.focus();
    } catch (error) {
      console.error('❌ Failed to open chat:', error);
      this.showError('فشل تحميل المحادثة');
    }
  }

  async loadMessages(contactId) {
    try {
      const response = await api.refreshMessages(contactId);

      if (response.success && response.data.messages) {
        const messages = response.data.messages;
        await db.saveMessages(messages);
        return messages;
      }
    } catch (error) {
      console.warn('⚠️ API failed, using local cache:', error);
    }

    // Fallback to local cache
    try {
      return await db.getConversation(window.messagingConfig.userId, contactId);
    } catch (dbError) {
      console.error('❌ DB error:', dbError);
      return [];
    }
  }

  renderMessages(messages, options = {}) {
    const forceBottom = options.forceBottom === true;
    this.els.messagesContainer.innerHTML = '';

    if (!Array.isArray(messages) || messages.length === 0) {
      this.els.messagesContainer.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon"><i class="ri-mail-open-line"></i></div>
          <div>لا توجد رسائل</div>
        </div>
      `;
      return;
    }

    // Determine if there are unread received messages
    const hasUnreadMessages = !forceBottom && messages.some(msg => {
      const isReceived = msg.sender_id !== window.messagingConfig.userId && msg.senderId !== window.messagingConfig.userId;
      return isReceived && (!msg.read_at && !msg.readAt);
    });

    let separatorAdded = false;
    let lastReadMessageIndex = -1;

    // Find the last read message index
    if (hasUnreadMessages) {
      for (let i = messages.length - 1; i >= 0; i--) {
        const msg = messages[i];
        const isReceived = msg.sender_id !== window.messagingConfig.userId && msg.senderId !== window.messagingConfig.userId;
        if (isReceived && (msg.read_at || msg.readAt)) {
          lastReadMessageIndex = i;
          break;
        }
      }
    }

    messages.forEach((msg, index) => {
      const isSent = msg.sender_id === window.messagingConfig.userId || msg.senderId === window.messagingConfig.userId;
      const isReceived = !isSent;
      const isRead = isReceived && (msg.read_at || msg.readAt);
      const isUnread = isReceived && !isRead;

      // Add separator before first unread message
      if (hasUnreadMessages && !separatorAdded && isUnread && index > lastReadMessageIndex) {
        const separator = document.createElement('div');
        separator.className = 'unread-separator';
        separator.innerHTML = `
          <div class="unread-separator-text">رسائل جديدة</div>
        `;
        this.els.messagesContainer.appendChild(separator);
        separatorAdded = true;
      }

      const msgEl = document.createElement('div');
      msgEl.className = `message ${isSent ? 'sent' : 'received'} ${isUnread ? 'unread' : ''}`;
      msgEl.dataset.messageId = msg.id;

      const bubble = document.createElement('div');
      bubble.className = 'message-bubble';

      let hasContent = false;

      // Show reply if exists
      if (msg.reply_to && msg.repliedMessage) {
        const replyEl = document.createElement('div');
        replyEl.className = 'message-reply';
        replyEl.innerHTML = '<div class="reply-line"></div><div class="reply-content"><div class="reply-label">رد على <strong></strong></div><div class="reply-text"></div></div>';
        replyEl.querySelector('strong').textContent = msg.repliedMessage.senderName || msg.repliedMessage.sender_name || 'رسالة';
        replyEl.querySelector('.reply-text').textContent = msg.repliedMessage.content || msg.repliedMessage.text || 'مرفق';
        bubble.appendChild(replyEl);
      }

      if (msg.content && msg.content.trim()) {
        const content = document.createElement('div');
        content.className = 'message-content';
        content.textContent = msg.content;
        bubble.appendChild(content);
        hasContent = true;
      }

      const attachmentUrl = msg.attachmentUrl || msg.attachment_url || (msg.attachment_path ? `/storage/${msg.attachment_path}` : null);
      if (attachmentUrl) {
        let finalUrl = attachmentUrl;
        if (finalUrl) {
          if (finalUrl.startsWith('message_attachments/') || finalUrl.startsWith('message_audio/')) {
            finalUrl = '/storage/' + finalUrl;
          } else if (!finalUrl.startsWith('http://') && !finalUrl.startsWith('https://') && !finalUrl.startsWith('/storage/') && !finalUrl.startsWith('/')) {
            finalUrl = '/storage/' + finalUrl;
          }
        }

        const mediaEl = media.renderMediaElement({
          attachmentUrl: finalUrl,
          attachmentMime: msg.attachmentMime || msg.attachment_mime || msg.attachment_type,
          attachmentName: msg.attachmentName || msg.attachment_name,
        });

        if (mediaEl) {
          if (hasContent) {
            mediaEl.style.marginTop = '8px';
          }
          bubble.appendChild(mediaEl);
        }
      }

      const footer = document.createElement('div');
      footer.className = 'message-footer';

      const timeEl = document.createElement('span');
      timeEl.className = 'message-time';
      const msgTime = this.parseDateTime(msg.created_at || msg.createdAt || msg.timestamp);
      timeEl.textContent = msgTime
        ? msgTime.toLocaleTimeString('ar-SA', { timeZone: 'Asia/Riyadh', hour: '2-digit', minute: '2-digit', hour12: false })
        : 'Unknown';
      footer.appendChild(timeEl);

      if (isSent) {
        const status = document.createElement('span');
        status.className = 'message-status';
        status.innerHTML = msg.read_at || msg.readAt ? '✓✓' : '✓';
        footer.appendChild(status);
      }

      bubble.appendChild(footer);
      msgEl.appendChild(bubble);

      if (isSent) {
        msgEl.addEventListener('contextmenu', (e) => {
          e.preventDefault();
          this.showMessageContextMenu(msg, e, msgEl);
        });
        msgEl.addEventListener('touchstart', (e) => {
          if (e.touches.length === 1) {
            this.longPressTimer = setTimeout(() => {
              this.showMessageContextMenu(msg, e, msgEl);
            }, 500);
          }
        });
        msgEl.addEventListener('touchend', () => {
          clearTimeout(this.longPressTimer);
        });
      }

      this.els.messagesContainer.appendChild(msgEl);
    });

    // Add media load listeners for scroll adjustment
    this.els.messagesContainer.querySelectorAll('img, video, audio').forEach(el => {
      el.addEventListener('loadeddata', () => this.scrollToBottom());
      el.addEventListener('loadedmetadata', () => this.scrollToBottom());
      el.addEventListener('load', () => this.scrollToBottom());
    });

    // Schedule scroll operations based on unread messages
    this.scheduleScroll(hasUnreadMessages, forceBottom);
  }

  createMessageElement(msg) {
    const isSent = msg.sender_id === window.messagingConfig.userId || msg.senderId === window.messagingConfig.userId;
    const isReceived = !isSent;
    const isRead = isReceived && (msg.read_at || msg.readAt || msg.readAt !== undefined);
    const isUnread = isReceived && !isRead;

    const msgEl = document.createElement('div');
    msgEl.className = `message ${isSent ? 'sent' : 'received'} ${isUnread ? 'unread' : ''}`;
    msgEl.dataset.messageId = msg.id;

    const bubble = document.createElement('div');
    bubble.className = 'message-bubble';

    let hasContent = false;

      // Show reply if exists
      if (msg.reply_to && msg.repliedMessage) {
        const replyEl = document.createElement('div');
        replyEl.className = 'message-reply';
        replyEl.innerHTML = '<div class="reply-line"></div><div class="reply-content"><div class="reply-label">رد على <strong></strong></div><div class="reply-text"></div></div>';
        replyEl.querySelector('strong').textContent = msg.repliedMessage.senderName || msg.repliedMessage.sender_name || 'رسالة';
        replyEl.querySelector('.reply-text').textContent = msg.repliedMessage.content || msg.repliedMessage.text || 'مرفق';
        bubble.appendChild(replyEl);
      }

      if (msg.content && msg.content.trim()) {
      const content = document.createElement('div');
      content.className = 'message-content';
      content.textContent = msg.content;
      bubble.appendChild(content);
      hasContent = true;
    }

    const attachmentUrl = msg.attachmentUrl || msg.attachment_url || (msg.attachment_path ? `/storage/${msg.attachment_path}` : null);
    if (attachmentUrl) {
      let finalUrl = attachmentUrl;
      if (finalUrl.startsWith('message_attachments/') || finalUrl.startsWith('message_audio/')) {
        finalUrl = '/storage/' + finalUrl;
      } else if (!finalUrl.startsWith('http://') && !finalUrl.startsWith('https://') && !finalUrl.startsWith('/storage/') && !finalUrl.startsWith('/')) {
        finalUrl = '/storage/' + finalUrl;
      }

      const mediaEl = media.renderMediaElement({
        attachmentUrl: finalUrl,
        attachmentMime: msg.attachmentMime || msg.attachment_mime || msg.attachment_type,
        attachmentName: msg.attachmentName || msg.attachment_name,
      });

      if (mediaEl) {
        if (hasContent) {
          mediaEl.style.marginTop = '8px';
        }
        bubble.appendChild(mediaEl);
      }
    }

    const footer = document.createElement('div');
    footer.className = 'message-footer';

    const timeEl = document.createElement('span');
    timeEl.className = 'message-time';
    const msgTime = this.parseDateTime(msg.created_at || msg.createdAt || msg.timestamp);
    timeEl.textContent = msgTime
      ? msgTime.toLocaleTimeString('ar-SA', { timeZone: 'Asia/Riyadh', hour: '2-digit', minute: '2-digit', hour12: false })
      : 'Unknown';
    footer.appendChild(timeEl);

    if (isSent) {
      const status = document.createElement('span');
      status.className = 'message-status';
      status.innerHTML = msg.read_at || msg.readAt ? '✓✓' : '✓';
      footer.appendChild(status);
    }

    bubble.appendChild(footer);
    msgEl.appendChild(bubble);

    if (isSent) {
      msgEl.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        this.showMessageContextMenu(msg, e, msgEl);
      });
      msgEl.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1) {
          this.longPressTimer = setTimeout(() => {
            this.showMessageContextMenu(msg, e, msgEl);
          }, 500);
        }
      });
      msgEl.addEventListener('touchend', () => {
        clearTimeout(this.longPressTimer);
      });
    }

    return msgEl;
  }

  appendMessageToCurrentChat(msg, options = {}) {
    if (!this.currentChat || !this.els.messagesContainer) return;

    const msgEl = this.createMessageElement(msg);
    this.els.messagesContainer.appendChild(msgEl);
    this.scrollToBottom(true);
    this.els.messagesContainer.scrollTop = this.els.messagesContainer.scrollHeight;

    if (options.updatePreview) {
      const previewText = msg.content || msg.attachmentName || 'مرفق';
      this.updateContactPreview(this.currentChat.id, {
        lastMessage: previewText,
        lastMessageTime: msg.created_at || msg.createdAt || msg.timestamp,
        unreadCount: 0,
      });
    }
  }

  scheduleScroll(hasUnreadMessages = false, forceBottom = false) {
    if (forceBottom) {
      setTimeout(() => this.scrollToBottom(true), 20);
      setTimeout(() => this.scrollToBottom(true), 120);
      setTimeout(() => this.scrollToBottom(true), 240);
      return;
    }

    if (hasUnreadMessages) {
      setTimeout(() => {
        const separator = this.els.messagesContainer.querySelector('.unread-separator');
        if (separator) {
          separator.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
          this.scrollToBottom();
        }
      }, 100);
    } else {
      setTimeout(() => this.scrollToBottom(), 50);
      setTimeout(() => this.scrollToBottom(), 150);
      setTimeout(() => this.scrollToBottom(), 250);
    }
  }

  copyMessageContent(msg) {
    const text = msg.content || '';
    if (text) {
      navigator.clipboard.writeText(text).then(() => {
        this.showSuccess('تم نسخ الرسالة');
      }).catch(() => {
        this.showError('فشل نسخ الرسالة');
      });
    }
  }

  showMessageContextMenu(msg, event, msgEl) {
    // Remove existing context menu
    const existing = document.querySelector('.message-context-menu');
    if (existing) existing.remove();

    const menu = document.createElement('div');
    menu.className = 'message-context-menu';
    menu.style.position = 'fixed';
    menu.style.left = event.clientX + 'px';
    menu.style.top = event.clientY + 'px';
    menu.style.zIndex = '1000';

    const canEdit = msg.sender_id === window.messagingConfig.userId && !msg.attachmentName && !msg.attachment_name;
    const canDelete = msg.sender_id === window.messagingConfig.userId;

    menu.innerHTML = `
      <div class="context-menu-item" data-action="reply">
        <i class="ri-reply-line"></i> رد
      </div>
      ${canEdit ? `<div class="context-menu-item" data-action="edit">
        <i class="ri-edit-line"></i> تعديل
      </div>` : ''}
      <div class="context-menu-item" data-action="copy">
        <i class="ri-file-copy-line"></i> نسخ
      </div>
      ${canDelete ? `<div class="context-menu-item delete" data-action="delete">
        <i class="ri-delete-bin-line"></i> حذف
      </div>` : ''}
    `;

    document.body.appendChild(menu);

    // Handle menu item clicks
    menu.addEventListener('click', (e) => {
      const action = e.target.closest('.context-menu-item')?.dataset.action;
      if (!action) return;

      switch (action) {
        case 'reply':
          this.replyToMessage(msg);
          break;
        case 'edit':
          this.editMessage(msg);
          break;
        case 'copy':
          this.copyMessageContent(msg);
          break;
        case 'delete':
          this.confirmDeleteMessage(msg);
          break;
      }
      menu.remove();
    });

    // Close menu on outside click
    const closeMenu = (e) => {
      if (!menu.contains(e.target)) {
        menu.remove();
        document.removeEventListener('click', closeMenu);
      }
    };
    setTimeout(() => document.addEventListener('click', closeMenu), 10);
  }

  confirmDeleteMessage(msg) {
    if (confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
      this.deleteMessage(msg);
    }
  }

  async deleteMessage(msgOrId) {
    const messageId = typeof msgOrId === 'object' ? msgOrId?.id : msgOrId;
    if (!messageId) return;

    try {
      const response = await api.deleteMessage(messageId);
      if (response.success) {
        // Remove message from UI
        const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (msgEl) msgEl.remove();
        this.showSuccess('تم حذف الرسالة');
      } else {
        this.showError('فشل في حذف الرسالة');
      }
    } catch (error) {
      console.error('Delete message error:', error);
      this.showError('خطأ في حذف الرسالة');
    }
  }

  replyToMessage(msg) {
    this.replyingTo = msg;
    this.els.messageInput.focus();
    this.showReplyIndicator(msg);
  }

  showReplyIndicator(msg) {
    const existing = document.getElementById('reply-indicator');
    if (existing) existing.remove();

    const indicator = document.createElement('div');
    indicator.id = 'reply-indicator';
    indicator.className = 'reply-indicator';
    indicator.innerHTML = `
      <div class="reply-content">
        <div class="reply-label">رد على <strong>${msg.senderName || 'رسالة'}</strong></div>
        <div class="reply-text">${msg.content ? msg.content.substring(0, 50) : 'مرفق'}</div>
      </div>
      <button class="reply-close" onclick="this.parentElement.remove();"><i class="ri-close-line"></i></button>
    `;
    this.els.messageInput.parentNode.insertBefore(indicator, this.els.messageInput);
  }

  editMessage(msg) {
    this.editingMsg = msg;
    this.els.messageInput.value = msg.content || '';
    this.autoResizeInput();
    this.els.messageInput.focus();

    // Show edit indicator
    const existing = document.getElementById('edit-indicator');
    if (existing) existing.remove();

    const indicator = document.createElement('div');
    indicator.id = 'edit-indicator';
    indicator.className = 'edit-indicator';
    indicator.innerHTML = `
      <i class="ri-edit-line"></i>
      <span>تعديل الرسالة</span>
      <button onclick="document.getElementById('edit-indicator').remove(); document.getElementById('messageInput').value = '';" style="background:none; border:none; color:#999; cursor:pointer; padding:0;">
        <i class="ri-close-line"></i>
      </button>
    `;
    this.els.messageInput.parentNode.insertBefore(indicator, this.els.messageInput);
  }

  async sendMessage() {
    const text = this.els.messageInput.value.trim();

    if (!text && !this.els.fileInput.files.length) {
      this.showError('اكتب رسالة أو أرفق ملف');
      return;
    }

    if (!this.currentChat) {
      this.showError('اختر محادثة أولاً');
      return;
    }

    this.els.sendBtn.disabled = true;

    try {
      // Check if editing a message
      if (this.editingMsg) {
        const response = await api.updateMessage(this.editingMsg.id, text);
        if (response.success) {
          // Update message in UI
          const msgEl = document.querySelector(`[data-message-id="${this.editingMsg.id}"]`);
          if (msgEl) {
            const contentEl = msgEl.querySelector('.message-content');
            if (contentEl) contentEl.textContent = text;
          }

          // Clear editing state
          this.editingMsg = null;
          this.els.messageInput.value = '';
          this.els.messageInput.style.height = 'auto';
          const editIndicator = document.getElementById('edit-indicator');
          if (editIndicator) editIndicator.remove();

          this.showSuccess('تم تعديل الرسالة');
        } else {
          this.showError(response.message || 'فشل في تعديل الرسالة');
        }
      } else {
        const file = this.els.fileInput.files.length ? this.els.fileInput.files[0] : null;
        if (file) {
          this.showFileUploadProgress(file);
        }
        const response = await api.sendMessage(this.currentChat.id, text, file, this.replyingTo ? this.replyingTo.id : null);

        if (response.success) {
          const fill = document.getElementById('upload-progress-fill');
          if (fill) fill.style.width = '100%';
          setTimeout(() => {
            this.els.messageInput.value = '';
            this.els.messageInput.style.height = 'auto';
            this.els.fileInput.value = '';
            const preview = document.getElementById('file-preview-container');
            if (preview) preview.remove();
            this.hideFileUploadProgress();
            // Clear reply state
            this.replyingTo = null;
            const replyIndicator = document.getElementById('reply-indicator');
            if (replyIndicator) replyIndicator.remove();
          }, 300);

          const lastMessageText = response.data.content || response.data.attachmentName || 'مرفق';
          const newMessage = {
            id: response.data.id,
            sender_id: window.messagingConfig.userId,
            content: response.data.content,
            attachmentUrl: response.data.attachmentUrl,
            attachmentName: response.data.attachmentName,
            attachmentMime: response.data.attachmentMime,
            created_at: response.data.createdAt || response.data.created_at,
            read_at: response.data.readAt || null,
          };

          this.appendMessageToCurrentChat(newMessage, { updatePreview: true });
          this.showSuccess('تم الإرسال');
        } else {
          this.hideFileUploadProgress();
          this.showError(response.message || 'فشل الإرسال');
        }
      }
    } catch (error) {
      console.error('❌ Send error:', error);
      this.hideFileUploadProgress();
      this.showError('خطأ في الإرسال');
    } finally {
      this.els.sendBtn.disabled = false;
    }
  }

  async sendAudio(audioBlob, duration) {
    if (!this.currentChat) {
      this.showError('اختر محادثة أولاً');
      return;
    }

    this.els.micBtn.disabled = true;

    try {
      // Show upload progress
      this.showAudioUploadProgress(duration);

      const response = await api.uploadAudio(this.currentChat.id, audioBlob, duration);

      if (response.success) {
        this.hideAudioUploadProgress();

        const newMessage = {
          id: response.data.id,
          sender_id: window.messagingConfig.userId,
          content: response.data.content,
          attachmentUrl: response.data.attachmentUrl,
          attachmentName: response.data.attachmentName || 'رسالة صوتية',
          attachmentMime: response.data.attachmentMime,
          duration: response.data.duration,
          created_at: response.data.createdAt || response.data.created_at,
          read_at: response.data.readAt || null,
        };

        this.appendMessageToCurrentChat(newMessage, { updatePreview: true });
        this.showSuccess('تم إرسال الرسالة الصوتية');
      } else {
        this.hideAudioUploadProgress();
        this.showError(response.message || 'فشل إرسال الرسالة الصوتية');
      }
    } catch (error) {
      console.error('❌ Audio send error:', error);
      this.hideAudioUploadProgress();
      this.showError('خطأ في إرسال الرسالة الصوتية: ' + error.message);
    } finally {
      this.els.micBtn.disabled = false;
    }
  }

  showAudioUploadProgress(duration) {
    this.hideAudioUploadProgress();
    const progress = document.createElement('div');
    progress.id = 'audio-upload-progress';
    progress.className = 'audio-upload-progress';
    progress.innerHTML = `
      <div class="upload-icon">
        <i class="ri-mic-line"></i>
      </div>
      <div class="upload-info">
        <div class="upload-name">رسالة صوتية</div>
        <div class="upload-size">${duration.toFixed(1)} ثانية</div>
      </div>
      <div class="upload-status">جاري الإرسال...</div>
    `;
    this.els.messageInput.parentNode.appendChild(progress);
  }

  hideAudioUploadProgress() {
    const progress = document.getElementById('audio-upload-progress');
    if (progress) {
      progress.remove();
    }
  }

  async toggleAudioRecording() {
    if (!this.isRecording) {
      // Start recording
      const success = await media.startAudioRecording();
      if (success) {
        this.isRecording = true;
        this.els.micBtn.classList.add('recording');
        this.recordingStartTime = Date.now();

        // Update button text/icon to show recording state
        this.els.micBtn.innerHTML = '<i class="ri-stop-circle-line"></i>';
        this.els.micBtn.title = 'إيقاف التسجيل';

        // Add visual feedback
        this.showRecordingIndicator();
      }
    } else {
      // Stop recording
      this.isRecording = false;
      this.els.micBtn.classList.remove('recording');
      this.els.micBtn.innerHTML = '<i class="ri-mic-line"></i>';
      this.els.micBtn.title = 'تسجيل صوتي';

      // Hide recording indicator
      this.hideRecordingIndicator();

      const blob = await media.stopAudioRecording();

      if (blob && this.currentChat) {
        const duration = (Date.now() - this.recordingStartTime) / 1000;
        await this.sendAudio(blob, duration);
      }
    }
  }

  showRecordingIndicator() {
    // Remove existing indicator if any
    this.hideRecordingIndicator();

    const indicator = document.createElement('div');
    indicator.id = 'recording-indicator';
    indicator.className = 'recording-indicator';
    indicator.innerHTML = `
      <div class="recording-pulse"></div>
      <div class="recording-text">
        <i class="ri-mic-line"></i>
        <span>جاري التسجيل...</span>
        <span id="recording-timer">00:00</span>
      </div>
    `;

    document.body.appendChild(indicator);

    // Update timer
    this.recordingTimer = setInterval(() => {
      const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
      const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
      const seconds = (elapsed % 60).toString().padStart(2, '0');
      const timerEl = document.getElementById('recording-timer');
      if (timerEl) {
        timerEl.textContent = `${minutes}:${seconds}`;
      }
    }, 1000);
  }

  hideRecordingIndicator() {
    const indicator = document.getElementById('recording-indicator');
    if (indicator) {
      indicator.remove();
    }
    if (this.recordingTimer) {
      clearInterval(this.recordingTimer);
      this.recordingTimer = null;
    }
  }

  handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    console.log(`📎 Selected: ${file.name}`);

    // Validate file size (max 500MB)
    if (file.size > 500 * 1024 * 1024) {
      this.showError('حجم الملف كبير جداً (الحد الأقصى 500 ميجابايت)');
      this.els.fileInput.value = '';
      return;
    }

    // Validate file type
    const allowedTypes = ['image/', 'video/', 'audio/', 'application/pdf', 'text/', 'application/msword', 'application/vnd.openxmlformats-officedocument', 'application/x-zip-compressed', 'application/x-rar-compressed'];
    const isAllowed = allowedTypes.some(type => file.type.startsWith(type) ||
                      file.name.toLowerCase().endsWith('.docx') ||
                      file.name.toLowerCase().endsWith('.xlsx') ||
                      file.name.toLowerCase().endsWith('.pptx') ||
                      file.name.toLowerCase().endsWith('.zip') ||
                      file.name.toLowerCase().endsWith('.rar'));

    if (!isAllowed) {
      this.showError('نوع الملف غير مدعوم');
      this.els.fileInput.value = '';
      return;
    }

    // Show file preview instead of auto-sending
    this.showFilePreview(file);
  }

  showFilePreview(file) {
    const existingPreview = document.getElementById('file-preview-container');
    if (existingPreview) {
      existingPreview.remove();
    }

    const preview = document.createElement('div');
    preview.id = 'file-preview-container';
    preview.className = 'file-preview-container';

    const fileType = this.getFileTypeCategory(file.type, file.name);
    const icon = this.getFileIcon(fileType);
    const fileName = file.name;
    const fileSize = this.formatFileSize(file.size);

    // Build preview content based on file type
    let mediaPreview = '';
    let hasMediaPreview = false;

    if (fileType === 'image') {
      hasMediaPreview = true;
      mediaPreview = '<div class="preview-image-container"><img class="preview-image" src="" alt="معاينة" style="display:none;"></div>';

      const reader = new FileReader();
      reader.onload = (e) => {
        const img = preview.querySelector('.preview-image');
        if (img) {
          img.src = e.target.result;
          img.style.display = 'block';
        }
      };
      reader.readAsDataURL(file);
    } else if (fileType === 'video') {
      hasMediaPreview = true;
      mediaPreview = '<div class="preview-video-container"><video class="preview-video" controls style="display:none;"></video></div>';

      const reader = new FileReader();
      reader.onload = (e) => {
        const video = preview.querySelector('.preview-video');
        if (video) {
          const blob = new Blob([e.target.result], { type: file.type });
          const url = URL.createObjectURL(blob);
          video.src = url;
          video.style.display = 'block';
        }
      };
      reader.readAsArrayBuffer(file);
    } else if (fileType === 'audio') {
      hasMediaPreview = true;
      mediaPreview = '<div class="preview-audio-container"><audio class="preview-audio" controls style="display:none;"></audio></div>';

      const reader = new FileReader();
      reader.onload = (e) => {
        const audio = preview.querySelector('.preview-audio');
        if (audio) {
          const blob = new Blob([e.target.result], { type: file.type });
          const url = URL.createObjectURL(blob);
          audio.src = url;
          audio.style.display = 'block';
        }
      };
      reader.readAsArrayBuffer(file);
    }

    preview.innerHTML = `
      <div class="preview-header">
        <span>معاينة الملف</span>
        <button class="remove-file-btn" type="button" aria-label="إغلاق">
          <i class="ri-close-line"></i>
        </button>
      </div>
      ${hasMediaPreview ? mediaPreview : ''}
      <div class="preview-info">
        <div class="preview-icon">${icon}</div>
        <div class="preview-details">
          <div class="preview-name">${fileName}</div>
          <div class="preview-size">${fileSize}</div>
        </div>
      </div>
      <div class="preview-actions">
        <button class="remove-file-btn-large" type="button">
          <i class="ri-delete-bin-line"></i> إزالة
        </button>
      </div>
    `;

    // Close button handler
    const closeBtn = preview.querySelector('.remove-file-btn');
    closeBtn.addEventListener('click', () => {
      this.els.fileInput.value = '';
      preview.remove();
      console.log('📎 File cleared');
    });

    // Remove button handler
    const removeBtn = preview.querySelector('.remove-file-btn-large');
    removeBtn.addEventListener('click', () => {
      this.els.fileInput.value = '';
      preview.remove();
      console.log('📎 File removed');
    });

    this.els.messageInput.parentNode.insertBefore(preview, this.els.messageInput);
    console.log(`✅ Preview shown for: ${fileName}`);
  }

  getFileTypeCategory(mimeType, fileName) {
    const lowerName = fileName.toLowerCase();
    if (mimeType.startsWith('image/')) return 'image';
    if (mimeType.startsWith('video/')) return 'video';
    if (mimeType.startsWith('audio/')) return 'audio';
    if (mimeType === 'application/pdf' || lowerName.endsWith('.pdf')) return 'pdf';
    if (lowerName.endsWith('.docx') || lowerName.endsWith('.doc') || mimeType.includes('word')) return 'document';
    if (lowerName.endsWith('.xlsx') || lowerName.endsWith('.xls') || mimeType.includes('sheet')) return 'spreadsheet';
    if (lowerName.endsWith('.pptx') || lowerName.endsWith('.ppt') || mimeType.includes('presentation')) return 'presentation';
    if (lowerName.endsWith('.zip') || lowerName.endsWith('.rar')) return 'archive';
    return 'file';
  }

  getFileIcon(fileType) {
    const icons = {
      image: '<i class="ri-image-2-line"></i>',
      video: '<i class="ri-video-line"></i>',
      audio: '<i class="ri-music-line"></i>',
      pdf: '<i class="ri-file-pdf-line"></i>',
      document: '<i class="ri-file-word-line"></i>',
      spreadsheet: '<i class="ri-file-excel-line"></i>',
      presentation: '<i class="ri-file-pptx-line"></i>',
      archive: '<i class="ri-file-zip-line"></i>',
      file: '<i class="ri-file-line"></i>',
    };
    return icons[fileType] || icons.file;
  }

  hideFileUploadProgress() {
    const progress = document.getElementById('file-upload-progress');
    if (progress) {
      progress.remove();
    }
  }

  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  filterChats(query) {
    document.querySelectorAll('.chat-item').forEach(el => {
      const name = el.querySelector('.chat-name').textContent;
      const matches = name.toLowerCase().includes(query.toLowerCase());
      el.style.display = matches ? '' : 'none';
    });
  }

  updateStatus(contact) {
    const status = this.els.chatStatus;
    const lastSeenValue = contact.lastSeen || contact.last_seen || contact.lastSeenAt || contact.last_seen_at;
    const parsedLastSeen = this.parseDateTime(lastSeenValue);

    const isActive = contact.isOnline || (parsedLastSeen && (new Date() - parsedLastSeen) < 60000);
    if (isActive) {
      status.innerHTML = '<span class="status-dot"></span><span>نشطة الآن</span>';
      return;
    }

    status.innerHTML = `<span>${this.formatLastSeen(lastSeenValue)}</span>`;
  }

  formatLastSeen(value) {
    if (!value) {
      return 'لم يسجل دخول من قبل';
    }

    if (typeof value === 'string' && (value.includes('آخر ظهور') || value.includes('نشط الآن') || value.includes('آخر نشاط') || value.includes('منذ'))) {
      return value;
    }

    const date = this.parseDateTime(value);
    if (!date) {
      return 'لم يسجل دخول من قبل';
    }

    const now = new Date();
    const diffMs = now - date;
    const diffMinutes = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMinutes / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMinutes < 1) {
      return 'الآن';
    } else if (diffMinutes < 60) {
      return `منذ ${diffMinutes} دقيقة`;
    } else if (diffHours < 24) {
      return `منذ ${diffHours} ساعة`;
    } else if (diffDays === 1) {
      return 'أمس';
    } else if (diffDays < 7) {
      return `منذ ${diffDays} أيام`;
    } else if (diffDays < 30) {
      const weeks = Math.floor(diffDays / 7);
      return `منذ ${weeks} أسبوع${weeks > 1 ? '' : ''}`;
    } else {
      return date.toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        timeZone: 'Asia/Riyadh'
      });
    }
  }

  parseDateTime(value) {
    if (!value) return null;
    if (value instanceof Date) return value;
    if (typeof value === 'number') return new Date(value);
    if (typeof value !== 'string') return null;

    const trimmed = value.trim();
    if (/^\d{1,2}:\d{2}$/.test(trimmed)) {
      const [hours, minutes] = trimmed.split(':').map(Number);
      const today = new Date();
      today.setHours(hours, minutes, 0, 0);
      return today;
    }

    const normalized = trimmed.replace(' ', 'T').replace(/\s+UTC$/, 'Z');
    if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/.test(normalized)) {
      return new Date(`${normalized}+03:00`);
    }

    const date = new Date(normalized);
    return isNaN(date) ? null : date;
  }
  scrollToBottom(force = false) {
    requestAnimationFrame(() => {
      const container = this.els.messagesContainer;
      if (!container) return;
      const lastMessage = container.lastElementChild;
      if (lastMessage) {
        lastMessage.scrollIntoView({ behavior: force ? 'auto' : 'smooth', block: 'end', inline: 'nearest' });
        if (force) {
          container.scrollTop = container.scrollHeight;
        }
      } else {
        container.scrollTop = container.scrollHeight;
      }
    });
  }

  autoResizeInput() {
    const ta = this.els.messageInput;
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 100) + 'px';
  }

  showFileUploadProgress(file) {
    this.hideFileUploadProgress();

    const progress = document.createElement('div');
    progress.id = 'file-upload-progress';
    progress.className = 'file-upload-progress';
    progress.innerHTML = `
      <div class="upload-icon"><i class="ri-file-upload-line"></i></div>
      <div class="upload-info">
        <div class="upload-name">${file.name}</div>
        <div class="upload-size">${this.formatFileSize(file.size)}</div>
        <div class="upload-status">جاري رفع الملف...</div>
        <div class="upload-progress-bar">
          <div class="upload-progress-fill" id="upload-progress-fill"></div>
        </div>
      </div>
    `;

    this.els.messageInput.parentNode.appendChild(progress);

    // Animate progress bar
    const fill = document.getElementById('upload-progress-fill');
    if (fill) {
      setTimeout(() => fill.style.width = '50%', 100);
      setTimeout(() => fill.style.width = '80%', 500);
    }
  }

  formatTime(date) {
    const parsed = this.parseDateTime(date);
    if (!parsed) return '';

    const now = new Date();
    const diff = now - parsed;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const timeOptions = { timeZone: 'Asia/Riyadh', hour: '2-digit', minute: '2-digit', hour12: false };
    const dateOptions = { timeZone: 'Asia/Riyadh' };

    if (days === 0) {
      return parsed.toLocaleTimeString('ar-SA', timeOptions);
    } else if (days === 1) {
      return 'أمس';
    } else if (days < 7) {
      const dayNames = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
      return dayNames[parsed.getDay()];
    } else {
      return parsed.toLocaleDateString('ar-SA', dateOptions);
    }
  }

  showError(msg) {
    const alert = document.createElement('div');
    alert.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #e53935;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      z-index: 9999;
      font-size: 14px;
      animation: slideUp 0.3s ease;
    `;
    alert.textContent = msg;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 2000);
  }

  showSuccess(msg) {
    const alert = document.createElement('div');
    alert.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #31a24c;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      z-index: 9999;
      font-size: 14px;
      animation: slideUp 0.3s ease;
    `;
    alert.textContent = msg;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 1500);
  }

  async fixOldAttachments() {
    if (window.messagingConfig.userRole !== 'teacher') {
      this.showError('غير مصرح لك بهذا الإجراء');
      return;
    }

    if (!confirm('هل تريد إصلاح روابط المرفقات القديمة في قاعدة البيانات؟ هذا قد يستغرق بعض الوقت.')) {
      return;
    }

    try {
      const response = await fetch(window.messagingRoutes.messaging_fix_attachments, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });

      const data = await response.json();

      if (data.success) {
        this.showSuccess(`تم إصلاح ${data.updated_count} رسالة`);
        // Reload current chat to show fixed attachments
        if (this.currentChat) {
          await this.loadChat(this.currentChat.id);
        }
      } else {
        this.showError('فشل في إصلاح المرفقات');
      }
    } catch (error) {
      console.error('Fix attachments error:', error);
      this.showError('حدث خطأ أثناء إصلاح المرفقات');
    }
  }
}

// Initialize
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    const _app = new TelegramMessagingApp();
    window.addEventListener('beforeunload', () => {
      if (_app.recordingTimer) { clearInterval(_app.recordingTimer); _app.recordingTimer = null; }
    });
  });
} else {
  const _app = new TelegramMessagingApp();
  window.addEventListener('beforeunload', () => {
    if (_app.recordingTimer) { clearInterval(_app.recordingTimer); _app.recordingTimer = null; }
  });
}


