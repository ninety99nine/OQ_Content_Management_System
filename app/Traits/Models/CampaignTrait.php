<?php

namespace App\Traits\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait CampaignTrait
{
    /**
     * @param string $startDate e.g 2022-04-09
     * @param string $startTime e.g 06:00
     * @return Carbon
     */
    public function getCampaignStartDateTime($startDate = null, $startTime = null)
    {
        $startDate = $startDate != null ? Carbon::parse($startDate) : $this->start_date;
        $startTime = $startTime != null ? $startTime : $this->start_time;

        return $startDate->copy()->addHours(Str::before($startTime, ':'))->addMinutes(Str::after($startTime, ':'));
    }

    /**
     * @param string $startDate e.g 2022-04-09
     * @param string $startTime e.g 06:00
     * @return Carbon
     */
    public function getCampaignEndDateTime()
    {
        return $this->end_date->copy()->addHours(Str::before($this->end_time, ':'))->addMinutes(Str::after($this->end_time, ':'));
    }

    /**
     *  Suggest the next date and time to send a message
     *  to the subscriber based on the campaign settings
     */
    public function nextCampaignSmsMessageDate()
    {
        $startDate = $this->getCampaignStartDateTime();
        $endDate = $this->getCampaignEndDateTime();
        $suitableDate = null;
        $nextDate = null;
        $count = 0;

        /**
         *  Loop to find a date that satifies the campaign requirements.
         *  We will keep searching for a suitable date until we find it
         *  or until we reach 100 years from start date
         */
        while($suitableDate == null || (!is_null($nextDate) && $nextDate->diffInYears($startDate) <= 100 )) {

            //  Set the duration e.g (1, 2 or 3)
            $duration = ($this->recurring_duration * $count);
            $frequency = strtolower($this->recurring_frequency);

            /**
             *  On the first loop, we set the next date to the value of the start
             *  date. This makes sense if the start date is in the future and
             *  we want to send on exactly on that starting date before we
             *  can increment by days, weeks, months or year on that
             *  starting date. So first lets see if we can use the
             *  start date as the next date first.
             */

            if( $nextDate === null ) {

                //  Get the start date as the next date
                $nextDate = $startDate->copy();

            //  Lets try other dates
            }else{

                if( in_array($frequency, ['day', 'days']) ) {

                    //  Get the start date and add the number of days from start date
                    $nextDate = $startDate->copy()->addDays($duration);

                }elseif( in_array($frequency, ['week', 'weeks']) ) {

                    //  Get the start date and add the number of weeks from start date
                    $nextDate = $startDate->copy()->addWeeks($duration);

                }elseif( in_array($frequency, ['month', 'months']) ) {

                    //  Get the start date and add the number of months from start date
                    $nextDate = $startDate->copy()->addMonths($duration);

                }elseif( in_array($frequency, ['year', 'years']) ) {

                    //  Get the start date and add the number of years from start date
                    $nextDate = $startDate->copy()->addYears($duration);

                }elseif( in_array($frequency, ['hour', 'hours']) ) {

                    //  Get the start date and add the number of hours from start date
                    $nextDate = $startDate->copy()->addHours($duration);

                }elseif( in_array($frequency, ['minute', 'minutes']) ) {

                    //  Get the start date and add the number of hours from start date
                    $nextDate = $startDate->copy()->addMinutes($duration);

                }elseif( in_array($frequency, ['second', 'seconds']) ) {

                    //  Get the start date and add the number of hours from start date
                    $nextDate = $startDate->copy()->addSeconds($duration);

                }

            }

            /**
             *  Do we satisfy the must be in the future requirement?
             *
             *  If the date suggested is in the future of the current date
             *  then we can proceed. It does not make sense to suggest a
             *  date that is in the past of our current date (now). We
             *  cannot suggest this date to be the next date to send
             *  a message.
             */
            $isInTheFuture = $nextDate->greaterThan(now());

            if( $isInTheFuture ) {

                /**
                 *  Do we satisfy the start date requirement?
                 *
                 *  If the date suggested is exactly equal to the start date or
                 *  is in the future of the start date then we can proceed.
                 */
                $isWithinStartDate = $nextDate->greaterThanOrEqualTo($startDate);

                if( $isWithinStartDate ) {

                    /**
                     *  Do we satisfy the end date requirement?
                     *
                     *  If the date suggested is exactly equal to the end date or
                     *  is in the past of the end date then we can proceed.
                     */
                    $isWithinEndDate = $nextDate->lessThanOrEqualTo($endDate);

                    if( $isWithinEndDate ) {

                        /**
                         *  Do we satisfy the day of the week requirement?
                         *
                         *  If the date suggested is one of the days of the week or
                         *  if the days of the week are not specified then we pass
                         *  this requirement.
                         */
                        $isWithinDaysOfTheWeek = count($this->days_of_the_week ?? []) == 0 ||
                                                (!is_null($this->days_of_the_week) && collect( $this->days_of_the_week )->contains(
                                                    fn($day_of_the_week) => ($day_of_the_week == $nextDate->englishDayOfWeek))
                                                );

                        //  If this date is one of the permitted days of the week
                        if( $isWithinDaysOfTheWeek ) {

                            //  Set the suitable date
                            $suitableDate = $nextDate;

                            //  Stop, since we found the suitable date
                            break;

                        }

                    }else{

                        /**
                         *  If the suggested date is not exactly equal to the end date or
                         *  in the past of the end date, then every other date will have
                         *  the same outcome. Stop, since no date can ever be found.
                         */
                        break;

                    }

                }

            }

            //  Increment the count to try other dates
            ++$count;

        }

        //  Return the next suggested date
        return $suitableDate;

    }

    /**
     *  Determine whether or not we can start the
     *  campaign based on the campaign settings
     */
    public function canStartSmsCampaign()
    {
        $startDate = $this->getCampaignStartDateTime();
        $endDate = $this->getCampaignEndDateTime();

        /**
         *  Do we satisfy the start date requirement?
         *
         *  If the current date is exactly equal to the start date or
         *  is in the future of the start date then we can proceed.
         */
        $isWithinStartDate = now()->greaterThanOrEqualTo($startDate);

        if( $isWithinStartDate ) {

            /**
             *  Do we satisfy the end date requirement?
             *
             *  If the current date is exactly equal to the end date or
             *  is in the past of the end date then we can proceed.
             */
            $isWithinEndDate = now()->lessThanOrEqualTo($endDate);

            if( $isWithinEndDate ) {

                /**
                 *  Do we satisfy the day of the week requirement?
                 *
                 *  If the current date is one of the days of the week or
                 *  if the days of the week are not specified then we pass
                 *  this requirement.
                 */
                $isWithinDaysOfTheWeek = count($this->days_of_the_week ?? []) == 0 ||
                                        (!is_null($this->days_of_the_week) && collect( $this->days_of_the_week )->contains(
                                            fn($day_of_the_week) => ($day_of_the_week == now()->englishDayOfWeek))
                                        );

                //  If this date is one of the permitted days of the week
                if( $isWithinDaysOfTheWeek ) {

                    //  Return true that we can start the campaign
                    return true;

                }

            }

        }

        //  Return false that we cannot start the campaign
        return false;

    }
}
