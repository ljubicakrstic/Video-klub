<?php


//unapred se izvinjavam svima koji citaju ovo sto je html ispresecam php-om
//to sam radila da bi izgledalo malo lepse, tj da se neke stvari vide samo onda kad treba da se vide...


session_start();

 $conn=mysqli_connect("localhost","root","","videoklub1")
            or die("Konekcija neuspesna");
 
 $sqlZanr="select * from Zanr";                                                 //upit za sve zanrove
 $zanrovi= mysqli_query($conn, $sqlZanr);
 

 
 
 if(!isset($_SESSION["admin"])){                                                //ako nisi admin, ides nazad na index, zbogom mali hakeri
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
        <?php  
              
        if(!isset($_POST['dodaj'])||                                            //ako mi nista od ovoga nije setovano, ispisace se html forma koje je ispod
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
                <?php                                                           //dohvatam sve zanrove preko gornjeg sql upita za zanrove
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
        }                                                                       //zatvaram gornji if, i kazem ako jeste setovano 'dodaj', dakle ako sam kliknula na dugme dodaj
             if(isset($_POST['dodaj'])){                                        //ovaj IF otvaram ovde i zatvaram ga tek skrooooz dole, ispod html-a koji sledi
            $naziv=$_POST['naziv'];                                             //izvlacim podatke iz forme...
            $cena=$_POST['cena'];
            $zanr=$_POST['zanr'];
            $duzina=$_POST['duzina'];

                if(empty($_POST['naziv']) ||                                    //ako nesto od podataka nije unesto, pisem poruku
                    empty($_POST['cena']) ||  
                    empty($_POST['duzina'])||
                    empty($_POST['zanr'])){
                       $poruka="Niste uneli sve podatke";
                   }else{                                                       //ako jeste sve uneto, idemo na zabavan deo 
            
            $sqlMaxId="select max(SifF) from Film";                             //pravim id, jer ne znam da li je AI
            $maxIdRez= mysqli_query($conn, $sqlMaxId);
            $id= (int) mysqli_fetch_row($maxIdRez)[0];

            $id++;

            $sqlInsert="INSERT INTO film(SifF, Naziv, 
                    Duzina,  Cena, SifZ) 
                    VALUES ($id,'$naziv',
                    $duzina,$cena,$zanr)";                                      //pravim upit za dodavanje filma u tabelu film

            $res= mysqli_query($conn, $sqlInsert);

            if($res){                                                           //ako je film dodat u tabelu film, ovo je poruka

                    $poruka="Film je dodat u bazu, odaberite kasetu:";


            }                                                                   //u suprotnom, ovo je poruka
              else {
                $poruka="Film nije dodat u bazu, pokusajte ponovo";
            }

        
             echo $poruka ?? "";                                                
          }
          
          //dodala sam novi film u tabelu 'film', sad idem dalje
          
         $sql1= "select sifk, k.duzina, sum(f.Duzina) as Stanje from kaseta k join sadrzi s using(SifK) join film f using(SifF)
                group by SifK;";                                                //pravim sql upit za proveru stanja na svakoj kaseti (sql1). kolona 'stanje' je zbir duzine svih filmova koji se nalaze na jednoj kaseti
         $sqlDuzinaKasete= mysqli_query($conn, $sql1);
         
        ?>                                                                      
        <form name="formaKaseta" method="POST" action="novi_film.php">          <!-- citav ovaj html se ispisuje samo ako sam pritisnula "dodaj", dakle nakon sto sam dodala film biram na koju cu kasetu da ga stavim-->
            Raspolozive kasete: 
            <select name="kasete">
                <option value="" disabled selected>Izaberite kasetu</option>
                <?php                                                           //pravim padajucu listu na kojoj se pojavljuju samo kasete na kojima ima dovoljno slobodnog mesta
                while($duzinaKasete = mysqli_fetch_array($sqlDuzinaKasete)){    //prvo dohvatam podatke o svim kasetama na osnovu gornjeg sql-a (sifra kasete, duzina kasete i zbir duzine svih filmova koji se na njoj nalaze)
                    $SifraKasete = $duzinaKasete[0];                            //onda iz tog niza izvlacim jedno po jedno - ovo je sifra kasete
                    $kasetaDuzina = $duzinaKasete[1];                           //ovo je duzina kasete
                    $duzinaFilmova = $duzinaKasete[2];                          //ovo je duzina svih filmova koji se na njoj nalaze
                    $sql2="select sifK from kaseta k join sadrzi s using(SifK) join film f using(SifF)  
                           where k.duzina-$duzinaFilmova>$duzina and sifK = $SifraKasete";  //pa u okviru gornjeg whilea pravim novi sql upit (sql2)
                    // pa kazem daj mi sifre kaseta na kojima je duzina same kasete minus duzina svih filmova koji se na njoj nalaze (taj podatak sam dobila iz prethodnog sql1)
                    //veca od duzine koju sam upravo unela za novi filma
      
                    $sqlKasete = mysqli_query($conn, $sql2);
                
                
                        while($kasete = mysqli_fetch_row($sqlKasete)){          //ispisujem u padajucoj listi sve kasete koje odgovaraju kriterijumima
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
             
             }                                                                  //zatvaram gornji if i kazem ako je setovano "ubaci" ("ubaci" je dugme za dodavanje filma na kasetu)
             
                if(isset ($_POST['ubaci'])){
                $sifKa = (int)$_POST['kasete'];                                 //sifKa je sifra kasete koju smo odabrali u padajucoj listi
                
                $sql="select max(SifF) from Film";                              //sad pravim sql za dohvatanjemax Id filma (jer smo ubacili novi film, pa imamo novi max Id filma)
                $maxId= mysqli_query($conn, $sql);
                $idF= (int) mysqli_fetch_row($maxId)[0];
                
                 $sql = "insert into sadrzi values ($sifKa, $idF)";             //sql upit za ubacivanje te dve vrednosti u tabelu "sadrzi"
                 $resKas = mysqli_query($conn, $sql);
                 //var_dump($sql);
                 
                 if($resKas){
                     $poruka="Dodali ste film na kasetu";                       //ako je film dodat na kasetu ovo je poruka
                     echo $poruka ?? "";
                     echo "<br/><br/><a href='index.php'>Vrati se na pocetnu stranu</a>";
                 }else{
                     $poruka="Film nije dodat na kasetu";                       //ako nije dodat, ovo je poruka
                 }
                 
           }
             
          
        ?>
            
       
    </body>
</html>
