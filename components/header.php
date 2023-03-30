<?php

require_once __DIR__ . "/../api/user.php";
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lolxd</title>
    <script src="https://kit.fontawesome.com/36c23ba1e4.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="static/style.css">
</head>

<body class="has-navbar-fixed-top">
    <?php

    // if ($_SESSION["user"] ?? null) {
        include 'navbar.php';
    // }

    ?>

    <section class="section">
        <div class="container">