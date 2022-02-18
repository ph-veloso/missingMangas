        <?php
        require_once('Connections/CONNECT_SQL.php');
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>

        <body>

            <?php

            //Query para selcionar todos os mangás e seus respectivos ultimos volumes
            $query = "SELECT manga, ultimo_volume FROM volumes_atual;";
            $result = mysqli_query($conn, $query);
            $possuidos = [1, 2, 3];

            //while para colocar os mangas e seus ultimos volumes em um array
            while ($row = mysqli_fetch_array($result)) {
                $mangasVolumes[$row['manga']] = $row['ultimo_volume'];
            }

            //foreach para separar o os mangas dos volumes
            foreach ($mangasVolumes as $manga => $volume) {

                //lógica para pegar todo o range entre o volume 1 e o ultimo volume do mangá disponível
                $todosVolumes[$manga] = (range(1, $volume));

                //lógica que pega o array de mangás possuídos e todo o range de volumes lançados do mangá para pegar o que sobra, ou seja, pegar os volumes que eu não possuo
                $volumesFaltando = array_diff($todosVolumes[$manga], $possuidos);

                print_r($manga . ':  <br>' . implode(', <br>', $volumesFaltando) . ' <br>');
            }
            //Chamada da API JIKAN para pegar o ID do mangá
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://jikan1.p.rapidapi.com/search/manga?q=Attack%20on%20Titan",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "x-rapidapi-host: jikan1.p.rapidapi.com",
                    "x-rapidapi-key: a73a6981bbmsh17c403bb1b0a01ap1b1f06jsn351beb61e790"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $test = json_decode($response, true);
                echo $test['results'][0]['mal_id'];
            }
            //finalização da chamada da API para pegar o ID
            ?>


        </body>

        </html>