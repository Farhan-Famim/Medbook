<?php
    include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    if($user_type == 'patient'){
        $sql = "SELECT * FROM $user_type
                WHERE NID = '$user'" ;
    }
    else if($user_type == 'doctor'){
        $sql = "SELECT * FROM $user_type
                WHERE NID = '$user'" ;
    }
    else if($user_type == 'hospital'){
        $sql = "SELECT * FROM $user_type
                WHERE License_no = '$user'" ;
    }
    
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //___________________________________________________________________
    // Search Users data from db
    $users = array();

    $searchValue1 = '';
    $search_message1 = 'No  Results.';

    if(isset($_POST['search_user']))
    { 
        $searchValue1 = $_POST['searched_user']; 

        $sql = "SELECT NID, Name, Profile_pic
                FROM patient
                WHERE (Name LIKE '%$searchValue1%')
                    OR (Email = '$searchValue1')
                LIMIT 10";
        $result = mysqli_query($conn, $sql);
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($users))
            $search_message1 = "Not found.";
        else
            $search_message1 = '';

        mysqli_free_result($result);
    }


    $_SESSION['view_patient_nid'] = '';
    if(isset($_POST['view_user']))
    {
        $_SESSION['view_patient_nid'] = $_POST['view_user'];

        header('Location: view_patient.php');
    }

    //___________________________________________________________________
    // Search Hospital data from db
    $hospitals = array();

    $searchValue2 = '';
    $search_message2 = 'No  Results.';

    if(isset($_POST['search_hos']))
    { 
        $searchValue2 = $_POST['searched_hospital']; 

        $sql = "SELECT Name, Address, License_no 
                FROM hospital
                WHERE (Name LIKE '%$searchValue2%')
                    OR (Address LIKE '%$searchValue2%')
                LIMIT 10";
        $result = mysqli_query($conn, $sql);
        $hospitals = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($hospitals))
            $search_message2 = "Not found.";
        else
            $search_message2 = '';

        mysqli_free_result($result);
    }


    $_SESSION['view_hospital_license'] = '';
    if(isset($_POST['view_hospital']))
    {
        $_SESSION['view_hospital_license'] = $_POST['view_hospital'];

        header('Location: view_hospital.php');
    }

    //___________________________________________________________________
    // Search Doctors data from db
    $doctors = array();

    $searchValue3 = '';
    $search_message3 = 'No  Results.';

    if(isset($_POST['search_doctor']))
    { 
        $searchValue3 = $_POST['searched_doctor']; 

        $sql = "SELECT NID, Name, Profile_pic, Hospital
                FROM doctor
                WHERE (Name LIKE '%$searchValue3%')
                    OR (Email = '$searchValue3')
                    OR (Hospital LIKE '%$searchValue3%')
                LIMIT 10";
        $result = mysqli_query($conn, $sql);
        $doctors = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($doctors))
            $search_message3 = "Not found.";
        else
            $search_message3 = '';

        mysqli_free_result($result);
    }


    $_SESSION['view_doctor_nid'] = '';
    if(isset($_POST['view_doctor']))
    {
        $_SESSION['view_doctor_nid'] = $_POST['view_doctor'];

        header('Location: view_doctor.php');
    }

?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="search-divs-container">
        <div class="search-section-user">
            <div class="search-user-header">
                <div class="search-header-title">
                    Find Other
                    <span style="color:rgb(3, 178, 3); font-weight:500;">Users:</span>
                </div>

                <form class="search-bar1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Name or Email" name="searched_user" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button" type="submit" name="search_user">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="search-user-table-wrapper">
                <table class="search-user-table">
                    <thead>
                        <tr>
                            <th>User Profile</th>
                            <th>Visit</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($users)) { ?>
                            <?php foreach($users as $each_user){ ?>
                                <tr>
                                    <td>
                                        <div class="searched-profile">
                                            <div class="searched-profile-pic">
                                                <img src="images/profile_pics/<?php echo $each_user['Profile_pic']; ?>" />
                                            </div>

                                            <p class="searched-profile-name">
                                                <?php echo $each_user['Name']; ?>
                                            </p>
                                        </div>
                                    </td>

                                    <td>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                            <button class="hospital-profile" title="View Profile" type="submit" name="view_user" value="<?php echo $each_user['NID']; ?>">
                                                View
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(255,255,255)" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                </svg>
                                            </button> 
                                        </form>
                                    </td>
                                </tr>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>

            <div class="search-message">
                <?php echo htmlspecialchars($search_message1); ?>
            </div>
        </div>


        <div class="search-section-hospital">
            <div class="search-hospital-header">
                <div class="search-header-title">
                    Hospitals & Clinics:
                </div>

                <form class="search-bar2" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Name or Address" name="searched_hospital" value="<?php echo htmlspecialchars($searchValue2); ?>" >

                    <button class="search-bar-button" type="submit" name="search_hos">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="search-hospital-table-wrapper">
                <table class="search-hospital-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Account</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($hospitals)) { ?>
                            <?php foreach($hospitals as $each_hospital){ ?>
                                <tr>
                                <td> <p> <?php echo htmlspecialchars($each_hospital['Name']); ?> </p> </td>
                                <td> <p> <?php echo htmlspecialchars($each_hospital['Address']); ?> </p> </td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <button class="hospital-profile" title="View Accout" type="submit" name="view_hospital" value="<?php echo $each_hospital['License_no']; ?>">
                                            Profile
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(255,255,255)" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                            </svg>
                                        </button> 
                                    </form>
                                </td>
                                </tr>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>
            

            <div class="search-message">
                <?php echo htmlspecialchars($search_message2); ?>
            </div>
        </div>


        <div class="search-section-doctor">
            <div class="search-doctor-header">
                <div class="search-header-title">
                    Look for
                    <span style="color:rgb(3, 178, 3); font-weight:500;">Doctors:</span>
                </div>

                <form class="search-bar3" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Name / Email / Specialization" name="searched_doctor" value="<?php echo htmlspecialchars($searchValue3); ?>" >

                    <button class="search-bar-button" type="submit" name="search_doctor">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="search-doctor-table-wrapper">
                <table class="search-doctor-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Appointed In</th>
                            <th>Visit</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($doctors)) { ?>
                            <?php foreach($doctors as $each_doctor){ ?>
                                <tr>
                                    <td>
                                        <div class="searched-profile">
                                            <div class="searched-profile-pic">
                                                <img src="images/profile_pics/<?php echo $each_doctor['Profile_pic']; ?>" />
                                            </div>

                                            <p class="searched-profile-name">
                                                <?php echo $each_doctor['Name']; ?>
                                            </p>
                                        </div>
                                    </td>

                                    <td> <p><?php echo $each_doctor['Hospital']; ?></p> </td>

                                    <td>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                            <button class="hospital-profile" title="View Profile" type="submit" name="view_doctor" value="<?php echo $each_doctor['NID']; ?>">
                                                View
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(255,255,255)" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                </svg>
                                            </button> 
                                        </form>
                                    </td>
                                </tr>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>

            <div class="search-message">
                <?php echo htmlspecialchars($search_message3); ?>
            </div>
        </div>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 