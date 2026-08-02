import { usePage } from '@inertiajs/react';

export default function AppLayout({ children, title }) {
    const { auth, locale } = usePage().props;

    return (
        <div className="app" dir={locale === 'ar' ? 'rtl' : 'ltr'}>
            <main className="main">
                {children}
            </main>
        </div>
    );
}
