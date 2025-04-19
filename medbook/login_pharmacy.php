<?php
    $prompt = 'Email / License no. / ID:';

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
            $sql = "SELECT password, license_no, u_id FROM pharmacy 
                    WHERE Email = '$user'
                        OR license_no = '$user'
                        OR u_id = '$user'
                        LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $account = mysqli_fetch_assoc($result);

            if(! $account){ //when $account is empty
                $popup = true;
                $popup_message = "No account found.";
            }

            else if($account['password'] != $password){ // wrong password
                $popup = true;
                $popup_message = "Wrong Password.";
            }

            else{ // logging in
                session_start();
                $_SESSION['user'] = $account['u_id'];
                $_SESSION['user_type'] = 'pharmacy';
                
                header("Location: account_pharmacy.php"); 
            }
            mysqli_free_result($result);
        }
    }


    //___________________________________________________________________________________
    //signup button
    if(isset($_POST['signup']))
    {
        header('Location: signup_pharmacy.php');
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