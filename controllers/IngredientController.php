<?php

require_once __DIR__ . '/../models/Ingredient.php';

class IngredientController
{
    private $ingredient;

    public function __construct()
    {
        $this->ingredient = new Ingredient();
    }

    // Get all ingredients
    public function getAll()
    {
        return $this->ingredient->getAll();
    }

    // Get ingredient by ID
    public function getById($id)
    {
        return $this->ingredient->getById($id);
    }

    // Create new ingredient with validation
    public function create($data)
    {
        $errors = array();

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom de l\'ingrédient est requis';
        } else if (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Le nom doit contenir au moins 2 caractères';
        } else {
            $this->ingredient->name = trim($data['name']);
        }

        // Validate calories
        if (empty($data['calories'])) {
            $errors['calories'] = 'La valeur des calories est requise';
        } elseif (!is_numeric($data['calories']) || (float)$data['calories'] < 0) {
            $errors['calories'] = 'Les calories doivent être un nombre positif';
        } else {
            $this->ingredient->calories = (float)$data['calories'];
        }

        // Validate proteins
        if (empty($data['proteins'])) {
            $errors['proteins'] = 'La valeur des protéines est requise';
        } elseif (!is_numeric($data['proteins']) || (float)$data['proteins'] < 0) {
            $errors['proteins'] = 'Les protéines doivent être un nombre positif';
        } else {
            $this->ingredient->proteins = (float)$data['proteins'];
        }

        // Validate glucides
        if (empty($data['glucides'])) {
            $errors['glucides'] = 'La valeur des glucides est requise';
        } elseif (!is_numeric($data['glucides']) || (float)$data['glucides'] < 0) {
            $errors['glucides'] = 'Les glucides doivent être un nombre positif';
        } else {
            $this->ingredient->glucides = (float)$data['glucides'];
        }

        // Validate lipides
        if (empty($data['lipides'])) {
            $errors['lipides'] = 'La valeur des lipides est requise';
        } elseif (!is_numeric($data['lipides']) || (float)$data['lipides'] < 0) {
            $errors['lipides'] = 'Les lipides doivent être un nombre positif';
        } else {
            $this->ingredient->lipides = (float)$data['lipides'];
        }

        // If no errors, create ingredient
        if (empty($errors)) {
            $result = $this->ingredient->create();
            return array('success' => $result !== false, 'errors' => array());
        }

        return array('success' => false, 'errors' => $errors);
    }

    // Update ingredient with validation
    public function update($id, $data)
    {
        $errors = array();

        $this->ingredient->id = $id;

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom de l\'ingrédient est requis';
        } else if (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Le nom doit contenir au moins 2 caractères';
        } else {
            $this->ingredient->name = trim($data['name']);
        }

        // Validate calories
        if (empty($data['calories'])) {
            $errors['calories'] = 'La valeur des calories est requise';
        } elseif (!is_numeric($data['calories']) || (float)$data['calories'] < 0) {
            $errors['calories'] = 'Les calories doivent être un nombre positif';
        } else {
            $this->ingredient->calories = (float)$data['calories'];
        }

        // Validate proteins
        if (empty($data['proteins'])) {
            $errors['proteins'] = 'La valeur des protéines est requise';
        } elseif (!is_numeric($data['proteins']) || (float)$data['proteins'] < 0) {
            $errors['proteins'] = 'Les protéines doivent être un nombre positif';
        } else {
            $this->ingredient->proteins = (float)$data['proteins'];
        }

        // Validate glucides
        if (empty($data['glucides'])) {
            $errors['glucides'] = 'La valeur des glucides est requise';
        } elseif (!is_numeric($data['glucides']) || (float)$data['glucides'] < 0) {
            $errors['glucides'] = 'Les glucides doivent être un nombre positif';
        } else {
            $this->ingredient->glucides = (float)$data['glucides'];
        }

        // Validate lipides
        if (empty($data['lipides'])) {
            $errors['lipides'] = 'La valeur des lipides est requise';
        } elseif (!is_numeric($data['lipides']) || (float)$data['lipides'] < 0) {
            $errors['lipides'] = 'Les lipides doivent être un nombre positif';
        } else {
            $this->ingredient->lipides = (float)$data['lipides'];
        }

        // If no errors, update ingredient
        if (empty($errors)) {
            $result = $this->ingredient->update();
            return array('success' => $result, 'errors' => array());
        }

        return array('success' => false, 'errors' => $errors);
    }

    // Delete ingredient
    public function delete($id)
    {
        $this->ingredient->id = $id;
        return $this->ingredient->delete();
    }
}
