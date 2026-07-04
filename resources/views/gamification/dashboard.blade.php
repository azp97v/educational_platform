<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة إحصائياتي - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    @include('components.account-theme-head')

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-body), 'Tajawal', sans-serif; background: var(--bg); color: var(--text-primary); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 { font-size: 28px; font-weight: 700; }

        .header-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            background: var(--gold);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: var(--gold-dark);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 20px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--gold);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-achievements {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .achievement-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #F0F2F5;
            border-radius: 8px;
        }

        .achievement-badge {
            font-size: 32px;
        }

        .achievement-info {
            flex: 1;
        }

        .achievement-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .achievement-date {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .achievement-reward {
            font-weight: 600;
            color: #FF9500;
        }

        .progress-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .progress-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .progress-label {
            font-weight: 606;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }

        .progress-bar {
            background: #E5E5EA;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(90deg, var(--gold), var(--gold-dark));
            height: 100%;
            border-radius: 4px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .action-btn {
            padding: 15px;
            background: #F0F2F5;
            border: 2px solid #E5E5EA;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-primary);
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: var(--gold);
            color: white;
            border-color: var(--gold);
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        @media (max-width: 480px) {
            .container { padding: 10px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎮 لوحة إحصائياتي</h1>
            <div class="header-buttons">
                <a href="{{ route('gamification.leaderboard') }}" class="btn">
                    <i class="ri-trophy-line"></i> لوحة المتصدرين
                </a>
                <a href="{{ route('gamification.achievements') }}" class="btn">
                    <i class="ri-award-line"></i> الإنجازات
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💎</div>
                <div class="stat-value">{{ $stats['total_points'] ?? 0 }}</div>
                <div class="stat-label">إجمالي النقاط</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🏅</div>
                <div class="stat-value">{{ $stats['rank'] ?? 'N/A' }}</div>
                <div class="stat-label">الترتيب العام</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-value">{{ $stats['exams_passed'] ?? 0 }}</div>
                <div class="stat-label">اختبارات مكتملة</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-value">{{ count($achievements ?? []) }}</div>
                <div class="stat-label">إنجازاتك</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🔥</div>
                <div class="stat-value">{{ $stats['consecutive_days'] ?? 0 }}</div>
                <div class="stat-label">أيام متتالية</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-value">{{ $stats['smart_rewinds_mastered'] ?? 0 }}</div>
                <div class="stat-label">مهارات متقنة</div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Recent Achievements -->
            <div class="card">
                <div class="card-title">
                    <i class="ri-award-line"></i> الإنجازات الحديثة
                </div>

                @if($recentAchievements->count() > 0)
                    <div class="recent-achievements">
                        @foreach($recentAchievements as $achievement)
                            <div class="achievement-item">
                                <div class="achievement-badge">{{ $achievement->badge_icon }}</div>
                                <div class="achievement-info">
                                    <div class="achievement-name">{{ $achievement->name }}</div>
                                    <div class="achievement-date">
                                        منذ {{ $achievement->pivot->achieved_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="achievement-reward">
                                    +{{ $achievement->reward_points }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; color: var(--text-secondary); padding: 40px 20px;">
                        لم تحقق إنجازات بعد. اجمع النقاط وأكمل المهام! 🚀
                    </p>
                @endif
            </div>

            <!-- Progress & Quick Actions -->
            <div>
                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-title">
                        <i class="ri-bar-chart-line"></i> التقدم
                    </div>

                    <div class="progress-section">
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>الاختبارات المكتملة</span>
                                <span>{{ $stats['exams_passed'] ?? 0 }}/10</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min(((($stats['exams_passed'] ?? 0) / 10) * 100), 100) }}%"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-label">
                                <span>المهارات المتقنة</span>
                                <span>{{ $stats['smart_rewinds_mastered'] ?? 0 }}/20</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min(((($stats['smart_rewinds_mastered'] ?? 0) / 20) * 100), 100) }}%"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-label">
                                <span>الأيام المتتالية</span>
                                <span>{{ $stats['consecutive_days'] ?? 0 }}/30</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min(((($stats['consecutive_days'] ?? 0) / 30) * 100), 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="ri-arrow-right-line"></i> إجراءات سريعة
                    </div>

                    <div class="quick-actions">
                        <a href="{{ route('student.index') }}" class="action-btn">
                            <i class="ri-book-line"></i> الدروس
                        </a>
                        <a href="{{ route('smart-rewind.index') }}" class="action-btn">
                            <i class="ri-video-ai-line"></i> Smart Rewind
                        </a>
                        <a href="{{ route('gamification.leaderboard') }}" class="action-btn">
                            <i class="ri-trophy-line"></i> ترتيبي
                        </a>
                        <a href="{{ route('gamification.achievements') }}" class="action-btn">
                            <i class="ri-award-line"></i> الإنجازات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
