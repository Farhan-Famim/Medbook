<?php
    include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    if($user_type=='patient'  ||  $user_type=='doctor'){
        $sql = "SELECT * FROM $user_type
                WHERE (NID = $user);" ;
    }
    else if($user_type='pharmacy'){
        $user = mysqli_real_escape_string($conn, $user);

        $sql = "SELECT * FROM $user_type
                WHERE (u_id='$user');" ;
    }
    
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    //__________________________________________________________________________
    // retriveve Prescription

    $target_pres = $_SESSION['target_prescription'];
    

    $sql3 = "SELECT d.Name AS dname, h.Name AS hname, 
                h.Address AS haddress, h.Contact_no AS hcontact, 
                pr.date, pr.valid_till, pr.disease, pr.medication, pr.Sl AS pres_Sl, a.Sl
            FROM doctor AS d, hospital AS h, appointments AS a, prescriptions AS pr
            WHERE (pr.Sl=$target_pres)
            AND (a.Sl=$target_pres)
            AND (d.NID=a.doctor_nid)
            AND (h.License_no=a.hospital_license);";
    $result = mysqli_query($conn, $sql3);
    $ta_prescription = mysqli_fetch_assoc($result); //ta: target appointment
        
    mysqli_free_result($result);



    //___________________________________________________________________
    // Search Users data from db
    $pharmacies = array();

    $searchValue1 = '';
    $search_message1 = 'No  Results.';

    if(isset($_POST['search_pharmacy']))
    { 
        $searchValue1 = $_POST['searched_pharmacy']; 

        $sql4 = "SELECT *
                FROM pharmacy
                WHERE (Name LIKE '%$searchValue1%')
                    OR (u_id = '$searchValue1')
                    OR (address LIKE '%$searchValue1%')
                LIMIT 10";
        $result = mysqli_query($conn, $sql4);
        $pharmacies = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($pharmacies))
            $search_message1 = "Not found.";
        else
            $search_message1 = '';

        mysqli_free_result($result);
    }


    //____________________________________________________________________________________________
    $popup_pr = false;
    $popup_message_pr = '';

    // sending the prescription (table: pharmacy_prescription)
    if(isset($_POST['send_pres']))
    {
        $target_pharmacy =  $_POST['send_pres'];
        $today = new DateTime(); //current date
        $today_str = $today->format('Y-m-d');
        $status = 'pending';

        $sql4 = "INSERT INTO pharmacy_prescription(pharmacy_id, pres_Sl, date, status)
                VALUES('$target_pharmacy', '$target_pres', '$today_str', '$status');";
        mysqli_query($conn, $sql4);

        $popup_pr = true;
        $popup_message_pr = "Prescription has been sent successfully.";
    }

?>




<html>
    <?php include('account/headerAccount.php'); ?> 

    <!-- display Popup -->
    <?php if($popup_pr == true){ ?>
        <div class="popup">
            <?php echo $popup_message_pr; ?>
        </div>
    <?php } ?>

    <section class="prescription-each-container">
        <div class="ca-prescription" >
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
                        <input type="text" name="pres_date" value="<?php echo $ta_prescription['date']; ?>" readonly>
                    </div>

                    <div class="cap-inner-subject3">
                        <p>Valid till:</p>
                        <input type="text" name="pres_date" value="<?php echo $ta_prescription['valid_till']; ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="cap-disease">
                <p>Disease / Cause:</p>

                <textarea class="cap-textarea3" readonly
                    type="text" name="disease"><?php echo $ta_prescription['disease']; ?></textarea>
            </div>

            <div class="cap-disease">
                <p>Medicine / Test:</p>

                <textarea class="cap-textarea4" readonly
                    type="text" name="medication"><?php echo $ta_prescription['medication']; ?></textarea>
            </div>
        </div>

        <!-- send prescription -->
         <?php if($user_type == 'patient') { ?>
            <div class="search-section-user" style="margin-top: 90px;">
                <div class="search-user-header">
                    <div class="search-header-title">
                        Forward to a
                        <span style="color:rgb(3, 178, 3); font-weight:500;">Pharmacy</span>
                    </div>

                    <form class="search-bar1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <input type="text" placeholder="Name / U.ID / Address" name="searched_pharmacy" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                        <button class="search-bar-button" type="submit" name="search_pharmacy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="search-pharmacy-table-wrapper">
                    <table class="search-pharmacy-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>U. ID</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(! empty($pharmacies)) { ?>
                                <?php foreach($pharmacies as $each_pharmacy){ ?>
                                    <tr>
                                        <td>
                                            <div class="searched-profile">
                                                <div class="searched-profile-pic">
                                                    <img src="images/profile_pics/<?php echo $each_pharmacy['Profile_pic']; ?>" />
                                                </div>

                                                <p class="searched-profile-name">
                                                    <?php echo $each_pharmacy['Name']; ?>
                                                </p>
                                            </div>
                                        </td>

                                        <td><?php echo $each_pharmacy['u_id'];?></td>

                                        <td><?php echo $each_pharmacy['contact_no'];?></td>

                                        <td>
                                            <p style="font-size:14px; font-weight:400; color: var(--secondary-font);">
                                                <?php echo $each_pharmacy['address'];?>
                                            </p>
                                        </td>

                                        <td>
                                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                                <button class="hospital-profile" title="send prescription" type="submit" name="send_pres" value="<?php echo $each_pharmacy['u_id']; ?>">
                                                    Send
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(255,255,255)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
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
        <?php } //end of if statement ?>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html>