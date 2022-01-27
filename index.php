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
    'location',
    'description',
    'url',
    'followers_count',
    'friends_count',
    'listed_count',
    'created_at',
    'favourites_count',
    'utc_offset',
    'time_zone',
    'geo_enabled',
    'verified',
    'statuses_count',
    'lang',
    'following',
    'follow_request_sent',
    'notifications',
    'last_active'
];
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
        $piece[] = $account->location;
        $piece[] = str_replace("\n", " ", $account->description);
        $piece[] = $account->url;
        $piece[] = $account->followers_count;
        $piece[] = $account->friends_count;
        $piece[] = $account->listed_count;
        $piece[] = $account->created_at;
        $piece[] = $account->favourites_count;
        $piece[] = $account->utc_offset;
        $piece[] = $account->time_zone;
        $piece[] = $account->geo_enabled;
        $piece[] = $account->verified;
        $piece[] = $account->statuses_count;
        $piece[] = $account->lang;
        $piece[] = $account->following;
        $piece[] = $account->follow_request_sent;
        $piece[] = $account->notifications;
        $piece[] = $account->status->created_at;

        $data[] = $piece;
    }

    print_r("Search results: \n");
    print_r($data);
    print_r("\n");
    $csv->insertAll($data);
    $page++;
} while (count($result) != 0 && $page < 52);


$filename = "search_" . $query . ".csv";
print_r("Writing results to the file $filename \n");
file_put_contents($filename, $csv);

print_r("Stopping application \n");
