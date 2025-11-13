<?php

class Salaryincrease extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // load leave helper
        $this->load->helper('leave');
    }

    function index($emp_id = '', $date_from = '', $date_to = '')
    {
        // show all php errors
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (!empty($date_from) && !empty($date_to)) {
            $date_from = date('Y-m-d', strtotime($date_from));
            $date_to = date('Y-m-d', strtotime($date_to));
        } else {
            $date_from = date('Y-01-01');
            $date_to = date('Y-m-d');
        }
        $emp_id = !empty($emp_id) ? $this->mysecurity->decrypt_url($emp_id) : '';


        $emp_where = '';
        if ($this->_logindata['emp_level'] == 'employee') {
            $emp_where = " AND id = '{$this->_logindata['id']}'";
        }

        $data['employee'] = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_mname, emp_lname, emp_level, hiring_date FROM employees WHERE status != '3' $emp_where ORDER BY FIELD(status, 1, 0) DESC, emp_lname ASC, emp_fname ASC");


        $increase_where = '';
        if (!empty($emp_id)) {
            $increase_where = " AND s.employee_id = '$emp_id'";
        }


        $data['records'] = $this->model->getBySQL("SELECT s.*, e.emp_lname, e.emp_fname, ad.emp_lname AS added_by_lname, ad.emp_fname AS added_by_fname FROM employee_salary s LEFT JOIN employees AS e ON s.employee_id = e.id LEFT JOIN employees AS ad ON ad.id = s.added_by WHERE s.effective_date >= '$date_from' AND s.effective_date <= '$date_to' $increase_where ORDER BY s.effective_date DESC");

        $data['page_title'] = 'Salary Increase';
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['emp_id'] = $emp_id;
        $data['content'] = 'salaryincrease/index';
        $this->display($data);
    }

    public function getAttendanceDetails()
    {
        if (!IS_AJAX) show_404();
        $attendance_id = $this->mysecurity->decrypt_url($this->input->post('attendance_id'));

        if (empty($attendance_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Attendance ID is required.')));
        }

        $attendance = $this->model->getBySQL("SELECT a.date, a.employee_id, a.punch_in, a.punch_out, a.late, a.notes, a.type FROM attendance a WHERE a.id = '$attendance_id'", 'row');

        if (empty($attendance)) {
            die(json_encode([
                'status' => 'failed',
                'message' => 'Attendance record not found.'
            ]));
        }

        $attendance['date'] = date('l, F j, Y', strtotime($attendance['date']));
        $attendance['punch_in'] = !empty($attendance['punch_in']) ? date('D, M d, Y h:i A', strtotime($attendance['punch_in'])) : '';
        $attendance['punch_out'] = !empty($attendance['punch_out']) ? date('D, M d, Y h:i A', strtotime($attendance['punch_out'])) : '';

        $attendance_breaks = $this->employee_breaks($attendance['employee_id'], $attendance['date'], $attendance['date']);

        $attendance['breaks'] = [];
        $attendance['summary'] = [];
        $attendance['timeline'] = [];

        if (!empty($attendance_breaks)) {
            $attendance_breaks = is_string($attendance_breaks) ? json_decode($attendance_breaks, true) : $attendance_breaks;

            $total_breaks = 0;
            if (!empty($attendance_breaks['status']) && $attendance_breaks['status'] === 'success') {
                $attendance['breaks'] = $attendance_breaks['breaks'] ?? [];
                $attendance['summary'] = $attendance_breaks['summary'] ?? [];
                $attendance['timeline'] = $attendance_breaks['timeline'] ?? [];

                $total_breaks = $attendance_breaks['summary']['total_breaks_minutes'] ?? 0;
            }

            // total working hours 
            $shift_start = !empty($attendance['punch_in']) ? strtotime($attendance['punch_in']) : 0;
            $shift_end = !empty($attendance['punch_out']) ? strtotime($attendance['punch_out']) : 0;

            // total working hours calculation
            if ($shift_start && $shift_end) {
                $total_seconds = $shift_end - $shift_start;
                $total_minutes = floor($total_seconds / 60);

                $total_minutes -= $total_breaks; // subtract total breaks in minutes

                $attendance['total_working_hours_minutes'] = $total_minutes;
                $attendance['total_working_hours'] = sprintf('%02d Hrs %02d min', floor($total_minutes / 60), $total_minutes % 60);
            } else {
                $attendance['total_working_hours_minutes'] = 0;
                $attendance['total_working_hours'] = '00 Hrs 00 min';
            }
        }

        die(json_encode([
            'status' => 'success',
            'data' => $attendance
        ]));
    }

    private function employee_breaks($emp_id, $date_from = '', $date_to = '')
    {
        if (empty($emp_id)) {
            die(json_encode(['status' => 'failed', 'message' => 'Employee ID is required.']));
        }

        $timeclock = Modules::load('timeclock/Timeclock');

        $date_from = $date_from ? date('Y-m-d', strtotime($date_from)) : date('Y-m-d');
        $date_to   = $date_to ? date('Y-m-d', strtotime($date_to)) : $date_from;

        $days = get_days($date_from, $date_to, false);
        $breaks = [];

        foreach ($days as $day) {
            $schedule = $timeclock->emp_schedule($emp_id, "$day 22:00:00");
            if (!$schedule) continue;

            $start = $schedule['today_schedule_start'];
            $end   = $schedule['today_schedule_end'];

            $daily_breaks = $this->model->getBySQL(
                "SELECT * FROM employee_break WHERE employee_id = '$emp_id' AND date BETWEEN '$start' AND '$end'"
            );

            if (!empty($daily_breaks)) {
                $breaks = array_merge($breaks, $daily_breaks);
            }
        }

        if (empty($breaks)) {
            return json_encode(['status' => 'failed', 'message' => 'No breaks found for the specified date range.']);
        }

        // Group by break_type
        $grouped = [];
        foreach ($breaks as $break) {
            $type = $break['break_type'] ?? 'unknown';
            $grouped[$type][] = $break;
        }

        // Timeline construction
        $timeline = [];
        foreach ($breaks as $break) {
            $type = ucfirst($break['break_type']);
            if (!empty($break['break_start'])) {
                $timeline[] = [
                    'label' => "{$type} Start",
                    'time'  => date('M d, Y h:i A', strtotime($break['break_start'])),
                    'type'  => strtolower($break['break_type']),
                    'action' => 'start'
                ];
            }
            if (!empty($break['break_end'])) {
                $timeline[] = [
                    'label' => "{$type} End",
                    'time'  => date('M d, Y h:i A', strtotime($break['break_end'])),
                    'type'  => strtolower($break['break_type']),
                    'action' => 'end'
                ];
            }
        }

        // Sort timeline by time
        usort($timeline, fn($a, $b) => strtotime($a['time']) <=> strtotime($b['time']));

        // Summary with duration and over time
        $results = [];
        foreach (['lunch', 'break'] as $type) {
            $total_seconds = 0;

            if (!empty($grouped[$type])) {
                foreach ($grouped[$type] as $entry) {
                    $start = strtotime($entry['break_start']);
                    $end   = !empty($entry['break_end']) ? strtotime($entry['break_end']) : time();
                    $total_seconds += ($end - $start);
                }

                $total_minutes = floor($total_seconds / 60);
                $hours = floor($total_minutes / 60);
                $minutes = $total_minutes % 60;

                $formatted_total = trim(
                    ($hours > 0 ? "{$hours} Hr" . ($hours > 1 ? "s " : " ") : '') .
                        ($minutes > 0 ? "{$minutes} min" . ($minutes > 1 ? "s" : "") : '')
                );

                $threshold = $type === 'lunch' ? $this->_lunchtime : $this->_breaktime;
                $over_minutes = max(0, $total_minutes - $threshold);
                $formatted_over = sprintf('%02d min', $over_minutes);

                $results[$type] = [
                    'total_minutes'         => $total_minutes,
                    'total_formatted'       => $formatted_total,
                    'status'                => $over_minutes > 0 ? "over{$type}" : '',
                    'over_by_minutes'       => $over_minutes,
                    'over_by_formatted'     => $over_minutes > 0 ? $formatted_over : '',
                    // 'lti' => $over_minutes > 5 ? $over_minutes - 5 : 0,
                    'lti' => $over_minutes ? $over_minutes : 0,
                ];
            }
        }

        if (!empty($results)) {
            $results['total_over_by_minutes'] = array_sum(array_column($results, 'over_by_minutes'));
            $results['total_over_by_formatted'] = sprintf('%02d min', $results['total_over_by_minutes']);

            // total breaks lunch and break
            $results['total_breaks_minutes'] = array_sum(array_column($results, 'total_minutes'));
            $results['total_breaks_formatted'] = sprintf('%02d Hrs %02d min', floor($results['total_breaks_minutes'] / 60), $results['total_breaks_minutes'] % 60);

            // total lti from lunch and break
            $results['total_lti'] = array_sum(array_column($results, 'lti'));
            $results['total_lti_formatted'] = sprintf('%02d min', $results['total_lti']);
        }

        $return = [
            'status' => 'success',
            'breaks' => $grouped,
            'summary' => $results,
            'timeline' => $timeline
        ];

        return json_encode($return);
    }


    public function updateAttendance()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array('Remarks', 'Punch_OUT', 'Late', 'Punch_IN');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update attendance.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $attendance_id = $this->mysecurity->decrypt_url($formdata['attendance_id']);
            if (empty($attendance_id)) {
                $result['message'] = 'Attendance ID is required.';
            } else {
                $to_update = array(
                    'punch_in' => !empty($formdata['Punch_IN']) ? date('Y-m-d H:i:s', strtotime($formdata['Punch_IN'])) : null,
                    'punch_out' => !empty($formdata['Punch_OUT']) ? date('Y-m-d H:i:s', strtotime($formdata['Punch_OUT'])) : null,
                    'type' => !empty($formdata['attendance_type']) ? $formdata['attendance_type'] : '',
                    'late' => !empty($formdata['Late']) ? $formdata['Late'] : '00:00:00',
                    'notes' => !empty($formdata['Remarks']) ? $formdata['Remarks'] : '',
                );

                // if both punch_in and punch_out are null set as absent
                if (empty($to_update['punch_in']) && empty($to_update['punch_out'])) {
                    $to_update['absent'] = 'TRUE';
                    $to_update['late'] = '00:00:00'; // reset late to 00:00:00 if absent
                } else {
                    $to_update['absent'] = '';
                }

                if ($this->model->update('attendance', $to_update, "id='$attendance_id'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Attendance updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function deleteAttendance()
    {
        if (!IS_AJAX) show_404();
        $attendance_id = $this->mysecurity->decrypt_url($this->input->post('attendance_id'));

        if (empty($attendance_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Attendance ID is required.')));
        }

        if ($this->model->delete('attendance', "id='$attendance_id'")) {
            die(json_encode(array('status' => 'success', 'message' => 'Attendance deleted successfully.')));
        } else {
            die(json_encode(array('status' => 'failed', 'message' => 'Failed to delete attendance.')));
        }
    }

    public function addAttendance()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array('Punch_IN', 'Punch_OUT', 'Late', 'Remarks');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add attendance.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_insert = array(
                'employee_id' => !empty($formdata['Employee']) ? $this->mysecurity->decrypt_url($formdata['Employee']) : '',
                'date' => !empty($formdata['Date']) ? date('Y-m-d', strtotime($formdata['Date'])) : date('Y-m-d'),
                'shift_start' => !empty($formdata['Shift_Start']) ? date('H:i:s', strtotime($formdata['Shift_Start'])) : null,
                'shift_end' => !empty($formdata['Shift_End']) ? date('H:i:s', strtotime($formdata['Shift_End'])) : null,
                'punch_in' => !empty($formdata['Punch_IN']) ? date('Y-m-d H:i:s', strtotime($formdata['Punch_IN'])) : null,
                'punch_out' => !empty($formdata['Punch_OUT']) ? date('Y-m-d H:i:s', strtotime($formdata['Punch_OUT'])) : null,
                'type' => !empty($formdata['attendance_type']) ? $formdata['attendance_type'] : '',
                'late' => !empty($formdata['Late']) ? $formdata['Late'] : '00:00:00',
                'notes' => !empty($formdata['Remarks']) ? $formdata['Remarks'] : '',
            );

            // if both punch_in and punch_out are null set as absent
            if (empty($to_insert['punch_in']) && empty($to_insert['punch_out'])) {
                $to_insert['absent'] = 'TRUE';
                $to_insert['late'] = '00:00:00'; // reset late to 00:00:00 if absent
            } else {
                $to_insert['absent'] = '';
            }

            if ($this->model->insert('attendance', $to_insert)) {
                $result['status'] = 'success';
                $result['message'] = 'Attendance added successfully.';
            }
        }

        die(json_encode($result));
    }


    public function dummy()
    {
        exit;
        $emp_id = '10';
        $date_start = '2025-06-01';
        $date_end = '2025-06-25';
        $shift_start = '22:00:00';
        $shift_end = '07:00:00';

        $current = strtotime($date_start);
        $end = strtotime($date_end);

        while ($current <= $end) {
            $date = date('Y-m-d', $current);

            // skip weekends
            if (date('N', $current) >= 6) {
                $current = strtotime('+1 day', $current);
                continue;
            }

            // 0.5% chance of being late
            $is_late = (rand(1, 100) <= 30);
            $late_minutes = $is_late ? rand(1, 10) : 0;

            // Punch in (based on late)
            $punch_in_time = strtotime($date . ' 22:00:00') + ($late_minutes * 60);
            $punch_in = date('Y-m-d H:i:s', $punch_in_time);

            // Punch out next day between 07:00:00 and 07:05:00
            $punch_out_time = strtotime($date . ' +1 day 07:00:00') + rand(0, 300);
            $punch_out = date('Y-m-d H:i:s', $punch_out_time);

            $late_str = sprintf('00:%02d:00', $late_minutes);

            // $sql = "INSERT INTO attendance (id, employee_id, date, shift_start, shift_end, punch_in, punch_out, late, absent, notes) VALUES (NULL, '$emp_id', '$date', '$shift_start', '$shift_end', '$punch_in', '$punch_out', '$late_str', '', '');";

            $to_insert = array(
                'employee_id' => $emp_id,
                'date' => $date,
                'shift_start' => $shift_start,
                'shift_end' => $shift_end,
                'punch_in' => $punch_in,
                'punch_out' => $punch_out,
                'late' => $late_str,
                'absent' => '',
                'notes' => '',
            );

            if ($this->model->insert('attendance', $to_insert)) {
                echo "Inserted attendance for $date: Punch In: $punch_in, Punch Out: $punch_out, Late: $late_str\n";
            } else {
                echo "Failed to insert attendance for $date\n";
            }


            $current = strtotime('+1 day', $current);
        }
    }

    // Private functions

    private function groupAttendanceByEmployee($attendance, $date_current = '')
    {
        $date_current = !empty($date_current) ? date('Y-m', strtotime($date_current)) : date('Y-m');
        $grouped = [];
        if (empty($attendance)) return $grouped;

        foreach ($attendance as $record) {
            $total_lti = 0;

            $emp_id = $record['employee_id'];
            $date = $record['date'];
            if (!isset($grouped[$emp_id])) {
                $grouped[$emp_id] = [];
            }

            // check if employee has over break or over lunch
            $break_summary_json = $this->employee_breaks($emp_id, $date, $date);
            $break_summary_json = is_string($break_summary_json) ? json_decode($break_summary_json, true) : $break_summary_json;

            $over_break = false;
            if ($break_summary_json['status'] === 'success') {
                if (!empty($break_summary_json['summary']['lunch']['status']) && $break_summary_json['summary']['lunch']['status'] === 'overlunch') {
                    $over_break = true;
                }

                if (!empty($break_summary_json['summary']['break']['status']) && $break_summary_json['summary']['break']['status'] === 'overbreak') {
                    $over_break = true;
                }

                if (!empty($break_summary_json['summary']['total_lti'])) {
                    $total_lti = $break_summary_json['summary']['total_lti'];
                }
            }

            $record['total_lti'] = $total_lti;
            $record['over_break'] = $over_break;
            $grouped[$emp_id][$date] = $record;
        }

        return $grouped;
    }
}
