<?php
    require 'ImageDirectory.php';
    require 'Hyperparameters.php';
    require 'Database.php';
    require 'ImageProcessing.php';
    require 'ResultDisplay.php';
    
    // Exemple d'utilisation
    try {
        // Récupération et validation du chemin
        $directory_path = $_POST['imageDirectory'] ?? null;
    
        if (empty($directory_path) || !is_string($directory_path)) {
            throw new Exception("Veuillez fournir un chemin de dossier valide.");
        }
    
        // Instanciation de la classe ImageDirectory
        $imageDir = new ImageDirectory($directory_path);    
        
        // Affiche les images trouvées dans le dossier
        $imageDir->displayImages();
    } catch (Exception $e) {
        echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
        exit;
    }
    
    $hyperParams = new Hyperparameters($_POST);
    $errors = $hyperParams->validate();
    if (!empty($errors)) {
        echo implode("<br>", $errors);
        exit;
    }
    
    $db = new Database('localhost', 'root', '', 'basemodel');
    echo $db->saveHyperparameters($hyperParams->getParams());
    
    // $imageProcessor = new ImageProcessing('process_images.py');
    // $result = $imageProcessor->processImages('images/');
    // $display = new ResultDisplay();
    // $display->display($result);
    
    // $configs = $db->getPreviousConfigurations();
    // $display->displayPreviousConfigurations($configs);
?>     