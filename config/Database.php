<?php
/**
 * DvloAsiaCode - 数据库连接类
 * 
 * @copyright Copyright (c) 2024 DvloAsiaCode
 * @license MIT
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct($dbFile = 'data/site.db') {
        $this->initDatabase($dbFile);
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initDatabase($dbFile) {
        // 创建数据目录
        $dataDir = dirname($dbFile);
        if (!file_exists($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
        
        try {
            $this->pdo = new PDO('sqlite:' . $dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->createTables();
        } catch (PDOException $e) {
            throw new Exception("数据库连接失败: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        // 用户表
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // 项目表
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                subdomain TEXT UNIQUE NOT NULL,
                description TEXT,
                is_public INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE(user_id, name)
            )
        ");
        
        // 创建索引
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_projects_user_id ON projects(user_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_projects_subdomain ON projects(subdomain)");
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollback();
    }
}
?>