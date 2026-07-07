@extends('layouts.app-unified')

@section('title', 'إدارة أسئلة الاختبار - لوحة المعلم')

@section('styles')
<style>
        :root { --sidebar-w: 260px; --topbar-h: 70px; }
        .sidebar { width: var(--sidebar-w); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
          font-family: 'Tajawal', sans-serif;
          color: var(--text-primary);
          transition: var(--transition);
        }

        .main {
          margin-right: calc(var(--sidebar-w) + 22px);
          background: radial-gradient(circle at top left, rgba(255,214,122,0.16), transparent 22%),
                      linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%);
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 24px; }

        .header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 32px;
        }

        .header-left h1 {
          font-size: 28px;
          font-weight: 900;
          margin-bottom: 8px;
          background: linear-gradient(135deg, var(--text-primary), var(--gold));
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
        }

        .header-left p {
          color: var(--text-secondary);
          font-size: 14px;
        }

        .btn {
          padding: 14px 28px;
          border: none;
          border-radius: var(--radius-md);
          font-family: 'Tajawal', sans-serif;
          font-size: 15px;
          font-weight: 700;
          cursor: pointer;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 8px;
          position: relative;
          overflow: hidden;
          min-height: 44px;
          letter-spacing: 0.3px;
        }

        .btn::before {
          content: '';
          position: absolute;
          top: 0;
          right: 100%;
          width: 100%;
          height: 100%;
          background: rgba(255,255,255,0.1);
          transition: var(--transition);
          z-index: -1;
        }

        .btn:hover::before { right: 0; }

        .btn-primary {
          background: linear-gradient(135deg, var(--gold), var(--gold-dark));
          color: white;
          box-shadow: 0 4px 16px rgba(196,150,58,0.25);
          font-weight: 700;
          border: none;
        }

        .btn-primary:hover {
          transform: translateY(-3px);
          box-shadow: 0 8px 24px rgba(196,150,58,0.35);
        }

        .btn-primary:active {
          transform: translateY(-1px);
        }

        .btn-secondary {
          background: transparent;
          color: var(--text-secondary);
          border: 2px solid rgba(196,150,58,0.4);
          font-weight: 700;
        }

        .btn-secondary:hover {
          background: var(--gold-light);
          color: var(--gold);
          border-color: var(--gold);
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(196,150,58,0.2);
        }

        .alert {
          padding: 16px;
          border-radius: var(--radius-lg);
          margin-bottom: 24px;
          display: flex;
          align-items: flex-start;
          gap: 12px;
          border-left: 4px solid;
          animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
          from { opacity: 0; transform: translateX(20px); }
          to { opacity: 1; transform: translateX(0); }
        }

        .alert-success {
          background: rgba(52,199,89,0.1);
          border-left-color: #34C759;
          color: var(--text-primary);
        }

        .questions-list {
          display: flex;
          flex-direction: column;
          gap: 16px;
        }

        .question-card {
          background: var(--card-bg);
          border-radius: var(--radius-lg);
          padding: 20px;
          box-shadow: var(--shadow);
          border-left: 4px solid var(--gold);
          transition: var(--transition);
        }

        .question-card:hover {
          box-shadow: var(--shadow-hover);
          transform: translateY(-2px);
        }

        .question-header {
          display: flex;
          justify-content: space-between;
          align-items: start;
          margin-bottom: 12px;
        }

        .question-title {
          font-size: 16px;
          font-weight: 700;
          color: var(--text-primary);
        }

        .question-type {
          display: inline-block;
          padding: 4px 12px;
          background: var(--gold-light);
          color: var(--gold);
          border-radius: 20px;
          font-size: 12px;
          font-weight: 600;
        }

        .question-text {
          color: var(--text-secondary);
          font-size: 14px;
          margin-bottom: 12px;
          line-height: 1.6;
        }

        .answers {
          margin: 16px 0;
          padding: 12px;
          background: var(--bg);
          border-radius: var(--radius-md);
        }

        .answer-item {
          padding: 8px 0;
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 14px;
          color: var(--text-secondary);
        }

        .answer-correct {
          color: var(--success);
          font-weight: 600;
        }

        .question-actions {
          display: flex;
          gap: 8px;
          margin-top: 12px;
        }

        .action-btn {
          padding: 8px 16px;
          border: none;
          background: transparent;
          color: var(--text-secondary);
          border: 1px solid var(--border);
          border-radius: var(--radius-md);
          font-size: 13px;
          font-weight: 600;
          cursor: pointer;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 6px;
          font-family: 'Tajawal', sans-serif;
        }

        .action-btn:hover {
          background: var(--gold-light);
          color: var(--gold);
          border-color: var(--gold);
        }

        .empty-state {
          text-align: center;
          padding: 60px 20px;
          color: var(--text-secondary);
        }

        .empty-state i {
          font-size: 64px;
          margin-bottom: 20px;
          display: block;
          opacity: 0.5;
        }

        .empty-state h3 {
          margin-bottom: 10px;
        }

        .empty-state p {
          font-size: 14px;
          margin-bottom: 20px;
        }

        .breadcrumb {
          display: flex;
          gap: 8px;
          margin-bottom: 24px;
          align-items: center;
          font-size: 14px;
        }

        .breadcrumb a {
          color: var(--gold);
          text-decoration: none;
          transition: var(--transition);
        }

        .breadcrumb a:hover {
          text-decoration: underline;
        }

        .breadcrumb-separator {
          color: var(--text-muted);
        }

        /* Modal Styles */
        .modal {
          display: none;
          position: fixed;
          z-index: 1000;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0,0,0,0.5);
          animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }

        .modal.show { display: flex; }

        .modal-content {
          background: var(--card-bg);
          margin: auto;
          padding: 36px;
          border-radius: var(--radius-lg);
          width: 90%;
          max-width: 700px;
          max-height: 90vh;
          overflow-y: auto;
          animation: slideUp 0.3s ease;
          box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        @keyframes slideUp {
          from { transform: translateY(50px); opacity: 0; }
          to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 28px;
          border-bottom: 2px solid rgba(196,150,58,0.3);
          padding-bottom: 20px;
        }

        .modal-header h2 {
          font-size: 22px;
          font-weight: 800;
          letter-spacing: 0.5px;
        }

        .close-btn {
          background: none;
          border: none;
          font-size: 32px;
          cursor: pointer;
          color: var(--text-secondary);
          transition: var(--transition);
          padding: 4px;
          min-height: 40px;
          width: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: var(--radius-md);
        }

        .close-btn:hover {
          color: var(--gold);
          background: var(--gold-light);
          transform: rotate(90deg) scale(1.1);
        }

        .form-group {
          margin-bottom: 24px;
          animation: fadeInUp 0.5s ease-out;
        }

        #answersSection {
          animation: scaleIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
          transform-origin: top;
        }

        #answersSection.removing {
          animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) reverse;
        }

        .form-group select {
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23C4963A' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
          background-repeat: no-repeat;
          background-position: left 16px center;
          padding-left: 40px;
          cursor: pointer;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group select:hover {
          border-color: var(--gold);
          box-shadow: 0 2px 8px rgba(196, 150, 58, 0.1);
        }

        .form-group label {
          display: block;
          margin-bottom: 12px;
          font-weight: 700;
          color: var(--text-primary);
          font-size: 15px;
          letter-spacing: 0.3px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
          width: 100%;
          padding: 14px 16px;
          border: 1.5px solid var(--border);
          border-radius: var(--radius-md);
          font-family: 'Tajawal', sans-serif;
          font-size: 15px;
          color: var(--text-primary);
          background: linear-gradient(135deg, var(--bg), rgba(196,150,58,0.02));
          transition: var(--transition);
          appearance: none;
          box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
        }

        .form-group select {
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23C4963A' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
          background-repeat: no-repeat;
          background-position: left 16px center;
          padding-left: 40px;
          cursor: pointer;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group select:hover {
          border-color: var(--gold);
          box-shadow: 0 2px 8px rgba(196, 150, 58, 0.1), inset 0 2px 4px rgba(0,0,0,0.15);
        }

        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes scaleIn {
          from {
            opacity: 0;
            transform: scale(0.95);
          }
          to {
            opacity: 1;
            transform: scale(1);
          }
        }

        @keyframes fadeOut {
          from {
            opacity: 1;
            transform: translateX(0);
          }
          to {
            opacity: 0;
            transform: translateX(-20px);
          }
        }

        .form-group select option {
          background: var(--card-bg);
          color: var(--text-primary);
          padding: 12px;
          font-size: 15px;
          font-weight: 500;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
          outline: none;
          border-color: var(--gold);
          box-shadow: 0 0 0 3px rgba(196,150,58,0.15), inset 0 0 0 1px rgba(196,150,58,0.3), inset 0 2px 4px rgba(0,0,0,0.15);
          background: linear-gradient(135deg, var(--card-bg), rgba(196,150,58,0.05));
        }

        .form-group textarea {
          resize: vertical;
          min-height: 120px;
          line-height: 1.6;
        }

        .answers-container {
          margin-top: 16px;
          padding: 20px;
          background: var(--gold-light);
          border-radius: var(--radius-md);
          border-left: 4px solid var(--gold);
          animation: fadeInUp 0.4s ease-out;
        }

        .answer-field {
          display: flex;
          gap: 12px;
          margin-bottom: 16px;
          align-items: flex-end;
          padding: 14px 16px;
          background: var(--card-bg);
          border-radius: var(--radius-lg);
          border: 1px solid rgba(196, 150, 58, 0.2);
          animation: fadeInUp 0.5s ease-out backwards;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .answer-field:nth-child(1) { animation-delay: 0.05s; }
        .answer-field:nth-child(2) { animation-delay: 0.1s; }
        .answer-field:nth-child(3) { animation-delay: 0.15s; }

        .answer-field:hover {
          box-shadow: 0 4px 16px rgba(196, 150, 58, 0.15);
          border-color: rgba(196, 150, 58, 0.4);
          transform: translateY(-2px);
        }

        .answer-field:last-child {
          margin-bottom: 0;
        }

        .answer-field input {
          flex: 1;
          padding: 12px 14px;
          font-size: 14px;
        }

        .answer-field label {
          display: flex;
          align-items: center;
          gap: 8px;
          margin: 0;
          font-weight: 600;
          white-space: nowrap;
          margin-bottom: 0;
          cursor: pointer;
          font-size: 14px;
        }

        .answer-field input[type="checkbox"] {
          width: 20px;
          height: 20px;
          cursor: pointer;
          accent-color: var(--gold);
        }

        .answer-field .delete-btn {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, rgba(255, 59, 48, 0.1), rgba(255, 59, 48, 0.05));
          border: 1.5px solid rgba(255, 59, 48, 0.3);
          border-radius: var(--radius-md);
          cursor: pointer;
          color: #FF3B30;
          font-size: 18px;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          font-weight: 700;
          min-height: 40px;
          padding: 0;
          background-color: rgba(255, 59, 48, 0.08);
        }

        .answer-field .delete-btn:hover {
          background: linear-gradient(135deg, rgba(255, 59, 48, 0.2), rgba(255, 59, 48, 0.15));
          border-color: #FF3B30;
          color: white;
          background-color: #FF3B30;
          transform: scale(1.1) rotate(5deg);
          box-shadow: 0 4px 12px rgba(255, 59, 48, 0.3);
        }

        .answer-field .delete-btn:active {
          transform: scale(0.95);
        }

        .add-answer-btn {
          padding: 12px 20px;
          background: transparent;
          border: 2px solid var(--gold);
          color: var(--gold);
          border-radius: var(--radius-md);
          cursor: pointer;
          font-family: 'Tajawal', sans-serif;
          font-weight: 700;
          font-size: 15px;
          transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
          width: 100%;
          margin-top: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          min-height: 44px;
        }

        .add-answer-btn:hover {
          background: var(--gold);
          color: white;
          transform: translateY(-3px);
          box-shadow: 0 6px 20px rgba(196,150,58,0.3);
        }

        .add-answer-btn:active {
          transform: translateY(-1px);
        }

        .modal-footer {
          display: flex;
          gap: 12px;
          justify-content: flex-end;
          margin-top: 32px;
          padding-top: 20px;
          border-top: 2px solid rgba(196,150,58,0.3);
          flex-direction: row-reverse;
        }

        .modal-footer .btn {
          margin: 0;
          flex: 0 0 auto;
          min-width: 120px;
          justify-content: center;
        }

        .error-text {
          display: block;
          color: #FF3B30;
          font-size: 14px;
          margin-top: 6px;
          font-weight: 600;
          letter-spacing: 0.2px;
        }

        .form-group input:invalid,
        .form-group textarea:invalid,
        .form-group select:invalid {
          border-color: #FF3B30;
          border-width: 2px;
          background-color: rgba(255, 59, 48, 0.03);
        }

        .select-container {
          position: relative;
        }

        #answersSection > label {
          font-size: 16px;
          font-weight: 800;
          color: var(--gold);
          margin-bottom: 16px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        /* ===== BEAUTIFUL ALERT MODAL ===== */
        .modal-overlay {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.6);
          z-index: 2000;
          animation: fadeInOverlay 0.3s ease;
          backdrop-filter: blur(4px);
        }

        @keyframes fadeInOverlay {
          from { opacity: 0; }
          to { opacity: 1; }
        }

        .modal-overlay.show {
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .alert-modal {
          background: var(--card-bg);
          border-radius: 20px;
          padding: 40px;
          max-width: 500px;
          width: 90%;
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
          animation: slideInAlert 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
          position: relative;
          overflow: hidden;
        }

        .alert-modal::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, var(--gold), var(--danger));
        }

        @keyframes slideInAlert {
          from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
          }
          to {
            opacity: 1;
            transform: translateY(0) scale(1);
          }
        }

        .alert-modal.warning {
          border: 2px solid #FF9500;
        }

        .alert-modal.warning::before {
          background: linear-gradient(90deg, #FF9500, #FF6B6B);
        }

        .alert-header {
          display: flex;
          align-items: flex-start;
          gap: 16px;
          margin-bottom: 20px;
        }

        .alert-icon {
          width: 50px;
          height: 50px;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          flex-shrink: 0;
          animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .alert-icon.warning {
          background: rgba(255, 149, 0, 0.15);
          color: #FF9500;
        }

        @keyframes iconBounce {
          0% {
            transform: scale(0) rotate(-20deg);
          }
          50% {
            transform: scale(1.1);
          }
          100% {
            transform: scale(1) rotate(0);
          }
        }

        .alert-content {
          flex: 1;
        }

        .alert-content h3 {
          font-size: 18px;
          font-weight: 800;
          color: var(--text-primary);
          margin: 0 0 8px 0;
          letter-spacing: 0.3px;
        }

        .alert-content p {
          color: var(--text-secondary);
          font-size: 14px;
          line-height: 1.6;
          margin: 0 0 16px 0;
        }

        .alert-suggestion {
          background: var(--gold-light);
          border-radius: 10px;
          padding: 12px 14px;
          margin-bottom: 16px;
          border-right: 3px solid var(--gold);
          font-size: 13px;
          color: var(--text-primary);
          line-height: 1.5;
        }

        .alert-suggestion strong {
          color: var(--gold);
          display: block;
          margin-bottom: 4px;
        }

        .alert-actions {
          display: flex;
          gap: 10px;
          justify-content: flex-end;
        }

        .alert-btn {
          padding: 10px 20px;
          border: none;
          border-radius: 10px;
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          font-weight: 600;
          cursor: pointer;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 6px;
        }

        .alert-btn-primary {
          background: linear-gradient(135deg, var(--gold), var(--gold-dark));
          color: white;
          box-shadow: 0 4px 12px rgba(196, 150, 58, 0.3);
        }

        .alert-btn-primary:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 16px rgba(196, 150, 58, 0.4);
        }

        .alert-btn-secondary {
          background: transparent;
          color: var(--text-secondary);
          border: 1px solid var(--border);
        }

        .alert-btn-secondary:hover {
          background: var(--bg);
          color: var(--text-primary);
        }

        .close-alert-btn {
          position: absolute;
          top: 16px;
          right: 16px;
          width: 32px;
          height: 32px;
          border: none;
          background: var(--bg);
          border-radius: 50%;
          color: var(--text-secondary);
          font-size: 18px;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: var(--transition);
        }

        .close-alert-btn:hover {
          background: var(--gold-light);
          color: var(--gold);
          transform: rotate(90deg);
        }

        /* ===== CONFIRM DELETE MODAL ===== */
        .confirm-modal {
          background: var(--card-bg);
          border-radius: 20px;
          padding: 40px;
          max-width: 500px;
          width: 90%;
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
          animation: slideInAlert 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
          position: relative;
          overflow: hidden;
          border: 2px solid #FF3B30;
        }

        .confirm-modal::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, #FF3B30, #FF6B6B);
        }

        .confirm-header {
          display: flex;
          align-items: flex-start;
          gap: 16px;
          margin-bottom: 20px;
        }

        .confirm-icon {
          width: 50px;
          height: 50px;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          flex-shrink: 0;
          animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
          background: rgba(255, 59, 48, 0.15);
          color: #FF3B30;
        }

        .confirm-content {
          flex: 1;
        }

        .confirm-content h3 {
          font-size: 18px;
          font-weight: 800;
          color: var(--text-primary);
          margin: 0 0 8px 0;
          letter-spacing: 0.3px;
        }

        .confirm-content p {
          color: var(--text-secondary);
          font-size: 14px;
          line-height: 1.6;
          margin: 0;
        }

        .confirm-message {
          background: rgba(255, 59, 48, 0.1);
          border-radius: 10px;
          padding: 12px 14px;
          margin-top: 16px;
          border-right: 3px solid #FF3B30;
          font-size: 13px;
          color: var(--text-primary);
          line-height: 1.5;
        }

        .confirm-message strong {
          color: #FF3B30;
          display: block;
          margin-bottom: 4px;
        }

        .confirm-actions {
          display: flex;
          gap: 10px;
          justify-content: flex-end;
          margin-top: 28px;
          padding-top: 20px;
          border-top: 1px solid var(--border);
        }

        .confirm-btn {
          padding: 10px 20px;
          border: none;
          border-radius: 10px;
          font-family: 'Tajawal', sans-serif;
          font-size: 14px;
          font-weight: 600;
          cursor: pointer;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 6px;
        }

        .confirm-btn-danger {
          background: linear-gradient(135deg, #FF3B30, #FF6B6B);
          color: white;
          box-shadow: 0 4px 12px rgba(255, 59, 48, 0.3);
        }

        .confirm-btn-danger:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 16px rgba(255, 59, 48, 0.4);
        }

        .confirm-btn-cancel {
          background: transparent;
          color: var(--text-secondary);
          border: 1px solid var(--border);
        }

        .confirm-btn-cancel:hover {
          background: var(--bg);
          color: var(--text-primary);
        }

        .close-confirm-btn {
          position: absolute;
          top: 16px;
          right: 16px;
          width: 32px;
          height: 32px;
          border: none;
          background: var(--bg);
          border-radius: 50%;
          color: var(--text-secondary);
          font-size: 18px;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: var(--transition);
        }

        .close-confirm-btn:hover {
          background: rgba(255, 59, 48, 0.15);
          color: #FF3B30;
          transform: rotate(90deg);
        }

        /* ===== BACK BUTTON STYLES ===== */
        .back-button-container {
          margin-bottom: 24px;
          display: flex;
          align-items: center;
          animation: slideInBack 0.5s cubic-bezier(0.4,0,0.2,1);
        }

        @keyframes slideInBack {
          from {
            opacity: 0;
            transform: translateX(-20px);
          }
          to {
            opacity: 1;
            transform: translateX(0);
          }
        }

        .back-button {
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 12px 20px;
          background: linear-gradient(135deg, var(--gold), var(--gold-dark));
          color: white;
          border: none;
          border-radius: var(--radius-lg);
          font-family: 'Tajawal', sans-serif;
          font-size: 15px;
          font-weight: 700;
          cursor: pointer;
          transition: var(--transition);
          text-decoration: none;
          box-shadow: 0 4px 16px rgba(196,150,58,0.25);
          letter-spacing: 0.3px;
          min-height: 44px;
          position: relative;
          overflow: hidden;
        }

        .back-button::before {
          content: '';
          position: absolute;
          top: 0;
          right: 0;
          width: 0;
          height: 100%;
          background: rgba(255,255,255,0.2);
          transition: var(--transition);
          z-index: -1;
        }

        .back-button:hover {
          transform: translateY(-3px);
          box-shadow: 0 8px 24px rgba(196,150,58,0.35);
        }

        .back-button:hover::before {
          right: 100%;
          width: 100%;
        }

        .back-button:active {
          transform: translateY(-1px);
        }

        .back-button i {
          font-size: 18px;
          transition: var(--transition);
        }

        .back-button:hover i {
          transform: translateX(-3px);
        }

        /* ===== HIDE SCROLLBAR ===== */
        ::-webkit-scrollbar {
          display: none;
        }

        body {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }

        @media (max-width: 1024px) {
          .main { margin-right: 72px !important; }
        }

        @media (max-width: 768px) {
          .main { margin-right: 0 !important; }
          .container { padding: 0 12px; }
          .questions-grid { grid-template-columns: 1fr !important; }
          .question-form { padding: 16px; }
        }

        @media (max-width: 480px) {
          .container { padding: 0 8px; }
        }
    </style>
