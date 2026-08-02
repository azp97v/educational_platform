import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function StudentDashboard({ user, stats, courses }) {
    return (
        <AppLayout>
            <Head title="لوحة التحكم" />
            <div className="content">
                <div className="p-6">
                    <h1 className="text-2xl font-bold text-text mb-6">
                        أهلاً {user?.name} 👋
                    </h1>

                    {/* Stats row */}
                    <div className="grid grid-cols-1 gap-4" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(160px, 1fr))' }}>
                        <StatCard label="مسارات نشطة" value={stats?.active_courses ?? 0} icon="ri-book-open-line" />
                        <StatCard label="نقاطي" value={stats?.points ?? 0} icon="ri-trophy-line" />
                        <StatCard label="شهاداتي" value={stats?.certificates ?? 0} icon="ri-award-line" />
                        <StatCard label="أيام متواصلة" value={stats?.streak ?? 0} icon="ri-fire-line" />
                    </div>

                    {/* Courses */}
                    {courses?.length > 0 && (
                        <div className="mt-6">
                            <h2 className="text-lg font-semibold text-text mb-3">مساراتك التعليمية</h2>
                            <div className="grid grid-cols-1 gap-3">
                                {courses.map(course => (
                                    <CourseCard key={course.id} course={course} />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

function StatCard({ label, value, icon }) {
    return (
        <div className="rounded-md p-4" style={{ background: 'var(--theme-surface)', border: '1px solid var(--theme-border)' }}>
            <i className={`${icon} text-gold text-xl mb-2 block`}></i>
            <div className="text-2xl font-bold text-text">{value}</div>
            <div className="text-sm text-muted mt-1">{label}</div>
        </div>
    );
}

function CourseCard({ course }) {
    const progress = course.progress ?? 0;
    return (
        <div className="rounded-md p-4" style={{ background: 'var(--theme-surface)', border: '1px solid var(--theme-border)' }}>
            <div className="flex items-center justify-between mb-2">
                <span className="font-semibold text-text">{course.title}</span>
                <span className="text-sm text-muted">{progress}%</span>
            </div>
            <div style={{ background: 'var(--theme-border)', borderRadius: 4, height: 6 }}>
                <div style={{ width: `${progress}%`, background: 'var(--theme-gold)', borderRadius: 4, height: 6, transition: 'width 0.4s' }} />
            </div>
        </div>
    );
}
