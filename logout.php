<?php

    session_start();                                                            //ponistavam sve u sesiji i vracam se na index
    session_destroy();
    header("Location: index.php");
    exit();
?>

