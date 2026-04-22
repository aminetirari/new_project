<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->user = new User($this->db);
    }

    /**
     * Handle Sign Up
     */
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['mot_de_passe'] ?? '';
            $confirmPassword = $_POST['confirm_mot_de_passe'] ?? '';

            // Validate input
            $errors = User::validateRegistration($nom, $email, $password, $confirmPassword);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check if email already exists
            $this->user->email = $email;
            if ($this->user->emailExists()) {
                return ['success' => false, 'errors' => ['Email already registered']];
            }

            // Register user
            $this->user->nom = $nom;
            $this->user->email = $email;
            $this->user->mot_de_passe = $password;
            $this->user->role = 'user'; // Default role

            if ($this->user->register()) {
                // Auto login after registration
                $_SESSION['user_id'] = $this->db->lastInsertId();
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';

                return ['success' => true, 'message' => 'Registration successful! Redirecting...'];
            } else {
                return ['success' => false, 'errors' => ['Error registering user']];
            }
        }
    }

    /**
     * Handle Sign In
     */
    public function signin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validate input
            $errors = User::validateLogin($email, $password);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check credentials
            $this->user->email = $email;
            $this->user->mot_de_passe = $password;

            $userData = $this->user->login();

            if ($userData) {
                // Set session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_nom'] = $userData['nom'];
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['user_role'] = $userData['role'];
                $_SESSION['logged_in'] = true;

                return ['success' => true, 'message' => 'Login successful! Redirecting...', 'role' => $userData['role']];
            } else {
                return ['success' => false, 'errors' => ['Invalid email or password']];
            }
        }
    }

    /**
     * Handle Logout
     */
    public function logout() {
        $_SESSION = [];
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Handle Profile Update
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $nom = trim($_POST['nom'] ?? '');
            $age = trim($_POST['age'] ?? '');
            $poids = trim($_POST['poids'] ?? '');
            $taille = trim($_POST['taille'] ?? '');
            $allergique = isset($_POST['allergique']) ? 1 : 0;

            // Validate input
            $errors = User::validateProfile($nom, $age, $poids, $taille);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Update user with proper type conversion
            $this->user->id = $_SESSION['user_id'];
            $this->user->nom = $nom;
            $this->user->age = !empty($age) ? (int)$age : NULL;
            $this->user->poids = !empty($poids) ? (float)$poids : NULL;
            $this->user->taille = !empty($taille) ? (float)$taille : NULL;
            $this->user->allergique = (int)$allergique;

            if ($this->user->updateProfile()) {
                // Update session
                $_SESSION['user_nom'] = $nom;

                return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
            } else {
                return ['success' => false, 'errors' => ['Failed to update profile. Please try again.']];
            }
        }
    }

    /**
     * Handle Password Change
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            $errors = [];

            if (empty($currentPassword)) {
                $errors[] = 'Current password is required';
            }

            if (empty($newPassword)) {
                $errors[] = 'New password is required';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Verify current password
            $userData = $this->user->getUserById($_SESSION['user_id']);

            if (!password_verify($currentPassword, $userData['mot_de_passe'])) {
                return ['success' => false, 'errors' => ['Current password is incorrect']];
            }

            // Update password
            $this->user->id = $_SESSION['user_id'];
            $this->user->mot_de_passe = $newPassword;

            if ($this->user->updatePassword()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'errors' => ['Error changing password']];
            }
        }
    }

    /**
     * Get User Profile
     */
    public function getProfile() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->user->getUserById($_SESSION['user_id']);
    }

    /**
     * Get all users (for admin use)
     */
    public function getAllUsers() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return [];
        }

        return $this->user->getAllUsers();
    }
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Delete User Account
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $password = $_POST['delete_password'] ?? '';

            // Validate password
            if (empty($password)) {
                return ['success' => false, 'errors' => ['Password is required']];
            }

            // Verify password
            $userData = $this->user->getUserById($_SESSION['user_id']);

            if (!password_verify($password, $userData['mot_de_passe'])) {
                return ['success' => false, 'errors' => ['Password is incorrect']];
            }

            // Delete user account
            if ($this->user->deleteAccount($_SESSION['user_id'])) {
                // Clear session
                $_SESSION = [];
                session_destroy();

                return ['success' => true, 'message' => 'Account deleted successfully'];
            } else {
                return ['success' => false, 'errors' => ['Error deleting account']];
            }
        }
    }
}

if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    // Handle requests
    $method = $_POST['action'] ?? $_GET['action'] ?? null;

    $controller = new UserController();
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($method) {
        case 'signup':
            $response = $controller->signup();
            break;

        case 'signin':
            $response = $controller->signin();
            break;

        case 'logout':
            $response = $controller->logout();
            break;

        case 'update_profile':
            $response = $controller->updateProfile();
            break;

        case 'change_password':
            $response = $controller->changePassword();
            break;

        case 'delete_account':
            $response = $controller->deleteAccount();
            break;

        case 'get_profile':
            $response = ['success' => true, 'data' => $controller->getProfile()];
            break;

        default:
            // If it's a POST request but no action specified, try to determine from form
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check for hidden field or form context
                if (isset($_POST['nom']) && isset($_POST['mot_de_passe']) && !isset($_POST['age'])) {
                    // Likely signup
                    $response = $controller->signup();
                } elseif (isset($_POST['email']) && isset($_POST['password']) && !isset($_POST['confirm_mot_de_passe'])) {
                    // Likely signin
                    $response = $controller->signin();
                }
            }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
