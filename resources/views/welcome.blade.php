<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>MyFin - Kios Pemesanan Mandiri</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9fafb;
            --surface-color: #ffffff;
            --primary-color: #111827;
            --primary-hover: #374151;
            --accent-color: #2563eb;
            --accent-light: #eff6ff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            flex-direction: row;
        }

        /* Sidebar Navigation (Desktop Default) */
        .sidebar {
            width: 100px;
            background: var(--surface-color);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 0;
            gap: 1.5rem;
            z-index: 10;
            flex-shrink: 0;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            width: 100%;
            padding: 0 10px;
        }
        .sidebar-logo img {
            max-width: 100%;
            max-height: 65px;
            object-fit: contain;
        }
        .nav-items-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            flex: 1;
            width: 100%;
            align-items: center;
        }
        .nav-item {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }
        .nav-item:hover {
            color: var(--primary-color);
            background: #f3f4f6;
        }
        .nav-item:active {
            transform: scale(0.95);
        }
        .nav-item.active {
            color: var(--accent-color);
            background: var(--accent-light);
            box-shadow: 0 2px 8px -2px rgba(37, 99, 235, 0.2);
        }
        .nav-item svg {
            width: 26px;
            height: 26px;
            margin-bottom: 5px;
        }
        .nav-item span {
            font-size: 0.725rem;
            font-weight: 600;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-color);
            position: relative;
            min-width: 0;
            overflow: hidden;
        }

        /* Header */
        .header {
            background: var(--surface-color);
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-shrink: 0;
        }
        .header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }
        .header p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.15rem;
        }
        .pos-badge {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            background: #f1f5f9;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }
        .esp-badge {
            gap: 7px;
            transition: all 0.25s ease;
        }
        .esp-badge.online {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .esp-badge.online .esp-dot {
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
        }
        .esp-badge.connecting {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .esp-badge.connecting .esp-dot {
            background: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
            animation: pulseAmber 1.2s infinite;
        }
        .esp-badge.offline {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .esp-badge.offline .esp-dot {
            background: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
        }
        .esp-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #94a3b8;
            display: inline-block;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }
        @keyframes pulseAmber {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Menu Area & Cards */
        .menu-area {
            flex: 1;
            padding: 1.75rem 2rem;
            overflow-y: auto;
            padding-bottom: 110px;
            -webkit-overflow-scrolling: touch;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1.5rem;
            align-items: stretch;
        }
        .menu-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 0.85rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            cursor: pointer;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            user-select: none;
        }
        .menu-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .menu-card:active {
            transform: scale(0.98);
        }
        .menu-image {
            width: 100%;
            height: 140px;
            border-radius: 12px;
            object-fit: cover;
            background: #f1f5f9;
            margin-bottom: 0.75rem;
        }
        .menu-info {
            padding: 0 0.15rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .menu-info h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }
        .menu-info p {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.85rem;
            margin-top: auto;
        }
        .add-btn {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #111827;
            padding: 0.5rem 0;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            width: 100%;
        }
        .add-btn:hover, .menu-card:hover .add-btn, .add-btn:active {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
        }

        /* Cart Sidebar (Desktop) */
        .cart-sidebar {
            width: 350px;
            background: var(--surface-color);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 40;
            flex-shrink: 0;
        }
        .cart-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .cart-header h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary-color);
        }
        .close-cart {
            display: none;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 6px;
            border-radius: 8px;
        }
        .close-cart:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding: 1.25rem 1.5rem;
            gap: 1rem;
            -webkit-overflow-scrolling: touch;
        }
        .cart-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .cart-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .cart-item-img {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f1f5f9;
        }
        .cart-item-info {
            flex: 1;
            min-width: 0;
        }
        .cart-item-info h4 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.2rem;
            line-height: 1.3;
        }
        .cart-item-info p {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
        }
        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            background: #ffffff;
            padding: 0.2rem 0.35rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            flex-shrink: 0;
        }
        .qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #111827;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .qty-btn:hover {
            background: #f1f5f9;
        }
        .qty-btn:active {
            transform: scale(0.9);
        }
        .qty-value {
            font-size: 0.85rem;
            font-weight: 600;
            min-width: 18px;
            text-align: center;
            color: #111827;
        }
        .cart-summary {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background: var(--surface-color);
            flex-shrink: 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1.15rem;
            padding-top: 0.5rem;
            border-top: 1px dashed var(--border-color);
        }
        .checkout-btn {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.95rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .checkout-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }
        .checkout-btn:active {
            transform: scale(0.98);
        }

        /* Mobile Floating Cart Action Bar */
        .mobile-cart-btn {
            display: none;
            position: fixed;
            bottom: max(16px, env(safe-area-inset-bottom, 16px));
            left: 16px;
            right: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.9rem 1.25rem;
            border-radius: 16px;
            font-weight: 700;
            box-shadow: 0 16px 32px -6px rgba(0, 0, 0, 0.3);
            z-index: 30;
            cursor: pointer;
            align-items: center;
            justify-content: space-between;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mobile-cart-btn:active {
            transform: scale(0.98);
        }
        .mobile-cart-badge {
            background: var(--accent-color);
            color: white;
            border-radius: 9999px;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
        }

        /* Modal Backdrop Overlay */
        .cart-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 35;
            transition: opacity 0.3s ease;
        }

        /* Biometric & Transaction Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 1rem;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .modal-content {
            background: var(--surface-color);
            padding: 1.25rem 1.4rem;
            border-radius: 20px;
            text-align: center;
            max-width: 420px;
            width: 92%;
            max-height: 94vh;
            max-height: 94dvh;
            overflow: hidden; /* Hilangkan slider / scrollbar sepenuhnya */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            transform: scale(0.96);
            margin: auto;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        .fingerprint-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            color: var(--primary-color);
        }
        .cancel-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .cancel-btn:hover {
            background: #f1f5f9;
        }
        .cancel-btn:active {
            transform: scale(0.98);
        }

        /* Responsive Breakpoints: Tablet Landscape & Small Desktop (1024px - 1200px) */
        @media (max-width: 1200px) and (min-width: 1025px) {
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1.25rem;
            }
            .cart-sidebar {
                width: 320px;
            }
        }

        /* Responsive Breakpoints: Tablet Portrait & Small Laptops (769px - 1024px) */
        @media (max-width: 1024px) and (min-width: 769px) {
            .sidebar {
                width: 85px;
                padding: 1.25rem 0;
                gap: 1rem;
            }
            .nav-item {
                width: 65px;
                height: 65px;
            }
            .nav-item svg {
                width: 22px;
                height: 22px;
            }
            .header {
                padding: 1rem 1.5rem;
            }
            .menu-area {
                padding: 1.25rem 1.5rem;
            }
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
            }
            .menu-image {
                height: 120px;
            }
            .cart-sidebar {
                width: 300px;
            }
            .cart-items {
                padding: 1rem;
            }
        }

        /* Responsive Breakpoints: All Mobile Devices & Small Tablets (<= 768px - Android, iPhone, etc.) */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: 100vh;
                height: 100dvh;
                overflow: hidden;
            }

            /* Mobile Top Navigation & Category Bar */
            .sidebar {
                width: 100%;
                height: auto;
                min-height: 56px;
                flex-direction: row;
                padding: 0.6rem 1rem;
                justify-content: space-between;
                align-items: center;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                gap: 0.75rem;
                background: var(--surface-color);
                box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            }
            .sidebar-logo {
                margin-bottom: 0;
                width: auto;
                padding: 0;
                flex-shrink: 0;
            }
            .sidebar-logo img {
                max-height: 34px;
            }
            .nav-items-container {
                flex-direction: row;
                gap: 0.4rem;
                justify-content: flex-end;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding: 2px 0;
                scrollbar-width: none;
            }
            .nav-items-container::-webkit-scrollbar {
                display: none;
            }
            .nav-item {
                width: auto;
                height: 38px;
                padding: 0.4rem 0.85rem;
                flex-direction: row;
                gap: 0.35rem;
                border-radius: 9999px;
                flex-shrink: 0;
                font-size: 0.8rem;
                border: 1px solid var(--border-color);
                background: var(--surface-color);
            }
            .nav-item.active {
                border-color: var(--accent-color);
                background: var(--accent-light);
            }
            .nav-item svg {
                margin-bottom: 0;
                width: 16px;
                height: 16px;
            }
            .nav-item span {
                display: inline-block !important; /* Label tetap terbaca di Android/iPhone */
                font-size: 0.775rem;
                font-weight: 700;
                white-space: nowrap;
            }

            /* Header on Mobile */
            .header {
                padding: 0.75rem 1rem;
                flex-shrink: 0;
            }
            .header h1 {
                font-size: 1.15rem;
                line-height: 1.2;
            }
            .header p {
                font-size: 0.775rem;
                margin-top: 0.1rem;
            }
            .pos-badge {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            background: #f1f5f9;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }
        .esp-badge {
            gap: 7px;
            transition: all 0.25s ease;
        }
        .esp-badge.online {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .esp-badge.online .esp-dot {
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
        }
        .esp-badge.connecting {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .esp-badge.connecting .esp-dot {
            background: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
            animation: pulseAmber 1.2s infinite;
        }
        .esp-badge.offline {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .esp-badge.offline .esp-dot {
            background: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
        }
        .esp-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #94a3b8;
            display: inline-block;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }
        @keyframes pulseAmber {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

            /* Menu Grid for Mobile (2 Columns) */
            .menu-area {
                padding: 0.85rem 1rem 90px;
            }
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.85rem;
            }
            .menu-card {
                padding: 0.65rem;
                border-radius: 14px;
            }
            .menu-image {
                height: 110px;
                aspect-ratio: 4/3;
                border-radius: 10px;
                margin-bottom: 0.5rem;
            }
            .menu-info h3 {
                font-size: 0.875rem;
                line-height: 1.3;
                margin-bottom: 0.25rem;
            }
            .menu-info p {
                font-size: 0.85rem;
                margin-bottom: 0.65rem;
            }
            .add-btn {
                padding: 0.5rem;
                font-size: 0.8rem;
                border-radius: 8px;
            }

            /* Mobile Cart Slide-Out Drawer */
            .cart-sidebar {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                height: 100vh;
                height: 100dvh;
                transform: translateX(100%);
                width: 100%;
                max-width: 400px;
                box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2);
            }
            .cart-sidebar.open {
                transform: translateX(0);
            }
            .close-cart {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .mobile-cart-btn {
                display: flex;
            }
            .cart-overlay.active {
                display: block;
            }
            .cart-summary {
                padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 1.5rem));
            }
        }

        /* Ultra Compact Mobile (iPhone SE, Small Androids <= 375px) */
        @media (max-width: 375px) {
            .menu-grid {
                gap: 0.65rem;
            }
            .menu-card {
                padding: 0.5rem;
            }
            .menu-image {
                height: 95px;
            }
            .menu-info h3 {
                font-size: 0.825rem;
            }
            .menu-info p {
                font-size: 0.8rem;
            }
            .add-btn {
                font-size: 0.75rem;
                padding: 0.4rem;
            }
            .nav-item {
                padding: 0.35rem 0.65rem;
            }
            .nav-item span {
                font-size: 0.7rem;
            }
        }

        /* Micro Animations */
        @keyframes pulse-bio {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.08); opacity: 1; }
        }
        @keyframes blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            
        /* Success State Modal Styling */
        #modalSuccessState {
            animation: successPopIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes successPopIn {
            0% { opacity: 0; transform: scale(0.94); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes successRingPulse {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 0; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        .success-badge-container {
            position: relative;
            width: 76px;
            height: 76px;
            margin: 0 auto 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-badge-pulse {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            animation: successRingPulse 2.2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }
        .success-badge-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -4px rgba(16, 185, 129, 0.45), 0 4px 6px -2px rgba(16, 185, 129, 0.2);
            position: relative;
            z-index: 2;
        }
        .modern-receipt-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.35rem;
            text-align: left;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
            position: relative;
        }
        .receipt-ticket-divider {
            position: relative;
            margin: 0.95rem -1.4rem 1rem;
            border-top: 1.5px dashed #cbd5e1;
        }
        .receipt-ticket-divider::before,
        .receipt-ticket-divider::after {
            content: '';
            position: absolute;
            top: -8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--surface-color, #ffffff);
            border: 1.5px solid #e2e8f0;
            box-sizing: border-box;
        }
        .receipt-ticket-divider::before {
            left: -9px;
            border-left-color: transparent;
            border-top-color: transparent;
            transform: rotate(-45deg);
        }
        .receipt-ticket-divider::after {
            right: -9px;
            border-right-color: transparent;
            border-top-color: transparent;
            transform: rotate(45deg);
        }
        .success-finish-btn {
            width: 100%;
            padding: 0.9rem 1.25rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0f172a;
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.22);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .success-finish-btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.3);
        }
        .success-finish-btn:active {
            transform: scale(0.98);
        }

