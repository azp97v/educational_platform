import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function AdminDashboard({ user, stats, recentUsers, recentCourses }) {
    return (
        <AppLayout title="لوحة الإدارة">
            <Head title="لوحة الإدارة" />

            <div className="page-header">
                <h2 className="page-greeting">أهلاً {user?.name} <span role="img" aria-label="wave">👋</span></h2>
                <p className="page-sub">نظرة عامة على المنصة</p>
            </div>

            {/* Stats */}
            <div className="stats-grid">
                <StatCard icon="ri-team-line"          label="المستخدمون"    value={stats?.total_users ?? 0}    color="blue" />
                <StatCard icon="ri-user-line"          label="الطلاب"        value={stats?.students ?? 0}       color="green" />
                <StatCard icon="ri-user-star-line"     label="المعلمون"      value={stats?.teachers ?? 0}       color="purple" />
                <StatCard icon="ri-book-2-line"        label="المسارات"      value={stats?.total_courses ?? 0}  color="orange" />
                <StatCard icon="ri-file-list-line"     label="الاختبارات"    value={stats?.total_exams ?? 0}    color="teal" />
                <StatCard icon="ri-award-line"         label="الشهادات"      value={stats?.certificates ?? 0}   color="gold" />
            </div>

            <div className="dashboard-grid">
                {/* Platform Health */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-pulse-line" /> صحة المنصة</h3>
                    </div>
                    <div className="card-body">
                        <div className="health-list">
                            <HealthRow label="قاعدة البيانات" status={stats?.db_ok !== false} />
                            <HealthRow label="Redis / Cache" status={stats?.redis_ok !== false} />
                            <HealthRow label="Horizon Queue" status={stats?.horizon_ok !== false} />
                            <HealthRow label="تخزين الملفات (R2)" status={stats?.storage_ok !== false} />
                        </div>
                    </div>
                </section>

                {/* Quick Actions */}
                <section className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="ri-lightning-line" /> إجراءات سريعة</h3>
                    </div>
                    <div className="card-body">
                        <div className="quick-actions">
                            <QuickAction href="/admin/users"    icon="ri-team-line"         label="المستخدمون"    color="blue" />
                            <QuickAction href="/admin/courses"  icon="ri-book-2-line"       label="المسارات"      color="orange" />
                            <QuickAction href="/certificates"   icon="ri-award-line"        label="الشهادات"      color="gold" />
                            <QuickAction href="/messaging"      icon="ri-message-3-line"    label="الرسائل"       color="green" />
                            <QuickAction href="/admin/settings" icon="ri-settings-3-line"   label="الإعدادات"    color="teal" />
                            <QuickAction href="/horizon"        icon="ri-bar-chart-2-line"  label="Horizon"       color="purple" />
                        </div>
                    </div>
                </section>

                {/* Recent Users */}
                {recentUsers?.length > 0 && (
                    <section className="card">
                        <div className="card-header">
                            <h3 className="card-title"><i className="ri-user-add-line" /> أحدث المستخدمين</h3>
                            <a href="/admin/users" className="card-link">عرض الكل</a>
                        </div>
                        <div className="card-body">
                            <div className="user-list">
                                {recentUsers.map(u => (
                                    <UserRow key={u.id} user={u} />
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* Recent Courses */}
                {recentCourses?.length > 0 && (
                    <section className="card">
                        <div className="card-header">
                            <h3 className="card-title"><i className="ri-book-2-line" /> أحدث المسارات</h3>
                            <a href="/admin/courses" className="card-link">عرض الكل</a>
                        </div>
                        <div className="card-body">
                            <div className="course-list">
                                {recentCourses.map(course => (
                                    <AdminCourseRow key={course.id} course={course} />
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

function HealthRow({ label, status }) {
    return (
        <div className="health-row">
            <span className="health-label">{label}</span>
            <span className={`health-status ${status ? 'health-ok' : 'health-fail'}`}>
                <i className={status ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'} />
                {status ? 'يعمل' : 'مشكلة'}
            </span>
        </div>
    );
}

function UserRow({ user }) {
    const roleLabel = { student: 'طالب', teacher: 'معلم', admin: 'مدير' };
    const roleColor = { student: 'blue', teacher: 'green', admin: 'purple' };
    return (
        <div className="user-row">
            <div className="user-info">
                <span className="user-name">{user.name}</span>
                <span className="user-email">{user.email}</span>
            </div>
            <span className={`badge badge-${roleColor[user.role] ?? 'gray'}`}>
                {roleLabel[user.role] ?? user.role}
            </span>
        </div>
    );
}

function AdminCourseRow({ course }) {
    const statusColor = { published: 'green', draft: 'orange', archived: 'gray' };
    return (
        <div className="course-row">
            <div className="course-info">
                <span className="course-title">{course.title}</span>
                <span className="course-meta">{course.instructor_name}</span>
            </div>
            <span className={`badge badge-${statusColor[course.status] ?? 'gray'}`}>{course.status}</span>
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
