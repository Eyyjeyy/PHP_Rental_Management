<?php
    include 'admin.php';
    $admin = new Admin();

	// session_start();
	session_destroy();
    // $admin->handleRedirect();
    header('Location: login.php');


	// header('location:index.php');
?>