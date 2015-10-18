# Welcome to JavaScript #

### Good Reads ###

* [MDN Tutorials](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide) <-- Good place to start learning syntax
* [Stackoverflow is a great resource](http://stackoverflow.com/questions/3970961/getting-started-with-javascript)
* [An e-book of the definitive guide](JavaScript_The_Definitive_Guide_Sixth_Edition.pdf)
* [idiomatic.js](https://github.com/rwaldron/idiomatic.js/) <-- We follow this JS code styleguide
* [Streams! (read after assessment)](https://github.com/substack/stream-handbook)


## What to Do ##

### Setup ###
1. Set up NVM (https://github.com/creationix/nvm)
2. Install latest version of node (`nvm install 0.12`)
3. Alias that as the "default" node (`nvm alias default 0.12`)
4. Clone this repo! (using SSH of course: `git@github.com:eardish/lesson-js.git`)
5. Create a branch for yourself (use your name for the branch name)

### Part 1 (js-assessment) ###
1. [Clone this assessment repo](https://github.com/rmurphey/js-assessment)
  1. Follow the instructions in [https://github.com/rmurphey/js-assessment](https://github.com/rmurphey/js-assessment)
2. Create a Pull Request from your branch into master (so I can look at the diff and comment on your code)

### Part 2 (NodeJS) ###
The second part of this is to get you familiar with NodeJS, it's apis, the NPM package manager, and writing an actual application in JavaScript.

In this part you will
- Create a small CLI app in [Node](https://nodejs.org/api/)
- Create a pragmatic api for the same CLI app
- Create a RESTful endpoint leveraging the pragmatic api
- Install a dependecy with [NPM](https://docs.npmjs.com/)

#### First Steps ####

1. Create a new branch from `master` titled `part2-{yourname}` (replacing the {yourname} of course)
2. Choose a path below
  1. The classic [Palindrome](https://en.wikipedia.org/wiki/Palindrome) Question (further described below)
  2. This interview cake problem https://www.interviewcake.com/question/cake-thief

#### For Palindrome Question #####

Create a simple app the detects if a given string is a palindrome. The app must provide at least, these APIs: CLI, Pragmatic, and RESTful server endpoint. You can get as fancy as you want with palindrome detection, strict detection including all characters, or with ignoring spaces, or with stripping symbols, etc.; the more versions you implement the better. The input string should be tested against all versions of palindrome detection, and output should be labeled accordingly.

##### CLI #####

Node's main entry file is `index.js`, this is where the app starts, this file should detect if it is being ["require'd" or executed from the command line](https://nodejs.org/docs/latest/api/all.html#all_accessing_the_main_module)

Example CLI usage (the last uses node's "shorthand" syntax from project root)
```shell
$ node index.js 'abcba'
abcba is a palindrome (strict)

$ node index.js 'abbbc'
abbbc is not a palindrome (strict)

node . 'ab bba'
ab bba is not a palindrome (strict)
ab bba is a palindrome (ignore spaces)
```

Lines that pass should be colored green, lines that fail should be colored red. Search through https://www.npmjs.com/ to find a module to help you with colorizing output, there is one that has become a standard module for this (not color.js), hint: it is named after that stuff you used to draw on the sidewalk with as a kid.

##### Pragmatic #####

The pragmatic implementation should export a single function that takes a string input. The function returns an object of key-value pairs with the key being the type of check and values being a boolean of whether the test passed or failed. The result object should also contain the input string under the key 'input'. Note that this implementation should be leveraged by the CLI api, and it does not have to be in `index.js`, `index.js` can `require` the pragmatic API to support the CLI API.

Example of Pragmatic Usage
```js
var 
  results,
  toTest = [ 'abba', 'ab bba', 'racecar', '0101' ],
  isPalindrome = require( './src/is-palindrome.js' ); // example of where implementation file might live

results = toTest.map( function( input ) {
  return isPalindrome( input );
});
// JS note, the above map call could be shortened to:
//    results = toTest.map( isPalindrome );

console.log( results );

/* Resulting Output:
[
  {
    input: 'abba',
    strict: true,
    ignoreSpaces: true
  },
  {
    input: 'ab bba',
    strict: false,
    ignoreSpaces: true
  },
  {
    input: 'racecar',
    strict: true,
    ignoreSpaces: true
  },
  {
    input: '0101',
    strict: false,
    ignoreSpaces: false
  },
]
*/
```

##### RESTful Server #####

For the final part I would like you to create a clustered [(using nodes' cluster api)](https://nodejs.org/api/cluster.html#cluster_cluster) RESTful server [(using nodes' http module)](https://nodejs.org/api/http.html#http_http), **do not use a server framework**. A single server should be able to be started from `server.js` located in the root project folder, the clustering bit should go into `cluster.js` in the root project folder. Both `server.js` and `cluster.js` should be able to be executed from the command line. The `server.js` file should be able to take a port as an argument with the form `-p PORT_NUMBER`. The `cluster.js` file should also be able to take the port as an argument in addition to an optional argument of how many threads to create, with the form `-t NUM_THREADS` (if there is no thread argument default to one thread for every CPU core). Note that the `server.js` file will need to export a function to create a server to be used by `cluster.js` (require'd vs pragmatic again).

The server will have one handler for all requests, it should look for a query parameter `input` as the string input and return the JSON representation of the result object returned by the pragmatic api.

Start with the `server.js` file first, then move to adding in the clustering.

Example of starting a single server:
```shell
$ node server.js -p 5115
```

Example server request (using [httpie](https://github.com/jkbrzt/httpie)):
```shell
$ http GET "localhost:5115?input=abba"
HTTP/1.1 200 OK
Access-Control-Allow-Origin: *
Connection: keep-alive
Content-Type: text/plain
Date: Tue, 14 Jul 2015 20:14:58 GMT
Transfer-Encoding: chunked

{
  "input": "abba",
  "strict": true,
  "ignoreSpaces": true
}
```

Example of starting a server cluster:
```shell
$ node cluster.js -p 5115 -t 3
```

Feel free to bring in other NPM modules to spice things up (but again, not a server framework).

#### For [Cake Theif Qeustion](https://www.interviewcake.com/question/cake-thief) #####

You will need to support the same three APIs described above but with the input of "cakes" and "capacity" Example usage:

##### CLI #####

Arguments should be qualified with flags `-cakes` and `-capacity`
```shell
$ node . -cakes "[[ 7, 160 ], [ 3, 90 ], [ 2, 15 ]]" -capacity 20
For maximum $$$ buy:
  # of cake [ 7, 160 ]
  # of cake [ 3, 90 ]
  # of cake [ 2, 15 ]
```

##### Pragmatic #####
```js
var
  results,
  cakes = [[ 7, 160 ], [ 3, 90 ], [ 2, 15 ]],
  capacity = 20,
  cakeTheif = require( './src/cake-theif.js' );
  
results = cakeTheif( cakes, capacity );

console.log( results );
/* Example output
{
  input: {
    cakes: [[ 7, 160 ], [ 3, 90 ], [ 2, 15 ]],
    capacity: 20
  },
  // take is an array of the # of cakes to by where index of take matches index of input.cakes
  take: [
    #, // to buy of cake [ 7, 160 ]
    #, // to buy of cake [ 3, 90 ]
    #, // to buy of cake [ 2, 15 ]
  ]
}
*/
```

##### RESTful Server #####

Lunch time, I think you get it
