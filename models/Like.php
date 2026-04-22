<?php

/**
 * Polymorphic "like" on either a post or a comment.
 * target_type is 'post' | 'comment'; target_id is the id of the related row.
 * (user_id, target_type, target_id) is a UNIQUE key so a user can only like
 * a given item once.
 */
class Like {
    private $db;
    private $table = 'likes';

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Toggle the like for the given user on the target.
     * Returns one of: 'liked' (added), 'unliked' (removed), 'error'.
     */
    public function toggle($user_id, $target_type, $target_id) {
        if (!in_array($target_type, ['post', 'comment'], true)) {
            return 'error';
        }

        $query = "SELECT id FROM " . $this->table . "
                  WHERE user_id = :user_id
                    AND target_type = :target_type
                    AND target_id = :target_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
        $stmt->bindValue(':target_type', $target_type);
        $stmt->bindValue(':target_id', (int)$target_id, PDO::PARAM_INT);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $del = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
            $del->bindValue(':id', (int)$existing['id'], PDO::PARAM_INT);
            return $del->execute() ? 'unliked' : 'error';
        }

        $ins = $this->db->prepare(
            "INSERT INTO " . $this->table . " (user_id, target_type, target_id)
             VALUES (:user_id, :target_type, :target_id)"
        );
        $ins->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
        $ins->bindValue(':target_type', $target_type);
        $ins->bindValue(':target_id', (int)$target_id, PDO::PARAM_INT);
        return $ins->execute() ? 'liked' : 'error';
    }

    /**
     * Count likes for a single target.
     */
    public function countFor($target_type, $target_id) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM " . $this->table . "
             WHERE target_type = :target_type AND target_id = :target_id"
        );
        $stmt->bindValue(':target_type', $target_type);
        $stmt->bindValue(':target_id', (int)$target_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Does the user already like this target?
     */
    public function hasLiked($user_id, $target_type, $target_id) {
        if (!$user_id) { return false; }
        $stmt = $this->db->prepare(
            "SELECT 1 FROM " . $this->table . "
             WHERE user_id = :user_id AND target_type = :target_type AND target_id = :target_id
             LIMIT 1"
        );
        $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
        $stmt->bindValue(':target_type', $target_type);
        $stmt->bindValue(':target_id', (int)$target_id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Map target ids -> bool indicating whether the user liked each.
     * Useful to avoid N+1 queries on listings.
     * Returns an assoc array: [target_id => true|false].
     */
    public function likedMapFor($user_id, $target_type, array $target_ids) {
        $result = [];
        foreach ($target_ids as $id) { $result[(int)$id] = false; }
        if (!$user_id || empty($target_ids)) { return $result; }

        $placeholders = implode(',', array_fill(0, count($target_ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT target_id FROM " . $this->table . "
             WHERE user_id = ? AND target_type = ? AND target_id IN ($placeholders)"
        );
        $params = array_merge([(int)$user_id, $target_type], array_map('intval', $target_ids));
        $stmt->execute($params);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['target_id']] = true;
        }
        return $result;
    }

    /**
     * Remove every like attached to a given target (used when the target
     * itself is being deleted – keeps the likes table clean since the FK is
     * only on user_id, not on post/comment).
     */
    public function deleteAllFor($target_type, $target_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM " . $this->table . "
             WHERE target_type = :target_type AND target_id = :target_id"
        );
        $stmt->bindValue(':target_type', $target_type);
        $stmt->bindValue(':target_id', (int)$target_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
