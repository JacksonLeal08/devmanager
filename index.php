<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevManager - Sistema de Gestão de Desenvolvedores</title>
    
    <!-- Google Fonts & FontAwesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Variáveis & Reset */
        :root {
            /* Light Mode Palette */
            --bg-color: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-active: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-rgb: 79, 70, 229;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple-accent: #8b5cf6;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(226, 232, 240, 0.8);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        body.dark-mode {
            /* Dark Mode Palette */
            --bg-color: #0b0f19;
            --sidebar-bg: #111827;
            --sidebar-active: #1f2937;
            --sidebar-text: #9ca3af;
            --sidebar-text-active: #f9fafb;
            --card-bg: #1f2937;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border-color: #374151;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-rgb: 99, 102, 241;
            --success: #34d399;
            --danger: #f87171;
            --warning: #fbbf24;
            --info: #60a5fa;
            --purple-accent: #a78bfa;
            --glass-bg: rgba(31, 41, 55, 0.7);
            --glass-border: rgba(55, 65, 81, 0.8);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
        }
        
        body { 
            background-color: var(--bg-color); 
            color: var(--text-main); 
            display: flex; 
            height: 100vh; 
            overflow: hidden; 
            transition: background-color 0.3s, color 0.3s;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px; 
            background-color: var(--sidebar-bg); 
            color: var(--sidebar-text); 
            display: flex; 
            flex-direction: column; 
            transition: all 0.3s ease;
            border-right: 1px solid var(--border-color);
        }
        .sidebar-header { 
            padding: 24px; 
            font-size: 1.4rem; 
            font-weight: 700; 
            color: var(--sidebar-text-active);
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05); 
        }
        .sidebar-header i {
            background: linear-gradient(135deg, var(--primary), var(--purple-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-links { 
            list-style: none; 
            padding: 20px 12px; 
            flex: 1; 
        }
        .nav-item { 
            padding: 14px 16px; 
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            transition: all 0.2s ease; 
            font-weight: 500;
        }
        .nav-item i { 
            font-size: 1.1rem; 
            width: 20px;
            text-align: center;
        }
        .nav-item:hover { 
            background-color: rgba(255,255,255,0.03); 
            color: var(--sidebar-text-active);
        }
        .nav-item.active { 
            background-color: var(--primary); 
            color: white; 
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .btn-theme {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-theme:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text-active);
        }
        .btn-logout {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
            text-decoration: none;
        }
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.18) !important;
            color: var(--danger) !important;
        }

        /* Main Content Container */
        .main-content { 
            flex: 1; 
            overflow-y: auto; 
            padding: 40px; 
            transition: background-color 0.3s;
        }
        .page { 
            display: none; 
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); 
        }
        .page.active { 
            display: block; 
        }

        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(12px); } 
            to { opacity: 1; transform: translateY(0); } 
        }

        /* Typography & Utility Styles */
        h1 { 
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px; 
            color: var(--text-main); 
        }
        h2 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 20px;
            color: var(--text-main);
        }
        .header-actions { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
            flex-wrap: wrap; 
            gap: 15px;
        }
        
        /* Buttons with gradients */
        .btn { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.2s ease; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary), var(--purple-accent)); 
            color: white; 
            box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.2);
        }
        .btn-primary:hover { 
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(var(--primary-rgb), 0.3);
        }
        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .dark-mode .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .btn-danger { 
            background-color: var(--danger); 
            color: white; 
        }
        .btn-danger:hover { 
            opacity: 0.9; 
            transform: translateY(-1px);
        }
        .btn-sm { 
            padding: 6px 12px; 
            font-size: 0.8rem; 
            border-radius: 6px;
        }
        
        .text-danger { color: var(--danger) !important; font-weight: bold; }
        .text-success { color: var(--success) !important; font-weight: bold; }
        .text-muted { color: var(--text-muted) !important; }
        
        /* Form Inputs */
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid var(--border-color); 
            border-radius: 8px; 
            background-color: var(--card-bg);
            color: var(--text-main);
            margin-bottom: 18px; 
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        .form-control:focus { 
            outline: none; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); 
        }
        label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        
        /* Dashboard Stats Cards */
        .dashboard-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 24px; 
            margin-bottom: 40px; 
        }
        .card { 
            background: var(--card-bg); 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: var(--shadow); 
            border: 1px solid var(--border-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .card h3 { 
            font-size: 0.85rem; 
            color: var(--text-muted); 
            margin-bottom: 12px; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .card .value { 
            font-size: 2rem; 
            font-weight: 700; 
            color: var(--text-main); 
        }
        
        /* Data Tables styling */
        .table-container { 
            background: var(--card-bg); 
            border-radius: 12px; 
            box-shadow: var(--shadow); 
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 30px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            text-align: left; 
            font-size: 0.9rem;
        }
        th, td { 
            padding: 16px 20px; 
            border-bottom: 1px solid var(--border-color); 
        }
        th { 
            background-color: rgba(0, 0, 0, 0.02); 
            font-weight: 600; 
            color: var(--text-muted); 
        }
        .dark-mode th {
            background-color: rgba(255, 255, 255, 0.02); 
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr {
            transition: background-color 0.15s ease;
        }
        tr:hover { 
            background-color: rgba(0, 0, 0, 0.01); 
        }
        .dark-mode tr:hover {
            background-color: rgba(255, 255, 255, 0.01); 
        }

        /* Status Badges */
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            display: inline-block;
        }
        .status-andamento { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
        .status-concluido { background: rgba(16, 185, 129, 0.12); color: var(--success); }
        .status-pausado { background: rgba(100, 116, 139, 0.12); color: var(--text-muted); }
        .status-negociacao { background: rgba(79, 70, 229, 0.12); color: var(--primary); }
        .status-cancelado { background: rgba(239, 68, 68, 0.12); color: var(--danger); }

        .insight-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background: linear-gradient(135deg, rgba(121, 40, 202, 0.1), rgba(255, 0, 128, 0.1));
            color: var(--purple-accent);
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
            border: 1px solid rgba(121, 40, 202, 0.2);
            transition: all 0.2s;
        }
        .insight-badge:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, rgba(121, 40, 202, 0.2), rgba(255, 0, 128, 0.2));
        }

        /* Modals and Overlay */
        .modal-overlay { 
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0; 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(4px);
            display: none; 
            align-items: center; 
            justify-content: center; 
            z-index: 1000; 
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .modal-overlay.active { 
            display: flex; 
            opacity: 1;
        }
        .modal-content { 
            background: var(--card-bg); 
            padding: 32px; 
            border-radius: 16px; 
            width: 100%; 
            max-width: 540px; 
            max-height: 90vh; 
            overflow-y: auto; 
            position: relative; 
            box-shadow: var(--shadow-lg); 
            border: 1px solid var(--border-color);
            transform: scale(0.95);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        .modal-close { 
            position: absolute; 
            top: 20px; 
            right: 20px; 
            font-size: 1.5rem; 
            cursor: pointer; 
            color: var(--text-muted); 
            border: none; 
            background: none; 
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }
        .modal-close:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--text-main);
        }
        .dark-mode .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: 1 / -1; }

        /* Notification Toasts */
        .toast {
            position: fixed; 
            top: 24px; 
            right: 24px; 
            padding: 16px 24px; 
            border-radius: 10px;
            background: var(--card-bg); 
            color: var(--text-main); 
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--primary); 
            display: flex; 
            align-items: center; 
            gap: 12px;
            z-index: 10000; 
            transform: translateX(130%); 
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-weight: 600; 
            font-size: 0.9rem; 
            border: 1px solid var(--border-color);
        }
        .toast.active { transform: translateX(0); }
        .toast-success { border-left-color: var(--success); }
        .toast-error { border-left-color: var(--danger); }
        .toast-warning { border-left-color: var(--warning); }
        .toast i { font-size: 1.3rem; }
        .toast-success i { color: var(--success); }
        .toast-error i { color: var(--danger); }
        .toast-warning i { color: var(--warning); }

        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        /* Avatar Upload and Lightbox Styles */
        .avatar-preview-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
            box-shadow: var(--shadow);
            background: rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .avatar-preview-wrapper:hover {
            transform: scale(1.06);
            border-color: var(--purple-accent);
            box-shadow: var(--shadow-lg);
        }
        .avatar-hover-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(1px);
        }
        .avatar-preview-wrapper:hover .avatar-hover-overlay {
            opacity: 1;
        }

        /* Confirm Modal Button Styling & Animations */
        #confirm-btn-cancelar, #confirm-btn-confirmar {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #confirm-btn-cancelar:hover {
            background-color: var(--border-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        #confirm-btn-confirmar:hover {
            background-color: #dc2626 !important; /* Vermelho mais escuro no hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
        }
        #confirm-btn-cancelar:active, #confirm-btn-confirmar:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; flex-direction: row; align-items: center; justify-content: space-between; padding: 0 20px; border-right: none; border-bottom: 1px solid var(--border-color); }
            .sidebar-header { border-bottom: none; padding: 15px 0; }
            .nav-links { display: flex; padding: 0; overflow-x: auto; margin-left: 20px; }
            .nav-item { padding: 10px 14px; margin-bottom: 0; font-size: 0.85rem; white-space: nowrap; }
            .sidebar-footer { display: none; }
            .main-content { padding: 24px 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navegation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-laptop-code"></i>
            <span>DevManager</span>
        </div>
        
        <!-- Logged-in User Profile Card -->
        <div class="user-card" style="padding: 16px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 10px;">
            <?php if (!empty($_SESSION['foto_path'])): ?>
                <img src="<?php echo htmlspecialchars($_SESSION['foto_path']); ?>" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
            <?php else: ?>
                <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--sidebar-active); display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary); font-weight: bold; color: white;">
                    <?php echo strtoupper(substr($_SESSION['nome'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            <div style="overflow: hidden; flex: 1;">
                <div style="font-weight: 600; font-size: 0.85rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($_SESSION['nome']); ?></div>
                <div style="font-size: 0.75rem; color: var(--sidebar-text);"><?php echo htmlspecialchars($_SESSION['tipo_conta']); ?></div>
            </div>
        </div>

        <ul class="nav-links">
            <li class="nav-item active" data-target="dashboard"><i class="fa-solid fa-chart-pie"></i> Dashboard</li>
            <li class="nav-item" data-target="clientes"><i class="fa-solid fa-users"></i> Clientes</li>
            <li class="nav-item" data-target="projetos"><i class="fa-solid fa-cubes"></i> Projetos</li>
            <li class="nav-item" data-target="planos"><i class="fa-solid fa-layer-group"></i> Planos</li>
            <li class="nav-item" data-target="usuarios"><i class="fa-solid fa-user-gear"></i> Usuários</li>
        </ul>
        <div class="sidebar-footer">
            <button id="theme-toggle" class="btn-theme">
                <i class="fa-solid fa-moon"></i> <span>Modo Escuro</span>
            </button>
            <a href="logout.php" class="btn-theme btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Sair</span>
            </a>
        </div>
    </nav>

    <!-- Main Screens Content -->
    <main class="main-content">
        
        <!-- DASHBOARD PAGE -->
        <section id="dashboard" class="page active">
            <h1>Dashboard</h1>
            <div class="dashboard-grid">
                <div class="card">
                    <h3>Clientes Ativos</h3>
                    <div class="value" id="dash-clientes">0</div>
                </div>
                <div class="card">
                    <h3>Projetos em Andamento</h3>
                    <div class="value" id="dash-projetos-andamento">0</div>
                </div>
                <div class="card">
                    <h3>Concluídos (Último Mês)</h3>
                    <div class="value" id="dash-projetos-concluidos">0</div>
                </div>
                <div class="card">
                    <h3>Receita Estimada (Mês)</h3>
                    <div class="value" id="dash-receita">R$ 0,00</div>
                </div>
            </div>

            <h2>Próximos Prazos</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Projeto</th>
                            <th>Cliente</th>
                            <th>Prazo Final</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="table-dash-prazos">
                    </tbody>
                </table>
            </div>
        </section>

        <!-- CLIENTS PAGE -->
        <section id="clientes" class="page">
            <div class="header-actions">
                <h1>Gestão de Clientes</h1>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="busca-cliente" class="form-control" placeholder="Buscar cliente..." style="width:240px; margin-bottom:0;">
                    <button class="btn btn-primary" onclick="abrirModal('modal-cliente')">
                        <i class="fa-solid fa-plus"></i> Novo Cliente
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nome Fantasia</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="table-clientes">
                    </tbody>
                </table>
            </div>
        </section>

        <!-- PROJECTS PAGE -->
        <section id="projetos" class="page">
            <div class="header-actions">
                <h1>Gestão de Projetos</h1>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select id="filtro-status-projeto" class="form-control" style="width:180px; margin-bottom:0;">
                        <option value="">Todos os Status</option>
                        <option value="Em negociação">Em negociação</option>
                        <option value="Em andamento">Em andamento</option>
                        <option value="Pausado">Pausado</option>
                        <option value="Concluído">Concluído</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                    <button class="btn btn-primary" onclick="abrirModalProjeto()">
                        <i class="fa-solid fa-plus"></i> Novo Projeto
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Projeto</th>
                            <th>Cliente</th>
                            <th>Plano</th>
                            <th>Início</th>
                            <th>Prazo</th>
                            <th>Valor (R$)</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="table-projetos">
                    </tbody>
                </table>
            </div>
        </section>

        <!-- PLANS PAGE -->
        <section id="planos" class="page">
            <div class="header-actions">
                <h1>Tipos de Plano e Modalidades</h1>
                <button class="btn btn-primary" onclick="abrirModal('modal-tipo-plano')">
                    <i class="fa-solid fa-plus"></i> Novo Tipo de Plano
                </button>
            </div>
            <div id="container-planos">
            </div>
        </section>

        <!-- USER CONTROL PAGE -->
        <section id="usuarios" class="page">
            <div class="header-actions">
                <h1>Controle de Usuários</h1>
                <button class="btn btn-primary" onclick="abrirModalUsuario()">
                    <i class="fa-solid fa-user-plus"></i> Novo Usuário
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nome Completo</th>
                            <th>E-mail</th>
                            <th>Contato</th>
                            <th>Tipo de Conta</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="table-usuarios">
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- CLIENT MODAL -->
    <div class="modal-overlay" id="modal-cliente">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-cliente')">&times;</button>
            <h2 id="modal-cliente-title">Novo Cliente</h2>
            <form id="form-cliente">
                <input type="hidden" id="cli-id">
                <div class="form-grid">
                    <div class="full-width">
                        <label>Nome Fantasia *</label>
                        <input type="text" id="cli-nome" class="form-control" required>
                    </div>
                    <div class="full-width">
                        <label>Razão Social / CNPJ</label>
                        <input type="text" id="cli-razao" class="form-control">
                    </div>
                    <div>
                        <label>E-mail *</label>
                        <input type="email" id="cli-email" class="form-control" required>
                    </div>
                    <div>
                        <label>Telefone</label>
                        <input type="text" id="cli-telefone" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width" style="margin-top: 15px;">Salvar Cliente</button>
            </form>
        </div>
    </div>

    <!-- PROJECT MODAL -->
    <div class="modal-overlay" id="modal-projeto">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-projeto')">&times;</button>
            <h2 id="modal-projeto-title">Novo Projeto</h2>
            <form id="form-projeto">
                <input type="hidden" id="proj-id">
                <div class="form-grid">
                    <div class="full-width">
                        <label>Nome do Projeto *</label>
                        <input type="text" id="proj-nome" class="form-control" required>
                    </div>
                    <div>
                        <label>Cliente *</label>
                        <select id="proj-cliente" class="form-control" required></select>
                    </div>
                    <div>
                        <label>Status *</label>
                        <select id="proj-status" class="form-control" required>
                            <option value="Em negociação">Em negociação</option>
                            <option value="Em andamento">Em andamento</option>
                            <option value="Pausado">Pausado</option>
                            <option value="Concluído">Concluído</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="full-width">
                        <label>Plano / Modalidade *</label>
                        <select id="proj-modalidade" class="form-control" required></select>
                    </div>
                    <div>
                        <label>Data de Início *</label>
                        <input type="date" id="proj-inicio" class="form-control" required>
                    </div>
                    <div>
                        <label>Prazo Final</label>
                        <input type="date" id="proj-prazo" class="form-control">
                    </div>
                    <div class="full-width">
                        <label>Valor Total (R$) *</label>
                        <input type="number" step="0.01" id="proj-valor" class="form-control" required>
                    </div>
                    <div class="full-width">
                        <label>Descrição</label>
                        <textarea id="proj-descricao" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <!-- AI Insight integration fields -->
                    <div class="full-width" id="container-insight-ia" style="display:none; margin-top:5px; padding: 15px; border-radius: 8px; background: rgba(121, 40, 202, 0.08); border: 1px solid rgba(121, 40, 202, 0.15);">
                        <label style="color: var(--purple-accent); font-weight: bold; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                            <i class="fa-solid fa-lightbulb"></i> Insight Inteligente (Gemini IA)
                        </label>
                        <p id="proj-insight-texto" style="font-size:0.85rem; line-height:1.5; color: var(--text-main); font-style: italic; white-space: pre-line;"></p>
                    </div>
                    
                    <div class="full-width" id="container-btn-insight" style="display: none;">
                        <button type="button" id="btn-gerar-insight" class="btn btn-secondary full-width" style="background: linear-gradient(135deg, var(--purple-accent), #ec4899); color: white; display: flex; justify-content: center; gap: 8px; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.2);">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> <span>Gerar Insight por IA</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width" style="margin-top: 20px;">Salvar Projeto</button>
            </form>
        </div>
    </div>

    <!-- PLAN TYPE MODAL -->
    <div class="modal-overlay" id="modal-tipo-plano">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-tipo-plano')">&times;</button>
            <h2>Novo Tipo de Plano</h2>
            <form id="form-tipo-plano">
                <div class="form-grid">
                    <div class="full-width">
                        <label>Nome do Tipo de Plano (Ex: E-commerce) *</label>
                        <input type="text" id="tp-nome" class="form-control" required>
                    </div>
                    <div class="full-width">
                        <label>Descrição</label>
                        <textarea id="tp-descricao" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width" style="margin-top: 15px;">Salvar Tipo de Plano</button>
            </form>
        </div>
    </div>

    <!-- MODALITY PLAN MODAL -->
    <div class="modal-overlay" id="modal-modalidade">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-modalidade')">&times;</button>
            <h2>Nova Modalidade</h2>
            <form id="form-modalidade">
                <input type="hidden" id="mod-tipo-id">
                <div class="form-grid">
                    <div class="full-width">
                        <label>Nome da Modalidade (Ex: Premium - 40h/mês) *</label>
                        <input type="text" id="mod-nome" class="form-control" required>
                    </div>
                    <div>
                        <label>Valor Base (R$) *</label>
                        <input type="number" step="0.01" id="mod-valor" class="form-control" required>
                    </div>
                    <div>
                        <label>Tipo de Cobrança *</label>
                        <select id="mod-recorrente" class="form-control" required>
                            <option value="Projeto Único">Projeto Único</option>
                            <option value="Mensal">Mensal</option>
                            <option value="Trimestral">Trimestral</option>
                            <option value="Anual">Anual</option>
                        </select>
                    </div>
                    <div class="full-width">
                        <label>Duração Padrão (Dias) - *Use 0 para sem prazo*</label>
                        <input type="number" id="mod-duracao" class="form-control" value="0" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width" style="margin-top: 15px;">Salvar Modalidade</button>
            </form>
        </div>
    </div>

    <!-- AI INSIGHT VIEWING MODAL -->
    <div class="modal-overlay" id="modal-view-insight">
        <div class="modal-content" style="border-left: 6px solid var(--purple-accent);">
            <button class="modal-close" onclick="fecharModal('modal-view-insight')">&times;</button>
            <h2 style="color: var(--purple-accent); display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-lightbulb"></i> Insight IA do Projeto
            </h2>
            <div style="margin-top: 20px;">
                <h3 id="view-insight-projeto-nome" style="font-size: 1.15rem; margin-bottom: 8px; color: var(--text-main);"></h3>
                <p id="view-insight-texto" style="font-style: italic; line-height: 1.6; color: var(--text-main); background: rgba(139, 92, 246, 0.06); padding: 20px; border-radius: 10px; border: 1px solid rgba(139, 92, 246, 0.15); white-space: pre-line;"></p>
            </div>
            <button class="btn btn-primary full-width" style="margin-top: 25px;" onclick="fecharModal('modal-view-insight')">Fechar</button>
        </div>
    </div>

    <!-- USER MODAL -->
    <div class="modal-overlay" id="modal-usuario">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-usuario')">&times;</button>
            <h2 id="modal-usuario-title">Novo Usuário</h2>
            <form id="form-usuario" enctype="multipart/form-data">
                <input type="hidden" id="usr-id" name="id">
                
                <!-- Profile picture circle preview with interactive hover/zoom -->
                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; width: 100%;">
                    <div id="usr-foto-preview-container" class="avatar-preview-wrapper" title="Clique para ampliar">
                        <img id="usr-foto-preview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <div id="usr-foto-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 2.2rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="avatar-hover-overlay">
                            <i class="fa-solid fa-magnifying-glass-plus" style="margin-right: 4px;"></i> Ampliar
                        </div>
                    </div>
                    <label for="usr-foto" style="margin-top: 10px; cursor: pointer;" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-camera"></i> Escolher Foto
                    </label>
                    <input type="file" id="usr-foto" name="foto" accept="image/*" style="display: none;">
                </div>

                <div class="form-grid">
                    <div class="full-width">
                        <label>Nome Completo *</label>
                        <input type="text" id="usr-nome" name="nome" class="form-control" required>
                    </div>
                    <div>
                        <label>E-mail *</label>
                        <input type="email" id="usr-email" name="email" class="form-control" required>
                    </div>
                    <div>
                        <label>Contato</label>
                        <input type="text" id="usr-contato" name="contato" class="form-control" placeholder="(xx) xxxxx-xxxx">
                    </div>
                    <div>
                        <label>Senha * <span id="pwd-help-text" style="font-weight: normal; color: var(--text-muted);">(deixe em branco para manter)</span></label>
                        <input type="password" id="usr-senha" name="senha" class="form-control" required autocomplete="new-password">
                    </div>
                    <div>
                        <label>Tipo de Conta *</label>
                        <select id="usr-tipo" name="tipoConta" class="form-control" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Gerente">Gerente</option>
                            <option value="Desenvolvedor">Desenvolvedor</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width" style="margin-top: 15px;">Salvar Usuário</button>
            </form>
        </div>
    </div>

    <!-- CUSTOM CONFIRMATION DIALOG -->
    <div class="modal-overlay" id="modal-confirmacao">
        <div class="modal-content" style="max-width: 440px; border-top: 4px solid var(--danger); text-align: center; padding: 28px;">
            <div style="font-size: 3rem; color: var(--danger); margin-bottom: 12px; display: inline-flex; align-items: center; justify-content: center; width: 70px; height: 70px; border-radius: 50%; background: rgba(239, 68, 68, 0.1);">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 2.2rem;"></i>
            </div>
            <h2 style="font-size: 1.3rem; margin-top: 10px; margin-bottom: 10px; color: var(--text-main); font-weight: 700;">Confirmar Exclusão</h2>
            
            <!-- Logged in user info -->
            <div style="font-size: 0.8rem; background: rgba(0,0,0,0.02); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); color: var(--text-muted); margin-bottom: 18px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-user-shield" style="color: var(--primary);"></i> Operador: <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong> (<span style="font-style: italic;"><?php echo htmlspecialchars($_SESSION['tipo_conta']); ?></span>)
            </div>
            
            <p id="confirm-mensagem" style="font-size: 0.95rem; margin-bottom: 15px; color: var(--text-main); line-height: 1.5;"></p>
            <p style="font-size: 0.85rem; color: var(--danger); font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Essa ação é definitiva e não pode ser desfeita!
            </p>
            
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="btn btn-secondary" id="confirm-btn-cancelar" style="flex: 1; justify-content: center;">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-btn-confirmar" style="flex: 1; justify-content: center; background-color: var(--danger); color: white;">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- IMAGE LIGHTBOX MODAL -->
    <div class="modal-overlay" id="modal-lightbox" onclick="fecharModal('modal-lightbox')">
        <div class="modal-content" style="max-width: 500px; background: transparent; border: none; box-shadow: none; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: visible;" onclick="event.stopPropagation()">
            <button class="modal-close" style="color: white; background: rgba(0,0,0,0.5); top: -45px; right: 0;" onclick="fecharModal('modal-lightbox')">&times;</button>
            <div style="position: relative; width: 100%; max-height: 70vh; border-radius: 12px; overflow: hidden; background: #000; border: 2px solid var(--border-color); box-shadow: var(--shadow-lg);">
                <img id="lightbox-img" src="" style="width: 100%; height: auto; max-height: 70vh; display: block; object-fit: contain; transition: transform 0.3s ease;">
            </div>
            <!-- Slider for Zoom -->
            <div style="margin-top: 15px; background: rgba(15,23,42,0.85); padding: 10px 20px; border-radius: 30px; display: flex; align-items: center; gap: 12px; border: 1px solid var(--border-color); width: 220px; justify-content: center; backdrop-filter: blur(4px);">
                <i class="fa-solid fa-magnifying-glass-minus" style="color: white; cursor: pointer;" onclick="ajustarZoomLightbox(-0.25)"></i>
                <input type="range" id="lightbox-zoom-range" min="1" max="3" step="0.1" value="1" style="flex: 1; cursor: pointer; accent-color: var(--primary);">
                <i class="fa-solid fa-magnifying-glass-plus" style="color: white; cursor: pointer;" onclick="ajustarZoomLightbox(0.25)"></i>
            </div>
        </div>
    </div>

    <!-- Script Application Logic -->
    <script>
        // --- APP STATE ---
        let appData = { clientes: [], projetos: [], tiposPlano: [], modalidadesPlano: [], usuarios: [] };

        // --- CUSTOM CONFIRM DIALOG LOGIC ---
        function escapeHTML(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function showConfirmDialog(tipoElemento, nomeElemento, onConfirm) {
            const msgEl = document.getElementById('confirm-mensagem');
            if (msgEl) {
                msgEl.innerHTML = `Tem certeza que deseja excluir o ${tipoElemento} <strong>"${escapeHTML(nomeElemento)}"</strong>?`;
            }
            
            const btnConfirmar = document.getElementById('confirm-btn-confirmar');
            const btnCancelar = document.getElementById('confirm-btn-cancelar');
            
            if (btnConfirmar && btnCancelar) {
                const newBtnConfirmar = btnConfirmar.cloneNode(true);
                const newBtnCancelar = btnCancelar.cloneNode(true);
                
                btnConfirmar.parentNode.replaceChild(newBtnConfirmar, btnConfirmar);
                btnCancelar.parentNode.replaceChild(newBtnCancelar, btnCancelar);
                
                newBtnCancelar.addEventListener('click', () => {
                    fecharModal('modal-confirmacao');
                });
                
                newBtnConfirmar.addEventListener('click', () => {
                    fecharModal('modal-confirmacao');
                    onConfirm();
                });
            }
            
            abrirModal('modal-confirmacao');
        }

        // Toast notifications system
        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let icon = 'fa-circle-check';
            if (type === 'error') icon = 'fa-circle-xmark';
            if (type === 'warning') icon = 'fa-circle-exclamation';

            toast.innerHTML = `
                <i class="fa-solid ${icon}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('active'), 10);
            
            // Remove after delay
            setTimeout(() => {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // --- FETCH DATA FROM SERVER DB ---
        async function loadData() {
            try {
                const responseClientes = await fetch('/devmanager/api/clientes.php');
                const responsePlanos = await fetch('/devmanager/api/planos.php');
                const responseModalidades = await fetch('/devmanager/api/modalidades.php');
                const responseProjetos = await fetch('/devmanager/api/projetos.php');
                const responseUsuarios = await fetch('/devmanager/api/usuarios.php');

                if (!responseClientes.ok || !responsePlanos.ok || !responseModalidades.ok || !responseProjetos.ok || !responseUsuarios.ok) {
                    throw new Error('Erro nas requisições da API.');
                }

                const clientes = await responseClientes.json();
                const planos = await responsePlanos.json();
                const modalidades = await responseModalidades.json();
                const projetos = await responseProjetos.json();
                const usuarios = await responseUsuarios.json();

                // Map data types correctly to prevent string comparison errors
                appData.clientes = clientes.map(c => ({
                    ...c,
                    id: parseInt(c.id)
                }));
                appData.tiposPlano = planos.map(p => ({
                    ...p,
                    id: parseInt(p.id)
                }));
                appData.modalidadesPlano = modalidades.map(m => ({
                    ...m,
                    id: parseInt(m.id),
                    tipoPlanoId: parseInt(m.tipoPlanoId),
                    valorBase: parseFloat(m.valorBase),
                    duracaoDias: parseInt(m.duracaoDias)
                }));
                appData.projetos = projetos.map(p => ({
                    ...p,
                    id: parseInt(p.id),
                    clienteId: parseInt(p.clienteId),
                    modalidadeId: p.modalidadeId ? parseInt(p.modalidadeId) : null,
                    valorTotal: parseFloat(p.valorTotal)
                }));
                appData.usuarios = usuarios.map(u => ({
                    ...u,
                    id: parseInt(u.id)
                }));

                updateUI(); 
            } catch (error) {
                console.error("Erro na comunicação com a API:", error);
                showNotification("Não foi possível carregar os dados do servidor. Verifique a conexão com o banco de dados.", "error");
            }
        }

        // Helper formatter functions
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        }

        function formatDateBR(dateString) {
            if (!dateString) return '-';
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        // --- SPA NAVIGATION ---
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
                
                item.classList.add('active');
                document.getElementById(item.getAttribute('data-target')).classList.add('active');
            });
        });

        // --- MODAL UTILS ---
        function abrirModal(id) {
            document.getElementById(id).classList.add('active');
        }
        function fecharModal(id) {
            document.getElementById(id).classList.remove('active');
            if (id !== 'modal-modalidade' && id !== 'modal-view-insight') {
                const form = document.querySelector(`#${id} form`);
                if (form) form.reset();
            }
            if (id === 'modal-usuario') {
                const previewImg = document.getElementById('usr-foto-preview');
                const placeholderEl = document.getElementById('usr-foto-placeholder');
                if (previewImg && placeholderEl) {
                    previewImg.src = '';
                    previewImg.style.display = 'none';
                    placeholderEl.style.display = 'flex';
                }
            }
            if (id === 'modal-lightbox') {
                const lightboxImg = document.getElementById('lightbox-img');
                const zoomRange = document.getElementById('lightbox-zoom-range');
                if (lightboxImg) lightboxImg.style.transform = 'scale(1)';
                if (zoomRange) zoomRange.value = 1;
            }
        }

        // --- UI DRAW/UPDATE CONTROLLER ---
        function updateUI() {
            renderDashboard();
            renderClientes();
            renderProjetos();
            renderPlanos();
            renderUsuarios();
        }

        // --- DASHBOARD SCREEN ---
        function renderDashboard() {
            const hoje = new Date();
            const umMesAtras = new Date();
            umMesAtras.setMonth(hoje.getMonth() - 1);

            const ativos = appData.clientes.length;
            const emAndamento = appData.projetos.filter(p => p.status === 'Em andamento').length;
            
            // Finished projects in the last 30 days
            const concluidosUltimoMes = appData.projetos.filter(p => {
                if (p.status !== 'Concluído') return false;
                if (!p.prazoFinal) return false;
                const d = new Date(p.prazoFinal);
                return d >= umMesAtras && d <= hoje;
            }).length;
            
            // Estimated current active revenue
            const receitaMes = appData.projetos
                .filter(p => p.status === 'Em andamento')
                .reduce((acc, p) => acc + parseFloat(p.valorTotal), 0);

            document.getElementById('dash-clientes').innerText = ativos;
            document.getElementById('dash-projetos-andamento').innerText = emAndamento;
            document.getElementById('dash-projetos-concluidos').innerText = concluidosUltimoMes;
            document.getElementById('dash-receita').innerText = formatCurrency(receitaMes);

            // Upcomming Deadlines list
            const proximos = [...appData.projetos]
                .filter(p => p.status !== 'Concluído' && p.status !== 'Cancelado' && p.prazoFinal)
                .sort((a, b) => new Date(a.prazoFinal) - new Date(b.prazoFinal))
                .slice(0, 5);

            const tbody = document.getElementById('table-dash-prazos');
            tbody.innerHTML = '';
            
            if (proximos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-muted" style="text-align: center; padding: 20px;">Nenhum prazo futuro cadastrado.</td></tr>';
            } else {
                proximos.forEach(p => {
                    const cliente = appData.clientes.find(c => c.id === p.clienteId);
                    const isOverdue = new Date(p.prazoFinal) < hoje;
                    const classeStatus = p.status.toLowerCase().replace(' ', '-').replace('ç','c').replace('ã','a');
                    
                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${p.nome}</strong></td>
                            <td>${cliente ? cliente.nome : '-'}</td>
                            <td class="${isOverdue ? 'text-danger' : ''}">${formatDateBR(p.prazoFinal)}</td>
                            <td><span class="status-badge status-${classeStatus}">${p.status}</span></td>
                        </tr>
                    `;
                });
            }
        }

        // --- CLIENTS SCREEN ---
        function renderClientes() {
            const termoBusca = document.getElementById('busca-cliente').value.toLowerCase();
            const tbody = document.getElementById('table-clientes');
            tbody.innerHTML = '';
            
            const filtrados = appData.clientes.filter(c => c.nome.toLowerCase().includes(termoBusca));

            if (filtrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-muted" style="text-align: center; padding: 20px;">Nenhum cliente cadastrado.</td></tr>';
            } else {
                filtrados.forEach(c => {
                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${c.nome}</strong></td>
                            <td>${c.email}</td>
                            <td>${c.telefone || '-'}</td>
                            <td>${formatDateBR(c.dataCadastro)}</td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="editarCliente(${c.id})"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="excluirCliente(${c.id})"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            }
        }

        document.getElementById('busca-cliente').addEventListener('input', renderClientes);

        // Submit form (Create / Update)
        document.getElementById('form-cliente').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('cli-id').value;
            const cliente = {
                id: id ? parseInt(id) : undefined,
                nome: document.getElementById('cli-nome').value,
                razao: document.getElementById('cli-razao').value,
                email: document.getElementById('cli-email').value,
                telefone: document.getElementById('cli-telefone').value
            };

            const method = id ? 'PUT' : 'POST';
            const button = e.submitter;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<span class="spinner"></span> Gravando...`;

            try {
                const response = await fetch('/devmanager/api/clientes.php', {
                    method: method,
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(cliente)
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Erro ao salvar cliente.');
                }

                showNotification(resData.message || 'Cliente salvo com sucesso!', 'success');
                fecharModal('modal-cliente');
                await loadData();
            } catch (err) {
                showNotification(err.message, 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        window.editarCliente = function(id) {
            const cliente = appData.clientes.find(c => c.id === id);
            document.getElementById('modal-cliente-title').innerText = "Editar Cliente";
            document.getElementById('cli-id').value = cliente.id;
            document.getElementById('cli-nome').value = cliente.nome;
            document.getElementById('cli-razao').value = cliente.razao || '';
            document.getElementById('cli-email').value = cliente.email;
            document.getElementById('cli-telefone').value = cliente.telefone || '';
            abrirModal('modal-cliente');
        }

        window.excluirCliente = function(id) {
            const cliente = appData.clientes.find(c => c.id === id);
            const nomeCliente = cliente ? cliente.nome : `Cliente #${id}`;
            showConfirmDialog('cliente', nomeCliente, () => {
                fetch(`/devmanager/api/clientes.php?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(resObj => {
                        if (resObj.status !== 200) {
                            throw new Error(resObj.data.message || 'Erro ao excluir.');
                        }
                        showNotification(resObj.data.message, 'success');
                        loadData();
                    })
                    .catch(err => {
                        showNotification("Erro: " + err.message, 'error');
                    });
            });
        }

        // --- PROJECTS SCREEN ---
        function renderProjetos() {
            const filtroStatus = document.getElementById('filtro-status-projeto').value;
            const tbody = document.getElementById('table-projetos');
            tbody.innerHTML = '';
            const hoje = new Date();

            const filtrados = appData.projetos.filter(p => filtroStatus ? p.status === filtroStatus : true);

            if (filtrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-muted" style="text-align: center; padding: 20px;">Nenhum projeto cadastrado.</td></tr>';
            } else {
                filtrados.forEach(p => {
                    const cliente = appData.clientes.find(c => c.id === p.clienteId);
                    const mod = appData.modalidadesPlano.find(m => m.id === p.modalidadeId);
                    const isOverdue = new Date(p.prazoFinal) < hoje && !['Concluído', 'Cancelado'].includes(p.status);
                    const classeStatus = p.status.toLowerCase().replace(' ', '-').replace('ç','c').replace('ã','a');

                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <div><strong>${p.nome}</strong></div>
                                ${p.insightIa ? `<span class="insight-badge" onclick="visualizarInsight(${p.id})"><i class="fa-solid fa-lightbulb"></i> Ver Insight</span>` : ''}
                            </td>
                            <td>${cliente ? cliente.nome : '<span class="text-danger">Excluído</span>'}</td>
                            <td>${mod ? mod.nome : '<span class="text-muted">Personalizado</span>'}</td>
                            <td>${formatDateBR(p.dataInicio)}</td>
                            <td class="${isOverdue ? 'text-danger' : ''}">${formatDateBR(p.prazoFinal)}</td>
                            <td>${formatCurrency(p.valorTotal)}</td>
                            <td><span class="status-badge status-${classeStatus}">${p.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="editarProjeto(${p.id})"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="excluirProjeto(${p.id})"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            }
        }

        document.getElementById('filtro-status-projeto').addEventListener('change', renderProjetos);

        window.abrirModalProjeto = function() {
            document.getElementById('modal-projeto-title').innerText = "Novo Projeto";
            
            // Hide AI Insight fields for new projects (saving is required first)
            document.getElementById('container-insight-ia').style.display = 'none';
            document.getElementById('container-btn-insight').style.display = 'none';
            
            const selectCli = document.getElementById('proj-cliente');
            const selectMod = document.getElementById('proj-modalidade');
            
            selectCli.innerHTML = '<option value="">Selecione o Cliente</option>';
            appData.clientes.forEach(c => selectCli.innerHTML += `<option value="${c.id}">${c.nome}</option>`);
            
            selectMod.innerHTML = '<option value="">Modalidade Personalizada / Sob Demanda</option>';
            appData.tiposPlano.forEach(tp => {
                const mods = appData.modalidadesPlano.filter(m => m.tipoPlanoId === tp.id);
                if (mods.length > 0) {
                    const group = document.createElement('optgroup');
                    group.label = tp.nome;
                    mods.forEach(m => group.innerHTML += `<option value="${m.id}">${m.nome} (${formatCurrency(m.valorBase)})</option>`);
                    selectMod.appendChild(group);
                }
            });

            abrirModal('modal-projeto');
        }

        // Auto-calculating value and deadline based on chosen plan modality
        document.getElementById('proj-modalidade').addEventListener('change', calcularAutoProjeto);
        document.getElementById('proj-inicio').addEventListener('change', calcularAutoProjeto);

        function calcularAutoProjeto() {
            const modId = document.getElementById('proj-modalidade').value;
            const dataIn = document.getElementById('proj-inicio').value;
            
            if (modId) {
                const mod = appData.modalidadesPlano.find(m => m.id === parseInt(modId));
                if (mod) {
                    document.getElementById('proj-valor').value = mod.valorBase;
                    if (dataIn && mod.duracaoDias > 0) {
                        const d = new Date(dataIn);
                        d.setDate(d.getDate() + parseInt(mod.duracaoDias));
                        document.getElementById('proj-prazo').value = d.toISOString().split('T')[0];
                    } else if (mod.duracaoDias === 0) {
                        document.getElementById('proj-prazo').value = '';
                    }
                }
            }
        }

        // Save project form (Create / Update)
        document.getElementById('form-projeto').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('proj-id').value;
            
            const modVal = document.getElementById('proj-modalidade').value;
            const modalidadeId = modVal ? parseInt(modVal) : null;

            const projeto = {
                id: id ? parseInt(id) : undefined,
                nome: document.getElementById('proj-nome').value,
                clienteId: parseInt(document.getElementById('proj-cliente').value),
                modalidadeId: modalidadeId,
                status: document.getElementById('proj-status').value,
                dataInicio: document.getElementById('proj-inicio').value,
                prazoFinal: document.getElementById('proj-prazo').value || null,
                valorTotal: parseFloat(document.getElementById('proj-valor').value),
                descricao: document.getElementById('proj-descricao').value
            };

            const method = id ? 'PUT' : 'POST';
            const button = e.submitter;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<span class="spinner"></span> Gravando...`;

            try {
                const response = await fetch('/devmanager/api/projetos.php', {
                    method: method,
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(projeto)
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Erro ao salvar projeto.');
                }

                showNotification(resData.message || 'Projeto salvo!', 'success');
                fecharModal('modal-projeto');
                await loadData();
            } catch (err) {
                showNotification(err.message, 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        window.editarProjeto = function(id) {
            abrirModalProjeto(); // populate selection inputs first
            
            document.getElementById('modal-projeto-title').innerText = "Editar Projeto";
            const p = appData.projetos.find(p => p.id === id);
            
            document.getElementById('proj-id').value = p.id;
            document.getElementById('proj-nome').value = p.nome;
            document.getElementById('proj-cliente').value = p.clienteId;
            document.getElementById('proj-modalidade').value = p.modalidadeId || '';
            document.getElementById('proj-status').value = p.status;
            document.getElementById('proj-inicio').value = p.dataInicio;
            document.getElementById('proj-prazo').value = p.prazoFinal || '';
            document.getElementById('proj-valor').value = p.valorTotal;
            document.getElementById('proj-descricao').value = p.descricao || '';
            
            // Show AI Insight fields for existing projects
            document.getElementById('container-btn-insight').style.display = 'block';
            if (p.insightIa) {
                document.getElementById('proj-insight-texto').innerText = p.insightIa;
                document.getElementById('container-insight-ia').style.display = 'block';
            } else {
                document.getElementById('proj-insight-texto').innerText = '';
                document.getElementById('container-insight-ia').style.display = 'none';
            }

            // Wire up event for AI Insight button
            const btnInsight = document.getElementById('btn-gerar-insight');
            // Remove previous event listeners
            const newBtnInsight = btnInsight.cloneNode(true);
            btnInsight.parentNode.replaceChild(newBtnInsight, btnInsight);

            newBtnInsight.addEventListener('click', async () => {
                const projNome = document.getElementById('proj-nome').value;
                const projDesc = document.getElementById('proj-descricao').value;
                
                if (!projDesc) {
                    showNotification("Por favor, preencha a descrição do projeto para gerar insights mais assertivos.", "warning");
                    return;
                }

                newBtnInsight.disabled = true;
                const span = newBtnInsight.querySelector('span');
                span.innerText = "Analisando projeto...";
                const icon = newBtnInsight.querySelector('i');
                icon.className = "fa-solid fa-spinner fa-spin";

                try {
                    const response = await fetch('/devmanager/api/ai_insight.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            projetoId: p.id,
                            nome: projNome,
                            descricao: projDesc
                        })
                    });

                    const resData = await response.json();

                    if (!response.ok) {
                        throw new Error(resData.message || 'Erro ao gerar insight.');
                    }

                    showNotification('Insight inteligente gerado!', 'success');
                    
                    // Display it in the modal
                    document.getElementById('proj-insight-texto').innerText = resData.insight;
                    document.getElementById('container-insight-ia').style.display = 'block';
                    
                    // Update in-memory state
                    p.insightIa = resData.insight;
                    
                    // Re-render table on background
                    renderProjetos();
                } catch (err) {
                    showNotification(err.message, 'error');
                } finally {
                    newBtnInsight.disabled = false;
                    span.innerText = "Gerar Insight por IA";
                    icon.className = "fa-solid fa-wand-magic-sparkles";
                }
            });
        }

        window.excluirProjeto = function(id) {
            const proj = appData.projetos.find(p => p.id === id);
            const nomeProjeto = proj ? proj.nome : `Projeto #${id}`;
            showConfirmDialog('projeto', nomeProjeto, () => {
                fetch(`/devmanager/api/projetos.php?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(resObj => {
                        if (resObj.status !== 200) {
                            throw new Error(resObj.data.message || 'Erro ao excluir.');
                        }
                        showNotification(resObj.data.message, 'success');
                        loadData();
                    })
                    .catch(err => {
                        showNotification("Erro: " + err.message, 'error');
                    });
            });
        }

        window.visualizarInsight = function(id) {
            const p = appData.projetos.find(p => p.id === id);
            if (p && p.insightIa) {
                document.getElementById('view-insight-projeto-nome').innerText = p.nome;
                document.getElementById('view-insight-texto').innerText = p.insightIa;
                abrirModal('modal-view-insight');
            }
        }

        // --- PLANS AND MODALITIES SCREEN ---
        function renderPlanos() {
            const container = document.getElementById('container-planos');
            container.innerHTML = '';

            if (appData.tiposPlano.length === 0) {
                container.innerHTML = `<div class="card text-muted" style="text-align: center; padding: 40px;">
                    <i class="fa-solid fa-layer-group" style="font-size: 2.5rem; margin-bottom: 15px; color: var(--border-color);"></i>
                    <p>Nenhum tipo de plano cadastrado ainda.</p>
                </div>`;
                return;
            }

            appData.tiposPlano.forEach(tp => {
                const modalidades = appData.modalidadesPlano.filter(m => m.tipoPlanoId === tp.id);
                let modalidadesHTML = '';

                if (modalidades.length === 0) {
                    modalidadesHTML = `<p class="text-muted" style="padding: 20px; font-style: italic; font-size: 0.85rem;"><i class="fa-solid fa-triangle-exclamation"></i> Nenhuma modalidade cadastrada para este plano.</p>`;
                } else {
                    modalidadesHTML = `
                        <div class="table-container" style="margin-top: 15px; background: rgba(0, 0, 0, 0.01);">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Modalidade</th>
                                        <th>Valor</th>
                                        <th>Recorrência</th>
                                        <th>Duração Padrão</th>
                                        <th style="width: 80px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${modalidades.map(m => `
                                        <tr>
                                            <td><strong>${m.nome}</strong></td>
                                            <td>${formatCurrency(m.valorBase)}</td>
                                            <td><span class="status-badge status-negociacao">${m.recorrente}</span></td>
                                            <td>${m.duracaoDias > 0 ? m.duracaoDias + ' dias' : '<span class="text-muted">Sem prazo</span>'}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="excluirModalidade(${m.id})"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                container.innerHTML += `
                    <div class="card" style="margin-bottom: 25px; padding: 28px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; flex-wrap: wrap; gap: 15px;">
                            <div>
                                <h2 style="margin-bottom: 5px; font-size: 1.25rem;">${tp.nome}</h2>
                                <p style="color: var(--text-muted); font-size: 0.9rem;">${tp.descricao || 'Sem descrição cadastrada.'}</p>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-sm btn-primary" onclick="abrirModalModalidade(${tp.id})"><i class="fa-solid fa-plus"></i> Modalidade</button>
                                <button class="btn btn-sm btn-danger" onclick="excluirTipoPlano(${tp.id})"><i class="fa-solid fa-trash"></i> Excluir</button>
                            </div>
                        </div>
                        ${modalidadesHTML}
                    </div>
                `;
            });
        }

        // Save plan type (Create)
        document.getElementById('form-tipo-plano').addEventListener('submit', async function(e) {
            e.preventDefault();
            const tp = {
                nome: document.getElementById('tp-nome').value,
                descricao: document.getElementById('tp-descricao').value
            };

            const button = e.submitter;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<span class="spinner"></span> Gravando...`;

            try {
                const response = await fetch('/devmanager/api/planos.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(tp)
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Erro ao criar tipo de plano.');
                }

                showNotification('Tipo de plano criado!', 'success');
                fecharModal('modal-tipo-plano');
                await loadData();
            } catch (err) {
                showNotification(err.message, 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        window.excluirTipoPlano = function(id) {
            const plano = appData.tiposPlano.find(p => p.id === id);
            const nomePlano = plano ? plano.nome : `Plano #${id}`;
            showConfirmDialog('plano de cobrança', nomePlano, () => {
                fetch(`/devmanager/api/planos.php?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(resObj => {
                        if (resObj.status !== 200) {
                            throw new Error(resObj.data.message || 'Erro ao excluir.');
                        }
                        showNotification(resObj.data.message, 'success');
                        loadData();
                    })
                    .catch(err => {
                        showNotification("Erro: " + err.message, 'error');
                    });
            });
        }

        window.abrirModalModalidade = function(tipoId) {
            document.getElementById('form-modalidade').reset();
            document.getElementById('mod-tipo-id').value = tipoId;
            abrirModal('modal-modalidade');
        }

        // Save plan modality (Create)
        document.getElementById('form-modalidade').addEventListener('submit', async function(e) {
            e.preventDefault();
            const mod = {
                tipoPlanoId: parseInt(document.getElementById('mod-tipo-id').value),
                nome: document.getElementById('mod-nome').value,
                valorBase: parseFloat(document.getElementById('mod-valor').value),
                recorrente: document.getElementById('mod-recorrente').value,
                duracaoDias: parseInt(document.getElementById('mod-duracao').value)
            };

            const button = e.submitter;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<span class="spinner"></span> Gravando...`;

            try {
                const response = await fetch('/devmanager/api/modalidades.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(mod)
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Erro ao criar modalidade.');
                }

                showNotification('Modalidade de plano criada com sucesso!', 'success');
                fecharModal('modal-modalidade');
                await loadData();
            } catch (err) {
                showNotification(err.message, 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        window.excluirModalidade = function(id) {
            const modalidade = appData.modalidadesPlano.find(m => m.id === id);
            const nomeModalidade = modalidade ? modalidade.nome : `Modalidade #${id}`;
            showConfirmDialog('modalidade de plano', nomeModalidade, () => {
                fetch(`/devmanager/api/modalidades.php?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(resObj => {
                        if (resObj.status !== 200) {
                            throw new Error(resObj.data.message || 'Erro ao excluir.');
                        }
                        showNotification(resObj.data.message, 'success');
                        loadData();
                    })
                    .catch(err => {
                        showNotification("Erro: " + err.message, 'error');
                    });
            });
        }

        // --- USERS SCREEN ---
        function renderUsuarios() {
            const tbody = document.getElementById('table-usuarios');
            if (!tbody) return;
            tbody.innerHTML = '';

            if (appData.usuarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-muted" style="text-align: center; padding: 20px;">Nenhum usuário cadastrado.</td></tr>';
            } else {
                appData.usuarios.forEach(u => {
                    let photoHTML = '';
                    if (u.fotoPath) {
                        photoHTML = `<img src="${u.fotoPath}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color);">`;
                    } else {
                        photoHTML = `<div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(var(--primary-rgb), 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">${u.nome.charAt(0).toUpperCase()}</div>`;
                    }

                    let badgeClass = 'status-pausado';
                    if (u.tipoConta === 'Administrador') badgeClass = 'status-cancelado';
                    else if (u.tipoConta === 'Gerente') badgeClass = 'status-andamento';
                    else if (u.tipoConta === 'Desenvolvedor') badgeClass = 'status-negociacao';

                    tbody.innerHTML += `
                        <tr>
                            <td style="width: 60px;">${photoHTML}</td>
                            <td><strong>${u.nome}</strong></td>
                            <td>${u.email}</td>
                            <td>${u.contato || '-'}</td>
                            <td><span class="status-badge ${badgeClass}">${u.tipoConta}</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="editarUsuario(${u.id})"><i class="fa-solid fa-user-pen"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="excluirUsuario(${u.id})"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            }
        }

        window.abrirModalUsuario = function() {
            document.getElementById('modal-usuario-title').innerText = "Novo Usuário";
            document.getElementById('form-usuario').reset();
            document.getElementById('usr-id').value = '';
            document.getElementById('usr-senha').required = true;
            document.getElementById('pwd-help-text').style.display = 'none';
            
            // Reset photo preview to empty / placeholder state
            const previewImg = document.getElementById('usr-foto-preview');
            const placeholderEl = document.getElementById('usr-foto-placeholder');
            if (previewImg && placeholderEl) {
                previewImg.src = '';
                previewImg.style.display = 'none';
                placeholderEl.style.display = 'flex';
            }
            
            abrirModal('modal-usuario');
        }

        window.editarUsuario = function(id) {
            document.getElementById('modal-usuario-title').innerText = "Editar Usuário";
            document.getElementById('form-usuario').reset();
            
            const u = appData.usuarios.find(usr => usr.id === id);
            if (u) {
                document.getElementById('usr-id').value = u.id;
                document.getElementById('usr-nome').value = u.nome;
                document.getElementById('usr-email').value = u.email;
                document.getElementById('usr-contato').value = u.contato || '';
                document.getElementById('usr-tipo').value = u.tipoConta;
                document.getElementById('usr-senha').required = false;
                document.getElementById('pwd-help-text').style.display = 'inline';
                
                // Show existing user photo preview if available
                const previewImg = document.getElementById('usr-foto-preview');
                const placeholderEl = document.getElementById('usr-foto-placeholder');
                if (previewImg && placeholderEl) {
                    if (u.fotoPath) {
                        previewImg.src = u.fotoPath;
                        previewImg.style.display = 'block';
                        placeholderEl.style.display = 'none';
                    } else {
                        previewImg.src = '';
                        previewImg.style.display = 'none';
                        placeholderEl.style.display = 'flex';
                    }
                }
                
                abrirModal('modal-usuario');
            }
        }

        document.getElementById('form-usuario').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('usr-id').value;
            const password = document.getElementById('usr-senha').value;
            
            if (!id && !password) {
                showNotification("A senha é obrigatória para cadastrar novos usuários.", "warning");
                return;
            }

            const formData = new FormData(this);
            const button = e.submitter;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<span class="spinner"></span> Gravando...`;

            try {
                const response = await fetch('/devmanager/api/usuarios.php', {
                    method: 'POST',
                    body: formData
                });

                const resData = await response.json();

                if (!response.ok) {
                    throw new Error(resData.message || 'Erro ao salvar usuário.');
                }

                showNotification(resData.message || 'Usuário salvo com sucesso!', 'success');
                fecharModal('modal-usuario');
                await loadData();
            } catch (err) {
                showNotification(err.message, 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        window.excluirUsuario = function(id) {
            // Check if current user is deleting themselves
            const currentEmail = '<?php echo $_SESSION['email']; ?>';
            const u = appData.usuarios.find(usr => usr.id === id);
            
            if (u && u.email === currentEmail) {
                showNotification("Você não pode excluir sua própria conta enquanto estiver logado.", "warning");
                return;
            }

            const nomeUsuario = u ? u.nome : `Usuário #${id}`;
            showConfirmDialog('usuário', nomeUsuario, () => {
                fetch(`/devmanager/api/usuarios.php?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(resObj => {
                        if (resObj.status !== 200) {
                            throw new Error(resObj.data.message || 'Erro ao excluir.');
                        }
                        showNotification(resObj.data.message, 'success');
                        loadData();
                    })
                    .catch(err => {
                        showNotification("Erro: " + err.message, 'error');
                    });
            });
        }

        // --- THEME SELECTOR SYSTEM (DARK / LIGHT) ---
        const themeToggle = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            document.body.classList.add('dark-mode');
            themeToggle.innerHTML = `<i class="fa-solid fa-sun" style="color: var(--warning);"></i> <span>Modo Claro</span>`;
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            let theme = 'light';
            
            if (document.body.classList.contains('dark-mode')) {
                theme = 'dark';
                themeToggle.innerHTML = `<i class="fa-solid fa-sun" style="color: var(--warning);"></i> <span>Modo Claro</span>`;
            } else {
                themeToggle.innerHTML = `<i class="fa-solid fa-moon"></i> <span>Modo Escuro</span>`;
            }
            
            localStorage.setItem('theme', theme);
        });

        // --- USER PHOTO PREVIEW & LIGHTBOX ZOOM SYSTEM ---
        const fileInput = document.getElementById('usr-foto');
        const imgPreview = document.getElementById('usr-foto-preview');
        const placeholder = document.getElementById('usr-foto-placeholder');
        const previewContainer = document.getElementById('usr-foto-preview-container');
        const lightboxModal = document.getElementById('modal-lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const zoomRange = document.getElementById('lightbox-zoom-range');

        // FileReader preview when file input changes
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (imgPreview && placeholder) {
                            imgPreview.src = e.target.result;
                            imgPreview.style.display = 'block';
                            placeholder.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Open Lightbox Zoom Modal when clicking the preview wrapper
        if (previewContainer) {
            previewContainer.addEventListener('click', function() {
                if (imgPreview && imgPreview.src && imgPreview.style.display !== 'none') {
                    if (lightboxImg && zoomRange) {
                        lightboxImg.src = imgPreview.src;
                        zoomRange.value = 1;
                        lightboxImg.style.transform = 'scale(1)';
                        abrirModal('modal-lightbox');
                    }
                }
            });
        }

        // Lightbox Slider range input event mapping to CSS scale transform
        if (zoomRange) {
            zoomRange.addEventListener('input', function() {
                if (lightboxImg) {
                    lightboxImg.style.transform = `scale(${this.value})`;
                }
            });
        }

        // Lightbox programmatical adjustment via +/- zoom buttons
        window.ajustarZoomLightbox = function(delta) {
            if (zoomRange && lightboxImg) {
                let newVal = parseFloat(zoomRange.value) + delta;
                if (newVal < 1) newVal = 1;
                if (newVal > 3) newVal = 3;
                zoomRange.value = newVal;
                lightboxImg.style.transform = `scale(${newVal})`;
            }
        }

        // --- APP INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>