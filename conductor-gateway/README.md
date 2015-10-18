Conductor [![Build Status](https://magnum.travis-ci.com/eardish/ephect-api.svg?token=3VxnfKzgpXxqJKzHzB68&branch=dev)](https://magnum.travis-ci.com/eardish/ephect-api) [![Coverage Status](https://coveralls.io/repos/eardish/ephect-api/badge.png?branch=dev)](https://coveralls.io/r/eardish/ephect-api?branch=dev) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eardish/ephect-api/badges/quality-score.png?b=dev&s=4fdefc81872cf0472ac12d012bc371b86e181b48)](https://scrutinizer-ci.com/g/eardish/ephect-api/?branch=dev)
==========

Conductor is a PHP library that is designed to abstract and handle API
correspondence between a PHP application and a JavaScript front end.

It accomplishes this by being highly configurable and offering a standard and
extensible way for an application to interact and respond with standard API formats.

WARNING!! This Project Is Not Functional Yet!
---------------------------------------------

This project is not ready for testing as core development is still ongoing. If
you would like to contribute to this project, please contact the maintainer at

1) The Architecture
-------------------

Conductor is built on three fundamental concepts and two services.

### Concept 1: The Formats

A format in Conductor Gateway is a class which extends

    Gateway\Formats\Core\BasicFormatFactory

and defines a structured way for the API to respond to a request. It defines three
critical properties (`_concat`, `_opentag`, and `_closetag`) and three critical
methods (`buildSingleExport`, `buildPartialExport`, and `buildFullExport`).

It also can optionally take in a set of header information to pre-pend to all responses.

When a `FormatFactory` executes a `buildFullExport` call, it by default will do
the following:

    $this->openTag();
    $this->headers().$this->_concat;
    implode($this->_concat, $compiledArray).$this->_concat;
    $this->closeTag();

For instance, in a `JSONFactory` this might result in:

    {
    "header":{"attr1":"on","attr2":"off"},
    "block1:"value1","block2":value2","block3":{"block4":"value4","block5":"value5"},
    }

Any class which extends `BasicFormatFactory` can be used as a Format.

### Concept 2: The Data Objects

By default there are two core Data Objects: `SingleLevelDataObject` and `MultipleLevelDataObject`.

These two objects are never used directly to build your API, but rather are used to
build complex Data Objects in the `Gateway\DataObjects\DataObjects` namespace
that include some of your business logic.

Conductor comes with several of these complex Data Objects pre-built to utilize
the default configurations settings, including: `StatusCode`, `DataBlock`, `MultipleDataBlock`,
`ReferralBlock`, and `MetaBlock`.

These Data Objects define actual parameters which you can set as part of your API
response, including `status.code`, `data.result.text`, or `referral.format`.

These responses can be traversed by any asynchronous Javascript front end to decide
what to do with the result.

Any class which extends a class in the `Gateway\DataObjects\Core` namespace can
be used as a custom Data Object.

### Concept 3: Responders

Responders contain the logic that glues various Data Objects together into coherent
responses. For instance, by default a status code `02` (`The operation completed and 
there is no data to return.`) will use the `NullResponder`.

The `NullResponder` only implements the `StatusCode` Data Object, meaning that by
default a status code `02` response will result in only `status.code` and `status.message`
being sent.

More complex Responders however can combine unlimited Data Objects into a standard
response format.

Any class which extends `Gateway\Responders\Core\BasicResponder` can be used as
a Responder by the Builder.

### Service 1: The Builder

The Builder acts as the interface between all of the organized objects in the 
Conductor and your PHP program. It abstracts the Responders and the Data Objects
into simple and repeatable methods, with just a touch of "magic" to take care of
certain tasks.

The behavior of the Builder can be changed by supplying it with a different config
file, which is detailed below.

### Service 2: The Interpreter

The Interpreter is the other half of the coin, allowing your application to listen
for structured data and act on it.

An example implementation of the Interpreter might look like this:

    // Start Interpreter
    $data = new Interpreter();
    // Listen for data at $_POST['data']
    $data->bindToPost('data');

    // Your data is available as an array now
    $data->get("action"); //$data->_result['action']
    $data->get("form.name"); //$data->_result['form']['name']
    $data->raw(); //$data->_result

Or, you can use the Interpreter to validate and sanitize the user input as well as 
to build conditional booleans

    $outsideBool = true;
    $result = $data->strComp("format", "image")
                   ->joinAnd()
                   ->groupBool(array("type", "id", $outsidebool), false, "any")
                   ->toBool();
         
    if ($result) {...}

The Interpreter is a powerful tool that allows your PHP application and Javascript
application to talk to each other in a standardized but flexible way, and can easily
integrate into almost any code base.

2) The Default Specification
----------------------------

