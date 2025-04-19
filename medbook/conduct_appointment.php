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

    //______________________________________________________________________________
    // retrieving appointment info from appointments

    $target_appointment = $_SESSION['target_doc_appointment'];

    $sql2 = "SELECT * FROM appointments
            WHERE Sl=$target_appointment;";
        $result = mysqli_query($conn, $sql2);
        $appointment = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //______________________________________________________________________________
    // retrieving patient info from table: patient

    $appointment_patient = $appointment['user_id'];

    $sql2 = "SELECT * FROM patient
            WHERE NID='$appointment_patient';";
    $result = mysqli_query($conn, $sql2);
    $ta_patient = mysqli_fetch_assoc($result); //ta: target appointment
        
    mysqli_free_result($result); 

    //_________________________________________________________________
    //calculating age of view user

    $dob_date = new DateTime($ta_patient['DOB']); //create DateTime object from DOB
    $today = new DateTime(); //current date
    $age = $today->diff($dob_date)->y;  // Get the age in years

    //fixing weight
    $weight = '';
    if($ta_patient['Weight'] != 0)
        $weight = $ta_patient['Weight'] . ' ' . 'kg';


    //___________________________________________________________________
    // prevoious MEDICATIONS table

    $table_message3 = 'No results';

    $sql4 = "SELECT p.NID, pr.medication, pr.disease, d.Name AS dname,
                    pr.date, pr.valid_till, pr.Sl AS pres_Sl, a.Sl
            FROM patient AS p, doctor AS d, hospital AS h, 
                prescriptions AS pr, appointments AS a
            WHERE (p.NID='$appointment_patient')
            AND (a.user_id = '$appointment_patient')
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
            WHERE (p.NID='$appointment_patient')
            AND (a.user_id = '$appointment_patient')
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


    //_________________________________________________________________________________
    //View & Download report file
    $table_message4 = ''; 

    $sql6 = "SELECT * FROM report_files
            WHERE user_id='$appointment_patient'
            ORDER BY Sl DESC
            LIMIT 3;";
    $result = mysqli_query($conn, $sql6);
    $report_files = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(empty($report_files))
        $table_message4 = 'No uploaded files.';
        
    mysqli_free_result($result);

    //_______________________________________________________________________
    // search & sort Report files

    $searchValue3 = '';

    //search
    if(isset($_POST['search_file']))
    {
        $searchValue3 = $_POST['searched_file'];

        $sql6 = "SELECT * FROM report_files
            WHERE (user_id='$appointment_patient')
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
    if(isset($_POST['file_oldest'])){
        $sql6 = "SELECT * FROM report_files
        WHERE user_id='$appointment_patient'
        ORDER BY Sl ASC
        LIMIT 3;";
    }
    else if(isset($_POST['file_recent'])){
        $sql6 = "SELECT * FROM report_files
        WHERE user_id='$appointment_patient'
        ORDER BY Sl DESC
        LIMIT 3;";
    }
    else if(isset($_POST['file_viewAll'])){
        $sql6 = "SELECT * FROM report_files
        WHERE user_id='$appointment_patient'
        ORDER BY Sl DESC;";
    }
    $result = mysqli_query($conn, $sql6);
    $report_files = mysqli_fetch_all($result, MYSQLI_ASSOC);



    //______________________________________________________________________________
    // retrieving prescription related info

    $appointment_patient = $appointment['user_id'];

    $sql3 = "SELECT d.Name AS dname, h.Name AS hname, 
                h.Address AS haddress, h.Contact_no AS hcontact, a.Sl
            FROM doctor AS d, hospital AS h, appointments AS a
            WHERE (a.Sl=$target_appointment)
            AND (d.NID=a.doctor_nid)
            AND (h.License_no=a.hospital_license);";
    $result = mysqli_query($conn, $sql3);
    $ta_prescription = mysqli_fetch_assoc($result); //ta: target appointment
        
    mysqli_free_result($result);

    //______________________________________________________________________________
    // Prescription

    $valid_till = $pres_date = $disease = $medication = $popup_message_ca = '';
    $today_str = $today->format('Y-m-d');
    $popup_ca = false;

    if(isset($_POST['send_prescription']))
    {
        $pres_sl = $_POST['send_prescription'];
        $pres_date = $_POST['pres_date'];
        $valid_till = $_POST['pres_valid_till'];
        $disease = $_POST['disease'];
        $medication = $_POST['medication'];

        $disease = mysqli_real_escape_string($conn, $disease);
        $medication = mysqli_real_escape_string($conn, $medication);

        $sql4 = "INSERT INTO prescriptions(Sl, date, valid_till, disease, medication)
                VALUES('$pres_sl', '$pres_date', '$valid_till', '$disease', '$medication');";
        mysqli_query($conn, $sql4);

        $popup_ca = true;
        $popup_message_ca = "Prescription has been sent to " . $ta_patient['Name'] . "'s account successfully.";
    }


    //_____________________________________________________________________________
    // view Prescription

    if(isset($_POST['view_prescription']))
    {
        $_SESSION['target_prescription'] = $_POST['view_prescription'];
        header('Location: prescription_each.php');
    }


    //______________________________________________________________________________________
    // conclude appointment (update table: appointments)

    if(isset($_POST['ca_conclude']))
    {
        $sql5 = "UPDATE appointments
                SET status='conducted'
                WHERE Sl=$target_appointment";
        mysqli_query($conn, $sql5);

        $sql5 = "INSERT INTO treated_by(P_NID, D_NID, Appointment)
                VALUES('$appointment_patient', '$user', $target_appointment);";
        mysqli_query($conn, $sql5);

        $popup_ca = true;
        $popup_message_ca = "Appointment Concluded Successfully.";
    } 
?>




<html>
    <?php include('account/headerAccount.php'); ?> 

    <!-- display Popup -->
    <?php if($popup_ca == true){ ?>
        <div class="popup">
            <?php echo $popup_message_ca; ?>
        </div>
    <?php } ?>

    <section class="conduct-appointment-container">
        <!-- banner card -->
        <div class="conduct-appointment-banner">
            <div class="cab-text">
                <div class="cab-text-name">
                    <?php echo $ta_patient['Name']; ?>
                </div>

                <div class="cab-text-email">
                    <?php echo $ta_patient['Email']; ?>
                </div>

                <div class="cab-text-tables">
                    <table class="cab-table1">
                        <thead>
                            <tr>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Religion</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <p><?php echo $age; ?></p> </td>

                                <td> <p><?php echo $ta_patient['Gender']; ?></p> </td>

                                <td> <p><?php echo $ta_patient['Religion']; ?></p> </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="cab-table1-wrapper">
                        <table class="cab-table1">
                            <thead>
                                <tr>
                                    <th>Blood type</th>
                                    <th>Weight (appx.) </th>
                                    <th>Height (appx.) </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td> <p><?php echo $ta_patient['Blood_type']; ?></p> </td>

                                    <td> <p><?php echo $weight; ?></p> </td>

                                    <td> <p><?php echo $ta_patient['Height']; ?></p> </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="cab_image">
                <img src="images/profile_pics/<?php echo $ta_patient['Profile_pic']; ?>" >
            </div>
        </div>


        <!-- table 1 -->
        <div class="search-details-header2" style="margin-top: 75px;">
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
                    <button name="file_oldest" value="1">Oldest</button>
                    <button name="file_recent" value="1">Recent</button>
                    <button name="file_viewAll">View all</button>
                </form>
            </div>

            <div class="upload-download-files">
                <form class="download-files" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <table class="download-file-table" style="width: 590px;">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Date</th>
                                <th>Action</th>
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

                                                <a download="<?php echo $report_file['file_name']; ?>" href="report_files/<?php echo $report_file['file_name']; ?>">Download</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } //end of foreach loop ?>
                            <?php } //end of if statement ?>
                        </tbody>
                    </table>

                    <div class="search-message" style="text-align:center;">
                        <?php echo htmlspecialchars($table_message4); ?>
                    </div>
                </form>
            </div>


        <!-- prescription -->
        <form class="ca-prescription" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="cap-header">
                <div class="cap-header-logo">
                    <img src="images/logo.png">
                </div>

                <div class="cap-header-text">
                    <div class="cap-header-text1">
                        <?php echo $ta_prescription['hname']; ?>
                    </div>

                    <div class="cap-header-text2">
                        <?php echo $ta_prescription['haddress']; ?>
                    </div>

                    <div class="cap-header-text3">
                        <?php echo 'Contact: ' . $ta_prescription['hcontact']; ?>
                    </div>
                </div>
            </div>

            <div class="cap-subject">
                <div class="cap-inner-subject1">
                    <p>Prescribed by:</p>
                    <input type="text" value="<?php echo $ta_prescription['dname']; ?>" readonly>
                </div>

                <div class="cap-inner-subject2">
                    <div class="cap-inner-subject3">
                        <p>Date:</p>
                        <input type="text" name="pres_date" value="<?php echo $today_str; ?>" readonly>
                    </div>

                    <div class="cap-inner-subject3">
                        <p>Valid till:</p>
                        <input type="date" name="pres_valid_till" value="<?php $valid_till; ?>" required>
                    </div>
                </div>
            </div>

            <div class="cap-disease">
                <p>Disease / Cause:</p>

                <textarea class="cap-textarea1" type="text" name="disease"
                placeholder="Cause or name of sickness/disease/trouble."><?php echo $disease; ?></textarea>
            </div>

            <div class="cap-disease">
                <p>Medicine / Test:</p>

                <textarea class="cap-textarea2" type="text" name="medication"
                placeholder="Prescribe required medicines or tests."><?php echo $medication; ?></textarea>
            </div>

            <button class="cap-send-button" type="submit" name="send_prescription" value="<?php echo $ta_prescription['Sl']; ?>">
                Send
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(255,255,255)" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </form>


        <!-- conclude appointment -->
        <form class="ca-conclude" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <button class="ca-conclude-button" name="ca_conclude" >
                Conclude Appointment
            </button>
        </form>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html>