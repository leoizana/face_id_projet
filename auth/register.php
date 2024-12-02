<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification de l'existence des données
    if (!isset($_POST['username']) || !isset($_FILES['photoUpload'])) {
        echo json_encode(['success' => false, 'message' => 'Pseudo ou photo manquante']);
        exit;
    }

    $username = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['username']); // Sanitize pseudo
    // Définir le dossier où les photos et les utilisateurs seront enregistrés
    $uploadDir = __DIR__ . "/../users/$username"; // Remonter d'un niveau pour accéder au dossier users

    // Créer un dossier utilisateur s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Sauvegarde de la photo
    $photoPath = $uploadDir . '/profile.jpg';
    if (move_uploaded_file($_FILES['photoUpload']['tmp_name'], $photoPath)) {
        echo json_encode(['success' => true, 'message' => 'Utilisateur inscrit avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la photo']);
    }
}
?>
