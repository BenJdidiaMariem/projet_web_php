<?php 
    class Database {
        private $conn;
    
        
    
// Constructeur pour initialiser la connexion à la base de données
    public function __construct($host = 'localhost', $user = 'root', $password = '', $dbName = 'basemodel') {
        $this->conn = new mysqli($host, $user, $password, $dbName);
        
        if ($this->conn->connect_error) {
            die("Échec de la connexion à la base de données : " . $this->conn->connect_error);
        }
    }
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT firstName, lastName, email FROM utilisateurs");
        
        if (!$stmt) {
            return []; // Si la préparation de la requête échoue, retournez un tableau vide
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Retourne tous les utilisateurs si des lignes sont trouvées
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // Aucun utilisateur trouvé, retournez un tableau vide
            return [];
        }
    }
    public function savePerson($firstName, $lastName, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (firstName, lastName, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
    
        if (!$stmt->execute()) {
            return ["success" => false, "message" => "Erreur lors de l'enregistrement : " . $stmt->error];
        }
        return ["success" => true, "message" => "Utilisateur enregistré avec succès."];
    }
    
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT password FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            
            if (password_verify($password, $hashedPassword)) {
                return ["success" => true, "message" => "Connexion réussie."];
            } else {
                return ["success" => false, "message" => "Mot de passe incorrect."];
            }
        } else {
            return ["success" => false, "message" => "Utilisateur non trouvé."];
        }
    }
    
    public function saveHyperparameters($params) {
        // Vérifier si le nom du modèle existe déjà dans la base de données
        if ($this->checkIfModelExists($params['modelName'])) {
            return "Le modèle '{$params['modelName']}' existe déjà dans la base de données.";
        }

        // Si le modèle n'existe pas, on procède à l'enregistrement
        $stmt = $this->conn->prepare("INSERT INTO model (Taux_Apprentissage, Nombre_Epoques, Patience, Monitor, Optimiser, Model_Name, Activation_Function, Validation_Split, Test_Split, Directory_path) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Bind des paramètres
        $stmt->bind_param("diissssdds", $params['learningRate'], $params['epochs'], $params['patience'], $params['monitor'], $params['optimizer'], $params['modelName'], $params['activationFunction'], $params['validationSplit'], $params['testSplit'], $params['imageDirectory']);
        
        if (!$stmt->execute()) {
            return "Erreur : " . $stmt->error;
        }
        return "Données enregistrées avec succès.";
    }
        public function checkIfModelExists($modelName) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM model WHERE Model_Name = ?");
            $stmt->bind_param("s", $modelName);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            return $count > 0;  // Retourne true si le modèle existe, sinon false
        }

    
        public function getLastHyperparameters() {
            $stmt = $this->conn->prepare("SELECT * FROM model ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row;
            
        }
        public function saveModelResults($modelName, $finalAccuracy, $finalValLoss) {
            // Préparation de la requête SQL
            $stmt = $this->conn->prepare("INSERT INTO ModelResults (model_name, final_accuracy, final_val_loss) 
                                          VALUES (?, ?, ?)");
            
            // Bind des paramètres
            $stmt->bind_param("sdd", $modelName, $finalAccuracy, $finalValLoss);
            
            // Exécution de la requête
            if ($stmt->execute()) {
                return "Résultats enregistrés avec succès.";
            } else {
                return "Erreur : " . $stmt->error;
            }
        }
        
    }
?>    