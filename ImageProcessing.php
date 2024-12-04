<!-- <?php
    class ImageProcessing {
        private $scriptPath;
    
        public function __construct($scriptPath) {
            $this->scriptPath = $scriptPath;
        }
    
        public function processImages($directoryPath) {
            $command = escapeshellcmd("python {$this->scriptPath} {$directoryPath}");
            $output = shell_exec($command);
            return $output;
        }
    }
?>     -->