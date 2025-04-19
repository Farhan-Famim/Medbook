 
<?php
    $prompt = 'User Account';

    $user = $password = '';
    $error = ['user'=>'', 'password'=>''];
    $logged_in = false;

    $popup = false;
    $popup_message = '';

    $otp_show = false;
    $otp = $otp_message = '';

    //starting session
    session_start();


    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;



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
            $sql = "SELECT Password, NID, Email FROM patient 
                    WHERE Email = '$user'
                        OR Contact_number = '$user'
                        OR NID = '$user'
                        LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $account = mysqli_fetch_assoc($result);

            mysqli_free_result($result);

            if(! $account){ //when $account is empty
                $popup = true;
                $popup_message = "No account found.";
            }

            else if($account['Password'] != $password){ // wrong password
                $popup = true;
                $popup_message = "Wrong Password.";
            }

            else // sending OTP
            { 
                $email = $account['Email'];

                // Generate a 6-digit random number
                $otp_random = rand(100000, 999999);

                // Store OTP and account, so that it can be used later in the otp checking
                $_SESSION['otp'] = $otp_random;
                $_SESSION['account'] = $account;


                //Load Composer's autoloader
                require 'PHPMailer\PHPMailer.php';
                require 'PHPMailer\SMTP.php';
                require 'PHPMailer\Exception.php';

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $mail->Username   = 'farhanfamim2@gmail.com';                     //SMTP username
                    $mail->Password   = 'symo byrv umjp ukjj';                               //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                    //Recipients
                    $mail->setFrom('farhanfamim2@gmail.com', 'MEDBOOK');
                    $mail->addAddress($email, 'User');     //Add a recipient
                    

                    //Content
                    $mail->isHTML(true);                                  //Set email format to HTML
                    $mail->Subject = 'Login OTP.';
                    $mail->Body    = "Your OTP is: <b>$otp_random</b> \n\n";

                    $mail->send();
                    echo 'OTP has been sent';  

                    $otp_show = true;
                } 
                catch (Exception $e) {
                   // echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
                   echo "OTP could not be sent. \nCheck internet connection.";
                }
            }
        }
    }



    if(isset($_POST['otp_submit'])) // checking OTP
    {
            $otp = $_POST['otp']; 

            $otp = $_POST['otp'];
            $otp_random = $_SESSION['otp'];  // Retrieve OTP from session
            $account = $_SESSION['account']; // Retrieve account from session

            if($otp != $otp_random){ // wrong otp
                echo 'Wrong OTP. Try again.';
            }
            else{ // logging in
                $_SESSION['user'] = $account['NID'];
                $_SESSION['user_type'] = 'patient';
                        
                header("Location: account_patient.php");  
                exit();
            } 
    }
    


    //___________________________________________________________________________________
    //signup button
    if(isset($_POST['signup']))
    {
        header('Location: signup_general.php');
    }
?> 




<html>
    <?php include('login_general.php'); ?> 

    <?php if($otp_show==true) { ?>
        <form class="otp-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <p>Enter the OTP sent to your email:</p>
            <input type="text" name="otp" placeholder="6 digits" value="<?php echo $otp; ?>" >

            <button class="login-button" name="otp_submit" value="1">
                Submit
            </button>
        </form>

    <?php } ?>

    <form class="sign-up-div" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <p> Click 'Sign Up' to create account. </p>

            <button class="signup-button" type="submit" name="signup" value="signup">
                Sign Up
            </button>
    </form>

    <?php include('login/footerLogin.php'); ?> 
</html> 