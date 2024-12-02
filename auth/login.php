<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $descriptor = $data['descriptor'] ?? null;

    if (!$descriptor) {
        echo json_encode(['success' => false, 'message' => 'Descripteur manquant']);
        exit;
    }

    $usersDir = __DIR__ . '/users';
    foreach (scandir($usersDir) as $user) {
        if ($user === '.' || $user === '..') continue;

        $profileImage = "$usersDir/$user/profile.jpg";
        if (file_exists($profileImage)) {
            // Charger et comparer le descripteur
            // Utiliser un script JS côté serveur (node.js) ou Python pour la correspondance.
            // Placeholder : connexion réussie si le fichier existe
            echo json_encode(['success' => true, 'username' => $user]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Utilisateur non reconnu']);
}
?>
