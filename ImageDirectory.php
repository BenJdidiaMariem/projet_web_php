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

    public function getImages() {
        $images = glob($this->directoryPath . '/*.{jpg,png,gif}', GLOB_BRACE);
        return $images ?: []; // Retourne un tableau vide si aucune image n'est trouvée
    }

    public function displayImages() {
        $images = $this->getImages();
        if (empty($images)) {
            echo "<p>Aucune image trouvée dans le dossier : {$this->directoryPath}</p>";
        } else {
            foreach ($images as $image) {
                echo "<img src='$image' alt='Image' style='width:100px;height:auto; margin: 5px;'>";
            }
        }
    }
}

?>