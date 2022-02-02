# Twitter Search Application

### Local set up

For local development docker environment can be used.
To start container use the command  
`docker-compose up`

### Server set up
Copy all the files from `www` directory to the server. Make sure directory is writable.
Run the command to install dependencies  
`php composer.phar install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist`
### Running the application
Copy `config.php_tpl` to `config.php`.   
Set Twitter API details and DB details in `config.php` file.  
To save results in database flag `$saveResultsToDatabase` should be set to `true`.  
Database schema could be found in `dump/myDb.sql`  
Run the command  
`php index.php searchquery`  
You can use any word as an argument instead of "searchquery". 
IMPORTANT: search is performed not only by username, but also in other fields.  
Result CSV files wil be written in the same directory.
