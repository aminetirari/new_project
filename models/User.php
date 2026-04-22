<?php

class User {
    private $db;
    private $table = 'user';

    public $id;
    public $nom;
    public $email;
    public $mot_de_passe;
    public $role;
    public $date_creation;
    public $age;
    public $poids;
    public $taille;
    public $allergique;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Register a new user
     */
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, email, mot_de_passe, role, date_creation) 
                  VALUES 
                  (:nom, :email, :mot_de_passe, :role, NOW())";

        $stmt = $this->db->prepare($query);

        // Hash password
        $this->mot_de_passe = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':mot_de_passe', $this->mot_de_passe);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Login user - returns user data if credentials valid
     */
    public function login() {
        $query = "SELECT id, nom, email, mot_de_passe, role, age, poids, taille, allergique 
                  FROM " . $this->table . " 
                  WHERE email = :email";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($this->mot_de_passe, $result['mot_de_passe'])) {
            return $result;
        }

        return false;
    }

    /**
     * Check if email exists
     */
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        $query = "SELECT id, nom, email, role, date_creation, age, poids, taille, allergique 
                  FROM " . $this->table . " 
                  ORDER BY nom ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateProfile() {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, 
                      age = :age, 
                      poids = :poids, 
                      taille = :taille, 
                      allergique = :allergique 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Bind values with proper type handling for NULL values
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $this->nom, PDO::PARAM_STR);
        $stmt->bindValue(':age', $this->age, $this->age !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':poids', $this->poids, $this->poids !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':taille', $this->taille, $this->taille !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':allergique', $this->allergique, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Update password
     */
    public function updatePassword() {
        $query = "UPDATE " . $this->table . " 
                  SET mot_de_passe = :mot_de_passe 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Hash new password
        $hashedPassword = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':mot_de_passe', $hashedPassword);

        return $stmt->execute();
    }

    /**
     * Validate registration inputs
     */
    public static function validateRegistration($nom, $email, $password, $confirmPassword) {
        $errors = [];

        if (empty($nom)) {
            $errors[] = "Full name is required";
        } elseif (strlen($nom) < 2) {
            $errors[] = "Full name must be at least 2 characters";
        }

        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }

        return $errors;
    }

    /**
     * Validate login inputs
     */
    public static function validateLogin($email, $password) {
        $errors = [];

        if (empty($email)) {
            $errors[] = "Email is required";
        }

        if (empty($password)) {
            $errors[] = "Password is required";
        }

        return $errors;
    }

    /**
     * Validate profile update
     */
    public static function validateProfile($nom, $age, $poids, $taille) {
        $errors = [];

        if (empty($nom)) {
            $errors[] = "Full name is required";
        } elseif (strlen($nom) < 2) {
            $errors[] = "Full name must be at least 2 characters";
        }

        if (!empty($age) && (!is_numeric($age) || $age < 1 || $age > 120)) {
            $errors[] = "Age must be a number between 1 and 120";
        }

        if (!empty($poids) && (!is_numeric($poids) || $poids < 1 || $poids > 500)) {
            $errors[] = "Weight must be a number between 1 and 500 kg";
        }

        if (!empty($taille) && (!is_numeric($taille) || $taille < 30 || $taille > 300)) {
            $errors[] = "Height must be a number between 30 and 300 cm";
        }

        return $errors;
    }

    /**
     * Delete user account
     */
    public function deleteAccount($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
?>
