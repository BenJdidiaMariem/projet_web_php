<?php 
    class Database {
        private $conn;
    
        public function __construct($host, $user, $password, $dbName) {
            $this->conn = new mysqli($host, $user, $password, $dbName);
            if ($this->conn->connect_error) {
                die("Erreur de connexion : " . $this->conn->connect_error);
            }
        }
    
        public function saveHyperparameters($params) {
            var_dump($params);
            // Utiliser 'imageDirectory' au lieu de 'directory_path'
            $stmt = $this->conn->prepare("INSERT INTO model (Taux_Apprentissage, Nombre_Epoques, Patience, Monitor, Optimiser, Model_Name, Activation_Function, Validation_Split, Test_Split, Directory_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Bind des paramètres, assurez-vous que le nom de la clé est correct (imageDirectory)
            $stmt->bind_param("diissssdds", $params['learningRate'], $params['epochs'], $params['patience'], $params['monitor'], $params['optimizer'], $params['modelName'], $params['activationFunction'], $params['validationSplit'], $params['testSplit'], $params['imageDirectory']);
            
            if (!$stmt->execute()) {
                return "Erreur : " . $stmt->error;
            }
            return "Données enregistrées avec succès.";
        }
    
        public function getPreviousConfigurations() {
            $result = $this->conn->query("SELECT * FROM model ORDER BY created_at DESC");
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
?>    