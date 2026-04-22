<?php

class Post {
    private $db;
    private $table = 'posts';

    public $id;
    public $title;
    public $content;
    public $image_path;
    public $author_id;
    public $created_at;
    public $updated_at;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Create a new post. $image_path is optional and may be null.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (title, content, image_path, author_id, created_at, updated_at)
                  VALUES
                  (:title, :content, :image_path, :author_id, NOW(), NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':author_id', $this->author_id);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get post by ID (with author name).
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
     * Get all posts (no counts).
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
     * Get all posts with comment_count and like_count columns (one query).
     */
    public function getAllWithCounts() {
        $query = "SELECT p.*, u.nom as author_name,
                         (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count,
                         (SELECT COUNT(*) FROM likes l WHERE l.target_type = 'post' AND l.target_id = p.id) AS like_count
                  FROM " . $this->table . " p
                  LEFT JOIN user u ON p.author_id = u.id
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kept for backwards compatibility – only comment count.
     */
    public function getAllWithCommentCount() {
        return $this->getAllWithCounts();
    }

    /**
     * Count comments for a given post.
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
     * Update post. image_path can be:
     *   - null  : leave image_path unchanged
     *   - ''    : clear image_path
     *   - '...' : set to new path
     */
    public function update($touch_image = false) {
        if ($touch_image) {
            $query = "UPDATE " . $this->table . "
                      SET title = :title, content = :content, image_path = :image_path, updated_at = NOW()
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . "
                      SET title = :title, content = :content, updated_at = NOW()
                      WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);
        if ($touch_image) {
            $stmt->bindParam(':image_path', $this->image_path);
        }

        return $stmt->execute();
    }

    /**
     * Delete post.
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
