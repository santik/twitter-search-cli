<?php

use League\Csv\Writer;

print_r("Starting twitter search app \n");

require_once "config.php";

$twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

$query = $argv[1];
print_r("Search query: $query \n");

print_r("Creating CSV \n");
$header = ['first name', 'last name', 'email'];
//load the CSV document from a string
$csv = Writer::createFromString();

$csv->insertOne($header);

$page = 1;
do {
    print_r("Calling user search API page $page \n");
    $res = $twitter->request('users/search', 'GET', ['q' => $query, "page" => $page]);
    print_r("Search results: $res \n");
    $res = [
        [1, 2, 3],
        ['foo', 'bar', 'baz'],
        ['john', 'doe', 'john.doe@example.com'],
    ];
    $csv->insertAll($res);
    $page++;
} while (count($res) != 0);

$filename = "search_" . $query . "_" . time() . ".csv";
print_r("Writing results to the file $filename \n");
file_put_contents($filename, $csv);

print_r("Stopping application \n");
