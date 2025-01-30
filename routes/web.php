<?php

$router->add('/', 'HomeController', 'index');
$router->add('login', 'AuthController', 'login');
$router->add('register', 'AuthController', 'register');
$router->add('logout', 'AuthController', 'logout');
$router->add('profile', 'ProfileController', 'show');
$router->add('apply', 'InternshipController', 'apply');
$router->add('post-internship', 'InternshipController', 'post');
$router->add('admin', 'AdminController', 'index');
