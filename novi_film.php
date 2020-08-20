<?php


session_start();

 $conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");
 
 $sqlZanr="select * from Zanr";                                                 
 $zanrovi= mysqli_query($conn, $sqlZanr);
 

 
 
 if(!isset($_SESSION["admin"])){                                                
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
              
        if(!isset($_POST['dodaj'])||                                            
               (empty($_POST['naziv']) ||
                empty($_POST['cena']) ||  
                empty($_POST['duzina'])||
                empty($_POST['zanr']))){
                   
        ?>
        <form name="novi_film" method="POST"
            action="novi_film.php">
            Naziv: <input type="text" name="naziv"
                value="<?php echo $naziv ?? ""; ?>"><br>      
            Duzina: <input type="number" name="duzina"
                value="<?php echo $duzina ?? ""; ?>"><br>
            Cena: <input type="number" name="cena"
                value="<?php echo $cena ?? ""; ?>"><br>
            Zanr: 
            <select name="zanr">
                <?php                                                           
                    while($zanr= mysqli_fetch_assoc($zanrovi)){
                        $idZanr=$zanr['SifZ'];
                        $nazivZanr=$zanr['Naziv'];

                        if($_POST['zanr']==$idZanr){
                            echo "<option value='$idZanr' selected>
                                $nazivZanr</option>";
                        }
                        else 
                            echo "<option value='$idZanr'>
                                $nazivZanr</option>";
                    }
                
                ?>
            </select>
       
            <br>
            <input type="submit" name="dodaj" value="dodaj">
        </form>     
        <?php
        }                                                                       
             if(isset($_POST['dodaj'])){                                        
            $naziv=$_POST['naziv'];                                             
            $cena=$_POST['cena'];
            $zanr=$_POST['zanr'];
            $duzina=$_POST['duzina'];

                if(empty($_POST['naziv']) ||                                    
                    empty($_POST['cena']) ||  
                    empty($_POST['duzina'])||
                    empty($_POST['zanr'])){
                       $poruka="Niste uneli sve podatke";
                   }else{                                                       
            
            $sqlMaxId="select max(SifF) from Film";                             
            $maxIdRez= mysqli_query($conn, $sqlMaxId);
            $id= (int) mysqli_fetch_row($maxIdRez)[0];

            $id++;

            $sqlInsert="INSERT INTO film(SifF, Naziv, 
                    Duzina,  Cena, SifZ) 
                    VALUES ($id,'$naziv',
                    $duzina,$cena,$zanr)";                                      

            $res= mysqli_query($conn, $sqlInsert);

            if($res){                                                           

                    $poruka="Film je dodat u bazu, odaberite kasetu:";


            }                                                                   
              else {
                $poruka="Film nije dodat u bazu, pokusajte ponovo";
            }

        
             echo $poruka ?? "";                                                
          }
          
          
          
         $sql1= "select sifk, k.duzina, sum(f.Duzina) as Stanje from kaseta k join sadrzi s using(SifK) join film f using(SifF)
                group by SifK;";                                                
         $sqlDuzinaKasete= mysqli_query($conn, $sql1);
         
        ?>                                                                      
        <form name="formaKaseta" method="POST" action="novi_film.php">          
            Raspolozive kasete: 
            <select name="kasete">
                <option value="" disabled selected>Izaberite kasetu</option>
                <?php                                                           
                while($duzinaKasete = mysqli_fetch_array($sqlDuzinaKasete)){    
                    $SifraKasete = $duzinaKasete[0];                            
                    $kasetaDuzina = $duzinaKasete[1];                           
                    $duzinaFilmova = $duzinaKasete[2];                          
                    $sql2="select sifK from kaseta k join sadrzi s using(SifK) join film f using(SifF)  
                           where k.duzina-$duzinaFilmova>$duzina and sifK = $SifraKasete";  
                    
      
                    $sqlKasete = mysqli_query($conn, $sql2);
                
                
                        while($kasete = mysqli_fetch_row($sqlKasete)){         
                            $sifK = $kasete[0];
                            echo "<option value='$sifK'>$sifK</option>";
                }
                }
                ?>
            </select>
            <br/>
            <input type="submit" name="ubaci" value="Dodaj film">
        </form>
        

        
        <?php
             
             }                                                                  
             
                if(isset ($_POST['ubaci'])){
                $sifKa = (int)$_POST['kasete'];                                 
                
                $sql="select max(SifF) from Film";                              
                $maxId= mysqli_query($conn, $sql);
                $idF= (int) mysqli_fetch_row($maxId)[0];
                
                 $sql = "insert into sadrzi values ($sifKa, $idF)";             
                 $resKas = mysqli_query($conn, $sql);
                 //var_dump($sql);
                 
                 if($resKas){
                     $poruka="Dodali ste film na kasetu";                       
                     echo $poruka ?? "";
                     echo "<br/><br/><a href='index.php'>Vrati se na pocetnu stranu</a>";
                 }else{
                     $poruka="Film nije dodat na kasetu";                       
                 }
                 
           }
             
          
        ?>
            
       
    </body>
</html>
