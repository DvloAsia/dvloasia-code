<?php
/**
 * DvloAsiaCode - 用户模型
 * 
 * @copyright Copyright (c) 2024 DvloAsiaCode
 * @license MIT
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($username, $password, $email) {
        // 验证输入
        if (empty($username) || empty($password) || empty($email)) {
            throw new Exception("所有字段都是必需的");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("邮箱格式不正确");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("密码至少需要6个字符");
        }
        
        // 检查用户名和邮箱是否已存在
        if ($this->usernameExists($username)) {
            throw new Exception("用户名已存在");
        }
        
        if ($this->emailExists($email)) {
            throw new Exception("邮箱已被注册");
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, email) VALUES (?, ?, ?)"
        );
        
        return $stmt->execute([$username, $hash, $email]);
    }
    
    public function login($username, $password) {
        $stmt = $this->db->prepare("
            SELECT id, username, password, email, created_at 
            FROM users WHERE username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // 移除密码字段
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT id, username, email, created_at 
            FROM users WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function usernameExists($username) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
    
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }
}
?>