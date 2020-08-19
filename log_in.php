<?php
session_start();

$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

if(isset($_POST['log'])){//ako sam pritisnula dugme log, pamtim podatke korisnika
    $username= $_POST['ime'];
    $pass= $_POST['pass'];
    
    //pravim sql upit za dohvatanje svih korisnika sa tim imenom i lozinkom
    
    $sql= "SELECT * FROM clan
           where korisnickoime = '$username'
           and lozinka = '$pass'";
    
    //izvrsavam upit i dohvatam sve redove koji odgovaraju podacima (fetch ne mora da se radi, jednostavnije je onako kako je Drazen objasnio, ali meni nije palo na pamet)
    
    $sqlKorisnik = mysqli_query($conn, $sql);
    $Korisnik = mysqli_fetch_row($sqlKorisnik);
    
    //proveravam da li ono sto sam dohvatila nije null, tj da li postoji bar jedan red
    if(is_null($Korisnik)==FALSE){
        $_SESSION["loggedIn"] = true;//upisujem u sesiju da je korisnik ulogovan
        $_SESSION["username"] = $username;
        $_SESSION["admin"] = $Korisnik[5]; //pamtim u seiji username i admin
        header("Location: index.php");//saljem ga na pocetnu stranicu
        exit();
    }else{
        echo 'Pogresni podaci!';//ako fetch nije vratio nista, tj ako jeste null
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
        // put your code here
        ?>
    </body>
</html>
