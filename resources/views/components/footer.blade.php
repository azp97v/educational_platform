<!-- 
    ═════════════════════════════════════════════════════════════════════════
    FOOTER COMPONENT - تذييل الصفحة
    ═════════════════════════════════════════════════════════════════════════
    
    المسؤوليات:
    ✓ عرض معلومات الاتصال والتقرير
    ✓ الروابط السريعة
    ✓ حقوق النشر
-->

<footer class="app-footer">
    <div class="footer-container">
        <!-- عن المنصة -->
        <div class="footer-section">
            <h4>عن منصة إجلال</h4>
            <p>منصة تعليمية ذكية توفر تجربة تعليمية متقدمة مع أدوات تقييم وتلعيب مبتكرة.</p>
        </div>
        
        <!-- روابط سريعة -->
        <div class="footer-section">
            <h4>روابط سريعة</h4>
            <ul>
                <li><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                <li><a href="{{ route('register') }}">التسجيل</a></li>
                <li><a href="#">سياسة الخصوصية</a></li>
                <li><a href="#">شروط الاستخدام</a></li>
            </ul>
        </div>
        
        <!-- التواصل -->
        <div class="footer-section">
            <h4>التواصل</h4>
            <ul>
                <li><i class="ri-mail-line"></i> <a href="mailto:info@iglal.com">info@iglal.com</a></li>
                <li><i class="ri-phone-line"></i> <a href="tel:+966123456789">+966 12 345 6789</a></li>
                <li><i class="ri-map-pin-line"></i> الرياض، المملكة العربية السعودية</li>
            </ul>
        </div>
    </div>
    
    <!-- القسم السفلي -->
    <div class="footer-bottom">
        <p>&copy; 2026 منصة إجلال التعليمية. جميع الحقوق محفوظة.</p>
        <div class="social-links">
            <a href="#" title="تويتر"><i class="ri-twitter-line"></i></a>
            <a href="#" title="فيسبوك"><i class="ri-facebook-box-line"></i></a>
            <a href="#" title="إنستجرام"><i class="ri-instagram-line"></i></a>
            <a href="#" title="لينكد إن"><i class="ri-linkedin-box-line"></i></a>
        </div>
    </div>
</footer>

<style>
    /**
     * Footer Styles
     */
    .app-footer {
        background-color: var(--bg-secondary);
        border-top: 1px solid var(--bg-tertiary);
        padding: var(--space-3xl) var(--space-2xl);
        margin-top: var(--space-4xl);
        color: var(--text-secondary);
    }
    
    .footer-container {
        max-width: var(--max-content-width);
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-2xl);
        margin-bottom: var(--space-2xl);
        padding-bottom: var(--space-2xl);
        border-bottom: 1px solid var(--bg-tertiary);
    }
    
    .footer-section h4 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-lg);
    }
    
    .footer-section p {
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 0;
        color: var(--text-secondary);
    }
    
    .footer-section ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
    }
    
    .footer-section li {
        display: flex;
        align-items: center;
        gap: var(--space-md);
        font-size: 13px;
    }
    
    .footer-section a {
        color: var(--color-gold);
        text-decoration: none;
        transition: var(--transition-fast);
    }
    
    .footer-section a:hover {
        color: var(--color-gold-dark);
        text-decoration: underline;
    }
    
    .footer-section i {
        font-size: 16px;
        color: var(--color-gold);
        flex-shrink: 0;
    }
    
    .footer-bottom {
        max-width: var(--max-content-width);
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: var(--space-lg);
    }
    
    .footer-bottom p {
        font-size: 12px;
        margin: 0;
        color: var(--text-tertiary);
    }
    
    .social-links {
        display: flex;
        gap: var(--space-md);
    }
    
    .social-links a {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: var(--bg-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-gold);
        font-size: 16px;
        transition: var(--transition-fast);
    }
    
    .social-links a:hover {
        background-color: var(--color-gold);
        color: #fff;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .app-footer {
            padding: var(--space-2xl) var(--space-lg);
        }
        
        .footer-container {
            grid-template-columns: 1fr;
            gap: var(--space-xl);
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
