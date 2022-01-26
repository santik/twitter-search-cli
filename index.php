<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

use League\Csv\Writer;

print_r("Starting twitter search app \n");

require_once "config.php";

$twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

$query = $argv[1];
print_r("Search query: $query \n");

print_r("Creating CSV \n");
$header = [
    'id',
    'name',
    'screen_name',
//    'location',
//    'description',
    'url'];
//load the CSV document from a string
$csv = Writer::createFromString();

$csv->insertOne($header);

$page = 0;
do {
    print_r("Calling user search API page $page \n");
    $result = $twitter->request('users/search', 'GET', ['q' => $query, "page" => $page, 'include_entities' => false]);
    $data = [];
    foreach ($result as $account) {
        $piece = [];
        $piece[] = $account->id;
        $piece[] = $account->name;
        $piece[] = $account->screen_name;
//        $piece[] = $account->location;
//        $piece[] = $account->description;
        $piece[] = $account->url;
        $data[] = $piece;
    }

    print_r("Search results: \n");
    print_r($data);
    print_r("\n");
    $csv->insertAll($data);
    $page++;
} while (count($result) != 0 && $page < 52);


$filename = "search_" . $query . "_" . time() . ".csv";
print_r("Writing results to the file $filename \n");
file_put_contents($filename, $csv);

print_r("Stopping application \n");
