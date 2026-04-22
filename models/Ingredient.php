<?php

require_once __DIR__ . '/../config/Database.php';

class Ingredient
{
    private $conn;
    private $table = 'Ingredient';

    // Properties
    public $id;
    public $name;
    public $calories;
    public $proteins;
    public $glucides;
    public $lipides;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Get all ingredients
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ingredient by ID
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create ingredient
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (name, calories, proteins, glucides, lipides)
                  VALUES (:name, :calories, :proteins, :glucides, :lipides)";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->calories = htmlspecialchars(strip_tags($this->calories));
        $this->proteins = htmlspecialchars(strip_tags($this->proteins));
        $this->glucides = htmlspecialchars(strip_tags($this->glucides));
        $this->lipides = htmlspecialchars(strip_tags($this->lipides));

        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':calories', $this->calories);
        $stmt->bindParam(':proteins', $this->proteins);
        $stmt->bindParam(':glucides', $this->glucides);
        $stmt->bindParam(':lipides', $this->lipides);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Update ingredient
    public function update()
    {
        $query = "UPDATE " . $this->table . "
                  SET name = :name, calories = :calories, proteins = :proteins, 
                      glucides = :glucides, lipides = :lipides
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->calories = htmlspecialchars(strip_tags($this->calories));
        $this->proteins = htmlspecialchars(strip_tags($this->proteins));
        $this->glucides = htmlspecialchars(strip_tags($this->glucides));
        $this->lipides = htmlspecialchars(strip_tags($this->lipides));

        // Bind values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':calories', $this->calories);
        $stmt->bindParam(':proteins', $this->proteins);
        $stmt->bindParam(':glucides', $this->glucides);
        $stmt->bindParam(':lipides', $this->lipides);

        return $stmt->execute();
    }

    // Delete ingredient
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Search ingredients by name
    public function search($searchTerm)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE name LIKE :name ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $searchTerm . '%';
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ingredient by name
    public function getByName($name)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE name = :name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
