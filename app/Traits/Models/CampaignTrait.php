<?php

namespace App\Traits\Models;

use Carbon\Carbon;

trait CampaignTrait
{
    /**
     *  Get the next message date and time
     */
    public function nextMessageDate()
    {
        $nextDate = null;
        $nowDate = Carbon::now();
        $this->end_date = $this->end_date;
        $this->start_date = $this->start_date;

        //  Set the count
        $count = 1;

        //  Loop to find a date that satifies the campaign requirements
        while($nextDate == null) {

            //  Set the duration e.g (1, 2 or 3)
            $duration = ($this->recurring_duration * $count);

            if( $this->recurring_frequency == 'day' ) {

                //  Get the start date and add the number of days from now
                $date = $this->start_date->addDays($duration);

            }elseif( $this->recurring_frequency == 'week' ) {

                //  Get the start date and add the number of weeks from now
                $date = $this->start_date->addWeeks($duration);

            }elseif( $this->recurring_frequency == 'month' ) {

                //  Get the start date and add the number of months from now
                $date = $this->start_date->addMonths($duration);

            }elseif( $this->recurring_frequency == 'year' ) {

                //  Get the start date and add the number of years from now
                $date = $this->start_date->addYears($duration);

            }

            $isWithinStartDate = ($date->greaterThan($this->start_date) || is_null($this->start_date));
            $isWithinEndDate = ($date->lessThan($this->end_date) || is_null($this->end_date));

            //  If this date is within the permitted days and times
            if( $isWithinStartDate && $isWithinEndDate ) {

                $isWithinDaysOfTheWeek = is_null($this->days_of_the_week) ||
                                         collect( $this->days_of_the_week )->contains(fn($day_of_the_week) => ($day_of_the_week == $date->englishDayOfWeek));

                //  If this date is one of the permitted days of the week
                if( $isWithinDaysOfTheWeek ){

                    //  Return this date
                    return $date;

                }

            }

            //  Increment the count to try other dates
            ++$count;

        }
    }
}
