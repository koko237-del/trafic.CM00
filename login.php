<?php
session_start();
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'] ?? '';
    $email = htmlspecialchars($_POST['email'] ?? $_POST['email_mairie'] ?? '');

    // CAS ADMINISTRATIF
    if ($type == 'utilisateurs' && isset($_POST['cle_admin'])) {
        $cle_admin = htmlspecialchars($_POST['cle_admin']);

        $stmt = $pdo->prepare("
            SELECT u.id, a.email_mairie, a.cle_admin 
            FROM utilisateurs u
            JOIN administratifs a ON u.id = a.utilisateur_id
            WHERE a.email_mairie = ? AND a.cle_admin = ?
        ");
        $stmt->execute([$email, $cle_admin]);
        $admin = $stmt->fetch();

        if ($admin) {
            $_SESSION['id'] = $admin['id'];
            header("Location: interface-admi1.php");
            exit;
        } else {
            echo "Email ou clé incorrecte.";
            exit;
        }
    }

    // CAS UTILISATEUR SIMPLE ou CONDUCTEUR
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['id'] = $user['id'];

        // Vérifier dans quelle table spécifique il est
        $stmtSimple = $pdo->prepare("SELECT utilisateur_id FROM simples_utilisateurs WHERE utilisateur_id = ?");
        $stmtSimple->execute([$user['id']]);
        if ($stmtSimple->fetch()) {
            header("Location: interface-s-utilisateur.php");
            exit;
        }

        $stmtConducteur = $pdo->prepare("SELECT utilisateur_id FROM conducteurs WHERE utilisateur_id = ?");
        $stmtConducteur->execute([$user['id']]);
        if ($stmtConducteur->fetch()) {
            header("Location: interface-conducteur.php");
            exit;
        }

        echo "Utilisateur introuvable.";
        exit;

    } else {
        echo "Email ou mot de passe incorrect.";
        exit;
    }
}
?>
