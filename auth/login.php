<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=facial_recognition', 'root', 'root'); // Remplacer par les bonnes informations de connexion

// Vérifier si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Erreur dans les données JSON envoyées']);
        exit;
    }

    $descriptor = $data['descriptor'] ?? null;

    // Vérifier que le descripteur est présent
    if (!$descriptor) {
        echo json_encode(['success' => false, 'message' => 'Descripteur manquant']);
        exit;
    }

    // Convertir le descripteur envoyé en tableau PHP
    $descriptor = json_decode($descriptor);

    // Vérifier que le descripteur est un tableau valide
    if (!is_array($descriptor) || count($descriptor) == 0) {
        echo json_encode(['success' => false, 'message' => 'Descripteur invalide']);
        exit;
    }

    // Préparer la requête pour récupérer les utilisateurs et leurs descripteurs
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        // Récupérer les descripteurs faciaux de l'utilisateur depuis la base de données
        $storedDescriptors = json_decode($user['descriptors']); // Descripteurs stockés sous forme JSON
        
        // Vérifier que les descripteurs stockés sont un tableau valide
        if (!is_array($storedDescriptors) || count($storedDescriptors) == 0) {
            continue; // Passer si descripteurs invalides dans la base
        }

        // Comparer les descripteurs (ici, une comparaison simple, mais tu peux utiliser une méthode plus avancée)
        $distance = calculateDistance($descriptor, $storedDescriptors);

        if ($distance < 0.6) { // Seuil de similarité (ajuste ce seuil en fonction de la précision souhaitée)
            echo json_encode(['success' => true, 'username' => $user['username']]);
            exit;
        }
    }

    // Si aucune correspondance n'a été trouvée
    echo json_encode(['success' => false, 'message' => 'Utilisateur non reconnu']);
}

// Fonction pour calculer la distance entre deux descripteurs (distance euclidienne simplifiée)
function calculateDistance($descriptor1, $descriptor2) {
    $sum = 0;
    $length = count($descriptor1);

    // Calcul de la distance euclidienne entre les deux descripteurs
    for ($i = 0; $i < $length; $i++) {
        $sum += pow($descriptor1[$i] - $descriptor2[$i], 2);
    }

    return sqrt($sum); // Retourne la distance euclidienne
}
?>
