<php
session_start();
require 'inscription.php'; 
require 'connexion.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trafic.CM</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #e53935;
      --primary-dark: #c62828;
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      background-color: #121212;
      color: #fff;
      transition: var(--transition);
    }

    body.light-theme {
      background-color: #f4f4f4;
      color: #000;
    }

    header {
      background: #1b1b1b;
      color: white;
      padding: 2rem 1rem;
      text-align: center;
      position: relative;
      transition: var(--transition);
    }

    body.light-theme header {
      background: #fff;
      color: #000;
    }

    header h1 {
      font-size: 3.5rem;
      color: var(--primary);
    }

    .subtitle {
      color: #aaa;
    }

    main {
      padding: 2rem 1rem;
      max-width: 960px;
      margin: auto;
      text-align: center;
    }

    img {
      height: 200px;
      width: 200px;
      border-radius: 50%;
      border: 5px solid #fff;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
      margin-bottom: 1.5rem;
      transition: transform 0.3s ease;
    }

    img:hover {
      transform: scale(1.05);
    }

    .button-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .button-container button,
    .theme-toggle {
      background: var(--primary);
      border: none;
      color: white;
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      border-radius: 16px;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .button-container button:hover,
    .theme-toggle:hover {
      background: var(--primary-dark);
    }

    .form-card {
      background: #1e1e1e;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
      max-width: 400px;
      margin: 2rem auto;
      text-align: left;
      transition: var(--transition);
    }

    body.light-theme .form-card {
      background: #fff;
      color: #000;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input {
      padding: 0.75rem;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      outline: none; 
    }

    form button[type="submit"] {
      background: var(--primary);
      border: none;
      padding: 0.8rem;
      color: white;
      font-weight: bold;
      border-radius: 10px;
      cursor: pointer;
      transition: var(--transition);
    }

    form button[type="submit"]:hover {
      background: var(--primary-dark);
    }

    a {
      text-align: center;
      color: #ccc;
      text-decoration: none;
      display: block;
      font-size: 0.9rem;
      margin-top: 0.5rem;
    }

    footer {
      background: #1b1b1b;
      color: #aaa;
      padding: 1rem;
      text-align: center;
      font-size: 0.9rem;
      transition: var(--transition);
    }

    body.light-theme footer {
      background: #fff;
      color: #555;
    }

    .theme-toggle {
      position: absolute;
      top: 1rem;
      right: 1rem;
    }
  </style>
</head>

<body>
  <header>
    <button class="theme-toggle" onclick="toggleTheme()">
      <i class="fa-solid fa-circle-half-stroke"></i>
    </button>
    <h1><i class="fa-solid fa-car-on"></i> Trafic.CM</h1>
    <p class="subtitle">Signalez un embouteillage et aidez les autres usagers</p>
  </header>

  <main>
    <img src="Capture.JPG" alt="Trafic Routier Camerounais" />
    <h1>Bienvenue sur Trafic.CM</h1>
    <div class="button-container">
      <button onclick="openLogin('user')"><i class="fa-solid fa-user"></i> S-Utilisateur</button>
      <button onclick="openLogin('driver')"><i class="fa-solid fa-car"></i> Conducteur</button>
      <button onclick="openLogin('admin')"><i class="fa-solid fa-building"></i> Administratif</button>
    </div>

    <div id="login-forms"></div>
  </main>

  <footer>
    <p>&copy; 2025 Trafic.CM | Tous droits réservés</p>
  </footer>

  <script>
    function toggleTheme() {
      document.body.classList.toggle("light-theme");
    }

    function openLogin(role) {
      let html = '';

      if (role === 'user') {
        html = `
          <div class="form-card">
            <h3>Connexion S-Utilisateur</h3>
            <form method='POST' action='login.php'>
              <input name='email' type="email" placeholder="Email" required>
              <input name='mot_de_passe'  type="password" placeholder="Mot de passe" required>
              <input type="text" name='type' value="utilisateurs" style="display:none">
              <button type="submit">Se connecter</a></button>
            </form>
            <a href="#">Mot de passe oublié ?</a>
            <a href="#" onclick="showRegisterForm('user')">Pas de compte ? S'inscrire</a>
          </div>
        `;
      } else if (role === 'driver') {
        html = `
          <div class="form-card">
            <h3>Connexion Conducteur</h3>
            <form method='POST'  action='login.php'>
              <input name='email' type="email" placeholder="Email" required>
              <input name='mot_de_passe' type="password" placeholder="Mot de passe" required>
              <input type="text" name='type' value="utilisateurs" style="display:none">
              <button type="submit">Se connecter</button>
            </form>
            <a href="#">Mot de passe oublié ?</a>
            <a href="#" onclick="showRegisterForm('driver')">Pas de compte ? S'inscrire</a>
          </div>
        `;
      } else if (role === 'admin') {
        html = `
          <div class="form-card">
            <h3>Connexion Administratif</h3>
            <form method='POST' action='login.php'>
              <input name='email' type="email" placeholder="Email de la mairie" required>
              <input name='cle_admin' type="text" placeholder="Clé de sécurité" required>
              <input type="text" name='type' value="utilisateurs" style="display:none">
              <button type="submit">Se connecter</button>
            </form>
            <a href="#">Mot de passe oublié ?</a>
            <a href="#" onclick="showRegisterForm('admin')">Obtenir un compte administratif</a>
          </div>
        `;
      }

      document.getElementById("login-forms").innerHTML = html;
    }

    function showRegisterForm(role) {
      let html = '';

      if (role === 'user') {
        html = `
          <div class="form-card">
            <h3>Inscription S-Utilisateur</h3>
          <form action="inscription.php" method="post">
                <input type="text" name="nom_pseudo" placeholder="Nom ou pseudo" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="hidden" name="type" value="simple">
               <button type="submit">S'inscrire</button>
          </form>

          </div>
        `;
      } else if (role === 'driver') {
        html = `
          <div class="form-card">
            <h3>Inscription Conducteur</h3>
              <form action="inscription.php" method="post">
                <input type="text" name="nom_pseudo" placeholder="Nom ou pseudo" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="text" name="plaque" placeholder="Plaque d'immatriculation" required>
                <input type="hidden" name="type" value="conducteur">
                <button type="submit">S'inscrire</button>
              </form>

          </div>
        `;
      } else if (role === 'admin') {
        html = `
          <div class="form-card">
            <h3>Inscription Administratif</h3>
               <form action="inscription.php" method="post">
                 <input type="text" name="region" placeholder="Région" required>
                 <input type="text" name="departement" placeholder="Département" required>
                 <input type="text" name="ville" placeholder="Ville" required>
                 <input type="text" name="mairie" placeholder="Mairie" required>
                 <input type="date" name="date_mairie" required>
                 <input type="email" name="email_mairie" placeholder="Email de la mairie" required>
                 <input type="text" name='type' value="administratif" style="display:none">
                 <button type="submit">S'inscrire</button>
               </form>

          </div>
        `;
      }

      document.getElementById("login-forms").innerHTML = html;
    }
  </script>
</body>

</html>
