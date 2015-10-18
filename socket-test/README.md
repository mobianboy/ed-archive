Websocket Tester
================
Used to help diagnose websocket issues

Run gateway.php, bridge.php, service.php and servicetwo.php in the command line. Telnet into localhost, port 7000. Send a number. Sending a number like 20,000,000 makes service 1 work for about 10 seconds. Sending anything under 100,000 makes sevicetwo get called. 

This is helpful when trying to visualize blocking and behavior across unix sockets.
