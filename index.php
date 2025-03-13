<?php

require __DIR__ . '/vendor/autoload.php';

use Plateformweb\Datatables\Datatable;

function dump($dump) 
{
    echo '<pre>'.print_r($dump, true).'</pre>';
}

$datatable = new Datatable('/datas.php');
$datatable
    ->addColumn('civility')
    ->addColumn('nom', [
        'export' => false,
    ])
    ->addColumn('prenom')
    ->addColumn('age', [
        'visible' => false,
    ])
    ->addExcelButton()
    ->addPdfButton()
    ->addCopyButton()
    ->addPrintButton()
    ->addColumnsVisibilityButton()
    ->addCollectionButton()
    ->setResponsive()
    ->setSelectable(['row'])
    ->setSelectableRows('multi')
;

echo <<<HTML
    <!doctype html>
    <html lang="fr">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>DEMO - Simplify server side with datatable</title>
        </head>
        <body>
            {$datatable}
        </body>
    </html>
HTML;
