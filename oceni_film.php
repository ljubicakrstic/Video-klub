<?php

session_start();
$idF = $_SESSION['id'];

$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
<?php

if(isset($_POST['dodajOcenu'])){                                                
    $SifF = $_POST['sifF'];
    $novaOcena = (int)$_POST['ocena'];
    
    $sqlDodajOcenu= "insert into ocene (siff, ocena)                            
                    values ($SifF, $novaOcena)";                                
    
    $sqlNadjiSrednjuVrednost ="SELECT AVG(Ocena) AS prosOcena FROM ocene        
                               WHERE siff=$SifF
                               GROUP BY siff;";                                 
    
        
        if(mysqli_query($conn, $sqlDodajOcenu)){                                
            $nadjiSrednjuVrednost= mysqli_query($conn, $sqlNadjiSrednjuVrednost);
            while($srednjaVrednost= mysqli_fetch_array($nadjiSrednjuVrednost)){ 
                $srednjaOcena =$srednjaVrednost[0];                             
                $sqlFinalOcena="update film set ocena = $srednjaOcena where siff = $SifF";  
                    if(mysqli_query($conn, $sqlFinalOcena)){      
                                $ocenaZaPrikaz = round($srednjaOcena, 2);                                                        
                        $poruka= "Hvala vam sto ste dodali ocenu<br/>Trenutna ocena za ovaj film je $ocenaZaPrikaz";
                        
                    }else{
                        $poruka = "Ocena nije dodata";                          
                    }
            }
        }
        
        ?>
        
        <a href="index.php">Vrati se na pocetnu stranu</a>
        <br/><br/>
            <?php
            echo $poruka ?? "";
            }else{                                                              
            ?>
        <form name="formaOceni" method="POST" action="oceni_film.php">
            <input type="hidden" name="sifF" value="<?php echo $idF ?>">
            Vasa ocena: <select name="ocena">
                <option value="" disabled selected>Odaberite ocenu</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <br/><br/>
            <input type="submit" name="dodajOcenu" value="Dodaj ocenu">
            
        </form>
        <?php
            }
        ?>
    </body>
</html>
