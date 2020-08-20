<?php
session_start();

$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

if(isset($_POST['log'])){
    $username= $_POST['ime'];
    $pass= $_POST['pass'];
    
    
    $sql= "SELECT * FROM clan
           where korisnickoime = '$username'
           and lozinka = '$pass'";
    

    
    $sqlKorisnik = mysqli_query($conn, $sql);
    $Korisnik = mysqli_fetch_row($sqlKorisnik);
    
    
    if(is_null($Korisnik)==FALSE){
        $_SESSION["loggedIn"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["admin"] = $Korisnik[5]; 
        header("Location: index.php");
        exit();
    }else{
        echo 'Pogresni podaci!';
    }
    
}


?>



<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <form name="LogInForma" method="POST" action="log_in.php">
            <table>
                <tr>
                    <td>Korisnicko ime: </td>
                    <td><input type="text" name="ime" value="<?php echo isset($_POST['ime']) ? $_POST['ime'] : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Lozinka: </td>
                    <td><input type="password" name="pass"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="log" value="Log in"</td>
                </tr>
            </table>
           
        </form>
        
        
        <?php
        
        ?>
    </body>
</html>
