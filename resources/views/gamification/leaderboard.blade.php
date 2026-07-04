<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة المتصدرين - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    @include('components.account-theme-head')
    
    <style>
        :root {
            --silver: #C0C0C0;
            --bronze: #CD7F32;
        }

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

        .leaderboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .leaderboard-table {
            background: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table-header {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 20px;
            font-weight: 600;
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .table-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr;
            gap: 15px;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #E5E5EA;
            transition: background 0.3s;
        }

        .table-row:hover {
            background: #F0F2F5;
        }

        .rank-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 18px;
        }

        .rank-1 {
            background: var(--gold);
            color: white;
        }

        .rank-2 {
            background: var(--silver);
            color: #333;
        }

        .rank-3 {
            background: var(--bronze);
            color: white;
        }

        .rank-other {
            background: #E5E5EA;
            color: var(--text-primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--gold);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .points-badge {
            background: #FFF3CD;
            color: #856404;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .current-user-section {
            background: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
        }

        .current-user-title {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 15px;
        }

        .current-user-badge {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .current-user-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .current-user-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E5E5EA;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--gold);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        .nearby-players {
            margin-top: 20px;
        }

        .nearby-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .nearby-item {
            background: #F0F2F5;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .nearby-rank {
            font-weight: 600;
            color: var(--gold);
            min-width: 30px;
        }

        @media (max-width: 768px) {
            .leaderboard-grid {
                grid-template-columns: 1fr;
            }

            .table-header, .table-row {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 480px) {
            .container { padding: 10px; }
            .header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="ri-trophy-line"></i> لوحة المتصدرين</h1>
            <p>أفضل الطلاب النشطين في منصة إجلال</p>
        </div>

        <div class="leaderboard-grid">
            <!-- Leaderboard Table -->
            <div class="leaderboard-table">
                <div class="table-header">
                    <span>الترتيب</span>
                    <span>الطالب</span>
                    <span>النقاط</span>
                    <span>الإنجازات</span>
                </div>

                @forelse($topPlayers as $index => $player)
                    <div class="table-row">
                        <div>
                            <div class="rank-badge rank-{{ $player->rank <= 3 ? $player->rank : 'other' }}">
                                {{ $player->rank }}
                            </div>
                        </div>
                        <div class="user-info">
                            <div class="user-avatar">{{ mb_substr($player->user->name, 0, 1) }}</div>
                            <span class="user-name">{{ $player->user->name }}</span>
                        </div>
                        <div>
                            <span class="points-badge">{{ $player->total_points }} <i class="ri-coin-line"></i></span>
                        </div>
                        <div style="text-align: center;">
                            <span style="color: var(--text-secondary); font-size: 13px;">
                                {{ $player->user->achievements()->count() }} إنجاز
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 20px; text-align: center; grid-column: 1/-1; color: var(--text-secondary);">
                        لا توجد بيانات حالياً
                    </div>
                @endforelse
            </div>

            <!-- Current User Section -->
            @if(auth()->check())
                <div class="current-user-section">
                    <div class="current-user-title">ترتيبك الحالي</div>
                    
                    @php
                        $userLeaderboard = \App\Models\Leaderboard::where('user_id', auth()->id())->first();
                    @endphp

                    @if($userLeaderboard)
                        <div class="current-user-badge">
                            @if($userLeaderboard->rank <= 3)
                                @if($userLeaderboard->rank == 1)
                                    🥇
                                @elseif($userLeaderboard->rank == 2)
                                    🥈
                                @else
                                    🥉
                                @endif
                            @else
                                🏅
                            @endif
                        </div>

                        <div class="current-user-name">{{ auth()->user()->name }}</div>

                        <div class="current-user-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $userLeaderboard->rank }}</div>
                                <div class="stat-label">الترتيب</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $userLeaderboard->total_points }}</div>
                                <div class="stat-label">النقاط</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ auth()->user()->achievements()->count() }}</div>
                                <div class="stat-label">الإنجازات</div>
                            </div>
                        </div>

                        @if($currentUserRank && $currentUserRank->count() > 0)
                            <div class="nearby-players">
                                <div class="nearby-title">الترتيب القريب</div>
                                @foreach($currentUserRank as $item)
                                    <div class="nearby-item">
                                        <span class="nearby-rank">#{{ $item->rank }}</span>
                                        <span style="flex: 1; margin-right: 10px;">{{ $item->user->name }}</span>
                                        <span style="color: var(--gold); font-weight: 600;">{{ $item->total_points }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('gamification.dashboard') }}" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: var(--gold); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                            <i class="ri-bar-chart-line"></i> إحصائياتي
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Info Section -->
        <div style="background: var(--card-bg); border-radius: 14px; padding: 20px; box-shadow: var(--shadow); text-align: center; color: var(--text-secondary);">
            <p>🎮 <strong>تحديث تلقائي كل ساعة</strong> - استمر في الدراسة وأتقن المهارات لتصعد الترتيب! 🚀</p>
        </div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
