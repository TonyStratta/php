<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba carlos f</title>
</head>

<body>

    <form method="POST" action="pruebas.php">
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
include "vendor/autoload.php";

use Goutte\Client;

$search = $_POST['titulo'];

$client = new Client();
$resultados = [];
$numeroPaginas = 0;

// remueve todos los espacios que sobran a los lados de un string
$search = trim($search);

// convierte una string a minusculas aunque tenga acentos
$search = mb_strtolower($search);


// elimina la acentuacion de las vocales del titulo
$table = array(
    'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',  'ç' => 'c', 'è' => 'e', 'é' => 'e',
    'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ò' => 'o', 'ó' => 'o',
    'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u', 'ú' => 'u', 'ü' => 'u'
);


$search = strtr($search, $table);

// elimina acentos y comas de la busqueda
$puntuacion = array(
    ',' => '', '.' => '', ';' => '', ':' => '', '"' => ''
);

$search = strtr($search, $puntuacion);


//reemplaza los espacios entre palabras con el caracter indicado
$busqueda = preg_replace('/\s+/', '+', $search);
$liga = "https://www.libreriacarlosfuentes.mx/es/catalogo?keywords=";
$url = $liga . $busqueda;


// calcula el numero de paginas
$pages = $client->request("GET", $url);
$pages->filter(".views-element-container ")->each(function ($node) use (&$numeroPaginas) {



    $npag = $node->filter(".view-catalogo");

    if ($npag->filter(".pager")->count() > 0) {

        $enlaceInc = $npag->filter(".pager .pager__items .pager__item--last a ")->attr("href");

        //encuentra la primer coincidencia y elimina el resto de la cadena a la izquierda
        $recorte = strchr($enlaceInc, "page=");


        //elimina la cadena indicada
        $numeroPaginas = trim($recorte, "page=");
    } else {
        $numeroPaginas = 0;
    }
});




for ($i = 0; $i < $numeroPaginas + 1; $i++) {


    $liga = "https://www.libreriacarlosfuentes.mx/es/catalogo?keywords=" . $busqueda . "&field_isbn=&page=" . $i;


    $cr = $client->request("GET", $liga);

    // extreae los valores de las etiquetas y los asigna a una variable
    $cr->filter(".view-content .views-row")->each(function ($node) use (&$search, &$resultados) {
        $autor = $node->filter(".views-field-field-autor")->text();
        $titulo = $node->filter(".views-field-title")->text();
        $imgSource = $node->filter(".views-field-field-imagen .field-content img")->attr("src");
        $domain = "https://www.libreriacarlosfuentes.mx";
        $img = $domain . $imgSource;
        $linkSource = $node->filter(".views-field-field-imagen .field-content a")->attr("href");
        $link = $domain . $linkSource;



        $metodoPago = "credito y debito";
        $tiempoEnvio = "2 a 5 dias Estafeta";
        $costoEnvio = "varia";
        $logotipo = "https://www.libreriacarlosfuentes.mx/themes/custom/fuentes_theme/logo.png";
        $formato = "fisico";




        //indentifica si un titulo tiene precio de oferta 
        $precio = 0;
        $listaPrecios = $node->filter(".views-field-price .views-field-price__number");

        if ($listaPrecios->filter(".price-with-discount")->count() > 0) {

            $precio = $listaPrecios->filter(".price-with-discount .price-product")->text();
        } else {
            $precio = $listaPrecios->filter(".price-without-discount")->text();
        }

        // elimina el simbolo de peso de los precios
        $precio = str_replace("$", "", $precio);
        $precio= str_replace(",","",$precio);
        $precio= floatval($precio);


        //convierte una string a minusculas aunque tenga acentos
        $titulotem = mb_strtolower($titulo);


        // elimina la acentuacion de las vocales del titulo
        $table = array(
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',  'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u', 'ú' => 'u', 'ü' => 'u'
        );


        $titulotem = strtr($titulotem, $table);

        // elimina los espacios de los lados del titulo
        $titulotem = trim($titulotem);

        // elimina los signos de puntuacion del titulo
        $puntuacion = array(
            ',' => '', '.' => '', ';' => '', ':' => '', '"' => ''
        );

        $titulotem = strtr($titulotem, $puntuacion);

        // divide el titulo en strings 
        $titulotem = explode(" ", $titulotem);
        $search = explode(" ", $search);

        // ordena un string alfabeticamente 
        sort($titulotem);
        sort($search);

        // convierte una array en un string
        $titulotem = implode(" ", $titulotem);
        $search = implode(" ", $search);


        // guarda en un arreglo los resultados que coincidan exactamente (case insensitive) con la busqueda del usuario y que cuenten con existencia
        if (strcasecmp($titulotem, $search) == 0) {

            // envia los valores a un arreglo asociativo
            array_push($resultados, [
                "titulo" => $titulo,
                "autor" => $autor,
                "formato" => $formato,
                "precio" => $precio,
                "link" => $link,
                "img" => $img,
                "metodoPago" => $metodoPago,
                "tiempoEnvio" => $tiempoEnvio,
                "costoEnvio" => $costoEnvio,
                "logotipo" => $logotipo,

            ]);
        }
    });
     // retorna la array con los resultados
     return $resultados;
}



?>

</body>

</html>