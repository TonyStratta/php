<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    


<form method="POST" action="arreglos.php">

<input type="text" name="titulo" placeholder="titulo">
<br>
<input type="text" name="autor" placeholder="autor">
<br>
<input type="text" name="precio" placeholder="precio">
<br>
<input type="submit" value="agregar">
<br>


</form>


<?php



$precio= "1,829";
$precio= str_replace(",","",$precio);
$precio= intval($precio);

echo "este es el marica precio ". $precio. "<br>"; 


$mujeres = [];
array_push($mujeres, ['nombre' => "maria",'apellido' => "diaz",'edad' => "23"] );

$hombres = ['nombre' => "julian",'apellido' => "casablanca",'edad' => "34"];



//print_r($resultados);
$numeros= [];

for ($i=0; $i<10; $i++ )
{
   array_push($numeros, ['numero' => $i, 'segundo' => "heh" ]);  
}


$tamano= count($numeros);


for ($i=0; $i<$tamano; $i++)
{
    echo $numeros[$i]['numero']."<br>";
    echo $numeros[$i]['segundo']."<br>";

}

foreach ($numeros as $numeros)
{ 
echo $numeros['numero'].$numeros['segundo']."<br>";
}






?>


</body>
</html>





