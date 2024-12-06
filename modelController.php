<?php

class ModelController {
    private $pythonScriptPath;

    public function __construct($pythonScriptPath) {
        $this->pythonScriptPath = $pythonScriptPath;
    }

    public function runModel($params) {
        // Récupérer les paramètres extraits de la base de données
        $imageDirectory = escapeshellarg($params['Directory_path']);
        $learningRate = escapeshellarg($params['Taux_Apprentissage']);
        $epochs = escapeshellarg($params['Nombre_Epoques']);
        $patience = escapeshellarg($params['Patience']);
        $monitor = escapeshellarg($params['Monitor']);
        $optimizer = escapeshellarg($params['Optimiser']);
        $modelName = escapeshellarg($params['Model_Name']);
        $activationFunction = escapeshellarg($params['Activation_Function']);
        $validationSplit = escapeshellarg($params['Validation_Split']);
        $testSplit = escapeshellarg($params['Test_Split']);

        // Commande pour appeler le script Python avec les paramètres
        $command = "python3 {$this->pythonScriptPath} --imageDirectory=$imageDirectory --learningRate=$learningRate --epochs=$epochs --patience=$patience --monitor=$monitor --optimizer=$optimizer --modelName=$modelName --activationFunction=$activationFunction --validationSplit=$validationSplit --testSplit=$testSplit";

        // Exécution du script Python et capture de la sortie
        $output = shell_exec($command);

        // Retourner les résultats du script Python sous forme de tableau PHP (s'ils sont en JSON)
        return json_decode($output, true);
    }
}
