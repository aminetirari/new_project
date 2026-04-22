<?php

require_once __DIR__ . '/../config/Database.php';

class Objectif {
    private $conn;
    private $table = 'objectif';

    public $id_objectif;
    public $user_id;
    public $type_objectif;
    public $valeur_cible;
    public $poids_initial;
    public $date_limite;
    public $description;
    public $statut;
    public $niveau_priorite;
    public $date_creation;
    public $date_mise_a_jour;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Create a new objectif
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, type_objectif, valeur_cible, poids_initial, date_limite, description, statut, niveau_priorite) 
                  VALUES 
                  (:user_id, :type_objectif, :valeur_cible, :poids_initial, :date_limite, :description, :statut, :niveau_priorite)";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':type_objectif', $this->type_objectif);
        $stmt->bindParam(':valeur_cible', $this->valeur_cible);
        $stmt->bindParam(':poids_initial', $this->poids_initial);
        $stmt->bindParam(':date_limite', $this->date_limite);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':niveau_priorite', $this->niveau_priorite);

        if ($stmt->execute()) {
            $this->id_objectif = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get objectif by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_objectif = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all objectifs for a user
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all objectives
     */
    public function getAll() {
        $query = "SELECT o.*, u.nom as user_nom, u.email as user_email 
                  FROM " . $this->table . " o 
                  LEFT JOIN user u ON o.user_id = u.id 
                  ORDER BY o.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update objectif
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET type_objectif = :type_objectif, 
                      valeur_cible = :valeur_cible, 
                      poids_initial = :poids_initial, 
                      date_limite = :date_limite, 
                      description = :description, 
                      statut = :statut, 
                      niveau_priorite = :niveau_priorite 
                  WHERE id_objectif = :id_objectif";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':id_objectif', $this->id_objectif);
        $stmt->bindParam(':type_objectif', $this->type_objectif);
        $stmt->bindParam(':valeur_cible', $this->valeur_cible);
        $stmt->bindParam(':poids_initial', $this->poids_initial);
        $stmt->bindParam(':date_limite', $this->date_limite);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':niveau_priorite', $this->niveau_priorite);

        return $stmt->execute();
    }

    /**
     * Delete objectif
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_objectif = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Validate objectif data
     */
    public static function validate($type_objectif, $valeur_cible, $user_id) {
        $errors = [];

        if (empty($type_objectif)) {
            $errors[] = "Type d'objectif is required";
        }

        if (empty($valeur_cible) || !is_numeric($valeur_cible)) {
            $errors[] = "Valeur cible must be a valid number";
        }

        if (empty($user_id) || !is_numeric($user_id)) {
            $errors[] = "Valid user ID is required";
        }

        return $errors;
    }
}
?>