<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['id'])) {
    // Récupérer le nom de l'utilisateur depuis la session
    $nom_utilisateur = $_SESSION['nom'] ?? ''; // Assurez-vous que cette variable est définie lors de la connexion
} else {
    // L'utilisateur n'est pas connecté
    $nom_utilisateur = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ÉcoFutur - La gestion intelligente des déchets pour un avenir durable</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header class="bg-light fixed-top">
        <nav class="navbar navbar-expand-lg navbar-light container">
            <a class="navbar-brand" href="#">
                <img src="tri.png" alt="Logo ÉcoFutur" style="max-height: 40px; font-weight: bolder;"> ÉcoFutur
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">
                            <i class="fas fa-home mr-1"></i> Accueil <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nos-solutions-detail">
                            <i class="fas fa-lightbulb mr-1"></i> Nos Solutions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#temoignages">
                            <i class="fas fa-comments mr-1"></i> Témoignages
                        </a>
                    </li>
                 
                    <li class="nav-item">
                        <a class="nav-link" href="#a-propos">
                            <i class="fas fa-info-circle mr-1"></i> À Propos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            <i class="fas fa-envelope mr-1"></i> Contact
                        </a>
                    </li>
                    <?php if ($nom_utilisateur) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user mr-1"></i> <?php echo htmlspecialchars($nom_utilisateur); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="index1.php"><i class="fas fa-cog mr-1"></i> Configuration</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Déconnexion</a>
                            </div>
                        </li>
<?php else : ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle btn btn-outline-success ml-lg-3" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: green;">
            <i class="fas fa-user mr-1"></i>
            <?php
            if (isset($_SESSION['nom_utilisateur'])) {
                echo htmlspecialchars($_SESSION['nom_utilisateur']);
            } else {
                echo 'Connexion / Inscription';
            }
            ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a class="dropdown-item" href="index1.php"><i class="fas fa-cog mr-1"></i> Configuration</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Déconnexion</a>
            <?php else : ?>
                <a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt mr-1"></i> Se connecter</a>
                <a class="dropdown-item" href="login.php"><i class="fas fa-user-plus mr-1"></i> S'inscrire</a>
            <?php endif; ?>
        </div>
    </li>
<?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container mt-5 pt-5">
        <section class="jumbotron bg-white text-center text-lg-left">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 font-weight-bold text-primary">ÉcoFutur : La gestion intelligente des déchets <span class="text-success">pour un avenir durable</span></h1>
                    <p class="lead">Des poubelles connectées qui optimisent la collecte, réduisent les coûts et contribuent à des villes plus propres.</p>
                    <div class="d-flex flex-column flex-lg-row">
                        <a href="#nos-solutions-detail" class="btn btn-success btn-lg mr-lg-3 mb-2 mb-lg-0 pulse-button">
                            <i class="fas fa-arrow-right mr-2"></i> Découvrez nos solutions
                        </a>
                        <a href="<?php echo $nom_utilisateur ? 'index1.php' : 'index1.php'; ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i> <?php echo $nom_utilisateur ? 'Accéder à la plateforme' : 'Accéder à la plateforme'; ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="poub1.jpg" alt="Drone surveillant des poubelles intelligentes" class="img-fluid rounded shadow-sm hero-image">
                </div>
            </div>
        </section>

        <section id="support-prioritaire" class="py-4 bg-light text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-success btn-block"><i class="fas fa-phone-alt mr-2"></i> Demander un devis</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-success btn-block"><i class="fas fa-check-circle mr-2"></i> Support prioritaire 7j/7</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-info btn-block"><i class="fas fa-user-tie mr-2"></i> Contacter un expert</button>
                    </div>
                </div>
                <p class="mt-2 text-muted small">Tous nos prix sont HT. Des options supplémentaires peuvent être ajoutées à chaque forfait.</p>
            </div>
        </section>

        <section id="nos-solutions-detail"class="py-5">
            <h2 class="text-center mb-4">Nos Solutions</h2>
            <p class="text-center lead">ÉcoFutur propose des poubelles intelligentes qui révolutionnent la gestion des déchets urbains grâce à une technologie de pointe et une approche écologique.</p>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-2x text-primary mb-3"></i>
                            <h5 class="card-title font-weight-bold">Optimisation des collectes</h5>
                            <p class="card-text">Nos capteurs intelligents déterminent le niveau de remplissage en temps réel pour optimiser les tournées de collecte.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-coins fa-2x text-success mb-3"></i>
                            <h5 class="card-title font-weight-bold">Réduction des coûts</h5>
                            <p class="card-text">Réduisez jusqu'à 30% vos coûts opérationnels grâce à une gestion optimisée des ressources.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-leaf fa-2x text-info mb-3"></i>
                            <h5 class="card-title font-weight-bold">Impact environnemental</h5>
                            <p class="card-text">Diminuez votre empreinte carbone en limitant les déplacements inutiles et en favorisant le tri.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                            <h5 class="card-title font-weight-bold">Données en temps réel</h5>
                            <p class="card-text">Suivez l'état de votre parc de poubelles et analysez les tendances via notre plateforme intuitive.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-recycle fa-2x text-secondary mb-3"></i>
                            <h5 class="card-title font-weight-bold">Amélioration du tri</h5>
                            <p class="card-text">Encouragez le tri sélectif grâce à nos systèmes interactifs et pédagogiques.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 solution-card">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h5 class="card-title font-weight-bold">Villes plus propres</h5>
                            <p class="card-text">Évitez les débordements et contribuez à l'amélioration de la propreté urbaine.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="temoignages" class="py-5 bg-light">
            <h2 class="text-center mb-4">Ce que disent nos clients</h2>
            <p class="text-center lead">Découvrez pourquoi les municipalités et organisations choisissent nos solutions de gestion intelligente des déchets.</p>
            <div class="row justify-content-center mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 testimonial-card">
                        <div class="card-body text-center">
                            <div class="testimonial-avatar rounded-circle bg-primary text-white font-weight-bold mb-3" style="width: 60px; height: 60px; line-height: 60px; font-size: 1.5rem; margin: 0 auto;">MD</div>
                            <p class="card-text">"Depuis l'installation des poubelles intelligentes ÉcoFutur, nous avons réduit nos coûts de collecte de 25% tout en améliorant la propreté de notre centre-ville."</p>
                            <h6 class="mt-3 mb-1 font-weight-bold">Marie Dupont</h6>
                            <p class="card-subtitle text-muted small">Directrice des services techniques, Ville de Grenoble</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 testimonial-card">
                        <div class="card-body text-center">
                            <div class="testimonial-avatar rounded-circle bg-success text-white font-weight-bold mb-3" style="width: 60px; height: 60px; line-height: 60px; font-size: 1.5rem; margin: 0 auto;">JM</div>
                            <p class="card-text">"L'interface de gestion est intuitive et nous permet de suivre l'activité en temps réel. Une révolution pour notre équipe de maintenance !"</p>
                            <h6 class="mt-3 mb-1 font-weight-bold">Jean Martin</h6>
                            <p class="card-subtitle text-muted small">Responsable environnement, Centre commercial Les Terrasses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 testimonial-card">
                        <div class="card-body text-center">
                            <div class="testimonial-avatar rounded-circle bg-info text-white font-weight-bold mb-3" style="width: 60px; height: 60px; line-height: 60px; font-size: 1.5rem; margin: 0 auto;">SL</div>
                            <p class="card-text">"Nos citoyens sont ravis de ce nouveau système qui garantit des espaces publics plus propres et contribue à notre engagement écologique."</p>
                            <h6 class="mt-3 mb-1 font-weight-bold">Sophie Laurent</h6>
                            <p class="card-subtitle text-muted small">Adjointe au maire, Ville d'Annecy</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

      <!--   <section id="tarifs" class="py-5 bg-light">
            <h2 class="text-center mb-4">Nos Tarifs Adaptés</h2>
            <p class="text-center lead">Découvrez nos offres flexibles pour répondre aux besoins de chaque ville et entreprise.</p>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm text-center pricing-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h3>Essentiel</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title pricing-card-title">1.000fcfa <small class="text-muted">/ mois</small></h4>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Jusqu'à 10 poubelles</li>
                                <li>Suivi en temps réel</li>
                                <li>Alertes de base</li>
                            </ul>
                            <a href="#" class="btn btn-primary btn-block">Choisir cette offre</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm text-center pricing-card">
                        <div class="card-header bg-success text-white py-3">
                            <h3>Avancé</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title pricing-card-title">25.000fcfa <small class="text-muted">/ mois</small></h4>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Jusqu'à 50 poubelles</li>
                                <li>Suivi avancé et historique</li>
                                <li>Analyse prédictive</li>
                            </ul>
                            <a href="#" class="btn btn-success btn-block">Choisir cette offre</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm text-center pricing-card">
                        <div class="card-header bg-info text-white py-3">
                            <h3>Entreprise</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title pricing-card-title">Contactez-nous</h4>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Nombre illimité de poubelles</li>
                                <li>Solutions personnalisées</li>
                                <li>Support prioritaire</li>
                            </ul>
                            <a href="#contact" class="btn btn-info btn-block">Nous contacter</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>-->

        <section id="a-propos" class="py-5">
            <h2 class="text-center mb-4">À Propos d'ÉcoFutur</h2>
            <div class="row">
                <div class="col-lg-6">
                    <img src="nettoyage.jpg" alt="Équipe ÉcoFutur" class="img-fluid rounded shadow-sm mb-4">
                </div>
                <div class="col-lg-6">
                    <p class="lead">ÉcoFutur est une entreprise passionnée par l'innovation et la durabilité. Notre mission est de transformer la gestion des déchets grâce à des technologies intelligentes, contribuant ainsi à des villes plus propres, plus efficaces et plus respectueuses de l'environnement.</p>
                    <p>Nous croyons en un avenir où chaque déchet est géré de manière optimale, réduisant l'impact environnemental et améliorant la qualité de vie des citoyens. Notre équipe d'experts travaille sans relâche pour développer des solutions fiables, évolutives et faciles à intégrer pour les municipalités, les entreprises et les collectivités.</p>
                    <p>Rejoignez-nous dans cette révolution de la gestion des déchets !</p>
                </div>
            </div>
        </section>

        <section id="contact" class="py-5 bg-light">
            <h2 class="text-center mb-4">Contactez-nous</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Votre email">
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Votre message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Envoyer le message</button>
                    </form>
                </div>
            </div>
        </section>

        <section id="gestion" style="display: none;">
            <h2 class="text-center mb-4">Page de Gestion (Simulée)</h2>
            <p class="text-center">Ceci est une simulation de la page de gestion. Ici, les utilisateurs connectés pourraient visualiser et gérer leurs poubelles intelligentes.</p>
            <div class="alert alert-info text-center" role="alert">
                Fonctionnalité à implémenter : Affichage des données des poubelles, gestion des alertes, configuration des itinéraires, etc.
            </div>
            <div class="text-center">
                <a href="#" class="btn btn-secondary">Retour à l'accueil</a>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>ÉcoFutur</h5>
                    <p>Solutions innovantes de gestion des déchets pour un avenir plus durable et des villes plus propres.</p>
                    <div class="mt-3">
                        <a href="#" class="text-light mr-2"><i class="fab fa-facebook-square fa-lg"></i></a>
                        <a href="#" class="text-light mr-2"><i class="fab fa-twitter-square fa-lg"></i></a>
                        <a href="#" class="text-light mr-2"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-light mr-2"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <h5>Navigation</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Accueil</a></li>
                        <li><a href="#nos-solutions-detail" class="text-light">Nos Solutions</a></li>
                        <li><a href="#tarifs" class="text-light">Tarifs</a></li>
                        <li><a href="#a-propos" class="text-light">À Propos</a></li>
                        <li><a href="#contact" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <h5>Liens utiles</h5>
                    <ul class="list-unstyled">
                        <li><a href="auth.php" class="text-light">Connexion / Inscription</a></li>
                        <li><a href="<?php echo $nom_utilisateur ? 'config.php' : 'auth.php'; ?>" class="text-light">Accès plateforme</a></li>
                        <li><a href="#" class="text-light">FAQ</a></li>
                        <li><a href="#" class="text-light">Mentions légales</a></li>
                        <li><a href="#" class="text-light">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Ave des 3 Martyrs, Brazzaville, République du Congo.</li>
                        <li><i class="fas fa-phone mr-2"></i> +242 06 733 47 30 </li>
                        <li><i class="fas fa-envelope mr-2"></i> contact@ecofutur.cg</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary mt-4 mb-2">
            <p class="text-center text-muted small">&copy; 2025 ÉcoFutur - Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

<?php
// logout.php
if ($_SERVER['SCRIPT_NAME'] === '/logout.php') {
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>