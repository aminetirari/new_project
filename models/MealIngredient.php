<?php

require_once __DIR__ . '/../config/Database.php';

class MealIngredient
{
    private $conn;
    private $table = 'Meal_Ingredient';

    // Properties
    public $meal_id;
    public $ingredient_id;
    public $quantity;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Get all ingredients for a specific meal
    public function getIngredientsByMeal($meal_id)
    {
        $query = "SELECT i.id, i.name, i.calories, i.proteins, i.glucides, i.lipides, mi.quantity
                  FROM " . $this->table . " mi
                  INNER JOIN Ingredient i ON mi.ingredient_id = i.id
                  WHERE mi.meal_id = :meal_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $meal_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all meals for a specific ingredient
    public function getMealsByIngredient($ingredient_id)
    {
        $query = "SELECT m.id, m.name, m.date, m.notes, mi.quantity
                  FROM " . $this->table . " mi
                  INNER JOIN Meal m ON mi.meal_id = m.id
                  WHERE mi.ingredient_id = :ingredient_id
                  ORDER BY m.date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingredient_id', $ingredient_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add ingredient to meal
    public function addIngredientToMeal()
    {
        $query = "INSERT INTO " . $this->table . "
                  (meal_id, ingredient_id, quantity)
                  VALUES (:meal_id, :ingredient_id, :quantity)";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->meal_id = htmlspecialchars(strip_tags($this->meal_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));

        // Bind values
        $stmt->bindParam(':meal_id', $this->meal_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->bindParam(':quantity', $this->quantity);

        return $stmt->execute();
    }

    // Update quantity of ingredient in meal
    public function updateQuantity()
    {
        $query = "UPDATE " . $this->table . "
                  SET quantity = :quantity
                  WHERE meal_id = :meal_id AND ingredient_id = :ingredient_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->meal_id = htmlspecialchars(strip_tags($this->meal_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));

        // Bind values
        $stmt->bindParam(':meal_id', $this->meal_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->bindParam(':quantity', $this->quantity);

        return $stmt->execute();
    }

    // Remove ingredient from meal
    public function removeIngredientFromMeal()
    {
        $query = "DELETE FROM " . $this->table . "
                  WHERE meal_id = :meal_id AND ingredient_id = :ingredient_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->meal_id = htmlspecialchars(strip_tags($this->meal_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));

        // Bind values
        $stmt->bindParam(':meal_id', $this->meal_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);

        return $stmt->execute();
    }

    // Remove all ingredients from a meal
    public function removeAllIngredientsFromMeal()
    {
        $query = "DELETE FROM " . $this->table . " WHERE meal_id = :meal_id";
        $stmt = $this->conn->prepare($query);
        $this->meal_id = htmlspecialchars(strip_tags($this->meal_id));
        $stmt->bindParam(':meal_id', $this->meal_id);
        return $stmt->execute();
    }

    // Get total nutritional values for a meal
    public function getMealNutrition($meal_id)
    {
        $query = "SELECT 
                    SUM(i.calories * mi.quantity) as total_calories,
                    SUM(i.proteins * mi.quantity) as total_proteins,
                    SUM(i.glucides * mi.quantity) as total_glucides,
                    SUM(i.lipides * mi.quantity) as total_lipides
                  FROM " . $this->table . " mi
                  INNER JOIN Ingredient i ON mi.ingredient_id = i.id
                  WHERE mi.meal_id = :meal_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $meal_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if ingredient exists in meal
    public function ingredientExistsInMeal()
    {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE meal_id = :meal_id AND ingredient_id = :ingredient_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $this->meal_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
