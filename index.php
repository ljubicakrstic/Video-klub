<!DOCTYPE html>
<?php

 session_start();                                                               
    if (!(isset($_SESSION["loggedIn"])                                          
            && $_SESSION["loggedIn"] == true)) {
        header("Location: log_in.php");
        
    } 
        $user = $_SESSION["username"];
       


$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

$sql = "select ime from clan where korisnickoime = '$user'";
$sqlImeClana = mysqli_query($conn, $sql);
$ImeClana = mysqli_fetch_row($sqlImeClana)[0];



$sql="select * from film";                                                      

$naziv = "";                                                                     
$min = "";
$max = "";
$zanrPret = "";

if(isset($_GET['pretraga'])) {                                                  
    $naziv = $_GET['naziv'];
    $sql = $sql . " where film.naziv like '%$naziv%'";
    
    if(isset($_GET['zanr'])){                                                    
        $zanrPret = $_GET['zanr'];
        $sql= $sql . " and sifz = $zanrPret";
    }
    
    $min = $_GET['min'];                                                        
    $max = $_GET['max'];
    
    
    
    if(!empty($min)) {                                                          
        $sql = $sql . " and ocena >= $min";
    }
    
    if(!empty($max)) {                                                          
        $sql = $sql . " and ocena <= $max";
    }
    
   
} if(isset($_GET['zanro'])){ 
$zanr1 = $_GET['zanro']; 
$sql = $sql . " where sifz = $zanr1";
}

$filmovi=mysqli_query($conn, $sql); 

$sql = "select * from zanr"; 
$zanrovi = mysqli_query($conn, $sql);

$sql = "select * from zanr"; 
$zanroviLink = mysqli_query($conn, $sql);

$user = $_SESSION['username']; 
$sql = "select sifC from clan where korisnickoime = '$user'";
$sqlKor= mysqli_query($conn, $sql);
$Kor = mysqli_fetch_array($sqlKor)[0]; 


$sql= "select sifK, Dana, Naziv, sifP from pozajmica join film using(siff)
       where sifc=$Kor"; 
$sqlPozajmice= mysqli_query($conn, $sql);





?>


<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <div id="header">
            <a href="index.php" id="naslov">Video klub</a>
                                                             
                    <form name="logOutForm" action="logout.php" method="POST">
                    <input type="submit" name="logout" value="Log out" id="logout">
                    </form>
        </div>
        <div id="meni">
            <ul id="lista">
                <li><a href="index.php?trazi=1">Pretraga</a></li>
                <li><a href="index.php?sifC=<?php echo $Kor ?>">Moje pozajmice</a></li> 
                <li><a href="index.php?filmovi=1">Svi filmovi</a></li>
                <?php                                                                      
                    if(is_null($_SESSION["admin"])==FALSE){       
                    echo "<li><a href='novi_film.php'>Dodaj film</a></li>";
                    echo "<li><a href='film_na_kasetu.php'>Dodaj film na kasetu</a></li>";
            }
            ?>
            </ul>
        </div>
        <div id="levo">
        <?php if(isset($_GET['trazi'])){ ?>
        
            <form name="pretraga" method="GET" action="index.php">
            <table>
                <tr>
                    <td>Naziv: </td> 
                <td><input type="text" name="naziv" 
                           value="<?php echo $naziv ?>"></td>
                </tr>
                <tr>
                    <td>Min ocena:</td>
                <td><input type="number" name="min"
                           value="<?php echo $min ?>"></td>
                </tr>
                <tr>
                    <td>Max ocena:</td>
                <td><input type="number" name="max"
                           value="<?php echo $max ?>"></td>
                </tr>
                <tr>
                    <td>Zanr: </td>
                <td><select name="zanr">
                <option value="" disabled selected>Izaberite zanr</option>
                <?php                                                            
                while($zanr = mysqli_fetch_assoc($zanrovi)){
                    $idZanr = $zanr['SifZ'];
                    $nazivZanr = $zanr['Naziv'];
                    
                    echo "<option value='$idZanr'>$nazivZanr</option>";
                }
                ?>
                
                    </select></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="pretraga"
                                       value="Pretraga"></td>
            </tr>
            </table>
        </form>
            <?php }?>
        </div> 
        
        <div id="centar"> 
        <?php
        if(isset($_GET['sifC'])){                                               
            if(mysqli_num_rows($sqlPozajmice)==0){ 
                echo "Nemate pozajmice";
            }else{ 
            echo "<h2>Vase pozajmice:</h2>";
            while($Pozajmice = mysqli_fetch_array($sqlPozajmice)){
                
                echo "<h3>Naziv filma: ".$Pozajmice[2]."</h3>"."Sifra kasete: ".$Pozajmice[0]."<br/> Broj dana: ".$Pozajmice[1]."<br/> <a href='index.php?sifP=$Pozajmice[3]'>Vrati</a>";
            }
          }
        }else if(isset ($_GET['filmovi'])||                                     
                 isset($_GET['pretraga'])||                                     
                 isset($_GET['zanro'])){                                        
                 if(mysqli_num_rows($filmovi)==0){                              
                     echo 'Nema filmova u trazenim kategorijama';
                 }else{                                                         
                    while($row = mysqli_fetch_array($filmovi)){
                    $id=$row[0]; //$id=$row['SifF'];
                    echo $row[1]." ".$row[3]
                    ." <a href='film_info.php?id=$id'>INFO</a> "
                    ."<br>";                                                    
                    }
                 }
            }else if(isset($_GET['sifP'])){                                     
                     $sifP= $_GET['sifP'];                                      
                    $sql="delete from pozajmica
                          where sifP=$sifP";                                    
                    $sqlVrati = mysqli_query($conn, $sql);
                    if($sqlVrati){                                              
                        $poruka="Vratili ste film";
                    }else {                                                     
                    $poruka="Niste uspesno vratili film";
    
                    }
}else{                                                                          
                echo '<h1>Dobro dosli, '.$ImeClana.'</h1>';
            }
            
            echo $poruka ?? "";
        ?>
        </div>
        <div id="zanrovi">
            <?php                                                               
            
           
         while($zanrLink = mysqli_fetch_assoc($zanroviLink)){
                    $idZanrLink = $zanrLink['SifZ'];
                    $nazivZanrLink = $zanrLink['Naziv'];
                    
                    echo "<a href='index.php?zanro=$idZanrLink'>$nazivZanrLink</a>"."<br/>";       
                }  
                ?>
        </div>
        
    </body>
</html>
