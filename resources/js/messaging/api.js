/**
 * API Service - Communication with Backend
 */

class APIService {
  constructor() {
    this.baseUrl = window.location.origin;
    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  }

  async request(method, endpoint, data = null, options = {}) {
    const url = endpoint.startsWith('http://') || endpoint.startsWith('https://')
      ? endpoint
      : `${this.baseUrl}${endpoint}`;
    const headers = {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': this.csrfToken,
      ...options.headers,
    };

    const config = {
      method,
      headers,
      ...options,
    };

    if (data && method !== 'GET') {
      if (data instanceof FormData) {
        // Don't set Content-Type for FormData - browser handles it
        delete headers['Content-Type'];
        config.body = data;
      } else {
        headers['Content-Type'] = 'application/json';
        config.body = JSON.stringify(data);
      }
    }

    try {
      const response = await fetch(url, config);
      const json = await response.json();

      if (!response.ok) {
        throw new Error(json.message || `HTTP ${response.status}`);
      }

      return json;
    } catch (error) {
      console.error(`API Error [${method} ${endpoint}]:`, error);
      throw error;
    }
  }

  // Messaging endpoints
  async sendMessage(recipientId, content, attachmentFile = null, replyTo = null) {
    const formData = new FormData();
    formData.append('recipient_id', recipientId);
    if (content) formData.append('content', content);
    if (attachmentFile) formData.append('attachment', attachmentFile);
    if (replyTo) formData.append('reply_to', replyTo);

    const route = this.getRoute('messaging.send');
    return this.request('POST', route, formData);
  }

  async uploadAudio(recipientId, audioBlob, duration) {
    const formData = new FormData();
    formData.append('recipient_id', recipientId);
    formData.append('audio', audioBlob, 'audio-message.webm');
    formData.append('duration', Math.round(duration));

    const route = this.getRoute('messaging.audio');
    return this.request('POST', route, formData);
  }

  async updateMessage(messageId, content) {
    const route = this.getRoute('messages.update', messageId);
    return this.request('PUT', route, { content, edit_id: messageId });
  }

  async deleteMessage(messageId) {
    const route = this.getRoute('messages.destroy', messageId);
    return this.request('DELETE', route);
  }

  async loadMessages(recipientId, page = 1) {
    const route = this.getRoute('messaging.load');
    return this.request('GET', `${route}?recipient_id=${recipientId}&page=${page}`);
  }

  async refreshMessages(recipientId) {
    const route = this.getRoute('messaging.refresh');
    return this.request('GET', `${route}?recipient_id=${recipientId}`);
  }

  async searchMessages(query) {
    const route = this.getRoute('messaging.search');
    return this.request('GET', `${route}?q=${encodeURIComponent(query)}`);
  }

  async markAsRead(contactId) {
    const route = this.getRoute('messaging.read');
    return this.request('POST', route, { contact_id: contactId });
  }

  // Helper: Get route from window object (set in Blade)
  getRoute(name, param = null) {
    if (!window.routes) {
      console.warn('Routes not found in window object');
      return '';
    }

    // Convert route name to match window.routes keys
    const routeKey = name.replace(/\./g, '_');

    let route = window.routes[routeKey];
    if (param && route) {
      route = route.replace('MESSAGE_ID', param);
    }
    return route || '';
  }
}

export default new APIService();
