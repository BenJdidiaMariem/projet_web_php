<?php
    class Hyperparameters {
        private $params = [];
    
        public function __construct($params) {
            $this->params = $params;
        }
    
        public function validate() {
            $errors = [];
            $learning_rate = $this->params['learningRate'] ?? null;
            $epochs = $this->params['epochs'] ?? null;
            $patience = $this->params['patience'] ?? null;
            $monitor = $this->params['monitor'] ?? null;
            $optimizer = $this->params['optimizer'] ?? null;
            $model_name = $this->params['modelName'] ?? null;
            $directory_path = $this->params['imageDirectory'] ?? null;
            $activation_function = $this->params['activationFunction'] ?? null;
            $validation_split = $this->params['validationSplit'] ?? null;
            $test_split = $this->params['testSplit'] ?? null;

            if ($learning_rate === null || $learning_rate < 0.0001 || $learning_rate > 1) {
                $errors[] = "Le taux d’apprentissage doit être un nombre entre 10⁻⁴ et 1.";
            }
            
            if ($epochs === null || $epochs < 10 || $epochs > 50) {
                $errors[] = "Le nombre d’époques doit être entre 10 et 50.";
            }
            
            if (!in_array($patience, [3, 5, 7])) {
                $errors[] = "La patience doit être 3, 5 ou 7.";
            }
            
            if (!in_array($monitor, ['val_loss', 'val_accuracy'])) {
                $errors[] = "Le monitor doit être 'val_loss' ou 'val_accuracy'.";
            }
            
            if (!in_array($optimizer, ['adam', 'sgd'])) {
                $errors[] = "L'optimiseur doit être 'Adam' ou 'SGD'.";
            }
            
            if (empty($model_name)) {
                $errors[] = "Le nom du modèle ne peut pas être vide.";
            }
            
            if (!in_array($activation_function, ['sigmoid', 'relu', 'tanh', 'softmax'])) {
                $errors[] = "La fonction d'activation doit être Sigmoid, ReLU, Tanh ou Softmax.";
            }
            
            if (!in_array($validation_split, [0.1, 0.2])) {
                $errors[] = "Le validation_split doit être 0.1 ou 0.2.";
            }
            
            if (!in_array($test_split, [0.1, 0.2])) {
                $errors[] = "Le test_split doit être 0.1 ou 0.2.";
            }
            
            if (($validation_split + $test_split) >= 1) {
                $errors[] = "La somme de validation_split et test_split doit être inférieure à 1.";
            }
            
            return $errors;
        }
    
        public function getParams() {
            return $this->params;
        }
    }
?>    