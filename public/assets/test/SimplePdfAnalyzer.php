<?php
class SimplePdfAnalyzer {
    private $keywords;
    
    public function __construct() {
        $this->keywords = require __DIR__ . '/keywords_config.php';
        error_log("Mots-clés chargés : " . print_r($this->keywords, true));
    }
    
    public function analyzePdf($pdfPath) {
        try {
            $pdftotext = 'C:\Program Files\poppler-24.08.0\Library\bin\pdftotext.exe';
            
            if (!file_exists($pdftotext)) {
                error_log("pdftotext n'est pas trouvé à l'emplacement : " . $pdftotext);
                return false;
            }

            error_log("Tentative d'analyse du fichier : " . $pdfPath);
            
            $content = shell_exec('"' . $pdftotext . '" "' . $pdfPath . '" -');
            if ($content === null) {
                error_log("Impossible de lire le PDF : " . $pdfPath);
                return false;
            }
            
            $text = strtolower($content);
            $matches = [];
            
            foreach ($this->keywords as $category => $subcategories) {
                foreach ($subcategories as $subcategory => $keywords) {
                    $keywordFound = false;
                    
                    foreach ($keywords as $keyword) {
                        if (strpos($text, strtolower($keyword)) !== false) {
                            if (!isset($matches[$category])) {
                                $matches[$category] = [];
                            }
                            if (!isset($matches[$category][$subcategory])) {
                                $matches[$category][$subcategory] = [];
                            }
                            if (!in_array(basename($pdfPath), $matches[$category][$subcategory])) {
                                $matches[$category][$subcategory][] = basename($pdfPath);
                            }
                            $keywordFound = true;
                        }
                    }
                }
            }
            
            return $matches;
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'analyse du PDF : " . $e->getMessage());
            return false;
        }
    }
}