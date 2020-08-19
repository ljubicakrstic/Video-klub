<!DOCTYPE html>
<?php

 session_start();                                                               //proveravam da li je korisnik ulogovan, ako nije saljem ga na log in stranicu
    if (!(isset($_SESSION["loggedIn"])                                          //ako se ulogovao, ovo ce biti setovano i true, to sam definisala na log in stranici
            && $_SESSION["loggedIn"] == true)) {
        header("Location: log_in.php");
        
    } 
        $user = $_SESSION["username"];
       


$conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");

$sql = "select ime from clan where korisnickoime = '$user'";
$sqlImeClana = mysqli_query($conn, $sql);
$ImeClana = mysqli_fetch_row($sqlImeClana)[0];



$sql="select * from film";                                                      //pravim osnovni upit sa sve filmove

$naziv = "";                                                                    //definisem promenljive da budu prazne na pocetku, bez ovoga pretraga ne sljaka kako treba (bar mi se tako cini, jebem li ga) 
$min = "";
$max = "";
$zanrPret = "";

if(isset($_GET['pretraga'])) {                                                  //ako sam kliknula pretraga, pamtim naziv iz geta i dodajem naziv u sql upit
    $naziv = $_GET['naziv'];
    $sql = $sql . " where film.naziv like '%$naziv%'";
    
    if(isset($_GET['zanr'])){                                                    //ako sam dodala i zanr u polju za pretragu, pamtim sifru zanra i dodajem i to u sql upit
        $zanrPret = $_GET['zanr'];
        $sql= $sql . " and sifz = $zanrPret";
    }
    
    $min = $_GET['min'];                                                        //pravim promenljive za min i max
    $max = $_GET['max'];
    
    
    
    if(!empty($min)) {                                                          //ako sam upisala minimum, dodajem i to u sql
        $sql = $sql . " and ocena >= $min";
    }
    
    if(!empty($max)) {                                                          //ako sam upisala i maximum, dodajem i to u sql
        $sql = $sql . " and ocena <= $max";
    }
    
   
} if(isset($_GET['zanro'])){ //ovo je za pretragu po zanru preko linkova, a ne treko padajuceg menija. Kad se klikne na link zanra, u get dodajem ?zanro= i tu pisem sifru zanra
$zanr1 = $_GET['zanro']; //pamtim sifru zanra i dodajem je samo na osnovni sql
$sql = $sql . " where sifz = $zanr1";
}

$filmovi=mysqli_query($conn, $sql); //izvrasavam upit za filmove, koji ce biti u skladu sa onim sto sam unela u polja za ptretragu, ili sa linkom zanra na koji sam kliknula

$sql = "select * from zanr"; //imam dva ista upita sa zanr jer na stranici imam dve pretrage po zanru (link i padajuci meni) pa sam zato pravila dva.. mozda ne mora, nemam pojma
$zanrovi = mysqli_query($conn, $sql);

$sql = "select * from zanr"; //dakle, isto sranje kao gore, samo se upit zove drugacije
$zanroviLink = mysqli_query($conn, $sql);

$user = $_SESSION['username']; //pravim promenljivu za usera koji je ulogovan
$sql = "select sifC from clan where korisnickoime = '$user'";
$sqlKor= mysqli_query($conn, $sql);
$Kor = mysqli_fetch_array($sqlKor)[0]; //dohvatam sifru clana koji je ulogovan, to mi je potrebno da bi mi izlistalo sve njegove pozajmice


$sql= "select sifK, Dana, Naziv, sifP from pozajmica join film using(siff)
       where sifc=$Kor"; //pravim upit za sve pozajmice tog clana
$sqlPozajmice= mysqli_query($conn, $sql);



//var_dump($_SESSION);

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
                <?php                                                                       //ako je ulogovani korisnik admin, onda mu se ispisuje jos jedan link, koji vodi na stranicu za dodavanje filmova novi film
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
                <?php                                                            //pozivam upit za zanrove (prvi od dva) da bih ih izlistala
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
        if(isset($_GET['sifC'])){                                               //ako mi je setovano sifc u getu, znaci da sam kliknula na 'moje pozajmice' pa pozivam sql upit za pozajmice tog clana
            if(mysqli_num_rows($sqlPozajmice)==0){ //ako nema pozajmica...
                echo "Nemate pozajmice";
            }else{ //ako ima pozajmica...
            echo "<h2>Vase pozajmice:</h2>";
            while($Pozajmice = mysqli_fetch_array($sqlPozajmice)){
                //pored svake pozajmice je link 'vrati' u koji u get ugradjujem sifru pozajmice
                echo "<h3>Naziv filma: ".$Pozajmice[2]."</h3>"."Sifra kasete: ".$Pozajmice[0]."<br/> Broj dana: ".$Pozajmice[1]."<br/> <a href='index.php?sifP=$Pozajmice[3]'>Vrati</a>";
            }
          }
        }else if(isset ($_GET['filmovi'])||                                     //ovo mi se izvrsava i u slusaju da sam kliknula link 'svi filmovi'...
                 isset($_GET['pretraga'])||                                      //i u slucaju da pretrazujem filmove preko forme
                 isset($_GET['zanro'])){                                        //i u slucaju da ih pretrazujem preko linka za zanrove
                 if(mysqli_num_rows($filmovi)==0){                              //ako nema filmova po zadatim kriterijumima..
                     echo 'Nema filmova u trazenim kategorijama';
                 }else{                                                         //ako ih ima...
                    while($row = mysqli_fetch_array($filmovi)){
                    $id=$row[0]; //$id=$row['SifF'];
                    echo $row[1]." ".$row[3]
                    ." <a href='film_info.php?id=$id'>INFO</a> "
                    ."<br>";                                                    //info pored svakog filma, u get ugradjujem id filma, vodi na film info stranicu
                    }
                 }
            }else if(isset($_GET['sifP'])){                                     //ako je setovana sifra pozajmice u getu, znaci da sam kliknula 'vrati'
                     $sifP= $_GET['sifP'];                                      //pravim promenljivu sa sifrom pozajmice
                    $sql="delete from pozajmica
                          where sifP=$sifP";                                    //i novi sql upit za brisanje te pozajmice
                    $sqlVrati = mysqli_query($conn, $sql);
                    if($sqlVrati){                                              //ako je upit uspesno izvrsen...
                        $poruka="Vratili ste film";
                    }else {                                                     //i ako nije uspesno izvrsen
                    $poruka="Niste uspesno vratili film";
    
                    }
}else{                                                                          //ako nista od gore navdenog nije setovano, znaci da sam tek usla na stranicu pa mi na pocetku pise samo "Dobro dosli u video klub"
                echo '<h1>Dobro dosli, '.$ImeClana.'</h1>';
            }
            
            echo $poruka ?? "";
        ?>
        </div>
        <div id="zanrovi">
            <?php                                                               //ispisujem linkove sa zanrovima, pozivam drugi sql upit za zanrove
            
           
         while($zanrLink = mysqli_fetch_assoc($zanroviLink)){
                    $idZanrLink = $zanrLink['SifZ'];
                    $nazivZanrLink = $zanrLink['Naziv'];
                    
                    echo "<a href='index.php?zanro=$idZanrLink'>$nazivZanrLink</a>"."<br/>";       //u get ugradjujem sifru zanra, a ispisujem naziv zanra
                }  
                ?>
        </div>
        
    </body>
</html>
