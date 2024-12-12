<?php
header('Content-Type: application/json');

// Connexion à la base de données
$pdo = new PDO('mysql:host=127.0.0.1;dbname=facial_recognition', 'root', 'root');

// Récupérer les utilisateurs et leurs descripteurs depuis la base de données
$stmt = $pdo->query("SELECT username, descriptors FROM users");
$usersData = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Décoder les descripteurs depuis la base de données (format JSON)
    $descriptors = json_decode($row['descriptors'], true);

    // Vérifier si l'utilisateur a des descripteurs
    if (!empty($descriptors)) {
        // Ajouter l'utilisateur à la liste avec son nom d'utilisateur et les descripteurs
        $usersData[] = [
            'username' => $row['username'],
            'descriptors' => $descriptors
        ];
    }
}

// Retourner les données des utilisateurs et des descripteurs au format JSON
echo json_encode($usersData);
?>
