<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alfa</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <form method="POST" action="alfa.php">
        <h2> Buscador </h2>
        <br>
        <label> Usuario </label>
        <input type="text" name="titulo">
        <br>
        <input type="submit" name="" value="buscar">

        <br>
        <br>

    </form>   

</body>

</html>

<?php
include "fgandhi.php";
include "Fcarlos.php"; 

//asigna la funcion a una variable
$gand = gandhi($search);
$fuentes = fuentes($search);

//combina dos o mas arrays repitiendo valores
$union = array_merge($gand, $fuentes);

//ordena los arreglos del menor al mayor
$marks = array();
foreach ($union as $key => $row)
{
    $marks[$key] = $row['precio'];  
}

array_multisort($marks, SORT_ASC, $union);

// recorre el arreglo retornado de la funcion
foreach ($union as $union) {
    
    ?>
    <table border="3">
        <tr>
            <th> <img src="<?php echo $union['logotipo'] ?>" alt=""> </th>
            <th> <?php echo $union['titulo'] ?></th>
            <th> <?php echo $union['autor'] ?></th>
            <th> <?php echo $union['formato'] ?></th>
            <th> <?php echo $union['precio'] ?></th>
            <th> <?php echo $union['metodoPago'] ?></th>
            <th> <?php echo $union['tiempoEnvio'] ?></th>
            <th> <?php echo $union['costoEnvio'] ?></th>
            <th> <a href="<?php echo $union['link']?>"><img src="<?php echo $union['img'] ?>" alt=""></a> </th>
        </tr>
    </table>
    <?php
}

?>

</body>

</html>