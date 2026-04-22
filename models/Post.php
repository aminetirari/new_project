<?php

class Post {
    private $db;
    private $table = 'posts';

    public $id;
    public $title;
    public $content;
    public $author_id;
    public $created_at;
    public $updated_at;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Create a new post
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, content, author_id, created_at, updated_at) 
                  VALUES 
                  (:title, :content, :author_id, NOW(), NOW())";

        $stmt = $this->db->prepare($query);

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':author_id', $this->author_id);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get post by ID
     */
    public function getById($id) {
        $query = "SELECT p.*, u.nom as author_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN user u ON p.author_id = u.id 
                  WHERE p.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all posts
     */
    public function getAll() {
        $query = "SELECT p.*, u.nom as author_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN user u ON p.author_id = u.id 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all posts with comment count
     */
    public function getAllWithCommentCount() {
        $query = "SELECT p.*, u.nom as author_name, 
                         (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
                  FROM " . $this->table . " p 
                  LEFT JOIN user u ON p.author_id = u.id 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count comments for a given post
     */
    public function countComments($post_id) {
        $query = "SELECT COUNT(*) AS cnt FROM comments WHERE post_id = :post_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Update post
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, content = :content, updated_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Delete post
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}