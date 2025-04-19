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

    
    //______________________________________________________________
    //retrieve view Hospitals's info from db

    $view_hospital = $_SESSION['view_hospital_license'];

    // Getting data from the database
    $sql = "SELECT * FROM hospital
                WHERE License_no = '$view_hospital'" ;
    $result = mysqli_query($conn, $sql);
    $targetView = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //_________________________________________________________________
    // View hospital appointment schedule

    $search_message1 = '';
    $searchValue1 = '';
    
    $sql2 = "SELECT w.Department, d.Name, w.Working_days, w.Visit_hours, w.Visit_fee, w.Doctor_nid
            FROM works_at AS w INNER JOIN doctor AS d
            ON w.Doctor_nid=d.NID
            WHERE w.Hospital_license='$view_hospital';" ;
    $result = mysqli_query($conn, $sql2);
    $dept = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_free_result($result);

    //______________________________________
    // Search in the schedule table

    if(isset($_POST['search_schedule']))
    {  
        $searchValue1 = $_POST['searched_schedule']; 

        //searching data from works_at
        $sql3 = "SELECT w.Department, d.Name, w.Working_days, w.Visit_hours, w.Visit_fee, w.Doctor_nid
                FROM works_at AS w INNER JOIN doctor AS d
                ON w.Doctor_nid=d.NID
                WHERE (w.Hospital_license='$view_hospital')
                    AND ((w.Department LIKE '%$searchValue1%')
                        OR (d.Name LIKE '%$searchValue1%'));" ;
        $result = mysqli_query($conn, $sql3);
        $dept = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($dept))
            $search_message1 = "Not found.";
        else
            $search_message1 = '';

        mysqli_free_result($result);
    }


    //_______________________________________
    //Appointment button

    $popup_vh = false;
    $popup_message_vh = '';

    if(isset($_POST['send_appointment']))
    {
        //$search_message1 = $_POST['visit_days'] . " ". $_POST['visit_hours'];

        $vh = explode('|', $_POST['visit_hours']); // vh[0]=visit_hour , vh[1]=visit_day
        $visit_date = $_POST['visit_date'];
        $arr = explode('|', $_POST['send_appointment']);  // $arr[0]=Department , $arr[1]=Doctor_nid , $arr[2]=Visit_fee

        //filtering the data
        $vh[0] =  mysqli_real_escape_string($conn, $vh[0]);
        $vh[1] =  mysqli_real_escape_string($conn, $vh[1]);
        $arr[0] =  mysqli_real_escape_string($conn, $arr[0]);
        $arr[2] =  mysqli_real_escape_string($conn, $arr[2]);

        //Inserting data in db (table: appointments)
        $sql4 = "INSERT INTO appointments(user_id, doctor_nid, hospital_license, hos_department, visit_date, visit_day, visit_hour, visit_fee, status)
                VALUES('$user', '$arr[1]', '$view_hospital', '$arr[0]', '$visit_date', '$vh[1]', '$vh[0]', '$arr[2]', 'pending');";
        mysqli_query($conn, $sql4);

        $popup_vh = true;
        $popup_message_vh = "\nAppointment request has been sent successfully.";
    }


?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <!-- display Popup -->
    <?php if($popup_vh == true){ ?>
        <div class="popup">
            <?php echo $popup_message_vh; ?>
        </div>
    <?php } ?>

    <section class="view-hospital-container">
        <div class="banner-hospital">
            <div class="banner-hospital-dp">
                <img src="images/profile_pics/<?php echo $targetView['Profile_pic']; ?>" />
            </div>

            <div class="banner-hospital-text">
                <div class="banner-hospital-title">
                    <?php echo $targetView['Name']; ?>
                </div>

                <div class="banner-hospital-address">
                    <?php echo $targetView['Address']; ?>
                </div>

                <div class="banner-hospital-contact">
                    <?php echo "Cont.:  " ?>
                    <span style="color: rgb(2, 2, 147);">
                        <?php echo $targetView['Contact_no']; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="view-hospital-info">
            <div class="search-details-header">
                <div class="search-header-title">
                    Appointment
                    <span style="color:rgb(3, 178, 3); font-weight:500;">Schedule</span>
                </div>

                <form class="search-bar1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Department or Doctor" name="searched_schedule" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button" type="submit" name="search_schedule">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="view-appointment-table-wrapper">
                <table class="view-appointment-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Doctor</th>
                            <th>Visit date</th>
                            <th>Visit hours</th>
                            <th>Visit fee</th>
                            <th>Appointment</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($dept)) { ?>
                            <?php foreach($dept as $each_depts){ ?>
                                <tr>
                                    <td>
                                        <p><?php echo $each_depts['Department']; ?></p>
                                    </td>

                                    <td>
                                        <p><?php echo $each_depts['Name']; ?></p>
                                    </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <input class="view-options" type="date" name="visit_date" required> 
                                        </td>

                                        <td>
                                            <select class="view-options" name="visit_hours" >
                                                <?php 
                                                    $vdays = explode(',' , $each_depts['Working_days']);
                                                    $vhours = explode(',' , $each_depts['Visit_hours']);
                                                    for($i=0; $i<count($vdays); $i++) { ?>
                                                        <option value="<?php echo $vhours[$i%count($vhours)] . '|' . $vdays[$i%count($vdays)]; ?>"> 
                                                            <?php echo $vhours[$i%count($vhours)] . ' (' . $vdays[$i%count($vdays)] . ')'; ?> 
                                                        </option>
                                                <?php } ?>
                                            </select>
                                        </td>

                                        <td>
                                            <p><?php echo $each_depts['Visit_fee']; ?> tk/-</p>
                                        </td>

                                        <td>
                                            <button class="send-request-button" 
                                                    title="Sent appointment request" 
                                                    type="submit" name="send_appointment" 
                                                    value="<?php echo ($each_depts['Department'] . '|' . $each_depts['Doctor_nid'] . '|' . $each_depts['Visit_fee']); ?>" >
                                                Request

                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="rgb(3, 105, 3)" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                                </svg>
                                            </button>
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
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 