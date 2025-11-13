<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('system__lang')) {
    function system__lang($system = '', $type = '', $keyid = '')
    {
        $CI = &get_instance();
        $keyidwhere = !empty($keyid) ? " AND keyid = '$keyid' " : '';
        $items = $CI->model->getBySQL("SELECT keyid, value FROM admin_lang WHERE keyword = '{$system}|{$type}' $keyidwhere", $keyid ? "row" : "");

        return !empty($keyid) ? $items['value'] : $items;
    }
}

if (!function_exists('users__lang')) {
    function users__lang($type = '', $keyid = '')
    {
        $CI = &get_instance();
        $keyidwhere = !empty($keyid) ? " AND keyid = '$keyid' " : '';
        $items = $CI->model->getBySQL("SELECT keyid, value FROM admin_lang WHERE keyword = 'user|{$type}' $keyidwhere", $keyid ? "row" : "");

        return !empty($keyid) ? $items['value'] : $items;
    }
}

if (!function_exists('admin__lang')) {
    function admin__lang($type = '', $key = '', $keyid = '')
    {
        $CI = &get_instance();
        $keyidwhere = !empty($keyid) ? " AND keyid = '$keyid' " : '';
        $items = $CI->model->getBySQL("SELECT keyid, value FROM admin_lang WHERE keyword = '{$type}|{$key}' $keyidwhere", $keyid ? "row" : "");

        if (!empty($keyid)) {
            return !empty($items['value']) ? $items['value'] : 'unknown';
        }

        return !empty($items) ? $items : 'unknown';
    }
}

// admin lang for select options
if (!function_exists('admin__lang_select')) {
    function admin__lang_select($type = '', $key = '', $keyid = '', $text = '', $selected = '')
    {
        $CI = &get_instance();
        $keyidwhere = !empty($keyid) ? " AND keyid = '$keyid' " : '';
        $items = $CI->model->getBySQL("SELECT keyid, value FROM admin_lang WHERE keyword = '{$type}|{$key}' $keyidwhere", "result");

        // return <option> tags
        $options = '';
        if (!empty($items)) {
            foreach ($items as $item) {
                // check if the item is selected
                $isSelected = !empty($selected) && $selected == $item['keyid'] ? ' selected' : '';

                $options .= "<option value='{$item['keyid']}' $isSelected>{$item['value']} {$text}</option>";
            }
        }
        return $options;
    }
}

// allow function 
if (!function_exists('allow')) {
    function allow($keyid = '')
    {
        $CI = &get_instance();
        $logindata = $CI->session->userdata('hridealtech_login');

        if (!empty($logindata)) {
            $where = "status = 1";

            // if user level or user id is allowed to view the tab
            $where .= " AND ((find_in_set('{$logindata['emp_level']}',level) OR find_in_set('{$logindata['id']}',special_user)) AND NOT find_in_set('{$logindata['id']}',exclude_user)) ";

            // get tabs from db
            $tabs = $CI->model->getBySQL("SELECT * FROM admin_tabs WHERE {$where} AND keyword = '{$keyid}'", "row");

            return !empty($tabs);
        }
        return false;
    }
}

if(!function_exists('get_latest_empid')) {
    function get_latest_empid() {
        $CI = &get_instance();
        $latest_empid = $CI->model->getBySQL("SELECT CONCAT( '2023-', LPAD( CAST(SUBSTRING(MAX(emp_id), 6) AS UNSIGNED) + 1, 5, '0' ) ) AS next_emp_id FROM employees WHERE emp_id <> 'admin00';", "row");
        return !empty($latest_empid) ? $latest_empid['next_emp_id'] : null;
    }
}

/**
 * Get all days between two dates
 * @param string $date_from Start date (Y-m-d format)
 * @param string $date_to End date (Y-m-d format)
 * @param bool $include_weekends Whether to include weekends (default: true)
 * @return array Array of dates in Y-m-d format
 */
if (!function_exists('get_days')) {
    function get_days($date_from, $date_to, $include_weekends = true)
    {
        $days = array();

        // Validate and format dates
        $start_date = date('Y-m-d', strtotime($date_from));
        $end_date = date('Y-m-d', strtotime($date_to));

        // Ensure start date is not after end date
        if (strtotime($start_date) > strtotime($end_date)) {
            return $days; // Return empty array if invalid date range
        }

        $current = strtotime($start_date);
        $end = strtotime($end_date);

        while ($current <= $end) {
            $date = date('Y-m-d', $current);

            // If weekends should be excluded, skip Saturday (6) and Sunday (7)
            if (!$include_weekends && date('N', $current) >= 6) {
                $current = strtotime('+1 day', $current);
                continue;
            }

            $days[] = $date;
            $current = strtotime('+1 day', $current);
        }

        return $days;
    }
}

if (!function_exists('getWeeksOfMonth')) {
    function getWeeksOfMonth($month)
    {
        $weeks = [];

        // Get first and last day of the month
        $firstDay = new DateTime("$month-01");
        $lastDay = new DateTime($firstDay->format('Y-m-t'));

        // Find first Sunday
        $firstSunday = clone $firstDay;
        if ($firstSunday->format('w') != 0) { // Not Sunday
            $firstSunday->modify('next sunday');
        }

        // Week 1
        $weeks[] = [
            'week' => 1,
            'start' => $firstDay->format('Y-m-d'),
            'end' => $firstSunday->format('Y-m-d')
        ];

        // Remaining weeks
        $weekNumber = 2;
        $weekStart = clone $firstSunday;
        $weekStart->modify('next monday');

        while ($weekStart <= $lastDay) {
            $weekEnd = clone $weekStart;
            $weekEnd->modify('next sunday');

            // Force last week to end on last day of month
            if ($weekEnd > $lastDay) {
                $weekEnd = $lastDay;
            }

            $weeks[] = [
                'week' => $weekNumber,
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d')
            ];

            $weekNumber++;
            $weekStart->modify('+7 days');
        }

        return $weeks;
    }
}