@endsection

@section('content')
  <div class="container">
    <!-- Back Button -->
    <div class="back-button-container">
      <a href="{{ route('teacher.exams') }}" class="back-button">
        <i class="ri-arrow-left-line"></i>
        العودة إلى الاختبارات
      </a>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i> الرئيسية</a>
      <span class="breadcrumb-separator">/</span>
      <a href="{{ route('teacher.exams') }}">الاختبارات</a>
      <span class="breadcrumb-separator">/</span>
      <span>{{ $exam->name }}</span>
    </div>

    <!-- Header -->
    <div class="header">
      <div class="header-left">
        <h1>{{ $exam->name }}</h1>
        <p>{{ $exam->lesson->course->name ?? 'غير محدد' }} - {{ $exam->lesson->title ?? 'غير محدد' }}</p>
      </div>
    </div>

    <!-- Add Question Modal -->
    <div id="questionModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 id="modalTitle">إضافة سؤال جديد</h2>
          <button class="close-btn" id="closeQuestionModalBtn">&times;</button>
        </div>

        <form id="questionForm" method="POST" action="/teacher/exams/{{ $exam->id }}/questions">
          @csrf
          <input type="hidden" id="question_id" name="question_id" value="">

          <div class="form-group">
            <label for="question_type">نوع السؤال *</label>
            <select id="question_type" name="question_type" required>
              <option value="">اختر نوع السؤال</option>
              <option value="multiple_choice">اختيار من متعدد</option>
              <option value="true_false">صح/خطأ</option>
              <option value="short_answer">إجابة قصيرة</option>
            </select>
            @error('question_type')
              <span class="error-text">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="question_text">نص السؤال * (على الأقل 5 أحرف)</label>
            <textarea id="question_text" name="question_text" required minlength="5" placeholder="أدخل نص السؤال"></textarea>
            @error('question_text')
              <span class="error-text">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="order">ترتيب السؤال</label>
            <input type="number" id="order" name="order" value="1" min="1">
            <small style="color: var(--text-secondary); font-size: 13px; margin-top: 4px; display: block;">
              💡 سيتم تحديث الرقم تلقائياً بناءً على عدد الأسئلة
            </small>
            @error('order')
              <span class="error-text">{{ $message }}</span>
            @enderror
          </div>

          <!-- Multiple Choice / True-False Answers Section -->
          <div id="answersSection" class="form-group" style="display: none;">
            <label>الإجابات *</label>
            <div class="answers-container" id="answersContainer">
              <!-- Answer fields will be generated here -->
            </div>
            <button type="button" class="add-answer-btn" id="addAnswerBtn">
              <i class="ri-add-line"></i> إضافة إجابة
            </button>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeQuestionModalBtn2">إلغاء</button>
            <button type="submit" class="btn btn-primary">
              <i class="ri-check-line"></i> حفظ السؤال
            </button>
          </div>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">
        <i class="ri-check-double-line"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    <!-- Errors -->
    @if($errors->any())
      <div class="alert alert-danger" style="border-left-color: #FF3B30; background: rgba(255,59,48,0.1);">
        <i class="ri-alert-line"></i>
        <div>
          <strong>حدثت أخطاء:</strong>
          <ul style="margin-top: 8px; margin-right: 12px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <!-- Add Question Section -->
    <div style="margin-bottom: 32px;">
      <button id="openQuestionModalBtn" class="btn btn-primary">
        <i class="ri-add-line"></i> إضافة سؤال جديد
      </button>
    </div>

    <!-- Questions List -->
    @if($questions->count() > 0)
      <div class="questions-list">
        @foreach($questions as $question)
          <div class="question-card" data-question="{{ json_encode(['id' => $question->id, 'text' => $question->question_text, 'type' => $question->question_type, 'order' => $question->order, 'answers' => $question->answers->map(fn($a) => ['id' => $a->id, 'text' => $a->answer_text, 'is_correct' => (bool)$a->is_correct])->toArray()], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}">
            <div class="question-header">
              <div>
                <div class="question-title"><strong>#{{ $question->order }}.</strong> {{ $question->question_text }}</div>
              </div>
              <span class="question-type">
                @switch($question->question_type)
                  @case('multiple_choice')
                    اختيار من متعدد
                  @break
                  @case('true_false')
                    صح/خطأ
                  @break
                  @case('short_answer')
                    إجابة قصيرة
                  @break
                @endswitch
              </span>
            </div>

            @if($question->answers->count() > 0)
              <div class="answers">
                <strong style="font-size: 13px; color: var(--text-secondary);">الإجابات:</strong>
                @foreach($question->answers as $answer)
                  <div class="answer-item">
                    <span @if($answer->is_correct) class="answer-correct" @endif>
                      @if($answer->is_correct)
                        <i class="ri-check-line"></i> ✓
                      @endif
                      {{ $answer->answer_text }}
                    </span>
                  </div>
                @endforeach
              </div>
            @endif

            <div class="question-actions">
              <button class="action-btn edit-question-btn" data-question-id="{{ $question->id }}">
                <i class="ri-edit-line"></i> تعديل
              </button>
              <button class="action-btn delete-question-btn" data-question-id="{{ $question->id }}">
                <i class="ri-delete-bin-line"></i> حذف
              </button>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="ri-questionnaire-line"></i>
        <h3>لا توجد أسئلة بعد</h3>
        <p>ابدأ بإضافة أسئلة للاختبار</p>
        <button id="openQuestionModalBtn2" class="btn btn-primary" style="display: inline-block;">
          <i class="ri-add-line"></i> إضافة سؤال جديد
        </button>
      </div>
    @endif

    <!-- Beautiful Alert Modal -->
    <div id="alertOverlay" class="modal-overlay">
      <div class="alert-modal warning">
        <button class="close-alert-btn" id="closeAlertModalBtn">أ—</button>
        <div class="alert-header">
          <div class="alert-icon warning">
            <i class="ri-alert-line"></i>
          </div>
          <div class="alert-content">
            <h3 id="alertTitle">تنبيه</h3>
            <p id="alertMessage">رسالة الخطأ</p>
            <div id="alertSuggestion" class="alert-suggestion" style="display: none;">
              <strong>💡 اقتراح:</strong>
              <span id="alertSuggestionText"></span>
            </div>
          </div>
        </div>
        <div class="alert-actions">
          <button class="alert-btn alert-btn-primary" id="closeAlertModalBtn2">
            <i class="ri-check-line"></i> فهمت
          </button>
        </div>
      </div>
    </div>

    <!-- Beautiful Delete Confirm Modal -->
    <div id="confirmOverlay" class="modal-overlay">
      <div class="confirm-modal">
        <button class="close-confirm-btn" id="closeConfirmModalBtn">أ—</button>
        <div class="confirm-header">
          <div class="confirm-icon">
            <i class="ri-delete-bin-6-line"></i>
          </div>
          <div class="confirm-content">
            <h3>حذف السؤال</h3>
            <p>هل أنت متأكد من رغبتك في حذف هذا السؤال؟</p>
            <div class="confirm-message">
              <strong>⚠️ تحذير:</strong>
              هذا الإجراء لا يمكن التراجع عنه. سيتم حذف السؤال والإجابات المرتبطة به نهائياً.
            </div>
          </div>
        </div>
        <div class="confirm-actions">
          <button class="confirm-btn confirm-btn-cancel" id="closeConfirmModalBtn2">
            <i class="ri-close-line"></i> إلغاء
          </button>
          <button class="confirm-btn confirm-btn-danger" id="confirmDeleteBtn">
            <i class="ri-delete-bin-line"></i> حذف نهائياً
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
    const examId = {{ $exam->id }};
    let answerCount = 0;

    // Get all questions
    function getAllQuestionsCount() {
      return document.querySelectorAll('.question-card').length;
    }

    // Update dynamic order
    function updateDynamicOrder() {
      const totalQuestions = getAllQuestionsCount();
      const orderInput = document.getElementById('order');

      // Only update if adding new question (no question_id)
      if (!document.getElementById('question_id').value) {
        orderInput.value = totalQuestions + 1;
      }
    }

    // Open Question Modal (Add or Edit)
    function openQuestionModal(questionId = null) {
      const form = document.getElementById('questionForm');
      form.reset();

      document.getElementById('answersContainer').innerHTML = '';
      answerCount = 0;

      const modalTitle = document.getElementById('modalTitle');
      const questionIdInput = document.getElementById('question_id');
      const questionTypeSelect = document.getElementById('question_type');

      if (questionId) {
        // Edit Mode
        modalTitle.textContent = 'تعديل السؤال';

        // Find question card with data
        const questionCard = Array.from(document.querySelectorAll('.question-card')).find(card => {
          const data = card.getAttribute('data-question');
          if (!data) return false;
          const questionData = JSON.parse(data);
          return questionData.id === questionId;
        });

        if (questionCard) {
          const questionData = JSON.parse(questionCard.getAttribute('data-question'));

          // Populate form
          questionIdInput.value = questionData.id;
          questionTypeSelect.value = questionData.type;
          document.getElementById('question_text').value = questionData.text;
          document.getElementById('order').value = questionData.order;

          // Set form action
          form.action = `/teacher/questions/${questionData.id}`;

          // Add hidden _method for PUT request
          let methodInput = form.querySelector('input[name="_method"]');
          if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
          }

          // Populate answers if not short_answer
          if (questionData.type !== 'short_answer') {
            document.getElementById('answersSection').style.display = 'block';
            questionData.answers.forEach(function(answer) {
              addAnswerField(answer.text, answer.is_correct, answer.id);
            });
            updateAddAnswerBtnState();
          } else {
            document.getElementById('answersSection').style.display = 'none';
          }
        }
      } else {
        // Add Mode
        modalTitle.textContent = 'إضافة سؤال جديد';
        questionIdInput.value = '';

        // Remove _method for POST request
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();

        // Reset form action
        form.action = `/teacher/exams/${examId}/questions`;

        // Update dynamic order
        updateDynamicOrder();

        // Initialize with one answer field
        setTimeout(() => {
          questionTypeSelect.dispatchEvent(new Event('change'));
        }, 100);
      }

      document.getElementById('questionModal').classList.add('show');
      updateAddAnswerBtnState();
    }

    // Close Question Modal
    function closeQuestionModal() {
      document.getElementById('questionModal').classList.remove('show');
    }

    // Update Question Type
    document.getElementById('question_type').addEventListener('change', function() {
      const type = this.value;
      const answersSection = document.getElementById('answersSection');

      if (type === 'short_answer') {
        // Fade out animation
        answersSection.classList.add('removing');
        setTimeout(() => {
          answersSection.style.display = 'none';
          answersSection.classList.remove('removing');
        }, 300);
      } else {
        answersSection.style.display = 'block';
        document.getElementById('answersContainer').innerHTML = '';
        answerCount = 0;

        if (type === 'true_false') {
          addAnswerField('صح');
          addAnswerField('خطأ');
        } else if (type === 'multiple_choice') {
          addAnswerField();
        }

        updateAddAnswerBtnState();
      }
    });

    // Get max answers based on question type
    function getMaxAnswers() {
      const type = document.getElementById('question_type').value;
      if (type === 'true_false') return 2;
      if (type === 'multiple_choice') return 4;
      return 0;
    }

    // Update add answer button state
    function updateAddAnswerBtnState() {
      const btn = document.getElementById('addAnswerBtn');
      if (!btn) return;

      const maxAnswers = getMaxAnswers();
      const currentAnswers = document.querySelectorAll('.answer-field').length;

      if (currentAnswers >= maxAnswers && maxAnswers > 0) {
        btn.disabled = true;
        const typeName = document.getElementById('question_type').value === 'true_false' ? 'الصح والخطأ' : 'الاختيار من متعدد';
        btn.title = `الحد الأقصى: ${maxAnswers} إجابات لأسئلة ${typeName}`;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
      } else {
        btn.disabled = false;
        btn.title = '';
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
      }
    }

    // Add Answer Field
    document.getElementById('addAnswerBtn').addEventListener('click', function(e) {
      e.preventDefault();
      const maxAnswers = getMaxAnswers();
      const currentAnswers = document.querySelectorAll('.answer-field').length;
      const typeName = document.getElementById('question_type').value === 'true_false' ? 'الصح والخطأ' : 'الاختيار من متعدد';

      if (maxAnswers > 0 && currentAnswers >= maxAnswers) {
        showMaxAnswersAlert(maxAnswers, typeName);
        return;
      }

      if (currentAnswers < maxAnswers) {
        addAnswerField();
        updateAddAnswerBtnState();
      }
    });

    function addAnswerField(text = '', isCorrect = false, answerId = null) {
      const container = document.getElementById('answersContainer');
      const index = answerCount++;

      const field = document.createElement('div');
      field.className = 'answer-field';

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'delete-btn';
      removeBtn.title = 'حذف هذه الإجابة';
      removeBtn.innerHTML = '<i class="ri-delete-bin-6-line"></i>';
      removeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        field.style.animation = 'fadeOut 0.3s ease-out forwards';
        setTimeout(() => {
          field.remove();
          updateAddAnswerBtnState();
        }, 300);
      });

      const inputField = document.createElement('input');
      inputField.type = 'text';
      inputField.name = `answers[${index}][text]`;
      inputField.placeholder = 'نص الإجابة';
      inputField.value = text;
      inputField.required = true;

      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.name = `answers[${index}][is_correct]`;
      checkbox.checked = isCorrect;

      const label = document.createElement('label');
      label.style.cssText = 'display: flex; align-items: center; gap: 6px; margin: 0; font-weight: 400; white-space: nowrap; margin-bottom: 0;';
      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(' الإجابة الصحيحة'));

      field.appendChild(inputField);
      field.appendChild(label);

      // Add ID field if editing
      if (answerId) {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = `answers[${index}][id]`;
        idInput.value = answerId;
        field.appendChild(idInput);
      }

      field.appendChild(removeBtn);

      container.appendChild(field);
    }

    // Delete Question (confirm and submit form)
    function deleteQuestion(questionId) {
      showDeleteConfirmModal(questionId);
    }

    function showDeleteConfirmModal(questionId) {
      const confirmOverlay = document.getElementById('confirmOverlay');
      const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

      // Store the question ID in the button's data attribute
      confirmDeleteBtn.dataset.questionId = questionId;

      confirmOverlay.classList.add('show');
    }

    function closeConfirmModal() {
      const confirmOverlay = document.getElementById('confirmOverlay');
      confirmOverlay.classList.remove('show');
    }

    function confirmDeleteQuestion() {
      const questionId = document.getElementById('confirmDeleteBtn').dataset.questionId;

      // Submit form to delete
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/teacher/questions/${questionId}`;
      form.innerHTML = '@csrf<input type="hidden" name="_method" value="DELETE">';
      document.body.appendChild(form);
      form.submit();
    }

    // Close confirm modal when clicking outside
    document.getElementById('confirmOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeConfirmModal();
      }
    });

    // Edit Question (simplified - just open modal with data)
    function editQuestion(questionId) {
      openQuestionModal(questionId);
    }

    // Apply saved theme on page load
    (function() {
      const theme = localStorage.getItem('app-theme') || localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', theme);

      // Open modal if add=true in URL
      if (new URLSearchParams(window.location.search).get('add') === 'true') {
        setTimeout(openQuestionModal, 200);
      }
    })();

    // Close modal when clicking outside
    document.getElementById('questionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeQuestionModal();
      }
    });

    // ==== CHECK FOR DUPLICATE QUESTIONS ====
    function validateQuestionBeforeSave() {
      const questionText = document.getElementById('question_text').value.trim().toLowerCase();
      const questionType = document.getElementById('question_type').value;
      const questionId = document.getElementById('question_id').value; // For editing

      // Validate at least one correct answer for non-short_answer types
      if (questionType === 'multiple_choice' || questionType === 'true_false') {
        const hasCorrect = [...document.querySelectorAll('.answer-field input[type="checkbox"]')].some(cb => cb.checked);
        if (!hasCorrect) {
          document.getElementById('alertTitle').textContent = '⚠️ لا توجد إجابة صحيحة!';
          document.getElementById('alertMessage').innerHTML = 'يجب تحديد <strong>إجابة صحيحة واحدة على الأقل</strong> قبل حفظ السؤال.';
          document.getElementById('alertSuggestion').style.display = 'none';
          document.getElementById('alertOverlay').classList.add('show');
          return false;
        }
      }

      // Get all current answers
      const answerFields = document.querySelectorAll('.answer-field');
      const currentAnswers = [];

      answerFields.forEach(field => {
        const answerText = field.querySelector('input[placeholder="نص الإجابة"]').value.trim().toLowerCase();
        const isCorrect = field.querySelector('input[type="checkbox"]').checked;
        currentAnswers.push({
          text: answerText,
          is_correct: isCorrect
        });
      });

      // Get all existing question cards
      const questionCards = document.querySelectorAll('.question-card');

      for (let card of questionCards) {
        const existingData = JSON.parse(card.dataset.question);

        // Skip if editing the same question (question_id matches)
        if (questionId && questionId == existingData.id) {
          continue;
        }

        // Check if question text matches
        const existingText = existingData.text.trim().toLowerCase();
        if (existingText === questionText && existingData.type === questionType) {
          // Check if answers also match
          if (answersMatch(currentAnswers, existingData.answers)) {
            showDuplicateQuestionAlert(existingData.text, existingData.order);
            return false;
          }
        }
      }

      return true;
    }

    function showDuplicateQuestionAlert(questionText, questionOrder) {
      const alertOverlay = document.getElementById('alertOverlay');
      const alertTitle = document.getElementById('alertTitle');
      const alertMessage = document.getElementById('alertMessage');
      const alertSuggestion = document.getElementById('alertSuggestion');
      const alertSuggestionText = document.getElementById('alertSuggestionText');

      alertTitle.textContent = '⚠️ السؤال مكرر!';
      alertMessage.innerHTML = `<strong>"${questionText}"</strong><br>هذا السؤال موجود بالفعل في الاختبار برقم <strong>#${questionOrder}</strong> بنفس الإجابات.`;

      alertSuggestionText.textContent = 'يمكنك تعديل السؤال الموجود بدلاً من إضافة نسخة جديدة، أو تغيير محتوى السؤال الحالي.';
      alertSuggestion.style.display = 'block';

      alertOverlay.classList.add('show');
    }

    function closeAlertModal() {
      const alertOverlay = document.getElementById('alertOverlay');
      alertOverlay.classList.remove('show');
    }

    // Close alert when clicking outside
    document.getElementById('alertOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeAlertModal();
      }
    });

    function showMaxAnswersAlert(maxAnswers, typeName) {
      const alertOverlay = document.getElementById('alertOverlay');
      const alertTitle = document.getElementById('alertTitle');
      const alertMessage = document.getElementById('alertMessage');
      const alertSuggestion = document.getElementById('alertSuggestion');
      const alertSuggestionText = document.getElementById('alertSuggestionText');

      alertTitle.textContent = '🛑 تم الوصول للحد الأقصى';
      alertMessage.innerHTML = `لا يمكن إضافة أكثر من <strong>${maxAnswers} إجابات</strong> لأسئلة ${typeName}.`;

      alertSuggestionText.textContent = `حالياً لديك ${maxAnswers} إجابات، وهذا هو الحد الأقصى المسموح به لهذا النوع من الأسئلة.`;
      alertSuggestion.style.display = 'block';

      alertOverlay.classList.add('show');
    }

    // Helper function to check if two answer arrays match
    function answersMatch(currentAnswers, existingAnswers) {
      // For short_answer questions, no answers to check
      if (currentAnswers.length === 0 && existingAnswers.length === 0) {
        return true;
      }

      // If lengths don't match, they're different
      if (currentAnswers.length !== existingAnswers.length) {
        return false;
      }

      // Check if all answers are identical (order matters for now)
      for (let i = 0; i < currentAnswers.length; i++) {
        const current = currentAnswers[i];
        const existing = existingAnswers[i];

        if (current.text !== existing.text.toLowerCase() ||
            current.is_correct !== existing.is_correct) {
          return false;
        }
      }

      return true;
    }
    // ==== CSP-compliant event listeners (replacing inline handlers) ====
    document.getElementById('closeQuestionModalBtn').addEventListener('click', closeQuestionModal);
    document.getElementById('closeQuestionModalBtn2').addEventListener('click', closeQuestionModal);
    document.getElementById('openQuestionModalBtn').addEventListener('click', function() { openQuestionModal(); });
    document.getElementById('openQuestionModalBtn2')?.addEventListener('click', function() { openQuestionModal(); });
    document.getElementById('closeAlertModalBtn').addEventListener('click', closeAlertModal);
    document.getElementById('closeAlertModalBtn2').addEventListener('click', closeAlertModal);
    document.getElementById('closeConfirmModalBtn').addEventListener('click', closeConfirmModal);
    document.getElementById('closeConfirmModalBtn2').addEventListener('click', closeConfirmModal);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDeleteQuestion);

    // Edit question buttons (with data-question-id)
    document.querySelectorAll('.edit-question-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var qid = parseInt(this.getAttribute('data-question-id'));
        editQuestion(qid);
      });
    });

    // Delete question buttons (with data-question-id)
    document.querySelectorAll('.delete-question-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var qid = parseInt(this.getAttribute('data-question-id'));
        deleteQuestion(qid);
      });
    });

    // Form submit validation
    document.getElementById('questionForm').addEventListener('submit', function(e) {
      if (!validateQuestionBeforeSave()) {
        e.preventDefault();
      }
    });

  </script>

@endsection

