<?php

class Comment {
    private $db;
    private $table = 'comments';

    public $id;
    public $post_id;
    public $user_id;
    public $content;
    public $created_at;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Create a new comment
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (post_id, user_id, content, created_at) 
                  VALUES 
                  (:post_id, :user_id, :content, NOW())";

        $stmt = $this->db->prepare($query);

        // Bind values
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get comments by post ID
     */
    public function getByPostId($post_id) {
        $query = "SELECT c.*, u.nom as user_name 
                  FROM " . $this->table . " c 
                  LEFT JOIN user u ON c.user_id = u.id 
                  WHERE c.post_id = :post_id 
                  ORDER BY c.created_at ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a comment by its ID (with author name)
     */
    public function getById($id) {
        $query = "SELECT c.*, u.nom as user_name 
                  FROM " . $this->table . " c 
                  LEFT JOIN user u ON c.user_id = u.id 
                  WHERE c.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete comment
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}