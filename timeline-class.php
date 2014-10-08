<?php
class Timeline extends Connect {
    public $dates = array();
    public $colors = array();
}

function displayTimelineClass() {
    $timeLine = new Timeline();

    $timeLine->setColumnName("posts");
    $query = "SELECT * FROM ".$timeLine->getPostPrefix()." WHERE post_type = 'life_calendar_events' AND post_status <> 'auto-draft' ORDER BY post_date ASC";
    $timeLinePosts = $timeLine->wpdb()->get_results($query, OBJECT);
    $dates = $timeLine->dates;

    $colors = $timeLine->colors = array('green', 'blue', 'chreme');
    ?>
<div id="main">
    <h1>Your time line</h1>

    <div class="timeline row">
        <?php
        $i = 0;

        if(!empty($timeLinePosts)) {
            foreach($timeLinePosts as $post ) {
                $dates[date('Y',strtotime($post->post_date))][] = $post;
            }
        }

        foreach ($dates as $year=>$array) {
            ?>
            <div class="event col-md-3" id="<?php echo $year; ?>">
                <div class="event-wrapper <?php echo $colors[$i++%3]; ?>"><?php echo $year; ?></div>
                <?php
                foreach($array as $event) {
                    ?>
                    <div class="event-title">
                        <p><?php echo $event->post_title; ?></p>
                    </div>
                    <div class="timeline-dialog-message" data-timeline-title="<?php echo $event->post_title; ?>">
                        <?php echo apply_filters('the_content', $event->post_content); ?>
                        <div class="date"><?php echo $event->post_date; ?></div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
            $i++;
        }
        ?>
    </div>
</div>

    <script type="text/javascript">
        (function ($) {
            $('.event.col-md-3 .event-title').each(function() {
                $.data(this, 'dialog',
                    $(this).next('.timeline-dialog-message').dialog({
                        autoOpen: false,
                        modal: true,
                        title: $(this).text(),
                        width: 600,
                        height: 'auto',
                        draggable: false
                    })
                );
            }).click(function() {
                $.data(this, 'dialog').dialog('open');
                return false;
            });
        }(jQuery));
    </script>

<?php } ?>