You can override any part of or even the entire default configuration, but Conductor
comes with a default configuration that allows you to use it right away.

Much like HTTP response codes, Conductor has "Status Code Blocks" and "Status Codes".

### Status Blocks

The default blocks are:

    "statusBlockSpaces": {
        "00": "Requested action completed",
        "10": "Requested action incomplete: improper request",
        "20": "Requested action incomplete: program encountered an error",
        "30": "Requested action incomplete: third party service unavailable",
        "40": "Requested action incomplete: invalid authorization or authentication",
        "50": "Requested action incomplete: untrusted source",
        "60": "Requested action incomplete: server error",
        "70": "Reserved for userspace definitions",
        "80": "Reserved for userspace definitions",
        "90": "Reserved for userspace definitions"
    }

A status block can be thought of as a group of reserved codes that all have a unified
meaning within the context of the API. With the above specification, a Javascript
developer could reasonably assume that if the response code is less than 10, then
the request they made to the application was completely successful.

Status Blocks are defined by their ceilings and floors. For convention's sake, the 
default blocks are all 10 codes in size, however blocks can be any size, and do not
even have to be the same size between different blocks.

The Builder interprets a blockspace as the range (left inclusive) between the 
block definition and the next block definition. That is, the range defined by:

    BlockSpaceDefinition <= Range < Next BlockSpaceDefinition

### Status Codes

The default codes are:

    "statusBlockCodesMessages": {
        "01": "The operation completed and the data has been returned.",
        "02": "The operation completed and there is no data to return.",
        "03": "The operation completed and there is a new request to make.",
        "04": "The operation completed and there is a third party service to contact.",
        "10": "No action could be found for the request.",
        "11": "The request did not contain the correct parameters for the request.",
        "12": "The request did not contain the correct parameters for the request and the structure has been provided.",
        "20": "The program encountered an unknown unrecoverable error.",
        "21": "The program encountered a describable unrecoverable error.",
        "30": "A third-party service did not respond or was unavailable.",
        "31": "A third-party service refused the service call and there is no further explanation.",
        "32": "A third-party service refused the service call and the reason is described.",
        "40": "The request did not contain the required credentials.",
        "41": "The credentials provided were invalid.",
        "42": "The credentials provided do not have permissions to this action.",
        "50": "The request has been rejected because it came from an untrusted source.",
        "51": "The request has been rejected because the token is not recognized.",
        "60": "The program stopped due to an error in an underlying technology and no further information is available.",
        "61": "The program stopped due to an error in an underlying technology and the reason is described."
    }

Each code is given a default response type, which corresponds to a Response object.
The default types for the default codes are:

    "statusBlockCodesResponseFormats": {
        "01": "data-responder",
        "02": "null-responder",
        "03": "referrer-responder",
        "04": "referrer-responder",
        "10": "null-responder",
        "11": "null-responder",
        "12": "data-responder",
        "20": "null-responder",
        "21": "data-responder",
        "30": "data-responder",
        "31": "null-responder",
        "32": "data-responder",
        "40": "null-responder",
        "41": "null-responder",
        "42": "null-responder",
        "50": "null-responder",
        "51": "null-responder",
        "60": "null-responder",
        "61": "data-responder"
    }

3) Development
--------------

While it is in the very early alpha phases, please limit pull requests, as I will be
working to fill in the missing parts of the service. Any contributions are welcome, 
but at the moment I don't even have working unit tests, so keep that in mind.

4) License
----------

The Conductor is currently released under a develop-share license only. That is:

- You may download and alter the code however you wish and may use it in applications.
- You may contribute to the project through pull requests to the GitHub respository.
- You may not distribute or sell the code in any way.

All other uses are prohibited.

This project will be relicensed under a standard open source license (such as GPL) 
at some point in the future.

5) Contact
----------

If you wish to contact the developer about anything, please send an email to:
