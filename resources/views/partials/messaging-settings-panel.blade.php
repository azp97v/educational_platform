<div class="settings-shell" v-if="settingsPanelOpen">
    <div class="settings-shell__scrim" @click="closeSettings"></div>

    <section class="settings-drawer" @click.stop>
        <template v-if="settingsSection === 'main_menu'">
            <div class="settings-drawer__head">
                <div>
                    <div class="settings-kicker">لوحة التحكم</div>
                    <h3 class="settings-title">إعدادات المراسلة</h3>
                </div>
                <button class="h-icon-btn" @click="closeSettings"><i class="ri-close-line"></i></button>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-hero-card">
                    <div class="settings-hero-card__avatar">
                        <img v-if="myProfileAvatar || currentUserAvatar" :src="myProfileAvatar || normalizeAvatarUrl(currentUserAvatar)" alt="" v-on:error="handleAvatarError($event, {avatar_url: null})">
                        <span v-else>@{{ getAuthorInitial(userName) }}</span>
                    </div>

                    <div class="settings-hero-card__body">
                        <div class="settings-hero-card__name">@{{ userName }}</div>
                        <div class="settings-hero-card__meta" dir="ltr">@{{ settingsAccount.phone || '' }}</div>
                        <div class="settings-hero-card__meta">@{{ settingsAccount.username ? '@' + settingsAccount.username : (currentUserId ? '#' + currentUserId : '') }}</div>
                    </div>

                    <button class="settings-hero-card__action" @click="openSettingsSection('account')">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                </div>

                <div class="settings-group-card">
                    <button class="settings-nav-row" @click="openSettingsSection('account')">
                        <span class="settings-nav-row__icon"><i class="ri-user-3-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الحساب والملف الشخصي</strong>
                            <small>الاسم، الصورة، اسم المستخدم، السيرة، ورمز QR</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('notifications')">
                        <span class="settings-nav-row__icon"><i class="ri-notification-3-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الإشعارات والأصوات</strong>
                            <small>إشعارات المتصفح، الصوت، المعاينة، ومستوى الصوت</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('privacy')">
                        <span class="settings-nav-row__icon"><i class="ri-lock-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الخصوصية والأمان</strong>
                            <small>الرؤية، المحظورون، الجلسات، الحذف التلقائي، و2FA</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('chats')">
                        <span class="settings-nav-row__icon"><i class="ri-chat-1-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>المحادثات والمجلدات</strong>
                            <small>الثيمات، الخلفيات، الخطوط، سلوك الرسائل، وتنظيم القائمة</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('media')">
                        <span class="settings-nav-row__icon"><i class="ri-image-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الوسائط والتنزيل</strong>
                            <small>التحميل التلقائي وجودة الوسائط وسياسات الشبكة</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('mic')">
                        <span class="settings-nav-row__icon"><i class="ri-mic-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الأجهزة الصوتية</strong>
                            <small>اختيار الميكروفون المتاح في المتصفح</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('calls')">
                        <span class="settings-nav-row__icon"><i class="ri-phone-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>المكالمات</strong>
                            <small>نغمة الرنين، الأجهزة، البيانات المنخفضة، وعدم الإزعاج</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>
                </div>

                <div class="settings-group-card">
                    <button class="settings-nav-row" @click="openSettingsSection('battery')">
                        <span class="settings-nav-row__icon"><i class="ri-battery-2-charge-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>الأداء والحركة</strong>
                            <small>تقليل الحركة وتحسين استهلاك الواجهة</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('advanced')">
                        <span class="settings-nav-row__icon"><i class="ri-settings-4-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>متقدم</strong>
                            <small>المدقق الإملائي، العنوان، التصدير، وتنظيف التخزين المحلي</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('language')">
                        <span class="settings-nav-row__icon"><i class="ri-translate-2"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>اللغة</strong>
                            <small>@{{ settingsLanguageChoice === 'ar' ? 'العربية' : 'English' }}</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-nav-row" @click="openSettingsSection('about')">
                        <span class="settings-nav-row__icon"><i class="ri-information-line"></i></span>
                        <span class="settings-nav-row__body">
                            <strong>عن التطبيق</strong>
                            <small>معلومات تقنية وسلوك حفظ الإعدادات</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>
                </div>

                <div class="settings-inline-card">
                    <div class="settings-inline-card__copy">
                        <strong>المظهر الليلي</strong>
                        <small>تبديل مباشر بين الوضع الليلي والنهاري على مستوى التطبيق</small>
                    </div>
                    <button class="sp-toggle" :class="{on: isDarkMode}" @click="toggleDark"></button>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'account'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الحساب والملف الشخصي</h4>
                <button class="h-icon-btn" @click="settingsEdit = !settingsEdit"><i :class="settingsEdit ? 'ri-close-line' : 'ri-edit-2-line'"></i></button>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-profile-card">
                    <div class="settings-profile-card__top">
                        <div class="settings-profile-card__avatar">
                            <img v-if="myProfileAvatar || currentUserAvatar" :src="myProfileAvatar || normalizeAvatarUrl(currentUserAvatar)" alt="" v-on:error="handleAvatarError($event, {avatar_url: null})">
                            <span v-else>@{{ getAuthorInitial(userName) }}</span>
                            <button v-if="settingsEdit" class="settings-profile-card__camera" @click.stop="$refs.avatarFileInput.click()"><i class="ri-camera-line"></i></button>
                            <input ref="avatarFileInput" type="file" accept="image/*" style="display:none;" @change="uploadAvatarFile">
                        </div>

                        <div class="settings-profile-card__identity">
                            <div class="settings-profile-card__name" :style="{color: settingsChats.nameColor || 'var(--text)'}">@{{ userName }}</div>
                            <div class="settings-profile-card__username">@{{ settingsAccount.username ? '@' + settingsAccount.username : (currentUserId ? '#' + currentUserId : '') }}</div>
                            <div class="settings-profile-card__meta">@{{ settingsAccount.member_since || 'عضو جديد' }}</div>
                        </div>

                        <button class="settings-ghost-btn" @click="qrModalOpen = true; qrModalContact = {id:currentUserId,name:myProfileDisplayName || userName,username:settingsAccount.username,avatar_url:myProfileAvatar}; $nextTick(() => { doGenerateQrCode(); })"><i class="ri-qr-code-line"></i><span>QR</span></button>
                    </div>
                </div>

                <div class="settings-info-grid" v-if="settingsEdit">
                    <div>
                        <div class="ve-label">الاسم الكامل</div>
                        <input class="sp-input" v-model="settingsEditName" placeholder="الاسم الكامل">
                    </div>

                    <div>
                        <div class="ve-label">اسم المستخدم</div>
                        <div class="sp-input-check-wrap">
                            <input class="sp-input" v-model="settingsEditUsername" @input="onUsernameInput" placeholder="username" minlength="3">
                            <i class="sp-input-check-icon ri-loader-4-line spin" v-if="usernameCheckState === 'checking'"></i>
                            <i class="sp-input-check-icon ri-checkbox-circle-fill ok" v-else-if="usernameCheckState === 'available'"></i>
                            <i class="sp-input-check-icon ri-close-circle-fill bad" v-else-if="usernameCheckState === 'unavailable'"></i>
                        </div>
                        <div class="sp-input-hint" v-if="usernameCheckMessage" :class="{bad: usernameCheckState === 'unavailable'}">@{{ usernameCheckMessage }}</div>
                        <div class="sp-input-hint" v-else-if="settingsAccount.username_changed_at && usernameCooldownDaysLeft > 0">يمكنك تغييره بعد @{{ usernameCooldownDaysLeft }} يوماً</div>
                    </div>

                    <div>
                        <div class="ve-label">رقم الهاتف</div>
                        <div class="sp-input-check-wrap">
                            <input class="sp-input" v-model="settingsEditPhone" @input="onPhoneInput" placeholder="05xxxxxxxx" minlength="6">
                            <i class="sp-input-check-icon ri-loader-4-line spin" v-if="phoneCheckState === 'checking'"></i>
                            <i class="sp-input-check-icon ri-checkbox-circle-fill ok" v-else-if="phoneCheckState === 'available'"></i>
                            <i class="sp-input-check-icon ri-close-circle-fill bad" v-else-if="phoneCheckState === 'unavailable'"></i>
                        </div>
                        <div class="sp-input-hint bad" v-if="phoneCheckMessage">@{{ phoneCheckMessage }}</div>
                    </div>

                    <div>
                        <div class="ve-label">السيرة الذاتية</div>
                        <input class="sp-input" v-model="settingsEditBio" maxlength="120" placeholder="مثال: مصمم، 23 سنة">
                    </div>

                    <div>
                        <div class="ve-label">تاريخ الميلاد</div>
                        <input class="sp-input" type="date" v-model="settingsEditBirthday">
                    </div>
                </div>

                <div class="settings-accent-card" v-if="settingsEdit">
                    <div class="settings-kicker">لون الاسم</div>
                    <div class="settings-chip-row">
                        <button class="settings-color-chip" :class="{active: !settingsChats.nameColor}" @click="settingsChats.nameColor=''; saveMessagingSettings()">
                            <i class="ri-close-line"></i>
                        </button>
                        <button v-for="c in ['#c0392b', '#2980b9', '#27ae60', '#8e44ad', '#d35400', '#16a085', '#c6a475']" :key="'nc-'+c" class="settings-color-chip" :class="{active: settingsChats.nameColor===c}" :style="{background:c}" @click="settingsChats.nameColor=c; saveMessagingSettings()"></button>
                    </div>
                    <button class="sp-save-btn" @click="saveAccountInfo">حفظ التعديلات</button>
                </div>

                <div class="settings-group-card" v-else>
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">الاسم</span>
                        <strong>@{{ userName }}</strong>
                    </div>
                    <div class="settings-stat-row" v-if="settingsAccount.bio">
                        <span class="settings-stat-row__label">السيرة الذاتية</span>
                        <strong>@{{ settingsAccount.bio }}</strong>
                    </div>
                    <div class="settings-stat-row" v-if="settingsAccount.phone">
                        <span class="settings-stat-row__label">الهاتف</span>
                        <strong dir="ltr">@{{ settingsAccount.phone }}</strong>
                    </div>
                    <div class="settings-stat-row" v-if="settingsAccount.birthday">
                        <span class="settings-stat-row__label">تاريخ الميلاد</span>
                        <strong>@{{ settingsAccount.birthday }}</strong>
                    </div>
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">البريد المرتبط</span>
                        <strong dir="ltr">@{{ settingsAccount.email_masked || '--' }}</strong>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'notifications'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الإشعارات والأصوات</h4>
            </div>

            <div class="settings-drawer__body">

                {{-- ── معاينة الإشعار (TikTok-style card) ─────── --}}
                <p class="sn-section-label">معاينة الإشعار</p>
                <div class="settings-group-card sn-preview-group">
                    <div class="sn-notif-card">
                        <div class="sn-nc-avatar">
                            <i class="ri-user-3-fill"></i>
                        </div>
                        <div class="sn-nc-body">
                            <p class="sn-nc-name" v-if="settingsNotifications.showName">أحمد محمد</p>
                            <p class="sn-nc-name sn-nc-blur" v-else>■■■■■■■</p>
                            <p class="sn-nc-msg" v-if="settingsNotifications.showText">مرحباً، كيف حالك؟</p>
                            <p class="sn-nc-msg sn-nc-blur" v-else>■■■■■■■■■■■</p>
                        </div>
                        <div class="sn-nc-right">
                            <span class="sn-nc-time">الآن</span>
                            <button class="sn-nc-close"><i class="ri-close-line"></i></button>
                        </div>
                    </div>
                    <div class="sn-preview-checks">
                        <label class="sn-check-label">
                            <input type="checkbox" v-model="settingsNotifications.showName" @change="saveMessagingSettings()">
                            <span>الاسم</span>
                        </label>
                        <label class="sn-check-label">
                            <input type="checkbox" v-model="settingsNotifications.showText" @change="saveMessagingSettings()">
                            <span>النص</span>
                        </label>
                    </div>
                </div>

                {{-- ── الإعدادات العامة ─────────────────────────── --}}
                <p class="sn-section-label">الإعدادات العامة</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>إشعارات سطح المكتب</strong>
                            <small>إشعار من المتصفح عند ورود رسالة والنافذة غير مركّزة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.desktopEnabled}" @click="settingsNotifications.desktopEnabled ? (settingsNotifications.desktopEnabled=false, saveMessagingSettings()) : requestDesktopNotifications()"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>إضاءة شريط المهام</strong>
                            <small>تومض أيقونة التطبيق عند وصول إشعار جديد</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.flashTaskbar}" @click="settingsNotifications.flashTaskbar=!settingsNotifications.flashTaskbar; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>السماح بالأصوات</strong>
                            <small>تشغيل أو إيقاف صوت التنبيهات الواردة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.soundEnabled}" @click="settingsNotifications.soundEnabled=!settingsNotifications.soundEnabled; saveMessagingSettings();"></button>
                    </div>
                    <div class="sp-slider-row" v-if="settingsNotifications.soundEnabled">
                        <span class="sp-slider-label">المستوى</span>
                        <input type="range" min="0" max="100" :value="settingsNotifications.volume" @input="updateGlobalVolume($event.target.value)" class="sp-slider">
                        <span class="sp-slider-value">@{{ settingsNotifications.volume }}%</span>
                    </div>
                </div>

                {{-- ── الإشعارات للمحادثات ─────────────────────── --}}
                <p class="sn-section-label">الإشعارات للمحادثات</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>المحادثات الخاصة</strong>
                            <small v-if="mutedContactsCount > 0">@{{ mutedContactsCount }} استثناء نشط</small>
                            <small v-else>جميع المحادثات الخاصة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.privateChats}" @click="settingsNotifications.privateChats=!settingsNotifications.privateChats; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>المجموعات</strong>
                            <small>جميع المجموعات والمحادثات الجماعية</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.groups}" @click="settingsNotifications.groups=!settingsNotifications.groups; saveMessagingSettings();"></button>
                    </div>
                </div>

                {{-- ── الأحداث ──────────────────────────────────── --}}
                <p class="sn-section-label">الأحداث</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>انضمام جهة اتصال</strong>
                            <small>إشعار عند انضمام شخص من جهات اتصالك</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.notifyContactJoined}" @click="settingsNotifications.notifyContactJoined=!settingsNotifications.notifyContactJoined; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>الرسائل المثبتة</strong>
                            <small>إشعار عند تثبيت رسالة في محادثة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.notifyPinned}" @click="settingsNotifications.notifyPinned=!settingsNotifications.notifyPinned; saveMessagingSettings();"></button>
                    </div>
                </div>

                {{-- ── المكالمات + بطاقة المكالمة ───────────────── --}}
                <p class="sn-section-label">المكالمات</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>قبول المكالمات على هذا الجهاز</strong>
                            <small>استقبال المكالمات الواردة على هذا المتصفح</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.acceptCallsOnDevice}" @click="settingsNotifications.acceptCallsOnDevice=!settingsNotifications.acceptCallsOnDevice; saveMessagingSettings();"></button>
                    </div>
                    {{-- Call card preview --}}
                    <div class="sn-call-card">
                        <div class="sn-call-glow"></div>
                        <div class="sn-call-avatar-wrap">
                            <div class="sn-call-ring sn-call-ring--1"></div>
                            <div class="sn-call-ring sn-call-ring--2"></div>
                            <div class="sn-call-avatar"><i class="ri-user-3-fill"></i></div>
                        </div>
                        <div class="sn-call-info">
                            <span class="sn-call-label">مكالمة صوتية واردة</span>
                            <strong class="sn-call-name">أحمد محمد</strong>
                        </div>
                        <div class="sn-call-actions">
                            <button class="sn-call-btn sn-call-decline" title="رفض"><i class="ri-close-line"></i></button>
                            <button class="sn-call-btn sn-call-accept" title="قبول"><i class="ri-phone-fill"></i></button>
                        </div>
                    </div>
                </div>

                {{-- ── عداد الشارة ───────────────────────────────── --}}
                <p class="sn-section-label">عداد الشارة</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تضمين المكتومة في العدد الكلي</strong>
                            <small>تُحسب الرسائل في المحادثات المكتومة ضمن الشارة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.includeMutedBadge}" @click="settingsNotifications.includeMutedBadge=!settingsNotifications.includeMutedBadge; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تضمين المكتومة في عدادات المجلدات</strong>
                            <small>تُظهر المجلدات الرسائل المكتومة في عداداتها</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.includeMutedFolders}" @click="settingsNotifications.includeMutedFolders=!settingsNotifications.includeMutedFolders; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>عد الرسائل غير المقروءة</strong>
                            <small>عرض عدد الرسائل بدلاً من عدد المحادثات</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.countUnread}" @click="settingsNotifications.countUnread=!settingsNotifications.countUnread; saveMessagingSettings();"></button>
                    </div>
                </div>

                {{-- ── تكامل النظام ──────────────────────────────── --}}
                <p class="sn-section-label">تكامل النظام</p>
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>استخدام إشعارات ويندوز</strong>
                            <small>إرسال الإشعارات عبر نظام ويندوز بدلاً من المتصفح</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.useWindowsNotif}" @click="settingsNotifications.useWindowsNotif=!settingsNotifications.useWindowsNotif; saveMessagingSettings();"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>احترام وضع التركيز</strong>
                            <small>لا ترسل إشعارات عند تفعيل وضع التركيز في ويندوز</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsNotifications.respectFocusMode}" @click="settingsNotifications.respectFocusMode=!settingsNotifications.respectFocusMode; saveMessagingSettings();"></button>
                    </div>
                </div>

                {{-- ── موقع الإشعار + عدد المتراكمة (تفاعلي) ──── --}}
                <p class="sn-section-label">موقع وعدد الإشعارات</p>
                <div class="settings-group-card">
                    <div class="sn-screen-picker">

                        {{-- Virtual monitor: hover corner → real viewport preview --}}
                        <div class="sn-screen-monitor">
                            <div class="sn-screen-inner">
                                <button class="sn-corner sn-corner--tl"
                                    :class="{active: settingsNotifications.notifPosition==='top-left'}"
                                    @mouseenter="showSnPreview('top-left')"
                                    @click="settingsNotifications.notifPosition='top-left'; saveMessagingSettings();">
                                    <span class="sn-corner-dot"></span>
                                </button>
                                <button class="sn-corner sn-corner--tr"
                                    :class="{active: settingsNotifications.notifPosition==='top-right'}"
                                    @mouseenter="showSnPreview('top-right')"
                                    @click="settingsNotifications.notifPosition='top-right'; saveMessagingSettings();">
                                    <span class="sn-corner-dot"></span>
                                </button>
                                <button class="sn-corner sn-corner--bl"
                                    :class="{active: settingsNotifications.notifPosition==='bottom-left'}"
                                    @mouseenter="showSnPreview('bottom-left')"
                                    @click="settingsNotifications.notifPosition='bottom-left'; saveMessagingSettings();">
                                    <span class="sn-corner-dot"></span>
                                </button>
                                <button class="sn-corner sn-corner--br"
                                    :class="{active: settingsNotifications.notifPosition==='bottom-right'}"
                                    @mouseenter="showSnPreview('bottom-right')"
                                    @click="settingsNotifications.notifPosition='bottom-right'; saveMessagingSettings();">
                                    <span class="sn-corner-dot"></span>
                                </button>
                                {{-- Active corner indicator --}}
                                <div class="sn-screen-active-dot"
                                    :class="`sn-dot-${settingsNotifications.notifPosition}`">
                                </div>
                            </div>
                            <div class="sn-screen-stand"></div>
                        </div>

                        {{-- Position label --}}
                        <p class="sn-pos-label">
                            @{{ {'top-right':'أعلى اليمين','top-left':'أعلى اليسار','bottom-right':'أسفل اليمين','bottom-left':'أسفل اليسار'}[settingsNotifications.notifPosition] }}
                        </p>
                        <p class="settings-note" style="padding:0 2px 8px; color: var(--text-secondary); font-size: 11px;">مرّر الماوس على زاوية لمعاينة موضع الإشعار على شاشتك</p>

                        {{-- Count tabs below --}}
                        <p class="sn-count-label">عدد الإشعارات المتراكمة</p>
                        <div class="sn-count-tabs">
                            <button v-for="n in [1,2,3,4,5]" :key="n"
                                class="sn-count-tab"
                                :class="{active: settingsNotifications.notifMaxCount === n}"
                                @click="settingsNotifications.notifMaxCount=n; saveMessagingSettings(); snPreviewVisible && showSnPreview(snPreviewPos)">
                                @{{ n }}
                            </button>
                        </div>

                        {{-- Display mode toggle --}}
                        <p class="sn-count-label" style="margin-top:10px;">طريقة العرض</p>
                        <div class="sn-display-mode-tabs">
                            <button class="sn-mode-tab"
                                :class="{active: settingsNotifications.notifDisplayMode === 'stacked'}"
                                @click="settingsNotifications.notifDisplayMode='stacked'; saveMessagingSettings(); snPreviewVisible && showSnPreview(snPreviewPos)">
                                <i class="ri-stack-line"></i>
                                <span>مكدّسة</span>
                            </button>
                            <button class="sn-mode-tab"
                                :class="{active: settingsNotifications.notifDisplayMode === 'separate'}"
                                @click="settingsNotifications.notifDisplayMode='separate'; saveMessagingSettings(); snPreviewVisible && showSnPreview(snPreviewPos)">
                                <i class="ri-layout-top-2-line"></i>
                                <span>منفصلة</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </template>

        <template v-if="settingsSection === 'privacy'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الخصوصية والأمان</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>إخفاء حالة الاتصال</strong>
                            <small>لن يظهر أنك متصل الآن داخل تطبيق المراسلة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsPrivacy.hideOnlineStatus}" @click="settingsPrivacy.hideOnlineStatus=!settingsPrivacy.hideOnlineStatus; saveMessagingSettings();"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>التحقق بخطوتين</strong>
                            <small>@{{ settingsSecurity.twoFaEnabled ? 'مفعّل حاليًا على البريد المرتبط' : 'غير مفعّل بعد' }}</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsSecurity.twoFaEnabled}" @click="settingsSecurity.twoFaEnabled ? null : request2FACode()"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>قفل محلي برمز PIN</strong>
                            <small>حماية إضافية داخل الواجهة الحالية</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsSecurity.pinEnabled}" @click="settingsSecurity.pinEnabled=!settingsSecurity.pinEnabled"></button>
                    </div>

                    <div class="settings-security-form" v-if="settingsSecurity.pinEnabled">
                        <input class="sp-input" type="password" v-model="settingsSecurity.pin" placeholder="أدخل رمز PIN" maxlength="6">
                        <input class="sp-input" type="password" v-model="settingsSecurity.pinConfirm" placeholder="أعد إدخال رمز PIN" maxlength="6">
                        <button class="sp-save-btn" @click="savePIN">حفظ رمز PIN</button>
                    </div>

                    <div class="settings-security-form" v-if="!settingsSecurity.twoFaEnabled && settings2FAStep === 'code-sent'">
                        <input class="sp-input" v-model="settings2FACode" placeholder="أدخل رمز التحقق المرسل إلى بريدك" maxlength="6">
                        <button class="sp-save-btn" @click="confirm2FACode">تأكيد التفعيل</button>
                    </div>

                    <div class="settings-security-form" v-if="settingsSecurity.twoFaEnabled">
                        <input class="sp-input" type="password" v-model="settings2FADisablePassword" placeholder="كلمة المرور لتعطيل التحقق بخطوتين">
                        <button class="sp-save-btn" @click="disable2FAConfirm">تعطيل التحقق بخطوتين</button>
                    </div>
                </div>

                <div>
                    <div class="ve-label">من يرى آخر ظهور</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.lastSeenFor==='all'}" @click="settingsPrivacy.lastSeenFor='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>الجميع</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.lastSeenFor==='contacts'}" @click="settingsPrivacy.lastSeenFor='contacts'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>جهات الاتصال فقط</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.lastSeenFor==='nobody'}" @click="settingsPrivacy.lastSeenFor='nobody'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>لا أحد</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">من يرى صورة الملف الشخصي</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.profilePhotoFor==='all'}" @click="settingsPrivacy.profilePhotoFor='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>الجميع</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.profilePhotoFor==='contacts'}" @click="settingsPrivacy.profilePhotoFor='contacts'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>جهات الاتصال فقط</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.profilePhotoFor==='nobody'}" @click="settingsPrivacy.profilePhotoFor='nobody'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>لا أحد</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">من يمكنه مراسلتي</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.messageFrom==='all'}" @click="settingsPrivacy.messageFrom='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>الجميع</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.messageFrom==='contacts'}" @click="settingsPrivacy.messageFrom='contacts'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>جهات الاتصال فقط</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">من يمكنه الاتصال بي</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.callFrom==='all'}" @click="settingsPrivacy.callFrom='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>الجميع</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.callFrom==='contacts'}" @click="settingsPrivacy.callFrom='contacts'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>جهات الاتصال فقط</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.callFrom==='nobody'}" @click="settingsPrivacy.callFrom='nobody'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>لا أحد</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">إظهار رقم الهاتف</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.phoneVisibleFor==='all'}" @click="settingsPrivacy.phoneVisibleFor='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>الجميع</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.phoneVisibleFor==='contacts'}" @click="settingsPrivacy.phoneVisibleFor='contacts'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>جهات الاتصال فقط</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.phoneVisibleFor==='nobody'}" @click="settingsPrivacy.phoneVisibleFor='nobody'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>لا أحد</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">نسبة الرسائل المحوّلة إليّ</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.forwardedMessagesFor==='all'}" @click="settingsPrivacy.forwardedMessagesFor='all'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>إظهار اسم المرسل</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.forwardedMessagesFor==='nobody'}" @click="settingsPrivacy.forwardedMessagesFor='nobody'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>إخفاء المصدر</span></div>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>اقتراح جهات الاتصال المتكررة</strong>
                            <small>عرض الأشخاص الأكثر مراسلة في نتائج البحث والاختيار</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsPrivacy.frequentContactsEnabled}" @click="settingsPrivacy.frequentContactsEnabled=!settingsPrivacy.frequentContactsEnabled; saveMessagingSettings(); settingsPrivacy.frequentContactsEnabled && loadFrequentContacts()"></button>
                    </div>

                    <div class="settings-contact-list" v-if="settingsPrivacy.frequentContactsEnabled && settingsFrequentContacts.length">
                        <div class="settings-contact-row" v-for="c in settingsFrequentContacts" :key="'freq-'+c.id">
                            <span class="settings-contact-row__avatar">
                                <img v-if="c.avatar_url" :src="normalizeAvatarUrl(c.avatar_url)" alt="">
                                <span v-else>@{{ getAuthorInitial(c.name) }}</span>
                            </span>
                            <span class="settings-contact-row__body">
                                <strong>@{{ c.name }}</strong>
                                <small>@{{ c.message_count }} رسالة</small>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">الحذف التلقائي للرسائل</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.autoDeleteDays===0}" @click="settingsPrivacy.autoDeleteDays=0; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>معطّل</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.autoDeleteDays===30}" @click="settingsPrivacy.autoDeleteDays=30; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>بعد شهر</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.autoDeleteDays===90}" @click="settingsPrivacy.autoDeleteDays=90; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>بعد 3 أشهر</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.autoDeleteDays===365}" @click="settingsPrivacy.autoDeleteDays=365; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>بعد سنة</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">تعطيل الحساب إذا كنت غائبًا</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.deleteAccountAfterMonths===0}" @click="settingsPrivacy.deleteAccountAfterMonths=0; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>أبدًا</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.deleteAccountAfterMonths===6}" @click="settingsPrivacy.deleteAccountAfterMonths=6; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>6 أشهر</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsPrivacy.deleteAccountAfterMonths===12}" @click="settingsPrivacy.deleteAccountAfterMonths=12; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>12 شهرًا</span></div>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">المستخدمون المحظورون</span>
                        <strong>@{{ settingsBlockedList.length }}</strong>
                    </div>

                    <div class="settings-inline-form">
                        <input class="sp-input" v-model="settingsBlockUserIdInput" placeholder="أدخل معرّف المستخدم للحظر" inputmode="numeric">
                        <button class="sp-save-btn settings-inline-form__button" @click="blockUserById">حظر</button>
                    </div>

                    <div class="settings-list-empty" v-if="settingsBlockedLoading">جارٍ تحميل المحظورين...</div>
                    <div class="settings-list-empty" v-else-if="!settingsBlockedList.length">لا يوجد مستخدمون محظورون.</div>

                    <div class="settings-contact-list" v-else>
                        <div class="settings-contact-row" v-for="u in settingsBlockedList" :key="'blk-'+u.id">
                            <span class="settings-contact-row__avatar">
                                <img v-if="u.avatar_url" :src="normalizeAvatarUrl(u.avatar_url)" alt="">
                                <span v-else>@{{ getAuthorInitial(u.name) }}</span>
                            </span>
                            <span class="settings-contact-row__body">
                                <strong>@{{ u.name }}</strong>
                                <small>@{{ u.username ? '@'+u.username : '#'+u.id }}</small>
                            </span>
                            <button class="settings-ghost-btn danger" @click="unblockUserById(u.id)">فك الحظر</button>
                        </div>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">الجلسات النشطة</span>
                        <strong>@{{ settingsSessionsList.length }}</strong>
                        <button class="settings-ghost-btn" style="margin-right:auto;padding:2px 8px;font-size:12px;" @click="loadActiveSessions()" :disabled="settingsSessionsLoading">
                            <i :class="settingsSessionsLoading ? 'ri-loader-4-line spin' : 'ri-refresh-line'"></i>
                        </button>
                    </div>

                    <div class="settings-list-empty" v-if="settingsSessionsLoading"><i class="ri-loader-4-line spin"></i> جارٍ تحميل الجلسات...</div>
                    <div class="settings-list-empty" v-else-if="!settingsSessionsList.length">لا توجد جلسات نشطة أخرى.</div>

                    <div class="settings-contact-list" v-else>
                        <div class="settings-contact-row" v-for="s in settingsSessionsList" :key="'sess-'+s.id"
                             :class="{ 'session-current': s.is_current }">
                            <span class="settings-contact-row__avatar session" :class="{ current: s.is_current }">
                                <i :class="parseUserAgent(s.user_agent).icon"></i>
                            </span>
                            <span class="settings-contact-row__body">
                                <strong>
                                    @{{ s.is_current ? 'هذا الجهاز' : parseUserAgent(s.user_agent).name }}
                                    <span v-if="s.is_current" class="session-current-badge">حالية</span>
                                </strong>
                                <small dir="ltr">@{{ s.ip_address }} · @{{ formatLastActivity(s.last_activity) }}</small>
                            </span>
                            <button v-if="!s.is_current" class="settings-ghost-btn danger" @click="terminateSessionById(s.id)">إنهاء</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'chats'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>المحادثات والمجلدات</h4>
            </div>

            <div class="settings-drawer__body">
                <div>
                    <div class="ve-label">الثيم الافتراضي</div>
                    <div class="settings-theme-grid">
                        <button v-for="th in chatThemeDefs" :key="'sth-'+th.id" class="settings-theme-card" :class="{active: settingsChats.defaultTheme===th.id}" @click="settingsChats.defaultTheme=th.id; applyChatThemeVars(); saveMessagingSettings();">
                            <span class="settings-theme-card__preview" :style="{background: th.id ? (th.wp >= 0 ? wallpapers[th.wp] : (isDarkMode ? th.vars['--th-bubble-tint'] : (th.varsLight['--th-bubble-tint'] || th.vars['--th-bubble-tint']))) : 'radial-gradient(circle at top right, color-mix(in srgb, var(--gold) 36%, transparent), transparent 55%), linear-gradient(135deg, var(--panel-2), var(--panel))'}">
                                <span class="settings-theme-card__bubble outgoing" :style="{background: th.id ? (isDarkMode ? th.vars['--th-accent'] : (th.varsLight['--th-accent'] || th.vars['--th-accent'])) : 'var(--gold)'}"></span>
                                <span class="settings-theme-card__bubble incoming"></span>
                            </span>
                            <strong>@{{ th.name }}</strong>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="ve-label">الخلفية الافتراضية</div>
                    <div class="wallpaper-grid settings-wallpaper-grid">
                        <div v-for="(wp, idx) in wallpapers" :key="'swp-'+idx" class="wallpaper-item" :class="{active: activeWallpaper===idx}" :style="{background: wp}" @click="applyWallpaper(idx); saveMessagingSettings();"></div>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>الإرسال بزر Enter</strong>
                            <small>عند التعطيل يصبح Enter سطرًا جديدًا وCtrl+Enter للإرسال</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.sendWithEnter}" @click="toggleSendWithEnter"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>الوضع الليلي التلقائي</strong>
                            <small>اتباع مظهر النظام عندما يكون ذلك متاحًا</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.autoNightMode}" @click="toggleAutoNightMode"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>إظهار وسم المجلد</strong>
                            <small>عرض اسم المجلد بجانب المحادثة في القائمة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.showFolderTags}" @click="settingsChats.showFolderTags=!settingsChats.showFolderTags; saveMessagingSettings();"></button>
                    </div>
                </div>

                <div>
                    <div class="ve-label">عائلة الخط</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" v-for="f in [{id:'default',name:'افتراضي'},{id:'serif',name:'Serif'},{id:'mono',name:'Monospace'},{id:'rounded',name:'مدوّر'}]" :key="'font-'+f.id" :class="{selected: settingsChats.fontFamily===f.id}" @click="settingsChats.fontFamily=f.id; applyChatFont(); saveMessagingSettings();"><div class="sp-radio-dot"></div><span>@{{ f.name }}</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">النقر المزدوج على الرسائل</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsChats.doubleClickAction==='reply'}" @click="settingsChats.doubleClickAction='reply'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>رد</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsChats.doubleClickAction==='react'}" @click="settingsChats.doubleClickAction='react'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>تفاعل ❤️</span></div>
                    </div>
                </div>

                <div>
                    <div class="ve-label">موضع تبويبات القائمة</div>
                    <div class="sp-radio-group">
                        <div class="sp-radio-item" :class="{selected: settingsChats.tabsPosition==='left'}" @click="settingsChats.tabsPosition='left'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>يسار</span></div>
                        <div class="sp-radio-item" :class="{selected: settingsChats.tabsPosition==='top'}" @click="settingsChats.tabsPosition='top'; saveMessagingSettings();"><div class="sp-radio-dot"></div><span>أعلى</span></div>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">مجلدات المحادثات</span>
                        <strong>@{{ settingsFoldersList.length }}</strong>
                    </div>

                    <div class="settings-inline-form">
                        <input class="sp-input" v-model="settingsFolderDraft2.name" placeholder="اسم مجلد جديد" maxlength="60">
                        <button class="sp-save-btn settings-inline-form__button" @click="saveSettingsFolder">إضافة</button>
                    </div>

                    <div class="settings-list-empty" v-if="settingsFoldersLoading">جارٍ تحميل المجلدات...</div>
                    <div class="settings-list-empty" v-else-if="!settingsFoldersList.length">لا توجد مجلدات مخصّصة بعد.</div>

                    <div class="settings-folder-list" v-else>
                        <div class="settings-folder-card" v-for="f in settingsFoldersList" :key="'fold-'+f.id">
                            <button class="settings-folder-card__head" @click="toggleFolderChatPicker(f)">
                                <span class="settings-folder-card__icon"><i :class="f.icon || 'ri-folder-3-line'"></i></span>
                                <span class="settings-folder-card__body">
                                    <strong>@{{ f.name }}</strong>
                                    <small>@{{ (f.include_ids || []).length }} محادثة محددة</small>
                                </span>
                                <i class="ri-arrow-down-s-line settings-folder-card__chevron"></i>
                            </button>

                            <div class="settings-folder-card__picker" v-if="folderChatPickerId === f.id">
                                <label v-for="c in contacts" :key="'fcp-'+f.id+'-'+c.id" class="settings-folder-card__option">
                                    <span>@{{ c.name }}</span>
                                    <input type="checkbox" :checked="(f.include_ids || []).includes(Number(c.id))" @change="toggleFolderChatInclude(f, c.id)">
                                </label>
                            </div>

                            <div class="settings-folder-card__foot">
                                <button class="settings-ghost-btn danger" @click="deleteSettingsFolder(f.id)">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'media'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الوسائط والتنزيل</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تحميل الصور تلقائيًا</strong>
                            <small>تنزيل الصور مباشرة عند فتح المحادثات</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsMedia.autoDownloadImages}" @click="settingsMedia.autoDownloadImages=!settingsMedia.autoDownloadImages; saveMessagingSettings();"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تحميل الفيديو تلقائيًا</strong>
                            <small>مناسب للشبكات السريعة أو الاستخدام المتكرر للفيديو</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsMedia.autoDownloadVideos}" @click="settingsMedia.autoDownloadVideos=!settingsMedia.autoDownloadVideos; saveMessagingSettings();"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تحميل الملفات تلقائيًا</strong>
                            <small>تنزيل المستندات والملفات المرفقة بدون خطوة إضافية</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsMedia.autoDownloadFiles}" @click="settingsMedia.autoDownloadFiles=!settingsMedia.autoDownloadFiles; saveMessagingSettings();"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>Wi-Fi فقط</strong>
                            <small>تقييد التحميلات التلقائية على الشبكات اللاسلكية فقط</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsMedia.wifiOnly}" @click="settingsMedia.wifiOnly=!settingsMedia.wifiOnly; saveMessagingSettings();"></button>
                    </div>
                </div>

                <div>
                    <div class="ve-label">الجودة الافتراضية</div>
                    <div class="settings-quality-grid">
                        <button v-for="q in ['360p','480p','720p','1080p']" :key="'mq-'+q" class="settings-quality-card" :class="{active: settingsMedia.quality===q}" @click="settingsMedia.quality=q; saveMessagingSettings();">
                            <strong>@{{ q }}</strong>
                            <small>@{{ q === '1080p' ? 'أعلى جودة' : 'مناسب للاستخدام اليومي' }}</small>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'mic'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الأجهزة الصوتية</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">الميكروفون الحالي</span>
                        <strong>@{{ settingsMicDevices.find(d => d.deviceId === settingsMicDeviceId)?.label || 'غير محدد' }}</strong>
                    </div>

                    <div class="settings-list-empty" v-if="!settingsMicDevices.length">اضغط تحديث ليطلب المتصفح الإذن ويعرض الأجهزة المتاحة.</div>

                    <div class="settings-mic-list" v-else>
                        <button class="settings-mic-card" :class="{active: settingsMicDeviceId===d.deviceId}" v-for="d in settingsMicDevices" :key="d.deviceId" @click="selectMicDevice(d.deviceId)">
                            <span class="settings-mic-card__body">
                                <strong>@{{ d.label || 'ميكروفون' }}</strong>
                                <small>@{{ settingsMicDeviceId===d.deviceId ? 'محدد حاليًا' : 'اضغط للاختيار' }}</small>
                            </span>
                            <i v-if="settingsMicDeviceId===d.deviceId" class="ri-check-line"></i>
                        </button>
                    </div>

                    <button class="sp-save-btn" @click="loadMicDevices">تحديث قائمة الأجهزة</button>

                    <div style="margin-top:12px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <button class="sp-save-btn" :style="{background: micTestActive ? 'rgba(255,59,48,0.18)' : undefined}" @click="micTestActive ? stopMicTest() : startMicTest()">
                                <i :class="micTestActive ? 'ri-stop-circle-line' : 'ri-mic-line'"></i>
                                @{{ micTestActive ? 'إيقاف الاختبار' : 'اختبار الميكروفون' }}
                            </button>
                        </div>
                        <div v-if="micTestActive" style="display:flex;align-items:flex-end;gap:3px;height:48px;padding:4px 0;">
                            <div v-for="i in 28" :key="i" class="mic-bar" :ref="'mb'+i"
                                 style="width:6px;border-radius:3px;background:var(--gold);transition:height .05s;flex-shrink:0;min-height:3px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'calls'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>المكالمات</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">نغمة الرنين</span>
                        <select v-model="callSettings.ringtone" @change="saveCallSettings" style="min-width:130px;">
                            <option v-for="t in toneList" :key="t.id" :value="t.id">@{{ t.label }}</option>
                        </select>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">حالة إذن الميكروفون</span>
                        <strong :style="{color: micPermissionState==='granted' ? 'var(--ok)' : (micPermissionState==='denied' ? '#e74c3c' : 'var(--muted)')}">
                            @{{ micPermissionState==='granted' ? 'مسموح' : (micPermissionState==='denied' ? 'غير مسموح' : 'غير محدد') }}
                        </strong>
                    </div>
                    <div class="settings-list-empty" v-if="micPermissionState!=='granted'">اضغط لطلب إذن الوصول للميكروفون من المتصفح.</div>
                    <div class="settings-mic-list" v-else>
                        <button class="settings-mic-card" :class="{active: callSettings.micDeviceId===d.deviceId}" v-for="d in callAudioInputs" :key="'cm-'+d.deviceId" @click="callSettings.micDeviceId=d.deviceId; saveCallSettings()">
                            <span class="settings-mic-card__body">
                                <strong>@{{ d.label || 'ميكروفون' }}</strong>
                                <small>@{{ callSettings.micDeviceId===d.deviceId ? 'محدد حاليًا' : 'اضغط للاختيار' }}</small>
                            </span>
                            <i v-if="callSettings.micDeviceId===d.deviceId" class="ri-check-line"></i>
                        </button>
                    </div>
                    <button class="sp-save-btn" @click="requestCallMediaPermission('microphone')">@{{ micPermissionState==='granted' ? 'تحديث قائمة الأجهزة' : 'طلب إذن الميكروفون' }}</button>
                </div>

                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">حالة إذن الكاميرا</span>
                        <strong :style="{color: cameraPermissionState==='granted' ? 'var(--ok)' : (cameraPermissionState==='denied' ? '#e74c3c' : 'var(--muted)')}">
                            @{{ cameraPermissionState==='granted' ? 'مسموح' : (cameraPermissionState==='denied' ? 'غير مسموح' : 'غير محدد') }}
                        </strong>
                    </div>
                    <div class="settings-list-empty" v-if="cameraPermissionState!=='granted'">اضغط لطلب إذن الوصول للكاميرا من المتصفح.</div>
                    <div class="settings-mic-list" v-else-if="callVideoInputs.length">
                        <button class="settings-mic-card" :class="{active: callSettings.cameraDeviceId===d.deviceId}" v-for="d in callVideoInputs" :key="'cc-'+d.deviceId" @click="callSettings.cameraDeviceId=d.deviceId; saveCallSettings()">
                            <span class="settings-mic-card__body">
                                <strong>@{{ d.label || 'كاميرا' }}</strong>
                                <small>@{{ callSettings.cameraDeviceId===d.deviceId ? 'محددة حاليًا' : 'اضغط للاختيار' }}</small>
                            </span>
                            <i v-if="callSettings.cameraDeviceId===d.deviceId" class="ri-check-line"></i>
                        </button>
                    </div>
                    <button class="sp-save-btn" @click="requestCallMediaPermission('camera')">@{{ cameraPermissionState==='granted' ? 'تحديث قائمة الأجهزة' : 'طلب إذن الكاميرا' }}</button>
                </div>

                <div class="settings-group-card" v-if="callAudioOutputs.length">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">مكبر الصوت</span>
                        <select v-model="callSettings.speakerDeviceId" @change="saveCallSettings" style="min-width:130px;">
                            <option value="">الافتراضي</option>
                            <option v-for="d in callAudioOutputs" :key="d.deviceId" :value="d.deviceId">@{{ d.label || 'مكبر صوت' }}</option>
                        </select>
                    </div>
                </div>

                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>وضع البيانات المنخفضة</strong>
                            <small>تقليل دقة الفيديو في المكالمات لتوفير الإنترنت</small>
                        </div>
                        <button class="sp-toggle" :class="{on: callSettings.lowDataMode}" @click="callSettings.lowDataMode=!callSettings.lowDataMode; saveCallSettings()"></button>
                    </div>
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>عدم الإزعاج</strong>
                            <small>رفض كل المكالمات الواردة تلقائياً دون رنين</small>
                        </div>
                        <button class="sp-toggle" :class="{on: callSettings.doNotDisturb}" @click="callSettings.doNotDisturb=!callSettings.doNotDisturb; saveCallSettings()"></button>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'battery'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>الأداء والحركة</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>تقليل الحركة والتأثيرات</strong>
                            <small>تعطيل معظم الانتقالات لتحسين الأداء وتقليل الإجهاد البصري</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.reduceMotion}" @click="toggleReduceMotion"></button>
                    </div>
                    <p class="settings-note">هذا الخيار يطبق فورًا على الواجهة الحالية ويؤثر على الرسوم الانتقالية فقط دون المساس بالرسائل أو البيانات.</p>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'advanced'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>متقدم</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>المدقق الإملائي للمتصفح</strong>
                            <small>تفعيل تدقيق النصوص داخل حقل كتابة الرسائل</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.spellcheckEnabled}" @click="settingsChats.spellcheckEnabled=!settingsChats.spellcheckEnabled; saveMessagingSettings();"></button>
                    </div>

                    <div class="settings-inline-card">
                        <div class="settings-inline-card__copy">
                            <strong>إظهار عدد غير المقروء في عنوان التبويب</strong>
                            <small>مفيد عند العمل على عدة تبويبات أو أثناء تصغير الصفحة</small>
                        </div>
                        <button class="sp-toggle" :class="{on: settingsChats.showUnreadInTitle}" @click="settingsChats.showUnreadInTitle=!settingsChats.showUnreadInTitle; saveMessagingSettings(); updateDocumentTitle();"></button>
                    </div>
                </div>

                <div class="settings-group-card">
                    <button class="settings-action-row" @click="exportMyData">
                        <span class="settings-action-row__icon"><i class="ri-download-2-line"></i></span>
                        <span class="settings-action-row__body">
                            <strong>تصدير بياناتي</strong>
                            <small>تنزيل رسائلك وبيانات الحساب بصيغة JSON</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>

                    <button class="settings-action-row danger" @click="clearLocalCache">
                        <span class="settings-action-row__icon"><i class="ri-delete-bin-line"></i></span>
                        <span class="settings-action-row__body">
                            <strong>مسح التخزين المحلي</strong>
                            <small>يحذف الكاش المحلي والثيمات المحفوظة دون حذف الرسائل من الخادم</small>
                        </span>
                        <i class="ri-arrow-left-s-line settings-nav-row__chevron"></i>
                    </button>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'language'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>اللغة</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-language-grid">
                    <button class="settings-language-card" :class="{active: settingsLanguageChoice==='ar'}" @click="changeSettingsLanguage('ar')">
                        <strong>العربية</strong>
                        <small>الواجهة كاملة من اليمين إلى اليسار</small>
                    </button>
                    <button class="settings-language-card" :class="{active: settingsLanguageChoice==='en'}" @click="changeSettingsLanguage('en')">
                        <strong>English</strong>
                        <small>UI labels and saved preference for your account</small>
                    </button>
                </div>
            </div>
        </template>

        <template v-if="settingsSection === 'about'">
            <div class="settings-subhead">
                <button class="h-icon-btn" @click="settingsSection='main_menu'"><i class="ri-arrow-right-line"></i></button>
                <h4>عن التطبيق</h4>
            </div>

            <div class="settings-drawer__body">
                <div class="settings-group-card">
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">الإصدار</span>
                        <strong>3.0.0</strong>
                    </div>
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">المنصة</span>
                        <strong>Laravel + Vue 3</strong>
                    </div>
                    <div class="settings-stat-row">
                        <span class="settings-stat-row__label">نطاق العمل</span>
                        <strong>إعدادات المراسلة مرتبطة بالحساب الحالي وتُحفظ مركزيًا</strong>
                    </div>
                </div>

                <div class="settings-group-card">
                    <p class="settings-note">تم تكييف هذه اللوحة لتخدم تطبيق الويب الحالي بكفاءة، لذلك جرى استبعاد أي إعداد مكتبي لا يملك سلوكًا حقيقيًا في البيئة الحالية.</p>
                </div>
            </div>
        </template>
    </section>
</div>
