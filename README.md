
[phpMysqliWrapper](http://blog.vjeux.com/2009/php/mysqli-wrapper-short-and-secure-queries.html) - Short and Secure queries
================================

Mysqli Wrapper is shortening the code required to run queries, make them 100% safe against SQL Injections and gives a handy Array as result. No more pain writing queries!

When developing a PHP application, SQL queries is the most dangerous area. This is because the built-in tools are either not secure by conception or too much complicated to use. I've made a little wrapper to mysqli that solves all these problems and makes you enjoy writing queries!

It has been designed with these goals in mind:

* A single 6 characters function
* Secure because parameterized
* Returns an Array
* Easy migration from the basic method


### Prototype
The class is extremely simple. The constructor connects to the database, the function q() execute queries and if you need to access to the original mysqli object you can do it with the handle() function.

	class mysqliWrapper() {
		public __constructor($host, $username, $password, $database, $debug);
		public function q($query, $types, [$param ...]);
                public function handle();
	}

### Example

Executing secure queries and processing the results requires really few overhead characters.
	
	// Connect to the database:
	// The last parameter is the debug mode.
	
	$db = new mysqliWrapper('host', 'user', 'pass', 'database', true);

	// Do the query:
	// It fits into a single line now
	// mysqli replaces '?' safely with your parameters, sql injections are no longer possible

	$result = $db->q(
		"SELECT name, country_code FROM city WHERE zip = ? AND population > ?",
		'si',        // Types of the parameters: s for string, i for integer
		$zip, $pop); // Parameters

	// Use the results:
	// The results are returned in an array! This makes it a lot easier to process

	foreach ($result as $key => $city) {
	  // $city['Name'], $city['CountryCode']
	}

	// Additional notes:
	// It returns false when an error happens
	// It returns the number of affected rows for UPDATE and INSERT queries
	// Use $mysqli = $db->handle() to get the mysqli object
