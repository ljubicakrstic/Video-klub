<?php 

session_start();

    if(empty($_GET['id'])){                                                     //ako nisam kliknula na link 'info' id u getu nije setovan i vraca me na index
        header("Location: index.php");
    }
    $id=$_GET['id'];                                                            //bespotreban dupli posao, u svakom slucaju pamtim id filma i u sesiji                                               
    $_SESSION['id']=$id;
    
    $conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");
    
    $sql="select sifK, kaseta.Duzina                                          
         from kaseta join sadrzi using(SifK) join film using(SifF)
         where sifK not in(select sifK from pozajmica)
         and SifF = $id";                                                       //dohvatam sve kasete za taj film koje nisu iznajmljene
    
    $kasete= mysqli_query($conn, $sql);
    
    $sqlFilm="SELECT film.SifF, film.Naziv, Duzina, Ocena, 
        Cena, zanr.Naziv as Zanr 
        FROM film join zanr using(SifZ)
        where SifF=$id";
    $filmTabela= mysqli_query($conn, $sqlFilm);                                 //dohvatam sve podatke za taj film
    $film= mysqli_fetch_assoc($filmTabela);
    if($film==null){
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <?php                                                                   //ispisujem sve informacije o tom filmu
            echo "Naziv: ".$film['Naziv']."<br>";
            echo "Duzina: ".$film['Duzina']."<br>";
            echo "Ocena: ".$film['Ocena']."<br>";
            echo "Cena: ".$film['Cena']."<br>";
            echo "Zanr: ".$film['Zanr']."<br>";
            
            
            if(mysqli_num_rows($kasete)==0){                                    //ako nema nijedne kasete koja nije iznajmljena
                echo "Nema nijedne kasete";
            }
            else {                                                              //ako ima, ispisujem sifru kasete, duzinu kasete i link 'iznajmi' pored svake kasete. U get ugrradjujem id filma i id kasete
            echo "Kaseta, Duzina<br>";
            while($kaseta= mysqli_fetch_array($kasete)){
                echo $kaseta[0]." ".$kaseta[1]." <a href='iznajmi.php?idFilm=$id&idKas=$kaseta[0]'>Iznajmi</a><br/>";
            }
            }
        ?>
        <br/><br/>
        <a href="promeni_film.php?id=<?php echo $film['SifF'] ?>">              <!-- imam link za 'promeni' koji vodi na promeni film i pamti id u getu-->
            Promeni
        </a>
        
        <br/><br/>
        <a href="oceni_film.php?id=<?php echo $id ?>&oceni=1">Oceni film</a>    <!-- imam link za 'oceni film' koji vodi na oceni film stranicu-->
        
    </body>
</html>