if (!function_exists('removeInlineStyles')) {
    function removeInlineStyles(string $html): string
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        // prevent DOMDocument from wrapping with <html><body>
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//*[@style]') as $el) {
            $el->removeAttribute('style');
        }

        // optional: remove Word/Office XML comments that sometimes get pasted in
        foreach ($xpath->query('//comment()[contains(., "?xml")]') as $c) {
            $c->parentNode->removeChild($c);
        }

        return $dom->saveHTML();
    }
}

if (!function_exists('attStatusLabels')) {
    function attStatusLabels($keyid = '', $type = 'label')
    {
        $labels = [
            'label' => [
                'P'     => 'Present',
                'A'     => 'Absent',
                'U'     => 'Undertime',
                'L'     => 'Late',
                'NCNS'  => 'No Call, No Show',
                'VL'    => 'Vacation Leave',
                'SL'    => 'Sick Leave',
                'EL'    => 'Emergency Leave',
                'AH'    => 'Account Holiday',
                'LWOP'  => 'Leave Without Pay',
                'HD'    => 'Half-Day',
                'SUS'   => 'Suspension',
            ],
            'text' => [
                'present'           => 'Present',
                'absent'            => 'Absent',
                'undertime'         => 'Undertime',
                'late'              => 'Late',
                'ncns'              => 'No Call, No Show',
                'vacation_leave'    => 'Vacation Leave',
                'sick_leave'        => 'Sick Leave',
                'emergency_leave'   => 'Emergency Leave',
                'account_holiday'   => 'Account Holiday',
                'leave_without_pay' => 'Leave Without Pay',
                'half_day'          => 'Half Day',
                'suspension'        => 'Suspension',
            ]
        ];

        if (!isset($labels[$type])) return [];

        // Case 1: Label key given (e.g., 'P') → return matching text key (e.g., 'present')
        if ($type === 'text' && $keyid !== '') {
            $labelList = $labels['label'];
            $textList = $labels['text'];

            if (isset($labelList[$keyid])) {
                $labelValue = $labelList[$keyid];
                foreach ($textList as $textKey => $textValue) {
                    if (strcasecmp($textValue, $labelValue) === 0) {
                        return $textKey;
                    }
                }
            }
            return null;
        }

        // Case 2: "Label - Key" format (e.g., 'Present - P') → return text key (e.g., 'present')
        if (strpos($keyid, ' - ') !== false) {
            list($labelText, $labelKey) = array_map('trim', explode(' - ', $keyid));
            foreach ($labels['text'] as $textKey => $textValue) {
                if (strcasecmp($textValue, $labelText) === 0) {
                    return $textKey;
                }
            }
            return null;
        }

        // Case 3: Regular return
        if ($keyid !== '') {
            return $labels[$type][$keyid] ?? null;
        }

        return $labels[$type];
    }
}

// count present per date
if (!function_exists('countPresentPerDate')) {
    function countPresentPerDate($date)
    {
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');

        $CI = &get_instance();
        $attendance = $CI->model->getBySQL("SELECT id, employee_id, date, type, absent FROM attendance WHERE date = '$date' AND punch_in IS NOT NULL AND absent != 'TRUE'");

        return $attendance;
    }
}

// count absent per date
if (!function_exists('countAbsentPerDate')) {
    function countAbsentPerDate($date)
    {
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');

        $CI = &get_instance();
        $attendance = $CI->model->getBySQL("SELECT id, employee_id, date, type, absent FROM attendance WHERE date = '$date' AND absent = 'TRUE'");

        return $attendance;
    }
}

if (!function_exists('countLeavePerDate')) {
    function countLeavePerDate($date, $user_id = '')
    {
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');

        $CI = &get_instance();
        
        // Count leaves based on shift date (when the leave/shift started)
        // A leave is counted on the date when the shift begins, regardless of when it ends
        $leave = $CI->model->getBySQL("SELECT leave_id, employee_id, type, date_from, date_to FROM employee_leave WHERE DATE(date_from) = '$date' AND mgr_status = 'approved'" . ($user_id ? " AND employee_id = '$user_id'" : ""));

        return $leave;
    }
}


if (!function_exists('isValidTime')) {
    function isValidTime($time)
    {
        return !empty($time) && $time !== '00:00:00' && $time !== '0:00:00';
    }
}


if (!function_exists('formatPhone')) {
    function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '9') {
            // Format Philippine mobile number starting with 9 (add +63 prefix)
            $formattedPhone = '+63 ' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6);
        } elseif (strlen($phone) == 10) {
            // Format as (123) 456-7890
            $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } elseif (strlen($phone) == 11 && substr($phone, 0, 1) == '1') {
            // Format as +1 (234) 567-8901
            $formattedPhone = '+1 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        } elseif (strlen($phone) == 11 && substr($phone, 0, 2) == '09') {
            // Format Philippine mobile number starting with 09
            $formattedPhone = '+63 ' . substr($phone, 1, 3) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
        } elseif (strlen($phone) == 13 && substr($phone, 0, 4) == '6399') {
            // Format Philippine mobile number starting with +639 (without the +)
            $formattedPhone = '+' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 3) . ' ' . substr($phone, 9);
        } else {
            // Return the phone number as is if it doesn't match expected lengths
            $formattedPhone = $phone;
        }

        return $formattedPhone;
    }
}
