<?php 
header('Content-Type: application/json');
require 'Database.php';

class UserManager {
    private $db;

    // Constructeur
    public function __construct() {
        $this->db = new Database(); 
    }

    public function register($formData) {
        $firstName = $formData['firstName'];
        $lastName = $formData['lastName'];
        $email = $formData['email'];
        $password = $formData['password'];

        // Ici vous pouvez ajouter une validation supplémentaire côté serveur
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "Les champs email et mot de passe sont obligatoires."];
        }

        return $this->db->savePerson($firstName, $lastName, $email, $password);
    }
   

    public function getAllUsers() {
        
        return $this->db->getAllUsers(); }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "Les champs email et mot de passe sont obligatoires."];
        }

        return $this->db->login($email, $password);
    }
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userManager = new UserManager();

    if (!isset($_POST['action'])) {
        echo json_encode(["success" => false, "message" => "Action manquante."]);
        exit;
    }

    switch ($_POST['action']) {
        case 'login':
            $email = $_POST['email'];
            $password = $_POST['password'];
            $result = $userManager->login($email, $password);
            break;

        case 'register':
            $result = $userManager->register($_POST);
            header('Location: form.html');
            break;
        

        default:
            echo json_encode(["success" => false, "message" => "Action invalide."]);
            exit;
    }

    echo json_encode($result);
}
?>
