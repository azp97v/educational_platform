import { check, sleep } from 'k6';
import http from 'k6/http';
import { BASE_URL } from './config.js';

export const options = {
  vus: 1,
  duration: '30s',
  thresholds: {
    http_req_duration: ['p(95)<2000'],
    http_req_failed: ['rate<0.01'],
  },
};

export default function () {
  const res = http.get(`${BASE_URL}/login`);
  check(res, {
    'login page loads': (r) => r.status === 200,
    'has Arabic text': (r) => r.body && r.body.includes('تسجيل'),
  });
  sleep(1);
}
