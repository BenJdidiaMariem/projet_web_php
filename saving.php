<?php
require 'Hyperparameters.php';
require 'Database.php';
require 'modelController.php';
require 'ImageDirectory.php';
// Vérifiez si les données du formulaire sont envoyées via POST
if (isset($_POST['learningRate'], $_POST['epochs'], $_POST['patience'], $_POST['monitor'], $_POST['optimizer'], $_POST['modelName'], $_POST['activationFunction'], $_POST['validationSplit'], $_POST['testSplit'], $_POST['imageDirectory'])) {
    $directory_path = $_POST['imageDirectory']; 
   
    // Vérification si le chemin est vide
    if (empty($directory_path)) {
        echo "Le chemin du dossier ne peut pas être vide.";
        exit;
    }
      
    $hyperParams = new Hyperparameters($_POST); 
} else {
    echo "Des données sont manquantes dans le formulaire.";
    exit;
}

// Valider les paramètres
$errors = $hyperParams->validate();
if (!empty($errors)) {
    // Si des erreurs sont présentes, les afficher et arrêter l'exécution
    echo implode("<br>", $errors);
    exit;
}

// Connexion à la base de données
$db = new Database('localhost', 'root', '', 'basemodel');

// Enregistrer les paramètres dans la base de données
echo $db->saveHyperparameters($hyperParams->getParams());
$lastParams = $db->getLastHyperparameters();  // Récupère les derniers hyperparamètres enregistrés
var_dump($lastParams);
// Appel du modèle Python avec les hyperparamètres extraits de la base de données
$modelController = new modelController('dog_species_reduit.py',$db);
$modelController->runModel($lastParams);

?>

