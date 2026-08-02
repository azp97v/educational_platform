import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function StudentDashboard({ user, stats, enrolledCourses, availableCourses, recentActivity }) {
    return (
        <AppLayout title="لوحة التحكم">
            <Head title="الرئيسية" />

            {/* Welcome */}
            <div className="page-header">
                <h2 className="page-greeting">
                    أهلاً {user?.name} <span role="img" aria-label="wave">👋</span>
                </h2>
                <p className="page-sub">تابع مسيرتك التعليمية</p>
            </div>

            {/* Stats */}
            <div className="stats-grid">
                <StatCard icon="ri-book-open-line"   label="مسارات نشطة"    value={stats?.active_courses ?? 0}  color="blue" />
                <StatCard icon="ri-trophy-line"       label="نقاطي"          value={stats?.points ?? 0}           color="gold" />
                <StatCard icon="ri-award-line"        label="شهاداتي"        value={stats?.certificates ?? 0}    color="green" />
                <StatCard icon="ri-fire-line"         label="أيام متواصلة"  value={stats?.streak ?? 0}           color="orange" />
                <StatCard icon="ri-check-double-line" label="دروس مكتملة"   value={stats?.completed_lessons ?? 0} color="purple" />
                <StatCard icon="ri-time-line"         label="وقت التعلم"    value={formatHours(stats?.total_minutes)} color="teal" />
            </div>

            <div className="dashboard-grid">
                {/* Enrolled Courses */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-book-open-line" /> مساراتي التعليمية</h3>
                        <a href="/student/academy" className="card-link">عرض الكل</a>
                    </div>
                    <div className="card-body">
                        {enrolledCourses?.length > 0 ? (
                            <div className="course-list">
                                {enrolledCourses.map(course => (
                                    <CourseRow key={course.id} course={course} />
                                ))}
                            </div>
                        ) : (
                            <Empty icon="ri-book-open-line" text="لم تلتحق بأي مسار بعد" action={{ href: '/student/academy', label: 'استعرض المسارات' }} />
                        )}
                    </div>
                </section>

                {/* Available Courses */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-compass-discover-line" /> مسارات متاحة</h3>
                        <a href="/student/academy" className="card-link">عرض الكل</a>
                    </div>
                    <div className="card-body">
                        {availableCourses?.length > 0 ? (
                            <div className="course-list">
                                {availableCourses.slice(0, 4).map(course => (
                                    <AvailableCourseRow key={course.id} course={course} />
                                ))}
                            </div>
                        ) : (
                            <Empty icon="ri-compass-discover-line" text="لا توجد مسارات متاحة حالياً" />
                        )}
                    </div>
                </section>

                {/* Recent Activity */}
                {recentActivity?.length > 0 && (
                    <section className="card card-full">
                        <div className="card-header">
                            <h3 className="card-title"><i className="ri-history-line" /> آخر نشاطاتي</h3>
                        </div>
                        <div className="card-body">
                            <div className="activity-list">
                                {recentActivity.map((item, i) => (
                                    <ActivityRow key={i} item={item} />
                                ))}
                            </div>
                        </div>
                    </section>
                )}
            </div>
        </AppLayout>
    );
}

function StatCard({ icon, label, value, color }) {
    return (
        <div className={`stat-card stat-${color}`}>
            <i className={`${icon} stat-icon`} />
            <div className="stat-value">{value}</div>
            <div className="stat-label">{label}</div>
        </div>
    );
}

function CourseRow({ course }) {
    const progress = course.progress ?? 0;
    return (
        <a href={`/student/course/${course.id}`} className="course-row">
            <div className="course-info">
                <span className="course-title">{course.title}</span>
                <span className="course-meta">{course.lessons_count ?? 0} درس</span>
            </div>
            <div className="progress-wrap">
                <div className="progress-bar">
                    <div className="progress-fill" style={{ width: `${progress}%` }} />
                </div>
                <span className="progress-label">{progress}%</span>
            </div>
        </a>
    );
}

function AvailableCourseRow({ course }) {
    return (
        <div className="course-row">
            <div className="course-info">
                <span className="course-title">{course.title}</span>
                <span className="course-meta">{course.lessons_count ?? 0} درس</span>
            </div>
            <button
                className="btn-sm btn-primary"
                onClick={() => router.post(`/student/enroll/${course.id}`)}
            >
                الالتحاق
            </button>
        </div>
    );
}

function ActivityRow({ item }) {
    const icons = { lesson: 'ri-play-circle-line', exam: 'ri-file-list-line', certificate: 'ri-award-line' };
    return (
        <div className="activity-row">
            <i className={`${icons[item.type] ?? 'ri-check-line'} activity-icon`} />
            <div className="activity-info">
                <span className="activity-title">{item.title}</span>
                <span className="activity-time">{item.time_ago}</span>
            </div>
        </div>
    );
}

function Empty({ icon, text, action }) {
    return (
        <div className="empty-state">
            <i className={icon} />
            <p>{text}</p>
            {action && <a href={action.href} className="btn-sm btn-primary">{action.label}</a>}
        </div>
    );
}

function formatHours(minutes) {
    if (!minutes) return '0 س';
    const h = Math.floor(minutes / 60);
    return h > 0 ? `${h} س` : `${minutes} د`;
}
