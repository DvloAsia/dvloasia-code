<?php
/**
 * DvloAsiaCode - 样式工具类
 * 
 * @copyright Copyright (c) 2024 DvloAsiaCode
 * @license MIT
 */

class Styles {
    public static function getGoogleStyles() {
        return '
        <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        :root {
            --primary-color: #1a73e8;
            --primary-hover: #1669d6;
            --secondary-color: #5f6368;
            --background-color: #f8f9fa;
            --surface-color: #ffffff;
            --error-color: #d93025;
            --success-color: #0f9d58;
            --border-color: #dadce0;
            --text-primary: #202124;
            --text-secondary: #5f6368;
            --shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
            --shadow-hover: 0 1px 3px 0 rgba(60,64,67,0.3), 0 4px 8px 3px rgba(60,64,67,0.15);
        }
        
        body {
            font-family: "Google Sans", "Roboto", Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        
        /* 头部样式 */
        header {
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        
        .logo {
            font-size: 22px;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .logo-highlight {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        /* 按钮样式 */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: var(--surface-color);
            color: var(--primary-color);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            box-shadow: var(--shadow);
        }
        
        .btn-danger {
            background: var(--error-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c5221f;
            box-shadow: var(--shadow-hover);
        }
        
        .btn-text {
            background: transparent;
            color: var(--primary-color);
            padding: 8px 16px;
        }
        
        .btn-text:hover {
            background: rgba(26, 115, 232, 0.04);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        /* 卡片样式 */
        .card {
            background: var(--surface-color);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-hover);
        }
        
        /* 表单样式 */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            background: var(--surface-color);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            background: var(--surface-color);
            cursor: pointer;
        }
        
        /* 消息样式 */
        .message {
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message-error {
            background: #fce8e6;
            color: var(--error-color);
            border: 1px solid #f5c0b8;
        }
        
        .message-success {
            background: #e6f4ea;
            color: var(--success-color);
            border: 1px solid #b8e0c2;
        }
        
        .message-info {
            background: #e8f0fe;
            color: var(--primary-color);
            border: 1px solid #c2d7f0;
        }
        
        .message-warning {
            background: #fef7e0;
            color: #f29900;
            border: 1px solid #fde293;
        }
        
        /* 网格布局 */
        .grid {
            display: grid;
            gap: 24px;
        }
        
        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        /* 项目卡片 */
        .project-card {
            padding: 24px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .project-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-hover);
        }
        
        .project-name {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .project-description {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.5;
        }
        
        .project-url {
            color: var(--primary-color);
            font-size: 14px;
            margin-bottom: 16px;
            word-break: break-all;
        }
        
        .project-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* 空状态 */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* 文件上传 */
        .file-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: border-color 0.2s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-color);
        }
        
        .file-upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(26, 115, 232, 0.04);
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            .container {
                padding: 0 12px;
            }
            
            .header-content {
                padding: 0 12px;
            }
            
            .logo {
                font-size: 18px;
            }
            
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
            
            .project-actions {
                flex-direction: column;
            }
            
            .btn {
                padding: 12px 20px;
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 8px;
            }
            
            .header-content {
                padding: 0 8px;
                flex-direction: column;
                gap: 12px;
            }
            
            .logo {
                font-size: 16px;
            }
            
            .empty-state {
                padding: 40px 16px;
            }
            
            .empty-state-icon {
                font-size: 48px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                max-width: 900px;
            }
            
            .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* 工具类 */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mb-1 { margin-bottom: 8px; }
        .mb-2 { margin-bottom: 16px; }
        .mb-3 { margin-bottom: 24px; }
        .p-1 { padding: 8px; }
        .p-2 { padding: 16px; }
        .p-3 { padding: 24px; }
        .d-flex { display: flex; }
        .d-none { display: none; }
        .justify-between { justify-content: space-between; }
        .align-center { align-items: center; }
        .gap-1 { gap: 8px; }
        .gap-2 { gap: 16px; }
        .w-100 { width: 100%; }
        </style>';
    }
    
    public static function renderHeader($title = 'DvloAsiaCode') {
        return '
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($title) . '</title>
            ' . self::getGoogleStyles() . '
        </head>
        <body>';
    }
    
    public static function renderFooter() {
        return '
        </body>
        </html>';
    }
    
    public static function formatFileSize($bytes) {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $base = 1024;
        $class = min((int)log($bytes, $base), count($units) - 1);
        
        return sprintf('%1.2f %s', $bytes / pow($base, $class), $units[$class]);
    }
    
    public static function formatDate($timestamp) {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
?>