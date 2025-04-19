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

    //______________________________________________________________
    // Dept. table

    $dept = array();
    $search_message1 = "No results";
    $searchValue1 = '';
    
    $sql = "SELECT W.Department, D.Name, W.Working_days, W.Visit_hours, W.Sl
            FROM works_at AS W, doctor AS D
            WHERE (W.Doctor_nid=D.NID)
                AND (W.Hospital_license='$user')
            ORDER BY W.Department ASC";
    $result = mysqli_query($conn, $sql);
    $dept = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_free_result($result);

    if(! empty($dept))
        $search_message1 = '';

    //________________________________________________________________
    // Search from the table

    if(isset($_POST['search_details']))
    {  
        $searchValue1 = $_POST['searched_details']; 

        $sql = "SELECT W.Department, D.Name, W.Working_days, W.Visit_hours, W.Sl
                FROM works_at AS W, doctor AS D
                WHERE (W.Doctor_nid=D.NID)
                    AND (W.Department Like '%$searchValue1%'
                        OR D.Name Like '%$searchValue1%')
                    AND (W.Hospital_license='$user')
                ORDER BY W.Department ASC";
        $result = mysqli_query($conn, $sql);
        $dept = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(! array_filter($dept))
            $search_message1 = "Not found.";
        else
            $search_message1 = '';

        mysqli_free_result($result);
    }

    //____________________________________________________________
    //Remove from the table

    if(isset($_POST['remove_dept_info']))
    {
        $remove_dept = $_POST['remove_dept_info'];

        $sql = "DELETE FROM works_at
                WHERE Sl='$remove_dept'";
        mysqli_query($conn, $sql);

        header('Location: details_hospital.php');
    }

    //_______________________________________________________________
    // Add dept info form

    $dept_doctor = $dept_doctor_nid = $dept_name = '';
    $visit_fee = $visit_days = $visit_hours = '';

    if(isset($_POST['update_dept']))
    {
        $dept_doctor = $_POST['dept_doctor'];
        $dept_doctor_nid=  $_POST['dept_doctor_nid'];
        $dept_name =  $_POST['dept_name'];
        $visit_fee = $_POST['visit_fee'];
        $visit_days =  $_POST['visit_days'];
        $visit_hours =  $_POST['visit_hours'];

        //filtering the data before inserting them into the db
        $dept_doctor = mysqli_real_escape_string($conn, $dept_doctor);
        $dept_doctor_nid =  mysqli_real_escape_string($conn, $dept_doctor_nid);
        $dept_name = mysqli_real_escape_string($conn, $dept_name);
        $visit_days = mysqli_real_escape_string($conn, $visit_days);
        $visit_hours = mysqli_real_escape_string($conn, $visit_hours);
        $visit_fee = mysqli_real_escape_string($conn, $visit_fee);

        // Inserting data into the database
            $sql = "INSERT INTO works_at(Hospital_license, Doctor_nid, Department,
                                        Working_days, Visit_hours, Visit_fee)
                    VALUES('$user', '$dept_doctor_nid', '$dept_name',
                            '$visit_days', '$visit_hours', '$visit_fee')";

            // Save to db and check
            if(mysqli_query($conn, $sql)) {
                header('Location: details_hospital.php');
            }     
    }
?>



<html>
    <?php include('account/headerAccount.php'); ?> 

    <section class="hospital-details-container">
        <div class="hospital-details">
            <div class="search-details-header">
                <div class="search-header-title">
                    Departments'
                    <span style="color:rgb(3, 178, 3); font-weight:500;">Information:</span>
                </div>

                <form class="search-bar1" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" placeholder="Search Department or Doctor" name="searched_details" value="<?php echo htmlspecialchars($searchValue1); ?>" >

                    <button class="search-bar-button" type="submit" name="search_details">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="dept-details-table-wrapper">
                <table class="dept-details-table">
                    <thead>
                        <tr>
                            <th>Dept.</th>
                            <th>Doctor</th>
                            <th>Sits on</th>
                            <th>Visit hours</th>
                            <th>Action</th>
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

                                    <td>
                                        <p><?php echo $each_depts['Working_days']; ?></p>
                                    </td>

                                    <td>
                                        <p><?php echo $each_depts['Visit_hours']; ?></p>
                                    </td>

                                    <td>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                            <button class="remove-dept-details" title="Remove Information" type="submit" name="remove_dept_info" value="<?php echo $each_depts['Sl']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="rgb(211, 10, 10)" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                                </svg>
                                                Remove
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

        <div class="add-dept-container">
            <div class="search-header-title">
                Add New
                <span style="color:rgb(3, 178, 3); font-weight:500;">Schedule:</span>
            </div>

            <form class="add-dept-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>*Doctor's Name</p>
                        <input class="user-info" type="text" name="dept_doctor" value="<?php echo htmlspecialchars($dept_doctor); ?>" required>
                    </div>
                    
                    <div class="contact-info">
                        <p>*Doctor's NID</p>
                        <input class="user-info" type="text" name="dept_doctor_nid" placeholder="XXXXXXXXXXXXXXXXX" value="<?php echo htmlspecialchars($dept_doctor_nid); ?>" required>
                    </div>
                </div>

                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>*Department</p>
                        <input class="user-info" type="text" name="dept_name" value="<?php echo htmlspecialchars($dept_name); ?>" required>
                    </div>
                    
                    <div class="contact-info">
                        <p>Visit fee (in TK.)</p>
                        <input class="user-info" type="text" name="visit_fee" placeholder="i.g. 500" value="<?php echo htmlspecialchars($visit_fee); ?>" >
                    </div>
                </div>

                <div class="contact-info-container" >
                    <div class="contact-info" style="width: 100%;">
                        <p>Visiting Days (respectively):</p>
                        <input class="user-info" type="text" name="visit_days" placeholder="Sunday, Monday, Wed..." value="<?php echo htmlspecialchars($visit_days); ?>" >
                    </div>
                </div>

                <div class="contact-info-container" >
                    <div class="contact-info" style="width: 100%;">
                        <p>Visit Hours (respectively):</p>
                        <input class="user-info" type="text" name="visit_hours" placeholder="9.00am-5.00pm, 11.00am-3.00pm" value="<?php echo htmlspecialchars($visit_hours); ?>" >
                    </div>
                </div>

                <button class="update-button" type="submit" name="update_dept" value="update_dept" style="margin-left: 15px">
                    Update
                </button>
            </form>
        </div>
    </section>

    <?php include('account/footerAccount.php'); ?> 
</html> 