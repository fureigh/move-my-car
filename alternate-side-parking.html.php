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

      $filename = 'http://www.nyc.gov/html/dot/downloads/misc/' . date('Y') . '_alternate_side.ics';
      $file_headers = @get_headers($filename);

      if (($file_headers[0] != 'HTTP/1.0 404 Not Found') && ($file_headers[0] != 'HTTP/1.0 302 Found')) {
        $off_days = file_get_contents($filename);
      }

      $date_string = get_date_string();
      if ($date_string && $off_days) {
        $needle = 'DTSTART;VALUE=DATE:' . $date_string;
      }
      else {
        $the_verdict = 'Whoops.';
        $description = "There's a problem with the date. Bug Fureigh about it, will you?";
      }

      // Intentionally assigning a value to $start.
      if ($start = strpos($needle, $off_days)) {
        // Yes, alternate side parking is suspended tomorrow.
        // So no, you don't need to move your car.
        $the_verdict = 'No';

        // Search for the next instance of 'DESCRIPTION.'
        $remainder = substr($off_days, $start);
        $description = substr($remainder, strpos('DESCRIPTION:', $remainder));
        $description = ltrim($description, 'DESCRIPTION:');
      }
      else {
        // Sorry, bub, alternate side parking rules are in effect tomorrow.
        $the_verdict = 'Yes';
        $description = 'You gotta move your car before tomorrow. Or <a href="http://transalt.org" target="_blank">switch to a bike</a>.';
      }

      print $the_verdict . '!';

      function get_date_string() {
        // If today's the last day of the month,
        if (date('d') == date('t')) {
          // Tomorrow's the first day of the next month.
          $month = date('m');

          // If this is the last day of December, tomorrow's a new year too.
          if ($month == '12') {
            $year = date('Y') + 1;
          }
          else {
            $year = date('Y');
          }
          $date_string  = $year . $month + 1 . '01';
        }
        else {
          $date_string  = date('Ym') . date('d') + 1;
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