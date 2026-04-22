<?php

require_once __DIR__ . '/../config/Database.php';

class Meal
{
    private $conn;
    private $table = 'Meal';

    // Properties
    public $id;
    public $name;
    public $date;
    public $notes;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Get all meals
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get meal by ID
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create meal
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (name, date, notes)
                  VALUES (:name, :date, :notes)";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':notes', $this->notes);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Update meal
    public function update()
    {
        $query = "UPDATE " . $this->table . "
                  SET name = :name, date = :date, notes = :notes
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':notes', $this->notes);

        return $stmt->execute();
    }

    // Delete meal
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Get meal by date
    public function getByDate($date)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE date = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
