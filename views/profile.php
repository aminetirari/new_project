<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: auth.php');
    exit;
}

require_once '../config/Database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Get user profile
$profile = $user->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NutriMind - Mon Profil">

    <!-- title -->
    <title>Nutrimind - Mon Profil</title>

    <!-- favicon -->
    <link rel="shortcut icon" type="image/png" href="assets/img/favicon.png">
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <!-- fontawesome -->
    <link rel="stylesheet" href="assets/css/all.min.css">
    <!-- bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- main style -->
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- responsive -->
    <link rel="stylesheet" href="assets/css/responsive.css">
    <style>
        .profile-section {
            background-color: #f4f4f4;
            padding: 100px 0 60px 0;
            min-height: calc(100vh - 250px);
        }
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
            color: #004d4d;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
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
            border-color: #1161ee;
            box-shadow: 0 0 5px rgba(17, 97, 238, 0.3);
        }
        .submit-btn {
            background-color: #1161ee;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .submit-btn:hover {
            background-color: #0a51d1;
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
            color: #1161ee;
            border-bottom-color: #1161ee;
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
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-label input {
            width: auto;
        }
    </style>
</head>
<body>
    <!--PreLoader-->
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
    <!--PreLoader Ends-->

    <!-- header -->
    <div class="top-header-area" id="sticker">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-sm-12 text-center">
                    <div class="main-menu-wrap">
                        <!-- logo -->
                        <div class="site-logo">
                            <a href="index.php">
                                <img src="assets/img/logooo.png" alt="">
                            </a>
                        </div>
                        <!-- logo -->

                        <!-- menu start -->
                        <nav class="main-menu">
                            <ul>
                                <li class="current-list-item"><a href="#">Accueil</a>
                                    <ul class="sub-menu">
                                        <li><a href="index.php">Accueil</a></li>
                                    </ul>
                                </li>
                                <li><a href="about.html">À propos</a></li>
                                <li><a href="posts_list.php">Blog</a></li>
                                <li><a href="#">Pages</a>
                                    <ul class="sub-menu">
                                        <li><a href="404.html">Page 404</a></li>
                                        <li><a href="about.html">À propos</a></li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Repas</a>
                                    <ul class="sub-menu">
                                        <li><a href="meal_list.php">Repas</a></li>
                                        <li><a href="ingredient_list.php">Ingrédients</a></li>
                                    </ul>
                                </li>
                                <li><a href="contact.html">Contact</a></li>
                                <li>
                                    <div class="header-icons">
                                        <a class="shopping-cart" href="cart.html"><i class="fas fa-shopping-cart"></i></a>
                                        <a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>
                                        <div class="user-menu-wrapper">
                                            <a class="mobile-hide user-icon" href="#"><i class="fas fa-user"></i></a>
                                            <div class="user-dropdown">
                                                <a href="profile.php"><i class="fas fa-cog"></i> Mon Profil</a>
                                                <a href="#" onclick="logout(); return false;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </nav>
                        <a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a>
                        <div class="mobile-menu"></div>
                        <!-- menu end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end header -->

    <!-- Profile Section -->
    <div class="profile-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="profile-card">
            <div class="profile-header">
                <h2>Mon Profil</h2>
                <button class="logout-btn" onclick="logout()">Déconnexion</button>
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
                            <label for="age">Age (années)</label>
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
                <div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 30px;">
                    <h4 style="color: #856404; margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Attention</h4>
                    <p style="color: #856404; margin: 0;">Supprimer votre compte est une action permanente et irréversible. Toutes vos données et informations de profil seront supprimées de la base de données.</p>
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

                    <button type="submit" class="submit-btn" style="background-color: #dc3545;">Supprimer mon Compte Définitivement</button>
                </form>
            </div>
        </div>
    </div>

    <!-- footer -->
    <div class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box about-widget">
                        <h2 class="widget-title">À propos de nous</h2>
                        <p>NutriMind vous propose les meilleurs outils de gestion nutritionnelle. Trouvez tous les ingrédients et repas que vous recherchez.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box get-in-touch">
                        <h2 class="widget-title">Nous Contacter</h2>
                        <ul>
                            <li>34/8, East Hukupara, Gifirtok, Sadan.</li>
                            <li>support@NutriMind.com</li>
                            <li>+00 111 222 3333</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box pages">
                        <h2 class="widget-title">Pages</h2>
                        <ul>
                            <li><a href="index.php">Accueil</a></li>
                            <li><a href="about.html">À propos</a></li>
                            <li><a href="ingredient_list.php">Ingrédients</a></li>
                            <li><a href="meal_list.php">Repas</a></li>
                            <li><a href="contact.html">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box subscribe">
                        <h2 class="widget-title">S'abonner</h2>
                        <p>Abonnez-vous à notre liste de diffusion pour obtenir les dernières mises à jour.</p>
                        <form action="index.html">
                            <input type="email" placeholder="Email">
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end footer -->

    <!-- copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <p>Copyright &copy; 2024 NutriMind. All rights reserved.</p>
                </div>
                <div class="col-lg-6 text-right col-md-12">
                    <p>Designed by <a href="https://imransdesign.com/">Imran Hossain</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>

    <style>
        .user-menu-wrapper {
            position: relative;
            display: inline-block;
        }

        .user-icon {
            cursor: pointer;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .user-dropdown a:last-child {
            border-bottom: none;
        }

        .user-dropdown a:hover {
            background-color: #f5f5f5;
            color: #1161ee;
        }

        .user-dropdown i {
            margin-right: 10px;
            width: 14px;
        }

        /* Logout Modal Styles */
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

            fetch('../controllers/UserController.php', {
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
                showMessage('danger', 'Une erreur s\'est produite. Veuillez réessayer.');
            });
        });

        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'change_password');

            fetch('../controllers/UserController.php', {
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
                showMessage('danger', 'Une erreur s\'est produite. Veuillez réessayer.');
            });
        });

        // Delete account form submission
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!document.getElementById('confirm_delete').checked) {
                showMessage('danger', 'Vous devez confirmer la suppression de votre compte');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'delete_account');

            fetch('../controllers/UserController.php', {
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
                showMessage('danger', 'Une erreur s\'est produite. Veuillez réessayer.');
            });
        });

        // User menu toggle
        document.querySelector('.user-icon').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.user-menu-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const dropdown = wrapper.querySelector('.user-dropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
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
                fetch('../controllers/UserController.php?action=logout')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'index.php';
                        } else {
                            showMessage('danger', 'Erreur lors de la déconnexion');
                            closeModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('danger', 'Une erreur s\'est produite');
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
