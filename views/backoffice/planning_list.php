<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../controllers/PlanningController.php';
require_once '../../controllers/ObjectiveController.php';
require_once '../../controllers/UserController.php';

$planningController = new PlanningController();
$objectiveController = new ObjectiveController();
$userController = new UserController();

$plannings = $planningController->getAllWithDetails();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($planningController->delete($deleteId)) {
        $_SESSION['success_message'] = "Plan supprimé avec succès!";
        header('Location: planning_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du plan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Planning Management - NutriMind Admin</title>
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
      <h4 class="mb-0">Planning Management</h4>
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
              <h1 class="fs-3 mb-1">Gestion des Plans</h1>
              <p>Gérer les plans nutritionnels des utilisateurs</p>
            </div>
            <a href="planning_create.php" class="btn btn-primary">
              <i class="ti ti-plus me-2"></i>Créer un Plan
            </a>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Plans Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>ID</th>
                      <th>Utilisateur</th>
                      <th>Objectif</th>
                      <th>Titre</th>
                      <th>Calories/Jour</th>
                      <th>Date Début</th>
                      <th>Date Fin</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($plannings)): ?>
                      <tr>
                        <td colspan="9" class="text-center">Aucun plan trouvé</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($plannings as $planning): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($planning['id_planning']); ?></td>
                          <td><?php echo htmlspecialchars($planning['user_name'] ?? 'Utilisateur inconnu'); ?></td>
                          <td><?php echo htmlspecialchars($planning['objectif_type'] ?? 'Objectif inconnu'); ?></td>
                          <td><?php echo htmlspecialchars($planning['titre'] ?? 'Sans titre'); ?></td>
                          <td><?php echo htmlspecialchars($planning['calories_par_jour'] ?? '-'); ?> kcal</td>
                          <td><?php echo htmlspecialchars($planning['date_debut'] ?? '-'); ?></td>
                          <td><?php echo htmlspecialchars($planning['date_fin'] ?? '-'); ?></td>
                          <td>
                            <span class="badge bg-<?php
                              switch($planning['statut']) {
                                case 'actif': echo 'success'; break;
                                case 'inactif': echo 'secondary'; break;
                                case 'termine': echo 'info'; break;
                                default: echo 'light';
                              }
                            ?>">
                              <?php echo htmlspecialchars($planning['statut'] ?? 'inconnu'); ?>
                            </span>
                          </td>
                          <td>
                            <a href="planning_edit.php?id=<?php echo htmlspecialchars($planning['id_planning']); ?>" class="btn btn-sm btn-warning">
                              <i class="ti ti-edit"></i> Modifier
                            </a>
                            <a href="#" class="btn btn-sm btn-danger delete-planning" data-id="<?php echo htmlspecialchars($planning['id_planning']); ?>">
                              <i class="ti ti-trash"></i> Supprimer
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Êtes-vous sûr de vouloir supprimer ce plan ? Cette action est irréversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Delete confirmation
    document.querySelectorAll('.delete-planning').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const planningId = this.getAttribute('data-id');
        document.getElementById('confirmDelete').href = 'planning_list.php?delete=' + planningId;
        $('#deleteModal').modal('show');
      });
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