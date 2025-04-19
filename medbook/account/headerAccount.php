<?php
    //session_start(); //session start throws error.
    $user_type = $_SESSION['user_type'];

    if(isset($_POST['home'])) {
        if($user_type == 'patient')
            header('Location: account_patient.php');
        else if($user_type == 'hospital')
            header('Location: account_hospital.php');
        else if($user_type == 'doctor')
            header('Location: account_doctor.php');
        else if($user_type == 'pharmacy')
            header('Location: account_pharmacy.php');
    }
          

    if(isset($_POST['details'])) {
        if($user_type == 'patient')
            header('Location: account_patient.php');
        else if($user_type == 'hospital')
            header('Location: details_hospital.php');
        else if($user_type == 'doctor')
            header('Location: account_doctor.php');
        else if($user_type == 'pharmacy')
            header('Location: account_pharmacy.php');
    } 

    if(isset($_POST['search'])){
        header('Location: search_general.php');
    }


     //______________________________________________________________
    //log out
    if(isset($_POST['logout'])){
        session_unset();
        session_destroy();

        header('Location: index.php');
        exit();
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MEDBOOK</title>

    <link rel="stylesheet" href="account/css_account/root.css" /> 
    <link rel="stylesheet" href="account/css_account/header.css"/>
    <link rel="stylesheet" href="account/css_account/footer.css"/>

    <link rel="stylesheet" href="account/css_account/account.css"/>
    <link rel="stylesheet" href="account/css_account/account_hospital.css"/>
    <link rel="stylesheet" href="account/css_account/account_doctor.css"/>
    <link rel="stylesheet" href="account/css_account/account_pharmacy.css"/>

    <link rel="stylesheet" href="account/css_account/search_general.css"/>

    <link rel="stylesheet" href="account/css_account/view_user.css"/>
    <link rel="stylesheet" href="account/css_account/view_hospital.css"/>
    
    <link rel="stylesheet" href="account/css_account/details_hospital.css"/>

    <link rel="stylesheet" href="account/css_account/conduct_appointment.css"/>
    <link rel="stylesheet" href="account/css_account/prescription_each.css"/>
</head>


<body>
    <!-- Navigation bar for PC -->
    <section class="nav-bar-container-1">
        <div class="nav-bar-profile">
            <div class="navBar-profile-pic">
                <img src="images/profile_pics/<?php echo $targetUser['Profile_pic']; ?>" />
            </div>

            <div class="navBar-profile-name">
                <?php echo $targetUser['Name']; ?>
            </div>
        </div>

        <div class="nav-bar-logo">
            <img src="images/logo.png">

            MEDBOOK
        </div>

        <form class="nav-bar-buttons" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button class="navBar-button" type="submit" name="home" value="1"> Home </button>
            <button class="navBar-button" type="submit" name="details" value="1"> Details </button>
            <button class="navBar-search" type="submit" name="search" value="1">
                Search 

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="grey" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </button>
        </form>
    </section>


    <!-- Navigation bar for mobile -->
    <section class="nav-bar-container-2">
        <form class="navBar-burger-container" action="">
            <button class="navBar-burger" type="button" onclick="showSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(1, 1, 128)" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </form>

        <div class="nav-bar-logo">
            <img src="images/logo.png">

            MEDBOOK
        </div>

        <form class="nav-bar-buttons" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button class="navBar-search-2" type="submit" name="search" value="1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(1, 1, 128)" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </button>
        </form>
    </section>


    <!-- Side bar for mobile -->
    <section class="side-bar-container">
        <form class="side-bar" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button class="sidebar-close-button" onclick="hideSidebar()" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="side-bar-profile">
                <div class="sideBar-profile-pic">
                    <img src="images/profile_pics/<?php echo $targetUser['Profile_pic']; ?>" />
                </div>

                <div class="sideBar-profile-name">
                    <?php echo $targetUser['Name']; ?>
                </div>
            </div>

            <button class="sideBar-button" type="submit" name="home" value="1"> Home </button>
            <button class="sideBar-button" type="submit" name="details" value="1"> Details </button>
            <button class="sideBar-button" type="submit" name="settings" value="1"> Settings </button>
            <button class="sideBar-button" type="submit" name="help" value="1"> Help </button>

            <button class="sidebar-logout-button" type="submit" name="logout" value="logout">
                Logout

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" stroke-width="3.0" fill="white">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                </svg>
            </button>
        </form>
    </section>


    <!-- JavaScript code for the side-bar -->
    <script>
        function showSidebar(){
            const sidebar = document.querySelector('.side-bar');
            sidebar.style.display = 'flex';
        }

        function hideSidebar(){
            const sidebar = document.querySelector('.side-bar');
            sidebar.style.display = 'none';
        }
    </script>