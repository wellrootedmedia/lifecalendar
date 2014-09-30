<?php

class Calendar extends Connect
{

    public $blank;
    public $day;
    public $month;
    public $nextMonth;
    public $year;
    public $dates = array();
    private $_extraData = array();

    public function __get($proertyName)
    {
        if (array_key_exists($proertyName, $this->_extraData)) {
            return $this->_extraData[$proertyName];
        } else {
            return null;
        }
    }

    public function __set($propertyName, $propertyValue)
    {
        $this->_extraData[$propertyName] = $propertyValue;
    }

    public function setDay($day)
    {
        $this->day = $day;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function setMonth($month)
    {
        $this->month = $month;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getNextMonth()
    {
        return date('F', strtotime('first day of next month', strtotime($this->year . "-" . $this->month)));
    }

    public function getPrevMonth()
    {
        return date('F', strtotime('first day of previous month', strtotime($this->year . "-" . $this->month)));
    }

    public function getPrevYear()
    {
        return date('Y', strtotime('last day of -1 year', strtotime($this->year . "-" . $this->month)));
    }

    public function getNextYear()
    {
        return date('Y', strtotime('last day of +1 year', strtotime($this->year . "-" . $this->month)));
    }

}

function displayCalendarClass()
{

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
    $currentYear = (isset($_GET['myYear']) != "" ? date('Y', strtotime($_GET['myYear'] . "-" . $cal->month)) : date('Y'));
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
    $dates = $cal->dates;

    /*
     * Pagination
     */
    $getNextMonth = $cal->getNextMonth();
    $getPrevMonth = $cal->getPrevMonth();
    $getPrevYear = $cal->getPrevYear();
    $getNextYear = $cal->getNextYear();
    $getYearMonth = date('F', strtotime($cal->year . "-" . $cal->month));
    $ifYearExists = (isset($_GET['myYear']) ? "&myYear=" . $_GET['myYear'] : "");

    /*
     * Get posts
     */
    $cal->setColumnName("posts");
    $query = " SELECT * FROM " . $cal->getPostPrefix() . " WHERE post_type = 'life_calendar_events' AND post_status <> 'auto-draft' AND post_status <> 'trash'";
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
    ?>
    <table class="table">
        <tr>
            <th colspan=7><?php echo $title; ?> <?php echo $getYear; ?></th>
        </tr>
        <tr>
            <td class="row-day">
                <div class="dark-gray">Sun</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Mon</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Tue</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Wed</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Thur</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Fri</div>
            </td>
            <td class="row-day">
                <div class="dark-gray">Sat</div>
            </td>
        </tr>
        <tr>
            <?php
            while ($cal->blank > 0) {
                ?>
                <td class="empty-cell"></td>
                <?php
                $cal->blank = $cal->blank - 1;
                $dayCount++;
            }

            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $dates[date('Y', strtotime($post->post_date))][] = $post;
                }
            }

            if (!empty($dates)) {
                foreach ($dates as $array) {
                    while ($dayNum <= $daysInMonth) {
                        $today = ($dayNum == $getDay && $getYear == date('Y') && $getMonth == date('m') ? "today" : "");
                        ?>
                        <td class="cal-day day-<?php echo $dayNum; ?> <?php echo $today; ?>">
                            <div class="event-cell-wrapper">
                                <div class="event-cell-date">
                                    <?php echo $dayNum; ?>
                                </div>
                                <?php
                                foreach ($array as $event) {
                                $postDay = date('j', strtotime($event->post_date));
                                $postMonth = date('m', strtotime($event->post_date));
                                $postYear = date('Y', strtotime($event->post_date));
                                if ($dayNum == $postDay && $getMonth == $postMonth && $getYear == $postYear) {
                                ?>
                                <div class="event-cell-content">
                                    <a class="event" href="">
                                        <?php echo substr($event->post_title, 0, 10) . '...'; ?>
                                    </a>

                                    <div class="calendar-dialog-message" data-calendar-event="<?php echo $event->post_date; ?>"
                                         data-calendar-title="<?php echo $event->post_title; ?>">
                                        <?php echo $event->post_content; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                            </div>
                            <?php
                            }
                            ?>
                            </div>
                        </td>
                        <?php
                        $dayNum++;
                        $dayCount++;
                        if ($dayCount > 7) {
                        ?>
                            </tr>
                            <tr>
                        <?php
                        $dayCount = 1;
                        }
                    }
                }
            } else {
                while($dayNum <= $daysInMonth) {
                    $today = ($dayNum == $getDay && $getYear == date('Y') && $getMonth == date('m') ? "today" : "");
                    ?>
                        <td class="cal-day day-<?php echo $dayNum; ?> <?php echo $today; ?>">
                            <div class="event-cell-wrapper">
                                <div class="event-cell-date">
                                    <?php echo $dayNum; ?>
                                </div>
                            </div>
                        </td>
                    <?php
                }
            }

            while ($dayCount > 1 && $dayCount <= 7) {
                ?>
                <td class="empty-cell"></td>
                <?php
                $dayCount++;
            }
            ?>
        </tr>
    </table>

    <ul class="pagination">
        <li>
            <a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getYearMonth); ?>&myYear=<?php echo $getPrevYear; ?>">
                &lt;&lt;</a></li>
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getPrevMonth) . $ifYearExists; ?>">
                &lt;</a></li>
        <li><a href="<?php echo get_permalink(); ?>">Today</a></li>
        <li><a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getNextMonth) . $ifYearExists; ?>">
                &gt;</a></li>
        <li>
            <a href="<?php get_permalink(); ?>?myMonth=<?php echo strtolower($getYearMonth); ?>&myYear=<?php echo $getNextYear; ?>">
                &gt;&gt;</a></li>
    </ul>

    <script type="text/javascript">
        $(function () {
            $('.event-cell-content .event').each(function () {
                $.data(this, 'dialog',
                    $(this).next('.calendar-dialog-message').dialog({
                        autoOpen: false,
                        modal: true,
                        title: $(this).data('calendarTitle'),
                        maxWidth: 600,
                        height: 'auto',
                        draggable: false
                    })
                );
            }).click(function () {
                console.log("here");
                $.data(this, 'dialog').dialog('open');
                return false;
            });
        });
    </script>
<?php
}