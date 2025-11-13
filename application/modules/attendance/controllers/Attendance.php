<?php

class Attendance extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // load leave helper
        $this->load->helper('leave');
    }

    function index($date = '')
    {
        // Fetch all active employees (status != 3), sorted by status then by name
        $data['employee'] = $employee = $this->model->getBySQL(" SELECT id, emp_id, emp_fname, emp_mname, emp_lname, emp_level, hiring_date, rest_day FROM employees WHERE status != '3' ORDER BY FIELD(status, 1, 0) DESC, emp_lname ASC, emp_fname ASC ");

        // Extract and sanitize employee IDs for use in attendance query
        $employee_id = implode(',', array_map('intval', array_column($employee, 'id')));

        // Validate input $date in 'Y-m' format; fallback to current month if invalid
        if (!empty($date)) {
            $d = DateTime::createFromFormat('Y-m', $date);
            $isValid = $d && $d->format('Y-m') === $date;
            $date = $isValid ? $date : date('Y-m');
        } else {
            $date = date('Y-m');
        }

        // Fetch attendance records for the month, including schedule info
        $attendance = $this->model->getBySQL(" SELECT a.*, s.schedule_start, s.schedule_end FROM attendance a LEFT JOIN employee_schedule s ON a.employee_id = s.employee_id AND a.date >= s.schedule_from AND (s.schedule_to IS NULL OR a.date <= s.schedule_to) WHERE a.date LIKE '$date%' AND a.employee_id IN ($employee_id) ORDER BY a.date DESC ");

        // Group attendance records by employee ID and date
        $attendance = $this->groupAttendanceByEmployee($attendance, $date);
        $data['attendance'] = $attendance;

        // Set additional view data
        $data['searchDate'] = date('Y-m', strtotime($date));
        $data['page_title'] = 'Attendance';
        $data['content'] = 'attendance/index';

        // Render the page
        $this->display($data);
    }

    function weekssummary()
    {
        if (!IS_AJAX) show_404();
        $month = !empty($this->input->post('month')) ? $this->input->post('month') : date('Y-m');

        $employee = $this->model->getBySQL(" SELECT id, emp_id, emp_fname, emp_mname, emp_lname, emp_level, hiring_date, rest_day FROM employees WHERE status != '3' ORDER BY FIELD(status, 1, 0) DESC, emp_lname ASC, emp_fname ASC ");

        // Extract and sanitize employee IDs for use in attendance query
        $employee_id = implode(',', array_map('intval', array_column($employee, 'id')));

        // Fetch attendance records for the month, including schedule info
        $attendance = $this->model->getBySQL(" SELECT a.*, s.schedule_start, s.schedule_end FROM attendance a LEFT JOIN employee_schedule s ON a.employee_id = s.employee_id AND a.date >= s.schedule_from AND (s.schedule_to IS NULL OR a.date <= s.schedule_to) WHERE a.date LIKE '$month%' AND a.employee_id IN ($employee_id) ORDER BY a.date DESC ");

        $weeks = getWeeksOfMonth($month);
        if (!empty($weeks)) {
            foreach ($weeks as $index => $week) {
                $week_days = get_days($week['start'], $week['end'], false);
                $total_possible_attendance = count($employee) * count($week_days);

                // Count absent for the week
                $weeks[$index]['absent_count'] = count(array_filter($attendance, function ($row) use ($week_days) {
                    return in_array($row['date'], $week_days) && ((empty($row['punch_in']) && empty($row['punch_out'])) || strtolower($row['type']) == 'absent' || strtoupper($row['absent']) == 'TRUE');
                }));

                // Count late for the week
                $weeks[$index]['late_count'] = count(array_filter($attendance, function ($row) use ($week_days) {
                    return in_array($row['date'], $week_days) && $row['late'] != '00:00:00';
                }));

                // Count undertime for the week (punch_in present but punch_out is null)
                $weeks[$index]['undertime_count'] = count(array_filter($attendance, function ($row) use ($week_days) {
                    return in_array($row['date'], $week_days) && !empty($row['punch_in']) && empty($row['punch_out']);
                }));

                // Count late and undertime combined (counted as one if both occur on same date)
                $weeks[$index]['late_undertime_count'] = count(array_filter($attendance, function ($row) use ($week_days) {
                    $is_late = in_array($row['date'], $week_days) && $row['late'] != '00:00:00';
                    $is_undertime = in_array($row['date'], $week_days) && !empty($row['punch_in']) && empty($row['punch_out']);
                    return $is_late || $is_undertime;
                }));

                // Calculate rates
                $absent_rate = 0;
                $late_undertime_rate = 0;

                if ($total_possible_attendance > 0) {
                    $absent_rate = ($weeks[$index]['absent_count'] / $total_possible_attendance) * 100;
                    $late_undertime_rate = ($weeks[$index]['late_undertime_count'] / $total_possible_attendance) * 100;
                }

                $weeks[$index]['absent_rate'] = number_format($absent_rate, 2) . '%';
                $weeks[$index]['late_undertime_rate'] = number_format($late_undertime_rate, 2) . '%';

                // format date start and end
                $weeks[$index]['start'] = date('M d, Y', strtotime($week['start']));
                $weeks[$index]['end'] = date('M d, Y', strtotime($week['end']));
            }
        }

        die(json_encode(['status' => 'success', 'data' => $weeks, 'employee_count' => count($employee)]));
    }

    function records($date_from = '', $date_to = '', $emp_id = '')
    {
        // Get filter parameters from GET request (similar to break monitoring)
        $employee_id = $this->input->get('employee_id');
        $date_from_get = $this->input->get('date_from');
        $date_to_get = $this->input->get('date_to');

        // Use GET parameters if available, otherwise use URL parameters (for backward compatibility)
        if (!empty($employee_id) || !empty($date_from_get) || !empty($date_to_get)) {
            $emp_id = $employee_id;
            $date_from = $date_from_get;
            $date_to = $date_to_get;
        }

        if (!empty($date_from) && !empty($date_to)) {
            $date_from = date('Y-m-d', strtotime($date_from));
            $date_to = date('Y-m-d', strtotime($date_to));
        } else {
            $date_from = date('Y-m-01');
            $date_to = date('Y-m-d');
        }

        $emp_id = !empty($emp_id) ? $this->mysecurity->decrypt_url($emp_id) : '';

        // Check if user has manage_attendance permission
        if (!check_function('manage_attendance')) {
            // If no permission, only show current user's records
            $current_user_employee_id = $this->session->userdata('employee_id');
            if ($current_user_employee_id) {
                $emp_id = $current_user_employee_id;
            }
        }

        $emp_where = '';
        if (!check_function('manage_attendance')) {
            $emp_where = " AND id = '{$this->_logindata['id']}'";
        }

        $data['employee'] = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_mname, emp_lname, emp_level, hiring_date FROM employees WHERE status != '3' $emp_where ORDER BY FIELD(status, 1, 0) DESC, emp_lname ASC, emp_fname ASC");

        // Build WHERE clause for attendance query
        $attendance_where = "";
        if (!empty($emp_id)) {
            $attendance_where = " AND a.employee_id = '$emp_id'";
        }

        $data['attendance'] = $this->model->getBySQL("SELECT a.*, ee.emp_lname, ee.emp_fname, s.schedule_start, s.schedule_end, CONCAT(add_u.emp_lname, ', ', add_u.emp_fname) AS added_full_name, CONCAT(upd_u.emp_lname, ', ', upd_u.emp_fname) AS updated_full_name FROM attendance a LEFT JOIN employees AS ee ON ee.id = a.employee_id LEFT JOIN employee_schedule s ON a.employee_id = s.employee_id AND a.date >= s.schedule_from AND ( s.schedule_to IS NULL OR a.date <= s.schedule_to ) LEFT JOIN employees add_u ON a.added_by = add_u.id LEFT JOIN employees upd_u ON a.updated_by = upd_u.id WHERE a.date >= '$date_from' AND a.date <= '$date_to' $attendance_where ORDER BY a.date DESC");

        // Pass filter values to view
        $data['selected_employee'] = $emp_id;
        $data['selected_date_from'] = $date_from;
        $data['selected_date_to'] = $date_to;

        $data['page_title'] = 'Attendance';
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['emp_id'] = $emp_id;
        $data['content'] = 'attendance/records';
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

        $attendance['punch_in_date'] = !empty($attendance['punch_in']) ? date('F d, Y h:i A', strtotime($attendance['punch_in'])) : '';
        $attendance['punch_out_date'] = !empty($attendance['punch_out']) ? date('F d, Y h:i A', strtotime($attendance['punch_out'])) : '';

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
                    'date_last_update' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->_logindata['id'],
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
                'date_added' => date('Y-m-d H:i:s'),
                'date_last_update' => date('Y-m-d H:i:s'),
                'added_by' => $this->_logindata['id'],
                'updated_by' => $this->_logindata['id'],
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

    public function uploadAttendance()
    {
        if (!IS_AJAX) show_404();
        // show all php errors for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (!isset($_FILES['File']['tmp_name'])) {
            return die(json_encode(['status' => 'failed', 'message' => 'File is required.']));
        }

        $tmpFilePath = $_FILES['File']['tmp_name'];
        $csvData = [];

        if (($handle = fopen($tmpFilePath, 'r')) !== false) {
            $header = fgetcsv($handle);
            if ($header === false) {
                return die(json_encode(['status' => 'error', 'message' => 'Invalid CSV file.']));
            }

            $header = array_map('strtolower', $header);

            while (($row = fgetcsv($handle)) !== false) {
                $csvData[] = array_combine($header, $row);
            }
            fclose($handle);
        } else {
            return die(json_encode(['status' => 'error', 'message' => 'Unable to read the file.']));
        }

        if (empty($csvData)) {
            return die(json_encode(['status' => 'error', 'message' => 'CSV file is empty or invalid.']));
        } else {
            // helper to parse flexible time formats into Y-m-d H:i:s or null
            $parseTime = function ($time, $date = null) {
                if (empty($time)) return null;

                $time = trim($time);
                // treat common empty markers as null
                if ($time === '' || in_array(strtoupper($time), ['N/A', 'NA', 'NULL', '-'])) {
                    return null;
                }

                // try direct strtotime first (with date prefix if provided)
                $datePrefix = $date ? $date . ' ' : '';
                $ts = strtotime($datePrefix . $time);
                if ($ts !== false) {
                    return date('Y-m-d H:i:s', $ts);
                }

                // normalize separators and remove unexpected chars (keep digits, colon, am/pm)
                $norm = preg_replace('/[^0-9apmAPM:. ]+/', '', $time);
                $norm = str_ireplace('.', ':', $norm);

                // handle compact numeric times like 800, 0830, 123, 5 -> 08:00, 08:30, 01:23, 05:00
                if (preg_match('/^\d{1,4}$/', $norm)) {
                    $len = strlen($norm);
                    if ($len <= 2) {
                        // "8" -> "8:00"
                        $norm = $norm . ':00';
                    } elseif ($len == 3) {
                        // "830" -> "8:30"
                        $norm = substr($norm, 0, 1) . ':' . substr($norm, 1, 2);
                    } else { // 4
                        // "0830" -> "08:30"
                        $norm = substr($norm, 0, 2) . ':' . substr($norm, 2, 2);
                    }
                }

                // if still not parsed, try adding ":00" if only HH:MM provided without seconds
                if (preg_match('/^\d{1,2}:\d{2}$/', $norm)) {
                    $norm = $norm . ':00';
                }

                // final attempt with date prefix
                $ts = strtotime($datePrefix . $norm);
                if ($ts !== false) {
                    return date('Y-m-d H:i:s', $ts);
                }

                // could not parse
                return null;
            };

            foreach ($csvData as $row) {
                $emp_id = !empty($row['employee id']) ? $row['employee id'] : '';

                $employee = $this->model->getBySQL("SELECT id FROM employees WHERE emp_id = '$emp_id' AND status != 1", 'row');
                if (empty($employee)) {
                    continue; // skip if employee not found or inactive
                }

                $date = !empty($row['date']) ? date('Y-m-d', strtotime($row['date'])) : '';
                $raw_shift_start = !empty($row['on duty']) ? $row['on duty'] : null;
                $raw_shift_end = !empty($row['off duty']) ? $row['off duty'] : null;
                $type = !empty($row['type']) ? $row['type'] : '';

                // parse punch in/out with flexible formats
                $punch_in = $parseTime($row['start time'] ?? '', $date);
                $punch_out = $parseTime($row['end time'] ?? '', $date);

                // If punch_out parsed but earlier than punch_in, assume it is next day
                if (!empty($punch_in) && !empty($punch_out)) {
                    $in_ts = strtotime($punch_in);
                    $out_ts = strtotime($punch_out);
                    if ($out_ts < $in_ts) {
                        $punch_out = date('Y-m-d H:i:s', strtotime($punch_out . ' +1 day'));
                    }
                }

                // parse shift start/end into time-only values for DB (H:i:s) if provided
                $shift_start_time = $parseTime($raw_shift_start, $date);
                $shift_end_time = $parseTime($raw_shift_end, $date);
                $shift_start_db = $shift_start_time ? date('H:i:s', strtotime($shift_start_time)) : ($raw_shift_start ? date('H:i:s', strtotime($raw_shift_start)) : null);
                $shift_end_db = $shift_end_time ? date('H:i:s', strtotime($shift_end_time)) : ($raw_shift_end ? date('H:i:s', strtotime($raw_shift_end)) : null);

                // compute late if shift_start and punch_in are available
                $late = '00:00:00';
                if ($shift_start_db && $punch_in) {
                    $start_shift = date('Y-m-d H:i:s', strtotime($date . ' ' . $shift_start_db));
                    if (strtotime($punch_in) > strtotime($start_shift)) {
                        $late_seconds = strtotime($punch_in) - strtotime($start_shift);
                        $late = gmdate('H:i:s', $late_seconds);
                    }
                }

                if (!empty($type)) {
                    $type = attStatusLabels($type, 'text');
                } else {
                    $type = empty($punch_in) && empty($punch_out) ? 'absent' : '';
                }

                // insert to database
                $to_insert = array(
                    'employee_id' => $employee['id'],
                    'date' => $date,
                    'shift_start' => $shift_start_db,
                    'shift_end' => $shift_end_db,
                    'punch_in' => $punch_in,
                    'punch_out' => $punch_out,
                    'absent' => ((empty($punch_in) && empty($punch_out)) || (!empty($type) && $type == 'absent')) ? 'TRUE' : '',
                    'type' => $type,
                    'late' => $late,
                    'notes' => !empty($row['notes']) ? $row['notes'] : '',
                );

                // check if attendance for employee and date already exists
                $existing = $this->model->getBySQL("SELECT id FROM attendance WHERE employee_id = '{$employee['id']}' AND date = '$date'", 'row');
                if (!empty($existing)) {
                    // update existing record
                    $this->model->update('attendance', $to_insert, "id='{$existing['id']}'");
                } else {
                    // insert new record
                    $this->model->insert('attendance', $to_insert);
                }
            }
        }

        return die(json_encode(['status' => 'success', 'message' => 'Attendance data uploaded successfully.']));
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

            $label_data = array(
                'present' => 0,
                'absent' => 0,
                'undertime' => 0,
                'late' => 0,
                'ncns' => 0,
                'vacation_leave' => 0,
                'sick_leave' => 0,
                'emergency_leave' => 0,
                'account_holiday' => 0,
                'leave_without_pay' => 0,
                'half_day' => 0,
                'suspension' => 0,
            );

            // check type in label data
            if (!empty($record['type']) && isset($label_data[$record['type']])) {
                $label_data[$record['type']] = 1;
            } else {
                // check if punch_in and punch_out are empty
                if (empty($record['punch_in']) && empty($record['punch_out'])) {
                    $label_data['absent'] = 1;
                } elseif (!empty($record['punch_in']) && empty($record['punch_out'])) {
                    $label_data['absent'] = 1; // Absent if punch_in is present but punch_out is not
                } elseif (!empty($record['punch_in']) && !empty($record['punch_out'])) {
                    $label_data['present'] = 1; // Present
                }
            }

            if (!empty($record['late']) && $record['late'] != '00:00:00') {
                $label_data['late'] = 1;
            }


            $record['label_data'] = $label_data;
            $record['total_lti'] = $total_lti;
            $record['over_break'] = $over_break;
            $grouped[$emp_id][$date] = $record;
        }

        return $grouped;
    }

    /**
     * Generate or update attendance records for each employee for each day in the given month.
     * Usage: /attendance/test/YYYY-MM
     */
    public function test($month = '')
    {
        $month = !empty($month) ? $month : date('Y-m');
        $days = get_days("$month-01", "$month-" . date('29', strtotime($month)), false);
        $employees = $this->model->getBySQL("SELECT id, emp_id FROM employees WHERE 1");
        $timeclock = Modules::load('timeclock/Timeclock');

        foreach ($employees as $emp) {
            $emp_id = $emp['id'];
            foreach ($days as $day) {
                $schedule = $timeclock->emp_schedule($emp_id, "$day 22:00:00");
                if (!$schedule) continue;

                $earlyMin = 5;
                $lateMax = 15;
                $punch_in = $schedule['schedule_from'] . " " . $schedule['schedule_start'];
                $baseTime = strtotime($punch_in);

                // 2% chance of late
                $late = '00:00:00';
                $start_shift = date('Y-m-d H:i:s', strtotime($day . ' ' . $schedule['schedule_start']));

                if (rand(1, 100) <= 2) {
                    // Employee is late - punch in after scheduled start time
                    $lateMinutes = rand(1, $lateMax);
                    $punch_in_time = date('Y-m-d H:i:s', $baseTime + ($lateMinutes * 60));
                    $late_seconds = strtotime($punch_in_time) - strtotime($start_shift);
                    $late = gmdate('H:i:s', $late_seconds);
                } else {
                    // Employee is on time or early
                    $offsetMinutes = rand(-$earlyMin, 0);
                    $punch_in_time = date('Y-m-d H:i:s', $baseTime + ($offsetMinutes * 60));
                }

                $punch_out = $schedule['schedule_from'] . " " . $schedule['schedule_end'];
                $baseTimeOut = strtotime($punch_out);
                $offsetMinutesOut = rand(0, $lateMax);
                $punch_out_time = date('Y-m-d H:i:s', $baseTimeOut + ($offsetMinutesOut * 60));

                // 1% chance of absent
                $absent = '';
                if (rand(1, 100) <= 1) {
                    $punch_in_time = null;
                    $punch_out_time = null;
                    $absent = 'TRUE';
                    $late = '00:00:00'; // reset late to 00:00:00 if absent
                }

                $to_insert = array(
                    'employee_id' => $emp_id,
                    'date' => $day,
                    'shift_start' => $schedule['schedule_start'],
                    'shift_end' => $schedule['schedule_end'],
                    'punch_in' => $punch_in_time,
                    'punch_out' => $punch_out_time,
                    'late' => $late,
                    'absent' => $absent,
                    'type' => $absent === 'TRUE' ? 'absent' : '',
                );

                // check if attendance for employee and date already exists
                $existing = $this->model->getBySQL("SELECT id FROM attendance WHERE employee_id = '$emp_id' AND date = '$day'", 'row');
                if (!empty($existing)) {
                    // update existing record
                    $this->model->update('attendance', $to_insert, "id='{$existing['id']}'");
                } else {
                    // insert new record
                    $this->model->insert('attendance', $to_insert);
                }
            }
        }
        echo "Attendance records processed for all employees for $month.";
    }
}
