<?php
$conn = mysqli_connect("localhost", "root", "", "videoklub1")
            or die("Konekcija nije ostvarena");

if (!isset($_POST['promeni'])) {
    $id = $_GET["id"];
    
    $sql = "select * from film where siff=$id";

    $filmTabela = mysqli_query($conn, $sql);

    $film = mysqli_fetch_assoc($filmTabela);

    var_dump($film);

    $sql = "select * from zanr";
    $zanrovi = mysqli_query($conn, $sql);
} else {
    $id = $_POST['id'];
    $naziv = $_POST['naziv'];
    $duzina = $_POST['duzina'];
    $cena = $_POST['cena'];
    $ocena = $_POST['ocena'];
    $zanr = $_POST['zanr'];
    
    $sql = "update film "
            ."set naziv='$naziv', duzina=$duzina, cena=$cena, ocena=$ocena, sifz=$zanr "
            . "where siff=$id";
    
    var_dump($sql);
    $res = mysqli_query($conn, $sql);
    
    if ($res == true) {
        header("Location: film_info.php?id=$id");
        die();
    }
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <?php 
            echo $poruka ?? "";
        ?>
        <form name="promeni_film" method="POST"
            action="promeni_film.php">
            <input type="hidden" name="id" 
                   value="<?php echo $film['SifF'] ?? "" ?>">
            Naziv: <input type="text" name="naziv"
                value="<?php echo $film['Naziv'] ?? ""; ?>"><br>      
            Duzina: <input type="number" name="duzina"
                value="<?php echo $film['Duzina'] ?? ""; ?>"><br>
            Cena: <input type="number" name="cena"
                value="<?php echo $film['Cena'] ?? ""; ?>"><br>
            Ocena: <input type="number" name="ocena"
                value="<?php echo $film['Ocena'] ?? "" ?>"><br>
            Zanr: 
            <select name="zanr">
                <?php 
                    while($zanr = mysqli_fetch_assoc($zanrovi)){
                        $idZanr=$zanr['SifZ'];
                        $nazivZanr=$zanr['Naziv'];

                        if($film['SifZ'] == $idZanr){
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
            <input type="submit" name="promeni" value="Promeni">
        </form>
        
    </body>
</html>
