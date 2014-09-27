<?php
class Calendar {

    public $firstDay;
    public $date;
    public $blank;

    protected $glob;

    private $_extraData = array();

    public function __construct() {
        global $wpdb;
        $this->glob = $wpdb;
    }

//    public function __get($proertyName) {
//        if(array_key_exists($proertyName, $this->_extraData)) {
//            return $this->_extraData[$proertyName];
//        } else {
//            return null;
//        }
//    }
//
//    public function __set($propertyName, $propertyValue) {
//        $this->_extraData[$propertyName] = $propertyValue;
//    }

    public function showWpdb() {
        return $this->glob;
    }

    private function getPrefix() {
        return $this->glob->prefix;
    }

    private function getPostPrefix() {
        return $this->getPrefix() . "posts";
    }

    private function getData() {
        $query = "
            SELECT * FROM ".$this->getPostPrefix()."
            WHERE post_type = 'life_calendar_events'
            AND post_status <> 'auto-draft'
        ";

        return $this->glob->get_results($query, OBJECT);
    }

    public function getPosts() {
        return $this->getData();
    }

    public function day() {
        return (isset($_GET['myDay']) != ""
            ? date('d', strtotime($_GET['myDay']))
            : date('d', $this->date));
    }

    public function month() {
        return (isset($_GET['myMonth']) != ""
            ? date('m', strtotime($_GET['myMonth']))
            : date('m', $this->date));
    }

    public function year() {
        return (isset($_GET['myYear']) != ""
            ? date('Y', strtotime($_GET['myYear']."-".$this->month()."-".$this->day()))
            : date('Y', $this->date));
    }

    public function dayOfWeek() {
        return date('D', $this->firstDay);
    }

    public function calTitle() {
        return date('F', $this->firstDay);
    }
}

function displayCalendarClass () {

    $cal = new Calendar();

    $cal->date = time();
    $day = $cal->day();
    $month = $cal->month();
    $year = $cal->year();
    $cal->firstDay = mktime(0, 0, 0, $month, 1, $year);
    $title = $cal->calTitle();
    $dayOfWeek = $cal->dayOfWeek();
    $daysInMonth = cal_days_in_month(0, $month, $year);
    $dayCount = $cal->dayCount = 1;
    $dayNum = $cal->dayNum = 1;
    $posts = $cal->getPosts();
    $prevMonth = date('F', strtotime(date('Y-m') . " -1 month"));

    switch ($dayOfWeek) {
        case "Sun":
            $cal->blank = 0;
            break;

        case "Mon":
            $cal->blank = 1;
            break;

        case "Tue":
            $cal->blank = 2;
            break;

        case "Wed":
            $cal->blank = 3;
            break;

        case "Thu":
            $cal->blank = 4;
            break;

        case "Fri":
            $cal->blank = 5;
            break;

        case "Sat":
            $cal->blank = 6;
            break;
    }

    //echo "<p>" . $cal->getPosts() . "</p>";

    echo "<table class=\"table table-striped\">";
    echo "<tr><th colspan=7> " . $title . " " . $year . " </th></tr>";
    echo "<tr><td width=42>S</td><td width=42>M</td><td width=42>T</td><td width=42>W</td><td width=42>T</td><td width=42>F</td><td width=42>S</td></tr>";
    echo "<tr>";

    while ($cal->blank > 0) {
        echo "<td></td>";
        $cal->blank = $cal->blank - 1;
        $dayCount++;
    }

    $results = array();
    if(!empty($posts)) {
        foreach($posts as $post ) {
            array_push($results, array(
                'postTitle' => $post->post_title,
                'postDate' => $post->post_date
            ));
        }
    }

    while ($dayNum <= $daysInMonth) {
        $today = ($dayNum == $day ? "today" : "");
        foreach($results as $result) {
            $postDay = date('j', strtotime($result['postDate']));
            $postMonth = date('m', strtotime($result['postDate']));
            $postYear = date('Y', strtotime($result['postDate']));
            if($dayNum == $postDay && $month == $postMonth && $year == $postYear) {
                echo '<td class="cal-day day-' . $dayNum . ' hover '.$today.'" data-calendar-event="'.$result['postDate'].'"> ' . $dayNum  . ' <p style="color:red;">' . $result['postTitle'] . '</p></td>';

                $dayNum++;
                $dayCount++;
                if ($dayCount > 7) {
                    echo "</tr><tr>";
                    $dayCount = 1;
                }
            }
        }

        echo '<td class="cal-day day-' . $dayNum . ' '.$today.'"> ' . $dayNum . '</td>';

        $dayNum++;
        $dayCount++;
        if ($dayCount > 7) {
            echo "</tr><tr>";
            $dayCount = 1;
        }
    }

    while ($dayCount > 1 && $dayCount <= 7) {
        echo "<td></td>";
        $dayCount++;
    }

    echo "</tr></table>";

    echo '<a href="'.get_permalink().'">Today</a>';

    echo '<br/>';

    echo '<a href="'.add_query_arg( 'myMonth', strtolower($prevMonth), get_permalink(5276) ).'">'.$prevMonth.'</a>';
?>
    <div id="dialog-message">we're here</div>
<script type="text/javascript">
    //jQuery.noConflict();
    $(function () {
        $('#dialog-message').hide();

            $('body').find('td.cal-day').click(function () {
                if($(this).data('calendarEvent') != undefined) {
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
                }
            });

    });
</script>
<?php
}