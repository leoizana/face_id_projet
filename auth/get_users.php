<?php
header('Content-Type: application/json');

// Chemin vers le répertoire des utilisateurs
$usersDir = __DIR__ . '/../users';
$usersData = [];

// Vérifier si le dossier existe
if (is_dir($usersDir)) {
    // Lister les dossiers des utilisateurs
    foreach (scandir($usersDir) as $userFolder) {
        if ($userFolder === '.' || $userFolder === '..') {
            continue; // Ignorer les dossiers système
        }

        $userPath = $usersDir . '/' . $userFolder;

        // Vérifier si c'est un dossier
        if (is_dir($userPath)) {
            $images = [];

            // Récupérer les fichiers images dans le dossier de l'utilisateur
            foreach (scandir($userPath) as $file) {
                $filePath = $userPath . '/' . $file;
                if (is_file($filePath) && preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
                    $images[] = '/users/' . $userFolder . '/' . $file;
                }
            }

            // Ajouter l'utilisateur s'il a au moins une image
            if (!empty($images)) {
                $usersData[] = [
                    'username' => $userFolder,
                    'images' => $images
                ];
            }
        }
    }
}

// Retourner les données des utilisateurs au format JSON
echo json_encode($usersData);
