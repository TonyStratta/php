
<?php
include "vendor/autoload.php";
use Goutte\Client;

  // string que el usuario ingresa en el input para buscar un titulo
$search = $_POST['titulo'];

function gandhi($search)
{

    $client = new Client();
    $max = 0;
    $resultados = [];

    // remueve todos los espacios que sobran a los lados de un string
    $search = trim($search);

    // convierte una string a minusculas aunque tenga acentos
    $search= mb_strtolower($search);
               

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

     $search= strtr($search,$puntuacion);
    
    //reemplaza los espacios entre palabras con el simbolo +
    $busqueda = preg_replace('/\s+/', '+', $search);
    $liga = "https://www.gandhi.com.mx/catalogsearch/result/?q=";
    
    $url = $liga . $busqueda;


    // calcula el numero de resultados
    $pages = $client->request("GET", $url);
    $pages->filter(".results .toolbar-products .toolbar-amount .toolbar-number")->each(function ($node) use (&$max) {

        $cPag = $node->text();

        if ($cPag > $max) {
            $max = $cPag;
        }
    });


    // calcula el numero de paginas con resultados
    if ($max <= 20) {
        $totalPag = 1;
    } else if ($max > 20) {
        $operacion = $max / 20;

        if ($operacion % 20 == 0) {
            $totalPag = (int)$operacion;
        } else {

            $totalPag = ((int) $operacion) + 1;
        }
    }

    // ciclo para extraer resultados de cada pagina
    for ($i = 1; $i < $totalPag + 1; $i++) {


        $liga = "https://www.gandhi.com.mx/catalogsearch/result/index/?p=" . $i . "&q=" . $search;

        $cr = $client->request("GET", $liga);


        // extreae los valores de las etiquetas y los asigna a una variable
        $cr->filter(".prueba-25 .product-item .product-item-info")->each(function ($node) use (&$search, &$resultados) {
            $autor = $node->filter(".product-item-details .autor")->text();
            $titulo = $node->filter(".product-item-details .product-item-name")->text();
            $formato = $node->filter(".product-item-details .product-item-format")->text();
            $img = $node->filter('img')->attr("src");
            $link = $node->filter('a')->attr("href");
            

            $metodoPago = "paypal, mastercard, regalo";
            $tiempoEnvio= "2 a 5 dias";
            $costoEnvio= "gratis a mexico";
            $logotipo= "https://www.gandhi.com.mx/media/logo/stores/1/logo-home.png";


            //indentifica si un titulo tiene precio de oferta 
            $precio = 0;
            $listaPrecios = $node->filter(".product-item-details .price-box");

            if ($listaPrecios->filter(".old-price")->count() > 0) {

                $precio = $listaPrecios->filter(".special-price .price-wrapper ")->text();
            } else {
                $precio = $listaPrecios->filter(".price-final_price .price-container .price-wrapper")->text();
            }

            // elimina el simbolo de peso de los precios
            $precio= str_replace("$","",$precio);
            $precio= str_replace(",","",$precio);
            $precio= floatval($precio);

            // convierte una string a minusculas aunque tenga acentos
            $titulotem= mb_strtolower($titulo);

            // elimina acentos y comas del titulo
            $puntuacion = array(
               ',' => '', '.' => '', ';' => '', ':' => '', '"' => ''
            );

            $titulotem= strtr($titulotem,$puntuacion);

            // elimina la acentuacion de las vocales del titulo
            $table = array(
                'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',  'ç' => 'c', 'è' => 'e', 'é' => 'e',
                'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ò' => 'o', 'ó' => 'o',
                'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u', 'ú' => 'u', 'ü' => 'u'
            );
            
            $search = strtr($search, $table);
            $titulotem = strtr($titulotem, $table);

            //valida si el titulo esta en existencia
            $existencia = $node->filter(".product-item-details .actions-primary")->text();

            //str_starts_with($titulotem,$search)
            
            // guarda en un arreglo los resultados que coincidan exactamente (case insensitive) con la busqueda del usuario y que cuenten con existencia
            if (strcasecmp($search, $titulotem) == 0   && $existencia == "+ A mi bolsa") {
                
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
                    "costoEnvio"=> $costoEnvio,
                    "logotipo"=> $logotipo,

                ]);
            }
        });
    }

    // retorna la array con los resultados
    return $resultados;
}



?>
