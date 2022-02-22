        <?php
        require_once('connections/CONNECT_SQL.php');
        require_once('includes/declarations.php');
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mangás Faltando</title>
        </head>

        <body>
            <div class="container">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">ADICIONAR MANGÁ</button><br>
                </div>
                <!-- Modal -->
                <form id="form">
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background-color: #1F1D36; border-color: #3F3351">
                                    <h5 class="modal-title" id="staticBackdropLabel">Insira um mangá: </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-white" style="background-color: #3F3351;">
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">Nome do mangá: </label>
                                            <input class="form-control col-6" type="text" name="manga" placeholder="Ex: One Piece" aria-label="input manga">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Volumes que você possui: </label>
                                            <input class="form-control col-6" type="text" name="volume_possuido" placeholder="Ex: 1, 2, 3..." aria-label="input manga">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Último volume lançado: </label>
                                            <input class="form-control col-6" type="text" name="volume_atual" placeholder="Ex: 101" aria-label="input manga">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer" style="background-color: #3F3351; border-color: #6e6e6e">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" onclick="saveManga()" class="btn btn-outline-light">Understood</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Fim Modal -->
                <div class="row row-cols-1 row-cols-md-3 g-4 center">
                    <?php

                    //Query para selcionar todos os mangás e seus respectivos ultimos volumes
                    $query = "SELECT manga, ultimo_volume FROM volumes_atual order by manga;";
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

                        // print_r($manga . ':  <br>' . implode(', <br>', $volumesFaltando) . ' <br>');

                        $link = str_replace(' ', '%20', $manga);
                        // echo $link;

                        //Chamada da API JIKAN para pegar o ID do mangá
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => "https://jikan1.p.rapidapi.com/search/manga?q=" . $link,
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
                            $respJsonId = json_decode($response, true);
                        }

                        $id = $respJsonId['results'][0]['mal_id'];
                        //finalização da chamada da API para pegar o ID

                        //chamada da API para pegar a imagem do mangá
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => "https://jikan1.p.rapidapi.com/manga/" . $id . "/pictures",
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
                            $respJsonPic = json_decode($response, true);
                        }
                        //finalização da chamada da API para pegar a imagem do mangá

                        $pic = $respJsonPic['pictures'][0]['large'];

                        $mangaName = ucwords(str_replace('%20', ' ', $link));
                    ?>
                        <div class="col">
                            <div class="card text-white bg-dark mb-3 text-center">
                                <img src="<?= $pic; ?>" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $mangaName; ?></h5>
                                    <p class="card-text">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample<?= $id; ?>" aria-expanded="false" aria-controls="collapseExample<?= $id; ?>">
                                            Volumes Faltando
                                        </button>
                                    </p>
                                    <div class="collapse" id="collapseExample<?= $id; ?>">
                                        <div class="card card-body colapso" id='colapso'>
                                            <?= implode(', ', $volumesFaltando); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php

                    }
                    
                    mysqli_close($conn);
                    
                    ?>
                </div>
            </div>



        </body>
        <script>
        function saveManga(){
        $.ajax({
            url: "services/saveManga.php",
            type: "POST",
            dataType: "text",
            data: $('#form').serialize(),
            success: function(response){
                $('.cadastrado').show();
            }
        })
        }
        </script>
        </html>