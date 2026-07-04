const CACHE_NAME = 'ijlal-messaging-media-v1';
const OUTBOX_DB_NAME = 'IjlalMessagingDB';
const OUTBOX_STORE_NAME = 'outbox';
let sendEndpoint = '/messaging/send';

self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('message', (event) => {
  if (event.data?.type === 'SET_SEND_ENDPOINT') {
    sendEndpoint = event.data.sendEndpoint || sendEndpoint;
  }
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') {
    return;
  }

  if (request.destination === 'image' || request.destination === 'video' || request.destination === 'audio') {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cachedResponse = await cache.match(request);
        if (cachedResponse) {
          return cachedResponse;
        }

        const response = await fetch(request);
        if (response.ok) {
          cache.put(request, response.clone());
        }
        return response;
      })
    );
  }
});

self.addEventListener('sync', (event) => {
  if (event.tag === 'messaging-outbox-sync') {
    event.waitUntil(flushOutbox());
  }
});

function openOutboxStore() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(OUTBOX_DB_NAME);

    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains(OUTBOX_STORE_NAME)) {
        db.createObjectStore(OUTBOX_STORE_NAME, { keyPath: 'id', autoIncrement: true });
      }
    };

    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function readOutboxItems() {
  const db = await openOutboxStore();
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(OUTBOX_STORE_NAME, 'readonly');
    const store = transaction.objectStore(OUTBOX_STORE_NAME);
    const request = store.getAll();
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function deleteOutboxItem(id) {
  const db = await openOutboxStore();
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(OUTBOX_STORE_NAME, 'readwrite');
    const store = transaction.objectStore(OUTBOX_STORE_NAME);
    const request = store.delete(id);
    request.onsuccess = () => resolve();
    request.onerror = () => reject(request.error);
  });
}

async function flushOutbox() {
  const items = await readOutboxItems();
  for (const item of items) {
    try {
      const response = await fetch(sendEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(item),
      });

      if (response.ok) {
        await deleteOutboxItem(item.id);
      }
    } catch (error) {
      console.warn('Failed to flush outbox item', error);
    }
  }
}
