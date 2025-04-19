<?php
    $prompt="Hospital";

    $user_name = $license_no = $license_expiry = $license_type = $user_address = '';

    $user_contact = $user_email = $user_password1 = $user_password2 = '';
    $file_name = $temp_name = $folder = '';
    $error2 = ['user_contact'=>'', 'user_email'=>'', 'user_password'=>''];

    

    $form_saved = false;
    $popup = false;
    $popup_message = '';

    if(isset($_POST['signup']))
    {
        // form1 inputs
        $user_name = $_POST['user_name'];
        $license_no = $_POST['license_no'];
        $license_expiry = $_POST['license_expiry'];
        $license_type = $_POST['license_type']; 
        $user_address = $_POST['user_address'];


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
            $file_name = 'default_hospital.png';
        
        
        
        if(! array_filter($error2))
        { 
            //connection to the database
            include('db_connect.php');

            //filtering the data before inserting them into the db
            $user_name =  mysqli_real_escape_string($conn, $user_name);
            $license_no =  mysqli_real_escape_string($conn, $license_no);
            $user_address =  mysqli_real_escape_string($conn, $user_address);
            $user_password1 =  mysqli_real_escape_string($conn, $user_password1);
            $file_name =  mysqli_real_escape_string($conn, $file_name);

            // Inserting data into the database
            $sql = "INSERT INTO hospital(Name, License_no, License_expiry, Type,
                                        Contact_no, Email, Password, Profile_pic,
                                        Address)
                    VALUES('$user_name', '$license_no', '$license_expiry', '$license_type', 
                            '$user_contact', '$user_email', '$user_password1', '$file_name',
                            '$user_address')";

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
                <p>*Infrastructure's Name</p>
                <input class="user-info" autofocus type="text" name="user_name" placeholder="Registered name." value="<?php echo htmlspecialchars($user_name); ?>" required>

                <p>*License-Number</p>
                <input class="user-info" type="text" name="license_no" placeholder="XX-XXXX-XXXX" value="<?php echo htmlspecialchars($license_no); ?>" required>

                <div class="general-info-container">
                    <div class="general-info">
                        <p>License expiry date</p>
                        <input class="user-info" type="date" name="license_expiry" value="<?php echo htmlspecialchars($license_expiry); ?>" >
                    </div>
                    
                    <div class="general-info">
                        <p>Organization type</p>
                        <select class="user-info" name="license_type" >
                            <option value="Hospital">Hospital</option>
                            <option value="Medical">Medical</option>
                            <option value="Clinic">Clinic</option>
                        </select>
                    </div>
                </div>

                <p>Address/Location</p>
                <input class="user-info" type="text" name="user_address" value="<?php echo htmlspecialchars($user_address); ?>" >
            </div>

            <div class="signup-form2">
                <div class="contact-info-container">
                    <div class="contact-info">
                        <p>Contact Number (Official)</p>
                        <input class="user-info" type="text" name="user_contact" placeholder="11 digits (i.g. 01777777777)" value="<?php echo htmlspecialchars($user_contact); ?>" >
                        <div class="input-warning"> <?php echo $error2['user_contact']; ?> </div>
                    </div>
                    
                    <div class="contact-info">
                        <p>Email (Official)</p>
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
                        <p>Account Image</p>
                        <input class="user-info" type="file" name="image"  value="<?php echo htmlspecialchars($file_name); ?>" >
                    </div>

                    <div class="contact-info">
                        <p>Registered License (.pdf)</p>
                        <input class="user-info" type="file" name="pdf" >
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