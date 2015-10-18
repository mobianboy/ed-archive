var ARtoolsApp = angular.module('ARtoolsApp', ['file-model', 'textAngular']);

ARtoolsApp.controller('ARtoolsController', function($scope) {

  $(document).ready(function () {
    $('.dropdown-toggle').dropdown();
  });

  initTabs();
  function initTabs() {
    tabClasses = ["","","",""];
  }

  $scope.setActiveTab = function (tabNum) {
    initTabs();
    tabClasses[tabNum] = "active";
    $scope.profileResult = false;
    if (tabNum === 4){
      getArtistContent($scope.selectedArtist.id);
      $scope.showTrackArt = false;
    }
  };
  $scope.setActiveTab(5);

  var tabClasses;

  $scope.getTabClass = function (tabNum) {
    return tabClasses[tabNum];
  };

  $scope.getTabPaneClass = function (tabNum) {
    return "tab-pane " + tabClasses[tabNum];
  };
  $scope.changeTab = function(tab) {
    $scope.view_tab = tab;
  };
  $scope.tab1 = "";
  $scope.tab2 = "";
  $scope.tab3 = "";
  $scope.tab4 = "";


  var gateway = "REPLACE_IP";
  var gateway = "localhost";
  //var gateway = "apidev.eardishcorp.com";

  var imageBucket = "REPLACE_IMAGE_BUCKET";
  var imageBucket = "eardish.dev.images";

  var songBucket = "REPLACE_SONG_BUCKET";
  var songBucket = "eardish.dev.songs";

  var getProfiles = function() {
    //console.log("connection successful");
    var json = jsonGenerator();
    var profileResponse = function( nextIndex, dataList ) {
      return function (responseData) {
        //console.log(responseData);
        var profile = responseData.data;
        $scope.$apply(function(){
          var genreId = 1;
          if (profile.hasOwnProperty("genreId")) {
            genreId = profile.genreId;
          }

          $scope.artists.push({
            "id": parseInt(profile.id),
            "type": profile.type,
            "genre": $scope.genres[genreId -1].id,
            "artistName": profile.displayName,
            "hometown": profile.hometown,
            "influencedBy": profile.influencedBy,
            "inviteCode": profile.inviteCode,
            "yearFounded": parseInt(profile.yearFounded),
            "website": profile.website,
            "facebookPage": profile.socialLinks.facebookPage,
            "twitterPage": profile.socialLinks.facebookPage,
            "name": {
              "first": profile.name.first,
              "last": profile.name.last
            },
            "arRep": profile.arRep,
            "email": profile.email,
            "phone": profile.address.phone,
            "address1": profile.address.address1,
            "address2": profile.address.address2,
            "city": profile.address.city,
            "state": profile.address.state,
            "zipcode": profile.address.zipcode,
            "bio": profile.bio,
            "contactId": profile.contactId,
            "avatar": profile.art.phoneLarge,
            "lastEditedBy": profile.lastEditedBy
          });
          $scope.filteredArtists = $scope.artists;
        });

        // do next request
        if( dataList[ nextIndex ] ) {
          getProfileData( dataList[nextIndex].id, nextIndex+1, dataList );
        } else {
          $scope.$apply(function(){
            $scope.loading = false;
          });
        }
      };
    };
    function getProfileData( id, nextIndex, dataList ) {
      var request = jsonGenerator();
      request.action.route = 'profile/get';
      request.data.id = parseInt(id);

      // get profile information
      socketRequest(request, profileResponse( nextIndex, dataList ));
    }
    $scope.loading = true;
    json.action.route = "profile/list";
    // get the list profiles
    socketRequest(json, function(responseData) {
      if (responseData.data && responseData.data.artistProfiles) {
        var data = responseData.data.artistProfiles;
        getProfileData( data[0].id, 1, data );
      } else {
        $scope.loading = false;
      }
    });
  };

  $scope.loading = false;
  var responseCallbacks = {};
  var responseToken = 1;

  var webSocket = new WebSocket("ws://" + gateway + ":80");
  webSocket.onopen = function() {
    getProfiles();
  };
  webSocket.onclose =  healSocket;

  function healSocket() {
    webSocket = new WebSocket("ws://" + gateway + ":80");
    webSocket.onmessage = onMessageEvent;
    if (webSocket.readyState != 1) {
      window.setTimeout(function() {
        healSocket();
      }, 1000);
    } else {
      $scope.login(true);
      webSocket.onclose = healSocket;
    }
  }

  healSocket();
  // FUNCTIONALITY

  $scope.arts = {};
  $scope.selectedArt = "";

  $scope.tracks = {};
  $scope.selectedTrack = {};

  $scope.query = "";
  $scope.filteredArtists = $scope.artists;
  $scope.clearSearch = function()
  {
    $scope.query = "";
    $scope.filteredArtists = $scope.artists;
  };

  $scope.updateFilter = function(query)
  {
    query = query.toLowerCase();
    $scope.filteredArtists = [];
    if (query !== "") {
      for(var i = 0; i < $scope.artists.length; i++){
        var artistName = $scope.artists[i].artistName.toLowerCase();
        if (artistName.indexOf(query) > -1) {
          $scope.filteredArtists.push($scope.artists[i]);
        }
      }
    } else {
      $scope.filteredArtists = $scope.artists;
    }
  };

  $scope.artCount = $scope.arts.length;

  $scope.artDefault = true;
  $scope.trackArtDefault = true;
  $scope.showTrackArt = false;
  $scope.profilePicDefault = true;
  $scope.showEmail = true;

  $scope.$watch('selectedArtist', function() {
    //console.log($scope.selectedArtist);
    $scope.profileId = $scope.selectedArtist.id;
    //hide the email field if they are editing an artist (because this is part of user registration
    $scope.showEmail = $scope.selectedArtist.id === 0;
    $scope.profilePicDefault = !(typeof $scope.selectedArtist.avatar == 'string' || $scope.selectedArtist.avatar instanceof String);
    $scope.showTrackArt = false;
    $scope.profileResult = false;
    $scope.artUploadedResult = false;
    $scope.songUploadResult = false;
    $scope.profileMessage = "";
    if (!$scope.selectedArtist.lastEditedByName && $scope.selectedArtist.lastEditedBy) {
      getLastEditedBy();
    }
    getArtistContent($scope.profileId);
  });

  function getLastEditedBy() {
    if ($scope.selectedArtist.lastEditedByName == $scope.currentLoggedInProfile.id) {
      $scope.selectedArtist.lastEditedByName = $scope.currentLoggedInProfile.displayName;
    } else {
      var json = jsonGenerator();
      json.action.route = "profile/get";
      json.data.id = $scope.selectedArtist.lastEditedBy;
      socketRequest(json, function(responseData) {
        $scope.$apply(function() {
          if (responseData.status.code == 1) {
            $scope.selectedArtist.lastEditedByName = responseData.data.displayName;
          }
        });
      });
    }
  }

  function getArtistContent(profileId)
  {
    var json = jsonGenerator();
    json.action.route = "profile/content/list";
    json.data.id = profileId;
    socketRequest(json, function (responseData) {
      if (responseData.data) {
        var data = responseData.data.artistContent;
        //console.log(data);
        $scope.$apply(function() {
          $scope.artDefault = true;
          if (data) {
            $scope.arts[profileId] = [];
          }
          $scope.tracks[profileId] = [];
          var artIds = [];
          for (var i = 0; i < data.length; i++) {
            //console.log(data[i]);
            $scope.trackArtDefault = data[i].trackId ? false : true;
            //console.log($scope.trackArtDefault);
            if (data[i].type != 'avatar' && data[i].format == "phone_large") {
              //$scope.artDefault = false;
              var artId = data[i].artId;
              if(!isInArray(artId, artIds)) {
                artIds.push(artId);
                addArt(data[i].artTitle, "processed", data[i].url, data[i].profileId, data[i].artId);
              }
              if (data[i].trackId !== null && data[i].deleted != 't') {
                var published = false;
                if (data[i].published == 't') {
                  published = true;
                }
                addTrack(data[i].name, "processed", data[i].profileId, data[i].url, data[i].trackId, data[i].trackUrl, published);
              }
            }
          }
        });
      }
    });
  }

  function isInArray(value, array) {
    return array.indexOf(value) > -1;
  }

  //default artist for page load (creating a new artist)
  $scope.artists = [
    {
      id:0,
      artistName: "",
      "type": "artist-solo",
      "genre": 1,
      "hometown": "",
      "influencedBy": "",
      "yearFounded": 1950,
      "website": "",
      "facebookPage": "",
      "twitterPage": "",
      "name" : {
        "first": "",
        "last": ""
      },
      "arRep": 1,
      "email": "",
      "phone": "",
      "address1": "",
      "address2": "",
      "city": "",
      "state": "Alabama",
      "zipcode": "",
      "avatar": null
    }
  ];

  $scope.selectedArtist = $scope.artists[0];
  $scope.genres = [
    {id:1, name: "Pop"},
    {id:2, name: "Rock"},
    {id:3, name: "Alternative"},
    {id:4, name: "Country"},
    {id:5, name: "Hip-Hop/Urban"}
  ];

  $scope.selectedArtist.genre = $scope.genres[0].id;

  $scope.years = [];
  for (var i = 1950; i <= 2015; i++) {
    $scope.years.push(i);
  }
  $scope.pageStatus = "";

  $scope.loginParams = {
    email: "",
    password: ""
  };
  $scope.loggedIn = false;
  $scope.arRepLogin = false;
  $scope.currentLoggedInProfile = {};

  $scope.login = function(healingEvent) {
    $scope.loginResult = false;
    var json = jsonGenerator();
    json.auth = {};
    json.auth.email = healingEvent ? $scope.currentLoggedInProfile.email : $scope.loginParams.email;
    json.auth.password = healingEvent ? $scope.currentLoggedInProfile.password : $scope.loginParams.password;
    delete json.data;
    delete json.action.route;
    delete json.action.priority;
    socketRequest(json, function(responseData) {
      if (responseData.status.code == 1) {
        $scope.$apply(function() {
          $scope.loggedIn = true;
          $scope.currentLoggedInProfile.email = $scope.loginParams.email;
          $scope.currentLoggedInProfile.password = $scope.loginParams.password;
          if (!healingEvent) {
            var json = jsonGenerator();
            json.data.id = responseData.data.profileId;
            json.action.route = "profile/get";
            socketRequest(json, function(responseData){
              if (responseData.status.code == 1) {
                $scope.$apply(function() {
                  $scope.currentLoggedInProfile.displayName = responseData.data.displayName;
                  $scope.currentLoggedInProfile.id = responseData.data.id;
                  $scope.currentLoggedInProfile.type = responseData.data.type;
                  $scope.selectedArtist.arRep = responseData.data.id;
                  $scope.arRepLogin = true;
                  $scope.setActiveTab(1);
                  $scope.filteredArtists = $scope.filteredArtists.filter(function(artist) {
                    return parseInt(artist.arRep) == $scope.currentLoggedInProfile.id ||
                        $scope.currentLoggedInProfile.type == 'ar-admin';
                  });
                })
              } else {
                $scope.loginResult = true;
                $scope.loginMessage = responseData.status.message;
              }
              clearLoginPage();
            });
          }
        })
      } else {
        $scope.$apply(function() {
          if (healingEvent) {
            alert("Socket closed and user could not be logged in. Please refresh page or check the server");
          }
          $scope.loginResult = true;
          $scope.loginMessage = responseData.status.message;
        });
      }
    })
  };

  function clearLoginPage() {
    $scope.loginParams.email = "";
    $scope.loginParams.password = "";
  }

  $scope.logout = function() {
    var answer = confirm("Are you sure you want to logout?");
    if (answer == true) {
      $scope.loggedIn = false;
      $scope.arRepLogin = false;
      $scope.currentLoggedInProfile = {};
      $scope.setActiveTab(5);
      webSocket.close();
    }
  };

  $scope.register = {
    "name" : {
      "first": "",
      "last": ""
    },
    "email": "",
    "password": "",
    "passwordConfirmation": "",
    "inviteCode": "",
    "zipcode": ""
  };

  $scope.registerRep = function()
  {
    $scope.profileResult = false;
    if ($scope.register.password != $scope.register.passwordConfirmation) {
      $scope.profileResult = true;
      $scope.profileMessage = "Passwords must match";
      return true;
    }
    $scope.registering = true;
    $scope.register.type = "ar-rep";
    delete $scope.register.passwordConfirmation;
    $scope.selectedArtist = {};
    $scope.createProfile();
    $scope.registering = false;
  };

  $scope.newRep = {
    "email": "",
    "admin": false
  };

  $scope.inviteRep = function() {
    $scope.inviteResult = false;
    var json = jsonGenerator();
    json.action.route = "referral/create";
    json.data = $scope.newRep;
    socketRequest(json, function(responseData) {
      $scope.inviteResult = true;
      $scope.$apply(function() {
        if (responseData.status.code ==1 ) {
          var adminStatus = "";
          if ($scope.newRep.admin) {
            $scope.newRep.admin = false;
            adminStatus = "(admin)";
          } else {
            adminStatus = "(non-admin)";
          }
          $scope.inviteMessage = "Invite " + adminStatus + " sent to " + $scope.newRep.email;
          $scope.newRep.email = "";
        } else {
          $scope.inviteMessage = responseData.status.message;
        }
      });
    })
  };

  $scope.profilePic = null;
  // create/edit profile
  $scope.createProfile = function() {

    $scope.profileResult = false;
    $scope.waiting = true;
    var json = jsonGenerator();
    if ($scope.registering) {
      json.data = $scope.register;
    } else {
      json.data = $scope.selectedArtist;
    }
    console.log(json.data);
    delete json.data.bio;
    json.data.art = {};

    //decide if creating or updating profile
    var profileKey = $scope.selectedArtist.id;

    if (!$scope.selectedArtist.id) {
      if(!$scope.selectedArtist.email && !$scope.register.email) {
        $scope.waiting = false;
        $scope.profileResult = true;
        $scope.profileMessage = "email is required";
        return;
      }
      delete json.data.id;
      json.action.route = "profile/create";
      profileKey = "client-" + Math.random().toString(36).substring(7);
    } else {
      json.action.route = "profile/update";
      delete json.data.email;
    }
    if ($scope.registering) {
      json.action.route = "user/ar-rep/create";
    }

    // process image if one is uploaded
    if ($scope.profilePic) {
      //console.log($scope.profilePic);
      if ($scope.profilePicName === "") {
        $scope.profilePicName = $scope.profilePic.name;
      }

      var copySource = encodeURI(imageBucket + "/" + $scope.profilePic.name);
      var params = {
        accessKeyId: "AKIAI5ZWXGGHFLXLVVAQ",
        secretAccessKey: "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",
        region: "us-west-2"
      };

      var s3 = new AWS.S3(params);

      s3.upload({
        Key: "public/temp/" + profileKey + "/avatar/original/" + $scope.profilePic.name,
        ContentType: $scope.profilePic.type,
        Body: $scope.profilePic,
        Bucket: imageBucket,
        CopySource: copySource
        }, function (err, data) {
          //console.log(data);
          //console.log(err);
          json.data.art.url = data.Location;
          json.data.art.type = 'avatar';
          json.data.art.title = $scope.profilePic.name;
          $scope.$apply(function() {
            $scope.profilePic = null;
            $scope.profilePicDefault = false;
            $scope.selectedArtist.avatar = data.Location;
          });
          sendProfileRequest(json);
        }
      );

    } else {
      if ($scope.selectedArtist.avatar == null && $scope.selectedArtist.id == 0 && !$scope.registering) {
        $scope.profileResult = true;
        $scope.waiting = false;
        $scope.profileMessage = "Profile picture is required.";
        return;
      }
      //send request
      sendProfileRequest(json);
    }
  };

  function sendProfileRequest(json)
  {
    socketRequest(json, function (responseData) {
      $scope.$apply(function() {
        $scope.waiting = false;
        $scope.profileResult = true;
        if (responseData.status.code == 1) {
          $scope.selectedArtist.id = parseInt(responseData.data.id);
          $scope.selectedArtist.contactId = parseInt(responseData.data.contactId);
          $scope.profileId = responseData.data.id;
          $scope.showEmail = false;
          $scope.profileMessage = "Profile saved!";
          if (json.action.route == "user/ar-rep/create") {
            $scope.setActiveTab(5);
          }
        } else {
          $scope.profileMessage = responseData.status.message;
        }
      });
    });
  }

  $scope.saveBio = function()
  {
    $scope.waiting = true;
    var json = jsonGenerator();
    json.action.route = "profile/update";
    json.data.id = $scope.selectedArtist.id;
    json.data.bio = $scope.selectedArtist.bio.replace(/(\r\n|\n|\r)/gm,"<br>");
    console.log(json.data.bio);
    sendProfileRequest(json);
  };

  $scope.artUpload = null;
  $scope.uploadArt = function()
  {
    //console.log(($scope.artUpload));
    $scope.artUploadedResult = false;
    if ($scope.artUpload) {
      $scope.waiting = true;

      var copySource = imageBucket + "/" + $scope.artUpload.name;
      var params = {
        accessKeyId: "AKIAI5ZWXGGHFLXLVVAQ",
        secretAccessKey: "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",
        region: "us-west-2"
      };

      var s3 = new AWS.S3(params);

      s3.upload({
        Key: "public/temp/" +$scope.profileId + "/track/art/original/" + $scope.artUpload.name,
        ContentType: $scope.artUpload.type,
        Body: $scope.artUpload,
        Bucket: imageBucket,
        CopySource: copySource
      }, function (err, data) {
        if (err) {
          $scope.waiting = false;
          $scope.artMessage = "Error uploading art, please try again.";
        } else {
          var s3url = data.Location;
          var json = jsonGenerator();
          json.action.route = "profile/art/create";
          json.data.profileId = $scope.profileId;
          json.data.url = s3url;
          json.data.title = $scope.artUpload.name;
          json.data.type = 'track-art';

          $scope.$apply(function () {
            addArt($scope.artUpload.name, "pending", s3url, $scope.profileId, $scope.artId);
            // replace default photo with art from upload
            $scope.artDefault = false;
            //clear name field
            $scope.artName = "";
            $scope.artUpload = null;
          });

          //console.log($scope.arts);
          socketRequest(json, function (responseData) {
            $scope.$apply(function() {
              $scope.waiting = false;
              $scope.artUploadedResult = true;
              if (responseData.status.code == 1 || responseData.status.code == 2) {
                $scope.artMessage = "Uploaded!";
              } else {
                $scope.artMessage = responseData.status.message;
              }
            });
          });
        }
      })
    } else {
      $scope.artUploadedResult = true;
      $scope.artMessage = "Please select art to upload and try again.";
    }
  };

  function addArt(artName, status, s3url, profileId, artId)
  {
    if (!$scope.arts.hasOwnProperty(profileId)) {
      $scope.arts[profileId]= [];
    }
    $scope.arts[profileId].push(
      {
        "artName": artName,
        "status": status,
        "url": s3url,
        "artId": parseInt(artId)
      }
    );
  }

  $scope.$watch('selectedArt',function()
  {
    $scope.showTrackArt = true;
    //console.log($scope.selectedArt);
  });

  $scope.songUpload = null;

  $scope.waiting = false;

  $scope.uploadSong = function()
  {
    //console.log(($scope.songUpload));
    $scope.songUploadResult = false;
    if($scope.songUpload) {
      if ($scope.selectedArt) {
        //console.log($scope.selectedArt);
        $scope.waiting = true;
        $scope.songName = $scope.songUpload.name;

        var trackName = "";
        if ($scope.trackName) {
          trackName = $scope.trackName;
        } else {
          trackName = $scope.songUpload.name;
        }
        var track = jsonGenerator();
        track.data.trackName = trackName;
        track.data.profileId = $scope.profileId;
        track.data.artId = $scope.selectedArt.artId;
        track.data.genreId = $scope.selectedArtist.genre;
        track.data.published = false;
        track.action.route = "track/create";

        socketRequest(track, function(responseData) {
          if (responseData.status.code == 1) {
            var trackId = responseData.data.id;
            var copySource = songBucket + "/" + $scope.songName;
            var params = {
              accessKeyId: "AKIAI5ZWXGGHFLXLVVAQ",
              secretAccessKey: "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",
              region: "us-west-2"
            };
            //console.log(params);
            ///console.log($scope.selectedArt);
            var s3 = new AWS.S3(params);
            //  AWS.config.region = 'us-west-2';
            var key = "public/" + $scope.profileId + "/" + trackId + "/original.mp3";
            console.log(key);
            s3.upload({
              Key: key,
              ContentType: $scope.songUpload.type,
              Body: $scope.songUpload,
              Bucket: songBucket,
              CopySource: copySource
            }, function (err, data) {
              if (err) {
                console.log(err);
                $scope.songUploadResult = true;
                $scope.songMessage = "Error uploading track, please try again.";
              }
              if (data) {
                console.log(data);
                var s3url = data.Location;
                var json = jsonGenerator();
                json.action.route = "track/upload";
                json.data.profileId = $scope.selectedArtist.id;
                json.data.trackUrl = s3url;
                json.data.trackId = trackId;

                $scope.$apply(function () {
                  //console.log($scope.profileId);
                  //console.log($scope.selectedArt.url);

                  $scope.trackName = "";
                  $scope.songUpload = null;
                });
                //console.log($scope.tracks[$scope.profileId]);
                socketRequest(json, function(responseData) {
                  $scope.waiting = false;
                  if (responseData.status.code == 1) {
                    $scope.$apply(function(){
                      //console.log(responseData.data.trackUrl);
                      addTrack(trackName, "processed", $scope.profileId, $scope.selectedArt.url, responseData.data.trackId, responseData.data.trackUrl);
                      $scope.songUploadResult = true;
                      $scope.songMessage = "Uploaded!";
                    });
                  } else {
                    $scope.songUploadResult = true;
                    $scope.songMessage = responseData.status.message;
                  }
                })
              }
            });
          } else {
            $scope.waiting = false;
            $scope.songUploadResult = true;
            $scope.songMessage = responseData.status.message;
          }
        });
      } else {
        $scope.songUploadResult = true;
        $scope.songMessage = "Select art to be associated with your track. Click the art tab to upload art.";
      }
    } else {
      $scope.songUploadResult = true;
      $scope.songMessage = "Please select a song to be upload and try again.";
    }
  };

  function addTrack(trackName, status, profileId, artUrl, trackId, trackUrl, published)
  {
    if (!$scope.tracks.hasOwnProperty(profileId)) {
      console.log( "creating track array for " + profileId);
      $scope.tracks[profileId] = [];
    }
    $scope.tracks[profileId].push(
        {
          "trackName": trackName,
          "status": status,
          "trackId": trackId,
          "artUrl": artUrl,
          "trackUrl": trackUrl,
          "published": published
        }
    );
  }

  $scope.deleteTrack = function(track, index)
  {
    console.log(index);
    console.log(track.trackId);

    var response = confirm("Are you sure you want to delete track: " + track.trackName + "?");
    if (response === true) {
      var json = jsonGenerator();
      json.data.trackId = track.trackId;
      json.action.route = "track/delete";

      socketRequest(json, function(responseData){
        if (responseData.status.code == 2) {
          $scope.$apply(function(){
            $scope.tracks[$scope.profileId].splice(index,1);
          });
        }
      });
    }
  };

  $scope.publishTrack = function(track)
  {
    console.log(track);
    var json = jsonGenerator();
    json.action.route = "track/update";
    json.data.trackId = track.trackId;
    json.data.published = track.published;

    socketRequest(json, function(responseData){
      console.log(responseData);
    });
  };

  function onMessageEvent(event) {
    var responseData = JSON.parse(event.data);
    console.log("in onmessage: %o", responseData);
    if(responseData.meta) {
      if(responseData.meta.responseToken in responseCallbacks) {
        var token = responseData.meta.responseToken;
        responseCallbacks[token](responseData);
      }
    }
  }

  webSocket.onmessage = function(event) {
    onMessageEvent(event)
  };

  function jsonGenerator() {
    return {
      "action" :{
        "route": "",
        "priority": 10,
        "responseToken": ""
      },
      "data" : {},
      "auth":{
        "email": $scope.currentLoggedInProfile.email || "artoolsadmin@eardish.com",
        "password": $scope.currentLoggedInProfile.password || "pa$$4arToolsAdmin"
      }
    };
  }

  function socketRequest(json, callback)
  {
    json.action.responseToken = responseToken;
    console.log(json);
    responseCallbacks[responseToken] = callback;
    if (webSocket.readyState == 1) {
      webSocket.send(JSON.stringify(json));
    } else {
      window.setTimeout(function() {
        socketRequest(json, callback)
      }, 100)
    }
    responseToken++;
  }

  $scope.states = [
    {
      "name": "Alabama",
      "abbreviation": "AL"
    },
    {
      "name": "Alaska",
      "abbreviation": "AK"
    },
    {
      "name": "American Samoa",
      "abbreviation": "AS"
    },
    {
      "name": "Arizona",
      "abbreviation": "AZ"
    },
    {
      "name": "Arkansas",
      "abbreviation": "AR"
    },
    {
      "name": "California",
      "abbreviation": "CA"
    },
    {
      "name": "Colorado",
      "abbreviation": "CO"
    },
    {
      "name": "Connecticut",
      "abbreviation": "CT"
    },
    {
      "name": "Delaware",
      "abbreviation": "DE"
    },
    {
      "name": "District Of Columbia",
      "abbreviation": "DC"
    },
    {
      "name": "Federated States Of Micronesia",
      "abbreviation": "FM"
    },
    {
      "name": "Florida",
      "abbreviation": "FL"
    },
    {
      "name": "Georgia",
      "abbreviation": "GA"
    },
    {
      "name": "Guam",
      "abbreviation": "GU"
    },
    {
      "name": "Hawaii",
      "abbreviation": "HI"
    },
    {
      "name": "Idaho",
      "abbreviation": "ID"
    },
    {
      "name": "Illinois",
      "abbreviation": "IL"
    },
    {
      "name": "Indiana",
      "abbreviation": "IN"
    },
    {
      "name": "Iowa",
      "abbreviation": "IA"
    },
    {
      "name": "Kansas",
      "abbreviation": "KS"
    },
    {
      "name": "Kentucky",
      "abbreviation": "KY"
    },
    {
      "name": "Louisiana",
      "abbreviation": "LA"
    },
    {
      "name": "Maine",
      "abbreviation": "ME"
    },
    {
      "name": "Marshall Islands",
      "abbreviation": "MH"
    },
    {
      "name": "Maryland",
      "abbreviation": "MD"
    },
    {
      "name": "Massachusetts",
      "abbreviation": "MA"
    },
    {
      "name": "Michigan",
      "abbreviation": "MI"
    },
    {
      "name": "Minnesota",
      "abbreviation": "MN"
    },
    {
      "name": "Mississippi",
      "abbreviation": "MS"
    },
    {
      "name": "Missouri",
      "abbreviation": "MO"
    },
    {
      "name": "Montana",
      "abbreviation": "MT"
    },
    {
      "name": "Nebraska",
      "abbreviation": "NE"
    },
    {
      "name": "Nevada",
      "abbreviation": "NV"
    },
    {
      "name": "New Hampshire",
      "abbreviation": "NH"
    },
    {
      "name": "New Jersey",
      "abbreviation": "NJ"
    },
    {
      "name": "New Mexico",
      "abbreviation": "NM"
    },
    {
      "name": "New York",
      "abbreviation": "NY"
    },
    {
      "name": "North Carolina",
      "abbreviation": "NC"
    },
    {
      "name": "North Dakota",
      "abbreviation": "ND"
    },
    {
      "name": "Northern Mariana Islands",
      "abbreviation": "MP"
    },
    {
      "name": "Ohio",
      "abbreviation": "OH"
    },
    {
      "name": "Oklahoma",
      "abbreviation": "OK"
    },
    {
      "name": "Oregon",
      "abbreviation": "OR"
    },
    {
      "name": "Palau",
      "abbreviation": "PW"
    },
    {
      "name": "Pennsylvania",
      "abbreviation": "PA"
    },
    {
      "name": "Puerto Rico",
      "abbreviation": "PR"
    },
    {
      "name": "Rhode Island",
      "abbreviation": "RI"
    },
    {
      "name": "South Carolina",
      "abbreviation": "SC"
    },
    {
      "name": "South Dakota",
      "abbreviation": "SD"
    },
    {
      "name": "Tennessee",
      "abbreviation": "TN"
    },
    {
      "name": "Texas",
      "abbreviation": "TX"
    },
    {
      "name": "Utah",
      "abbreviation": "UT"
    },
    {
      "name": "Vermont",
      "abbreviation": "VT"
    },
    {
      "name": "Virgin Islands",
      "abbreviation": "VI"
    },
    {
      "name": "Virginia",
      "abbreviation": "VA"
    },
    {
      "name": "Washington",
      "abbreviation": "WA"
    },
    {
      "name": "West Virginia",
      "abbreviation": "WV"
    },
    {
      "name": "Wisconsin",
      "abbreviation": "WI"
    },
    {
      "name": "Wyoming",
      "abbreviation": "WY"
    }
  ];
});