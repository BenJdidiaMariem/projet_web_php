<?php
require 'Hyperparameters.php';
require 'Database.php';
require 'modelController.php';
require 'ImageDirectory.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exécution du modèle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        h3 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 5px;
        }

        .image-gallery {
            display: flex;
            flex-wrap: wrap;
        }

        
        .container {
            margin-top: 20px;
        }

        p {
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php

if (isset($_POST['learningRate'], $_POST['epochs'], $_POST['patience'], $_POST['monitor'], $_POST['optimizer'], $_POST['modelName'], $_POST['activationFunction'], $_POST['validationSplit'], $_POST['testSplit'], $_POST['imageDirectory'])) {
    $directoryPath = $_POST['imageDirectory']; 
    $modelName = $_POST['modelName'];
    $projectImagePath = "img";
    $hyperParams = new Hyperparameters($_POST);
} else {
    echo "<p class='error'>Des données sont manquantes dans le formulaire.</p>";
    exit;
}

// Valider les paramètres
$errors = $hyperParams->validate();
if (!empty($errors)) {
    echo "<p class='error'>" . implode("<br>", $errors) . "</p>";
    exit;
}

// Vérification si le chemin est vide
if (empty($directoryPath)) {
    echo "<p class='error'>Le chemin du dossier ne peut pas être vide.</p>";
    exit;
}

$db = new Database('localhost', 'root', '', 'basemodel');
$database = new Database();

// Vérifier si le modèle existe déjà dans la base de données
if ($database->checkIfModelExists($modelName)) {
    echo "<p class='error'>Le nom du modèle '$modelName' existe déjà. Veuillez en choisir un autre.</p>";
    echo "<a href='form.html'>Retour au formulaire</a>";
    exit;
} else {
    // Si le modèle n'existe pas, enregistrer les paramètres dans la base de données
    $db->saveHyperparameters($hyperParams->getParams());
    echo "<p class='success'>Les hyperparamètres ont été enregistrés dans la base avec succès.</p>";
}

// Instanciation des hyperparamètres pour affichage
$enteredParams = $hyperParams->getParams();  // Récupérer les hyperparamètres saisis par l'utilisateur

// Récupérer les derniers hyperparamètres enregistrés
$lastParams = $db->getLastHyperparameters();  // Récupère les derniers hyperparamètres enregistrés

// Affichage des hyperparamètres
echo "<h3>Hyperparamètres saisis :</h3>";
echo "<ul>";
foreach ($enteredParams as $key => $value) {
    echo "<li><strong>" . htmlspecialchars($key) . " :</strong> " . htmlspecialchars($value) . "</li>";
}
echo "</ul>";

// Appel du modèle Python avec les hyperparamètres extraits de la base de données
$modelController = new modelController('dog_species_reduit.py', $db);
$modelOutput = $modelController->runModel($lastParams);

// Vérifiez si la sortie est une erreur ou un résultat
if (strpos($modelOutput, 'Erreur') !== false) {
    echo "<p class='error'>$modelOutput</p>";
} else {
    // Affichage de la sortie du modèle Python (résultats décodés en JSON)
    $result = json_decode($modelOutput, true);
    
    if ($result) {
        $finalAccuracy = $result['final_accuracy'];
        $finalValLoss = $result['final_val_loss'];

        echo "<h3>Résultats du modèle :</h3>";
        echo "<p><strong>Précision finale :</strong> $finalAccuracy</p>";
        echo "<p><strong>Perte finale de validation :</strong> $finalValLoss</p>";
        $modelName = $lastParams['Model_Name'];
        $db->saveModelResults($modelName, $finalAccuracy, $finalValLoss);
    } else {
        echo "<p class='error'>Erreur lors de l'extraction des résultats du script Python.</p>";
    }
}

try {
    $imageDirectory = new ImageDirectory($directoryPath, $projectImagePath, $modelName);
    $imageDirectory->copyImagesToProject();
    $images = $imageDirectory->getImages();
    echo "<h3>Apercu du dataset :</h3>";
    echo "<div>";
    $imageDirectory->displayImages();
    echo "</div>";
} catch (Exception $e) {
    echo "<p class='error'>Erreur lors de la gestion des images : " . $e->getMessage() . "</p>";
    exit;
}
?>

</body>
</html>
