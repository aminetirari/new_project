<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Get user profile
$profile = $user->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Profile - Nutrimind Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
  <style>
    .profile-container {
      max-width: 800px;
      margin: 30px auto;
      padding: 20px;
    }
    .profile-card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 30px;
    }
    .profile-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 20px;
    }
    .profile-header h2 {
      margin: 0;
      color: #333;
      font-size: 28px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: block;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #27ae60;
      box-shadow: 0 0 5px rgba(39, 174, 96, 0.3);
    }
    .submit-btn {
      background-color: #27ae60;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    .submit-btn:hover {
      background-color: #229954;
    }
    .delete-btn {
      background-color: #dc3545;
    }
    .delete-btn:hover {
      background-color: #c82333;
    }
    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
      border-bottom: 2px solid #e0e0e0;
    }
    .tab-btn {
      background: none;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #666;
      border-bottom: 3px solid transparent;
      margin-bottom: -2px;
      transition: all 0.3s;
    }
    .tab-btn.active {
      color: #27ae60;
      border-bottom-color: #27ae60;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
      }
      .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
    }
    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .checkbox-label input {
      width: auto;
    }
    .warning-box {
      background-color: #fff3cd;
      padding: 20px;
      border-radius: 5px;
      border-left: 4px solid #ffc107;
      margin-bottom: 30px;
    }
    .warning-box h4 {
      color: #856404;
      margin-bottom: 10px;
    }
    .warning-box p {
      color: #856404;
      margin: 0;
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
          <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 250px;">
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
                  <span>Profile</span>
                </a>
                <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-logout text-danger"></i>
                  <span>Déconnexion</span>
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
    <div class="logo-area"></div>
    <nav class="sidebar-nav">
      <ul class="nav flex-column">
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="ti ti-smart-home"></i> <span>Tableau de Bord</span></a></li>
        <li class="nav-item"><a href="users.php" class="nav-link"><i class="ti ti-users"></i> <span>Utilisateurs</span></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="ti ti-packages"></i> <span>Inventaire</span></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="ti ti-plus-circle"></i> <span>Ajouter un Produit</span></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="ti ti-chart-bar"></i> <span>Rapports</span></a></li>
      </ul>
    </nav>
  </aside>

  <!-- CONTENT -->
  <div class="main-content">
    <div class="profile-container">
      <div class="profile-card">
        <div class="profile-header">
          <h2>Profil Admin</h2>
        </div>

        <div id="message"></div>

        <div class="tabs">
          <button class="tab-btn active" onclick="switchTab('profile-tab', this)">Informations de Profil</button>
          <button class="tab-btn" onclick="switchTab('password-tab', this)">Changer le Mot de Passe</button>
          <button class="tab-btn" onclick="switchTab('delete-tab', this)">Supprimer le Compte</button>
        </div>

        <!-- PROFILE INFORMATION TAB -->
        <div id="profile-tab" class="tab-content active">
          <form id="profileForm">
            <div class="form-group">
              <label for="email">Adresse E-mail</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>
              <small style="color: #999;">L'e-mail ne peut pas être changé</small>
            </div>

            <div class="form-group">
              <label for="nom">Nom Complet *</label>
              <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($profile['nom']); ?>" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="age">Âge (années)</label>
                <input type="number" id="age" name="age" min="1" max="120" value="<?php echo $profile['age'] ?? ''; ?>">
              </div>

              <div class="form-group">
                <label for="taille">Hauteur (cm)</label>
                <input type="number" id="taille" name="taille" min="30" max="300" step="0.1" value="<?php echo $profile['taille'] ?? ''; ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="poids">Poids (kg)</label>
                <input type="number" id="poids" name="poids" min="1" max="500" step="0.1" value="<?php echo $profile['poids'] ?? ''; ?>">
              </div>

              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="allergique" name="allergique" <?php echo ($profile['allergique'] ? 'checked' : ''); ?>>
                  <span>J'ai des allergies</span>
                </label>
              </div>
            </div>

            <button type="submit" class="submit-btn">Mettre à Jour le Profil</button>
          </form>
        </div>

        <!-- CHANGE PASSWORD TAB -->
        <div id="password-tab" class="tab-content">
          <form id="passwordForm">
            <div class="form-group">
              <label for="current_password">Mot de Passe Actuel *</label>
              <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
              <label for="new_password">Nouveau Mot de Passe *</label>
              <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirmer le Nouveau Mot de Passe *</label>
              <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="submit-btn">Changer le Mot de Passe</button>
          </form>
        </div>

        <!-- DELETE ACCOUNT TAB -->
        <div id="delete-tab" class="tab-content">
          <div class="warning-box">
            <h4><i class="ti ti-alert-triangle"></i> Attention</h4>
            <p>Supprimer votre compte est une action permanente et irréversible. Toutes vos données et informations de profil seront supprimées de la base de données.</p>
          </div>
          <form id="deleteForm">
            <div class="form-group">
              <label for="delete_password">Confirmez votre Mot de Passe *</label>
              <input type="password" id="delete_password" name="delete_password" placeholder="Entrez votre mot de passe pour confirmer" required>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
              <label class="checkbox-label">
                <input type="checkbox" id="confirm_delete" name="confirm_delete" required>
                <span>Je comprends que cette action est permanente et je souhaite supprimer mon compte</span>
              </label>
            </div>

            <button type="submit" class="submit-btn delete-btn">Supprimer mon Compte Définitivement</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Logout Modal Styles -->
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
    
    @keyframes slideIn {
      from {
        transform: translateY(-50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
    
    @media (max-width: 480px) {
      .logout-modal-content {
        min-width: 300px;
      }
      
      .logout-modal-buttons {
        flex-direction: column;
      }
    }
  </style>

  <script>
    // Tab switching
    function switchTab(tabId, button) {
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
      });
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      document.getElementById(tabId).classList.add('active');
      button.classList.add('active');
    }

    // Show message
    function showMessage(type, text) {
      const messageDiv = document.getElementById('message');
      messageDiv.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
      messageDiv.scrollIntoView({ behavior: 'smooth' });
    }

    // Profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'update_profile');

      fetch('../../controllers/UserController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showMessage('success', data.message);
        } else {
          const errorMsg = data.errors ? data.errors.join('<br>') : data.message;
          showMessage('danger', errorMsg);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showMessage('danger', 'An error occurred. Please try again.');
      });
    });

    // Password form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'change_password');

      fetch('../../controllers/UserController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showMessage('success', data.message);
          document.getElementById('passwordForm').reset();
        } else {
          const errorMsg = data.errors ? data.errors.join('<br>') : data.message;
          showMessage('danger', errorMsg);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showMessage('danger', 'An error occurred. Please try again.');
      });
    });

    // Delete account form submission
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!document.getElementById('confirm_delete').checked) {
        showMessage('danger', 'You must confirm the deletion of your account');
        return;
      }

      const formData = new FormData(this);
      formData.append('action', 'delete_account');

      fetch('../../controllers/UserController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showMessage('success', 'Votre compte a été supprimé. Redirection...');
          setTimeout(() => {
            window.location.href = 'index.php';
          }, 2000);
        } else {
          const errorMsg = data.errors ? data.errors.join('<br>') : data.message;
          showMessage('danger', errorMsg);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showMessage('danger', 'An error occurred. Please try again.');
      });
    });

    // Logout confirmation modal
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
              window.location.href = 'index.php';
            } else {
              showMessage('danger', 'Error during logout');
              closeModal();
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showMessage('danger', 'An error occurred');
            closeModal();
          });
      });
    }
    
    // Logout function
    function logout() {
      showLogoutModal();
    }
  </script>
</body>
</html>
