<?php

/**
 * Fonction permettant de retourner un nombre aléatoire compris dans un intervalle défini
 * @param  int $minNum
 * @param  int $maxNum
 * @return int
 */
function randomizator(int $minNum, int $maxNum): int
{
    if ($minNum >= 10 || $maxNum >= 10) {
        throw new Exception("Nan, j'ai pas encore eu la foi de gérer les chiffres au delà de 10.");
    }

    if ($minNum > $maxNum) {
        throw new Exception("Ah bah non !");
    } 

    //On récupère le timestamp actuel
    $timestamp = time();

    do {
        $postalCode = generatePostalCode();

        //On cherche les coordonnées d'une ville française au hasard
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api-adresse.data.gouv.fr/search/?q=" . $postalCode);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($curl);
    
        $datas = json_decode($output);
    
        curl_close($curl);
    } while (count($datas->features) === 0);

    $cityClass = $datas->features[rand(0, count($datas->features) - 1)];
    
    //On récupère ses coordonnées
    $coordinates = [$cityClass->geometry->coordinates[0], $cityClass->geometry->coordinates[1]];

    $result = (int) ($timestamp * $coordinates[0] * $coordinates[1]);
    
    if ($result < 0) {
        $result *= -1;
    }

    //On récupère une image au hasard d'un chat
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.thecatapi.com/v1/images/search");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $output = curl_exec($curl);

    $datas = json_decode($output);

    curl_close($curl);

    $size = [$datas[0]->width, $datas[0]->height];

    $result = (int) ($result * $size[0] * $size[1]);

    //On récupère un fait au hasard sur les chats
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://meowfacts.herokuapp.com/");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $output = curl_exec($curl);

    $datas = json_decode($output);

    curl_close($curl);

    $result = (int) ($result * strlen($datas->data[0]));

    if ($result < 0) {
        $result *= -1;
    }

    $i = 0;

    do {
        $resultToString = strval($result);
        $randomNumber = (int) $resultToString[$i];
        $i++;
    } while ($randomNumber < $minNum || $randomNumber > $maxNum);

    return $randomNumber;
}

function generatePostalCode(): string
{
    $state = rand(1, 95);

    if ($state < 10) {
        $state = '0' . $state;
    }

    $complement = rand(0, 999);

    if ($complement < 100) {
        $complement = str_pad($complement, 3, '0', STR_PAD_LEFT);
    }

    return $state . $complement;
}