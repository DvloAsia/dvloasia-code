<?php
/**
 * DvloAsiaCode - 项目模型
 * 
 * @copyright Copyright (c) 2024 DvloAsiaCode
 * @license MIT
 */

class Project {
    private $db;
    private $baseDir;
    
    public function __construct($baseDir = 'sites') {
        $this->db = Database::getInstance()->getConnection();
        $this->baseDir = $baseDir;
        $this->initBaseDirectory();
    }
    
    private function initBaseDirectory() {
        if (!file_exists($this->baseDir)) {
            mkdir($this->baseDir, 0755, true);
        }
    }
    
    public function create($userId, $name, $description = '') {
        // 验证输入
        if (empty($name)) {
            throw new Exception("项目名称不能为空");
        }
        
        if (!preg_match('/^[a-zA-Z0-9-_ ]+$/', $name)) {
            throw new Exception("项目名称只能包含字母、数字、空格、连字符和下划线");
        }
        
        // 检查项目名是否已存在
        if ($this->projectNameExists($userId, $name)) {
            throw new Exception("项目名称已存在");
        }
        
        $subdomain = $this->generateSubdomain($userId, $name);
        
        $stmt = $this->db->prepare("
            INSERT INTO projects (user_id, name, subdomain, description) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$userId, $name, $subdomain, $description])) {
            $projectId = $this->db->lastInsertId();
            $this->createProjectDirectory($subdomain);
            return $projectId;
        }
        
        return false;
    }
    
    private function generateSubdomain($userId, $projectName) {
        $userModel = new User();
        $user = $userModel->getUserById($userId);
        
        if (!$user) {
            throw new Exception("用户不存在");
        }
        
        $baseSubdomain = strtolower($user['username'] . '-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($projectName)));
        $subdomain = $baseSubdomain;
        $counter = 1;
        
        // 确保子域名唯一
        while ($this->subdomainExists($subdomain)) {
            $subdomain = $baseSubdomain . '-' . $counter;
            $counter++;
        }
        
        return $subdomain;
    }
    
    private function createProjectDirectory($subdomain) {
        $dir = $this->baseDir . '/' . $subdomain;
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new Exception("无法创建项目目录");
            }
        }
        return $dir;
    }
    
    public function getUserProjects($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM projects 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getProjectById($projectId, $userId = null) {
        $sql = "SELECT * FROM projects WHERE id = ?";
        $params = [$projectId];
        
        if ($userId !== null) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function getProjectBySubdomain($subdomain) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE subdomain = ?");
        $stmt->execute([$subdomain]);
        return $stmt->fetch();
    }
    
    public function uploadFiles($subdomain, $files) {
        $projectDir = $this->baseDir . '/' . $subdomain;
        
        if (!file_exists($projectDir)) {
            throw new Exception("项目目录不存在");
        }
        
        $uploaded = [];
        $errors = [];
        
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$key];
                $destination = $projectDir . '/' . basename($name);
                
                // 安全检查
                if (!$this->isSafeFilename($name)) {
                    $errors[] = "文件名不安全: " . $name;
                    continue;
                }
                
                if (!$this->isAllowedFileType($name)) {
                    $errors[] = "文件类型不允许: " . $name;
                    continue;
                }
                
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploaded[] = $name;
                } else {
                    $errors[] = "文件上传失败: " . $name;
                }
            } else {
                $errors[] = "文件上传错误: " . $name . " (错误代码: " . $files['error'][$key] . ")";
            }
        }
        
        return [
            'uploaded' => $uploaded,
            'errors' => $errors
        ];
    }
    
    public function deleteProject($projectId, $userId) {
        $project = $this->getProjectById($projectId, $userId);
        
        if (!$project) {
            throw new Exception("项目不存在或无权访问");
        }
        
        // 删除文件目录
        $this->deleteDirectory($this->baseDir . '/' . $project['subdomain']);
        
        // 删除数据库记录
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = ?");
        return $stmt->execute([$projectId]);
    }
    
    private function projectNameExists($userId, $projectName) {
        $stmt = $this->db->prepare("SELECT id FROM projects WHERE user_id = ? AND name = ?");
        $stmt->execute([$userId, $projectName]);
        return $stmt->fetch() !== false;
    }
    
    private function subdomainExists($subdomain) {
        $stmt = $this->db->prepare("SELECT id FROM projects WHERE subdomain = ?");
        $stmt->execute([$subdomain]);
        return $stmt->fetch() !== false;
    }
    
    private function isSafeFilename($filename) {
        // 防止目录遍历攻击
        return !preg_match('/\.\.|\/|\\\\/', $filename);
    }
    
    private function isAllowedFileType($filename) {
        $allowedExtensions = [
            'html', 'htm', 'css', 'js', 'json', 'txt',
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico',
            'woff', 'woff2', 'ttf', 'eot', 'pdf', 'xml'
        ];
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }
    
    private function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        return rmdir($dir);
    }
    
    public function getProjectFiles($subdomain) {
        $projectDir = $this->baseDir . '/' . $subdomain;
        
        if (!file_exists($projectDir)) {
            return [];
        }
        
        $files = array_diff(scandir($projectDir), ['.', '..']);
        $fileList = [];
        
        foreach ($files as $file) {
            $filePath = $projectDir . '/' . $file;
            $fileList[] = [
                'name' => $file,
                'size' => filesize($filePath),
                'modified' => filemtime($filePath)
            ];
        }
        
        return $fileList;
    }
}
?>