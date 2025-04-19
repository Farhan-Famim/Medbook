<?php
    if(isset($_POST['Patient'])) 
        header('Location: index.php');  

    if(isset($_POST['Doctor'])) 
        header('Location: login_doctor.php'); 

    if(isset($_POST['Hospital'])) 
        header('Location: login_hospital.php');  

    if(isset($_POST['Pharmacy'])) 
        header('Location: login_pharmacy.php'); 
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MEDBOOK</title>

    <link rel="stylesheet" href="login/css_login/root.css" /> 
    <link rel="stylesheet" href="login/css_login/header.css"/>
    <link rel="stylesheet" href="login/css_login/footer.css"/>

    <link rel="stylesheet" href="login/css_login/login.css"/>
</head>


<body>
    <section class="nav-bar-container">
        <div class="nav-bar-logo">
            <img src="images/logo.png">

            MEDBOOK
        </div>

        <form class="nav-bar-buttons" method="POST" action="index.php">
            <button type="submit" name="patient" value="1"> Patient </button>
            <button type="submit" name="Doctor" value="1"> Doctor </button>
            <button type="submit" name="Hospital" value="1"> Hospital </button>
            <button type="submit" name="Pharmacy" value="1"> Pharmacy </button> 
        </form>
    </section>