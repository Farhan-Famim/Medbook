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
    //retrieve view Doctor's info from db

    $view_doctor = $_SESSION['view_doctor_nid'];

    // Getting data from the database
    $sql = "SELECT * FROM doctor
                WHERE NID = $view_doctor" ;
    $result = mysqli_query($conn, $sql);
    $targetView = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);


    //_________________________________________________________________
    // View doctor appointment schedule

    $table_message1 = '';
    
    $sql2 = "SELECT h.Name, w.Department, w.Working_days, w.Visit_hours, w.Visit_fee, w.Doctor_nid
            FROM works_at AS w INNER JOIN hospital AS h
            ON w.Hospital_license=h.License_no
            WHERE w.Doctor_nid='$view_doctor'
            ORDER BY h.Name ASC;" ;
    $result = mysqli_query($conn, $sql2);
    $schedules = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(empty($schedules))
        $table_message1 = 'No schedule.';

    mysqli_free_result($result);


    //_________________________________________________________________
    //calculate age of view user

    $dob_date = new DateTime($targetView['DOB']); //create DateTime object from DOB
    $today = new DateTime(); //current date
    $age = $today->diff($dob_date)->y;  // Get the age in years
?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="view-user-container">
        <div class="banner-doctor-container">
            <div class="banner-doctor">
                <div class="view-user-dp">
                    <img src="images/profile_pics/<?php echo $targetView['Profile_pic']; ?>" />
                </div>

                <div class="banner-user-card">
                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Name
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Name']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachEven">
                        <div class="view-subj">
                            Age
                        </div>

                        <div class="view-data">
                            <?php echo $age; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Gender
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Gender']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachEven">
                        <div class="view-subj">
                            Specialization
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Specialization']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Contact No.
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Contact_number']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachEven">
                        <div class="view-subj">
                            Email
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Email']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- table 1 -->
            <div class="search-details-header2"  style="margin-top: 230px;">
                <div class="search-header-doc-title2">
                    Appointment
                    <span style="color:rgb(253, 83, 137); font-weight:500;">Schedules:</span>
                </div>
            </div>

            <table class="doctor-appointment-view-table">
                <thead>
                    <tr>
                        <th>Hospital/Clinic</th>
                        <th>Department</th>
                        <th>Visit hours</th>
                        <th>Visit fee</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(! empty($schedules)) { ?>
                        <?php foreach($schedules as $schedule){ ?>
                            <tr>
                                <td> <p><?php echo $schedule['Name']; ?></p> </td>

                                <td> <p><?php echo $schedule['Department']; ?></p> </td>

                                <td>
                                        <select class="view-options" >
                                            <?php 
                                                $vdays = explode(',' , $schedule['Working_days']);
                                                $vhours = explode(',' , $schedule['Visit_hours']);
                                                for($i=0; $i<count($vdays); $i++) { ?>
                                                    <option value="<?php echo $vhours[$i%count($vhours)] . '|' . $vdays[$i%count($vdays)]; ?>"> 
                                                        <?php echo $vhours[$i%count($vhours)] . ' (' . $vdays[$i%count($vdays)] . ')'; ?> 
                                                    </option>
                                            <?php } ?>
                                        </select>
                                </td>

                                <td style="padding-left: 9px"><?php echo $schedule['Visit_fee'] . ' tk/-'; ?></td>
                            </tr>
                        <?php /* end of foreach */} ?>
                    <?php } /*end of if statement*/?>
                </tbody>
            </table>

            <div class="search-message">
                <?php echo htmlspecialchars($table_message1); ?>
            </div>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 