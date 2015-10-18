DataObjects and Exceptions
==================

This repo holds all of the data objects required to rebuild serialized objects across servers.  
It also contains all of the custom EDException base and subclasses that every repo accesses.


** Exceptions **

All exceptions are based on the <code>EDException</code> class and have a status code that matches the issue.  The status code is automatically attached to the EDException subclass.  EDException is a general exception, that, when used alone, returns a status code of 21 (generalized application error, more or less).

All EDException-derived classes can also carry a 'state' object reference back with them, if attached when called.  At this point, the object can be anything.  It is just a way to carry information about the application back to the top, where the exception is handled.

** Classes **
<pre><code>EDException($message = null, $code = 21, $state = null)</code></pre> - EDException base class and general exception.  returns a status code of 21 without subclass.

<pre><code>EDMissingActionException($message = null, $state = null)</code></pre> -Missing Action.  returns a status code of 10.

<pre><code>EDInvalidOrMissingParameterException($message = null, $state = null)</code></pre> - invalid or missing parameters.  returns a status code of 11.

<pre><code>EDConnectionException($message = null, $addr = null, $port = null, $state = null)</code></pre> - cannot connect to a remote network resource for some reason.  returns a status of 22.

<pre><code>EDConnectionReadException($message = null, $addr = null, $port = null, $state = null)</code></pre> - cannot read from a networked resource.  returns a status of 23.

<pre><code>EDConnectionWriteException($message = null, $addr = null, $port = null, $state = null)</code></pre> - cannot write to a networked resource.  returns a status of 24

<pre><code>EDTransportException($message = null, $addr = null, $port = null, $state = null)</code></pre> - unable to send data via transport.  returns a status of 25

<pre><code>EDMissingCredentialsException($message = null, $state = null)</code></pre> - missing credentials.  returns a status code of 40.

<pre><code>EDInvalidCredentialsException($message = null, $state = null)</code></pre> - invalid credentials.  returns a status code of 41.

<pre><code>EDPermissionsException($message = null, $state = null)</code></pre> - valid credentials but does not have permission to access.  returns a status code of 42.


