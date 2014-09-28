<?php
class Calendar extends Connect {

    public $blank;
    public $day;
    public $month;
    public $nextMonth;
    public $year;
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

    public function setDay($day) {
        $this->day = $day;
    }

    public function getDay() {
        return $this->day;
    }

    public function setMonth($month) {
        $this->month = $month;
    }

    public function getMonth() {
        return $this->month;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function getYear() {
        return $this->year;
    }

    public function getNextMonth() {
        return date('F', strtotime('first day of next month', strtotime($this->year."-".$this->month)));
    }

    public function getPrevMonth() {
        return date('F', strtotime('first day of previous month', strtotime($this->year."-".$this->month)));
    }

    public function getPrevYear() {
        return date('Y', strtotime('last day of -1 year', strtotime($this->year."-".$this->month)));
    }

    public function getNextYear() {
        return date('Y', strtotime('last day of +1 year', strtotime($this->year."-".$this->month)));
    }

}

function displayCalendarClass () {

    $cal = new Calendar();

    /*
     * Day class related
     */
    $currentDay = (isset($_GET['myDay']) != "" ? date('d', strtotime($_GET['myDay'])) : date('d'));
    $cal->setDay($currentDay);
    $getDay = $cal->getDay();

    /*
     * Month class related
     * todo: check if "myMonth" is int, if it is convert it to
     */
    $currentMonth = (isset($_GET['myMonth']) != "" ? date('m', strtotime($_GET['myMonth'])) : date('m'));
    $cal->setMonth($currentMonth);
    $cal->getMonth();
    $cal->month = $cal->getMonth();
    $getMonth = $cal->getMonth();

    /*
     * Year class related
     */
    $currentYear = (isset($_GET['myYear']) != "" ? date('Y', strtotime($_GET['myYear']."-".$cal->month)) : date('Y'));
    $cal->setYear($currentYear);
    $getYear = $cal->getYear();
    $getNextYear = $cal->getNextYear($currentYear, $currentMonth);

    /*
     * Extra data related
     */
    $cal->firstDay = mktime(0, 0, 0, $getMonth, 1, $getYear);
    $title = $cal->calTitle = date('F', $cal->firstDay);
    $dayOfWeek = $cal->dayOfTheWeek = date('D', $cal->firstDay);
    $daysInMonth = cal_days_in_month(0, $getMonth, $getYear);
    $dayCount = $cal->dayCount = 1;
    $dayNum = $cal->dayNum = 1;

    /*
     * Pagination
     */
    $getNextMonth = $cal->getNextMonth();
    $getPrevMonth = $cal->getPrevMonth();
    $getPrevYear = $cal->getPrevYear();
    $getNextYear = $cal->getNextYear();
    $getYearMonth = date('F', strtotime($cal->year."-".$cal->month));
    $ifYearExists = (isset($_GET['myYear']) ? "&myYear=" . $_GET['myYear'] : "");

    /*
     * Get posts
     */
    $cal->setColumnName("posts");
    $query = " SELECT * FROM ".$cal->getPostPrefix()." WHERE post_type = 'life_calendar_events' AND post_status <> 'auto-draft'";
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

    echo "<table class=\"table table-striped\">";
    echo "<tr><th colspan=7> " . $title . " " . $getYear . " </th></tr>";
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
        $today = ($dayNum == $getDay && $getYear == date('Y') && $getMonth == date('m') ? "today" : "");
        foreach($results as $result) {
            $postDay = date('j', strtotime($result['postDate']));
            $postMonth = date('m', strtotime($result['postDate']));
            $postYear = date('Y', strtotime($result['postDate']));
            if($dayNum == $postDay && $getMonth == $postMonth && $getYear == $postYear) {
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
?>
    <ul class="pagination">
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getYearMonth); ?>&myYear=<?php echo $getPrevYear; ?>">&lt;&lt;</a></li>
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getPrevMonth) . $ifYearExists; ?>">&lt;</a></li>
        <li><a href="<?php echo get_permalink(); ?>">Today</a></li>
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getNextMonth) . $ifYearExists; ?>">&gt;</a></li>
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getYearMonth); ?>&myYear=<?php echo $getNextYear; ?>">&gt;&gt;</a></li>
    </ul>

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