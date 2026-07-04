export const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

export const STUDENT_EMAIL = __ENV.STUDENT_EMAIL || 'student@test.com';
export const STUDENT_PASSWORD = __ENV.STUDENT_PASSWORD || 'password';
export const TEACHER_EMAIL = __ENV.TEACHER_EMAIL || 'teacher@test.com';
export const TEACHER_PASSWORD = __ENV.TEACHER_PASSWORD || 'password';
export const ADMIN_EMAIL = __ENV.ADMIN_EMAIL || 'admin@test.com';
export const ADMIN_PASSWORD = __ENV.ADMIN_PASSWORD || 'password';

export const RAMP_UP_DURATION = __ENV.RAMP_UP || '30s';
export const STEADY_DURATION = __ENV.STEADY || '1m';
export const VUS = parseInt(__ENV.VUS || '20');
