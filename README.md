# GSearch
Google Search for PHP

## Usage
```php
//Just create new search object
$search = new GSearch();
//or you cant set cache time (minutes) and path
$search = new GSearch(24, './cache/');
//or even without caching
$search = new GSearch(false, false);

//And try it
$results = $search->query('Search for something...');
var_dump($results);
```

##Caching
This code supports caching. You have to set up writable path to allow the class save and process caches ("./cache/" by default).
