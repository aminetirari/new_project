<?php

require_once __DIR__ . '/../models/Objectif.php';

class ObjectiveController
{
    private $objectif;

    public function __construct()
    {
        $this->objectif = new Objectif();
    }

    // Get all objectives for the current user
    public function getAllForUser($user_id)
    {
        return $this->objectif->getByUserId($user_id);
    }

    // Get all objectives (for admin use)
    public function getAllObjectives()
    {
        return $this->objectif->getAll();
    }

    // Get objective by ID
    public function getById($id)
    {
        return $this->objectif->getById($id);
    }

    // Create new objective with validation
    public function create($data, $user_id)
    {
        $errors = array();

        // Validate user_id
        if (empty($user_id) || !is_numeric($user_id) || $user_id <= 0) {
            $errors['general'] = 'Utilisateur invalide';
            return array('success' => false, 'errors' => $errors);
        }

        // Validate type_objectif
        $allowed_types = ['perte_poids', 'prise_poids', 'maintien_poids', 'augmentation_muscle', 'amelioration_endurance', 'reduction_gras'];
        if (empty($data['type_objectif'])) {
            $errors['type_objectif'] = 'Le type d\'objectif est requis';
        } elseif (!in_array($data['type_objectif'], $allowed_types)) {
            $errors['type_objectif'] = 'Type d\'objectif invalide';
        } else {
            $this->objectif->type_objectif = trim($data['type_objectif']);
        }

        // Validate valeur_cible
        if (empty($data['valeur_cible'])) {
            $errors['valeur_cible'] = 'La valeur cible est requise';
        } elseif (!is_numeric($data['valeur_cible'])) {
            $errors['valeur_cible'] = 'La valeur cible doit être un nombre';
        } elseif ($data['valeur_cible'] <= 0) {
            $errors['valeur_cible'] = 'La valeur cible doit être positive';
        } elseif ($data['valeur_cible'] > 999.99) {
            $errors['valeur_cible'] = 'La valeur cible ne peut pas dépasser 999.99';
        } else {
            $this->objectif->valeur_cible = floatval($data['valeur_cible']);
        }

        // Validate poids_initial (optional)
        if (!empty($data['poids_initial'])) {
            if (!is_numeric($data['poids_initial'])) {
                $errors['poids_initial'] = 'Le poids initial doit être un nombre';
            } elseif ($data['poids_initial'] <= 0 || $data['poids_initial'] > 500) {
                $errors['poids_initial'] = 'Le poids initial doit être entre 0.01 et 500 kg';
            } else {
                $this->objectif->poids_initial = floatval($data['poids_initial']);
            }
        } else {
            $this->objectif->poids_initial = null;
        }

        // Validate date_limite (optional)
        if (!empty($data['date_limite'])) {
            $date_limite = DateTime::createFromFormat('Y-m-d', $data['date_limite']);
            if (!$date_limite) {
                $errors['date_limite'] = 'Format de date invalide (utilisez YYYY-MM-DD)';
            } else {
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                if ($date_limite < $today) {
                    $errors['date_limite'] = 'La date limite doit être dans le futur';
                } else {
                    $this->objectif->date_limite = $data['date_limite'];
                }
            }
        } else {
            $this->objectif->date_limite = null;
        }

        // Validate description (optional)
        if (!empty($data['description'])) {
            $description = trim($data['description']);
            if (strlen($description) > 1000) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            } else {
                $this->objectif->description = $description;
            }
        } else {
            $this->objectif->description = null;
        }

