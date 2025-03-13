<?php

require __DIR__ . '/vendor/autoload.php';

use Plateformweb\Datatables\ArrayAdapter;

$personnes = [];

$civilities = ['Monsieur', 'Madame'];
$noms = ['Dupont', 'Durand', 'Lemoine', 'Morel', 'Bernard', 'Robert', 'Richard', 'Petit', 'Garcia', 'Martinez', 'Lefevre', 'Gonzalez', 'Bonnet', 'Fournier', 'Girard'];
$prenoms = ['Jean', 'Sophie', 'Pierre', 'Claire', 'Luc', 'Emma', 'Paul', 'Julie', 'Antoine', 'Laura', 'Marc', 'Elodie', 'Nicolas', 'Amélie', 'Thomas'];

for ($i = 0; $i < 50; $i++) {
    $personnes[] = [
        'civility' => $civilities[array_rand($civilities)],
        'nom' => $noms[array_rand($noms)],
        'prenom' => $prenoms[array_rand($prenoms)],
        'age' => rand(18, 65)
    ];
}


$adapter = new ArrayAdapter();
$adapter->query($personnes);

$adapter->editColumn('nom', function ($data) {
    return strtoupper($data['nom']);
});

$adapter->editColumn('age', function ($data) {
    return $data['age'].' ans';
});

echo $adapter->getJson();