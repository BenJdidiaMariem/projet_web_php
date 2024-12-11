<?php
class ImageDirectory {
    private $directoryPath;

    public function __construct($directoryPath) {
        // Vérifie que le chemin est une chaîne
        if (empty($directoryPath) || !is_string($directoryPath)) {
            throw new Exception("Le chemin doit être une chaîne de caractères, reçu : " . gettype($directoryPath));
        }

        $this->directoryPath = $directoryPath;

        // Vérifie si le chemin est valide ou crée le dossier s'il n'existe pas
        if (!is_dir($this->directoryPath)) {
            if (!mkdir($this->directoryPath, 0777, true)) {
                throw new Exception("Impossible de créer ou accéder au dossier spécifié : {$this->directoryPath}");
            }
        }
    }

    // Recherche les images dans le répertoire principal et ses sous-dossiers
    public function getImages() {
        $images = [];
        $this->scanDirectory($this->directoryPath, $images);
        return $images;
    }

    private function scanDirectory($directory, &$images) {
        // Récupère tous les fichiers et sous-dossiers dans le répertoire
        $files = glob($directory . '/*');

        foreach ($files as $file) {
            // Si c'est un fichier, on vérifie si c'est une image
            if (is_file($file) && preg_match('/\.(jpg|png|gif)$/i', $file)) {
                $images[] = $file;
            }
            // Si c'est un sous-dossier, on appelle la fonction récursivement
            elseif (is_dir($file)) {
                $this->scanDirectory($file, $images);
            }
        }
    }

    public function displayImages() {
        $images = $this->getImages();
        if (empty($images)) {
            echo "<p>Aucune image trouvée dans le dossier : {$this->directoryPath}</p>";
        } else {
            foreach ($images as $image) {
                // Remplacer les antislashes par des slashes
                $imagePath = str_replace('\\', '/', $image);

                // Affichage du chemin de l'image
                echo "<p>Chemin de l'image : $imagePath</p>"; 

                // Affichage des images
                echo "<img src='$imagePath' alt='Image' style='width:100px;height:auto; margin: 5px;'>";
            }
        }
    }
}

?>
