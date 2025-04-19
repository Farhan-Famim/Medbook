<?php
     include('db_connect.php');

    session_start();
    $user = $_SESSION['user'];
    
    $user_type = $_SESSION['user_type'];

    // Getting data from the database
    $sql = "SELECT * FROM $user_type
                WHERE u_id = '$user'" ;
    $result = mysqli_query($conn, $sql);
    $targetUser = mysqli_fetch_assoc($result);
        
    mysqli_free_result($result);

    
    //_____________________________________________________________________________
    // retreiving prescriptions rows from the table 

    $search_message1 = '';

    $sql2 = "SELECT p.Name AS pname, p.NID, p.Contact_number AS pcontact, a.Sl, a.user_id, pr.Sl, 
                    pp.pharmacy_id, pp.pres_Sl AS pres_Sl, pp.date, pp.status, pp.Sl AS pp_Sl
            FROM patient AS p, appointments AS a, prescriptions AS pr, pharmacy_prescription AS pp
            WHERE(pp.pharmacy_id='$user')
            AND (pp.pres_Sl=pr.Sl)
            AND (pr.Sl=a.Sl)
            AND (a.user_id=p.NID)
            ORDER BY pp.Sl ASC;";
    $result = mysqli_query($conn, $sql2);
    $prescriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(empty($prescriptions))
        $search_message1 = 'No requests.';
        
    mysqli_free_result($result); 

    //__________________________________________________________________
    // search from the table

    $searchValue1 = '';

    if(isset($_POST['search_schedule']))
    {
        $searchValue1 = $_POST['searched_schedule'];

        $sql3 = "SELECT p.Name AS pname, p.NID, p.Contact_number AS pcontact, a.Sl, a.user_id, pr.Sl, 
                    pp.pharmacy_id, pp.pres_Sl AS pres_Sl, pp.date, pp.status, pp.Sl AS pp_Sl
                FROM patient AS p, appointments AS a, prescriptions AS pr, pharmacy_prescription AS pp
                WHERE(pp.pharmacy_id='$user')
                AND (pp.pres_Sl=pr.Sl)
                AND (pr.Sl=a.Sl)
                AND (a.user_id=p.NID)
                AND((p.Name LIKE '%$searchValue1%') 
                    OR (p.Contact_number LIKE '%$searchValue1%')
                    OR (pp.date LIKE '%$searchValue1%')
                    )
                ORDER BY pp.Sl ASC;";
        $result = mysqli_query($conn, $sql3);
        $prescriptions= mysqli_fetch_all($result, MYSQLI_ASSOC);

        if(empty($prescriptions))
            $search_message1 = 'Not found.';

        mysqli_free_result($result);
    }

    //______________________________________________________________________
    // Accept & Reject 

    $target_prescription = 101;

    //Accept button
    if(isset($_POST['view'])){
        // view Prescription
        $_SESSION['target_prescription'] = $_POST['view'];
        header('Location: prescription_each.php');
    }

    //Reject button
    if(isset($_POST['remove'])){
        $target_prescription = $_POST['remove']; 

        $sql4 = "DELETE FROM pharmacy_prescription
                WHERE pres_Sl=$target_prescription;"; 
        mysqli_query($conn, $sql4);  

        header('Location: account_pharmacy.php');
    }

    

    //______________________________________________________________
    //log out
    if(isset($_POST['logout'])){
        session_unset();
        session_destroy();

        header('Location: login_pharmacy.php');
        exit();
    }  

?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="pharmacy-account-container">
        <div class="pharmacy-uid">
            <div class="p-uid-1"> User ID: </div>

            <div class="p-uid-2"> <?php echo $targetUser['u_id'] ?> </div>
        </div>


        <div class="account-hospital-info">
            <div class="search-details-header2">
                <div class="search-header-title2">
                    Prescription
                    <span style="color:rgb(4, 71, 206); font-weight:500;">Requests</span>
                </div>

                <form class="search-bar-account" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Name/Contact/Date." name="searched_schedule" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button2" type="submit" name="search_schedule">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="hospital-account-table-wrapper">
                <table class="hospital-account-table" style="width: 600px;">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Customer Con.</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(! empty($prescriptions)) { ?>
                            <?php foreach($prescriptions as $each_prescription){ ?>
                                <tr>
                                    <td> <p><?php echo $each_prescription['pname']; ?></p> </td>

                                    <td> <p><?php echo $each_prescription['pcontact']; ?></p> </td>

                                    <td> <p><?php echo $each_prescription['date']; ?></p> </td>

                                    <form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <td>
                                            <div class="accept-reject-div" style="display:flex;  flex-direction:row;">
                                                <button class="accept-request-button" type="submit" name="view" value="<?php echo $each_prescription['pres_Sl']; ?>" >
                                                    View

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="rgb(3, 105, 3)" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </button>

                                                <button class="reject-request-button" type="submit" name="remove" value="<?php echo $each_prescription['pres_Sl']; ?>" >
                                                    Remove

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