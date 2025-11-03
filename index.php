<?php
/**
 * DvloAsiaCode - 类似 GitHub Pages 的静态网站托管系统
 * 
 * @copyright Copyright (c) 2024 DvloAsiaCode
 * @license MIT
 */

// 自动加载类文件
spl_autoload_register(function ($class) {
    $paths = [
        'config/' . $class . '.php',
        'models/' . $class . '.php', 
        'utils/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// 错误处理
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// 启动会话
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class DvloAsiaCode {
    private $userModel;
    private $projectModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->projectModel = new Project();
    }
    
    public function run() {
        try {
            $action = $_GET['action'] ?? 'home';
            
            switch ($action) {
                case 'register':
                    $this->handleRegister();
                    break;
                case 'login':
                    $this->handleLogin();
                    break;
                case 'logout':
                    $this->handleLogout();
                    break;
                case 'dashboard':
                    $this->handleDashboard();
                    break;
                case 'create_project':
                    $this->handleCreateProject();
                    break;
                case 'upload':
                    $this->handleUpload();
                    break;
                case 'view':
                    $this->handleViewSite();
                    break;
                case 'delete':
                    $this->handleDeleteProject();
                    break;
                case 'files':
                    $this->handleProjectFiles();
                    break;
                default:
                    $this->handleHome();
            }
        } catch (Exception $e) {
            $this->renderError($e->getMessage());
        }
    }
    
    private function handleHome() {
        echo Styles::renderHeader('DvloAsiaCode - 静态网站托管平台');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <div>
                    <a href="?action=login" class="btn btn-text">登录</a>
                    <a href="?action=register" class="btn btn-primary">立即开始</a>
                </div>
            </div>
        </header>
        
        <main>
            <div class="container">
                <div class="empty-state" style="padding: 120px 20px;">
                    <div class="empty-state-icon">🚀</div>
                    <h1 style="font-size: 48px; font-weight: 300; margin-bottom: 24px; color: var(--text-primary);">
                        DvloAsiaCode
                    </h1>
                    <p style="font-size: 20px; color: var(--text-secondary); margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                        像 GitHub Pages 一样简单、快速、免费的静态网站托管平台
                    </p>
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <a href="?action=register" class="btn btn-primary" style="padding: 16px 32px; font-size: 16px;">
                            免费注册
                        </a>
                        <a href="?action=login" class="btn btn-secondary" style="padding: 16px 32px; font-size: 16px;">
                            立即登录
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-3" style="margin: 80px 0;">
                    <div class="card" style="padding: 32px; text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
                        <h3 style="margin-bottom: 12px; font-weight: 500;">快速部署</h3>
                        <p style="color: var(--text-secondary);">上传文件后立即生效，无需等待</p>
                    </div>
                    <div class="card" style="padding: 32px; text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;">🔒</div>
                        <h3 style="margin-bottom: 12px; font-weight: 500;">安全可靠</h3>
                        <p style="color: var(--text-secondary);">基于 PHP 和 SQLite，稳定运行</p>
                    </div>
                    <div class="card" style="padding: 32px; text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;">🎯</div>
                        <h3 style="margin-bottom: 12px; font-weight: 500;">简单易用</h3>
                        <p style="color: var(--text-secondary);">直观的界面，轻松管理项目</p>
                    </div>
                </div>
            </div>
        </main>';
        echo Styles::renderFooter();
    }
    
    private function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $email = trim($_POST['email'] ?? '');
            
            if ($this->userModel->register($username, $password, $email)) {
                header('Location: ?action=login&registered=1');
                exit;
            }
        }
        
        $error = $_GET['error'] ?? '';
        
        echo Styles::renderHeader('注册 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=home" class="btn btn-text">返回首页</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 400px; margin: 60px auto; padding: 40px;">
                <h2 style="font-size: 24px; font-weight: 400; margin-bottom: 8px;">创建账户</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 32px;">注册 DvloAsiaCode 账户</p>
                
                ' . ($error ? '<div class="message message-error">' . htmlspecialchars($error) . '</div>' : '') . '
                
                <form method="POST">
                    <div class="form-group">
                        <label>用户名</label>
                        <input type="text" name="username" class="form-input" required placeholder="输入用户名" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>邮箱地址</label>
                        <input type="email" name="email" class="form-input" required placeholder="输入邮箱" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <input type="password" name="password" class="form-input" required placeholder="输入密码" minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px;">创建账户</button>
                </form>
                
                <div style="text-align: center; margin-top: 24px;">
                    <span style="color: var(--text-secondary); font-size: 14px;">已有账户？</span>
                    <a href="?action=login" style="color: var(--primary-color); font-size: 14px; text-decoration: none; margin-left: 8px;">立即登录</a>
                </div>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $this->userModel->updateLastLogin($user['id']);
                header('Location: ?action=dashboard');
                exit;
            } else {
                $error = '用户名或密码错误';
            }
        }
        
        $message = isset($_GET['registered']) ? '<div class="message message-success">注册成功！请登录</div>' : '';
        $error = $error ?? '';
        
        echo Styles::renderHeader('登录 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=home" class="btn btn-text">返回首页</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 400px; margin: 60px auto; padding: 40px;">
                <h2 style="font-size: 24px; font-weight: 400; margin-bottom: 8px;">登录账户</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 32px;">登录到 DvloAsiaCode</p>
                
                ' . $message . '
                ' . ($error ? '<div class="message message-error">' . htmlspecialchars($error) . '</div>' : '') . '
                
                <form method="POST">
                    <div class="form-group">
                        <label>用户名</label>
                        <input type="text" name="username" class="form-input" required placeholder="输入用户名">
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <input type="password" name="password" class="form-input" required placeholder="输入密码">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px;">登录</button>
                </form>
                
                <div style="text-align: center; margin-top: 24px;">
                    <span style="color: var(--text-secondary); font-size: 14px;">还没有账户？</span>
                    <a href="?action=register" style="color: var(--primary-color); font-size: 14px; text-decoration: none; margin-left: 8px;">立即注册</a>
                </div>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleLogout() {
        session_destroy();
        header('Location: ?action=home');
        exit;
    }
    
    private function handleDashboard() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $projects = $this->projectModel->getUserProjects($userId);
        
        echo Styles::renderHeader('控制面板 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="color: var(--text-secondary); font-size: 14px;">欢迎，' . htmlspecialchars($_SESSION['username']) . '</span>
                    <a href="?action=logout" class="btn btn-text">退出</a>
                </div>
            </div>
        </header>
        
        <div class="container">
            <div style="margin: 40px 0 32px;">
                <h1 style="font-size: 32px; font-weight: 400; margin-bottom: 8px;">我的项目</h1>
                <p style="color: var(--text-secondary);">管理你的静态网站项目</p>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <a href="?action=create_project" class="btn btn-primary">
                    <span>+</span> 创建新项目
                </a>
                <div style="color: var(--text-secondary); font-size: 14px;">
                    共 ' . count($projects) . ' 个项目
                </div>
            </div>
            
            ' . (empty($projects) ? '
            <div class="empty-state">
                <div class="empty-state-icon">📁</div>
                <h3 style="margin-bottom: 12px; font-weight: 400;">暂无项目</h3>
                <p style="color: var(--text-secondary); margin-bottom: 24px;">创建一个项目开始托管你的网站</p>
                <a href="?action=create_project" class="btn btn-primary">创建第一个项目</a>
            </div>' : '
            <div class="grid grid-2">
            ' . implode('', array_map(function($project) {
                return '
                <div class="project-card">
                    <div class="project-name">' . htmlspecialchars($project['name']) . '</div>
                    <div class="project-description">' . htmlspecialchars($project['description'] ?: '暂无描述') . '</div>
                    <div class="project-url">
                        <a href="?action=view&subdomain=' . urlencode($project['subdomain']) . '" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                            ' . htmlspecialchars($project['subdomain']) . '.dvloasia.com
                        </a>
                    </div>
                    <div class="project-actions">
                        <a href="?action=upload&project_id=' . $project['id'] . '" class="btn btn-secondary btn-sm">上传文件</a>
                        <a href="?action=files&project_id=' . $project['id'] . '" class="btn btn-secondary btn-sm">查看文件</a>
                        <a href="?action=delete&project_id=' . $project['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'确定删除此项目？\')">删除</a>
                    </div>
                </div>';
            }, $projects)) . '
            </div>') . '
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleCreateProject() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            try {
                if ($this->projectModel->create($_SESSION['user_id'], $name, $description)) {
                    header('Location: ?action=dashboard');
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $error = $error ?? '';
        
        echo Styles::renderHeader('创建项目 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=dashboard" class="btn btn-text">← 返回控制面板</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 40px auto; padding: 40px;">
                <h2 style="font-size: 24px; font-weight: 400; margin-bottom: 8px;">创建新项目</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 32px;">配置你的静态网站托管项目</p>
                
                ' . ($error ? '<div class="message message-error">' . htmlspecialchars($error) . '</div>' : '') . '
                
                <form method="POST">
                    <div class="form-group">
                        <label>项目名称</label>
                        <input type="text" name="name" class="form-input" required placeholder="my-awesome-site" pattern="[a-zA-Z0-9-_ ]+" title="只能包含字母、数字、空格、连字符和下划线">
                        <small style="color: var(--text-secondary); font-size: 12px; display: block; margin-top: 4px;">
                            将自动生成子域名：username-项目名称.dvloasia.com
                        </small>
                    </div>
                    <div class="form-group">
                        <label>项目描述（可选）</label>
                        <textarea name="description" class="form-input form-textarea" placeholder="简单描述一下你的项目" maxlength="500"></textarea>
                    </div>
                    
                    <div class="message message-info" style="font-size: 13px;">
                        <strong>项目说明：</strong><br>
                        • 支持 HTML、CSS、JS、图片等静态文件<br>
                        • 自动生成唯一的访问地址<br>
                        • 文件上传后立即生效
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">创建项目</button>
                        <a href="?action=dashboard" class="btn btn-secondary">取消</a>
                    </div>
                </form>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleUpload() {
        $this->requireLogin();
        
        $projectId = (int)($_GET['project_id'] ?? 0);
        $project = $this->projectModel->getProjectById($projectId, $_SESSION['user_id']);
        
        if (!$project) {
            header('Location: ?action=dashboard');
            exit;
        }
        
        $result = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
            $result = $this->projectModel->uploadFiles($project['subdomain'], $_FILES['files']);
        }
        
        echo Styles::renderHeader('上传文件 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=dashboard" class="btn btn-text">← 返回控制面板</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 40px auto; padding: 40px;">
                <h2 style="font-size: 24px; font-weight: 400; margin-bottom: 8px;">上传文件</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">
                    项目：<strong>' . htmlspecialchars($project['name']) . '</strong>
                </p>
                
                ' . ($result ? '
                    ' . (!empty($result['uploaded']) ? '<div class="message message-success">成功上传 ' . count($result['uploaded']) . ' 个文件：' . htmlspecialchars(implode(', ', $result['uploaded'])) . '</div>' : '') . '
                    ' . (!empty($result['errors']) ? '<div class="message message-error">上传错误：' . htmlspecialchars(implode('; ', $result['errors'])) . '</div>' : '') . '
                ' : '') . '
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>选择文件</label>
                        <input type="file" name="files[]" multiple required class="form-input" 
                               accept=".html,.htm,.css,.js,.json,.txt,.jpg,.jpeg,.png,.gif,.svg,.ico,.woff,.woff2,.ttf,.eot,.pdf,.xml">
                        <small style="color: var(--text-secondary); font-size: 12px; display: block; margin-top: 8px;">
                            可以一次选择多个文件，支持 HTML、CSS、JS、图片等静态文件
                        </small>
                    </div>
                    
                    <div class="message message-info" style="font-size: 13px;">
                        <strong>上传说明：</strong><br>
                        • 建议包含 index.html 作为首页<br>
                        • 文件将覆盖同名文件<br>
                        • 支持常见静态文件格式
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">上传文件</button>
                        <a href="?action=dashboard" class="btn btn-secondary">返回</a>
                    </div>
                </form>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleProjectFiles() {
        $this->requireLogin();
        
        $projectId = (int)($_GET['project_id'] ?? 0);
        $project = $this->projectModel->getProjectById($projectId, $_SESSION['user_id']);
        
        if (!$project) {
            header('Location: ?action=dashboard');
            exit;
        }
        
        $files = $this->projectModel->getProjectFiles($project['subdomain']);
        
        echo Styles::renderHeader('项目文件 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=dashboard" class="btn btn-text">← 返回控制面板</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 800px; margin: 40px auto; padding: 40px;">
                <h2 style="font-size: 24px; font-weight: 400; margin-bottom: 8px;">项目文件</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">
                    项目：<strong>' . htmlspecialchars($project['name']) . '</strong>
                </p>
                
                ' . (empty($files) ? '
                <div class="empty-state" style="padding: 40px 20px;">
                    <div class="empty-state-icon">📄</div>
                    <h3 style="margin-bottom: 12px; font-weight: 400;">暂无文件</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 24px;">上传文件以开始构建你的网站</p>
                    <a href="?action=upload&project_id=' . $projectId . '" class="btn btn-primary">上传文件</a>
                </div>' : '
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 18px; font-weight: 500;">文件列表</h3>
                        <a href="?action=upload&project_id=' . $projectId . '" class="btn btn-primary btn-sm">上传更多文件</a>
                    </div>
                    <div style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 500; border-bottom: 1px solid var(--border-color);">文件名</th>
                                    <th style="padding: 12px 16px; text-align: right; font-weight: 500; border-bottom: 1px solid var(--border-color);">大小</th>
                                    <th style="padding: 12px 16px; text-align: right; font-weight: 500; border-bottom: 1px solid var(--border-color);">修改时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . implode('', array_map(function($file) use ($project) {
                                    return '
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 12px 16px;">
                                            <a href="?action=view&subdomain=' . urlencode($project['subdomain']) . '&file=' . urlencode($file['name']) . '" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                                ' . htmlspecialchars($file['name']) . '
                                            </a>
                                        </td>
                                        <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary); font-size: 14px;">
                                            ' . Styles::formatFileSize($file['size']) . '
                                        </td>
                                        <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary); font-size: 14px;">
                                            ' . Styles::formatDate($file['modified']) . '
                                        </td>
                                    </tr>';
                                }, $files)) . '
                            </tbody>
                        </table>
                    </div>
                </div>') . '
                
                <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                    <a href="?action=upload&project_id=' . $projectId . '" class="btn btn-primary">上传文件</a>
                    <a href="?action=dashboard" class="btn btn-secondary">返回</a>
                </div>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function handleViewSite() {
        $subdomain = $_GET['subdomain'] ?? '';
        $file = $_GET['file'] ?? 'index.html';
        
        $project = $this->projectModel->getProjectBySubdomain($subdomain);
        
        if (!$project) {
            $this->renderError("项目不存在");
            return;
        }
        
        $siteDir = 'sites/' . $subdomain;
        $requestedFile = $siteDir . '/' . basename($file);
        
        // 安全检查
        if (!$this->isSafeFilename($file) || !file_exists($requestedFile)) {
            $requestedFile = $siteDir . '/index.html';
        }
        
        if (file_exists($requestedFile)) {
            $mimeType = $this->getMimeType($requestedFile);
            header('Content-Type: ' . $mimeType);
            readfile($requestedFile);
        } else {
            echo Styles::renderHeader(htmlspecialchars($project['name']) . ' - DvloAsiaCode');
            echo '
            <div class="container" style="text-align: center; padding-top: 100px;">
                <div class="empty-state-icon">🚀</div>
                <h1 style="font-size: 32px; margin-bottom: 16px; color: var(--text-primary);">
                    欢迎来到 ' . htmlspecialchars($project['name']) . '
                </h1>
                <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 24px;">
                    这是一个由 DvloAsiaCode 托管的静态网站
                </p>
                <p style="color: var(--text-secondary); font-size: 14px;">
                    请上传 index.html 文件以显示你的网站内容
                </p>
            </div>';
            echo Styles::renderFooter();
        }
    }
    
    private function handleDeleteProject() {
        $this->requireLogin();
        
        $projectId = (int)($_GET['project_id'] ?? 0);
        
        try {
            $this->projectModel->deleteProject($projectId, $_SESSION['user_id']);
        } catch (Exception $e) {
            // 静默处理错误
        }
        
        header('Location: ?action=dashboard');
        exit;
    }
    
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit;
        }
    }
    
    private function renderError($message) {
        echo Styles::renderHeader('错误 - DvloAsiaCode');
        echo '
        <header>
            <div class="header-content">
                <a href="?action=home" class="logo">
                    <span class="logo-highlight">DvloAsia</span>Code
                </a>
                <a href="?action=home" class="btn btn-text">返回首页</a>
            </div>
        </header>
        
        <div class="container">
            <div class="card" style="max-width: 500px; margin: 60px auto; padding: 40px; text-align: center;">
                <div class="empty-state-icon">❌</div>
                <h2 style="font-size: 24px; margin-bottom: 16px; color: var(--text-primary);">发生错误</h2>
                <p style="color: var(--text-secondary); margin-bottom: 24px;">' . htmlspecialchars($message) . '</p>
                <a href="?action=home" class="btn btn-primary">返回首页</a>
            </div>
        </div>';
        echo Styles::renderFooter();
    }
    
    private function isSafeFilename($filename) {
        return !preg_match('/\.\.|\/|\\\\/', $filename);
    }
    
    private function getMimeType($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'html' => 'text/html',
            'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'pdf' => 'application/pdf',
            'xml' => 'application/xml'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}

// 启动应用
try {
    $app = new DvloAsiaCode();
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    echo "系统错误: " . htmlspecialchars($e->getMessage());
}
?>