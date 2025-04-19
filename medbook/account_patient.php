<?php
    include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    $sql = "SELECT * FROM $user_type
                WHERE NID = $user" ;
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //_____________________________________________________________________________
    // retreiving rows for Upcoming appointments table (table: appointments)

    $table_message1 = 'No appointments.';

    $sql2 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status, a.Sl
            FROM appointments AS a, doctor AS d, hospital AS h
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status IN ('pending', 'accepted', 'rejected', 'canceled'))
            ORDER BY a.Sl ASC;";
    $result = mysqli_query($conn, $sql2);
    $appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(! empty($appointments))
        $table_message1 = '';
        
    mysqli_free_result($result); 



    //_____________________________________________________________________________
    // retreiving rows for Previous appointments table (table: appointments)

    $table_message2 = 'No appointments.';

    $sql3 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status ,pr.Sl AS pres_Sl, a.Sl
            FROM (appointments AS a, doctor AS d, hospital AS h) LEFT JOIN (prescriptions AS pr)
            ON pr.Sl=a.Sl
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            ORDER BY a.visit_date DESC
            LIMIT 7;";
    $result = mysqli_query($conn, $sql3);
    $prev_appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(! empty($prev_appointments))
        $table_message2= '';
        
    mysqli_free_result($result); 


    //__________________________________________________________________
    // search from the Previsout appointment table (db table: appointments)

    $searchValue1 = '';

    if(isset($_POST['search_appointment']))
    {
        $searchValue1 = $_POST['searched_appointment'];

        $sql3 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status ,pr.Sl AS pres_Sl, a.Sl
            FROM (appointments AS a, doctor AS d, hospital AS h) LEFT JOIN (prescriptions AS pr)
            ON pr.Sl=a.Sl
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            AND((d.Name LIKE '%$searchValue1%')
                OR (h.Name LIKE '%$searchValue1%'))
            ORDER BY a.visit_date DESC
            LIMIT 7;";
        $result = mysqli_query($conn, $sql3);
        $prev_appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($prev_appointments))
            $table_message2 = 'Not found.';

        mysqli_free_result($result);
    }

    //____________________________________________________________________________
    // Most recent, Oldest, View all - buttons for appointments

    if(isset($_POST['sdh_most_recent'])  ||  isset($_POST['sdh_oldest'])  ||  isset($_POST['sdh_view_all']))
    {
        if(isset($_POST['sdh_most_recent']))
        {
            $sql3 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status ,pr.Sl AS pres_Sl, a.Sl
            FROM (appointments AS a, doctor AS d, hospital AS h) LEFT JOIN (prescriptions AS pr)
            ON pr.Sl=a.Sl
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            ORDER BY a.visit_date DESC
            LIMIT 7;";
        }

        else if(isset($_POST['sdh_oldest']))
        {
            $sql3 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status ,pr.Sl AS pres_Sl, a.Sl
            FROM (appointments AS a, doctor AS d, hospital AS h) LEFT JOIN (prescriptions AS pr)
            ON pr.Sl=a.Sl
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            ORDER BY a.visit_date ASC
            LIMIT 7;";
        }

        else if(isset($_POST['sdh_view_all']))
        {
            $sql3 = "SELECT d.Name AS dname, h.Name AS hname, a.hos_department,
		            a.visit_date, a.visit_day, a.visit_hour, a.status ,pr.Sl AS pres_Sl, a.Sl
            FROM (appointments AS a, doctor AS d, hospital AS h) LEFT JOIN (prescriptions AS pr)
            ON pr.Sl=a.Sl
            WHERE (a.user_id='$user')
            AND (a.doctor_nid=d.NID)
            AND (a.hospital_license=h.License_no)
            AND(a.status='conducted')
            ORDER BY a.visit_date DESC;";
        }

        $result = mysqli_query($conn, $sql3);
        $prev_appointments= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(! empty($prev_appointments))
            $table_message2= '';
            
        mysqli_free_result($result); 
    }


    //______________________________________________________________
    //view presciption

    if(isset($_POST['view_prescription']))
    {
        $_SESSION['target_prescription'] = $_POST['view_prescription'];
        header('Location: prescription_each.php');
    }


    //___________________________________________________________________
    // prevoious MEDICATIONS table

    $table_message3 = 'No results';

    $sql4 = "SELECT p.NID, pr.medication, pr.disease, d.Name AS dname,
                    pr.date, pr.valid_till, pr.Sl AS pres_Sl, a.Sl
            FROM patient AS p, doctor AS d, hospital AS h, 
                prescriptions AS pr, appointments AS a
            WHERE (p.NID='$user')
            AND (a.user_id = '$user')
            AND (a.Sl=pr.Sl)
            AND (d.NID=a.doctor_nid)
            AND (h.License_no=a.hospital_license)
            ORDER BY pr.date DESC
            LIMIT 7;";

    $result = mysqli_query($conn, $sql4);
    $prev_medications= mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(! empty($prev_medications))
        $table_message3= '';
        
    mysqli_free_result($result);

    //___________________________________________________________________
    // search Medication

    $searchValue2 = '';

    if(isset($_POST['search_medication']))
    {
        $searchValue2 = $_POST['searched_medication'];

        $sql4 = "SELECT p.NID, pr.medication, pr.disease, d.Name AS dname,
                    pr.date, pr.valid_till, pr.Sl AS pres_Sl, a.Sl
            FROM patient AS p, doctor AS d, hospital AS h, 
                prescriptions AS pr, appointments AS a
            WHERE (p.NID='$user')
            AND (a.user_id = '$user')
            AND (a.Sl=pr.Sl)
            AND (d.NID=a.doctor_nid)
            AND (h.License_no=a.hospital_license)
            AND((pr.medication LIKE '%$searchValue2%')
                OR (pr.disease LIKE '%$searchValue2%')
                OR (d.Name LIKE '%$searchValue2%'))
            ORDER BY pr.date DESC
            LIMIT 7;";

        $result = mysqli_query($conn, $sql4);
        $prev_medications= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($prev_medications))
            $table_message3= 'Not found.';
            
        mysqli_free_result($result);
    }


    //___________________________________________________________________
    // Upload / Download files

    $popup_p = false;
    $popup_message_p = '';

    $today = new DateTime(); //current date
    $today_str = $today->format('Y-m-d'); //string

    //upload file
    if(isset($_POST['upload_file']))
    {
        $file_name = $_FILES['patient_file']['name'];
        $file_tmp_name = $_FILES['patient_file']['tmp_name'];
        $folder = 'report_files/'.$file_name;

        if(move_uploaded_file($file_tmp_name, $folder)){
            //inserting  the file name in db (table: report_files)
            $file_name = mysqli_real_escape_string($conn, $file_name);

            $sql5 = "INSERT INTO report_files(user_id, file_name, date)
                    VALUES('$user', '$file_name', '$today_str');";
            mysqli_query($conn, $sql5);

            //popup message
            $popup_p = true;
            $popup_message_p = "File Uploaded Successfully.";
        }
        else{
            $popup_p = true;
            $popup_message_p = "Sorry! Couldn't upload the file.";
        }
    }


    //download file
    $table_message4 = '';

    $sql6 = "SELECT * FROM report_files
            WHERE user_id='$user'
            ORDER BY Sl DESC
            Limit 3;";
    $result = mysqli_query($conn, $sql6);
    $report_files = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(empty($report_files))
        $table_message4 = 'No uploaded files.';
        
    mysqli_free_result($result);

    //_______________________________________________________________________
    // search and sort Report files

    $searchValue3 = '';

    //search
    if(isset($_POST['search_file']))
    {
        $searchValue3 = $_POST['searched_file'];

        $sql6 = "SELECT * FROM report_files
            WHERE (user_id='$user')
            AND ((file_name LIKE '%$searchValue3%')
                OR (date LIKE '%$searchValue3%'))
            ORDER BY Sl DESC;";
        $result = mysqli_query($conn, $sql6);
        $report_files = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($report_files))
            $table_message4 = 'Not found.';
            
        mysqli_free_result($result); 
    } 


    //sort files
    if(isset($_POST['file_ascending'])){
        $sql6 = "SELECT * FROM report_files
            WHERE user_id='$user'
            ORDER BY file_name ASC;";
    }
    else if(isset($_POST['file_descending'])){
        $sql6 = "SELECT * FROM report_files
            WHERE user_id='$user'
            ORDER BY file_name DESC;";
    }
    else if(isset($_POST['file_viewAll'])){
        $sql6 = "SELECT * FROM report_files
            WHERE user_id='$user'
            ORDER BY Sl DESC;";
    }
    $result = mysqli_query($conn, $sql6);
    $report_files = mysqli_fetch_all($result, MYSQLI_ASSOC);


    //_______________________________________________________________________
    // delete report file

    if(isset($_POST['delete_file']))
    {
        $delete_file = $_POST['delete_file'];

        $sql7 = "DELETE FROM report_files
            WHERE Sl=$delete_file;";
        mysqli_query($conn, $sql7);

        $popup_p = true;
        $popup_message_p = "File Deleted Successfully (press Home to refresh)."; 
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



<html>
    <?php include('account/headerAccount.php'); ?> 

    <!-- display Popup -->
    <?php if($popup_p == true){ ?>
        <div class="popup">
            <?php echo $popup_message_p; ?>
        </div>
    <?php } ?>


    <section class="patient-account-container" style="margin-top: 20px;">
        <div class="account-user-info">
            <!-- table 1 -->
            <div class="search-details-header2">
                <div class="search-header-title2">
                    Upcoming
                    <span style="color:rgb(4, 71, 206); font-weight:500;">Appointments:</span>
                </div>
            </div>

            <div class="user-appointment-table-wrapper">
                <table class="user-appointment-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Hospital</th>
                            <th>Department</th>
                            <th>Visit Date</th>
                            <th>Day & hours</th>
                            <th>Request</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($appointments)) { ?>
                            <?php foreach($appointments as $appointment){ ?>
                                <tr>
                                    <td> <p><?php echo $appointment['dname']; ?></p> </td>

                                    <td> <p><?php echo $appointment['hname']; ?></p> </td>

                                    <td> <p><?php echo $appointment['hos_department']; ?></p> </td>

                                    <td> <p style="font-size:15px;"><?php echo $appointment['visit_date']; ?></p> </td>

                                    <td> 
                                        <p style="font-size: 14px;">
                                            <?php echo $appointment['visit_day'] . '<br>'. $appointment['visit_hour']; ?>
                                        </p> 
                                    </td>

                                    <td> <p style="font-size:15px;"><?php echo $appointment['status']; ?></p> </td>
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
            <div class="search-details-header2" style="margin-top: 130px;">
                <div class="search-header-title2">
                    Previous
                    <span style="color:rgb(4, 71, 206); font-weight:500;">Appointments:</span>
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search by doctor or hospital." name="searched_appointment" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button2" type="submit" name="search_appointment">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>

                <form class="sdh-buttons" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <button type="submit" name="sdh_most_recent"> Most recent </button>

                    <button type="submit" name="sdh_oldest"> Oldest </button>

                    <button type="submit" name="sdh_view_all"> View all </button>
                </form>
            </div>

            <div class="user-appointment-table-wrapper">
                <table class="user-appointment-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Hospital</th>
                            <th>Department</th>
                            <th>Visit Date</th>
                            <th>Day & hours</th>
                            <th>Presciption</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($prev_appointments)) { ?>
                            <?php foreach($prev_appointments as $prev_appointment){ ?>
                                <tr>
                                    <td> <p><?php echo $prev_appointment['dname']; ?></p> </td>

                                    <td> <p><?php echo $prev_appointment['hname']; ?></p> </td>

                                    <td> <p><?php echo $prev_appointment['hos_department']; ?></p> </td>

                                    <td> <p style="font-size:15px;"><?php echo $prev_appointment['visit_date']; ?></p> </td>

                                    <td> 
                                        <p style="font-size: 14px;">
                                            <?php echo $prev_appointment['visit_day'] . '<br>'. $prev_appointment['visit_hour']; ?>
                                        </p> 
                                    </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <?php if($prev_appointment['pres_Sl'] != NULL){ ?>
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


            <!-- table 3 -->
            <div class="search-details-header2" style="margin-top: 130px;">
                <div class="search-header-title">
                    Medication
                    <span style="color:rgb(3, 178, 3); font-weight:500;">Record:</span>
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="medication, disease or doctor." name="searched_medication" value="<?php echo htmlspecialchars($searchValue2); ?>" >

                    <button class="search-bar-button" type="submit" name="search_medication">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="medication-table-wrapper">
                <table class="medication-table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Cause / Disease</th>
                            <th>Prescribed by</th>
                            <th>Prescibed on</th>
                            <th>Valid till</th>
                            <th>Presciption</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($prev_medications)) { ?>
                            <?php foreach($prev_medications as $prev_medication){ //outer loop of medications ?>
                                <?php 
                                    $each_medication = explode(',', $prev_medication['medication']);
                                    $each_disease = explode(',', $prev_medication['disease']);

                                    for($i=0; $i<count($each_medication); $i++) { // inner loop of Each medication separately
                                ?>
                                    <tr>
                                        <td> <p><?php echo $each_medication[$i%count($each_medication)]; ?></p> </td>

                                        <td> <p><?php echo $each_disease[$i%count($each_disease)]; ?></p> </td>

                                        <td> <p><?php echo $prev_medication['dname']; ?></p> </td>

                                        <td> <p><?php echo $prev_medication['date']; ?></p> </td>

                                        <td> <p><?php echo $prev_medication['valid_till']; ?></p> </td>

                                        <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                            <td>
                                                <?php if($prev_medication['pres_Sl'] != NULL){ ?>
                                                    <button class="accept-request-button" type="submit" name="view_prescription" value="<?php echo $prev_medication['pres_Sl']; ?>" >
                                                        view

                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(3, 105, 3)" class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                        </svg>
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </form>
                                    </tr>
                                <?php } // end of inner medication loop ?>
                            <?php /* end of foreach */} ?>
                        <?php } /*end of if statement*/?>
                    </tbody>
                </table>
            </div>

            <div class="search-message">
                <?php echo htmlspecialchars($table_message3); ?>
            </div>


            <!-- Upload / Downlad files -->
            <div class="search-user-header" style="margin-top: 100px;">
                <div class="search-header-title">
                    Files
                    <span style="color:rgb(3, 178, 3); font-weight:500;">&</span>
                    Reports
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="File name / date" name="searched_file" value="<?php echo htmlspecialchars($searchValue3); ?>" >

                    <button class="search-bar-button" type="submit" name="search_file">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>

                <form class="file-sort-buttons" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <button name="file_ascending" value="1">A to Z</button>
                    <button name="file_descending" value="1">Z to A</button>
                    <button name="file_viewAll">View all</button>
                </form>
            </div>

            <div class="upload-download-files">
                <form class="download-files" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="download-file-table-wrapper">
                        <table class="download-file-table">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Date</th>
                                    <th>View</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(! empty($report_files)) { ?>
                                    <?php foreach($report_files as $report_file) { ?>
                                        <tr>
                                            <td><p><?php echo $report_file['file_name'] ?></p></td>

                                            <td style="font-size:12px;"><?php echo $report_file['date'] ?></td>

                                            <td>
                                                <div class="file-options">
                                                    <a href="report_files/<?php echo $report_file['file_name']; ?>" target="_blank">View</a>

                                                    <button type="submit" name="delete_file" value="<?php echo $report_file['Sl']; ?>">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } //end of foreach loop ?>
                                <?php } //end of if statement ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="search-message" style="text-align:center;">
                        <?php echo htmlspecialchars($table_message4); ?>
                    </div>
                </form>


                <form class="upload-files" enctype="multipart/form-data" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <p>Upload Reports/Files:</p>
                    <input type="file" name="patient_file" required>

                    <button class="cap-send-button" type="submit" name="upload_file" value="upload" style="margin-left:0;">
                        upload
                    </button>
                </form>
            </div>
        </div>


        <!-- logout -->
        <form class="logout-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button type="submit" name="logout" value="logout">
                Logout
            </button>
        </form>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 