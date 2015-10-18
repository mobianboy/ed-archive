<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Eardish | Join the Evolution</title>

    <link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://d1azc1qln24ryf.cloudfront.net/22322/Eardish/style-cf.css">
    <link rel="stylesheet" type="text/css" href="/wp-content/themes/eardish/slick/slick.css"/>


    <!-- Bootstrap -->
    <link href="/wp-content/themes/eardish/dist/css/bootstrap.css" rel="stylesheet">
    <link href="/wp-content/themes/eardish/dist/css/bootstrap-theme.css" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="fullpage/jquery.fullPage.css" /> -->

    <!-- Toastr -->
    <link href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="sticky">
      <div class="container">
        <a class="logo" href="#top"><i class="icon-eardish"></i><h1>Eardish</h1></a>
        <a href="javascript:;" class="menu global-menu"><i class="icon-menu"></i></a>
      </div>
    </nav>
    <nav class="global" >
      <div class="exit"><a href="javascript:;" class="close"><i class="icon-close"></i></a></div>
      <ul class="nav nav-pills nav-stacked">
        <li><a href="#top">Home</a></li>
        <li><a href="#careers">Careers</a></li>
        <li><a href="#artist">Artists</a></li>
        <li><a href="#beta">Fans</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </nav>

    <div id="fullpage">


      <div class="section" id="top">
        <div class="slide" id="top-bg-1">
          <div class="container">
            <h2><strong>Join</strong><br>The Evolution!</h2>
            <p><a href="#beta" class="btn btn-primary">Beta</a></p>
          </div>
        </div>
        <div class="slide" id="top-bg-2">
          <div class="container">
            <h2><strong>Break</strong><br>Free of Labels!</h2>
            <p><a href="#beta" class="btn btn-primary">Beta</a></p>
          </div>
        </div>
        <div class="slide" id="top-bg-3">
          <div class="container">
            <h2><strong>Control</strong><br>Your Destiny!</h2>
            <p><a href="#beta" class="btn btn-primary">Beta</a></p>
          </div>
        </div>
        <div class="slide" id="top-bg-4">
          <div class="container">
            <h2><strong>Change</strong><br>The Industry!</h2>
            <p><a href="#beta" class="btn btn-primary">Beta</a></p>
          </div>
        </div>
        <div class="slide" id="top-bg-5">
          <div class="container">
            <h2><strong>Fund</strong><br>Your Creativity!</h2>
            <p><a href="#beta" class="btn btn-primary">Beta</a></p>
          </div>
        </div>
      </div>


      <div class="section" id="careers" data-midnight="white">
        <div class="page-header">
          <div class="container">
            <h3>Careers <small class="hidden-xs">Want to join us?</small></h3>
          </div>
        </div>
        <div class="container">
          <p>We're growing fast! <br>
          We always need talented people to join us!</p>
          <?/*<p><a href="https://www.ziprecruiter.com/jobs/eardish-30f69629" target="_new" class="btn-default btn button-career">View Available Positions</a></p>*/?>
          <p><a href="mailto:resumes@eardish.com" class="btn-default btn button-career">Submit your resume</a></p>
        </div>
      </div>


      <div class="section" id="artist" >
        <div class="page-header"><div class="container"><h3><strong>Become a</strong><br />Founding Artist</h3></div></div>
        <div class="container">

          <h4>Be one of the first to gain access to a music platform that:</h4>
          <div class="row perks">
            <div class="col-sm-offset-1 col-sm-5">
              <ul>
                <li>Increases and monetizes your fan base</li>
                <li>Provides exposure to a global audience</li>
                <li>Leverages and monitizes your analytics</li>
                <li>Sponsors you with brand patrons</li>
              </ul>
            </div>
            <div class="col-sm-5">
              <ul>
                <li>Allows you to set the retail price of your music</li>
                <li>Pays for every digital sale and stream</li>
                <li>Rewards top performing artists</li>
                <li>Empowers you with fan engagement tools</li>
              </ul>
            </div>
          </div>


          <span class="next"><i class="icon-arrow-down"></i> To be considered, please fill out the form below</span>
        </div>
      </div>


    
      <div class="section" id="artist-info" namr="artist-info" data-midnight="white">
        <div class="container">
          <h4>FOUNDING ARTIST APPLICATION</h4>
          <form id="artist-application" role="form">
            <div class="row">
              <div class="col-sm-10 col-md-8 ">
                <div class="row">
                  <div class="col-sm-6">
                    <fieldset class="inline">
                      <legend>*Artist Type:</legend>
                      <label class="radio-inline">
                        <input type="radio" name="artist-type" id="solo" value="solo"/> Solo
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="artist-type" id="group" value="group"/> Group
                      </label>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <label for="artist-genre">*Genre:</label>
                    <select class="form-control" name="artist-genre" id="artist-genre">
                      <option value="">-Genre-</option>
                      <option value="alt">Alternative</option>
                      <option value="country">Country</option>
                      <option value="dance">Dance/Electronica</option>
                      <option value="urban">Hip-Hop/Urban</option>
                      <option value="pop">Pop</option>
                      <option value="rock">Rock</option>
                      <option value="other">Other</option>
                    </select><br/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-10">
                    <label for="artist-name">*Artist / Band Name:</label>
                    <input class="form-control" type="text" name="artist-name" id="artist-name" />
                  </div>
                </div>
                <fieldset>
                  <legend>Main Contact Info</legend>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-email">*Email:</label>
                        <input class="form-control" type="text" id="artist-email" name="artist-email" />
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-phone">*Phone:</label>
                        <input class="form-control" type="text" id="artist-phone" name="artist-phone" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-first-name">*First Name:</label>
                        <input class="form-control" type="text" name="artist-first-name" id="artist-first-name">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-last-name">Last Name:</label>
                        <input class="form-control" type="text" name="artist-last-name" id="artist-last-name">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-address">Address</label>
                        <input class="form-control" type="text" name="artist-address" id="artist-address" />
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-address2">Address (Line 2)</label>
                        <input class="form-control" type="text" name="artist-address2" id="artist-address2" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-city">City:</label>
                        <input class="form-control" type="text" id="artist-city" name="artist-city" />
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="beta-state">State:</label>
                        <select class="form-control" name="artist-state" id="artist-state">
                          <option value="">-State-</option>
                          <option value="AL">Alabama</option>
                          <option value="AK">Alaska</option>
                          <option value="AZ">Arizona</option>
                          <option value="AR">Arkansas</option>
                          <option value="CA">California</option>
                          <option value="CO">Colorado</option>
                          <option value="CT">Connecticut</option>
                          <option value="DE">Delaware</option>
                          <option value="DC">District Of Columbia</option>
                          <option value="FL">Florida</option>
                          <option value="GA">Georgia</option>
                          <option value="HI">Hawaii</option>
                          <option value="ID">Idaho</option>
                          <option value="IL">Illinois</option>
                          <option value="IN">Indiana</option>
                          <option value="IA">Iowa</option>
                          <option value="KS">Kansas</option>
                          <option value="KY">Kentucky</option>
                          <option value="LA">Louisiana</option>
                          <option value="ME">Maine</option>
                          <option value="MD">Maryland</option>
                          <option value="MA">Massachusetts</option>
                          <option value="MI">Michigan</option>
                          <option value="MN">Minnesota</option>
                          <option value="MS">Mississippi</option>
                          <option value="MO">Missouri</option>
                          <option value="MT">Montana</option>
                          <option value="NE">Nebraska</option>
                          <option value="NV">Nevada</option>
                          <option value="NH">New Hampshire</option>
                          <option value="NJ">New Jersey</option>
                          <option value="NM">New Mexico</option>
                          <option value="NY">New York</option>
                          <option value="NC">North Carolina</option>
                          <option value="ND">North Dakota</option>
                          <option value="OH">Ohio</option>
                          <option value="OK">Oklahoma</option>
                          <option value="OR">Oregon</option>
                          <option value="PA">Pennsylvania</option>
                          <option value="RI">Rhode Island</option>
                          <option value="SC">South Carolina</option>
                          <option value="SD">South Dakota</option>
                          <option value="TN">Tennessee</option>
                          <option value="TX">Texas</option>
                          <option value="UT">Utah</option>
                          <option value="VT">Vermont</option>
                          <option value="VA">Virginia</option>
                          <option value="WA">Washington</option>
                          <option value="WV">West Virginia</option>
                          <option value="WI">Wisconsin</option>
                          <option value="WY">Wyoming</option>
                          <option value="XX">Other</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="artist-zip">Zip:</label>
                        <input class="form-control" type="tel" name="artist-zip" id="artist-zip" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="artist-website">Website:</label>
                        <input class="form-control" type="text" id="artist-website" name="artist-website" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-twitter">Twitter:</label>
                        <input class="form-control" type="text" id="artist-twitter" name="artist-twitter" placeholder="@handle" />
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="artist-facebook">Facebook:</label>
                        <input class="form-control" type="text" id="artist-facebook" name="artist-facebook" placeholder="user.name" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <label for="artist-relationship">Affiliation: (musician, manager, agent, etc.)</label>
                      <input type="text" class="form-control" id="artist-relationship" name="artist-relationship" />
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                    <div class="col-sm-12">
                      <label for="artist-referral">Referral: (scout or rep name)</label>
                      <input type="text" class="form-control" id="artist-referral" name="artist-referral" />
                    </div>
                  </div>
                </fieldset>
                <div class="button text-center">
                  <button class="btn btn-default" type="submit">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>


      <div class="section" id="beta" >
        <div class="page-header"><div class="container"><h3>Beta <small class="hidden-xs">Join the evolution!</small></h3></div></div>
        <div class="container">

          <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
              <h4>Want to be one of the first to try Eardish?</h4>
              <p>Subscribe below for an early access invitation! All fields are mandatory.</p>
              <form id="fan-application">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="beta-first-name">*First Name:</label>
                      <input class="form-control" type="text" name="beta-first-name" id="beta-first-name">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="beta-last-name">*Last Name:</label>
                      <input class="form-control" type="text" name="beta-last-name" id="beta-last-name">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="beta-email">*Email:</label>
                      <input class="form-control" type="text" id="beta-email" name="beta-email" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="beta-birthdate">*Birthdate:</label>
                      <input class="form-control" type="text" id="beta-birthdate" name="beta-birthdate" placeholder="MM/DD/YYYY" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="beta-city">*City:</label>
                      <input class="form-control" type="text" id="beta-city" name="beta-city" />
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="beta-state">*State:</label>
                      <select class="form-control" name="beta-state" id="beta-state">
                        <option value="">-State-</option>
												<option value="AL">Alabama</option>
												<option value="AK">Alaska</option>
												<option value="AZ">Arizona</option>
												<option value="AR">Arkansas</option>
												<option value="CA">California</option>
												<option value="CO">Colorado</option>
												<option value="CT">Connecticut</option>
												<option value="DE">Delaware</option>
												<option value="DC">District Of Columbia</option>
												<option value="FL">Florida</option>
												<option value="GA">Georgia</option>
												<option value="HI">Hawaii</option>
												<option value="ID">Idaho</option>
												<option value="IL">Illinois</option>
												<option value="IN">Indiana</option>
												<option value="IA">Iowa</option>
												<option value="KS">Kansas</option>
												<option value="KY">Kentucky</option>
												<option value="LA">Louisiana</option>
												<option value="ME">Maine</option>
												<option value="MD">Maryland</option>
												<option value="MA">Massachusetts</option>
												<option value="MI">Michigan</option>
												<option value="MN">Minnesota</option>
												<option value="MS">Mississippi</option>
												<option value="MO">Missouri</option>
												<option value="MT">Montana</option>
												<option value="NE">Nebraska</option>
												<option value="NV">Nevada</option>
												<option value="NH">New Hampshire</option>
												<option value="NJ">New Jersey</option>
												<option value="NM">New Mexico</option>
												<option value="NY">New York</option>
												<option value="NC">North Carolina</option>
												<option value="ND">North Dakota</option>
												<option value="OH">Ohio</option>
												<option value="OK">Oklahoma</option>
												<option value="OR">Oregon</option>
												<option value="PA">Pennsylvania</option>
												<option value="RI">Rhode Island</option>
												<option value="SC">South Carolina</option>
												<option value="SD">South Dakota</option>
												<option value="TN">Tennessee</option>
												<option value="TX">Texas</option>
												<option value="UT">Utah</option>
												<option value="VT">Vermont</option>
												<option value="VA">Virginia</option>
												<option value="WA">Washington</option>
												<option value="WV">West Virginia</option>
												<option value="WI">Wisconsin</option>
												<option value="WY">Wyoming</option>
												<option value="XX">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="beta-zip">*Zip:</label>
                      <input class="form-control" type="tel" name="beta-zip" id="beta-zip" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                    <fieldset class="inline">
                      <legend>*Gender:</legend>
                      <label for="beta-gender" class="radio-inline">
                        <input type="radio" name="beta-gender" value="m"/> Male
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="beta-gender" value="f"/> Female
                      </label>
                    </fieldset>
                  </div>
                </div>
              <!--
                <div class="row">
                  <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                    <label class="checkbox-inline"><input type="checkbox" name="beta-pp" value="true" /> I agree to the <a href="">Privacy Policy</a></label>
                  </div>
                </div>
              -->
                <div class="row">
                  <div class="col-sm-12 text-center button">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <div class="section" id="contact">
      <div class="page-header"><div class="container"><h3>Contact</h3></div></div>
        <div class="container">
          <div class="row">
            <div class="col-sm-5">
              <address>
                <strong>Eardish Corporation</strong>
                <div class="street">3726 Laurel Canyon Blvd. <br />Studio City, CA 91604</div>
                <? /*<div class="phone">323.553.1221</div>*/ ?>
                <div class="email"><a href="mailto:info@eardish.com">info@eardish.com</a></div>
              </address>
            </div>
            <div class="col-sm-6 col-sm-offset-1 col-md-5 hidden-xs">
              <img class="img-responsive" src="/wp-content/themes/eardish/images/map.png" alt="Map of Eardish HQ" />
            </div>
          </div>
        </div>
      </div>

    </div> <!-- end #fullpage -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- This following line is needed only in case of using other easing effect rather than "linear", "swing" or "easeInQuart". You can also add the full jQuery UI instead of this file if you prefer -->
    <script src="/wp-content/themes/eardish/fullpage/vendors/jquery.easings.min.js"></script>

    <!-- This following line needed in the case of using the plugin option `scrollOverflow:true` -->
    <script type="text/javascript" src="/wp-content/themes/eardish/fullpage/vendors/jquery.slimscroll.min.js"></script>

    <script type="text/javascript" src="/wp-content/themes/eardish/fullpage/jquery.fullPage.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/wp-content/themes/eardish/dist/js/bootstrap.min.js"></script>

    <script src="//cdn.jsdelivr.net/jquery.midnight/1.1.0/midnight.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.4.1/slick.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.14/jquery.scrollTo.min.js"></script>
    <script src="/wp-content/themes/eardish/dist/js/jquery.localScroll.min.js"></script>

    <!-- Toast notification lib that requires jquery -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js">
    </script>

    <script>
      $(document).ready(function() {
          $('nav.sticky').midnight();
          $('#top').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 4000,
            dots: true
          });

          var wh = $(window).height();
          var top = $("#top");

          if(top.length && wh > 600){
            top.css('height', wh);
          } else {
            top.css('height', 750);
          }
          $.localScroll();

          var link = $(".global-menu");
          var nav = $("nav.global");
          var close = $("nav.global a.close");

          nav.on('click', function(e){
            e.stopPropagation;
          });

          link.on('click', function(e){
            e.stopPropagation;
            nav.addClass("open");
          });

          close.on('click', function(){
            nav.removeClass('open');
          });

          // Toastr notification prefs
          toastr.options.timeOut = 10000;
          toastr.options.extendedTimeOut = 20000;
          toastr.options.positionClass = "toast-top-right";

      });

      // On submit of Fan / Beta tester application form
      $("#fan-application").submit(function(event) {

        // Stop form from submitting normally
        event.preventDefault();

        // Run the AJAX request
        $.ajax({
          type: "post",
          contentType: "application/x-www-form-urlencoded",
          url: "/wp-content/plugins/edf/api.php",
          data: {
            action    : "fan",
            firstName : $("#beta-first-name").val(),
            lastName  : $("#beta-last-name").val(),
            email     : $("#beta-email").val(),
            dob       : $("#beta-birthdate").val(),
            city      : $("#beta-city").val(),
            state     : $("#beta-state").val(),
            zip       : $("#beta-zip").val(),
            gender    : $("input:radio[name=beta-gender]:checked").val(),
            pp        : 1,
            tos       : 1 
          },
          success: function(responseData, textStatus, jqXHR) {
            if(responseData == 1) {
              $("#fan-application").each(function(){
                this.reset();
              });
              var title = 'Thank you';
              var message = 'The Eardish Fan Relations department will be in touch very soon.';
              toastr.success(message, title);
            } else {
              var title = 'Something is missing';
              var message = 'Please fill out all required fields to qualify.';
              toastr.error(message, title);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            var title = 'Oops';
            var message = 'There was an error. Please try again.';
            toastr.error(message, title);
          }
        })

      }); // end ajax form processor

      // On submit of Founding artist application form
      $("#artist-application").submit(function(event) {

        // Stop form from submitting normally
        event.preventDefault();

        // Run the AJAX request
        $.ajax({
          type: "post",
          contentType: "application/x-www-form-urlencoded",
          url: "/wp-content/plugins/edf/api.php",
          data: {
            action        : "artist",
            artistType    : $("input:radio[name=artist-type]:checked").val(),
            genre         : $("#artist-genre").val(),
            artistName    : $("#artist-name").val(),
            firstName     : $("#artist-first-name").val(),
            lastName      : $("#artist-last-name").val(),
            email         : $("#artist-email").val(),
            phone         : $("#artist-phone").val(),
            website       : $("#artist-website").val(),
            twitter       : $("#artist-twitter").val(),
            facebook      : $("#artist-facebook").val(),
            address       : $("#artist-address").val(),
            address2      : $("#artist-address2").val(),
            city          : $("#artist-city").val(),
            state         : $("#artist-state").val(),
            zip           : $("#artist-zip").val(),
            relationship  : $("#artist-relationship").val(),
            referral      : $("#artist-referral").val(),
            pp            : 1,
            tos           : 1 
          },

          success: function(responseData, textStatus, jqXHR) {
            if(responseData == 1) {
              $("#artist-application").each(function(){
                this.reset();
              });
              var title = 'Thank you';
              var message = 'The Eardish Artist Relations department will be in touch very soon.';
              toastr.success(message, title);
            } else {
              var title = 'Something is missing';
              var message = 'Please fill out all required fields to qualify.';
              toastr.error(message, title);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            var title = 'Oops';
            var message = 'There was an error. Please try again.';
            toastr.error(message, title);
          }
        })

      }); // end ajax form processor

    </script>
  </body>
</html>

<? if($instance == 'prod'): // if on production, add Google Analytics tracking code ?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-41471578-1', 'auto');
ga('send', 'pageview');
</script>
<? endif // end Google Analytics tracking ?>

