<?php 

session_start();

    if(empty($_GET['id'])){                                                     
        header("Location: index.php");
    }
    $id=$_GET['id'];                                                                                                           
    $_SESSION['id']=$id;
    
    $conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");
    
    $sql="select sifK, kaseta.Duzina                                          
         from kaseta join sadrzi using(SifK) join film using(SifF)
         where sifK not in(select sifK from pozajmica)
         and SifF = $id";                                                       
    
    $kasete= mysqli_query($conn, $sql);
    
    $sqlFilm="SELECT film.SifF, film.Naziv, Duzina, Ocena, 
        Cena, zanr.Naziv as Zanr 
        FROM film join zanr using(SifZ)
        where SifF=$id";
    $filmTabela= mysqli_query($conn, $sqlFilm);                                 
    $film= mysqli_fetch_assoc($filmTabela);
    if($film==null){
        header("Location: index.php");
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
        <?php                                                                   
            echo "Naziv: ".$film['Naziv']."<br>";
            echo "Duzina: ".$film['Duzina']."<br>";
            echo "Ocena: ".$film['Ocena']."<br>";
            echo "Cena: ".$film['Cena']."<br>";
            echo "Zanr: ".$film['Zanr']."<br>";
            
            
            if(mysqli_num_rows($kasete)==0){                                    
                echo "Nema nijedne kasete";
            }
            else {                                                              
            echo "Kaseta, Duzina<br>";
            while($kaseta= mysqli_fetch_array($kasete)){
                echo $kaseta[0]." ".$kaseta[1]." <a href='iznajmi.php?idFilm=$id&idKas=$kaseta[0]'>Iznajmi</a><br/>";
            }
            }
        ?>
        <br/><br/>
        <a href="promeni_film.php?id=<?php echo $film['SifF'] ?>">              
            Promeni
        </a>
        
        <br/><br/>
        <a href="oceni_film.php?id=<?php echo $id ?>&oceni=1">Oceni film</a>    
        
    </body>
</html>

