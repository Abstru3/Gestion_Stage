<?php
namespace App\Controllers;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        } else {
            require_once '../app/views/auth/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        } else {
            require_once '../app/views/auth/register.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
    }
}