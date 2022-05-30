<?php //roffy_pre($holidays); ?>

<div class="month">      
    <ul>
        <li id="prevMonth" class="prev">&#10094;</li>
        <li id="nextMonth" class="next">&#10095;</li>
        <li>
            <?php echo $month['name'] ." ". $month['year']; ?>
        </li>
    </ul>
</div>

<ul class="weekdays">
    <li>Mo</li>
    <li>Tu</li>
    <li>We</li>
    <li>Th</li>
    <li>Fr</li>
    <li>Sa</li>
    <li>Su</li>
    </ul>

    <ul class="days">
        <?php

            $i = 1;

            do{
                if($i == $month['start']){break;}
                else{echo "<li></li>";}
                $i++;
            }while(TRUE);


            $day = 1;

            while($day <= $month['days']){
                $nohol = TRUE;

                foreach($holidays as $hol){
                    if($hol['month'] == $month['month'] && $hol['year'] == $month['year']){
                        if($hol['day'] == $day){
                            if($hol['time'] != 'AD'){
                                $class = 'active '.strtolower($hol['time']);
                            }
                            else{
                                $class = 'active';
                            }
                            echo "<li class='".$class."' data-date='".$hol['year']."-".$hol['month']."-".$hol['day']."' data-toggle='modal' data-target='#viewHolModal'>".$day."</li>";
                            $nohol = FALSE;
                            break;
                        }
                    }


                }

                if($nohol){
                    echo "<li>".$day."</li>";
                }

                $day++;
            }

        ?>
</ul>

<?php //roffy_pre($holidays); ?>
