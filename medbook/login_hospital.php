<?php
    $prompt= 'Account ID';

    $type = $user = $password = '';
    $error = ['user'=>'', 'password'=>'', 'type'=>''];
    $logged_in = false;

    $popup = false;
    $popup_message = '';


    if(isset($_POST['login']))
    {
        $type = $_POST['infrastructure_type'];

        //checking user account input
        if(empty($_POST['user']))
            $error['user'] = 'User account is required.';
        else
            $user = $_POST['user'];

        //checking password input
        if(empty($_POST['password']))
            $error['password'] = 'password is required.';
        else
            $password = $_POST['password'];

        if(! array_filter($error))
        { 
            //connection to the database
            include('db_connect.php');

            //checking password
            $sql = "SELECT Password, License_no FROM hospital 
                    WHERE (Email = '$user' OR License_no = '$user')
                        AND (Type = '$type')
                    LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $account = mysqli_fetch_assoc($result);

            if(! $account){ //when $account is empty
                $popup = true;
                $popup_message = "No account found.";
            }

            else if($account['Password'] != $password){ // wrong password
                $popup = true;
                $popup_message = "Wrong Password.";
            }

            else{ // logging in
                session_start();
                $_SESSION['user'] = $account['License_no'];
                $_SESSION['user_type'] = 'hospital';

                header("Location: account_hospital.php");
            }

            mysqli_free_result($result);
        }
    }

    //___________________________________________________________________________________
    //signup button
    if(isset($_POST['signup']))
    {
        header('Location: signup_hospital.php');
    }
?>



<html>
    <?php include('login/headerLogin.php'); ?> 

    <!-- display Popup -->
    <?php if($popup == true){ ?>
        <div class="popup">
            <?php echo $popup_message; ?>
        </div>
    <?php } ?>

    <!-- login form -->
    <section class="patient-login-container">
        <div class="user-type-message">
            Select your user type from the navigation bar above.
        </div>

        <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <p> Infrastructure Type </p>
            <select class="user-id" name="infrastructure_type" >
                <option value="Hospital">Hospital</option>
                <option value="Medical">Medical</option>
                <option value="Clinic">Clinic</option>
            </select>
            <div class="input-warning"> <?php echo $error['type']; ?> </div>

            <p> <?php echo $prompt;?> </p>
            <input class="user-id" autofocus type="text" name="user" placeholder="Email (Official) / License NO." value="<?php echo htmlspecialchars($user); ?>">
            <div class="input-warning"> <?php echo $error['user']; ?> </div>

            <p>Password</p>
            <input class="user-password" type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
            <div class="input-warning"> <?php echo $error['password']; ?> </div>

            <button class="login-button" type="submit" name="login" value="logged_in">
                login
            </button>
        </form>
    </section>

    <form class="sign-up-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <p> Click 'Sign Up' to create account. </p>

            <button class="signup-button" type="submit" name="signup" value="signup">
                Sign Up
            </button>
    </form>

    <?php include('login/footerLogin.php'); ?> 
</html> 