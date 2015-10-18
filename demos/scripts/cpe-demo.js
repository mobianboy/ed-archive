( function() {
  "use strict";

  var
    randomSponsor,
    sponsorMap = {},
    wheelGifURL = "assets/cpe/wheel/wheel-with-solid-background.gif",
    wheelBlank = "assets/cpe/wheel/wheel-grey.svg",
    sponsorList = [
      "bmw",
      "budweiser",
      "clashofclans",
      "covergirl",
      "doritos",
      "dove",
      "esurance",
      "kia",
      "olay",
      "snickers"
    ],
    sponsorImageVideoArray = [
      {
        name: "BMW",
        image: "bmw.JPG",
        video: [ "bmw.mp4", "bmw.webm" ]
      },
      {
        name: "Budweiser",
        image: "budweiser.png",
        video: [ "budweiser.mp4", "budweiser.webm" ]
      },
      {
        name: "Clash of Clans",
        image: "clashofclans.png",
        video: [ "clashofclans.mp4", "clashofclans.webm" ]
      },
      {
        name: "Covergirl",
        image: "covergirl.jpg",
        video: [ "covergirl.mp4", "covergirl.webm" ]
      },
      {
        name: "Doritos",
        image: "doritos.jpg",
        video: [ "doritos.mp4", "doritos.webm" ]
      },
      {
        name: "Dove",
        image: "dove.jpg",
        video: [ "dove.mp4", "dove.webm" ]
      },
      {
        name: "Esurance",
        image: "esurance.jpg",
        video: [ "esurance.mp4", "esurance.webm" ]
      },
      {
        name: "Kia",
        image: "kia.png",
        video: [ "kia.mp4", "kia.webm" ]
      },
      {
        name: "Olay",
        image: "olay.jpg",
        video: [ "olay.mp4", "olay.webm" ]
      },
      {
        name: "Snickers",
        image: "snickers.png",
        video: [ "snickers.mp4", "snickers.webm" ]
      }
    ]
      .map(function( objectMap ) {
        return {
          name: objectMap.name,
          image: "assets/cpe/icons/" + objectMap.image,
          video: objectMap.video
            .map(function( basename ) {
              return "assets/cpe/videos/" + basename;
            })
        };
      });

  sponsorList.forEach(function( sponsorName, index ) {
    sponsorMap[ sponsorName ] = sponsorImageVideoArray[ index ];
  });

  randomSponsor = function() {
    var randomIndex = Math.floor( Math.random() * ( sponsorList.length - 1 ) + 0 );
    return sponsorMap[ sponsorList[ randomIndex ] ];
  };

  // Start Main Logic
  window.addEventListener("load", function() {
    var imagePreloader = new Image(),
      bindToEvents = [ "touchstart", "mousedown" ],
      elms = {
        notifications: document.getElementById("notifications"),
        notificationImage: document.querySelector("#notifications > img.logo"),
        startingProfile: document.getElementById("starting-profile"),
        searchResults: document.getElementById("search-results"),
        cpe: document.getElementById("cpe"),
        wheelImage: document.querySelector("#cpe > img"),
        video: document.querySelector("#cpe > video"),
        sources: (function(){
          var tmp = document.querySelectorAll("#cpe > video > source");

          return {
            mp4: tmp[0],
            webm: tmp[1]
          };
        })(),
        unlockedModal: document.getElementById( "unlocked-modal" ),
        myCollection: document.getElementById( "my-collection" ),
        artistProfile: document.getElementById( "artist-profile" ),
        endingProfile: document.getElementById( "ending-profile" )
      },
      notificationClicked,
      firePickSong,
      startCPE,
      showCollection,
      showArtistProfile,
      showEndingProfile,
      restartDemo,
      sizeNotifications,
      currentSponsor,
      setSponsorInfo;


    // Setup
    imagePreloader.src = wheelGifURL;
    elms.video.volume = 0.7;

    // Set Sponsor function
    setSponsorInfo = function() {
      console.log("fired");
      currentSponsor = randomSponsor();

      // Set Notification Image
      elms.notificationImage.src = currentSponsor.image;

      // Set Notification Sponsor Names
      Array.prototype.forEach.call(
        elms.notifications.querySelectorAll("span.sponsor-name"),
        function(element) {
          element.innerText = currentSponsor.name;
        }
      );

      // Set Video Source URLs
      elms.sources.mp4.src = currentSponsor.video[0];
      elms.sources.webm.src = currentSponsor.video[1];
    };
    setSponsorInfo();

    // Size Notification Hack TODO
    sizeNotifications = function( ) {
      var notifications = elms.notifications,
        image = elms.startingProfile;

      image.onload = function() {
        var imageHeight = image.offsetHeight;
        notifications.style.width = (640 * imageHeight) / 1136 + 1 + "px";
      };
    };

    // Event Listeners
    firePickSong = function( event ) {
      var currentClassName = elms.notifications.className;

      if ( currentClassName === "pick-song" ) {
        notificationClicked();
      } else {
        elms.notifications.className = "pick-song";
      }
    };
    bindToEvents.forEach(function(eventName) {
      elms.startingProfile.addEventListener(eventName, firePickSong);
    });

    notificationClicked = function( event ) {
      var notificationClass = elms.notifications.className;

      switch ( notificationClass ) {
        case "pick-song":
          elms.startingProfile.setAttribute( "hidden", "" );
          elms.searchResults.removeAttribute( "hidden" );
          elms.notifications.className = "";
          break;
        case "got-song":
          // todo
          showEndingProfile();
          break;
        case "thankyou":
          // todo
          restartDemo();
          break;
        default:
          break;
      }
    };
    bindToEvents.forEach(function(eventName) {
      elms.notifications.addEventListener(eventName, notificationClicked);
    });

    startCPE = function( event ) {
      var
        videoEndHandler = function( event ) {
          elms.cpe.setAttribute( "hidden", "" );
          elms.unlockedModal.removeAttribute( "hidden" );
          elms.video.removeEventListener( "ended", videoEndHandler );
        },
        canPlayHandler = function( event ) {
          elms.wheelImage.src = wheelGifURL;
          elms.video.play();
          elms.video.removeEventListener( "canplay", canPlayHandler );
        };

      elms.searchResults.setAttribute( "hidden", "" );
      elms.cpe.removeAttribute( "hidden" );

      elms.video.addEventListener( "canplay", canPlayHandler );
      elms.video.addEventListener( "ended", videoEndHandler );
      elms.video.load();
    };
    bindToEvents.forEach(function(eventName){
      elms.searchResults.addEventListener(eventName, startCPE);
    });

    showCollection = function( event ) {
      elms.unlockedModal.setAttribute( "hidden", "" );
      elms.myCollection.removeAttribute( "hidden" );
    };
    bindToEvents.forEach(function(eventName) {
      elms.unlockedModal.addEventListener(eventName, showCollection);
    });

    showArtistProfile = function( event ) {
      elms.myCollection.setAttribute( "hidden", "" );
      elms.artistProfile.removeAttribute( "hidden" );
      elms.notifications.className = "got-song";
    };
    bindToEvents.forEach(function(eventName){
      elms.myCollection.addEventListener(eventName, showArtistProfile);
    });

    showEndingProfile = function( event ) {
      elms.artistProfile.setAttribute( "hidden", "" );
      elms.endingProfile.removeAttribute( "hidden" );
      elms.notifications.className = "thankyou";
    };
    bindToEvents.forEach(function(eventName){
      elms.artistProfile.addEventListener(eventName, showEndingProfile);
    });

    restartDemo = function( event ) {
      elms.endingProfile.setAttribute( "hidden", "" );
      elms.notifications.className = "";
      elms.wheelImage.src = wheelBlank;
      elms.startingProfile.removeAttribute( "hidden" );

      setSponsorInfo();
    };
    bindToEvents.forEach(function(eventName){
      elms.endingProfile.addEventListener(eventName, restartDemo);
    });

    sizeNotifications();
  });
})();
