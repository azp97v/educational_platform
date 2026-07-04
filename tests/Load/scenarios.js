import { check, sleep, group } from 'k6';
import http from 'k6/http';
import { Rate, Trend } from 'k6/metrics';
import {
  BASE_URL, STUDENT_EMAIL, STUDENT_PASSWORD,
  RAMP_UP_DURATION, STEADY_DURATION, VUS,
} from './config.js';

const loginFailRate = new Rate('login_failures');
const loginDuration = new Trend('login_duration');
const msgSendDuration = new Trend('message_send_duration');
const pageLoadDuration = new Trend('page_load_duration');

export const options = {
  stages: [
    { duration: RAMP_UP_DURATION, target: VUS },
    { duration: STEADY_DURATION, target: VUS },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000', 'p(99)<5000'],
    http_req_failed: ['rate<0.02'],
    login_failures: ['rate<0.05'],
  },
};

function getRandomCourseId() {
  const ids = [1, 2, 3, 4, 5];
  return ids[Math.floor(Math.random() * ids.length)];
}

export default function () {
  group('Authentication', function () {
    const loginRes = http.post(`${BASE_URL}/login`, {
      email: STUDENT_EMAIL,
      password: STUDENT_PASSWORD,
    });
    loginDuration.add(loginRes.timings.duration);
    loginFailRate.add(loginRes.status !== 302 && loginRes.status !== 200);
    check(loginRes, {
      'login succeeds': (r) => r.status === 302 || r.status === 200,
    });

    const cookies = loginRes.cookies;
    sleep(1);
  });

  group('Browse Content', function () {
    const dashboardRes = http.get(`${BASE_URL}/student/dashboard`);
    pageLoadDuration.add(dashboardRes.timings.duration);
    check(dashboardRes, {
      'dashboard loads': (r) => r.status === 200,
    });
    sleep(2);

    const courseRes = http.get(`${BASE_URL}/student/course/${getRandomCourseId()}`);
    pageLoadDuration.add(courseRes.timings.duration);
    check(courseRes, {
      'course page loads': (r) => r.status === 200,
    });
    sleep(1);
  });

  group('Messaging', function () {
    const msgRes = http.get(`${BASE_URL}/student/messaging`);
    pageLoadDuration.add(msgRes.timings.duration);
    check(msgRes, {
      'messaging loads': (r) => r.status === 200,
    });
    sleep(1);
  });

  group('Exams', function () {
    const examsRes = http.get(`${BASE_URL}/student/exams`);
    pageLoadDuration.add(examsRes.timings.duration);
    check(examsRes, {
      'exams page loads': (r) => r.status === 200,
    });
    sleep(1);
  });
}