        // Validate statut
        $allowed_statuts = ['en_attente', 'en_cours', 'termine', 'annule'];
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide';
        } else {
            $this->objectif->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts) ? $data['statut'] : 'en_attente';
        }

        // Validate niveau_priorite
        $allowed_priorites = ['faible', 'moyen', 'eleve'];
        if (!empty($data['niveau_priorite']) && !in_array($data['niveau_priorite'], $allowed_priorites)) {
            $errors['niveau_priorite'] = 'Niveau de priorité invalide';
        } else {
            $this->objectif->niveau_priorite = isset($data['niveau_priorite']) && in_array($data['niveau_priorite'], $allowed_priorites) ? $data['niveau_priorite'] : 'moyen';
        }

        $this->objectif->user_id = $user_id;

        // If no errors, create objective
        if (empty($errors)) {
            if ($this->objectif->create()) {
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la création de l\'objectif'));
            }
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    // Update objective
    public function update($id, $data)
    {
        $errors = array();

        // Validate ID
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            $errors['general'] = 'ID d\'objectif invalide';
            return array('success' => false, 'errors' => $errors);
        }

        // Get existing objective
        $existing = $this->objectif->getById($id);
        if (!$existing) {
            return array('success' => false, 'errors' => array('general' => 'Objectif non trouvé'));
        }

        // Validate type_objectif
        $allowed_types = ['perte_poids', 'prise_poids', 'maintien_poids', 'augmentation_muscle', 'amelioration_endurance', 'reduction_gras'];
        if (empty($data['type_objectif'])) {
            $errors['type_objectif'] = 'Le type d\'objectif est requis';
        } elseif (!in_array($data['type_objectif'], $allowed_types)) {
            $errors['type_objectif'] = 'Type d\'objectif invalide';
        } else {
            $this->objectif->type_objectif = trim($data['type_objectif']);
        }

        // Validate valeur_cible
        if (empty($data['valeur_cible'])) {
            $errors['valeur_cible'] = 'La valeur cible est requise';
        } elseif (!is_numeric($data['valeur_cible'])) {
            $errors['valeur_cible'] = 'La valeur cible doit être un nombre';
        } elseif ($data['valeur_cible'] <= 0) {
            $errors['valeur_cible'] = 'La valeur cible doit être positive';
        } elseif ($data['valeur_cible'] > 999.99) {
            $errors['valeur_cible'] = 'La valeur cible ne peut pas dépasser 999.99';
        } else {
            $this->objectif->valeur_cible = floatval($data['valeur_cible']);
        }

        // Validate poids_initial (optional)
        if (!empty($data['poids_initial'])) {
            if (!is_numeric($data['poids_initial'])) {
                $errors['poids_initial'] = 'Le poids initial doit être un nombre';
            } elseif ($data['poids_initial'] <= 0 || $data['poids_initial'] > 500) {
                $errors['poids_initial'] = 'Le poids initial doit être entre 0.01 et 500 kg';
            } else {
                $this->objectif->poids_initial = floatval($data['poids_initial']);
            }
        } else {
            $this->objectif->poids_initial = null;
        }

        // Validate date_limite (optional)
        if (!empty($data['date_limite'])) {
            $date_limite = DateTime::createFromFormat('Y-m-d', $data['date_limite']);
            if (!$date_limite) {
                $errors['date_limite'] = 'Format de date invalide (utilisez YYYY-MM-DD)';
            } else {
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                if ($date_limite < $today) {
                    $errors['date_limite'] = 'La date limite doit être dans le futur';
                } else {
                    $this->objectif->date_limite = $data['date_limite'];
                }
            }
        } else {
            $this->objectif->date_limite = null;
        }

        // Validate description (optional)
        if (!empty($data['description'])) {
            $description = trim($data['description']);
            if (strlen($description) > 1000) {
                $errors['description'] = 'La description ne peut pas dépasser 1000 caractères';
            } else {
                $this->objectif->description = $description;
            }
        } else {
            $this->objectif->description = null;
        }

        // Validate statut
        $allowed_statuts = ['en_attente', 'en_cours', 'termine', 'annule'];
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide';
        } else {
            $this->objectif->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts) ? $data['statut'] : 'en_attente';
        }

        // Validate niveau_priorite
        $allowed_priorites = ['faible', 'moyen', 'eleve'];
        if (!empty($data['niveau_priorite']) && !in_array($data['niveau_priorite'], $allowed_priorites)) {
            $errors['niveau_priorite'] = 'Niveau de priorité invalide';
        } else {
            $this->objectif->niveau_priorite = isset($data['niveau_priorite']) && in_array($data['niveau_priorite'], $allowed_priorites) ? $data['niveau_priorite'] : 'moyen';
        }

        $this->objectif->id_objectif = $id;

        // If no errors, update objective
        if (empty($errors)) {
            if ($this->objectif->update()) {
                return array('success' => true, 'errors' => array());
            } else {
                return array('success' => false, 'errors' => array('general' => 'Erreur lors de la mise à jour de l\'objectif'));
            }
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    // Delete objective
    public function delete($id)
    {
        return $this->objectif->delete($id);
    }
}
?>