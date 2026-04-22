<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: backoffice/index.php');
    } else {
        header('Location: profile.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrimind - Connexion/Inscription</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        body{
            margin:0;
            color:#6a6f8c;
            background:#d4e8e8;
            font:600 16px/18px 'Open Sans',sans-serif;
        }

        .login-box{
            width:100%;
            margin:auto;
            max-width:525px;
            min-height:670px;
            position:relative;
            background:url(https://images.unsplash.com/photo-1507208773393-40d9fc670acf?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1268&q=80) no-repeat center;
            box-shadow:0 12px 15px 0 rgba(0,0,0,.24),0 17px 50px 0 rgba(0,0,0,.19);
        }
        .login-snip{
            width:100%;
            height:100%;
            position:absolute;
            padding:90px 70px 50px 70px;
            background:rgba(0, 77, 77,.9);
        }
        .login-snip .login,
        .login-snip .sign-up-form{
            top:0;
            left:0;
            right:0;
            bottom:0;
            position:absolute;
            transform:rotateY(180deg);
            backface-visibility:hidden;
            transition:all .4s linear;
        }
        .login-snip .sign-in,
        .login-snip .sign-up,
        .login-space .group .check{
            display:none;
        }
        .login-snip .tab,
        .login-space .group .label,
        .login-space .group .button{
            text-transform:uppercase;
        }
        .login-snip .tab{
            font-size:22px;
            margin-right:15px;
            padding-bottom:5px;
            margin:0 15px 10px 0;
            display:inline-block;
            border-bottom:2px solid transparent;
        }
        .login-snip .sign-in:checked + .tab,
        .login-snip .sign-up:checked + .tab{
            color:#fff;
            border-color:#1161ee;
        }
        .login-space{
            min-height:345px;
            position:relative;
            perspective:1000px;
            transform-style:preserve-3d;
        }
        .login-space .group{
            margin-bottom:15px;
        }
        .login-space .group .label,
        .login-space .group .input,
        .login-space .group .button{
            width:100%;
            color:#fff;
            display:block;
        }
        .login-space .group .input,
        .login-space .group .button{
            border:none;
            padding:15px 20px;
            border-radius:25px;
            background:rgba(255,255,255,.1);
        }
        .login-space .group input[data-type="password"]{
            text-security:circle;
            -webkit-text-security:circle;
        }
        .login-space .group .label{
            color:#aaa;
            font-size:12px;
        }
        .login-space .group .button{
            background:#1161ee;
        }
        .login-space .group label .icon{
            width:15px;
            height:15px;
            border-radius:2px;
            position:relative;
            display:inline-block;
            background:rgba(255,255,255,.1);
        }
        .login-space .group label .icon:before,
        .login-space .group label .icon:after{
            content:'';
            width:10px;
            height:2px;
            background:#fff;
            position:absolute;
            transition:all .2s ease-in-out 0s;
        }
        .login-space .group label .icon:before{
            left:3px;
            width:5px;
            bottom:6px;
            transform:scale(0) rotate(0);
        }
        .login-space .group label .icon:after{
            top:6px;
            right:0;
            transform:scale(0) rotate(0);
        }
        .login-space .group .check:checked + label{
            color:#fff;
        }
        .login-space .group .check:checked + label .icon{
            background:#1161ee;
        }
        .login-space .group .check:checked + label .icon:before{
            transform:scale(1) rotate(45deg);
        }
        .login-space .group .check:checked + label .icon:after{
            transform:scale(1) rotate(-45deg);
        }
        .login-snip .sign-in:checked + .tab + .sign-up + .tab + .login-space .login{
            transform:rotate(0);
        }
        .login-snip .sign-up:checked + .tab + .login-space .sign-up-form{
            transform:rotate(0);
        }

        *,:after,:before{box-sizing:border-box}
        .clearfix:after,.clearfix:before{content:'';display:table}
        .clearfix:after{clear:both;display:block}
        a{color:inherit;text-decoration:none}

        .hr{
            height:2px;
            margin:60px 0 50px 0;
            background:rgba(255,255,255,.2);
        }
        .foot{
            text-align:center;
        }
        .card{
            width: 500px;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .success-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            z-index: 1001;
            min-width: 300px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .success-modal h2 {
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .success-modal p {
            color: #666;
            margin-bottom: 20px;
        }

        .modal-overlay.active,
        .success-modal.active {
            display: block;
        }

        .success-icon {
            font-size: 50px;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .row {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
        }

        ::placeholder{
            color: #b3b3b3;
        }

        .register-btn {
            margin-top: 30px;
            text-align: center;
        }

        .register-btn a {
            color: #1161ee;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Success Modal -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="success-modal" id="successModal">
        <div class="success-icon">✓</div>
        <h2 id="modalTitle">Succès!</h2>
        <p id="modalMessage">Redirection en cours...</p>
    </div>
    <div class="row">
        <div class="col-md-6 mx-auto p-0">
            <div class="card">
                <div class="login-box">
                    <div class="login-snip">
                        <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Connexion</label>
                        <input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab">Inscription</label>
                        <div class="login-space">
                            <!-- LOGIN FORM -->
                            <div class="login">
                                <form id="loginForm">
                                    <div id="loginErrors" class="alert alert-danger" style="display:none;"></div>
                                    <div class="group">
                                        <label for="login-email" class="label">E-mail</label>
                                        <input id="login-email" type="email" class="input" name="email" placeholder="Entrez votre e-mail" required>
                                    </div>
                                    <div class="group">
                                        <label for="login-pass" class="label">Mot de passe</label>
                                        <input id="login-pass" type="password" class="input" name="password" data-type="password" placeholder="Entrez votre mot de passe" required>
                                    </div>
                                    <div class="group">
                                        <input id="check" type="checkbox" class="check" checked>
                                        <label for="check"><span class="icon"></span> Me garder connecté</label>
                                    </div>
                                    <div class="group">
                                        <input type="submit" class="button" value="Se connecter">
                                    </div>
                                    <div class="hr"></div>
                                    <div class="foot">
                                        <a href="#">Mot de passe oublié?</a>
                                    </div>
                                </form>
                            </div>

                            <!-- SIGN UP FORM -->
                            <div class="sign-up-form">
                                <form id="signupForm">
                                    <div id="signupErrors" class="alert alert-danger" style="display:none;"></div>
                                    <div class="group">
                                        <label for="signup-nom" class="label">Nom complet</label>
                                        <input id="signup-nom" type="text" class="input" name="nom" placeholder="Entrez votre nom complet" required>
                                    </div>
                                    <div class="group">
                                        <label for="signup-email" class="label">Adresse e-mail</label>
                                        <input id="signup-email" type="email" class="input" name="email" placeholder="Entrez votre adresse e-mail" required>
                                    </div>
                                    <div class="group">
                                        <label for="signup-pass" class="label">Mot de passe</label>
                                        <input id="signup-pass" type="password" class="input" name="mot_de_passe" data-type="password" placeholder="Créez votre mot de passe" required>
                                    </div>
                                    <div class="group">
                                        <label for="signup-confirm-pass" class="label">Confirmer le mot de passe</label>
                                        <input id="signup-confirm-pass" type="password" class="input" name="confirm_mot_de_passe" data-type="password" placeholder="Confirmez votre mot de passe" required>
                                    </div>
                                    <div class="group">
                                        <input type="submit" class="button" value="S'inscrire">
                                    </div>
                                    <div class="hr"></div>
                                    <div class="foot">
                                        <label for="tab-1">Déjà membre?</label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function showModal(title, message, redirectUrl = null) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('successModal').classList.add('active');
            
            if (redirectUrl) {
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 2000);
            }
        }

        // Login form submission - Direct redirect
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'signin');

            fetch('../controllers/UserController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('loginErrors');
                if (data.success) {
                    errorDiv.style.display = 'none';
                    // Check user role and redirect accordingly
                    if (data.role && data.role.toLowerCase() === 'admin') {
                        window.location.href = 'backoffice/index.php';
                    } else {
                        window.location.href = 'index.php';
                    }
                } else {
                    if (data.errors) {
                        errorDiv.innerHTML = data.errors.map(err => `<p>${err}</p>`).join('');
                    } else {
                        errorDiv.innerHTML = `<p>${data.message || 'Une erreur s\'est produite'}</p>`;
                    }
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loginErrors').innerHTML = '<p>Une erreur s\'est produite. Veuillez réessayer.</p>';
                document.getElementById('loginErrors').style.display = 'block';
            });
        });

        // Sign up form submission - Show modal
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'signup');

            fetch('../controllers/UserController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('signupErrors');
                if (data.success) {
                    errorDiv.style.display = 'none';
                    showModal('Bienvenue!', 'Votre compte a été créé avec succès. Redirection...', 'profile.php');
                } else {
                    if (data.errors) {
                        errorDiv.innerHTML = data.errors.map(err => `<p>${err}</p>`).join('');
                    } else {
                        errorDiv.innerHTML = `<p>${data.message || 'Une erreur s\'est produite'}</p>`;
                    }
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('signupErrors').innerHTML = '<p>Une erreur s\'est produite. Veuillez réessayer.</p>';
                document.getElementById('signupErrors').style.display = 'block';
            });
        });
    </script>
</body>
</html>
