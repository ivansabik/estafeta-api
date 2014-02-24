<?php
include_once 'simple_html_dom.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
    'idioma' => 'es',
    'dispatch' => 'doRastreoInternet',
    'tipoGuia' => 'REFERENCE',
    'guias' => '3563581975'
        //"guias" => '1872996868'
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);

$html = new simple_html_dom();
$html->load($output);
?>
<h1>Nodos text</h1>
<?php
$i = 0;
$searchKey = 'text';
foreach ($html->find($searchKey) as $txt) {
    if (trim(str_replace('&nbsp;', '', $html->find($searchKey, $i)->plaintext)) != '') {
        echo($i . ' --> ' . $html->find($searchKey, $i)->plaintext . '<br/>');
    }
    $i++;
}
?>

<h1>Nodos img</h1>
<?php
$i = 0;
$searchKey = 'img';
foreach ($html->find($searchKey) as $img) {
    echo($i . ' --> ' . $html->find($searchKey, $i)->src . '<br/>');
    $i++;
}
