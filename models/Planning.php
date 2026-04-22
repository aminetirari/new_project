<?php

require_once __DIR__ . '/../config/Database.php';

class Planning {
    private $conn;
    private $table = 'planning';

    public $id_planning;
    public $user_id;
    public $objectif_id;
    public $titre;
    public $description;
    public $calories_par_jour;
    public $objectif_proteines;
    public $objectif_glucides;
    public $objectif_lipides;
    public $nombre_repas_par_jour;
    public $heures_sommeil_par_jour;
    public $heures_entrainement_par_jour;
    public $date_debut;
    public $date_fin;
    public $statut;
    public $date_creation;
    public $date_mise_a_jour;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Create a new planning
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, objectif_id, titre, description, calories_par_jour, objectif_proteines, objectif_glucides, objectif_lipides, nombre_repas_par_jour, heures_sommeil_par_jour, heures_entrainement_par_jour, date_debut, date_fin, statut) 
                  VALUES 
                  (:user_id, :objectif_id, :titre, :description, :calories_par_jour, :objectif_proteines, :objectif_glucides, :objectif_lipides, :nombre_repas_par_jour, :heures_sommeil_par_jour, :heures_entrainement_par_jour, :date_debut, :date_fin, :statut)";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':objectif_id', $this->objectif_id);
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':calories_par_jour', $this->calories_par_jour);
        $stmt->bindParam(':objectif_proteines', $this->objectif_proteines);
        $stmt->bindParam(':objectif_glucides', $this->objectif_glucides);
        $stmt->bindParam(':objectif_lipides', $this->objectif_lipides);
        $stmt->bindParam(':nombre_repas_par_jour', $this->nombre_repas_par_jour);
        $stmt->bindParam(':heures_sommeil_par_jour', $this->heures_sommeil_par_jour);
        $stmt->bindParam(':heures_entrainement_par_jour', $this->heures_entrainement_par_jour);
        $stmt->bindParam(':date_debut', $this->date_debut);
        $stmt->bindParam(':date_fin', $this->date_fin);
        $stmt->bindParam(':statut', $this->statut);

        if ($stmt->execute()) {
            $this->id_planning = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get planning by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_planning = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all plannings for a user
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all plannings with user and objective details
     */
    public function getAllWithDetails() {
        $query = "SELECT p.*, u.nom as user_name, o.type_objectif as objectif_type
                  FROM " . $this->table . " p
                  LEFT JOIN user u ON p.user_id = u.id
                  LEFT JOIN objectif o ON p.objectif_id = o.id_objectif
                  ORDER BY p.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update planning
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET titre = :titre, 
                      description = :description, 
                      calories_par_jour = :calories_par_jour, 
                      objectif_proteines = :objectif_proteines, 
                      objectif_glucides = :objectif_glucides, 
                      objectif_lipides = :objectif_lipides, 
                      nombre_repas_par_jour = :nombre_repas_par_jour, 
                      heures_sommeil_par_jour = :heures_sommeil_par_jour, 
                      heures_entrainement_par_jour = :heures_entrainement_par_jour, 
                      date_debut = :date_debut, 
                      date_fin = :date_fin, 
                      statut = :statut 
                  WHERE id_planning = :id_planning";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':id_planning', $this->id_planning);
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':calories_par_jour', $this->calories_par_jour);
        $stmt->bindParam(':objectif_proteines', $this->objectif_proteines);
        $stmt->bindParam(':objectif_glucides', $this->objectif_glucides);
        $stmt->bindParam(':objectif_lipides', $this->objectif_lipides);
        $stmt->bindParam(':nombre_repas_par_jour', $this->nombre_repas_par_jour);
        $stmt->bindParam(':heures_sommeil_par_jour', $this->heures_sommeil_par_jour);
        $stmt->bindParam(':heures_entrainement_par_jour', $this->heures_entrainement_par_jour);
        $stmt->bindParam(':date_debut', $this->date_debut);
        $stmt->bindParam(':date_fin', $this->date_fin);
        $stmt->bindParam(':statut', $this->statut);

        return $stmt->execute();
    }

    /**
     * Delete planning
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_planning = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Validate planning data
     */
    public static function validate($user_id, $objectif_id, $titre) {
        $errors = [];

        if (empty($user_id) || !is_numeric($user_id)) {
            $errors[] = "Valid user ID is required";
        }

        if (empty($objectif_id) || !is_numeric($objectif_id)) {
            $errors[] = "Valid objectif ID is required";
        }

        if (empty($titre)) {
            $errors[] = "Titre is required";
        }

        return $errors;
    }
}
?>