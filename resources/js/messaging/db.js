/**
 * IndexedDB Service for Messages - Offline First Storage
 */

class MessageDatabase {
  constructor() {
    this.db = null;
    this.dbName = 'EjlalMessaging';
    this.version = 1;
    this.ready = this.initDB();
  }

  async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(this.dbName, this.version);

      request.onerror = () => {
        console.error('Failed to open IndexedDB:', request.error);
        reject(request.error);
      };

      request.onsuccess = () => {
        this.db = request.result;
        console.log('✓ IndexedDB initialized successfully');
        resolve(this.db);
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;

        // Messages store
        if (!db.objectStoreNames.contains('messages')) {
          const msgStore = db.createObjectStore('messages', { keyPath: 'id' });
          msgStore.createIndex('conversation', ['senderId', 'recipientId'], { unique: false });
          msgStore.createIndex('timestamp', 'createdAt', { unique: false });
          msgStore.createIndex('status', 'status', { unique: false });
        }

        // Contacts store
        if (!db.objectStoreNames.contains('contacts')) {
          const contactStore = db.createObjectStore('contacts', { keyPath: 'id' });
          contactStore.createIndex('lastMessageTime', 'lastMessageTime', { unique: false });
        }

        // Sync queue for offline messages
        if (!db.objectStoreNames.contains('syncQueue')) {
          db.createObjectStore('syncQueue', { keyPath: 'id', autoIncrement: true });
        }

        // Media cache
        if (!db.objectStoreNames.contains('mediaCache')) {
          db.createObjectStore('mediaCache', { keyPath: 'url' });
        }
      };
    });
  }

  async saveMessage(message) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages'], 'readwrite');
      const store = transaction.objectStore('messages');
      const request = store.put(message);

      request.onsuccess = () => resolve(message);
      request.onerror = () => reject(request.error);
    });
  }

  async saveMessages(messages) {
    await this.ready;
    return Promise.all(messages.map(msg => this.saveMessage(msg)));
  }

  async getConversation(senderId, recipientId, limit = 100) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages'], 'readonly');
      const store = transaction.objectStore('messages');
      const index = store.index('conversation');

      const range = IDBKeyRange.only([senderId, recipientId]);
      const request = index.getAll(range);

      request.onsuccess = () => {
        let results = request.result
          .sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt))
          .slice(-limit);
        resolve(results);
      };
      request.onerror = () => reject(request.error);
    });
  }

  async searchMessages(query, limit = 50) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages'], 'readonly');
      const store = transaction.objectStore('messages');
      const request = store.getAll();

      request.onsuccess = () => {
        const results = request.result
          .filter(msg => msg.content && msg.content.toLowerCase().includes(query.toLowerCase()))
          .slice(-limit);
        resolve(results);
      };
      request.onerror = () => reject(request.error);
    });
  }

  async updateMessageStatus(messageId, status) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages'], 'readwrite');
      const store = transaction.objectStore('messages');
      const request = store.get(messageId);

      request.onsuccess = () => {
        const message = request.result;
        if (message) {
          message.status = status;
          const updateRequest = store.put(message);
          updateRequest.onsuccess = () => resolve(message);
          updateRequest.onerror = () => reject(updateRequest.error);
        }
      };
      request.onerror = () => reject(request.error);
    });
  }

  async deleteMessage(messageId) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages'], 'readwrite');
      const store = transaction.objectStore('messages');
      const request = store.delete(messageId);

      request.onsuccess = () => resolve(true);
      request.onerror = () => reject(request.error);
    });
  }

  async saveContact(contact) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['contacts'], 'readwrite');
      const store = transaction.objectStore('contacts');
      const request = store.put(contact);

      request.onsuccess = () => resolve(contact);
      request.onerror = () => reject(request.error);
    });
  }

  async getAllContacts() {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['contacts'], 'readonly');
      const store = transaction.objectStore('contacts');
      const request = store.getAll();

      request.onsuccess = () => {
        resolve(request.result.sort((a, b) =>
          new Date(b.lastMessageTime) - new Date(a.lastMessageTime)
        ));
      };
      request.onerror = () => reject(request.error);
    });
  }

  async queueForSync(action, data) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['syncQueue'], 'readwrite');
      const store = transaction.objectStore('syncQueue');
      const request = store.add({
        action,
        data,
        timestamp: new Date().getTime(),
        synced: false,
      });

      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }

  async getSyncQueue() {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['syncQueue'], 'readonly');
      const store = transaction.objectStore('syncQueue');
      const request = store.getAll();

      request.onsuccess = () => {
        resolve(request.result.filter(item => !item.synced));
      };
      request.onerror = () => reject(request.error);
    });
  }

  async removeSyncItem(id) {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['syncQueue'], 'readwrite');
      const store = transaction.objectStore('syncQueue');
      const request = store.delete(id);

      request.onsuccess = () => resolve(true);
      request.onerror = () => reject(request.error);
    });
  }

  async clearDatabase() {
    await this.ready;
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(['messages', 'contacts', 'syncQueue', 'mediaCache'], 'readwrite');

      transaction.oncomplete = () => resolve(true);
      transaction.onerror = () => reject(transaction.error);

      transaction.objectStore('messages').clear();
      transaction.objectStore('contacts').clear();
      transaction.objectStore('syncQueue').clear();
      transaction.objectStore('mediaCache').clear();
    });
  }
}

export default new MessageDatabase();
