<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=facial_recognition', 'root', 'root'); // Remplacer par les bonnes informations de connexion

// Vérifier si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification de l'existence des données
    if (!isset($_POST['username']) || !isset($_FILES['photoUpload']) || !isset($_POST['descriptor'])) {
        echo json_encode(['success' => false, 'message' => 'Pseudo, photo ou descripteur manquants']);
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
    $photoUploaded = move_uploaded_file($_FILES['photoUpload']['tmp_name'], $photoPath);
    
    // Vérifier si la photo a bien été téléchargée
    if (!$photoUploaded) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la photo']);
        exit;
    }

    // Récupérer le descripteur facial envoyé par le client
    $descriptor = $_POST['descriptor']; // Descripteur envoyé en JSON

    // Vérifier que le descripteur est valide
    if (empty($descriptor)) {
        echo json_encode(['success' => false, 'message' => 'Descripteur facial manquant']);
        exit;
    }

    // Vérification si le nom d'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur déjà pris']);
        exit;
    }

    // Sauvegarder l'utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (username, profile_image_path, descriptors) VALUES (:username, :profile_image_path, :descriptors)");
    $stmt->execute([
        'username' => $username,
        'profile_image_path' => $photoPath,
        'descriptors' => $descriptor // Sauvegarder le descripteur facial en JSON
    ]);

    echo json_encode(['success' => true, 'message' => 'Utilisateur inscrit avec succès']);
}
?>
