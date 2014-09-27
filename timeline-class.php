<div id="main">
    <h1>Your life in a Time Line</h1>

    <div id="timelineLimiter"> <!-- Hides the overflowing timelineScroll div -->
        <div id="timelineScroll"> <!-- Contains the timeline and expands to fit -->
            <?php
            /* check connection */
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }

            $dates = array();
            $query = "";

            if ($result = $mysqli->query($query)) {

                /* fetch associative array */
                while ($row = $result->fetch_assoc()) {
                    $dates[date('Y', strtotime($row['date_event']))][] = $row;
                }

                $colors = array('green', 'blue', 'chreme');
                $scrollPoints = '';

                $i = 0;
                foreach ($dates as $year => $array) {
                    // Loop through the years:
                    echo '<div class="event" id="' . $year . '"><div class="eventHeading ' . $colors[$i++ % 3] . '">' . $year . '</div><ul class="eventList">';

                    foreach ($array as $event) {
                    // Loop through the events in the current year:
                        echo '<li class="' . $event['type'] . '"><span class="icon" title="' . ucfirst($event['type'])
                            . '"></span>' . htmlspecialchars($event['title']) . '
                        <div class="content">
                        <div class="body">' . ($event['type'] == 'image' ? '<div style="text-align:center"><img src="../'
                                . $event['body'] . '" alt="Image" /><a href="#">View All Photos</a></div>' : nl2br($event['body']))
                            . '</div>
                        <div class="title">' . htmlspecialchars($event['title']) . '</div>
                        <div class="date">' . date("F j, Y", strtotime($event['date_event'])) . '</div>
                        <a href="#" class="close">Close</a>
                        </div>
                        </li>';
                    }

                    echo '</ul></div>';

                    // Generate a list of years for the time line scroll bar:
                    $scrollPoints .= '<div class="scrollPoints"><a href="#' . $year . '">' . $year . '</a></div>';
                }

                /* free result set */
                $result->free();
            }

            /* close connection */
            $mysqli->close();

            ?>

            <div class="clear"></div>
        </div>

        <div id="scroll"> <!-- The year time line -->
            <div id="centered"> <!-- Sized by jQuery to fit all the years -->
                <div id="highlight"></div>
                <!-- The light blue highlight shown behind the years -->
                <?php echo $scrollPoints ?> <!-- This PHP variable holds the years that have events -->
                <div class="clear"></div>
            </div>
        </div>

        <div id="slider"> <!-- The slider container -->
            <div id="bar"> <!-- The bar that can be dragged -->
                <div id="barLeft"></div>
                <!-- Left arrow of the bar -->
                <div id="barRight"></div>
                <!-- Right arrow, both are styled with CSS -->
            </div>
        </div>

    </div>

    <p class="tutInfo">
    </p>
</div>
