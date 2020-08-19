<!DOCTYPE html>
<?php
session_start();
    
    $conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");
    
    if(!isset($_SESSION["loggedIn"]) && !isset($_SESSION['admin'])){
        header("Location:log_in.php");
    }
    echo 'Ulogovani ste kao administrator! <br><br>';
    echo 'Dobrodosli, admine ' .$_SESSION['osoba'];
      
    
  ?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>admin</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>

    <br><br>
    <form name="snimiForma" method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
    
    <select name="listaFilmova">
        <option>Izaberi film</option>
        <?php 
            $sqlListaFilmova = "SELECT SifF, Naziv, Duzina FROM film";
            
            $listaFilmova = mysqli_query($conn, $sqlListaFilmova)
                    or die('greska u upitu');
            
            while($red = mysqli_fetch_array($listaFilmova)){
                    $idFil = $red[0];
                    $nazivFilma = $red[1];
                    $duzinaFilma = (int)$red[2];
                  
                    echo "<option value=$idFil>$nazivFilma / $duzinaFilma min</option>";
            }
        ?>
    </select>
    <select name="listaKaseta"> 
        
        <option>Izaberi kasetu</option>
        <?php
        
        
            $sqlSlobodno = "SELECT kaseta.SifK, kaseta.duzina - sum(film.duzina)
                   FROM kaseta, film, sadrzi
                   where kaseta.SifK = sadrzi.SifK AND film.SifF = sadrzi.SifF
                   group by kaseta.SifK";
               
            $slobodnoTabela = mysqli_query($conn, $sqlSlobodno)
                     or die('greska u upitu');
            
             while($red = mysqli_fetch_array($slobodnoTabela)){
                 $idKas = $red[0];
                 $slobodno = (int)$red[1];
                
                 
                 echo "<option value=$idKas>$idKas / $slobodno min</option>";
             }
        
        ?>
    </select> 
    <br><br>
    
        <input type="submit" name="dugme" value="SNIMI">
    </form>
    
        <?php
            if(array_key_exists("dugme", $_POST)){
            $idKasete = $_POST['listaKaseta'];
            $idFilma = $_POST['listaFilmova'];
            
            $sqlNadjiSlobodno = "SELECT kaseta.duzina - sum(film.duzina)
                                FROM kaseta, film, sadrzi
                                where kaseta.SifK = sadrzi.SifK AND film.SifF = sadrzi.SifF
                                and kaseta.SifK=$idKasete
                                group by kaseta.SifK";
            $NadjiSlobodno = mysqli_query($conn, $sqlNadjiSlobodno);
            $Slobodno = mysqli_fetch_row($NadjiSlobodno)[0];
            
            
            $sqlNadjiDuzinu = "select duzina from film where siff=$idFilma";
            $NadjiDuzinu = mysqli_query($conn, $sqlNadjiDuzinu);
            $Duzina = mysqli_fetch_row($NadjiDuzinu)[0];
            
          
            $sqlSnimi = "INSERT INTO sadrzi (SifK, SifF) VALUES ($idKasete, $idFilma)";
            
         
            
            if($Slobodno >= $Duzina){
                $snimi = mysqli_query($conn, $sqlSnimi);
                if($snimi){
                    echo "<br><br>uspesno snimljen film " .$idFilma ." na kasetu " .$idKasete;
                }
            } else{
               echo "<br><br>neuspesno nasnimavanje filma";
            }
        }
        ?>
    <br><br>
    <a href="../index.php" target="_blank">Idi na pocetnu stranicu</a> <br><br>
    <a href="../logout.php">Izloguj se</a> <br><br>
    </body>
</html>