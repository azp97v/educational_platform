import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function TeacherDashboard({ user, stats, recentCourses, pendingRequests, recentActivity }) {
    return (
        <AppLayout title="لوحة المعلم">
            <Head title="لوحة المعلم" />

            <div className="page-header">
                <h2 className="page-greeting">أهلاً {user?.name} <span role="img" aria-label="wave">👋</span></h2>
                <p className="page-sub">إدارة مساراتك التعليمية</p>
            </div>

            {/* Stats */}
            <div className="stats-grid">
                <StatCard icon="ri-book-2-line"       label="مساراتي"         value={stats?.total_courses ?? 0}    color="blue" />
                <StatCard icon="ri-team-line"          label="طلابي"           value={stats?.total_students ?? 0}   color="green" />
                <StatCard icon="ri-user-add-line"      label="طلبات معلقة"   value={stats?.pending_requests ?? 0} color="orange" />
                <StatCard icon="ri-file-list-line"     label="اختباراتي"      value={stats?.total_exams ?? 0}      color="purple" />
                <StatCard icon="ri-award-line"         label="شهادات أُصدرت" value={stats?.certificates_issued ?? 0} color="gold" />
                <StatCard icon="ri-bar-chart-2-line"   label="متوسط الإتمام"  value={`${stats?.avg_completion ?? 0}%`} color="teal" />
            </div>

            <div className="dashboard-grid">
                {/* Pending Enrollment Requests */}
                {pendingRequests?.length > 0 && (
                    <section className="card card-alert">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="ri-user-add-line" />
                                طلبات الالتحاق المعلقة
                                <span className="badge badge-orange">{pendingRequests.length}</span>
                            </h3>
                            <a href="/teacher/enrollment-requests" className="card-link">عرض الكل</a>
                        </div>
                        <div className="card-body">
                            <div className="request-list">
                                {pendingRequests.slice(0, 5).map(req => (
                                    <RequestRow key={req.id} req={req} />
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* Recent Courses */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-book-2-line" /> مساراتي الأخيرة</h3>
                        <a href="/teacher/courses" className="card-link">إدارة المسارات</a>
                    </div>
                    <div className="card-body">
                        {recentCourses?.length > 0 ? (
                            <div className="course-list">
                                {recentCourses.map(course => (
                                    <TeacherCourseRow key={course.id} course={course} />
                                ))}
                            </div>
                        ) : (
                            <Empty icon="ri-book-2-line" text="لم تنشئ أي مسار بعد" action={{ href: '/teacher/create', label: 'إنشاء مسار' }} />
                        )}
                    </div>
                </section>

                {/* Quick Actions */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-lightning-line" /> إجراءات سريعة</h3>
                    </div>
                    <div className="card-body">
                        <div className="quick-actions">
                            <QuickAction href="/teacher/create"                icon="ri-add-circle-line"   label="مسار جديد"       color="blue" />
                            <QuickAction href="/teacher/exams"                  icon="ri-file-list-line"    label="الاختبارات"       color="purple" />
                            <QuickAction href="/teacher/enrollment-requests"    icon="ri-user-add-line"     label="طلبات الالتحاق"  color="orange" />
                            <QuickAction href="/teacher/analytics"              icon="ri-bar-chart-2-line"  label="التقارير"         color="teal" />
                            <QuickAction href="/teacher/students"               icon="ri-team-line"         label="طلابي"            color="green" />
                            <QuickAction href="/teacher/certificates/students"  icon="ri-award-line"        label="الشهادات"         color="gold" />
                        </div>
                    </div>
                </section>

                {/* Recent Activity */}
                {recentActivity?.length > 0 && (
                    <section className="card card-full">
                        <div className="card-header">
                            <h3 className="card-title"><i className="ri-history-line" /> آخر النشاطات</h3>
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

function TeacherCourseRow({ course }) {
    const statusLabel = { published: 'منشور', draft: 'مسودة', archived: 'مؤرشف' };
    const statusColor = { published: 'green', draft: 'orange', archived: 'gray' };
    return (
        <a href={`/teacher/courses/${course.id}`} className="course-row">
            <div className="course-info">
                <span className="course-title">{course.title}</span>
                <span className="course-meta">{course.students_count ?? 0} طالب • {course.lessons_count ?? 0} درس</span>
            </div>
            <span className={`badge badge-${statusColor[course.status] ?? 'gray'}`}>
                {statusLabel[course.status] ?? course.status}
            </span>
        </a>
    );
}

function RequestRow({ req }) {
    return (
        <div className="request-row">
            <div className="request-info">
                <span className="request-student">{req.student_name}</span>
                <span className="request-course">{req.course_title}</span>
            </div>
            <div className="request-actions">
                <button className="btn-xs btn-success" onClick={() => router.post(`/teacher/enrollment/${req.id}/approve`)}>
                    قبول
                </button>
                <button className="btn-xs btn-danger" onClick={() => router.post(`/teacher/enrollment/${req.id}/reject`)}>
                    رفض
                </button>
            </div>
        </div>
    );
}

function QuickAction({ href, icon, label, color }) {
    return (
        <a href={href} className={`quick-action quick-action-${color}`}>
            <i className={icon} />
            <span>{label}</span>
        </a>
    );
}

function ActivityRow({ item }) {
    const icons = { enrollment: 'ri-user-add-line', lesson: 'ri-play-circle-line', exam: 'ri-file-list-line' };
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
