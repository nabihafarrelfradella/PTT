{{-- Shared CSS for all portal pages --}}
<style>
    :root {
        --bg-color: #f9fafb;
        --surface-color: #ffffff;
        --primary-color: #111827;
        --primary-hover: #374151;
        --accent-color: #2563eb;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; -webkit-tap-highlight-color: transparent; }
    body { background-color: var(--bg-color); color: var(--text-main); display: flex; height: 100vh; height: 100dvh; overflow: hidden; }
    
    .sidebar { width: 250px; background: var(--surface-color); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 2rem 1rem; flex-shrink: 0; transition: width 0.2s ease; }
    .sidebar-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; padding: 0 0.75rem; }
    .sidebar-logo img { max-height: 40px; }
    .sidebar-logo span { font-weight: 700; color: var(--primary-color); font-size: 1.1rem; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.85rem 1rem; border-radius: 8px; color: var(--text-muted); cursor: pointer; transition: all 0.2s; font-weight: 500; margin-bottom: 0.25rem; text-decoration: none; font-size: 0.95rem; }
    .nav-item:hover { background: #f3f4f6; color: var(--primary-color); }
    .nav-item.active { background: #eff6ff; color: var(--accent-color); font-weight: 600; }
    .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }
    .nav-logout { margin-top: auto; }
    .nav-logout button { display: flex; align-items: center; gap: 12px; padding: 0.85rem 1rem; border-radius: 8px; color: var(--text-muted); cursor: pointer; transition: all 0.2s; font-weight: 500; background: transparent; border: none; width: 100%; text-align: left; font-size: 0.95rem; }
    .nav-logout button:hover { background: #fef2f2; color: var(--danger-color); }
    .nav-logout button svg { width: 20px; height: 20px; flex-shrink: 0; }
    
    .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; min-width: 0; -webkit-overflow-scrolling: touch; }
    .header { padding: 1.5rem 2.5rem; background: var(--surface-color); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-shrink: 0; }
    .header h1 { font-size: 1.35rem; font-weight: 700; }
    .user-info { display: flex; align-items: center; gap: 0.75rem; }
    .avatar { width: 36px; height: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; overflow: hidden; flex-shrink: 0; }
    .avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .dashboard-area { padding: 2rem 2.5rem; flex: 1; }
    .section-title { font-size: 1.1rem; font-weight: 600; color: var(--primary-color); margin-bottom: 1.25rem; }
    
    /* Table Container - Selalu bisa digeser / scroll horizontal di tablet dan ponsel */
    .table-container { 
        background: var(--surface-color); 
        border: 1px solid var(--border-color); 
        border-radius: 12px; 
        overflow-x: auto; 
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch; 
        margin-bottom: 2rem; 
        width: 100%;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .table-container::-webkit-scrollbar {
        height: 6px;
    }
    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 9999px;
    }
    .table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        min-width: 680px; /* Menjamin kolom lengkap & bisa digeser mulus */
    }
    th, td { 
        padding: 0.85rem 1.25rem; 
        text-align: left; 
        border-bottom: 1px solid var(--border-color); 
        white-space: nowrap; 
    }
    th { background: #f9fafb; font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em; }
    td { font-size: 0.9rem; color: var(--primary-color); font-weight: 500; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }
    
    .status-badge { padding: 0.2rem 0.65rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-block; white-space: nowrap; }
    .status-badge.active, .status-badge.success { background: #d1fae5; color: var(--success-color); }
    .status-badge.warning, .status-badge.pending { background: #fef3c7; color: var(--warning-color); }
    .status-badge.danger { background: #fee2e2; color: var(--danger-color); }
    
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--primary-color); color: white; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; font-size: 0.875rem; text-decoration: none; white-space: nowrap; }
    .btn:hover { background: var(--primary-hover); }
    .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-main); }
    .btn-outline:hover { background: #f3f4f6; }
    .btn-success { background: var(--success-color); }
    .btn-success:hover { background: #059669; }
    .btn-danger { background: var(--danger-color); }
    .btn-danger:hover { background: #dc2626; }
    .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.8rem; }
    
    .form-card { background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 2rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.85rem; color: var(--text-main); }
    .form-control { width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none; transition: border-color 0.2s; }
    .form-control:focus { border-color: var(--accent-color); }
    
    .alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.9rem; font-weight: 500; }
    .alert-success { background: #d1fae5; color: #065f46; }
    .alert-danger { background: #fee2e2; color: #991b1b; }
    
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .modal-overlay.active { display: flex; }
    .modal-body { background: var(--surface-color); border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px; }
    
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
    .photo-preview, .menu-img-preview { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #f1f5f9; display: block; }

    /* Tablet Landscape & Portrait Responsive (769px - 1024px) */
    @media (max-width: 1024px) and (min-width: 769px) {
        .sidebar { width: 210px; padding: 1.5rem 0.75rem; }
        .sidebar-logo { margin-bottom: 1.5rem; padding: 0 0.5rem; }
        .nav-item { padding: 0.7rem 0.85rem; font-size: 0.875rem; gap: 10px; }
        .header { padding: 1.25rem 1.5rem; }
        .header h1 { font-size: 1.2rem; }
        .dashboard-area { padding: 1.25rem 1.5rem; }
        table { min-width: 650px; }
    }

    /* Mobile & Small Tablets Responsive (<= 768px) */
    @media (max-width: 768px) {
        body { flex-direction: column; height: auto; min-height: 100vh; min-height: 100dvh; overflow-y: auto; }
        .sidebar { width: 100%; height: auto; flex-direction: row; padding: 0.6rem 1rem; border-right: none; border-bottom: 1px solid var(--border-color); overflow-x: auto; -webkit-overflow-scrolling: touch; gap: 0.5rem; align-items: center; position: sticky; top: 0; z-index: 50; background: var(--surface-color); }
        .sidebar-logo { margin-bottom: 0; flex-shrink: 0; }
        .sidebar-logo span { display: none; }
        .sidebar-logo img { max-height: 32px; }
        .nav-item span, .nav-logout span { display: inline-block; font-size: 0.8rem; }
        .nav-item { padding: 0.45rem 0.75rem; margin-bottom: 0; flex-shrink: 0; border-radius: 9999px; border: 1px solid var(--border-color); }
        .nav-item.active { border-color: var(--accent-color); background: #eff6ff; }
        .nav-logout { margin-top: 0; margin-left: auto; }
        .nav-logout button { padding: 0.45rem 0.75rem; border-radius: 9999px; }
        .header, .dashboard-area { padding: 1rem 1.15rem; }
        .header h1 { font-size: 1.15rem; }
        table { min-width: 600px; }
        .section-header { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
        .form-card { padding: 1.25rem; }
    }
</style>
