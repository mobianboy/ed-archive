var ARtoolsApp = angular.module('GatewayMonitor', []);

ARtoolsApp.controller('MonitorList', function($scope, $http) {
  //$scope.test = "hello";
  $scope.conn = new WebSocket("ws://10.151.0.51:80");
  $scope.conns = [];
  $scope.conn.onopen = function() {
      console.log("connection successful");
      var connectRequest = '{"key":"3ard1sh","mode":"connection"}';
      $scope.conn.send(connectRequest);
      console.log('sent');
  };

  $scope.conn.onmessage = function(message) {
    console.log(message);
      var message = JSON.parse(message.data);
            console.log(message);
      $(".row").append("<hr><div><span style='font-size:12px; color: #555;'>"+moment().format('MMM Do YYYY, h:mm a')+"</span><br />");
      $(".row").append("<strong>Current Conns</strong>:<br />");
      //console.log(message.conns);
      //Object.keys(message.conns).map(function(key){return message.conns[key]});
       Object.keys(message.conns).forEach(function (key) {
        var conns = message.conns;
         $(".row").append("<strong>"+key+"</strong><br />");

           Object.keys(conns[key]).forEach(function (property) {
            var conn = conns[key];
             $(".row").append("<strong>"+property +"</strong>" + ": " + conn[property] + "<br />");
           });
           $(".row").append("<br />");
       });
      $(".row").append("</br><strong>Message</strong>: " + message.info.message+" <br /><strong>Type</strong>:"+message.info.type+"</div>");
      Object.keys(message.activity).forEach(function (key) {
        var activity = message.activity;
         $(".row").append("<br /><strong>Requests in the last "+ (key-1) +" to "+key+" minute(s)</strong><br />");
          $(".row").append(activity[key] + "<br />");
       });
      window.scrollTo(0, document.body.scrollHeight);
  };
});
