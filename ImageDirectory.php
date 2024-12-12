<?php
class ImageDirectory {
    private $directoryPath;
    private $projectImagePath;
    private $modelName;

    // Ajouter un paramètre pour le nom du modèle
    public function __construct($directoryPath, $projectImagePath, $modelName) {
        if (empty($directoryPath) || !is_string($directoryPath)) {
            throw new Exception("Le chemin source doit être une chaîne de caractères, reçu : " . gettype($directoryPath));
        }
        if (empty($projectImagePath) || !is_string($projectImagePath)) {
            throw new Exception("Le chemin du projet doit être une chaîne de caractères, reçu : " . gettype($projectImagePath));
        }
        if (empty($modelName) || !is_string($modelName)) {
            throw new Exception("Le nom du modèle doit être une chaîne de caractères.");
        }

        $this->directoryPath = $directoryPath;
        $this->projectImagePath = $projectImagePath;
        $this->modelName = $modelName;

        // Créer le dossier du modèle dans le répertoire projet
        $modelPath = $this->projectImagePath . '/' . $this->modelName;
        if (!is_dir($modelPath)) {
            if (!mkdir($modelPath, 0777, true)) {
                throw new Exception("Impossible de créer le dossier du modèle : {$modelPath}");
            }
        }
        $this->projectImagePath = $modelPath;  // Mettre à jour la path du projet avec le dossier du modèle
    }

    public function copyImagesToProject() {
        $images = $this->getImages();
    
        foreach ($images as $image) {
            if (!is_string($image) || !file_exists($image)) {
                throw new Exception("Chemin d'image invalide : $image");
            }

            // Obtenir le chemin relatif
            $relativePath = str_replace($this->directoryPath, '', $image);
            $relativePath = ltrim($relativePath, '/\\'); 
    
            // Recréer la structure des dossiers
            $destinationDir = $this->projectImagePath . '/' . dirname($relativePath);
            $destination = $this->projectImagePath . '/' . $relativePath;
    
            // Créer les sous-dossiers si nécessaire
            if (!is_dir($destinationDir)) {
                if (!mkdir($destinationDir, 0777, true) && !is_dir($destinationDir)) {
                    throw new Exception("Impossible de créer le dossier : $destinationDir");
                }
            }
    
            // Copier le fichier
            if (!copy($image, $destination)) {
                throw new Exception("Impossible de copier l'image : $image vers $destination");
            }
        }
    
        
    }
    
    public function getImages($limit = 2) {
        $images = [];
        $this->scanDirectory($this->directoryPath, $images, $limit);
        return $images;
    }

    private function scanDirectory($directory, &$images, $limit, $currentCount = 0) {
        $files = glob($directory . '/*');

        foreach ($files as $file) {
            if ($currentCount >= $limit) {
                break;
            }

            if (is_file($file) && preg_match('/\.(jpg|png|gif)$/i', $file)) {
                $images[] = $file;
                $currentCount++;
            } elseif (is_dir($file)) {
                $this->scanDirectory($file, $images, $limit, $currentCount);
            }
        }
    }

    public function displayImages() {
        // Récupérer toutes les images dans le dossier du modèle
        $images = glob($this->projectImagePath . '/*/*.{jpg,jpeg,png,gif}', GLOB_BRACE); // Recherche dans les sous-dossiers également
        
        if (empty($images)) {
            echo "<p>Aucune image trouvée dans le dossier du projet : {$this->projectImagePath}</p>";
        } else {
            // Définir la structure de la grille
            echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; padding: 10px;">';
            
            foreach ($images as $image) {
                // Formater le chemin pour qu'il soit compatible avec l'affichage HTML
                $imagePath = str_replace('\\', '/', $image);
                // Afficher chaque image avec un style défini pour la grille
                echo "<div><img src='$imagePath' alt='Image' style='width: 100px; height: 100px; border-radius: 5px;'></div>";
            }
            
            // Fermeture de la grille
            echo '</div>';
        }
    }
    
    
}



?>
