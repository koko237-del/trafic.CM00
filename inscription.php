<?php
session_start();
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'] ?? '';
    $email = htmlspecialchars($_POST['email'] ?? $_POST['email_mairie'] ?? '');
    $mot_de_passe = password_hash($_POST['mot_de_passe'] ?? 'admin', PASSWORD_DEFAULT);
    $nom_pseudo = htmlspecialchars($_POST['nom_pseudo'] ?? '');

    try {
        $pdo->beginTransaction();

        // Insertion dans la table principale utilisateurs
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_pseudo, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$nom_pseudo, $email, $mot_de_passe]);
        $utilisateur_id = $pdo->lastInsertId();

        if ($type == 'simple') {
            $pdo->prepare("INSERT INTO simples_utilisateurs (utilisateur_id) VALUES (?)")->execute([$utilisateur_id]);
            $_SESSION['id'] = $utilisateur_id;
            header("Location: interface-s-utilisateur.php");
            exit;

        } elseif ($type == 'conducteur') {
            $plaque = htmlspecialchars($_POST['plaque'] ?? '');
            $pdo->prepare("INSERT INTO conducteurs (utilisateur_id, plaque) VALUES (?, ?)")->execute([$utilisateur_id, $plaque]);
            $_SESSION['id'] = $utilisateur_id;
            header("Location: interface-conducteur.php");
            exit;

        } elseif ($type == 'administratif') {
            $region = htmlspecialchars($_POST['region'] ?? '');
            $departement = htmlspecialchars($_POST['departement'] ?? '');
            $ville = htmlspecialchars($_POST['ville'] ?? '');
            $mairie = htmlspecialchars($_POST['mairie'] ?? '');
            $date_mairie = htmlspecialchars($_POST['date_mairie'] ?? '');
            $email_mairie = htmlspecialchars($_POST['email_mairie'] ?? '');

            $pdo->prepare("INSERT INTO administratifs (utilisateur_id, region, departement, ville, mairie, date_mairie, email_mairie) VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$utilisateur_id, $region, $departement, $ville, $mairie, $date_mairie, $email_mairie]);
            $_SESSION['id'] = $utilisateur_id;
            header("Location: interface-admi1.php");
            exit;
        }

        $pdo->commit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erreur lors de l'inscription : " . $e->getMessage();
    }
}
?>
