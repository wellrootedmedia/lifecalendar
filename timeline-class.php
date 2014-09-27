<?php function displayTimelineClass() { global $wpdb; ?>
<div id="main">
    <h1>Your time line</h1>

    <div id="timelineLimiter"> <!-- Hides the overflowing timelineScroll div -->
        <div id="timelineScroll"> <!-- Contains the timeline and expands to fit -->
            <?php
            $query = "
                SELECT * FROM ".$wpdb->prefix."posts
                WHERE post_type = 'life_calendar_events'
                AND post_status <> 'auto-draft'
            ";
            $posts = $wpdb->get_results($query, OBJECT);

            $dates = array();
            //$results = array();
            if(!empty($posts)) {
                foreach($posts as $post ) {
//                    array_push($results, array(
//                        'postTitle' => $post->post_title,
//                        'postDate' => $post->post_date
//                    ));
                        $dates[date('Y',strtotime($post->post_date))][] = $post;
                }
            }
            //print_r($results);
            $colors = array('green', 'blue', 'chreme');
            $scrollPoints = '';
            $i = 0;
            foreach ($dates as $year=>$array) {
                echo '<div class="event" id="'.$year.'"><div class="eventHeading '.$colors[$i++%3].'">'.$year.'</div><ul class="eventList">';
                foreach($array as $event) {
                    ?>
                    <li class="'.$event['type'].'">
                        <span class="icon" title=""></span><?php echo htmlspecialchars($event->post_title); ?>
                        <div class="content">
                            <div class="body"><div style="text-align:center"><img src="" alt="Image" /><a href="#">View All Photos</a></div></div>
                            <div class="title"><?php echo htmlspecialchars($event->post_title); ?></div>
                            <div class="date"><?php echo date("F j, Y",strtotime($event->post_date)); ?></div>
                            <a href="#" class="close">Close</a>
                        </div>
                    </li>
                <?php
                }
                $i++;
                echo '</ul></div>';
                $scrollPoints.='<div class="scrollPoints"><a href="#'.$year.'">'.$year.'</a></div>';
            }
            ?>

            <div class="clear"></div>
        </div>

        <div id="scroll"> <!-- The year time line -->
            <div id="centered"> <!-- Sized by jQuery to fit all the years -->
                <div id="highlight"></div> <!-- The light blue highlight shown behind the years -->
                <?php echo $scrollPoints ?> <!-- This PHP variable holds the years that have events -->
                <div class="clear"></div>
            </div>
        </div>

        <div id="slider"> <!-- The slider container -->
            <div id="bar"> <!-- The bar that can be dragged -->
                <div id="barLeft"></div>  <!-- Left arrow of the bar -->
                <div id="barRight"></div>  <!-- Right arrow, both are styled with CSS -->
            </div>
        </div>

    </div>
</div>
<?php } ?>