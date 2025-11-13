<?php

class Timeclock extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // if (!IS_AJAX) show_404();
    }

    public function get_timeclock()
    {
        if (!IS_AJAX) show_404();

        $data['current_time'] = date('h:i:s A');
        $data['current_date'] = date('l F d, Y');

        $current_day = date('Y-m-d');
        $current_schedule = $this->emp_schedule($this->_logindata['id'], date('Y-m-d H:i:s'));

        $current_attendance = $this->model->getBySQL("SELECT punch_in, punch_out, late FROM attendance WHERE employee_id = '{$this->_logindata['id']}' AND (punch_in BETWEEN '$current_schedule[today_schedule_start]' AND '$current_schedule[today_schedule_end]') LIMIT 1", 'row');

        $data['punch_in'] = $punch_in  = !empty($current_attendance['punch_in'])  ? date('h:i:s A', strtotime($current_attendance['punch_in']))  : '';
        $data['punch_out'] = $punch_out = !empty($current_attendance['punch_out']) ? date('h:i:s A', strtotime($current_attendance['punch_out'])) : '';
        if (!empty($current_attendance['late'])) {
            $late_seconds = strtotime($current_attendance['late']) - strtotime('TODAY');
            $late_hours = floor($late_seconds / 3600);
            $late_minutes = floor(($late_seconds % 3600) / 60);
            $late_formatted = sprintf('%02d Hrs %02d min', $late_hours, $late_minutes);
        } else {
            $late_formatted = '';
        }
        $data['late'] = $late_formatted;

        // get last break
        $last_break = $this->model->getBySQL("SELECT id, break_start, break_end FROM employee_break WHERE break_type = 'break' AND employee_id = '{$this->_logindata['id']}' AND (date BETWEEN '$current_schedule[today_schedule_start]' AND '$current_schedule[today_schedule_end]') ORDER BY id DESC");

        $break_count = 0;
        if (!empty($last_break)) {
            $total_break = 0;
            foreach ($last_break as $break) {
                if (!empty($break['break_start'])) {
                    $break_end = !empty($break['break_end']) ? $break['break_end'] : date('Y-m-d H:i:s');
                    $diff_seconds = strtotime($break_end) - strtotime($break['break_start']);
                    $total_break += $diff_seconds;
                }

                // count only completed breaks
                if (!empty($break['break_end'])) {
                    $break_count++;
                }
            }

            $hours = floor($total_break / 3600);
            $minutes = floor(($total_break % 3600) / 60);
            $total_break_formatted = sprintf('%02d Hrs %02d min', $hours, $minutes);
            $data['total_break'] = $total_break_formatted;

            $data['overbreak'] = $total_break >= $this->_breaktime * 60 ? 'overbreak' : '';
        }

        // return break count
        $data['break_count'] = $break_count > 0 ? addOrdinalSuffix($break_count + 1) : '';

        // return last break
        if (!empty($last_break[0]['break_start']) && !empty($last_break[0]['break_end'])) {
            // Both are filled, so return empty for the 2nd break
            $data['break_start'] = '';
            $data['break_end'] = '';
        } else {
            $data['break_start'] = !empty($last_break[0]['break_start']) ? date('h:i:s A', strtotime($last_break[0]['break_start'])) : '';
            $data['break_end'] = !empty($last_break[0]['break_end']) ? date('h:i:s A', strtotime($last_break[0]['break_end'])) : '';
        }

        // get last lunch break
        $last_lunch = $this->model->getBySQL("SELECT id, break_start, break_end FROM employee_break WHERE break_type = 'lunch' AND employee_id = '{$this->_logindata['id']}' AND (date BETWEEN '$current_schedule[today_schedule_start]' AND '$current_schedule[today_schedule_end]') ORDER BY id DESC");
        if (!empty($last_lunch)) {
            $total_lunch = 0;
            foreach ($last_lunch as $lunch) {
                if (!empty($lunch['break_start'])) {
                    $lunch_end = !empty($lunch['break_end']) ? $lunch['break_end'] : date('Y-m-d H:i:s');
                    $diff_seconds = strtotime($lunch_end) - strtotime($lunch['break_start']);
                    $total_lunch += $diff_seconds;
                }
            }

            $hours = floor($total_lunch / 3600);
            $minutes = floor(($total_lunch % 3600) / 60);
            $total_lunch_formatted = sprintf('%02d Hrs %02d min', $hours, $minutes);
            $data['total_lunch'] = $total_lunch_formatted;

            // if 1 hour or more, show overlunch
            $data['overlunch'] = $total_lunch >= $this->_lunchtime * 60 ? 'overlunch' : '';
        }

        // return last lunch break
        $data['lunch_start'] = !empty($last_lunch[0]['break_start']) ? date('h:i:s A', strtotime($last_lunch[0]['break_start'])) : '';
        $data['lunch_end'] = !empty($last_lunch[0]['break_end']) ? date('h:i:s A', strtotime($last_lunch[0]['break_end'])) : '';

        $total_working_hours = 0;
        if ($punch_in) {
            $diff_seconds = strtotime($punch_out) - strtotime($punch_in);
            $total_working_hours = gmdate('H:i:s', $diff_seconds);
        }
        $data['total_working_hours'] = $total_working_hours;

        die(json_encode($data));
    }

    public function punch($force = '')
    {
        $current_date = date('Y-m-d H:i:s');
        $current_day = date('Y-m-d');
        $current_schedule = $this->emp_schedule($this->_logindata['id'], $current_date);

        $return = ['status' => 'error', 'message' => 'Invalid request',];

        // check if attendance already exists
        $check_att = $this->model->getBySQL("SELECT id, punch_in, punch_out, shift_start FROM attendance WHERE employee_id = '{$this->_logindata['id']}' AND DATE(`date`) = '{$current_schedule['schedule_from']}' LIMIT 1", 'row');

        $punch = !empty($check_att) && !empty($check_att['id']) ? 'punchout' : 'punchin';
        if ($punch == 'punchin') {
            $data_insert = array(
                'employee_id' => $this->_logindata['id'],
                'date' => $current_schedule['schedule_from'],
                'shift_start' => $current_schedule['schedule_start'],
                'shift_end' => $current_schedule['schedule_end'],
                'punch_in' => date('Y-m-d H:i:s'),
            );

            // calculate late based on shift start time
            $late = 0;
            if (!empty($current_schedule['schedule_start'])) {
                $late = strtotime($current_date) - strtotime($current_schedule['schedule_from'] . ' ' . $current_schedule['schedule_start']);
                if ($late > 0) {
                    $late = round($late / 60);
                } else {
                    $late = 0;
                }
            }

            // show late as H:i:00
            $data_insert['late'] = gmdate('H:i:s', $late * 60);

            // save attendance
            if ($this->model->insert('attendance', $data_insert)) {
                $return = [
                    'status' => 'success',
                    'message' => 'You have successfully punched in.',
                    'punch' => 'punchin',
                    'late' => gmdate('H:i:s', $late * 60),
                    'punch_in' => date('H:i:s'),
                ];
            } else {
                $return = [
                    'status' => 'error',
                    'message' => 'Unable to punch in. Please try again.',
                ];
            }
        } else {

            $wh_punch_in = ($check_att['punch_in'] < $check_att['shift_start']) ? $check_att['shift_start'] : $check_att['punch_in'];

            // check if working hours is less than 8 hours
            $working_hours = 0;
            if (!empty($wh_punch_in) && (empty($force) || $force != 'force_punch')) {
                $diff_seconds = strtotime($current_date) - strtotime($wh_punch_in);
                $working_hours = gmdate('H:i:s', $diff_seconds);
                $working_hours = strtotime($working_hours) - strtotime('TODAY');

                // check if working hours is less than 8 hours return error
                if ($working_hours < 8 * 60 * 60) {
                    $return = [
                        'status' => 'work_hours_error',
                        'message' => 'You only worked for ' . gmdate('H:i:s', $working_hours) . '. Are you sure you want to punch out?',
                    ];
                    die(json_encode($return));
                }
            }

            $data_update = array(
                'punch_out' => date('Y-m-d H:i:s'),
            );

            if ($this->model->update('attendance', $data_update, array('id' => $check_att['id']))) {
                $return = [
                    'status' => 'success',
                    'message' => 'You have successfully punched out.',
                    'punch' => 'punchout',
                    'punch_out' => date('H:i:s'),
                    'punch_in' => date('H:i:s', strtotime($check_att['punch_in'])),
                ];
            } else {
                $return = [
                    'status' => 'error',
                    'message' => 'Unable to punch out. Please try again.',
                ];
            }
        }

        die(json_encode($return));
    }

    public function break($type = 'break')
    {
        $current_date = date('Y-m-d H:i:s');
        $current_schedule = $this->emp_schedule($this->_logindata['id'], $current_date);

        $return = ['status' => 'error', 'message' => 'Invalid request',];

        // check if attendance already exists
        $current_attendance = $this->model->getBySQL("SELECT punch_in, punch_out, late FROM attendance WHERE employee_id = '{$this->_logindata['id']}' AND (punch_in BETWEEN '$current_schedule[today_schedule_start]' AND '$current_schedule[today_schedule_end]') LIMIT 1", 'row');

        if ($current_attendance) {
            // check for last break
            $last_break = $this->model->getBySQL("SELECT id, break_start, break_end FROM employee_break WHERE (break_start IS NULL || break_end IS NULL) AND break_type = '$type' AND employee_id = '{$this->_logindata['id']}' AND (date BETWEEN '$current_schedule[today_schedule_start]' AND '$current_schedule[today_schedule_end]') ORDER BY id DESC LIMIT 1", 'row');

            if (empty($last_break)) {
                // punch in break
                $data_insert = array(
                    'employee_id' => $this->_logindata['id'],
                    'date' => date('Y-m-d H:i:s'),
                    'break_start' => date('Y-m-d H:i:s'),
                    'break_type' => $type,
                );

                if ($this->model->insert('employee_break', $data_insert)) {
                    $return = [
                        'status' => 'success',
                        'message' => "You have started your {$type}.",
                        'punch' => "{$type}in",
                    ];
                } else {
                    $return = [
                        'status' => 'error',
                        'message' => "Unable to punch in for {$type}. Please try again.",
                    ];
                }
            } else {
                // punch out break
                $data_update = array(
                    'break_end' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_break', $data_update, array('id' => $last_break['id']))) {
                    $return = [
                        'status' => 'success',
                        'message' => "You have successfully ended your {$type}.",
                        'punch' => "{$type}out",
                    ];
                } else {
                    $return = [
                        'status' => 'error',
                        'message' => "Unable to punch out for {$type}. Please try again.",
                    ];
                }
            }
        }

        die(json_encode($return));
    }


    public function emp_schedule($emp_id, $datetime)
    {
        $emp_id   = $this->db->escape_str($emp_id);
        $datetime = $this->db->escape_str($datetime);

        //
        // Normalize: 00:00–06:59 belongs to the previous shift day
        //
        $timePart = date('H:i:s', strtotime($datetime));
        if ($timePart < '11:00:00') {
            $lookupDatetime = date('Y-m-d H:i:s', strtotime("$datetime -1 day"));
        } else {
            $lookupDatetime = $datetime;
        }

        //
        // SQL query (simplified)
        //
        $sql = "
        SELECT
            schedule_start,
            schedule_end,
            schedule_from,
            schedule_to,

            DATE_SUB(CONCAT(schedule_from, ' ', schedule_start), INTERVAL 4 HOUR) AS date_start,
            DATE_ADD(CONCAT(COALESCE(schedule_to, schedule_from), ' ', schedule_end), INTERVAL 4 HOUR) AS date_end
        FROM employee_schedule
        WHERE employee_id = '{$emp_id}'
        HAVING '{$lookupDatetime}' BETWEEN date_start AND date_end
        ORDER BY id DESC
        LIMIT 1
    ";

        $query = $this->model->getBySQL($sql, 'row');
        if (!empty($query)) {

            // Build the missing calculated fields
            $baseDate = date('Y-m-d', strtotime($query->schedule_from));
            $nextDate = date('Y-m-d', strtotime($query->schedule_to));
            $prevDate = date('Y-m-d', strtotime("$baseDate -1 day"));

            $start = $query->schedule_start;
            $end   = $query->schedule_end;

            return [
                'schedule_start' => $start,
                'schedule_end'   => $end,
                'schedule_from'  => $query->schedule_from,
                'schedule_to'    => $query->schedule_to,

                'date_start'     => $query->date_start,
                'date_end'       => $query->date_end,

                'yesterday_schedule_start' => date('Y-m-d H:i:s', strtotime("$prevDate $start -4 hours")),
                'yesterday_schedule_end'   => date('Y-m-d H:i:s', strtotime("$baseDate $end +4 hours")),

                'today_schedule_start'     => date('Y-m-d H:i:s', strtotime("$baseDate $start -4 hours")),
                'today_schedule_end'       => date('Y-m-d H:i:s', strtotime("$nextDate $end +4 hours")),
            ];
        }

        //
        // DEFAULT FALLBACK NIGHT SHIFT (22:00 → 07:00)
        //

        $actualStart = '22:00:00';
        $actualEnd   = '07:00:00';

        $baseDate = date('Y-m-d', strtotime($lookupDatetime));
        $nextDate = date('Y-m-d', strtotime("$baseDate +1 day"));
        $prevDate = date('Y-m-d', strtotime("$baseDate -1 day"));

        // Extended schedule
        $dateStart = date('Y-m-d H:i:s', strtotime("$baseDate $actualStart -4 hours")); // 18:00
        $dateEnd   = date('Y-m-d H:i:s', strtotime("$nextDate $actualEnd +4 hours"));   // 11:00

        return [
            'schedule_start' => $actualStart,
            'schedule_end'   => $actualEnd,
            'schedule_from'  => $baseDate,
            'schedule_to'    => $nextDate,

            'date_start'     => $dateStart,
            'date_end'       => $dateEnd,

            'yesterday_schedule_start' => date('Y-m-d H:i:s', strtotime("$prevDate $actualStart -4 hours")),
            'yesterday_schedule_end'   => date('Y-m-d H:i:s', strtotime("$baseDate $actualEnd +4 hours")),

            'today_schedule_start'     => $dateStart,
            'today_schedule_end'       => $dateEnd,
        ];
    }
}
