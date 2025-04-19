 
<?php
    $user = $password = '';
    $error = ['user'=>'', 'password'=>''];
    $logged_in = false;

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
        }
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
            <p> <?php echo $prompt;?> </p>
            <input class="user-id" autofocus type="text" name="user" placeholder="Email / Contact number / NID NO." value="<?php echo htmlspecialchars($user); ?>">
            <div class="input-warning"> <?php echo $error['user']; ?> </div>
            
            

            <p>Password</p>
            <input class="user-password" type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
            <div class="input-warning"> <?php echo $error['password']; ?> </div>

            <button class="login-button" type="submit" name="login" value="logged_in">
                login
            </button>
        </form>
    </section>

</html> 