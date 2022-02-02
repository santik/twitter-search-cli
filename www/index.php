<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "config.php";

use League\Csv\Writer;
use DG\Twitter\Twitter;
use SearchTools\DBTools;

print_r("Starting twitter search app \n");

$dbTools = new DBTools($db);

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
        $dbData = [];

        $piece[] = $account->id;
        $dbData['id'] = $account->id;
        $piece[] = $account->name;
        $dbData['name'] = $account->name;
        $piece[] = $account->screen_name;
        $dbData['screen_name'] = $account->screen_name;
        $piece[] = $account->location;
        $dbData['location'] = $account->location;
        $description = mb_convert_encoding(
            str_replace("\n", " ", $account->description),
            'UTF-8', 'UTF-8'
        );
        $piece[] = $description;
        $dbData['description'] = $description;
        $piece[] = $account->url;
        $dbData['url'] = $account->url;
        $piece[] = $account->followers_count;
        $dbData['followers_count'] = $account->followers_count;
        $piece[] = $account->friends_count;
        $dbData['friends_count'] = $account->friends_count;
        $piece[] = $account->listed_count;
        $dbData['listed_count'] = $account->listed_count;
        $piece[] = $account->created_at;
        $dbData['created_at'] = $account->created_at;
        $piece[] = $account->favourites_count;
        $dbData['favourites_count'] = $account->favourites_count;
        $piece[] = $account->utc_offset;
        $piece[] = $account->time_zone;
        $piece[] = $account->geo_enabled;
        $piece[] = $account->verified;
        $dbData['verified'] = $account->verified;
        $piece[] = $account->statuses_count;
        $dbData['statuses_count'] = $account->statuses_count;
        $piece[] = $account->lang;
        $piece[] = $account->following;
        $piece[] = $account->follow_request_sent;
        $piece[] = $account->notifications;
        $piece[] = $account->status->created_at;
        $dbData['last_active'] = $account->status->created_at;

        $data[] = $piece;

        if ($saveResultsToDatabase) {
            $dbData = $dbTools->cleanInput($dbData);
            $dbTools->upsert('twitter', $dbData);
        }
    }

    $csv->insertAll($data);
    $page++;
} while (count($result) != 0 && $page < 52);


$filename = "search_" . $query . ".csv";
print_r("Writing results to the file $filename \n");
file_put_contents($filename, $csv);

print_r("Stopping application \n");
