<?php

session_start();

$korIme = $_SESSION["username"];                                                //izvlacim korisnicko ime iz sesije

$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

$sql = "select sifC from clan
       where korisnickoime = '$korIme'";                                        //da bih preko njega dosla do sifre clana
$sqlKorisnik = mysqli_query($conn, $sql);
$Korisnik = mysqli_fetch_array($sqlKorisnik);
$sifC = $Korisnik[0];


$sql = "select max(SifP) from pozajmica";                                       //pravim sifru pozajmice jer nije AI (mozda i jeste, nisam proveravala haha)
$sqlPozajmica = mysqli_query($conn, $sql);
$SifP = (int)mysqli_fetch_row($sqlPozajmica)[0];
$SifP++;

$sifFi = $_GET['idFilm']??"";                                                   //izvlacim sifru filma i sifru kasete iz geta 
$sifKa = $_GET['idKas']??"";

if(isset($_POST['iznajmi'])){                                                   //ako sam kliknula 'iznajmi'...
    
    if(empty($_POST['brDana'])){                                                //ako je broj dana prazan, ispisuje poruku da morate uneti broj dana duh
        $poruka = "Morate uneti broj dana!";
    }else{
        
    $brDana = $_POST['brDana'];                                                 //u suprotnom, pamtim broj dana i siff i sifk iz posta
    $sifF = $_POST['sifF'];                                                     // to vucem iz hidden polja, ne mogu direktno iz geta zato sto se url menja onog trenutka kad kliknem nesto
    $sifK = $_POST['sifK'];                                                     //a kad se promeni url, ode get. Znavi pamtim podatke iz geta, upisujem ih u hidden, i onda ih odatle izlacim preko posta
    
    $sql = "INSERT INTO pozajmica values($SifP, $sifK, $sifF, $sifC, $brDana)";  //sql upit za insert svih podataka koje sam skupila gore
    $sqlIznajmi = mysqli_query($conn, $sql);
    
    if($sqlIznajmi){                                                            //ako je upit izvrsen, poruka je ovo:
        $poruka = "Uspesno ste iznajmili film <br/><br/><a href='index.php'>Vrati se na pocetnu stranu</a>";
    }else {                                                                     //ako nije izvrsen, poruka je ovo
        $poruka = "Film nije iznajmljen";
    }
    
  }
}else{

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>   
        <form name="iznajmiForma" action="iznajmi.php" method="POST">
                   <input type="hidden" name="sifF" value="<?php echo $sifFi?>">
                   <input type="hidden" name="sifK" value="<?php echo $sifKa?>">
        Broj dana: <input type="number" name="brDana">
        <br/>
        <input type="submit" name="iznajmi" value="Iznajmi"> 
    </form>
        <?php
        }echo $poruka??"";                                                      //ispisujem poruku ako je setovana, ili nista ako nije
        ?>
        <br/><br/>
        
        <br/>
        <form name="logOutForm" action="logout.php" method="POST">
            <input type="submit" name="logout" value="Log out">                 <!-- dugme za logout-->
            
        </form>
        
    </body>
</html>
