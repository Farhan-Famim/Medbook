<?php
     include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    $sql = "SELECT * FROM $user_type
                WHERE License_no = '$user'" ;
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //_____________________________________________________________________________
    // retreiving appointment rows from the table: appointments

    $search_message1 = '';

    $sql2 = "SELECT p.Name AS pname, p.Contact_number AS pcontact, d.Name AS dname, a.hos_department,
                    a.visit_date, a.visit_day, a.visit_hour, a.Sl
            FROM appointments AS a, doctor AS d, patient AS p
            WHERE (a.hospital_license='$user')
                AND (a.user_id=p.NID)
                AND (a.doctor_nid=d.NID)
                AND (a.status='pending')
            ORDER BY a.Sl ASC;";
    $result = mysqli_query($conn, $sql2);
    $appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(empty($appointments))
        $search_message1 = 'No requests.';
        
    mysqli_free_result($result); 

    //__________________________________________________________________
    // search from the table

    $searchValue1 = '';

    if(isset($_POST['search_schedule']))
    {
        $searchValue1 = $_POST['searched_schedule'];

        $sql3 = "SELECT p.Name AS pname, p.Contact_number AS pcontact, d.Name AS dname, a.hos_department,
                        a.visit_date, a.visit_day, a.visit_hour, a.Sl
                FROM appointments AS a, doctor AS d, patient AS p
                WHERE (a.hospital_license='$user')
                    AND (a.user_id=p.NID)
                    AND (a.doctor_nid=d.NID)
                    AND (a.status='pending')
                    AND((p.Name LIKE '%$searchValue1%') 
                        OR (d.Name LIKE '%$searchValue1%')
                        OR (a.hos_department LIKE '%$searchValue1%')
                        );";
        $result = mysqli_query($conn, $sql3);
        $appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($appointments))
            $search_message1 = 'Not found.';

        mysqli_free_result($result);
    }

    //______________________________________________________________________
    // Accept & Reject appointment (table: appointments)

    $target_appointment = 101;

    //Accept button
    if(isset($_POST['accept_appointment'])){
        $target_appointment = $_POST['accept_appointment'];

        $sql4 = "UPDATE appointments
                SET status='accepted'
                WHERE Sl=$target_appointment;";
        mysqli_query($conn, $sql4);

        header('Location: account_hospital.php');
    }

    //Reject button
    if(isset($_POST['reject_appointment'])){
        $target_appointment = $_POST['reject_appointment'];

        $sql4 = "DELETE FROM appointments
                WHERE Sl=$target_appointment;";
        mysqli_query($conn, $sql4);

        header('Location: account_hospital.php');
    }

    //______________________________________________________________
    //log out
    if(isset($_POST['logout'])){
        session_unset();
        session_destroy();

        header('Location: login_hospital.php');
        exit();
    }

?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="hospital-account-container">
        <div class="account-hospital-info">
            <div class="search-details-header2">
                <div class="search-header-title2">
                    Appointment
                    <span style="color:rgb(4, 71, 206); font-weight:500;">Requests</span>
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Patient / Doctor / Dept." name="searched_schedule" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button2" type="submit" name="search_schedule">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="hospital-account-table-wrapper">
                <table class="hospital-account-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Patient Con.</th>
                            <th>Doctor</th>
                            <th>Dept.</th>
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

                                    <td> <p><?php echo $appointment['pcontact']; ?></p> </td>

                                    <td> <p><?php echo $appointment['dname']; ?></p> </td>

                                    <td> <p><?php echo $appointment['hos_department']; ?></p> </td>

                                    <td> <p style="font-size:15px;"><?php echo $appointment['visit_date']; ?></p> </td>

                                    <td> 
                                        <p style="font-size: 14px;">
                                            <?php echo $appointment['visit_day'] . '<br>'. $appointment['visit_hour']; ?>
                                        </p> 
                                    </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <div class="accept-reject-div">
                                                <button class="accept-request-button" type="submit" name="accept_appointment" value="<?php echo $appointment['Sl']; ?>" >
                                                    Accept

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(3, 105, 3)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </button>

                                                <button class="reject-request-button" type="submit" name="reject_appointment" value="<?php echo $appointment['Sl']; ?>" >
                                                    Reject

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
                <?php echo htmlspecialchars($search_message1); ?>
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