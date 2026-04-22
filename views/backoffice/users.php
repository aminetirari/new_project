<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admin users to home page
    header('Location: ../index.php');
    exit;
}

require_once '../../config/Database.php';

// Get all users from database
$database = new Database();
$db = $database->connect();

$query = "SELECT id, nom, email, role, date_creation FROM user ORDER BY date_creation DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Utilisateurs - Tableau de Bord Nutrimind</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
  <style>
    .role-badge {
      display: inline-block;
      padding: 0.35rem 0.65rem;
      border-radius: 0.25rem;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .role-admin {
      background-color: #fee;
      color: #c33;
    }
    .role-user {
      background-color: #efe;
      color: #3c3;
    }
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
    }
  </style>
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
    <div class="ms-auto">
      <!-- Navbar nav -->
      <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
        <!-- Dropdown -->
        <li class="ms-3 dropdown">
          <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 250px; position: absolute; right: 0; top: 100%; margin-top: 0.5rem;" data-popper-placement="bottom-end">
            <div>
              <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-4 py-3">
                <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-md rounded-circle" />
                <div>
                  <h5 class="mb-0 small fw-600"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></h5>
                  <p class="mb-0 text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                </div>
              </div>
              <div class="p-3 d-flex flex-column gap-2 small lh-lg">
                <a href="profile.php" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-user text-success"></i>
                  <span>Déconnexion</span>
                </a>
                <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-logout text-danger"></i>
                  <span>Déconnexion</span>
                </a>
              </div>
            </div>
          </div>
                  <span>Logout</span>
                </a>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <div class="logo-area">
     <a href="index.php" class="d-inline-flex"><img src="data:image/svg+xml,%3csvg%20width='62'%20height='67'%20viewBox='0%200%2062%2067'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M30.604%2066.378L0.00805664%2048.1582V35.7825L30.604%2054.0023V66.378Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2048.1582L30.604%2066.378V54.0023L61.1996%2035.7825V48.1582Z'%20fill='%23E66239'/%3e%3cpath%20d='M30.5955%200L0%2018.2198V30.5955L30.5955%2012.3757V0Z'%20fill='%23657E92'/%3e%3cpath%20d='M61.191%2018.2198L30.5955%200V12.3757L61.191%2030.5955V18.2198Z'%20fill='%23A3B2BE'/%3e%3cpath%20d='M30.604%2048.8457L0.00805664%2030.6259V18.2498L30.604%2036.47V48.8457Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2030.6259L30.604%2048.8457V36.47L61.1996%2018.2498V30.6259Z'%20fill='%23E66239'/%3e%3c/svg%3e" alt="" width="24">
        <span class="logo-text ms-2"> <img src="assets/images/logo.svg" alt=""></span>
      </a>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Main</small></li>
      <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span
            class="nav-text">Dashboard</span></a></li>
      <li><a class="nav-link active" href="users.php"><i class="ti ti-users"></i><span
            class="nav-text">Users</span></a></li>
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
      <div class="row ">
        <div class="col-12">
          <div class="mb-6">
            <h1 class="fs-3 mb-1">Gestion des Utilisateurs</h1>
            <p>Afficher et gérer tous les utilisateurs enregistrés</p>
          </div>
        </div>
      </div>
      
      <!-- Users Table -->
      <div class="row g-3">
        <div class="col-12">
          <div class="card">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Utilisateur</th>
                      <th>Courrier Électronique</th>
                      <th>Rôle</th>
                      <th>Date d'Inscription</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($users)): ?>
                      <?php foreach ($users as $user): ?>
                        <tr>
                          <td>
                            <div class="d-flex align-items-center gap-2">
                              <div class="user-avatar">
                                <?php echo strtoupper(substr($user['nom'], 0, 1)); ?>
                              </div>
                              <div>
                                <p class="mb-0 fw-semi-bold"><?php echo htmlspecialchars($user['nom']); ?></p>
                              </div>
                            </div>
                          </td>
                          <td><?php echo htmlspecialchars($user['email']); ?></td>
                          <td>
                            <span class="role-badge role-<?php echo strtolower($user['role']); ?>">
                              <?php echo strtolower($user['role']) === 'admin' ? 'Administrateur' : 'Utilisateur'; ?>
                            </span>
                          </td>
                          <td>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($user['date_creation'])); ?></small>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center py-4">
                          <p class="text-muted mb-0">Aucun utilisateur trouvé</p>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="row g-3 mt-4">
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo count($users); ?></h3>
                  <span class="text-muted">Utilisateurs Totaux</span>
                </div>
                <div>
                  <i class="ti ti-users fs-1 text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></h3>
                  <span class="text-muted">Utilisateurs Administrateurs</span>
                </div>
                <div>
                  <i class="ti ti-shield-check fs-1 text-danger"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'user')); ?></h3>
                  <span class="text-muted">Utilisateurs Normaux</span>
                </div>
                <div>
                  <i class="ti ti-user-check fs-1 text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
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

    // Initialize dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdownToggle = document.querySelector('.dropdown button[data-bs-toggle="dropdown"], .dropdown a[data-bs-toggle="dropdown"]');
        if (profileDropdownToggle) {
            profileDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                    dropdown.classList.toggle('show');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
            dropdownMenus.forEach(menu => {
                const dropdownContainer = menu.closest('.dropdown');
                if (dropdownContainer && !dropdownContainer.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
  </script>

  <style>
    .logout-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .logout-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }
    
    .logout-modal-content {
        padding: 30px;
        min-width: 400px;
        text-align: center;
    }
    
    .logout-modal-content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .logout-modal-content p {
        margin: 0 0 30px 0;
        color: #666;
        font-size: 16px;
    }
    
    .logout-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .logout-btn-cancel, .logout-btn-confirm {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .logout-btn-cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .logout-btn-cancel:hover {
        background-color: #d0d0d0;
    }
    
    .logout-btn-confirm {
        background-color: #dc3545;
        color: white;
    }
    
    .logout-btn-confirm:hover {
        background-color: #c82333;
    }

    /* Dropdown menu positioning to keep it on screen */
    .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        margin-top: 0.5rem !important;
        z-index: 1050 !important;
        max-height: 90vh;
        overflow-y: auto;
    }

    .dropdown-menu.show {
        display: block !important;
    }

    .dropdown {
        position: relative;
    }
  </style>

</body>
</html>
