<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../controllers/PlanningController.php';
require_once '../../controllers/UserController.php';
require_once '../../controllers/ObjectiveController.php';

$planningController = new PlanningController();
$userController = new UserController();
$objectiveController = new ObjectiveController();

// Get planning ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: planning_list.php');
    exit;
}

// Get planning data
$planning = $planningController->getById($id);

if (!$planning) {
    header('Location: planning_list.php');
    exit;
}

// Get all users and their objectives
$users = $userController->getAllUsers();
$objectives = $objectiveController->getAllObjectives();

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $planningController->update($id, $_POST);

    if ($result['success']) {
        $_SESSION['success_message'] = "Plan modifié avec succès!";
        header('Location: planning_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Planning - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logooo.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>
<body>
  <div id="overlay" class="overlay"></div>
  <!-- TOPBAR -->
  <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm ">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <!-- MOBILE -->
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div>
      <h4 class="mb-0">Edit Planning</h4>
    </div>
  </nav>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <div class="logo-area">
     <a href="index.php" class="d-inline-flex"><img src="assets/images/logooo.png" alt="Nutrimind" style="max-height: 50px; width: auto;"></a>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Main</small></li>
      <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span
            class="nav-text">Dashboard</span></a></li>
      <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span
            class="nav-text">Users</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link active" href="planning_list.php"><i class="ti ti-calendar-event"></i><span
            class="nav-text">Manage Plans</span></a></li>
      <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span
            class="nav-text">Create Plan</span></a></li>
      <li><a class="nav-link" href="inventory.html"><i class="ti ti-box-seam"></i><span
            class="nav-text">Inventory</span></a></li>
      <li><a class="nav-link" href="create-product.html"><i class="ti ti-plus"></i><span class="nav-text">Add
            Product</span></a></li>
    <li><a class="nav-link" href="reports.html"><i class="ti ti-receipt"></i><span class="nav-text">Reports</span></a>
      </li>
    <li><a class="nav-link" href="404-error.html"><i class="ti ti-alert-circle"></i><span class="nav-text">404 Error</span></a>
      </li>
      <li><a class="nav-link" href="docs.html"><i class="ti ti-file-text"></i><span class="nav-text">Docs</span></a></li>


      <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
      <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a>
      </li>
    </ul>
  </aside>

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h1 class="fs-3 mb-1">Modifier le Plan</h1>
              <p>Modifier les détails du plan nutritionnel</p>
            </div>
            <a href="planning_list.php" class="btn btn-secondary">
              <i class="ti ti-arrow-left me-2"></i>Retour à la liste
            </a>
          </div>
        </div>
      </div>

      <!-- Error Messages -->
      <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger">
          <?php echo htmlspecialchars($errors['general']); ?>
        </div>
      <?php endif; ?>

      <!-- Success Messages -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Modifier le Plan Nutritionnel</h4>
              <p class="card-description">Modifiez les détails du plan nutritionnel pour l'utilisateur.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="user_id" class="form-label font-weight-bold">Utilisateur <span class="text-danger">*</span></label>
                      <select class="form-control <?php echo isset($errors['user_id']) ? 'is-invalid' : ''; ?>"
                          id="user_id" name="user_id" required onchange="loadUserObjectives()">
                        <option value="">Sélectionner un utilisateur</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?php echo htmlspecialchars($user['id']); ?>"
                                  <?php echo ($planning['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['nom'] . ' (' . $user['email'] . ')'); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (isset($errors['user_id'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['user_id']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label for="objectif_id" class="form-label font-weight-bold">Objectif <span class="text-danger">*</span></label>
                      <select class="form-control <?php echo isset($errors['objectif_id']) ? 'is-invalid' : ''; ?>"
                          id="objectif_id" name="objectif_id" required>
                        <option value="">Sélectionner un objectif</option>
                        <?php foreach ($objectives as $objectif): ?>
                          <option value="<?php echo htmlspecialchars($objectif['id_objectif']); ?>"
                                  data-user="<?php echo htmlspecialchars($objectif['user_id']); ?>"
                                  <?php echo ($planning['objectif_id'] == $objectif['id_objectif']) ? 'selected' : ''; ?>
                                  style="display: <?php echo ($planning['user_id'] == $objectif['user_id']) ? 'block' : 'none'; ?>;">
                            <?php echo htmlspecialchars($objectif['type_objectif'] . ' - ' . substr($objectif['description'] ?? 'Sans description', 0, 30)); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (isset($errors['objectif_id'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['objectif_id']; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="titre" class="form-label font-weight-bold">Titre du plan</label>
                      <input type="text" class="form-control <?php echo isset($errors['titre']) ? 'is-invalid' : ''; ?>"
                          id="titre" name="titre" placeholder="Ex: Plan perte de poids semaine 1"
                          value="<?php echo htmlspecialchars($planning['titre'] ?? ''); ?>" maxlength="100">
                      <?php if (isset($errors['titre'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['titre']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label for="statut" class="form-label font-weight-bold">Statut</label>
                      <select class="form-control" id="statut" name="statut">
                        <option value="actif" <?php echo ($planning['statut'] == 'actif') ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactif" <?php echo ($planning['statut'] == 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                        <option value="termine" <?php echo ($planning['statut'] == 'termine') ? 'selected' : ''; ?>>Terminé</option>
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="date_debut" class="form-label font-weight-bold">Date de début</label>
                      <input type="date" class="form-control <?php echo isset($errors['date_debut']) ? 'is-invalid' : ''; ?>"
                          id="date_debut" name="date_debut"
                          value="<?php echo htmlspecialchars($planning['date_debut'] ?? ''); ?>">
                      <?php if (isset($errors['date_debut'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['date_debut']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label for="date_fin" class="form-label font-weight-bold">Date de fin</label>
                      <input type="date" class="form-control <?php echo isset($errors['date_fin']) ? 'is-invalid' : ''; ?>"
                          id="date_fin" name="date_fin"
                          value="<?php echo htmlspecialchars($planning['date_fin'] ?? ''); ?>">
                      <?php if (isset($errors['date_fin'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['date_fin']; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="description" class="form-label font-weight-bold">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>"
                        id="description" name="description" rows="3" maxlength="1000"
                        placeholder="Description détaillée du plan nutritionnel"><?php echo htmlspecialchars($planning['description'] ?? ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                      <div class="invalid-feedback d-block">
                        <?php echo $errors['description']; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="calories_par_jour" class="form-label font-weight-bold">Calories par jour</label>
                      <input type="number" class="form-control <?php echo isset($errors['calories_par_jour']) ? 'is-invalid' : ''; ?>"
                          id="calories_par_jour" name="calories_par_jour" min="500" max="10000"
                          value="<?php echo htmlspecialchars($planning['calories_par_jour'] ?? ''); ?>">
                      <?php if (isset($errors['calories_par_jour'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['calories_par_jour']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                      <label for="objectif_proteines" class="form-label font-weight-bold">Protéines (g)</label>
                      <input type="number" class="form-control <?php echo isset($errors['objectif_proteines']) ? 'is-invalid' : ''; ?>"
                          id="objectif_proteines" name="objectif_proteines" min="0" max="2000" step="0.1"
                          value="<?php echo htmlspecialchars($planning['objectif_proteines'] ?? ''); ?>">
                      <?php if (isset($errors['objectif_proteines'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['objectif_proteines']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                      <label for="objectif_glucides" class="form-label font-weight-bold">Glucides (g)</label>
                      <input type="number" class="form-control <?php echo isset($errors['objectif_glucides']) ? 'is-invalid' : ''; ?>"
                          id="objectif_glucides" name="objectif_glucides" min="0" max="2000" step="0.1"
                          value="<?php echo htmlspecialchars($planning['objectif_glucides'] ?? ''); ?>">
                      <?php if (isset($errors['objectif_glucides'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['objectif_glucides']; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="objectif_lipides" class="form-label font-weight-bold">Lipides (g)</label>
                      <input type="number" class="form-control <?php echo isset($errors['objectif_lipides']) ? 'is-invalid' : ''; ?>"
                          id="objectif_lipides" name="objectif_lipides" min="0" max="2000" step="0.1"
                          value="<?php echo htmlspecialchars($planning['objectif_lipides'] ?? ''); ?>">
                      <?php if (isset($errors['objectif_lipides'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['objectif_lipides']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                      <label for="nombre_repas_par_jour" class="form-label font-weight-bold">Nombre de repas par jour</label>
                      <input type="number" class="form-control <?php echo isset($errors['nombre_repas_par_jour']) ? 'is-invalid' : ''; ?>"
                          id="nombre_repas_par_jour" name="nombre_repas_par_jour" min="1" max="10"
                          value="<?php echo htmlspecialchars($planning['nombre_repas_par_jour'] ?? ''); ?>">
                      <?php if (isset($errors['nombre_repas_par_jour'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['nombre_repas_par_jour']; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                      <label for="heures_sommeil_par_jour" class="form-label font-weight-bold">Heures de sommeil par jour</label>
                      <input type="number" class="form-control <?php echo isset($errors['heures_sommeil_par_jour']) ? 'is-invalid' : ''; ?>"
                          id="heures_sommeil_par_jour" name="heures_sommeil_par_jour" min="0" max="24" step="0.5"
                          value="<?php echo htmlspecialchars($planning['heures_sommeil_par_jour'] ?? ''); ?>">
                      <?php if (isset($errors['heures_sommeil_par_jour'])): ?>
                        <div class="invalid-feedback d-block">
                          <?php echo $errors['heures_sommeil_par_jour']; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="heures_entrainement_par_jour" class="form-label font-weight-bold">Heures d'entraînement par jour</label>
                    <input type="number" class="form-control <?php echo isset($errors['heures_entrainement_par_jour']) ? 'is-invalid' : ''; ?>"
                        id="heures_entrainement_par_jour" name="heures_entrainement_par_jour" min="0" max="24" step="0.5"
                        value="<?php echo htmlspecialchars($planning['heures_entrainement_par_jour'] ?? ''); ?>">
                    <?php if (isset($errors['heures_entrainement_par_jour'])): ?>
                      <div class="invalid-feedback d-block">
                        <?php echo $errors['heures_entrainement_par_jour']; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary">Modifier le Plan</button>
                    <a href="planning_list.php" class="btn btn-secondary ml-2">Annuler</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    function loadUserObjectives() {
      const userId = document.getElementById('user_id').value;
      const objectifSelect = document.getElementById('objectif_id');
      const options = objectifSelect.querySelectorAll('option');

      options.forEach(option => {
        if (option.value === '') {
          option.style.display = 'block';
          return;
        }
        if (option.getAttribute('data-user') === userId) {
          option.style.display = 'block';
        } else {
          option.style.display = 'none';
        }
      });

      // Reset selection if current selection is not for this user
      if (objectifSelect.value) {
        const selectedOption = objectifSelect.querySelector(`option[value="${objectifSelect.value}"]`);
        if (selectedOption && selectedOption.getAttribute('data-user') !== userId) {
          objectifSelect.value = '';
        }
      }
    }

    // Load objectives on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadUserObjectives();
    });

    // Logout function
    function showLogoutModal() {
        const modal = document.createElement('div');
        modal.className = 'logout-modal-overlay';
        modal.innerHTML = `
            <div class="logout-modal">
                <div class="logout-modal-content">
                    <h3>Confirmation de Déconnexion</h3>
                    <p>Êtes-vous sûr de vouloir vous déconnecter?</p>
                    <div class="logout-modal-buttons">
                        <button class="logout-btn-cancel">Annuler</button>
                        <button class="logout-btn-confirm">Déconnexion</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.logout-btn-cancel');
        const confirmBtn = modal.querySelector('.logout-btn-confirm');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            fetch('../../controllers/UserController.php?action=logout')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../index.php';
                    } else {
                        alert('Error during logout');
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    alert('An error occurred during logout');
                });
        });
    }
    
    function logout() {
        showLogoutModal();
    }
  </script>
</body>
</html>