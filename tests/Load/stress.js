import { check, sleep } from 'k6';
import http from 'k6/http';
import { BASE_URL, ADMIN_EMAIL, ADMIN_PASSWORD } from './config.js';

export const options = {
  stages: [
    { duration: '1m', target: 50 },
    { duration: '1m', target: 100 },
    { duration: '1m', target: 200 },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<5000', 'p(99)<10000'],
    http_req_failed: ['rate<0.05'],
  },
};

export default function () {
  const loginRes = http.post(`${BASE_URL}/login`, {
    email: ADMIN_EMAIL,
    password: ADMIN_PASSWORD,
  });
  check(loginRes, { 'login ok': (r) => r.status === 302 || r.status === 200 });

  http.get(`${BASE_URL}/admin`);
  http.get(`${BASE_URL}/admin/settings`);
  http.get(`${BASE_URL}/admin/analytics`);
  http.get(`${BASE_URL}/admin/rbac`);

  sleep(1);
}
