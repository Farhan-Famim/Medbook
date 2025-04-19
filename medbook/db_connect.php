<?php
    //connect to Database
    $conn = mysqli_connect('localhost', 'root', '', 'medbook_db');
                        // (server name, server user id, password, database name)

    //checking the connection
    if(! $conn) // $conn will return true, if database is found
        echo mysqli_connect_error;
?>