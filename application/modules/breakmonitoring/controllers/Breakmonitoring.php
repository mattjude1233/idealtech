<?php

class Breakmonitoring extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($date_from = null, $date_to = null, $employee_id = null, $break_type = null)
    {
        // Handle both URL parameters and POST/GET data
        $filters = $this->get_filters($date_from, $date_to, $employee_id, $break_type);

        // Build WHERE clause for filters
        $where_conditions = [];

        // Check if user has manage_attendance permission
        if (!check_function('manage_attendance')) {
            // If no permission, only show current user's records
            $current_user_employee_id = $this->_logindata['id'];
            if ($current_user_employee_id) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($current_user_employee_id) . "'";
            }
        } else {
            // If has permission, allow filtering by employee_id
            if (!empty($filters['employee_id'])) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($filters['employee_id']) . "'";
            }
        }

        // Date filters with validation
        if (!empty($filters['date_from'])) {
            if ($this->validate_date($filters['date_from'])) {
                $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') >= '" . $this->db->escape_str($filters['date_from']) . "'";
            }
        }

        if (!empty($filters['date_to'])) {
            if ($this->validate_date($filters['date_to'])) {
                $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') <= '" . $this->db->escape_str($filters['date_to']) . "'";
            }
        }

        // Break type filter
        if (!empty($filters['break_type'])) {
            $where_conditions[] = "b.break_type = '" . $this->db->escape_str($filters['break_type']) . "'";
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
        }

        // Main query with filters - Enhanced with better aggregation
        $sql = "SELECT 
                    b.id AS break_id, 
                    DATE_FORMAT(b.date, '%Y-%m-%d') AS date, 
                    b.break_start, 
                    b.break_end, 
                    b.break_type, 
                    b.notes,
                    e.emp_id, 
                    e.emp_fname, 
                    e.emp_lname,
                    e.designation,
                    (SELECT COUNT(*) 
                     FROM employee_break b2 
                     WHERE b2.employee_id = b.employee_id 
                       AND DATE_FORMAT(b2.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b2.break_type = b.break_type 
                       AND b2.break_start <= b.break_start
                    ) AS break_count,
                    (SELECT SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, b3.break_start, b3.break_end))) 
                     FROM employee_break b3 
                     WHERE b3.employee_id = b.employee_id 
                       AND DATE_FORMAT(b3.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b3.break_type = 'break' 
                       AND b3.break_end IS NOT NULL
                    ) AS total_break,
                    (SELECT SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, b4.break_start, b4.break_end))) 
                     FROM employee_break b4 
                     WHERE b4.employee_id = b.employee_id 
                       AND DATE_FORMAT(b4.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b4.break_type = 'lunch' 
                       AND b4.break_end IS NOT NULL
                    ) AS total_lunch,
                    TIMESTAMPDIFF(SECOND, b.break_start, IFNULL(b.break_end, NOW())) AS duration_seconds
                FROM employee_break AS b 
                LEFT JOIN employees AS e ON e.id = b.employee_id" . $where_clause . " 
                ORDER BY b.date DESC, b.break_start DESC";

        $data['list'] = $this->model->getBySQL($sql);

        // Get employee list for dropdown (only if user has permission)
        if (check_function('manage_attendance')) {
            $data['employees'] = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_lname FROM employees WHERE status = 'active' ORDER BY emp_fname, emp_lname");
        } else {
            $data['employees'] = [];
        }

        // Pass filter values to view
        $data['selected_employee'] = $filters['employee_id'];
        $data['selected_date_from'] = !empty($filters['date_from']) ? $filters['date_from'] : date('Y-m-01');
        $data['selected_date_to'] = !empty($filters['date_to']) ? $filters['date_to'] : date('Y-m-d');
        $data['selected_break_type'] = $filters['break_type'];

        $data['page_title'] = 'Break Monitoring';
        $data['content'] = 'breakmonitoring/index';
        $this->display($data);
    }

    /**
     * Get filters from URL parameters or POST/GET data
     */
    private function get_filters($date_from = null, $date_to = null, $employee_id = null, $break_type = null)
    {
        $filters = [];

        // Priority: POST > GET > URL parameters
        $filters['date_from'] = $this->input->post('date_from') ?: $this->input->get('date_from') ?: $date_from;
        $filters['date_to'] = $this->input->post('date_to') ?: $this->input->get('date_to') ?: $date_to;
        $filters['employee_id'] = $this->input->post('employee_id') ?: $this->input->get('employee_id') ?: $employee_id;
        $filters['break_type'] = $this->input->post('break_type') ?: $this->input->get('break_type') ?: $break_type;

        // Clean up empty values
        foreach ($filters as $key => $value) {
            if (empty($value) || $value === 'null') {
                $filters[$key] = null;
            }
        }

        return $filters;
    }

    /**
     * Validate date format
     */
    private function validate_date($date)
    {
        if (empty($date)) {
            return false;
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Ajax filter method for real-time filtering
     */
    public function ajax_filter()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $filters = $this->get_filters();

        // Build WHERE clause (same logic as index method)
        $where_conditions = [];

        if (!check_function('manage_attendance')) {
            $current_user_employee_id = $this->_logindata['id'];
            if ($current_user_employee_id) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($current_user_employee_id) . "'";
            }
        } else {
            if (!empty($filters['employee_id'])) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($filters['employee_id']) . "'";
            }
        }

        if (!empty($filters['date_from']) && $this->validate_date($filters['date_from'])) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') >= '" . $this->db->escape_str($filters['date_from']) . "'";
        }

        if (!empty($filters['date_to']) && $this->validate_date($filters['date_to'])) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') <= '" . $this->db->escape_str($filters['date_to']) . "'";
        }

        if (!empty($filters['break_type'])) {
            $where_conditions[] = "b.break_type = '" . $this->db->escape_str($filters['break_type']) . "'";
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
        }

        // Use same query as index method
        $sql = "SELECT 
                    b.id AS break_id, 
                    DATE_FORMAT(b.date, '%Y-%m-%d') AS date, 
                    b.break_start, 
                    b.break_end, 
                    b.break_type, 
                    b.notes,
                    e.emp_id, 
                    e.emp_fname, 
                    e.emp_lname,
                    e.designation,
                    (SELECT COUNT(*) 
                     FROM employee_break b2 
                     WHERE b2.employee_id = b.employee_id 
                       AND DATE_FORMAT(b2.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b2.break_type = b.break_type 
                       AND b2.break_start <= b.break_start
                    ) AS break_count,
                    (SELECT SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, b3.break_start, b3.break_end))) 
                     FROM employee_break b3 
                     WHERE b3.employee_id = b.employee_id 
                       AND DATE_FORMAT(b3.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b3.break_type = 'break' 
                       AND b3.break_end IS NOT NULL
                    ) AS total_break,
                    (SELECT SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, b4.break_start, b4.break_end))) 
                     FROM employee_break b4 
                     WHERE b4.employee_id = b.employee_id 
                       AND DATE_FORMAT(b4.date, '%Y-%m-%d') = DATE_FORMAT(b.date, '%Y-%m-%d') 
                       AND b4.break_type = 'lunch' 
                       AND b4.break_end IS NOT NULL
                    ) AS total_lunch,
                    TIMESTAMPDIFF(SECOND, b.break_start, IFNULL(b.break_end, NOW())) AS duration_seconds
                FROM employee_break AS b 
                LEFT JOIN employees AS e ON e.id = b.employee_id" . $where_clause . " 
                ORDER BY b.date DESC, b.break_start DESC";

        $data = $this->model->getBySQL($sql);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
            'total_records' => count($data),
            'filters_applied' => $filters
        ]);
    }

    /**
     * Get violation summary for dashboard
     */
    public function get_violation_summary($date_from = null, $date_to = null)
    {
        $where_conditions = [];

        if (!empty($date_from)) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') >= '" . $this->db->escape_str($date_from) . "'";
        }

        if (!empty($date_to)) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') <= '" . $this->db->escape_str($date_to) . "'";
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
        }

        $sql = "SELECT 
                    b.break_type,
                    COUNT(*) as total_breaks,
                    SUM(CASE 
                        WHEN b.break_type = 'break' AND TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) > 15 THEN 1
                        WHEN b.break_type = 'lunch' AND TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) > 60 THEN 1
                        ELSE 0 
                    END) as violations,
                    AVG(TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end)) as avg_duration
                FROM employee_break b 
                LEFT JOIN employees e ON e.id = b.employee_id
                " . $where_clause . "
                AND b.break_end IS NOT NULL
                GROUP BY b.break_type";

        return $this->model->getBySQL($sql);
    }

    /**
     * Export break monitoring data
     */
    public function export($format = 'excel', $date_from = null, $date_to = null, $employee_id = null, $break_type = null)
    {
        $filters = $this->get_filters($date_from, $date_to, $employee_id, $break_type);

        // Use same query logic as index method
        $where_conditions = [];

        if (!check_function('manage_attendance')) {
            $current_user_employee_id = $this->_logindata['id'];
            if ($current_user_employee_id) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($current_user_employee_id) . "'";
            }
        } else {
            if (!empty($filters['employee_id'])) {
                $where_conditions[] = "b.employee_id = '" . $this->db->escape_str($filters['employee_id']) . "'";
            }
        }

        if (!empty($filters['date_from']) && $this->validate_date($filters['date_from'])) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') >= '" . $this->db->escape_str($filters['date_from']) . "'";
        }

        if (!empty($filters['date_to']) && $this->validate_date($filters['date_to'])) {
            $where_conditions[] = "DATE_FORMAT(b.date, '%Y-%m-%d') <= '" . $this->db->escape_str($filters['date_to']) . "'";
        }

        if (!empty($filters['break_type'])) {
            $where_conditions[] = "b.break_type = '" . $this->db->escape_str($filters['break_type']) . "'";
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
        }

        $sql = "SELECT 
                    e.emp_id as 'Employee ID',
                    CONCAT(e.emp_fname, ' ', e.emp_lname) as 'Employee Name',
                    DATE_FORMAT(b.date, '%Y-%m-%d') as 'Date',
                    TIME_FORMAT(b.break_start, '%h:%i %p') as 'Start Time',
                    IFNULL(TIME_FORMAT(b.break_end, '%h:%i %p'), 'Ongoing') as 'End Time',
                    CASE 
                        WHEN b.break_end IS NULL THEN 'Ongoing'
                        ELSE CONCAT(
                            FLOOR(TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) / 60), 'h ',
                            TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) % 60, 'm'
                        )
                    END as 'Duration',
                    UPPER(b.break_type) as 'Type',
                    CASE 
                        WHEN b.break_type = 'break' AND TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) > 15 THEN 'OVERBREAK'
                        WHEN b.break_type = 'lunch' AND TIMESTAMPDIFF(MINUTE, b.break_start, b.break_end) > 60 THEN 'OVER LUNCH'
                        WHEN b.break_end IS NULL THEN 'ONGOING'
                        ELSE 'NORMAL'
                    END as 'Status',
                    IFNULL(b.notes, '') as 'Notes'
                FROM employee_break AS b 
                LEFT JOIN employees AS e ON e.id = b.employee_id" . $where_clause . " 
                ORDER BY b.date DESC, b.break_start DESC";

        $data = $this->model->getBySQL($sql);

        // Generate filename with current filters
        $filename_parts = ['Break_Monitoring_Report'];
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $filename_parts[] = 'from_' . ($filters['date_from'] ?: 'start');
            $filename_parts[] = 'to_' . ($filters['date_to'] ?: 'end');
        }
        if (!empty($filters['break_type'])) {
            $filename_parts[] = strtoupper($filters['break_type']);
        }
        $filename_parts[] = date('Y-m-d_H-i-s');

        $filename = implode('_', $filename_parts);

        if ($format == 'excel') {
            $this->export_excel($data, $filename);
        } elseif ($format == 'csv') {
            $this->export_csv($data, $filename);
        } else {
            // Default to excel
            $this->export_excel($data, $filename);
        }
    }
    private function export_excel($data, $filename)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');

        echo '<table border="1">';
        if (!empty($data)) {
            // Headers
            echo '<tr>';
            foreach (array_keys($data[0]) as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr>';

            // Data
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars($cell) . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</table>';
    }

    private function export_csv($data, $filename)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        if (!empty($data)) {
            // Headers
            fputcsv($output, array_keys($data[0]));

            // Data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
    }
}
