<html>
  <head>
    <title>Do I have to move my car today?</title>

    <link href="http://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="styles.css" />

  </head>
  <body>

  <p id="answer">
    <?php
      date_default_timezone_set('America/New_York');

      $filename = 'http://www1.nyc.gov/apps/311/311Today.rss';
      $file_headers = @get_headers($filename);

      if (($file_headers[0] != 'HTTP/1.0 404 Not Found') && ($file_headers[0] != 'HTTP/1.0 302 Found')) {
        $asp_data = file_get_contents($filename);
      }

      $date_string = get_date_string();

      if (!($date_string && $asp_data)) {
        $the_verdict = 'Whoops.';
        $description = "There's a problem with the date. Bug <a href=\"http://www.fureigh.com\">Fureigh</a> about it, will you?";
      }
      else {
        // Assignment operator is intentional.
        if ($tomorrow = strpos($asp_data, $date_string)) {
          // @todo: Find out what time the RSS feed updates. If tomorrow were ever listed as the first day (i.e., today), 
          // you could get a false "not in effect" if alternate side parking rules were to be not in effect the day after tomorrow.

          $needle = 'Alternate side parking not in effect';

          // Assignment operator is intentional.
          if ($not_in_effect = strpos($asp_data, $needle, $tomorrow)) {
            // Yes, alternate side parking is suspended tomorrow.
            // So no, you don't need to move your car.
            $the_verdict = 'No';

            // @todo: See whether 311 provides a description or whether you can only get it from NYC Open Data's iCal file.
            $description = 'You don\'t have to move your car before tomorrow. Lucky you.';
          }
          else {
            // Sorry, bub, alternate side parking rules are in effect tomorrow.
            $the_verdict = 'Yes';
            $description = 'You gotta move your car before tomorrow. Or <a href="http://transalt.org" target="_blank">switch to a bike</a>.';
          }
        }
        // @todo: Create a set_error function. Call it above and here too.
      }

      // It sure should exist, but being on the safe side...
      if (!is_null($the_verdict)) {
        print $the_verdict . '!';
      }

      function get_date_string() {
        // If today's the last day of the month,
        if (date('j') == date('t')) {
          // Tomorrow's the first day of the next month.
          $month = date('F');

          // If this is the last day of December, tomorrow's a new year too.
          if ($month == 'December') {
            $year = date('Y') + 1;
          }
          else {
            $year = date('Y');
          }
          $date_string  = 'January 1, ' . $year;
        }
        else {
          // Saturday, December 15, 2013
          // l, F j, Y. But we don't actually need the day of the week; searching for the day, month, year will suffice.
          // @todo: Find out whether the 311 uses leading zeros for days. If so, change j to d.
          $date_string  = date('F') . ' ' . (date('j') + 1) . ', ' . date('Y');
        }

        if ($date_string) {
          return $date_string;
        }
        else {
          return FALSE;
        }
      }

    ?>
  </p>

    <?php
      if ($description) { ?>
        <p>
          <?php 
            print $description;
          ?>
        </p>
        <?php
      }
    ?>
  <aside>
    <p>That&rsquo;s just according to <a href="https://nycopendata.socrata.com">NYC Open Data</a>, though. Things can always <a href="http://twitter.com/nycasp">change</a>.</p>
  </aside>

  <footer>
    <p>Made in Brooklyn by <a href="http://www.fureigh.com">Fureigh</a>.</p>
    <p>Fureigh and the City of New York make no representations about any content or information made accessible herein, for any purpose. This information is provided &ldquo;as is,&rdquo; and you assume all risks associated with the use of this website.</p>
  </footer>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

  <script>
    $(document).ready(function() {
      $("p#answer:contains('Yes')").css("background-color", "#CC0000"); // Turn it red if there's bad news.
    });
  </script>

</body>
</html>