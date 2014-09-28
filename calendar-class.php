<?php
class Calendar extends Connect {

    public $firstDay;
    public $date;
    public $blank;
    private $_extraData = array();

    public function __get($proertyName) {
        if(array_key_exists($proertyName, $this->_extraData)) {
            return $this->_extraData[$proertyName];
        } else {
            return null;
        }
    }

    public function __set($propertyName, $propertyValue) {
        $this->_extraData[$propertyName] = $propertyValue;
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
    $prevMonth = date('F', strtotime(date('Y-m') . " -1 month"));

    $cal->setColumnName("posts");
    $query = "
            SELECT * FROM ".$cal->getPostPrefix()."
            WHERE post_type = 'life_calendar_events'
            AND post_status <> 'auto-draft'
        ";
    $posts = $cal->wpdb()->get_results($query, OBJECT);

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
                'postDate' => $post->post_date,
                'postContent' => $post->post_content
            ));
        }
    }

    while ($dayNum <= $daysInMonth) {
        $today = ($dayNum == $day && $year == date('Y') && $month == date('m') ? "today" : "");
        foreach($results as $result) {
            $postDay = date('j', strtotime($result['postDate']));
            $postMonth = date('m', strtotime($result['postDate']));
            $postYear = date('Y', strtotime($result['postDate']));
            if($dayNum == $postDay && $month == $postMonth && $year == $postYear) {
                ?>
                <td class="cal-day day-<?php echo $dayNum; ?> hover <?php echo $today; ?>" data-calendar-event="<?php echo $result['postDate']; ?>">
                    <?php echo $dayNum; ?>
                    <p style="color:red;"><?php echo substr($result['postTitle'],0,10).'...'; ?></p>
                    <div id="calendar-dialog-message" data-calendar-title="<?php echo $result['postTitle']; ?>"><?php echo $result['postContent']; ?></div>
                </td>
                <?php
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

<script type="text/javascript">
    $(function () {
        $('#calendar-dialog-message').hide();

            $('body').find('td.cal-day').click(function () {
                if($(this).data('calendarEvent') != undefined) {
                    $("#calendar-dialog-message").dialog({
                        modal: true,
                        title: $(this).find('#calendar-dialog-message').data("calendarTitle"),
                        buttons: {
                            Ok: function () {
                                $(this).dialog("close");
                            }
                        },
                        width: 500,
                        height: 400
                    });
                }
            });

    });
</script>
<?php
}