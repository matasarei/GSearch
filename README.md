# GSearch
Google Search for PHP

## Usage
```php
//Just create a new instance
$search = new GSearch();
//You can set cache timeout (hours) and path
$search = new GSearch(24, './cache/');
//...or even without caching
$search = new GSearch(false, false);

//And try it
$results = $search->query('Search for something...');
var_dump($results);
```

##Caching
This code supports caching. You have to set up writable path to allow cache processing ("./cache/" by default).
