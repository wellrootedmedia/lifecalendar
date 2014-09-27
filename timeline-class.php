<?php function displayTimelineClass() { global $wpdb; ?>
<div id="main">
    <h1>Your time line</h1>

    <div id="timelineLimiter">
        <div id="timelineScroll">
            <?php
            $query = "
                SELECT * FROM ".$wpdb->prefix."posts
                WHERE post_type = 'life_calendar_events'
                AND post_status <> 'auto-draft'
                ORDER BY post_date ASC
            ";
            $posts = $wpdb->get_results($query, OBJECT);

            $dates = array();
            $colors = array('green', 'blue', 'chreme');
            $scrollPoints = '';
            $i = 0;

            if(!empty($posts)) {
                foreach($posts as $post ) {
                    $dates[date('Y',strtotime($post->post_date))][] = $post;
                }
            }

            foreach ($dates as $year=>$array) {
                echo '<div class="event" id="'.$year.'"><div class="eventHeading '.$colors[$i++%3].'">'.$year.'</div><ul class="eventList">';
                foreach($array as $event) {
                    ?>
                    <li class="">
                        <span class="icon" title=""></span><?php echo htmlspecialchars($event->post_title); ?>
                        <div class="content">
                            <div class="body">
                                <div>
                                    <?php echo $post->post_content; ?>
                                </div>
                                <div style="text-align:center">
                                    <img src="" alt="Image" />
                                    <a href="#">View All Photos</a>
                                </div>
                            </div>
                            <div class="title"><?php echo htmlspecialchars($event->post_title); ?></div>
                            <div class="date"><?php echo date("F j, Y",strtotime($event->post_date)); ?></div>
                            <a href="#" class="close">Close</a>
                        </div>
                    </li>

                    <div id="dialog-message">
                        <div id="windowBox">
                            <a href="#" class="close">CLOSE<img src="../img/icons/69.png" border="0" width="20px;" /></a>
                            <div id="titleDiv"><?php echo $event->post_title; ?></div>
                            <?php echo $event->post_content; ?>
                            <div id="date"><?php echo $event->post_date; ?></div>
                        </div>
                    </div>
                <?php
                }
                echo '</ul></div>';
                $scrollPoints.='<div class="scrollPoints"><a href="#'.$year.'">'.$year.'</a></div>';
                $i++;
            }
            ?>

            <div class="clear"></div>
        </div>

        <div id="scroll">
            <div id="centered">
                <div id="highlight"></div>
                <?php echo $scrollPoints ?>
                <div class="clear"></div>
            </div>
        </div>
        <div id="slider">
            <div id="bar">
                <div id="barLeft"></div>
                <div id="barRight"></div>
            </div>
        </div>

    </div>
</div>

    <script type="text/javascript">
        $(function () {
            $('.eventList #dialog-message').each(function(){
                $(this).hide();
            });

            $('.eventList li').click(function(e){
                $("#dialog-message").dialog({
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    },
                    width: 500,
                    height: 400
                });

                $(document).scroll(function (e) {

                    if ($(".ui-widget-overlay")) {
                        $(".ui-widget-overlay").css({
                            position: 'fixed',
                            top: '0'
                        });

                        pos = $(".ui-dialog").position();

                        $(".ui-dialog").css({
                            position: 'fixed',
                            top: pos.y
                        });
                    }
                });
            });

        });
    </script>

<?php } ?>