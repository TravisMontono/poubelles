<?php
// auth.php

session_start();

// Database connection (replace with your actual credentials)
$servername = "";
$username = "root";
$password = "";
$dbname = "ecofuture";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$registration_error = '';
$login_error = '';

// Handle registration
if (isset($_POST['register_submit'])) {
    $register_nom = $_POST['register_nom'] ?? '';
    $register_prenom = $_POST['register_prenom'] ?? '';
    $register_email = $_POST['register_email'] ?? '';
    $register_password = $_POST['register_password'] ?? '';

    if (!empty($register_nom) && !empty($register_prenom) && !empty($register_email) && !empty($register_password)) {
        // Check if email already exists
        $check_email_sql = "SELECT id FROM utilisateurs WHERE email = ?";
        $check_email_stmt = $conn->prepare($check_email_sql);
        $check_email_stmt->bind_param("s", $register_email);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();

        if ($check_email_result->num_rows > 0) {
            $registration_error = "Cet email est déjà enregistré.";
        } else {
            // Hash the password
            $hashed_password = password_hash($register_password, PASSWORD_DEFAULT);

            // Insert new user
            $insert_sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, date_creation) VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $register_nom, $register_prenom, $register_email, $hashed_password);

            if ($insert_stmt->execute()) {
                // Registration successful, redirect to index.php
                header("Location: index.php?registered=1");
                exit();
            } else {
                $registration_error = "Erreur lors de l'enregistrement.";
            }
            $insert_stmt->close();
        }
        $check_email_stmt->close();
    } else {
        $registration_error = "Tous les champs sont requis pour l'inscription.";
    }
}

// Handle login
if (isset($_POST['login_submit'])) {
    $login_email = $_POST['login_email'] ?? '';
    $login_password = $_POST['login_password'] ?? '';

    if (!empty($login_email) && !empty($login_password)) {
        $login_sql = "SELECT id, nom, prenom, mot_de_passe FROM utilisateurs WHERE email = ?";
        $login_stmt = $conn->prepare($login_sql);
        $login_stmt->bind_param("s", $login_email);
        $login_stmt->execute();
        $login_result = $login_stmt->get_result();

        if ($login_result->num_rows > 0) {
            $user = $login_result->fetch_assoc();
            if (password_verify($login_password, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom_utilisateur'] = $user['prenom'] . ' ' . $user['nom'];
                header("Location: index.php");
                exit();
            } else {
                $login_error = "Mot de passe incorrect.";
            }
        } else {
            $login_error = "Identifiant incorrect.";
        }
        $login_stmt->close();
    } else {
        $login_error = "L'identifiant et le mot de passe sont requis.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - ÉcoFutur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .auth-title {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .auth-link {
            text-align: center;
            margin-top: 20px;
        }
        .auth-link a {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
        #register-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2 class="auth-title">Authentification ÉcoFutur</h2>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 1) : ?>
                <div class="success-message">Votre compte a été créé avec succès. Veuillez vous connecter.</div>
            <?php endif; ?>

            <div id="login-form">
                <h3>Connexion</h3>
                <?php if (!empty($login_error)) : ?>
                    <div class="error-message"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="login_email">Email</label>
                        <input type="email" class="form-control" id="login_email" name="login_email" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Mot de passe</label>
                        <input type="password" class="form-control" id="login_password" name="login_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" name="login_submit">Se connecter</button>
                </form>
                <p class="auth-link"><a id="show-register-form">Pas de compte ? S'inscrire</a></p>
            </div>

            <div id="register-form">
                <h3>Inscription</h3>
                <?php if (!empty($registration_error)) : ?>
                    <div class="error-message"><?php echo htmlspecialchars($registration_error); ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="register_nom">Nom</label>
                        <input type="text" class="form-control" id="register_nom" name="register_nom" required>
                    </div>
                    <div class="form-group">
                        <label for="register_prenom">Prénom</label>
                        <input type="text" class="form-control" id="register_prenom" name="register_prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="register_email">Email</label>
                        <input type="email" class="form-control" id="register_email" name="register_email" required>
                    </div>
                    <div class="form-group">
                        <label for="register_password">Mot de passe</label>
                        <input type="password" class="form-control" id="register_password" name="register_password" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block" name="register_submit">S'inscrire</button>
                </form>
                <p class="auth-link"><a id="show-login-form">Déjà un compte ? Se connecter</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('show-register-form').addEventListener('click', function() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });

        document.getElementById('show-login-form').addEventListener('click', function() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>