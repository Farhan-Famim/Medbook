<?php
     include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    $sql = "SELECT * FROM $user_type
                WHERE NID = '$user'" ;
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //_____________________________________________________________________________
    // retreiving appointment rows for Upcoming appointment table (db table: appointments)

    $table_message1 = 'No appointments.';

    $sql2 = "SELECT p.Name AS pname, h.Name AS hname, a.visit_date, a.visit_day, a.visit_hour, a.Sl
            FROM appointments AS a, patient AS p, hospital AS h
            WHERE (a.doctor_nid='$user')
            AND (a.user_id=p.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='accepted')
            ORDER BY a.Sl ASC;";
    $result = mysqli_query($conn, $sql2);
    $appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(! empty($appointments))
        $table_message1 = '';
        
    mysqli_free_result($result); 

    //__________________________________________________________________
    // search from the table: appointments

    $searchValue1 = '';

    if(isset($_POST['search_appointment']))
    {
        $searchValue1 = $_POST['searched_appointment'];

        $sql3 = "SELECT p.Name AS pname, h.Name AS hname, a.visit_date, a.visit_day, a.visit_hour, a.Sl
            FROM appointments AS a, patient AS p, hospital AS h
            WHERE (a.doctor_nid='$user')
            AND (a.user_id=p.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='accepted')
            AND((p.Name LIKE '%$searchValue1%')
                OR (h.Name LIKE '%$searchValue1%'))
            ORDER BY a.Sl ASC;";
        $result = mysqli_query($conn, $sql3);
        $appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($appointments))
            $table_message1 = 'Not found.';

        mysqli_free_result($result);
    }

    //______________________________________________________________________
    // Conduct & Cancel appointment (table: appointments)

    $target_appointment = 101;
    $_SESSION['target_doc_appointment'] = '';

    //Conduct button
    if(isset($_POST['conduct_appointment'])){
        $target_appointment = $_POST['conduct_appointment'];
        $_SESSION['target_doc_appointment'] = $target_appointment; 

        header('Location: conduct_appointment.php');
    }

    //Cancel button
    if(isset($_POST['cancel_appointment'])){
        $target_appointment = $_POST['cancel_appointment'];

        $sql4 = "UPDATE appointments
                SET status='canceled'
                WHERE Sl=$target_appointment;";
        mysqli_query($conn, $sql4);

        header('Location: account_doctor.php');
    }



     //_____________________________________________________________________________
    // retreiving appointment rows for previous/recent appointments table (db table: appointments)

    $table_message2 = 'No appointments.';

    $sql5 = "SELECT p.Name AS pname, h.Name AS hname, a.visit_date, a.visit_day, a.visit_hour, a.Sl, pr.Sl AS pres_Sl
            FROM (appointments AS a, patient AS p, hospital AS h) LEFT JOIN prescriptions AS pr
            ON pr.Sl=a.Sl
            WHERE (a.doctor_nid='$user')
            AND (a.user_id=p.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            ORDER BY a.visit_date DESC
            LIMIT 10;";
    $result = mysqli_query($conn, $sql5);
    $prev_appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(! empty($prev_appointments))
        $table_message2 = '';
        
    mysqli_free_result($result); 

    //__________________________________________________________________
    // search for the previous/recent appointment table (table: appointments)

    $searchValue2 = '';

    if(isset($_POST['search_appointment2']))
    {
        $searchValue2 = $_POST['searched_appointment2'];

        $sql6 = "SELECT p.Name AS pname, h.Name AS hname, a.visit_date, a.visit_day, a.visit_hour, a.Sl, pr.Sl AS pres_Sl
            FROM (appointments AS a, patient AS p, hospital AS h) LEFT JOIN prescriptions AS pr
            ON pr.Sl=a.Sl
            WHERE (a.doctor_nid='$user')
            AND (a.user_id=p.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            AND((p.Name LIKE '%$searchValue2%')
                OR (h.Name LIKE '%$searchValue2%'))
            ORDER BY a.visit_date DESC
            LIMIT 10;";
            
        $result = mysqli_query($conn, $sql6);
        $prev_appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($prev_appointments))
            $table_message2 = 'Not found.';

        mysqli_free_result($result);
    }


    //_____________________________________________________________________________
    // view Prescription

    if(isset($_POST['view_prescription']))
    {
        $_SESSION['target_prescription'] = $_POST['view_prescription'];
        header('Location: prescription_each.php');
    }



    //______________________________________________________________
    //log out
    if(isset($_POST['logout'])){
        session_unset();
        session_destroy();

        header('Location: login_doctor.php');
        exit();
    }

?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="patient-account-container">
        <div class="account-user-info">
            <!-- table 1 -->
            <div class="search-details-header2">
                <div class="search-header-doc-title2">
                    Upcoming
                    <span style="color:rgb(253, 83, 137); font-weight:500;">Appointments:</span>
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search by patient or hospital." name="searched_appointment" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button3" type="submit" name="search_appointment">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="doctor-appointment-table-wrapper">
                <table class="doctor-appointment-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Visit Date</th>
                            <th>Day & hours</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($appointments)) { ?>
                            <?php foreach($appointments as $appointment){ ?>
                                <tr>
                                    <td> <p><?php echo $appointment['pname']; ?></p> </td>

                                    <td> <p><?php echo $appointment['hname']; ?></p> </td>

                                    <td> <p style="font-size:15px;"><?php echo $appointment['visit_date']; ?></p> </td>

                                    <td> 
                                        <p style="font-size: 14px;">
                                            <?php echo $appointment['visit_day'] . '<br>'. $appointment['visit_hour']; ?>
                                        </p> 
                                    </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <div class="accept-reject-div">
                                                <button class="accept-request-button" type="submit" name="conduct_appointment" value="<?php echo $appointment['Sl']; ?>" >
                                                    Conduct

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(3, 105, 3)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </button>

                                                <button class="reject-request-button" type="submit" name="cancel_appointment" value="<?php echo $appointment['Sl']; ?>" >
                                                    Cancel

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(220, 9, 9)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                    </svg>

                                                </button>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>

            <div class="search-message">
                <?php echo htmlspecialchars($table_message1); ?>
            </div>

            
            <!-- table 2 -->
            <div class="search-details-header2" style="margin-top: 120px;">
                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search by patient or hospital." name="searched_appointment2" value="<?php echo htmlspecialchars($searchValue2); ?>" >

                    <button class="search-bar-button3" type="submit" name="search_appointment2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>

                <div class="search-header-doc-title2">
                    Most Recent
                    <span style="color:rgb(253, 83, 137); font-weight:500;">Appointments</span>
                </div>
            </div>

            <div class="doctor-appointment-table-wrapper">
                <table class="doctor-appointment-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Visit Date</th>
                            <th>Day & hours</th>
                            <th>Presciption</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($prev_appointments)) { ?>
                            <?php foreach($prev_appointments as $prev_appointment){ ?>
                                <tr>
                                    <td> <p><?php echo $prev_appointment['pname']; ?></p> </td>

                                    <td> <p><?php echo $prev_appointment['hname']; ?></p> </td>

                                    <td> <p style="font-size:15px;"><?php echo $prev_appointment['visit_date']; ?></p> </td>

                                    <td> 
                                        <p style="font-size: 14px;">
                                            <?php echo $prev_appointment['visit_day'] . '<br>'. $prev_appointment['visit_hour']; ?>
                                        </p> 
                                    </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <?php if($prev_appointment['pres_Sl'] != NULL) { ?>
                                                <button class="accept-request-button" type="submit" name="view_prescription" value="<?php echo $prev_appointment['pres_Sl']; ?>" >
                                                    view

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(3, 105, 3)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                    </svg>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </form>
                                </tr>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>

            <div class="search-message">
                <?php echo htmlspecialchars($table_message2); ?>
            </div>
        </div>

        <form class="logout-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button type="submit" name="logout" value="logout">
                Logout
            </button>
        </form>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 