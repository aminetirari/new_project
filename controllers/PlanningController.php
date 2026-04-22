<?php

require_once __DIR__ . '/../models/Planning.php';

class PlanningController
{
    private $planning;

    public function __construct()
    {
        $this->planning = new Planning();
    }

    // Get all plannings with user and objective details
    public function getAllWithDetails()
    {
        return $this->planning->getAllWithDetails();
    }

    // Get planning by ID
    public function getById($id)
    {
        return $this->planning->getById($id);
    }

    // Get all plans for a specific user
    public function getAllForUser($user_id)
    {
        return $this->planning->getByUserId($user_id);
    }

    // Create new planning with validation
    public function create($data)
    {
        $errors = array();

        // Validate user_id
        if (empty($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] <= 0) {
            $errors['user_id'] = 'Utilisateur invalide';
        }

        // Validate objectif_id
        if (empty($data['objectif_id']) || !is_numeric($data['objectif_id']) || $data['objectif_id'] <= 0) {
            $errors['objectif_id'] = 'Objectif invalide';
        }

        // Validate titre
        if (!empty($data['titre'])) {
            if (strlen(trim($data['titre'])) > 100) {
                $errors['titre'] = 'Le titre ne peut pas dépasser 100 caractères';
            } else {
                $this->planning->titre = trim($data['titre']);
            }
        } else {
            $this->planning->titre = null;
        }

        // Validate description
        if (!empty($data['description'])) {
            if (strlen(trim($data['description'])) > 1000) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            } else {
                $this->planning->description = trim($data['description']);
            }
        } else {
            $this->planning->description = null;
        }

        // Validate calories_par_jour
        if (!empty($data['calories_par_jour'])) {
            if (!is_numeric($data['calories_par_jour']) || $data['calories_par_jour'] < 500 || $data['calories_par_jour'] > 10000) {
                $errors['calories_par_jour'] = 'Les calories par jour doivent être entre 500 et 10000';
            } else {
                $this->planning->calories_par_jour = intval($data['calories_par_jour']);
            }
        } else {
            $this->planning->calories_par_jour = null;
        }

        // Validate macronutrients
        $macros = ['objectif_proteines', 'objectif_glucides', 'objectif_lipides'];
        foreach ($macros as $macro) {
            if (!empty($data[$macro])) {
                if (!is_numeric($data[$macro]) || $data[$macro] < 0 || $data[$macro] > 2000) {
                    $errors[$macro] = ucfirst(str_replace('_', ' ', $macro)) . ' doit être entre 0 et 2000g';
                } else {
                    $this->planning->$macro = intval($data[$macro]);
                }
            } else {
                $this->planning->$macro = null;
            }
        }

        // Validate nombre_repas_par_jour
        if (!empty($data['nombre_repas_par_jour'])) {
            if (!is_numeric($data['nombre_repas_par_jour']) || $data['nombre_repas_par_jour'] < 1 || $data['nombre_repas_par_jour'] > 10) {
                $errors['nombre_repas_par_jour'] = 'Le nombre de repas doit être entre 1 et 10';
            } else {
                $this->planning->nombre_repas_par_jour = intval($data['nombre_repas_par_jour']);
            }
        } else {
            $this->planning->nombre_repas_par_jour = null;
        }

        // Validate sleep and exercise hours
        $time_fields = ['heures_sommeil_par_jour', 'heures_entrainement_par_jour'];
        foreach ($time_fields as $field) {
            if (!empty($data[$field])) {
                if (!is_numeric($data[$field]) || $data[$field] < 0 || $data[$field] > 24) {
                    $errors[$field] = ucfirst(str_replace(['heures_', '_par_jour'], [' ', ''], $field)) . ' doit être entre 0 et 24 heures';
                } else {
                    $this->planning->$field = floatval($data[$field]);
                }
            } else {
                $this->planning->$field = null;
            }
        }

        // Validate dates
        if (!empty($data['date_debut'])) {
            $date_debut = DateTime::createFromFormat('Y-m-d', $data['date_debut']);
            if (!$date_debut) {
                $errors['date_debut'] = 'Format de date de début invalide';
            } else {
                $this->planning->date_debut = $data['date_debut'];
            }
        } else {
            $this->planning->date_debut = null;
        }

        if (!empty($data['date_fin'])) {
            $date_fin = DateTime::createFromFormat('Y-m-d', $data['date_fin']);
            if (!$date_fin) {
                $errors['date_fin'] = 'Format de date de fin invalide';
            } elseif (!empty($data['date_debut']) && $date_fin < $date_debut) {
                $errors['date_fin'] = 'La date de fin doit être après la date de début';
            } else {
                $this->planning->date_fin = $data['date_fin'];
            }
        } else {
            $this->planning->date_fin = null;
        }

