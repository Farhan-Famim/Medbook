<?php
    $prompt="Doctor";

    $user_nid = $name = $dob = $gender = '';

    $user_contact = $user_email = $user_password1 = $user_password2 = '';
    $file_name = $temp_name = $folder = '';
    $error2 = ['user_contact'=>'', 'user_email'=>'', 'user_password'=>''];

    $license_type = $license_no = $license_expiry =  '';
    $hospital = $department = $specializaion = '';

    $form_saved = false;
    $popup = false;
    $popup_message = '';

    if(isset($_POST['signup']))
    {
        // form1 inputs
        $user_nid = $_POST['user_nid'];
        $name = $_POST['name'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];

        //form2 inputs
        //checking user contact number input
        if(strlen($_POST['user_contact'])!=11  ||  !ctype_digit($_POST['user_contact']))
            $error2['user_contact'] = 'Invalid phone number.';
        else if(strlen($_POST['user_contact'])==0)
            $error2['user_contact'] = '';
        else
            $user_contact = $_POST['user_contact'];

        $user_email = $_POST['user_email'];
        
        //checking user password
        if($_POST['user_password1'] != $_POST['user_password2'])
            $error2['user_password'] = 'Password must match.';
        else
            $user_password1 = $_POST['user_password1'];

        
        //setting Profile Pic
        $file_name = $_FILES['image']['name'];
        $temp_name = $_FILES['image']['tmp_name'];
        $folder = 'images/profile_pics/'.$file_name;
        //uploading profile pic in the folder
        move_uploaded_file($temp_name, $folder);
        //default profile pic
        if($file_name == '')
            $file_name = 'default_dp.jpg';

        //form3 inputs
        $license_type = $_POST['license_type'];
        $license_no = $_POST['license_no'];
        $license_expiry = $_POST['license_expiry'];
        $hospital = $_POST['hospital'];
        $department = $_POST['department'];
        $specializaion = $_POST['specialization'];

        
        if(! array_filter($error2))
        { 
            //connection to the database
            include('db_connect.php');

            //filtering the data before inserting them into the db
            $name =  mysqli_real_escape_string($conn, $name);
            $user_password1 =  mysqli_real_escape_string($conn, $user_password1);
            $license_type =  mysqli_real_escape_string($conn, $license_type);
            $hospital =  mysqli_real_escape_string($conn, $hospital);
            $specializaion =  mysqli_real_escape_string($conn, $specializaion);
            $file_name =  mysqli_real_escape_string($conn, $file_name);

            // Inserting data into the database
            $sql = "INSERT INTO doctor(NID, Name, DOB, Gender,
                                        Contact_number, Email, Password, Profile_pic,
                                        License_type, License_no, License_expiry,
                                        Hospital, Department, Specialization)
                    VALUES('$user_nid', '$name', '$dob', '$gender', 
                            '$user_contact', '$user_email', '$user_password1', '$file_name',
                            '$license_type', '$license_no', '$license_expiry',
                            '$hospital', '$department', '$specializaion')";

            // Save to db and check
            if(mysqli_query($conn, $sql)) {
                $form_saved = true;
                $popup = true;
                $popup_message = 'Signed up successfully. Now login.';
            }     

            else
                echo mysqli_error($conn);

            
        }
    }
?> 




<html>
    <?php include('signup/headerSignup.php'); ?> 

    <!-- display Popup -->
    <?php if($popup == true){ ?>
        <div class="popup">
            <?php echo $popup_message; ?>
        </div>
    <?php } ?>

    <section class="patient-signup-container">
        <div class="user-type-message">
            <p>
                Selcted user type: 
                <span style="color:var(--third-font); font-weight:500"> 
                    <?php echo $prompt; ?>
                 </span>
            </p>

            Select your user type from the navigation bar above.
        </div>

        <form class="signup-forms" method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="signup-form1">
                <p>*NID Number</p>
                <input class="user-info" autofocus type="text" name="user_nid" placeholder="NID / Passport / Birth Reg. NO." value="<?php echo htmlspecialchars($user_nid); ?>" required>

                <p>*Name</p>
                <input class="user-info" type="text" name="name" placeholder="Name used in birth registration." value="<?php echo htmlspecialchars($name); ?>" required>

                <div class="general-info-container">
                    <div class="general-info">
                        <p>*Date of Birth</p>
                        <input class="user-info" type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
                    </div>
                    
                    <div class="general-info">
                        <p>*Gender</p>
                        <select class="user-info" name="gender" >
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="signup-form2">
                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>Contact Number</p>
                        <input class="user-info" type="text" name="user_contact" placeholder="11 digits (i.g. 01777777777)" value="<?php echo htmlspecialchars($user_contact); ?>" >
                        <div class="input-warning"> <?php echo $error2['user_contact']; ?> </div>
                    </div>
                    
                    <div class="contact-info">
                        <p>Email</p>
                        <input class="user-info" type="email" name="user_email" placeholder="yourName@gamil.com" value="<?php echo htmlspecialchars($user_email); ?>" >
                        <div class="input-warning"> <?php echo $error2['user_email']; ?> </div>
                    </div>
                </div>

                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>*Set account Password</p>
                        <input class="user-info" type="password" name="user_password1" placeholder="Maximum 15 characters." value="<?php echo htmlspecialchars($user_password1); ?>" required>
                        <div class="input-warning"> <?php echo $error2['user_password']; ?> </div>
                    </div>
                    
                    <div class="contact-info">
                        <p>*Confirm Password</p>
                        <input class="user-info" type="password" name="user_password2" placeholder="Rewrite password." value="<?php echo htmlspecialchars($user_password2); ?>" required>
                        <div class="input-warning"> <?php echo $error2['user_password']; ?> </div>
                    </div>
                </div>

                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>Upload Profile Picture</p>
                        <input class="user-info" type="file" name="image"  value="<?php echo htmlspecialchars($file_name); ?>" >
                    </div>
                </div>
            </div>

            <div class="signup-form3">
                <div class="medical-info-container">
                    <div class="medical-info">
                        <p>*License type</p>
                        <input class="user-info" type="text" name="license_type" placeholder="Doctor of Medicine(MD)" value="<?php echo htmlspecialchars($license_type); ?>" required>
                    </div>

                    <div class="medical-info">
                        <p>*License No.</p>
                        <input class="user-info" type="text" name="license_no" placeholder="XX-XXXX-XX" value="<?php echo htmlspecialchars($license_no); ?>" required>
                    </div>

                    <div class="medical-info">
                        <p>*License Expiration</p>
                        <input class="user-info" type="date" name="license_expiry" value="<?php echo htmlspecialchars($license_expiry);?>" required>
                    </div>
                </div>
                
                <div class="medical-info-container">
                    <div class="medical-info">
                        <p>Primary Hospital/Clinic</p>
                        <input class="user-info" type="text" name="hospital" value="<?php echo htmlspecialchars($hospital); ?>" >
                    </div>
                    
                    <div class="medical-info">
                        <p>Department</p>
                        <input class="user-info" type="text" name="department" value="<?php echo htmlspecialchars($department); ?>" >
                    </div>

                    <div class="medical-info">
                        <p>Specialization</p>
                        <input class="user-info" type="text" name="specialization" value="<?php echo htmlspecialchars($specializaion);?>" >
                    </div>
                </div>
            </div>

            <button class="signup-button" type="submit" name="signup" value="signed">
                Sign Up
            </button>
        </form>
    </section>

    <?php include('signup/footerSignup.php'); ?> 
</html> 