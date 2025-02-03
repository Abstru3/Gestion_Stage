<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        $pdo = require_once '/Gestion_Stage/app/config/database.php';
        $recent_internships = get_internships($pdo);
        require_once 'Gestion_Stage/app/views/home.php';
    }
}