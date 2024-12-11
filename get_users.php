<?php

include 'UserManager.php';
$userManager = new UserManager();
$users = $userManager->getAllUsers();
header('Content-Type: application/json');
echo json_encode($users);
