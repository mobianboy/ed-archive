# Service Bridge (Bridge) [![Build Status](https://magnum.travis-ci.com/eardish/ephect-slc.svg?token=vGUuUCxgKasxsVDXqcQR&branch=dev)](https://magnum.travis-ci.com/eardish/ephect-slc) [![Coverage Status](https://coveralls.io/repos/eardish/ephect-slc/badge.png?branch=dev)](https://coveralls.io/r/eardish/ephect-slc?branch=dev) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eardish/ephect-slc/badges/quality-score.png?b=dev&s=9d5fa1daf1b6a0485c4b4f3121dc6e13dd3d10a3)](https://scrutinizer-ci.com/g/eardish/ephect-slc/?branch=dev)


The Bridge is where most of the business logic exists. It is composed of two main parts:

- The Controllers
- The Agents

## The Controllers

The controllers are much like controllers in traditional MVC software, implementing much of the business logic. They provision services on demand using the agents.

## The Agents

The agents function as a library of functions for interfacing with the services directly over sockets. Much like services like MySQL, they act as a set of operations that can be performed on a stateless socket connection.

## How It Works
The Bridge is where all of the action happens.  The vast majority of valid requests on the Eardish system will be sent through the Bridge (with the noteable exception being Authentication).  The Bridge receives a 'routeable' Request object (one that has a RouteBlock and routing added in the APIManager layer), then uses that information to load a controller and action on the controller.  The action for the service knows what kind of data it needs from the Request object, and pulls it.  It then loads the appropriate Agent for the service, connects to it over network and passes either a serialized or json-formatted data package SYNCHRONOUSLY (blocking), and waits for the response.  When it gets the response, the Bridge (in the Service Manager) will create a Response object, package the data and send it to the API 'backport', where it is then sent to the Builder for 'formatting' and then sent back to the client (user) who initiated the request.

## Important Notes
Blocking I/O and non-blocking I/O is a concept at the very core the Bridge (in fact, all components of the Ephect architecture).  Everything is built on top of React PHP -- an evented, asynchronous communications library -- which has an event loop and eventing mechanism that is very similar to the one in node.js:

* The Bridge's Server (where it listens for requests from the APIManager -  e.g. inbound) is asynchronous and is non-blocking.  It uses an event loop to handle multiple connections effienctly in a single thread.

* However, when the Bridge connects to a Service (outbound), it is synchronous and blocks.  It connects, sends data, waits for a reply, gets it and then connects to the backport (also synchronously), dumps the request and disconnects.

Understanding this is key to understanding how the components work in Ephect.  

See the [React PHP Wiki] (https://github.com/reactphp/react/wiki) for more reading on the React event loop.

