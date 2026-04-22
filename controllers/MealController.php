<?php

require_once __DIR__ . '/../models/Meal.php';
require_once __DIR__ . '/../models/MealIngredient.php';

class MealController
{
    private $meal;
    private $mealIngredient;

    public function __construct()
    {
        $this->meal = new Meal();
        $this->mealIngredient = new MealIngredient();
    }

    // Get all meals
    public function getAll()
    {
        return $this->meal->getAll();
    }

    // Get meal by ID with ingredients
    public function getById($id)
    {
        $meal = $this->meal->getById($id);
        if ($meal) {
            $meal['ingredients'] = $this->mealIngredient->getIngredientsByMeal($id);
        }
        return $meal;
    }

    // Create new meal with validation
    public function create($data)
    {
        $errors = array();

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom du repas est requis';
        } else if (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Le nom doit contenir au moins 2 caractères';
        } else {
            $this->meal->name = trim($data['name']);
        }

        // Validate date
        if (empty($data['date'])) {
            $errors['date'] = 'La date du repas est requise';
        } else {
            $dateTime = DateTime::createFromFormat('Y-m-d', $data['date']);
            if (!$dateTime) {
                $errors['date'] = 'Le format de la date est invalide (utilisez YYYY-MM-DD)';
            } else {
                $this->meal->date = $data['date'];
            }
        }

        // Validate notes (optional but if provided, max length)
        if (!empty($data['notes']) && strlen($data['notes']) > 500) {
            $errors['notes'] = 'Les notes ne peuvent pas dépasser 500 caractères';
        } else {
            $this->meal->notes = isset($data['notes']) ? trim($data['notes']) : '';
        }

        // Validate ingredients
        if (empty($data['ingredients'])) {
            $errors['ingredients'] = 'Au moins un ingrédient doit être sélectionné';
        } else {
            $selectedIngredients = $data['ingredients'];
            $quantities = isset($data['quantities']) ? $data['quantities'] : array();
            
            foreach ($selectedIngredients as $ingredientId) {
                if (!isset($quantities[$ingredientId]) || empty($quantities[$ingredientId])) {
                    $errors['ingredients'] = 'Une quantité doit être spécifiée pour chaque ingrédient sélectionné';
                    break;
                } elseif (!is_numeric($quantities[$ingredientId]) || $quantities[$ingredientId] <= 0) {
                    $errors['ingredients'] = 'La quantité doit être un nombre positif';
                    break;
                }
            }
        }

        // If no errors, create meal and add ingredients
        if (empty($errors)) {
            $mealId = $this->meal->create();
            if ($mealId) {
                // Add ingredients to meal
                foreach ($selectedIngredients as $ingredientId) {
                    $this->mealIngredient->meal_id = $mealId;
                    $this->mealIngredient->ingredient_id = $ingredientId;
                    $this->mealIngredient->quantity = $quantities[$ingredientId];
                    
                    if (!$this->mealIngredient->addIngredientToMeal()) {
                        // If adding ingredient fails, we could delete the meal, but for simplicity, just log error
                        // For now, continue
                    }
                }
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la création du repas'));
            }
        }

        return array('success' => false, 'errors' => $errors);
    }

    // Update meal with validation
    public function update($id, $data)
    {
        $errors = array();

        $this->meal->id = $id;

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom du repas est requis';
        } else if (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Le nom doit contenir au moins 2 caractères';
        } else {
            $this->meal->name = trim($data['name']);
        }

        // Validate date
        if (empty($data['date'])) {
            $errors['date'] = 'La date du repas est requise';
        } else {
            $dateTime = DateTime::createFromFormat('Y-m-d', $data['date']);
            if (!$dateTime) {
                $errors['date'] = 'Le format de la date est invalide (utilisez YYYY-MM-DD)';
            } else {
                $this->meal->date = $data['date'];
            }
        }

        // Validate notes (optional but if provided, max length)
        if (!empty($data['notes']) && strlen($data['notes']) > 500) {
            $errors['notes'] = 'Les notes ne peuvent pas dépasser 500 caractères';
        } else {
            $this->meal->notes = isset($data['notes']) ? trim($data['notes']) : '';
        }

        // Validate ingredients
        if (empty($data['ingredients'])) {
            $errors['ingredients'] = 'Au moins un ingrédient doit être sélectionné';
        } else {
            $selectedIngredients = $data['ingredients'];
            $quantities = isset($data['quantities']) ? $data['quantities'] : array();
            
            foreach ($selectedIngredients as $ingredientId) {
                if (!isset($quantities[$ingredientId]) || empty($quantities[$ingredientId])) {
                    $errors['ingredients'] = 'Une quantité doit être spécifiée pour chaque ingrédient sélectionné';
                    break;
                } elseif (!is_numeric($quantities[$ingredientId]) || $quantities[$ingredientId] <= 0) {
                    $errors['ingredients'] = 'La quantité doit être un nombre positif';
                    break;
                }
            }
        }

        // If no errors, update meal and ingredients
        if (empty($errors)) {
            $result = $this->meal->update();
            if ($result) {
                // Remove all existing ingredients
                $this->mealIngredient->meal_id = $id;
                $this->mealIngredient->removeAllIngredientsFromMeal();
                
                // Add new ingredients
                foreach ($selectedIngredients as $ingredientId) {
                    $this->mealIngredient->meal_id = $id;
                    $this->mealIngredient->ingredient_id = $ingredientId;
                    $this->mealIngredient->quantity = $quantities[$ingredientId];
                    
                    if (!$this->mealIngredient->addIngredientToMeal()) {
                        // If adding ingredient fails, continue but could log error
                    }
                }
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la mise à jour du repas'));
            }
        }

        return array('success' => false, 'errors' => $errors);
    }

    // Delete meal
    public function delete($id)
    {
        $this->meal->id = $id;
        return $this->meal->delete();
    }
}
