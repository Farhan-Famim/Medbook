<?php
    $prompt = 'Doctor\'s Account';

    $user = $password = '';
    $error = ['user'=>'', 'password'=>''];
    $logged_in = false;

    $popup = false;
    $popup_message = '';

    if(isset($_POST['login']))
    {
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
            $sql = "SELECT Password, NID FROM doctor 
                    WHERE Email = '$user'
                        OR Contact_number = '$user'
                        OR NID = '$user'
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
                $_SESSION['user'] = $account['NID'];
                $_SESSION['user_type'] = 'doctor';
                
                header("Location: account_doctor.php");
            }
            mysqli_free_result($result);
        }
    }


    //___________________________________________________________________________________
    //signup button
    if(isset($_POST['signup']))
    {
        header('Location: signup_doctor.php');
    }
?> 



<html>
    <?php include('login_general.php'); ?> 

    <form class="sign-up-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <p> Click 'Sign Up' to create account. </p>

            <button class="signup-button" type="submit" name="signup" value="signup">
                Sign Up
            </button>
    </form>

    <?php include('login/footerLogin.php'); ?> 
</html> 