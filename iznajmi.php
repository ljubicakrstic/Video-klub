<?php

session_start();

$korIme = $_SESSION["username"];                                                

$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

$sql = "select sifC from clan
       where korisnickoime = '$korIme'";                                        
$sqlKorisnik = mysqli_query($conn, $sql);
$Korisnik = mysqli_fetch_array($sqlKorisnik);
$sifC = $Korisnik[0];


$sql = "select max(SifP) from pozajmica";                                       
$sqlPozajmica = mysqli_query($conn, $sql);
$SifP = (int)mysqli_fetch_row($sqlPozajmica)[0];
$SifP++;

$sifFi = $_GET['idFilm']??"";                                                   
$sifKa = $_GET['idKas']??"";

if(isset($_POST['iznajmi'])){                                                   
    
    if(empty($_POST['brDana'])){                                                
        $poruka = "Morate uneti broj dana!";
    }else{
        
    $brDana = $_POST['brDana'];                                                 
    $sifF = $_POST['sifF'];                                                     
    $sifK = $_POST['sifK'];                                                     
    
    $sql = "INSERT INTO pozajmica values($SifP, $sifK, $sifF, $sifC, $brDana)";  
    $sqlIznajmi = mysqli_query($conn, $sql);
    
    if($sqlIznajmi){                                                            
        $poruka = "Uspesno ste iznajmili film <br/><br/><a href='index.php'>Vrati se na pocetnu stranu</a>";
    }else {                                                                     
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
        }echo $poruka??"";                                                      
        ?>
        <br/><br/>
        
        <br/>
        <form name="logOutForm" action="logout.php" method="POST">
            <input type="submit" name="logout" value="Log out">                 
            
        </form>
        
    </body>
</html>
