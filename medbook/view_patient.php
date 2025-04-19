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
    //retrieve view patient's info from db

    $view_user = $_SESSION['view_patient_nid'];

    // Getting data from the database
    $sql = "SELECT * FROM patient
                WHERE NID = $view_user" ;
    $result = mysqli_query($conn, $sql);
    $targetView = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //_________________________________________________________________
    //calculate age of view user

    $dob_date = new DateTime($targetView['DOB']); //create DateTime object from DOB
    $today = new DateTime(); //current date
    $age = $today->diff($dob_date)->y;  // Get the age in years

    //fixing weight
    $weight = '';
    if($targetView['Weight'] != 0)
        $weight = $targetView['Weight'];


    //___________________________________________________________________
    // prevoious MEDICATIONS table

    $table_message3 = 'No results';

    $sql4 = "SELECT p.NID, pr.medication, pr.disease, d.Name AS dname,
                    pr.date, pr.valid_till, pr.Sl AS pres_Sl, a.Sl
            FROM patient AS p, doctor AS d, hospital AS h, 
                prescriptions AS pr, appointments AS a
            WHERE (p.NID='$view_user')
            AND (a.user_id = '$view_user')
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
            WHERE (p.NID='$view_user')
            AND (a.user_id = '$view_user')
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


    //_____________________________________________________________________________
    // view Prescription

    if(isset($_POST['view_prescription']))
    {
        $_SESSION['target_prescription'] = $_POST['view_prescription'];
        header('Location: prescription_each.php');
    }
?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="view-user-container">
        <div class="banner-user-container">
            <div class="banner-user">
                <div class="view-user-dp">
                    <img src="images/profile_pics/<?php echo $targetView['Profile_pic']; ?>" />
                </div>

                <div class="banner-user-card">
                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Name
                        </div>

                        <div class="view-data" style="font-weight:bold;">
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
                            Blood Type
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Blood_type']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Weight (apprx.)
                        </div>

                        <div class="view-data">
                            <?php echo $weight; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachEven">
                        <div class="view-subj">
                            Height (apprx.)
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Height']; ?>
                        </div>
                    </div>

                    <div class="banner-user-card-eachOdd">
                        <div class="view-subj">
                            Contact
                        </div>

                        <div class="view-data">
                            <?php echo $targetView['Contact_number']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- table 1 -->
        <div class="search-details-header2" style="margin-top: 250px;">
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
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 