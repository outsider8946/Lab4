<?php
require __DIR__ . '/vendor/autoload.php';
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="Table_fill_data.php" method="post">
    <input type="text" name="name" placeholder="Введите name">
    <input type="text" name="salary" placeholder="Введите salary">
    <input type="text" name="age" placeholder="Введите age">
    <input type="text" name="smth" placeholder="Введите smth">
    <input type="submit" name =  "button">
    <?php

        $client = new Google_Client();
        $client->setApplicationName('Google Sheets in PHP');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(__DIR__ . '/creditnails.json');

        $service = new Google_Service_Sheets($client);

        $range = 'A1:D20';

        if(!file_exists("table_data.txt"))
        {
            mkdir("table_data.txt");
        }
        $data_file = file_get_contents("table_data.txt");
        $data =[];

        if(strlen($data_file)===0)
        {
            $data  = [
                [
                    'Name',
                    'Salary',
                    'Age',
                    'Smth',
                ]
            ];
        }
        else
        {
            $data  = unserialize($data_file);
        }

        $input = [];
        foreach ($_SESSION["Info"] as $item)
        {
            $temp= $item[1];
            if(sizeof($input) === 0)
            {
                $input[] = $temp;
            }
            else
            {
                array_push($input,$temp);
            }
        }
        array_push($data,$input);

        $data_file  = serialize($data);
        file_put_contents("table_data.txt","");

        file_put_contents("table_data.txt", $data_file);

        $values = new Google_Service_Sheets_ValueRange([
            'values'=>$data
        ]);

        $options = [
            'valueInputOption' => 'RAW'
        ];

        $spreadsheetId = '1YTrDcTAZe7oJajvY1wrubGmBSWlu1RNP2InNxCGByMQ';
        $service->spreadsheets_values->update($spreadsheetId,$range,$values,$options);

        ?>
    </form>
</body>
</html>