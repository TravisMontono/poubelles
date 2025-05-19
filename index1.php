<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Connexion à la base de données
$servername = ""; // Votre nom de serveur - IMPORTANT : Remplissez ceci
$username = "root"; // Votre nom d'utilisateur de base de données
$password = ""; // Votre mot de passe de base de données - IMPORTANT : Remplissez ceci
$dbname = "ecofuture"; // Le nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Récupérer le rôle de l'utilisateur ---
$user_role = 'utilisateur'; // Rôle par défaut
$role_sql = "SELECT role FROM utilisateurs WHERE id = ? LIMIT 1";
$role_stmt = $conn->prepare($role_sql);
if ($role_stmt) {
    $role_stmt->bind_param("i", $user_id);
    $role_stmt->execute();
    $role_result = $role_stmt->get_result();
    if ($role_row = $role_result->fetch_assoc()) {
        $user_role = $role_row['role'];
    }
    $role_stmt->close();
}
$_SESSION['user_role'] = $user_role; // Stocker le rôle dans la session

// Gérer les actions basées sur les requêtes POST (Panneau d'administration)
if ($user_role === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_user':
                $nom = $_POST['nom'];
                $email = $_POST['email'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role = $_POST['role'];
                $add_user_sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
                $add_user_stmt = $conn->prepare($add_user_sql);
                if ($add_user_stmt) {
                    $add_user_stmt->bind_param("ssss", $nom, $email, $password, $role);
                    if ($add_user_stmt->execute()) {
                        $_SESSION['message'] = "Utilisateur ajouté avec succès.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Erreur lors de l'ajout de l'utilisateur: " . $add_user_stmt->error;
                        $_SESSION['message_type'] = 'danger';
                    }
                    $add_user_stmt->close();
                } else {
                    $_SESSION['message'] = "Erreur de préparation de la requête d'ajout d'utilisateur: " . $conn->error;
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'delete_user':
                $delete_user_id = $_POST['user_id'];
                $delete_user_sql = "DELETE FROM utilisateurs WHERE id = ?";
                $delete_user_stmt = $conn->prepare($delete_user_sql);
                if ($delete_user_stmt) {
                    $delete_user_stmt->bind_param("i", $delete_user_id);
                    if ($delete_user_stmt->execute()) {
                        $_SESSION['message'] = "Utilisateur supprimé avec succès.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Erreur lors de la suppression de l'utilisateur: " . $delete_user_stmt->error;
                        $_SESSION['message_type'] = 'danger';
                    }
                    $delete_user_stmt->close();
                } else {
                    $_SESSION['message'] = "Erreur de préparation de la requête de suppression d'utilisateur: " . $conn->error;
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'add_bin':
                $numero_poubelle = $_POST['numero_poubelle'];
                $id_device = $_POST['id_device'] ?: null;
                $adresse = $_POST['adresse'] ?: null;
                $utilisateur_id = $_POST['utilisateur_id'] ?: null;
                $add_bin_sql = "INSERT INTO poubelles (numero_poubelle, id_device, adresse, utilisateur_id) VALUES (?, ?, ?, ?)";
                $add_bin_stmt = $conn->prepare($add_bin_sql);
                if ($add_bin_stmt) {
                    $add_bin_stmt->bind_param("sssi", $numero_poubelle, $id_device, $adresse, $utilisateur_id);
                    if ($add_bin_stmt->execute()) {
                        $_SESSION['message'] = "Poubelle ajoutée avec succès.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Erreur lors de l'ajout de la poubelle: " . $add_bin_stmt->error;
                        $_SESSION['message_type'] = 'danger';
                    }
                    $add_bin_stmt->close();
                } else {
                    $_SESSION['message'] = "Erreur de préparation de la requête d'ajout de poubelle: " . $conn->error;
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'delete_bin':
                $delete_bin_id = $_POST['bin_id'];
                $delete_bin_sql = "DELETE FROM poubelles WHERE id = ?";
                $delete_bin_stmt = $conn->prepare($delete_bin_sql);
                if ($delete_bin_stmt) {
                    $delete_bin_stmt->bind_param("i", $delete_bin_id);
                    if ($delete_bin_stmt->execute()) {
                        $_SESSION['message'] = "Poubelle supprimée avec succès.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Erreur lors de la suppression de la poubelle: " . $delete_bin_stmt->error;
                        $_SESSION['message_type'] = 'danger';
                    }
                    $delete_bin_stmt->close();
                } else {
                    $_SESSION['message'] = "Erreur de préparation de la requête de suppression de poubelle: " . $conn->error;
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'assign_bin':
                $assign_bin_id = $_POST['bin_id'];
                $assign_user_id = $_POST['user_id'] ?: null;
                $assign_bin_sql = "UPDATE poubelles SET utilisateur_id = ? WHERE id = ?";
                $assign_bin_stmt = $conn->prepare($assign_bin_sql);
                if ($assign_bin_stmt) {
                    $assign_bin_stmt->bind_param("ii", $assign_user_id, $assign_bin_id);
                    if ($assign_bin_stmt->execute()) {
                        $_SESSION['message'] = "Poubelle assignée/désassignée avec succès.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Erreur lors de l'assignation/désassignation de la poubelle: " . $assign_bin_stmt->error;
                        $_SESSION['message_type'] = 'danger';
                    }
                    $assign_bin_stmt->close();
                } else {
                    $_SESSION['message'] = "Erreur de préparation de la requête d'assignation de poubelle: " . $conn->error;
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            // Gérer les paramètres d'alerte si nécessaire via POST
        }
        header("Location: index1.php"); // Rediriger pour rafraîchir les données
        exit();
    }
}

// Requête pour récupérer les poubelles intelligentes
if ($user_role === 'admin') {
    $sql_bins = "SELECT p.*, u.nom as utilisateur_nom FROM poubelles p LEFT JOIN utilisateurs u ON p.utilisateur_id = u.id ORDER BY p.numero_poubelle ASC";
    $result_bins = $conn->query($sql_bins);
} else {
    $sql_bins = "SELECT * FROM poubelles WHERE utilisateur_id = ? ORDER BY numero_poubelle ASC";
    $stmt_bins = $conn->prepare($sql_bins);
    if ($stmt_bins) {
        $stmt_bins->bind_param("i", $user_id);
        $stmt_bins->execute();
        $result_bins = $stmt_bins->get_result();
    } else {
        $result_bins = null;
    }
}

// Requête pour récupérer tous les utilisateurs (pour le panneau d'administration)
$result_users = null;
if ($user_role === 'admin') {
    $sql_users = "SELECT id, nom, email, role FROM utilisateurs ORDER BY nom ASC";
    $result_users = $conn->query($sql_users);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecofutur - Gestion Intelligente</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles de base pour le SPA */
        .content-section {
            display: none; /* Caché par défaut */
            padding-top: 20px;
        }
        .content-section.active {
            display: block; /* Affiché lorsque actif */
        }
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #343a40; /* Sidebar sombre */
            color: #fff;
            padding-top: 20px;
            transition: width 0.3s ease;
            z-index: 1000;
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar.collapsed .sidebar-brand span,
        .sidebar.collapsed .sidebar-menu span,
        .sidebar.collapsed .sidebar-bottom span {
            display: none;
        }
        .sidebar.collapsed .sidebar-brand i {
            font-size: 1.5rem; /* Ajuster la taille de l'icône lors de la réduction */
        }
        .sidebar.collapsed .sidebar-menu a i,
        .sidebar.collapsed .sidebar-bottom a i {
             margin-right: 0;
             font-size: 1.2rem; /* Ajuster la taille de l'icône */
        }
        .sidebar.collapsed .sidebar-menu li a,
        .sidebar.collapsed .sidebar-bottom a {
            text-align: center;
            padding-left: 0;
            padding-right: 0;
        }


        .sidebar-brand {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: bold;
        }
        .sidebar-brand i { margin-right: 10px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li a, .sidebar-bottom a {
            display: block;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .sidebar-menu li a:hover, .sidebar-bottom a:hover, .sidebar-menu li a.active-link {
            background-color: #495057;
            color: #fff;
        }
        .sidebar-menu li a i, .sidebar-bottom a i { margin-right: 10px; width: 20px; text-align: center;}
        .sidebar-bottom {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }
        .content {
            margin-left: 250px; /* Même largeur que la sidebar */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .content.sidebar-collapsed {
            margin-left: 60px; /* Largeur de la sidebar réduite */
        }
        .dashboard-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .hamburger-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            margin-right: 15px;
            cursor: pointer;
        }
        #pageTitle { font-size: 1.8rem; color: #333; }

        /* Styles des widgets */
        .dashboard-widgets { display: flex; flex-wrap: wrap; gap: 20px; }
        .bin-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 100%; /* Largeur complète pour chaque conteneur de poubelle initialement */
            margin-bottom: 20px;
        }
        .bin-container h2.bin-header {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .widgets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Grille responsive pour les widgets */
            gap: 20px;
        }
        .widget {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            min-height: 150px; /* S'assurer que les widgets ont une certaine hauteur */
        }
        .widget-title { font-size: 1.1rem; font-weight: bold; margin-bottom: 15px; color: #495057; }
        .fill-level-info, .battery-info, .conditions-internes-info, .emplacement-info { display: flex; align-items: center; gap: 15px; }
        .fill-level-icon, .battery-icon { font-size: 2rem; color: #007bff; }
        .fill-level-percentage, .battery-percentage { font-size: 1.5rem; font-weight: bold; }
        .battery-bar-container { width: 100px; height: 15px; background-color: #e9ecef; border-radius: 5px; overflow: hidden; }
        .battery-bar { height: 100%; background-color: #28a745; transition: width 0.5s ease; }
        .condition-item { text-align: center; }
        .condition-item i { font-size: 1.5rem; margin-bottom: 5px; }
        .btn-enregistrer { background-color: #28a745; color: white; }
        .btn-voir-carte { font-size: 0.9rem; }
        .chart-container { position: relative; height: 300px; width: 100%; } /* S'assurer que le graphique a des dimensions */
        .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .chart-title { font-size: 1.2rem; }

        /* Styles du Panneau d'Administration */
        .admin-table { width: 100%; margin-bottom: 1rem; color: #212529; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 0.75rem; vertical-align: top; border-top: 1px solid #dee2e6; }
        .admin-table thead th { vertical-align: bottom; border-bottom: 2px solid #dee2e6; background-color: #e9ecef; }
        .admin-form-container { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dee2e6;}
        .admin-form-container h4 { margin-bottom: 15px; }
        .alert-message { margin-top: 15px; }
            .bin-details {
        margin-top: 10px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .bin-details i {
        width: 1em;
        margin-right: 0.5em;
    }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-trash-alt"></i> <span>Ecofutur</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#dashboard" data-target="dashboard" class="active-link"><i class="fas fa-tachometer-alt"></i> <span>Tableau de bord</span></a></li>
            <li><a href="#historique" data-target="historique"><i class="fas fa-history"></i> <span>Historique</span></a></li>
            <li><a href="#localisation" data-target="localisation"><i class="fas fa-map-marker-alt"></i> <span>Localisation</span></a></li>
            <li><a href="#alertes-config" data-target="alertes-config"><i class="fas fa-bell"></i> <span>Configuration Alertes</span></a></li>

            <?php if ($user_role === 'admin'): ?>
            <li><a href="#admin-panel" data-target="admin-panel"><i class="fas fa-user-shield"></i> <span>Panel Admin</span></a></li>
            <?php endif; ?>

            <li><a href="#a-propos" data-target="a-propos"><i class="fas fa-info-circle"></i> <span>À propos</span></a></li>
        </ul>
        <div class="sidebar-bottom">
            <a href="#parametres" data-target="parametres"><i class="fas fa-cog"></i> <span>Paramètres</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a>
        </div>
    </div>

    <div class="content" id="mainContent">
        <div class="dashboard-header">
            <button class="hamburger-btn" id="hamburgerBtn">
                <i class="fas fa-bars"></i>
            </button>
            <h1 id="pageTitle">Tableau de bord</h1>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div id="dashboard-content" class="content-section active">
            <div class="dashboard-widgets">
                <?php
                if ($result_bins && $result_bins->num_rows > 0) {
                    while ($row = $result_bins->fetch_assoc()) {
                ?>
                <div class="bin-container" data-bin-id="<?php echo htmlspecialchars($row['id']); ?>">
                    <h2 class="bin-header">
                        Poubelle #<?php echo htmlspecialchars($row['numero_poubelle']); ?>
                        <?php if ($user_role === 'admin' && !empty($row['utilisateur_nom'])) { echo " <small class='text-muted'>(Assignée à: " . htmlspecialchars($row['utilisateur_nom']) . ")</small>"; } ?>
                        <?php if ($user_role === 'admin' && empty($row['utilisateur_id'])) { echo " <small class='text-muted'>(Non assignée)</small>"; } ?>
                    </h2>
                    <div class="widgets-grid">
                        <div class="widget">
                            <h3 class="widget-title">Niveau de Remplissage</h3>
                            <div class="fill-level-info" style="display:inline-block;">
                                <i class="fas fa-trash-alt fill-level-icon"></i>
                                <div>
                                    <p class="fill-level-percentage mb-0"><span><?php echo htmlspecialchars($row['niveau_remplissage']); ?></span>%</p>
                                    <p class="fill-level-status text-muted small">Statut: <strong>
                                        <?php
                                            $niveauRemplissage = intval($row['niveau_remplissage']);
                                            if ($niveauRemplissage <= 25) { echo "Quasi vide"; }
                                            elseif ($niveauRemplissage <= 50) { echo "Peu remplie"; }
                                            elseif ($niveauRemplissage <= 75) { echo "Bien remplie"; }
                                            elseif ($niveauRemplissage <= 95) { echo "Très remplie"; }
                                            else { echo "Pleine"; }
                                        ?>
                                    </strong></p>
                                </div>
                     <div class="bin-details mt-2">
                        <p class="mb-1"><i class="fas fa-tag mr-2"></i>Nom: <strong><?php echo htmlspecialchars($row['numero_poubelle']); ?></strong></p>
                        <?php if (!empty($row['id_device'])): ?>
                            <p class="mb-1"><i class="fas fa-microchip mr-2"></i>ID Device: <?php echo htmlspecialchars($row['id_device']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($row['date_installation'])): ?>
                            <p class="mb-1"><i class="fas fa-calendar-alt mr-2"></i>Installation: <?php echo htmlspecialchars($row['date_installation']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($row['derniere_vidange'])): ?>
                            <p class="mb-1"><i class="fas fa-dumpster mr-2"></i>Dernière vidange: <?php echo htmlspecialchars($row['derniere_vidange']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($row['adresse'])): ?>
                            <p class="mb-1"><i class="fas fa-map-marker-alt mr-2"></i>Adresse: <?php echo htmlspecialchars($row['adresse']); ?></p>
                        <?php endif; ?>
                        <?php
                        // Si vous voulez afficher des infos sur l'utilisateur (si la poubelle est assignée)
                        if (!empty($row['utilisateur_nom']) && $user_role === 'admin'): ?>
                            <p class="mb-0"><i class="fas fa-user mr-2"></i>Assignée à: <?php echo htmlspecialchars($row['utilisateur_nom']); ?></p>
                        <?php elseif ($user_role !== 'admin' && !empty($row['utilisateur_id'])):
                            // Si l'utilisateur connecté regarde sa propre poubelle (non-admin)
                            $sql_user_info = "SELECT nom, email FROM utilisateurs WHERE id = ? LIMIT 1";
                            $stmt_user_info = $conn->prepare($sql_user_info);
                            if ($stmt_user_info) {
                                $stmt_user_info->bind_param("i", $user_id);
                                $stmt_user_info->execute();
                                $result_user_info = $stmt_user_info->get_result();
                                if ($user_info = $result_user_info->fetch_assoc()) {
                                    echo "<p class='mb-0'><i class='fas fa-user mr-2'></i>Votre nom: " . htmlspecialchars($user_info['nom']) . "</p>";
                                    echo "<p class='mb-0'><i class='fas fa-envelope mr-2'></i>Votre email: " . htmlspecialchars($user_info['email']) . "</p>";
                                }
                                $stmt_user_info->close();
                            }
                        endif;
                        ?>
                    </div>
                            </div>
                        </div>

                        <div class="widget">
                            <h3 class="widget-title">Batterie</h3>
                            <div class="battery-info" style="display:inline-block;">
                                <i class="fas fa-battery-full battery-icon <?php echo (intval($row['niveau_batterie']) < 20 ? 'text-danger' : (intval($row['niveau_batterie']) < 50 ? 'text-warning' : 'text-success')); ?>"></i>
                                <div>
                                    <div class="battery-bar-container mb-1">
                                        <div class="battery-bar" style="width: <?php echo htmlspecialchars($row['niveau_batterie']); ?>%; background-color: <?php echo (intval($row['niveau_batterie']) < 20 ? '#dc3545' : (intval($row['niveau_batterie']) < 50 ? '#ffc107' : '#28a745')); ?>;"></div>
                                    </div>
                                    <span class="battery-percentage"><?php echo htmlspecialchars($row['niveau_batterie']); ?>%</span>
                                    <p class="battery-status-text text-muted small mb-0">Statut: <strong>
                                        <?php
                                            $niveauBatterie = intval($row['niveau_batterie']);
                                            if ($niveauBatterie <= 25) { echo "Faible"; }
                                            elseif ($niveauBatterie <= 50) { echo "Moyen"; }
                                            elseif ($niveauBatterie <= 75) { echo "Bon"; }
                                            else { echo "Excellent"; }
                                        ?>
                                    </strong></p>
                                </div>

                            </div>
                        </div>
 <div class="widget">
    <h3 class="widget-title">Humidité</h3>
    <div class="humidity-info" style="display:inline-block;">
        <i class="fas <?php
            $humidite = floatval($row['humidite']);
            if ($humidite <= 30) {
                echo 'fa-water'; // Très sec (goutte d'eau unique)
            } elseif ($humidite <= 60) {
                echo 'fa-droplet'; // Sec à modéré (goutte d'eau)
            } else {
                echo 'fa-humidity'; // Modéré à humide (icône d'humidité)
            }
        ?> humidity-icon text-info"></i>
        <div>
            <div class="humidity-bar-container mb-1">
                <div class="humidity-bar" style="width: <?php echo htmlspecialchars($row['humidite']); ?>%; background-color: <?php
                    if ($humidite <= 30) { echo '#6c757d'; } // Gris
                    elseif ($humidite <= 60) { echo '#007bff'; } // Bleu
                    else { echo '#17a2b8'; } // Cyan
                ?>;"></div>
            </div>
            <span class="humidity-percentage"><?php echo htmlspecialchars($row['humidite']); ?>%</span>
            <p class="humidity-status-text text-muted small mb-0">Statut: <strong>
                <?php
                    if ($humidite <= 30) { echo "Sec"; }
                    elseif ($humidite <= 60) { echo "Modéré"; }
                    else { echo "Humide"; }
                ?>
            </strong></p>
        </div>
    </div>
</div>

<style>
    /* ... vos styles existants ... */
    .humidity-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .humidity-icon {
        font-size: 2rem;
    }
    .humidity-bar-container {
        width: 100px;
        height: 15px;
        background-color: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }
    .humidity-bar {
        height: 100%;
        transition: width 0.5s ease;
    }
    .humidity-percentage {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>
<div class="widget">
    <h3 class="widget-title">Température</h3>
    <div class="temperature-info" style="display:inline-block;">
        <i class="fas <?php
            $temperature = floatval($row['temperature']);
            if ($temperature <= 10) {
                echo 'fa-thermometer-empty'; // Très froid
            } elseif ($temperature <= 20) {
                echo 'fa-thermometer-quarter'; // Froid
            } elseif ($temperature <= 30) {
                echo 'fa-thermometer-half'; // Tempéré
            } elseif ($temperature <= 40) {
                echo 'fa-thermometer-three-quarters'; // Chaud
            } else {
                echo 'fa-thermometer-full'; // Très chaud
            }
        ?> temperature-icon text-danger"></i>
        <div>
            <div class="temperature-bar-container mb-1">
                <div class="temperature-bar" style="width: <?php
                    // Normalisation de la température pour la barre (échelle approximative)
                    if ($temperature <= 0) { echo '0%'; }
                    elseif ($temperature >= 50) { echo '100%'; }
                    else { echo (($temperature / 50) * 100) . '%'; }
                ?>; background-color: <?php
                    if ($temperature <= 10) { echo '#1e90ff'; } // Bleu
                    elseif ($temperature <= 20) { echo '#87ceeb'; } // Bleu ciel
                    elseif ($temperature <= 30) { echo '#ffa500'; } // Orange
                    elseif ($temperature <= 40) { echo '#ff4500'; } // Orange foncé
                    else { echo '#ff0000'; } // Rouge
                ?>;"></div>
            </div>
            <span class="temperature-percentage"><?php echo htmlspecialchars($row['temperature']); ?>°C</span>
            <p class="temperature-status-text text-muted small mb-0">Statut: <strong>
                <?php
                    if ($temperature <= 10) { echo "Très froid"; }
                    elseif ($temperature <= 20) { echo "Froid"; }
                    elseif ($temperature <= 30) { echo "Tempéré"; }
                    elseif ($temperature <= 40) { echo "Chaud"; }
                    else { echo "Très chaud"; }
                ?>
            </strong></p>
        </div>
    </div>
</div>

<style>
    /* ... vos styles existants ... */
    .temperature-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .temperature-icon {
        font-size: 2rem;
    }
    .temperature-bar-container {
        width: 100px;
        height: 15px;
        background-color: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }
    .temperature-bar {
        height: 100%;
        transition: width 0.5s ease;
    }
    .temperature-percentage {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>                        <div class="widget">
                            <h3 class="widget-title">Graphique de Remplissage (Simulé)</h3>
                            <div class="chart-container">
                                <canvas id="fillLevelChart-<?php echo htmlspecialchars($row['id']); ?>"></canvas>
                            </div>
                        </div>

                        <div class="widget">
                            <h3 class="widget-title">Localisation (Mini-carte simulée)</h3>
                            <div style="height: 150px; background-color: #f0f0f0; text-align: center; line-height: 150px;">
                                Carte ici (intégration future)
                            </div>
                            <?php if (!empty($row['adresse'])): ?>
                                <p class="text-muted small mt-2">Adresse: <?php echo htmlspecialchars($row['adresse']); ?></p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <script>
                    // Exemple de données pour le graphique (à remplacer par des données réelles)
                    const fillLevelData_<?php echo htmlspecialchars($row['id']); ?> = {
                        labels: ['Il y a 7 jours', 'Il y a 6 jours', 'Il y a 5 jours', 'Il y a 4 jours', 'Il y a 3 jours', 'Il y a 2 jours', 'Hier', 'Aujourd\'hui'],
                        datasets: [{
                            label: 'Niveau de Remplissage (%)',
                            data: [20, 35, 45, 60, 70, 85, 90, <?php echo htmlspecialchars($row['niveau_remplissage']); ?>],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderWidth: 1
                        }]
                    };

                    const fillLevelChartCtx_<?php echo htmlspecialchars($row['id']); ?> = document.getElementById('fillLevelChart-<?php echo htmlspecialchars($row['id']); ?>').getContext('2d');
                    new Chart(fillLevelChartCtx_<?php echo htmlspecialchars($row['id']); ?>, {
                        type: 'line',
                        data: fillLevelData_<?php echo htmlspecialchars($row['id']); ?>,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                </script>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>Aucune poubelle intelligente trouvée.</p>";
                }
                ?>
            </div>
        </div>

        <div id="historique-content" class="content-section">
            <h2>Historique des Données</h2>
            <p>Ici, vous pourrez consulter l'historique des niveaux de remplissage et d'autres données importantes.</p>
            </div>

        <div id="localisation-content" class="content-section">
            <h2>Localisation des Poubelles</h2>
            <p>Carte interactive affichant l'emplacement de chaque poubelle intelligente.</p>
            </div>

        <div id="alertes-config-content" class="content-section">
            <h2>Configuration des Alertes</h2>
            <p>Personnalisez les seuils d'alerte pour chaque poubelle.</p>
            <?php
            if ($result_bins && $result_bins->num_rows > 0) {
                $result_bins->data_seek(0); // Réinitialiser le pointeur au début
                while ($row = $result_bins->fetch_assoc()) {
            ?>
            <div class="bin-container">
                <h3 class="bin-header">Poubelle #<?php echo htmlspecialchars($row['numero_poubelle']); ?> - Configuration des Alertes</h3>
                <form class="alert-settings-form" data-bin-id="<?php echo htmlspecialchars($row['id']); ?>">
                    <div class="form-group">
                        <label for="alert-level-<?php echo htmlspecialchars($row['id']); ?>">Niveau de remplissage pour l'alerte (%):</label>
                        <input type="range" class="form-control-range alert-level-slider" id="alert-level-<?php echo htmlspecialchars($row['id']); ?>" name="seuil_alerte" min="50" max="95" value="<?php echo htmlspecialchars($row['seuil_alerte'] ?: 80); ?>">
                        <span id="alert-level-value-<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['niveau_alerte'] ?: 80); ?></span>%
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm btn-enregistrer">Enregistrer les alertes</button>
                </form>
            </div>
            <?php
                }
            }
            ?>
        </div>

        <?php if ($user_role === 'admin'): ?>
        <div id="admin-panel-content" class="content-section">
            <h2>Panel Administrateur</h2>
            <ul class="nav nav-tabs mb-3" id="adminTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="users-tab" data-toggle="tab" href="#admin-user-management" role="tab" aria-controls="admin-user-management" aria-selected="true"><i class="fas fa-users"></i> Gestion Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bins-tab" data-toggle="tab" href="#admin-bin-management" role="tab" aria-controls="admin-bin-management" aria-selected="false"><i class="fas fa-trash-alt"></i> Gestion Poubelles</a>
                </li>
            </ul>

            <div class="tab-content" id="adminTabContent">
                <div class="tab-pane fade show active" id="admin-user-management" role="tabpanel" aria-labelledby="users-tab">
                    <h4>Gestion des Utilisateurs</h4>
                    <button class="btn btn-success btn-sm mb-3" onclick="document.getElementById('addUserFormContainer').style.display='block'; this.style.display='none'; document.getElementById('cancelAddUserBtn').style.display='inline-block';"><i class="fas fa-plus"></i> Ajouter Utilisateur</button>
                    <div id="addUserFormContainer" class="admin-form-container" style="display:none;">
                        <h5>Ajouter un nouvel utilisateur</h5>
                        <form method="post" action="index1.php">
                            <input type="hidden" name="action" value="add_user">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="newUserName">Nom:</label>
                                    <input type="text" class="form-control form-control-sm" id="newUserName" name="nom" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="newUserEmail">Email:</label>
                                    <input type="email" class="form-control form-control-sm" id="newUserEmail" name="email" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="newUserPassword">Mot de passe:</label>
                                    <input type="password" class="form-control form-control-sm" id="newUserPassword" name="password" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="newUserRole">Rôle:</label>
                                    <select class="form-control form-control-sm" id="newUserRole" name="role">
                                        <option value="utilisateur">Utilisateur</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                            <button type="button" class="btn btn-secondary btn-sm" id="cancelAddUserBtn" style="display:none;" onclick="document.getElementById('addUserFormContainer').style.display='none'; document.getElementById('showAddUserFormBtn').style.display='inline-block'; this.style.display='none'; document.getElementById('addUserForm').reset();">Annuler</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table table table-striped table-hover table-sm">
                            <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_users && $result_users->num_rows > 0) {
                                    while ($user = $result_users->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                        echo "<td><span class='badge badge-" . ($user['role'] === 'admin' ? 'danger' : 'secondary') . "'>" . htmlspecialchars($user['role']) . "</span></td>";
                                        echo "<td>";
                                        echo "<form method='post' action='index1.php' style='display:inline;'>";
                                        echo "<input type='hidden' name='action' value='delete_user'>";
                                        echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($user['id']) . "'>";
                                        echo "<button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\")'><i class='fas fa-trash-alt'></i></button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Aucun utilisateur trouvé.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="admin-bin-management" role="tabpanel" aria-labelledby="bins-tab">
                    <h4>Gestion des Poubelles</h4>
                    <button class="btn btn-success btn-sm mb-3" onclick="document.getElementById('addBinFormContainer').style.display='block'; this.style.display='none'; document.getElementById('cancelAddBinBtn').style.display='inline-block';"><i class="fas fa-plus"></i> Ajouter Poubelle</button>
                    <div id="addBinFormContainer" class="admin-form-container" style="display:none;">
                        <h5>Ajouter une nouvelle poubelle</h5>
                        <form method="post" action="index1.php">
                            <input type="hidden" name="action" value="add_bin">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="newBinNumero">Numéro Poubelle:</label>
                                    <input type="text" class="form-control form-control-sm" id="newBinNumero" name="numero_poubelle" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="newBinIdDevice">ID Device (Optionnel):</label>
                                    <input type="text" class="form-control form-control-sm" id="newBinIdDevice" name="id_device">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="assignUserId">Attribuer à (Utilisateur):</label>
                                    <select class="form-control form-control-sm" id="assignUserId" name="utilisateur_id">
                                        <option value="">Non assignée</option>
                                        <?php
                                        if ($result_users && $result_users->num_rows > 0) {
                                            $result_users->data_seek(0); // Réinitialiser le pointeur
                                            while ($user = $result_users->fetch_assoc()) {
                                                if ($user['role'] === 'utilisateur') {
                                                    echo "<option value='" . htmlspecialchars($user['id']) . "'>" . htmlspecialchars($user['nom']) . " (ID: " . htmlspecialchars($user['id']) . ")</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="newBinAdresse">Adresse:</label>
                                <input type="text" class="form-control form-control-sm" id="newBinAdresse" name="adresse" placeholder="Ex: 123 Rue Principale, Ville">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                            <button type="button" class="btn btn-secondary btn-sm" id="cancelAddBinBtn" style="display:none;" onclick="document.getElementById('addBinFormContainer').style.display='none'; document.getElementById('showAddBinFormBtn').style.display='inline-block'; this.style.display='none'; document.getElementById('addBinForm').reset();">Annuler</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table table table-striped table-hover table-sm">
                            <thead><tr><th>ID</th><th>N°</th><th>ID Device</th><th>Adresse</th><th>Assignée à</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_bins && $result_bins->num_rows > 0) {
                                    $result_bins->data_seek(0); // Réinitialiser le pointeur
                                    while ($bin = $result_bins->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($bin['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($bin['numero_poubelle']) . "</td>";
                                        echo "<td>" . htmlspecialchars($bin['id_device'] ?: 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($bin['adresse'] ?: 'N/A') . "</td>";
                                        echo "<td>";
                                        if ($bin['utilisateur_id']) {
                                            $user_assigned_sql = "SELECT nom FROM utilisateurs WHERE id = ? LIMIT 1";
                                            $user_assigned_stmt = $conn->prepare($user_assigned_sql);
                                            if ($user_assigned_stmt) {
                                                $user_assigned_stmt->bind_param("i", $bin['utilisateur_id']);
                                                $user_assigned_stmt->execute();
                                                $user_assigned_result = $user_assigned_stmt->get_result();
                                                if ($user_assigned_row = $user_assigned_result->fetch_assoc()) {
                                                    echo htmlspecialchars($user_assigned_row['nom']) . " (ID: " . htmlspecialchars($bin['utilisateur_id']) . ")";
                                                } else {
                                                    echo "Utilisateur inconnu (ID: " . htmlspecialchars($bin['utilisateur_id']) . ")";
                                                }
                                                $user_assigned_stmt->close();
                                            } else {
                                                echo "Erreur lors de la récupération de l'utilisateur.";
                                            }
                                        } else {
                                            echo "<span class='text-muted'>Non assignée</span>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<form method='post' action='index1.php' style='display:inline;'>";
                                        echo "<input type='hidden' name='action' value='delete_bin'>";
                                        echo "<input type='hidden' name='bin_id' value='" . htmlspecialchars($bin['id']) . "'>";
                                        echo "<button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette poubelle ?\")'><i class='fas fa-trash-alt'></i></button>";
                                        echo "</form>";
                                        echo "<button class='btn btn-sm btn-info' onclick='assignBin(" . htmlspecialchars($bin['id']) . ", \"" . htmlspecialchars($bin['utilisateur_id'] ?: '') . "\")'><i class='fas fa-user-plus'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Aucune poubelle trouvée.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div id="a-propos-content" class="content-section">
            <h2>À propos d'Ecofutur</h2>
            <p>Ecofutur est un projet innovant visant à optimiser la gestion des déchets grâce à des poubelles intelligentes. Notre solution permet de surveiller en temps réel le niveau de remplissage des conteneurs, l'état de leur batterie et potentiellement d'autres conditions environnementales. L'objectif est de rationaliser les collectes, de réduire les coûts opérationnels et d'améliorer la propreté de nos villes.</p>
            <p>Grâce à une interface utilisateur intuitive, les citoyens et les administrateurs peuvent visualiser l'état des poubelles, configurer des alertes pour les collectes et accéder à des données statistiques pour une prise de décision éclairée en matière de gestion des déchets.</p>
        </div>

        <div id="parametres-content" class="content-section">
            <h2>Paramètres</h2>
            <p>Vous pourrez modifier ici les paramètres de votre compte (fonctionnalité à implémenter).</p>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const pageTitle = document.getElementById('pageTitle');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a, .sidebar-bottom a[data-target]');
            const contentSections = document.querySelectorAll('.content-section');

            // Basculer l'affichage de la sidebar
            hamburgerBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
            });

            // Navigation dans la sidebar
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const targetText = this.querySelector('span') ? this.querySelector('span').textContent : 'Ecofutur';

                    sidebarLinks.forEach(sl => sl.classList.remove('active-link'));
                    this.classList.add('active-link');

                    contentSections.forEach(section => {
                        section.classList.remove('active');
                    });

                    const activeSection = document.getElementById(targetId + '-content');
                    if (activeSection) {
                        activeSection.classList.add('active');
                        pageTitle.textContent = targetText;
                    }
                    mainContent.scrollTop = 0; // Scroll vers le haut du contenu
                });
            });

            // Activer la section par défaut (Tableau de bord)
            const defaultLink = document.querySelector('.sidebar-menu a[data-target="dashboard"]');
            if (defaultLink) {
                defaultLink.click();
            }

            // Afficher la valeur des sliders de configuration des alertes
            document.querySelectorAll('.alert-level-slider').forEach(slider => {
                const binId = slider.id.replace('alert-level-', '');
                const valueDisplay = document.getElementById(`alert-level-value-${binId}`);
                if(valueDisplay) valueDisplay.textContent = slider.value; // Affichage initial

                slider.addEventListener('input', function() {
                    if(valueDisplay) valueDisplay.textContent = this.value;
                });
            });

            // Gérer la soumission du formulaire de configuration des alertes (simple redirection car pas d'AJAX)
            document.querySelectorAll('.alert-settings-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Pas besoin d'empêcher la soumission par défaut si on recharge la page
                    // La sauvegarde de ces paramètres serait gérée côté PHP
                    alert('Paramètres d\'alerte enregistrés (simulé).');
                });
            });

            // Fonction pour gérer l'assignation de la poubelle (via prompt, rechargement de la page)
            window.assignBin = function(binId, currentUserId) {
                const newUserId = prompt(`Entrez l'ID de l'utilisateur à qui assigner la poubelle (ID: ${binId}).\nLaissez vide pour désassigner. Actuellement assignée à: ${currentUserId || 'Personne'}`, currentUserId);
                if (newUserId !== null) {
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'index1.php';

                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'assign_bin';
                    form.appendChild(actionInput);

                    const binIdInput = document.createElement('input');
                    binIdInput.type = 'hidden';
                    binIdInput.name = 'bin_id';
                    binIdInput.value = binId;
                    form.appendChild(binIdInput);

                    const userIdInput = document.createElement('input');
                    userIdInput.type = 'hidden';
                    userIdInput.name = 'user_id';
                    userIdInput.value = newUserId.trim();
                    form.appendChild(userIdInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            };
        });
    </script>
</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>