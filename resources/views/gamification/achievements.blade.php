<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإنجازات - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    @include('components.account-theme-head')
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-body), 'Tajawal', sans-serif; background: var(--bg); color: var(--text-primary); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .header {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 40px 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 { font-size: 32px; margin-bottom: 10px; }

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
            text-align: center;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--gold);
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .achievement-card {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }

        .achievement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .achievement-locked {
            opacity: 0.5;
        }

        .achievement-badge {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .achievement-name {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .achievement-description {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .achievement-requirement {
            background: #F0F2F5;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .achievement-reward {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            font-weight: 600;
            color: #FF9500;
        }

        .achievement-status {
            margin-top: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-earned {
            background: var(--success);
            color: white;
        }

        .status-locked {
            background: #E5E5EA;
            color: #6C6C70;
        }

        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            background: var(--card-bg);
            border: 2px solid #E5E5EA;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background: var(--gold);
            color: white;
            border-color: var(--gold);
        }

        @media (max-width: 768px) {
            .achievements-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
        @media (max-width: 480px) {
            .container { padding: 10px; }
            .achievements-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="ri-award-line"></i> الإنجازات</h1>
            <p>اجمع الشارات وافتح إنجازات جديدة</p>
        </div>

        @if(auth()->check())
            <div class="stats-grid">
                @php
                    $userAchievementsCount = auth()->user()->achievements()->count();
                    $totalAchievements = \App\Models\Achievement::count();
                @endphp

                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value">{{ $userAchievementsCount }}</div>
                    <div class="stat-label">إنجازاتك</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-value">{{ $totalAchievements }}</div>
                    <div class="stat-label">إجمالي متاح</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-value">{{ round(($userAchievementsCount / max($totalAchievements, 1)) * 100) }}%</div>
                    <div class="stat-label">اكتمال</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-value">{{ $totalAchievements - $userAchievementsCount }}</div>
                    <div class="stat-label">متبقي</div>
                </div>
            </div>
        @endif

        <div class="achievements-grid">
            @forelse($allAchievements as $achievement)
                @php
                    $earned = in_array($achievement->id, $userAchievements);
                @endphp
                
                <div class="achievement-card {{ !$earned ? 'achievement-locked' : '' }}">
                    <a href="{{ route('gamification.achievement.detail', $achievement) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="achievement-badge">{{ $achievement->badge_icon }}</div>
                        <div class="achievement-name">{{ $achievement->name }}</div>
                        <div class="achievement-description">{{ Str::limit($achievement->description, 60) }}</div>
                        <div class="achievement-requirement">
                            @switch($achievement->type)
                                @case('points')
                                    اجمع {{ $achievement->requirement }} نقطة
                                    @break
                                @case('exams_passed')
                                    أكمل {{ $achievement->requirement }} اختبار
                                    @break
                                @case('consecutive_days')
                                    {{ $achievement->requirement }} أيام متتالية
                                    @break
                                @case('smart_rewind_mastered')
                                    أتقن {{ $achievement->requirement }} مهارة
                                    @break
                            @endswitch
                        </div>
                        <div class="achievement-reward">
                            <i class="ri-coin-line"></i> +{{ $achievement->reward_points }} نقطة
                        </div>
                        <div class="achievement-status">
                            @if($earned)
                                <span class="status-badge status-earned">
                                    <i class="ri-check-line"></i> تم الحصول عليه
                                </span>
                            @else
                                <span class="status-badge status-locked">
                                    <i class="ri-lock-line"></i> مقفول
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-secondary);">
                    <i class="ri-inbox-line" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <p>لم تتم إضافة الإنجازات بعد</p>
                </div>
            @endforelse
        </div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