50% { opacity: 0.3; transform: scale(0.75); }
        }

        /* Custom Modern On-Theme Toast */
        .custom-toast {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 0.9rem 1.15rem;
            box-shadow: 0 16px 36px -6px rgba(0, 0, 0, 0.14), 0 4px 10px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            pointer-events: auto;
            animation: toastSlideDown 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.25s ease;
        }
        .custom-toast.hide {
            animation: toastSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast-icon-box {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toast-icon-box.warning {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }
        .toast-icon-box.error {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #ffe4e6;
        }
        .toast-icon-box.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }
        @keyframes toastSlideDown {
            from { opacity: 0; transform: translateY(-24px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes toastSlideUp {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-20px) scale(0.96); }
        }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
    <!-- Midtrans Snap JS SDK -->
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>

    <!-- Sidebar / Top Nav on Mobile -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <!-- Menampilkan logo.png dari folder public/ -->
            <img src="/logo.png" alt="MyFin Logo" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
        </div>
        <div class="nav-items-container">
            <div class="nav-item active" onclick="filterMenu('semua', this)">
                <!-- Icon: Grid (All) -->
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Semua</span>
            </div>
            <div class="nav-item" onclick="filterMenu('makanan', this)">
                <!-- Icon: Burger (Food) -->
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2C8.134 2 5 5.134 5 9v1h14V9c0-3.866-3.134-7-7-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 14v1a3 3 0 003 3h10a3 3 0 003-3v-1H4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 11h18v2H3v-2z"></path></svg>
                <span>Makanan</span>
            </div>
            <div class="nav-item" onclick="filterMenu('minuman', this)">
                <!-- Icon: Cup (Drink) -->
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                <span>Minuman</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>Kantin PSDKU</h1>
                <p>Pilih pesanan Anda di bawah ini.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.65rem; flex-shrink: 0;">
                <!-- Status Device ESP32 (Sesuai Desain Template Kios) -->
                <div id="espDeviceBadge" class="pos-badge esp-badge connecting" title="Device ID: esp32-kios-01">
                    <span id="espStatusDot" class="esp-dot"></span>
                    <span id="espStatusText">Scanner: Connecting...</span>
                </div>
                <span class="pos-badge">Meja POS #01</span>
            </div>
        </div>

        <!-- Menu Area -->
        <div class="menu-area">
            <div class="menu-grid" id="menu-grid">
                @forelse($menus as $menu)
                <div class="menu-card" data-category="{{ $menu->effective_category }}" onclick="addToCart('{{ addslashes($menu->nama_menu) }}', {{ $menu->harga }}, '{{ $menu->foto_url }}', {{ $menu->id }})">
                    <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama_menu }}" class="menu-image" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';">
                    <div class="menu-info">
                        <h3>{{ $menu->nama_menu }}</h3>
                        <p>Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    </div>
                    <div class="add-btn">Tambah</div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <p style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Belum ada menu yang tersedia</p>
                    <p style="font-size: 0.85rem; margin-top: 4px;">Menu yang ditambahkan oleh stand merchant akan otomatis muncul di sini.</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <button class="mobile-cart-btn" onclick="toggleMobileCart()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            Pesanan <span class="mobile-cart-badge" id="mobile-badge">0</span>
        </button>
    </div>

    <div class="cart-overlay" id="cart-overlay" onclick="toggleMobileCart()"></div>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h2>Pesanan Anda</h2>
            <button class="close-cart" onclick="toggleMobileCart()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="cart-items" id="cart-items">
            <!-- Items will be injected via JS -->
            <div style="text-align:center; color: var(--text-muted); font-size: 0.9rem; margin-top: 2rem;">Belum ada pesanan</div>
        </div>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">Rp 0</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span id="total">Rp 0</span>
            </div>
            <button class="checkout-btn" onclick="showModal()">Bayar Sekarang</button>
        </div>
    </div>

        <!-- Biometric & Midtrans Modal -->
    <div class="modal-overlay" id="bioModal">
        <div class="modal-content" style="max-width: 420px; width: 92%; text-align: center; border-radius: 20px; padding: 1.25rem 1.4rem; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div id="modalWaitingState">
                <!-- Fingerprint Scanning Icon with Animation -->
                <div class="fingerprint-scan-box" style="position: relative; width: 60px; height: 60px; margin: 0 auto 0.65rem; color: #2563eb; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; animation: pulse-bio 1.8s infinite ease-in-out;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                </div>
                
                <div style="display: inline-block; background: #eff6ff; color: #1d4ed8; padding: 0.25rem 0.85rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.45rem; letter-spacing: 0.3px;" id="modalOrderBadge">
                    Menunggu Otorisasi Pembayaran
                </div>

                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 0.2rem;">Tempelkan Sidik Jari Anda</h2>
                <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 0.75rem; line-height: 1.35;">
                    Silakan letakkan jari pada scanner kios. Tagihan otomatis didebet dari kartu Anda.
                </p>

                <!-- Status & Payment Box (Layar Status Real-Time Pengganti LCD) -->
                <div id="virtualLcdContainer" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 0.75rem 0.95rem; margin-bottom: 0.85rem; text-align: left; transition: all 0.3s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.45rem; border-bottom: 1px solid #e2e8f0;">
                        <span style="color: #64748b; font-size: 0.8rem; font-weight: 500;">Total Pembayaran:</span>
                        <strong style="color: #0f172a; font-size: 1.15rem; font-weight: 800;" id="modalTotalAmount">Rp 0</strong>
                    </div>

                    <div style="margin-top: 0.55rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 5px;">
                                <span id="statusLiveDot" style="width: 7px; height: 7px; background: #2563eb; border-radius: 50%; display: inline-block; animation: blink 1.2s infinite;"></span>
                                Status Pembayaran
                            </span>
                        </div>

                        <!-- Live Virtual LCD Card -->
                        <div id="virtualLcdScreen" style="background: #ffffff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 0.65rem 0.8rem; box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.08); transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div id="statusIconWrapper" style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.3s ease;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <!-- Baris 1: Pengganti Baris 1 LCD (Status Utama) -->
                                    <div id="sensorStatusLine1" style="font-weight: 800; font-size: 0.875rem; color: #1e40af; line-height: 1.25; letter-spacing: 0.2px; transition: color 0.3s ease;">
                                        TEMPELKAN SIDIK JARI
                                    </div>
                                    <!-- Baris 2: Pengganti Baris 2 LCD (Keterangan / Panduan) -->
                                    <div id="sensorStatusLine2" style="font-size: 0.75rem; color: #64748b; margin-top: 2px; line-height: 1.3; font-weight: 500; transition: color 0.3s ease;">
                                        Silakan letakkan jari Anda pada pemindai biometrik di meja kios
                                    </div>
                                    <!-- Elemen kompatibilitas ID sebelumnya -->
                                    <span id="sensorStatusText" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="cancel-btn" style="width: 100%; padding: 0.65rem 1rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600;" onclick="hideModal()">Batalkan Pesanan</button>
            </div>

            <!-- Success State -->
            <div id="modalSuccessState" style="display: none; text-align: center;">
                <!-- Icon Centang Hijau Sesuai Screenshot Asli -->
                <div style="width: 52px; height: 52px; background: #dcfce7; border-radius: 50%; margin: 0 auto 0.65rem; display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="#10b981" viewBox="0 0 24 24" style="width: 28px; height: 28px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Judul & Subtitle Sesuai Screenshot Asli -->
                <h2 style="font-size: 1.2rem; font-weight: 800; color: #111827; margin-bottom: 0.2rem; letter-spacing: -0.01em;">
                    PEMBAYARAN BERHASIL!
                </h2>
                <p style="color: #059669; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.75rem;">
                    Verifikasi Biometrik Berhasil
                </p>

                <!-- Kartu Resi / Struk Bersih Sesuai Screenshot Asli -->
                <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 0.75rem 0.95rem; margin-bottom: 0.85rem; text-align: left;">
                    <!-- No. Referensi -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <span style="color: #6b7280; font-size: 0.8rem;">No. Referensi:</span>
                        <span style="font-family: monospace; font-size: 0.75rem; font-weight: 700; color: #1e40af; background: #eff6ff; padding: 2px 6px; border-radius: 4px;" id="receiptOrderCode">-</span>
                    </div>

                    <!-- Nama Pembeli -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <span style="color: #6b7280; font-size: 0.8rem;">Nama Pembeli:</span>
                        <strong style="color: #111827; font-size: 0.85rem;" id="receiptStudentName">-</strong>
                    </div>

                    <!-- Metode Bayar -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <span style="color: #6b7280; font-size: 0.8rem;">Metode Bayar:</span>
                        <strong style="color: #2563eb; font-size: 0.85rem;" id="receiptBankName">Bank BCA (•••• 2675)</strong>
                    </div>

                    <!-- Waktu -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <span style="color: #6b7280; font-size: 0.8rem;">Waktu:</span>
                        <span style="color: #6b7280; font-size: 0.8rem;" id="receiptTime">-</span>
                    </div>

                    <!-- Garis Titik-titik -->
                    <div style="border-top: 1px dashed #e5e7eb; margin: 0.45rem 0 0.55rem 0;"></div>

                    <!-- Total Dibayar -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: #111827; font-size: 0.85rem;">Total Dibayar:</strong>
                        <strong style="color: #059669; font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em;" id="receiptTotalAmount">Rp 0</strong>
                    </div>
                </div>

                <!-- Tombol Hitam Sesuai Screenshot Asli -->
                <button style="width: 100%; background: #111827; color: #ffffff; padding: 0.75rem; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; font-size: 0.9rem; transition: background 0.2s;" onclick="finishOrder()">
                    Selesai & Kembali ke Menu
                </button>
            </div>
        </div>
    </div>

    <script>
        // Sistem Notifikasi Toast Sesuai Tema Kios
        function showNotification(message, type = "warning", title = null) {
            const container = document.getElementById("toastContainer");
            if (!container) return;

            if (!title) {
                if (type === "warning") title = "Daftar Pesanan Masih Kosong";
                else if (type === "error") title = "Terjadi Kesalahan";
                else title = "Pemberitahuan";
            }

            let iconSvg = '';
            if (type === "warning") {
                iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            } else if (type === "error") {
                iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            } else {
                iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
            }

            const toast = document.createElement("div");
            toast.className = "custom-toast";
            toast.innerHTML = `
                <div class="toast-icon-box ${type}">
                    ${iconSvg}
                </div>
                <div style="flex: 1; text-align: left;">
                    <div style="font-weight: 700; font-size: 0.875rem; color: #0f172a; margin-bottom: 2px;">${title}</div>
                    <div style="font-size: 0.825rem; color: #475569; line-height: 1.4;">${message}</div>
                </div>
                <button type="button" onclick="this.closest('.custom-toast').remove()" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add("hide");
                setTimeout(() => toast.remove(), 250);
            }, 3200);
        }

        // Override native window.alert agar selalu menggunakan modal toast bertema Kios
        window.alert = function(msg) {
            showNotification(msg, "warning", "Perhatian Kios");
        };

        function playSuccessSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, now);
                osc.frequency.setValueAtTime(659.25, now + 0.1);
                osc.frequency.setValueAtTime(783.99, now + 0.2);
                osc.frequency.setValueAtTime(1046.50, now + 0.3);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.8);
            } catch(e) {}
        }

        function filterMenu(category, element) {
            const navItems = document.querySelectorAll('.nav-items-container .nav-item');
            navItems.forEach(item => item.classList.remove('active'));
            if (element) element.classList.add('active');

            const cards = document.querySelectorAll('.menu-card');
            cards.forEach(card => {
                const cardCat = (card.getAttribute('data-category') || 'makanan').toLowerCase().trim();
                if (category === 'semua' || cardCat === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        let cart = [];
        try { cart = JSON.parse(sessionStorage.getItem('kiosCart') || '[]'); } catch(e) { cart = []; }
        document.addEventListener('DOMContentLoaded', () => { renderCart(); checkAndResumePendingOrder(); });
        let currentOrderId = null;
        let paymentCompleted = false;
        let pollTimer = null;

        function toggleMobileCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        function addToCart(name, price, img, menu_id = null) {
            const existing = cart.find(item => item.name === name);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({name, price, img, qty: 1, menu_id: menu_id});
            }
            renderCart();
        }

        function updateQty(index, delta) {
            cart[index].qty += delta;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const badge = document.getElementById('mobile-badge');
            try { sessionStorage.setItem('kiosCart', JSON.stringify(cart)); } catch(e) {}
            
            let totalQty = 0;
            cart.forEach(item => totalQty += item.qty);
            badge.innerText = totalQty;

            if (cart.length === 0) {
                container.innerHTML = '<div style="text-align:center; color: var(--text-muted); font-size: 0.9rem; margin-top: 2rem;">Belum ada pesanan</div>';
                document.getElementById('subtotal').innerText = 'Rp 0';
                document.getElementById('total').innerText = 'Rp 0';
                return;
            }

            let html = '';
            let total = 0;
            cart.forEach((item, i) => {
                const itemTotal = item.price * item.qty;
                total += itemTotal;
                html += `
                <div class="cart-item">
                    <img src="${item.img}" class="cart-item-img">
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <p>Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="updateQty(${i}, -1)">-</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${i}, 1)">+</button>
                    </div>
                </div>
                `;
            });
            container.innerHTML = html;
            document.getElementById('subtotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('total').innerText = 'Rp ' + total.toLocaleString('id-ID');
            const mobTotal = document.getElementById('mobileTotalText'); if (mobTotal) mobTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function updateSensorStatusText(msg) {
            updatePaymentDisplayStatus({ line1: msg, line2: 'Status Pemindaian Sensor Biometrik', dot: 'blue' });
        }

        function updatePaymentDisplayStatus(data) {
            if (!data) return;
            
            let line1 = typeof data === 'string' ? data : (data.line1 || 'TEMPELKAN SIDIK JARI');
            let line2 = typeof data === 'string' ? 'Status Pemindaian Sensor Biometrik' : (data.line2 || '');
            let status = typeof data === 'string' ? 'waiting' : (data.status || 'waiting');
            let dot = typeof data === 'string' ? 'blue' : (data.dot || 'blue');

            const l1El = document.getElementById('sensorStatusLine1');
            const l2El = document.getElementById('sensorStatusLine2');
            const legacyEl = document.getElementById('sensorStatusText');
            const screenEl = document.getElementById('virtualLcdScreen');
            const iconWrap = document.getElementById('statusIconWrapper');
            const liveDot = document.getElementById('statusLiveDot');
            const scanBox = document.getElementById('modalWaitingState')?.querySelector('.fingerprint-scan-box');

            if (l1El) l1El.innerText = line1;
            if (l2El) l2El.innerText = line2;
            if (legacyEl) legacyEl.innerText = line1;

            if (status === 'processing' || dot === 'amber') {
                if (screenEl) {
                    screenEl.style.borderColor = '#f59e0b';
                    screenEl.style.backgroundColor = '#fffbeb';
                    screenEl.style.boxShadow = '0 4px 16px -2px rgba(245, 158, 11, 0.2)';
                }
                if (l1El) l1El.style.color = '#b45309';
                if (l2El) l2El.style.color = '#92400e';
                if (iconWrap) {
                    iconWrap.style.background = '#fef3c7';
                    iconWrap.style.color = '#d97706';
                    iconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px; animation: spin 1.2s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
                }
                if (liveDot) {
                    liveDot.style.background = '#f59e0b';
                    liveDot.style.animation = 'blink 0.8s infinite';
                }
                if (scanBox) {
                    scanBox.style.color = '#d97706';
                    scanBox.style.background = '#fef3c7';
                }
            } else if (status === 'success' || dot === 'green') {
                if (screenEl) {
                    screenEl.style.borderColor = '#10b981';
                    screenEl.style.backgroundColor = '#ecfdf5';
                    screenEl.style.boxShadow = '0 4px 16px -2px rgba(16, 185, 129, 0.2)';
                }
                if (l1El) l1El.style.color = '#047857';
                if (l2El) l2El.style.color = '#065f46';
                if (iconWrap) {
                    iconWrap.style.background = '#d1fae5';
                    iconWrap.style.color = '#059669';
                    iconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                }
                if (liveDot) {
                    liveDot.style.background = '#10b981';
                    liveDot.style.animation = 'none';
                }
                if (scanBox) {
                    scanBox.style.color = '#059669';
                    scanBox.style.background = '#d1fae5';
                }
            } else if (status === 'rejected' || status === 'failed' || dot === 'red') {
                if (screenEl) {
                    screenEl.style.borderColor = '#ef4444';
                    screenEl.style.backgroundColor = '#fef2f2';
                    screenEl.style.boxShadow = '0 4px 16px -2px rgba(239, 68, 68, 0.2)';
                }
                if (l1El) l1El.style.color = '#b91c1c';
                if (l2El) l2El.style.color = '#991b1b';
                if (iconWrap) {
                    iconWrap.style.background = '#fee2e2';
                    iconWrap.style.color = '#dc2626';
                    iconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
                }
                if (liveDot) {
                    liveDot.style.background = '#ef4444';
                    liveDot.style.animation = 'blink 0.5s infinite';
                }
                if (scanBox) {
                    scanBox.style.color = '#dc2626';
                    scanBox.style.background = '#fee2e2';
                }
            } else {
                // Default waiting (Blue)
                if (screenEl) {
                    screenEl.style.borderColor = '#bfdbfe';
                    screenEl.style.backgroundColor = '#ffffff';
                    screenEl.style.boxShadow = '0 4px 12px -2px rgba(37, 99, 235, 0.08)';
                }
                if (l1El) l1El.style.color = '#1e40af';
                if (l2El) l2El.style.color = '#64748b';
                if (iconWrap) {
                    iconWrap.style.background = '#eff6ff';
                    iconWrap.style.color = '#2563eb';
                    iconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>';
                }
                if (liveDot) {
                    liveDot.style.background = '#2563eb';
                    liveDot.style.animation = 'blink 1.2s infinite';
                }
                if (scanBox) {
                    scanBox.style.color = '#2563eb';
                    scanBox.style.background = '#eff6ff';
                }
            }
        }

        async function showModal() {
            if(cart.length === 0) return showNotification("Silakan pilih menu makanan atau minuman terlebih dahulu.", "warning", "Daftar Pesanan Masih Kosong");

            let total = 0;
            cart.forEach(item => total += (item.price * item.qty));

            if (pollTimer) { clearTimeout(pollTimer); pollTimer = null; } isPolling = false;
            paymentCompleted = false;

            // Generate Order ID Instan di Frontend (0ms tanpa menunggu request server selesai)
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const dateStr = now.getFullYear() + pad(now.getMonth()+1) + pad(now.getDate()) + pad(now.getHours()) + pad(now.getMinutes()) + pad(now.getSeconds());
            const randCode = Math.random().toString(36).substring(2, 6).toUpperCase();
            currentOrderId = `MYFIN-${dateStr}-${randCode}`;

            // Simpan ke localStorage agar pop-up tetap terbuka saat halaman di-reload
            localStorage.setItem('pendingKiosOrder', JSON.stringify({
                order_id: currentOrderId,
                total: total,
                cart: cart
            }));

            document.getElementById('modalWaitingState').style.display = 'block';
            document.getElementById('modalSuccessState').style.display = 'none';
            document.getElementById('modalTotalAmount').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('modalOrderBadge').innerText = 'Pesanan #' + currentOrderId;
            updatePaymentDisplayStatus({ line1: 'TEMPELKAN SIDIK JARI', line2: 'Silakan letakkan jari pada scanner di meja kios', status: 'waiting', dot: 'blue' });
            document.getElementById('bioModal').classList.add('active');

            // Mulai polling status seketika (0ms delay)
            startPollingStatus();

            // Simpan pesanan ke database di latar belakang
            fetch('/api/kios/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: currentOrderId,
                    items: cart,
                    total_amount: total,
                    device_id: 'ESP32-KANTIN-01'
                })
            }).catch(err => console.error("Checkout background err:", err));
        }

                        let isPolling = false;
        function startPollingStatus() {
            if (pollTimer) clearTimeout(pollTimer);
            isPolling = false;

            async function pollOnce() {
                if (!currentOrderId || paymentCompleted) return;
                if (isPolling) return;
                isPolling = true;

                const polledId = currentOrderId;
                try {
                    const res = await fetch(`/api/kios/order-status/${polledId}?_t=${Date.now()}`, { cache: 'no-store' });
                    const data = await res.json();
                    if (polledId === currentOrderId && !paymentCompleted && data.success) {
                        // Perbarui status tampilan virtual LCD di web secara real-time
                        if (data.display_status || data.lcd_status) {
                            updatePaymentDisplayStatus(data.display_status || data.lcd_status);
                        }

                        if (data.status === 'success') {
                            if (pollTimer) { clearTimeout(pollTimer); pollTimer = null; }
                            // Transisi instan (<0.1s) ke pop-up struk sesuai permintaan
                            handlePaymentSuccess(data.transaction);
                            return;
                        } else if (data.status === 'failed' || (data.display_status && data.display_status.dot === 'red')) {
                            updatePaymentDisplayStatus(data.display_status || {
                                line1: 'STATUS: DITOLAK',
                                line2: 'Sidik jari belum terdaftar di sistem',
                                status: 'failed',
                                dot: 'red'
                            });
                            // Kembalikan ke biru siaga setelah 2.5 detik agar pelanggan bisa coba jari lain
                            if (!window.rejectTimer) {
                                window.rejectTimer = setTimeout(() => {
                                    if (currentOrderId && !paymentCompleted) {
                                        updatePaymentDisplayStatus({
                                            line1: 'TEMPELKAN SIDIK JARI',
                                            line2: 'Silakan letakkan jari pada scanner di meja kios',
                                            status: 'waiting',
                                            dot: 'blue'
                                        });
                                    }
                                    window.rejectTimer = null;
                                }, 2500);
                            }
                        }
                    }
                } catch(e) {}
                finally {
                    isPolling = false;
                    if (currentOrderId && !paymentCompleted) {
                        pollTimer = setTimeout(pollOnce, 60);
                    }
                }
            }

            pollOnce();
        }

        async function payWithMidtransSnap() {
            if (!currentOrderId) {
                alert("Pesanan belum siap. Silakan ulangi.");
                return;
            }

            try {
                updateSensorStatusText("Membuat sesi Midtrans Snap...");
                const res = await fetch("/api/kios/snap-token", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ order_id: currentOrderId })
                });

                const data = await res.json();
                if (data.success && data.snap_token) {
                    if (typeof window.snap === "undefined") {
                        alert("Gagal memuat SDK Midtrans. Pastikan koneksi internet aktif.");
                        return;
                    }

                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log("Midtrans Snap Success:", result);
                            handlePaymentSuccess({
                                order_id: currentOrderId,
                                total_amount: cart.reduce((s, i) => s + (i.price * i.qty), 0),
                                student_name: "Mahasiswa (Midtrans Snap)",
                                bank: result.payment_type ? result.payment_type.toUpperCase() : "Midtrans QRIS/VA",
                                midtrans_id: result.transaction_id || result.order_id
                            });
                        },
                        onPending: function(result) {
                            console.log("Midtrans Snap Pending:", result);
                            updateSensorStatusText("Menunggu penyelesaian di Midtrans (Pending)...");
                        },
                        onError: function(result) {
                            console.error("Midtrans Snap Error:", result);
                            alert("Pembayaran Midtrans gagal: " + (result.status_message || "Ditolak"));
                            updateSensorStatusText("Pembayaran Midtrans gagal.");
                        },
                        onClose: function() {
                            updateSensorStatusText("Popup Midtrans ditutup.");
                        }
                    });
                } else {
                    alert("Gagal memanggil Snap Token: " + (data.message || "Periksa konfigurasi Midtrans"));
                }
            } catch (err) {
                console.error(err);
                alert("Koneksi ke Midtrans gagal.");
            }
        }

        async function simulateFingerprint(fingerprintId) {
            if (!currentOrderId) return;

            updatePaymentDisplayStatus({ line1: 'MEMPROSES JARI..', line2: 'Mencocokkan sidik jari & menghubungi bank...', status: 'processing', dot: 'amber' });

            try {
                const res = await fetch('/api/biometric/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        fingerprint_id: fingerprintId,
                        order_id: currentOrderId,
                        device_id: 'ESP32-KANTIN-01'
                    })
                });

                const data = await res.json();
                if (data.success && data.status === 'success') {
                    if (pollTimer) { clearTimeout(pollTimer); pollTimer = null; } isPolling = false;
                    handlePaymentSuccess({
                        order_id: currentOrderId,
                        student_name: data.student.name,
                        bank: data.transaction.bank,
                        masked_card: data.transaction.masked_card,
                        total_amount: data.transaction.total_amount,
                    });
                } else {
                    alert(data.message || "Verifikasi sidik jari gagal.");
                    updatePaymentDisplayStatus({ line1: 'TEMPELKAN SIDIK JARI', line2: 'Silakan letakkan jari pada scanner di meja kios', status: 'waiting', dot: 'blue' });
                }
            } catch (err) {
                alert("Gagal memproses simulasi: " + err.message);
                updatePaymentDisplayStatus({ line1: 'TEMPELKAN SIDIK JARI', line2: 'Silakan letakkan jari pada scanner di meja kios', status: 'waiting', dot: 'blue' });
            }
        }

        function handlePaymentSuccess(txData) {
            playSuccessSound();

            paymentCompleted = true; // Tandai pembayaran sukses
            localStorage.removeItem('pendingKiosOrder');

            document.getElementById('receiptOrderCode').innerText = txData.order_id || currentOrderId;
            document.getElementById('receiptStudentName').innerText = txData.student_name || 'Budi Santoso';
            
            // Format metode bank yang rapi tanpa kata-kata teknis
            let bankName = txData.bank || 'Bank BCA';
            if (txData.masked_card) {
                let digits = txData.masked_card.replace(/\D/g, "");
                let last4 = digits.length >= 4 ? digits.slice(-4) : "2675";
                bankName += " (•••• " + last4 + ")";
            }
            document.getElementById('receiptBankName').innerText = bankName;

            // Format tanggal yang elegan
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
            document.getElementById('receiptTime').innerText = dateStr + ', ' + timeStr + ' WIB';

            document.getElementById('receiptTotalAmount').innerText = 'Rp ' + Number(txData.total_amount).toLocaleString('id-ID');

            document.getElementById('modalWaitingState').style.display = 'none';
            document.getElementById('modalSuccessState').style.display = 'block';

            cart = [];
            sessionStorage.removeItem('kiosCart');
            renderCart();

            // Auto close receipt dalam 4 detik dengan countdown pada tombol
            let countdown = 4;
            const finishBtn = document.querySelector('#modalSuccessState button');
            const originalBtnText = "Selesai & Kembali ke Menu";
            if (finishBtn) finishBtn.innerText = `${originalBtnText} (${countdown}s)`;

            if (window.receiptCountdownInterval) clearInterval(window.receiptCountdownInterval);
            window.receiptCountdownInterval = setInterval(() => {
                countdown--;
                if (finishBtn) {
                    if (countdown > 0) {
                        finishBtn.innerText = `${originalBtnText} (${countdown}s)`;
                    } else {
                        finishBtn.innerText = originalBtnText;
                    }
                }
                if (countdown <= 0) {
                    clearInterval(window.receiptCountdownInterval);
                    window.receiptCountdownInterval = null;
                }
            }, 1000);

            if (window.receiptAutoCloseTimer) clearTimeout(window.receiptAutoCloseTimer);
            window.receiptAutoCloseTimer = setTimeout(() => {
                finishOrder();
            }, 4000);
        }

        function finishOrder() {
            if (window.receiptAutoCloseTimer) {
                clearTimeout(window.receiptAutoCloseTimer);
                window.receiptAutoCloseTimer = null;
            }
            if (window.receiptCountdownInterval) {
                clearInterval(window.receiptCountdownInterval);
                window.receiptCountdownInterval = null;
            }
            const finishBtn = document.querySelector('#modalSuccessState button');
            if (finishBtn) finishBtn.innerText = "Selesai & Kembali ke Menu";

            hideModal();
            if(window.innerWidth <= 768) {
                const sidebar = document.getElementById('cart-sidebar');
                if (sidebar && sidebar.classList.contains('open')) {
                    toggleMobileCart();
                }
            }
        }

        // Pemantau Status Device ESP32 Real-Time di Pojok Kanan Atas
        async function checkEspDeviceStatus() {
            try {
                const res = await fetch(`/api/esp32/device-status?device_id=esp32-kios-01&_t=${Date.now()}`, {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache, no-store' }
                });
                const data = await res.json();
                const badge = document.getElementById('espDeviceBadge');
                const text = document.getElementById('espStatusText');
                
                if (!badge || !text) return;

                const deviceId = data.device_id || 'esp32-kios-01';
                badge.title = `ID: ${deviceId} | Status: ${data.status}`;

                if (data.status === 'Online') {
                    badge.className = 'pos-badge esp-badge online';
                    text.innerText = 'Scanner: Online';
                } else if (data.status === 'Connecting') {
                    badge.className = 'pos-badge esp-badge connecting';
                    text.innerText = 'Scanner: Connecting...';
                } else {
                    badge.className = 'pos-badge esp-badge offline';
                    text.innerText = 'Scanner: Offline';
                }
            } catch (err) {
                const badge = document.getElementById('espDeviceBadge');
                const text = document.getElementById('espStatusText');
                if (badge && text) {
                    badge.className = 'pos-badge esp-badge offline';
                    text.innerText = 'Scanner: Offline';
                }
            }
        }

        // Panggil saat halaman dimuat & perbarui real-time setiap 1 detik
        checkEspDeviceStatus();
        setInterval(checkEspDeviceStatus, 1000);

        function hideModal() {
            if (pollTimer) { clearTimeout(pollTimer); pollTimer = null; }
            isPolling = false;
            if (window.receiptAutoCloseTimer) {
                clearTimeout(window.receiptAutoCloseTimer);
                window.receiptAutoCloseTimer = null;
            }
            if (window.receiptCountdownInterval) {
                clearInterval(window.receiptCountdownInterval);
                window.receiptCountdownInterval = null;
            }
            const finishBtn = document.querySelector('#modalSuccessState button');
            if (finishBtn) finishBtn.innerText = "Selesai & Kembali ke Menu";

            // Hapus pending order dari localStorage
            localStorage.removeItem('pendingKiosOrder');

            // Hanya kirim cancel jika pembayaran BELUM berhasil (bukan tutup setelah sukses)
            if (currentOrderId && !paymentCompleted) {
                fetch('/api/kios/cancel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: currentOrderId })
                }).catch(() => {});
            }
            document.getElementById('bioModal').classList.remove('active');
            currentOrderId = null;
            paymentCompleted = false; // Reset flag untuk pesanan berikutnya
        }

        // Pulihkan pop-up pembayaran jika halaman di-reload saat transaksi masih berjalan
        async function checkAndResumePendingOrder() {
            const saved = localStorage.getItem('pendingKiosOrder');
            if (!saved) return;

            let pending;
            try {
                pending = JSON.parse(saved);
            } catch(e) {
                localStorage.removeItem('pendingKiosOrder');
                return;
            }

            if (!pending || !pending.order_id) {
                localStorage.removeItem('pendingKiosOrder');
                return;
            }

            try {
                const res = await fetch(`/api/kios/order-status/${pending.order_id}?_t=${Date.now()}`, { cache: 'no-store' });
                const data = await res.json();

                if (!data.success || data.status === 'cancelled' || data.status === 'expired') {
                    // Pesanan sudah dibatalkan atau tidak ada di server
                    localStorage.removeItem('pendingKiosOrder');
                    return;
                }

                if (data.status === 'success') {
                    // Pesanan sudah berhasil dibayar
                    currentOrderId = pending.order_id;
                    handlePaymentSuccess(data.transaction || {
                        order_id: pending.order_id,
                        total_amount: pending.total,
                        student_name: data.student_name || 'Budi Santoso',
                        bank: data.bank || 'Bank BCA'
                    });
                    return;
                }

                // Masih berstatus pending: Tampilkan kembali modal pembayaran
                currentOrderId = pending.order_id;
                paymentCompleted = false;

                if (pending.cart && pending.cart.length > 0) {
                    cart = pending.cart;
                    renderCart();
                }

                document.getElementById('modalWaitingState').style.display = 'block';
                document.getElementById('modalSuccessState').style.display = 'none';
                document.getElementById('modalTotalAmount').innerText = 'Rp ' + Number(pending.total).toLocaleString('id-ID');
                document.getElementById('modalOrderBadge').innerText = 'Pesanan #' + currentOrderId;
                updatePaymentDisplayStatus(data.display_status || {
                    line1: 'TEMPELKAN SIDIK JARI',
                    line2: 'Silakan letakkan jari pada scanner di meja kios',
                    status: 'waiting',
                    dot: 'blue'
                });
                document.getElementById('bioModal').classList.add('active');

                // Lanjutkan polling status
                startPollingStatus();
            } catch (err) {
                console.error("Gagal melanjutkan pending order:", err);
            }
        }
    </script>
    <!-- On-Theme Toast Notification Container -->
    <div id="toastContainer" style="position: fixed; top: 24px; left: 50%; transform: translateX(-50%); z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; width: 100%; max-width: 420px; padding: 0 1rem;"></div>

</body>
</html>
