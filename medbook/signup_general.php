<?php
    $prompt="Patient";

    $user_nid = $name = $dob = $gender = '';

    $user_contact = $user_email = $user_password1 = $user_password2 = $user_address = '';
    $file_name = $temp_name = $folder = '';
    $error2 = ['user_contact'=>'', 'user_email'=>'', 'user_password'=>''];

    $blood_type = $weight = $height = '';
    $religion = $marital_status = $profession = '';

    $ins_provider = $ins_id = $ins_expire_date = '';

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
        $user_address = $_POST['user_address'];

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
        $blood_type = $_POST['blood_type'];
        $weight = $_POST['weight'];
        $height = $_POST['height'];
        $religion = $_POST['religion'];
        $marital_status = $_POST['marital_status'];
        $profession = $_POST['profession'];

        //form4 inputs
        $ins_provider = $_POST['ins_provider'];

        $ins_id = $_POST['ins_id'];

        $ins_expire_date = $_POST['ins_expire_date'];
        
        if(! array_filter($error2))
        { 
            //connection to the database
            include('db_connect.php');

            //filtering the data
            $name =  mysqli_real_escape_string($conn, $name);
            $height =  mysqli_real_escape_string($conn, $height);
            $profession =  mysqli_real_escape_string($conn, $profession);
            $user_password1 =  mysqli_real_escape_string($conn, $user_password1);
            $file_name = mysqli_real_escape_string($conn, $file_name);

            // Inserting data into the database
            $sql = "INSERT INTO patient(NID, Name, DOB, Gender, 
                                        Contact_number, Email, Password, Profile_pic, Address, 
                                        Blood_type, Weight, Height, Religion, Marital_status, Profession, 
                                        Ins_provider, Ins_id, Ins_expiry)
                    VALUES('$user_nid', '$name', '$dob', '$gender', 
                            '$user_contact', '$user_email', '$user_password1', '$file_name', '$user_address',
                            '$blood_type', '$weight', '$height', '$religion', '$marital_status', '$profession',
                            '$ins_provider', '$ins_id', '$ins_expire_date')";

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
                        <p>*Blood Type</p>
                        <select class="user-info" name="blood_type" >
                            <option value="unknown">unknown</option>
                            <option value="O-">O-</option>
                            <option value="O+">O+</option>
                            <option value="A-">A-</option>
                            <option value="A+">A+</option>
                            <option value="B-">B-</option>
                            <option value="B+">B+</option>
                            <option value="AB-">AB-</option>
                            <option value="AB+">AB+</option>
                        </select>
                    </div>
                    
                    <div class="medical-info">
                        <p>Weight (approximate)</p>
                        <input class="user-info" type="number" name="weight" placeholder="In Kilogram(kg)" value="<?php echo htmlspecialchars($weight); ?>" >
                    </div>

                    <div class="medical-info">
                        <p>Height (approximate)</p>
                        <input class="user-info" type="text" name="height" placeholder="In feet-inches (i.e. 5'10'')" pattern="[0-9]{1}'[0-9]{1,2}''" value="<?php echo htmlspecialchars($height); ?>" >
                    </div>
                </div>
                
                <div class="medical-info-container">
                    <div class="medical-info">
                        <p>Religion</p>
                        <select class="user-info" name="religion" >
                            <option value="Others">Others</option>
                            <option value="Islam">Islam</option>
                            <option value="Hinduism">Hinduism</option>
                            <option value="Christianity">Christianity</option>
                            <option value="Buddhism">Buddhism</option>
                        </select>
                    </div>
                    
                    <div class="medical-info">
                        <p>Marital Status</p>
                        <select class="user-info" name="marital_status" >
                            <option value="Prefer not to say.">Prefer not to say.</option>
                            <option value="Married">Married</option>
                            <option value="Unmarried">Unmarried</option>
                        </select>
                    </div>

                    <div class="medical-info">
                        <p>Profession</p>
                        <input class="user-info" type="text" name="profession" value="<?php echo htmlspecialchars($profession); ?>" >
                    </div>
                </div>

                <p>Present Address</p>
                <input class="user-info" type="text" name="user_address" value="<?php echo htmlspecialchars($user_address); ?>" >
            </div>

            <div class="signup-form4">
                <div class="insurance-info-container">
                    <div class="insurance-info">
                        <p>Insurance Provider</p>
                        <input class="user-info" type="text" name="ins_provider" value="<?php echo htmlspecialchars($ins_provider); ?>" >
                    </div>

                    <div class="insurance-info">
                        <p>Insurance ID</p>
                        <input class="user-info" type="number" name="ins_id" placeholder="Ins. membership ID" value="<?php echo htmlspecialchars($ins_id); ?>" >
                    </div>

                    <div class="insurance-info">
                        <p>Ins. Expiry Date</p>
                        <input class="user-info" type="date" name="ins_expire_date" placeholder="Ins. membership ID" value="<?php echo htmlspecialchars($ins_expire_date); ?>" >
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