        // Validate statut
        $allowed_statuts = ['actif', 'inactif', 'termine'];
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide';
        } else {
            $this->planning->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts) ? $data['statut'] : 'actif';
        }

        $this->planning->user_id = $data['user_id'];
        $this->planning->objectif_id = $data['objectif_id'];

        // If no errors, create planning
        if (empty($errors)) {
            if ($this->planning->create()) {
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la création du plan'));
            }
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    // Update planning
    public function update($id, $data)
    {
        $errors = array();

        // Validate ID
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            $errors['general'] = 'ID de plan invalide';
            return array('success' => false, 'errors' => $errors);
        }

        // Get existing planning
        $existing = $this->planning->getById($id);
        if (!$existing) {
            return array('success' => false, 'errors' => array('general' => 'Plan non trouvé'));
        }

        // Validate titre
        if (!empty($data['titre'])) {
            if (strlen(trim($data['titre'])) > 100) {
                $errors['titre'] = 'Le titre ne peut pas dépasser 100 caractères';
            } else {
                $this->planning->titre = trim($data['titre']);
            }
        } else {
            $this->planning->titre = null;
        }

        // Validate description
        if (!empty($data['description'])) {
            if (strlen(trim($data['description'])) > 1000) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            } else {
                $this->planning->description = trim($data['description']);
            }
        } else {
            $this->planning->description = null;
        }

        // Validate calories_par_jour
        if (!empty($data['calories_par_jour'])) {
            if (!is_numeric($data['calories_par_jour']) || $data['calories_par_jour'] < 500 || $data['calories_par_jour'] > 10000) {
                $errors['calories_par_jour'] = 'Les calories par jour doivent être entre 500 et 10000';
            } else {
                $this->planning->calories_par_jour = intval($data['calories_par_jour']);
            }
        } else {
            $this->planning->calories_par_jour = null;
        }

        // Validate macronutrients
        $macros = ['objectif_proteines', 'objectif_glucides', 'objectif_lipides'];
        foreach ($macros as $macro) {
            if (!empty($data[$macro])) {
                if (!is_numeric($data[$macro]) || $data[$macro] < 0 || $data[$macro] > 2000) {
                    $errors[$macro] = ucfirst(str_replace('_', ' ', $macro)) . ' doit être entre 0 et 2000g';
                } else {
                    $this->planning->$macro = intval($data[$macro]);
                }
            } else {
                $this->planning->$macro = null;
            }
        }

        // Validate nombre_repas_par_jour
        if (!empty($data['nombre_repas_par_jour'])) {
            if (!is_numeric($data['nombre_repas_par_jour']) || $data['nombre_repas_par_jour'] < 1 || $data['nombre_repas_par_jour'] > 10) {
                $errors['nombre_repas_par_jour'] = 'Le nombre de repas doit être entre 1 et 10';
            } else {
                $this->planning->nombre_repas_par_jour = intval($data['nombre_repas_par_jour']);
            }
        } else {
            $this->planning->nombre_repas_par_jour = null;
        }

        // Validate sleep and exercise hours
        $time_fields = ['heures_sommeil_par_jour', 'heures_entrainement_par_jour'];
        foreach ($time_fields as $field) {
            if (!empty($data[$field])) {
                if (!is_numeric($data[$field]) || $data[$field] < 0 || $data[$field] > 24) {
                    $errors[$field] = ucfirst(str_replace(['heures_', '_par_jour'], [' ', ''], $field)) . ' doit être entre 0 et 24 heures';
                } else {
                    $this->planning->$field = floatval($data[$field]);
                }
            } else {
                $this->planning->$field = null;
            }
        }

        // Validate dates
        if (!empty($data['date_debut'])) {
            $date_debut = DateTime::createFromFormat('Y-m-d', $data['date_debut']);
            if (!$date_debut) {
                $errors['date_debut'] = 'Format de date de début invalide';
            } else {
                $this->planning->date_debut = $data['date_debut'];
            }
        } else {
            $this->planning->date_debut = null;
        }

        if (!empty($data['date_fin'])) {
            $date_fin = DateTime::createFromFormat('Y-m-d', $data['date_fin']);
            if (!$date_fin) {
                $errors['date_fin'] = 'Format de date de fin invalide';
            } elseif (!empty($data['date_debut']) && $date_fin < $date_debut) {
                $errors['date_fin'] = 'La date de fin doit être après la date de début';
            } else {
                $this->planning->date_fin = $data['date_fin'];
            }
        } else {
            $this->planning->date_fin = null;
        }

        // Validate statut
        $allowed_statuts = ['actif', 'inactif', 'termine'];
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide';
        } else {
            $this->planning->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts) ? $data['statut'] : 'actif';
        }

        $this->planning->id_planning = $id;

        // If no errors, update planning
        if (empty($errors)) {
            if ($this->planning->update()) {
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la mise à jour du plan'));
            }
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    // Delete planning
    public function delete($id)
    {
        return $this->planning->delete($id);
    }
}
?>