<?php

class ModelController {
    private $pythonScriptPath;
    private $db;

    
    public function __construct($pythonScriptPath, $db) {
        $this->pythonScriptPath = $pythonScriptPath;
        $this->db = $db;  
    }
    public function runModel($params) {
        // Commande pour appeler le script Python avec les paramètres
        $command = "C:\\Users\\lenovo\\AppData\\Local\\Programs\\Python\\Python310\\python.exe {$this->pythonScriptPath} --learning_rate=" . escapeshellarg($params['Taux_Apprentissage']) . 
                             " --epochs=" . escapeshellarg($params['Nombre_Epoques']) . 
                             " --patience=" . escapeshellarg($params['Patience']) . 
                             " --monitor=" . escapeshellarg($params['Monitor']) . 
                             " --optimizer=" . escapeshellarg($params['Optimiser']) . 
                             " --model_name=" . escapeshellarg($params['Model_Name']) . 
                             " --activation_function=" . escapeshellarg($params['Activation_Function']) . 
                             " --validation_split=" . escapeshellarg($params['Validation_Split']) . 
                             " --test_split=" . escapeshellarg($params['Test_Split']) . 
                             " --directory_path=" . escapeshellarg($params['Directory_path']);
    
        // Exécution du script Python et capture de la sortie
        $output = shell_exec($command);
        
        if ($output === null) {
            // Erreur d'exécution du script
            return "Erreur d'exécution du script Python.";
        } else {
            // Retourne la sortie du script
            return $output;
        }
    }
    
}
