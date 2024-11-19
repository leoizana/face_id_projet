<?php
session_start();

// Vérifier si le paramètre 'username' est passé dans l'URL
if (isset($_GET['username'])) {
    $_SESSION['user'] = $_GET['username']; // Sauvegarder le nom de l'utilisateur dans la session
    echo json_encode(["message" => "Bienvenue, " . $_SESSION['user'] . " ! Vous etes connecte."]);
} else {
    echo json_encode(["message" => "Aucun utilisateur reconnu."]);
}
?>
