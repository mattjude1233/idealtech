<?php

class Home extends MY_Controller
{
    protected $_sil_per_month = 10; // 10 hours of SIL per month

    function __construct()
    {
        parent::__construct();
        $this->load->model('Document_model', 'doc_model');
        $this->load->helper('text');
    }

    function index()
    {
        $formdata = $result = '';
        if ($_POST) {
            $formdata = $result = $this->input->post('result');
        }

        if ($result) {
            // explode result by new line
            $result = explode("\n", $result);

            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    // explode value by tab
                    $value = explode("\t", $value);
                    $draw = array();

                    for ($i = 1; $i <= 3; $i++) {
                        if ($value[$i]) {
                            $no = preg_replace('/[^0-9]/', '', $value[$i]);

                            if ($no && strlen($no) == 3) {
                                $no = str_split($no, 1);

                                $to_insert = array(
                                    'draw' => $i,
                                    'no1' => $no[0],
                                    'no2' => $no[1],
                                    'no3' => $no[2],
                                    'date' => date('Y-m-d', strtotime($value[0]))
                                );

                                $check_if_exist = $this->model->getRow('id', "result", "draw = '" . $i . "' AND date = '" . $to_insert['date'] . "'");

                                if (!$check_if_exist) {
                                    $this->model->insert('result', $to_insert);
                                } else {
                                    $this->model->update('result', $to_insert, "id = '" . $check_if_exist['id'] . "'");
                                }
                            }
                        }
                    }
                }
            }
        }

        $data['result'] = $formdata;

        // Get dashboard statistics
        $data['stats'] = array(
            'active_employees' => $this->model->getBySQL("SELECT COUNT(*) as count FROM employees WHERE status = 0", 'row')['count'],
            'regular_employees' => $this->model->getBySQL("SELECT COUNT(*) AS count FROM employees WHERE status = 0 AND hiring_date IS NOT NULL AND hiring_date <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)", 'row')['count'],
            'probee_employees' => $this->model->getBySQL("SELECT COUNT(*) AS count FROM employees WHERE status = 0 AND hiring_date IS NOT NULL AND hiring_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)", 'row')['count'],
            'pending_leaves' => $this->model->getBySQL("SELECT COUNT(*) as count FROM employee_leave WHERE sv_status = 'pending' OR mgr_status = 'pending' OR (sv_status IS NULL AND mgr_status IS NULL)", 'row')['count'],
            // GET total holidays for current month of the year
            'total_holidays' => $this->model->getBySQL("SELECT COUNT(*) as count FROM holidays WHERE MONTH(date) = MONTH(CURDATE()) AND archived = 0", 'row')['count'],
            'disciplinary_cases' => $this->model->getBySQL("SELECT COUNT(*) as count FROM employee_discipline WHERE status != 2 AND archived != 1", 'row')['count']
        );

        // Get today's absent employees (based on attendance records)
        $today = date('Y-m-d');
        $data['today_absent'] = $this->model->getBySQL("
            SELECT e.emp_fname, e.emp_lname, a.date, a.type, a.absent, a.notes, al.value as type_label
            FROM attendance a 
            JOIN employees e ON a.employee_id = e.id 
            LEFT JOIN admin_lang al ON al.keyid = a.type AND al.keyword = 'attendance|type'
            WHERE a.date = '$today'
            AND (a.absent = 'TRUE' OR (a.punch_in IS NULL AND a.punch_out IS NULL) OR a.type IN ('absent', 'ncns', 'suspended'))
            ORDER BY e.emp_lname ASC, e.emp_fname ASC
            LIMIT 8
        ");

        // Get upcoming leaves (next 7 days)
        $next_week = date('Y-m-d', strtotime('+7 days'));
        $data['upcoming_leaves'] = $this->model->getBySQL("
            SELECT e.emp_fname, e.emp_lname, l.date_from, l.date_to, l.sv_status, l.mgr_status, lt.value as leave_type
            FROM employee_leave l 
            JOIN employees e ON l.employee_id = e.id 
            LEFT JOIN admin_lang lt ON lt.keyid = l.type AND lt.keyword = 'leave|type'
            WHERE DATE(l.date_from) BETWEEN '$today' AND '$next_week'
            AND l.archived = 0
            AND (l.sv_status = 'approved' OR l.mgr_status = 'approved' OR l.status = 'confirmed')
            ORDER BY l.date_from ASC, e.emp_lname ASC
            LIMIT 8
        ");

        // Get upcoming holidays (next 30 days)
        $next_month = date('Y-m-d', strtotime('+90 days'));
        $data['upcoming_holidays'] = $this->model->getBySQL("
            SELECT h.name, h.date, ht.value as type
            FROM holidays h
            LEFT JOIN admin_lang ht ON ht.keyid = h.type AND ht.keyword = 'holiday|type'
            WHERE h.date BETWEEN '$today' AND '$next_month'
            AND h.archived = 0
            ORDER BY h.date ASC
            LIMIT 5
        ");

        // Get active kudos for dashboard
        $data['active_kudos'] = $this->model->getBySQL("SELECT id, name, category, path FROM kudos WHERE active = 1 AND status = 1 LIMIT 1", 'row');

        // Get SIL data for employee dashboard
        if (!check_function('show_admin_dashboard') && isset($this->_logindata['id'])) {
            $employee_id = $this->_logindata['id'];
            $data['employee_sil'] = array(
                'earned' => $this->computeEarnedSIL($employee_id),
                'used' => $this->silUsed($employee_id),
                'remaining' => $this->remainingSIL($employee_id)
            );

            // Get attendance data for employee dashboard
            $data['employee_attendance'] = $this->getEmployeeAttendanceStats($employee_id);
        }

        // Get recent activities (recent leaves, new employees, disciplinary actions, etc.)
        $data['recent_activities'] = array();

        // Recent leave applications (last 7 days)
        $recent_leaves = $this->model->getBySQL("
            SELECT e.emp_fname, e.emp_lname, lt.value AS leave_type, l.sv_status, l.mgr_status, l.date_filed, 'leave' AS activity_type FROM employee_leave AS l JOIN employees AS e ON l.employee_id = e.id LEFT JOIN admin_lang AS lt ON lt.keyid = l.type AND lt.keyword = 'leave|type' WHERE l.archived = 0 AND l.date_filed >= CURDATE() - INTERVAL 7 DAY ORDER BY l.date_filed DESC LIMIT 4;
        ");

        // Recently added employees (last 30 days)
        $recent_employees = $this->model->getBySQL("
            SELECT emp_fname, emp_lname, emp_level, hiring_date, 'employee' as activity_type, hiring_date as date_filed
            FROM employees 
            WHERE DATE(hiring_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND status != 3
            ORDER BY hiring_date DESC
            LIMIT 4
        ");

        // Recent disciplinary actions (last 14 days)
        $recent_disciplinary = $this->model->getBySQL("
            SELECT e.emp_fname, e.emp_lname, d.violation, d.offense_level, d.date_added, 'disciplinary' as activity_type, d.date_added as date_filed
            FROM employee_discipline d
            JOIN employees e ON d.employee_id = e.id
            WHERE DATE(d.date_added) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            AND d.status != 2 AND d.archived != 1
            ORDER BY d.date_added DESC
            LIMIT 3
        ");

        // Merge and sort recent activities
        if (!empty($recent_leaves)) {
            $data['recent_activities'] = array_merge($data['recent_activities'], $recent_leaves);
        }
        if (!empty($recent_employees)) {
            $data['recent_activities'] = array_merge($data['recent_activities'], $recent_employees);
        }
        if (!empty($recent_disciplinary)) {
            $data['recent_activities'] = array_merge($data['recent_activities'], $recent_disciplinary);
        }

        // Sort by date_filed descending
        if (!empty($data['recent_activities'])) {
            usort($data['recent_activities'], function ($a, $b) {
                return strtotime($b['date_filed']) - strtotime($a['date_filed']);
            });
            $data['recent_activities'] = array_slice($data['recent_activities'], 0, 8);
        }

        if (check_function('show_admin_dashboard')) {
            $data['content'] = 'home/index';
        } else {
            $data['content'] = 'home/employee_dashboard';
        }
        $this->display($data);
    }

    function coc($pdf = '')
    {
        // Handle file upload
        if ($_POST && !empty($_FILES['document_file']['name'])) {
            $this->handle_document_upload();
        }

        // Handle document deletion
        if ($this->input->post('delete_document_id')) {
            $doc_id = $this->input->post('delete_document_id');
            if ($this->doc_model->delete_document($doc_id)) {
                $this->session->set_flashdata('success', 'Document deleted successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to delete document.');
            }
            redirect('home/coc');
        }

        // Get all documents
        $data['documents'] = $this->doc_model->get_documents();
        $data['categories'] = $this->doc_model->get_categories();
        
        // Get active document
        $active_document = null;
        if (!empty($pdf)) {
            // Try to find by ID first, then by filename
            if (is_numeric($pdf)) {
                $active_document = $this->doc_model->get_document_by_id($pdf);
            } else {
                // Legacy support for filename-based URLs
                $active_document = $this->doc_model->getRow('*', 'documents', 
                    "file_name LIKE '%$pdf%' AND is_active = 1 AND archived = 0");
            }
        }
        
        // If no specific document or document not found, get the featured document
        if (!$active_document) {
            $active_document = $this->doc_model->getRow('*', 'documents', 
                'is_featured = 1 AND is_active = 1 AND archived = 0', 'upload_date DESC');
        }
        
        // If still no document, get the latest one
        if (!$active_document) {
            $active_document = $this->doc_model->getRow('*', 'documents', 
                'is_active = 1 AND archived = 0', 'upload_date DESC');
        }

        $data['active_document'] = $active_document;
        
        // Record view if user is logged in and document exists
        if ($active_document && $this->_logindata['id']) {
            $employee_id = $this->_logindata['id'];
            $ip_address = $this->input->ip_address();
            $this->doc_model->record_view($active_document['id'], $employee_id, $ip_address);
        }

        // Load jQuery Confirm for delete confirmations
        $data['links__css']['jquery-confirm'] = 'plugins/jquery-confirm/jquery-confirm.min.css';
        $data['links__js']['jquery-confirm'] = 'plugins/jquery-confirm/jquery-confirm.min.js';

        $data['content'] = 'home/coc';
        $data['page_title'] = 'Memorandum and COC';
        $this->display($data);
    }

    private function handle_document_upload()
    {
        // Check if user has upload permissions
        if (!check_function('manage_coc_memo')) {
            $this->session->set_flashdata('error', 'You do not have permission to upload documents.');
            return false;
        }

        $config['upload_path'] = './uploads/documents/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|gif';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('document_file')) {
            $upload_data = $this->upload->data();
            
            // Prepare document data
            $document_data = array(
                'title' => $this->input->post('document_title'),
                'description' => $this->input->post('document_description'),
                'category' => $this->input->post('document_category'),
                'file_name' => $upload_data['file_name'],
                'original_name' => $upload_data['orig_name'],
                'file_path' => 'uploads/documents/',
                'file_type' => $upload_data['file_type'],
                'file_size' => $upload_data['file_size'] * 1024, // Convert to bytes
                'uploaded_by' => $this->_logindata['id'],
                'is_featured' => $this->input->post('is_featured') ? 1 : 0
            );
            
            if ($this->doc_model->insert_document($document_data)) {
                $this->session->set_flashdata('success', 'Document uploaded successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to save document information.');
            }
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
        
        redirect('home/coc');
    }

    public function download_document($id)
    {
        $document = $this->doc_model->get_document_by_id($id);
        
        if (!$document) {
            show_404();
        }
        
        $file_path = $document['file_path'] . $document['file_name'];
        $full_path = FCPATH . $file_path;
        
        if (!file_exists($full_path)) {
            show_404();
        }
        
        // Record view
        if ($this->_logindata['id']) {
            $employee_id = $this->_logindata['id'];
            $ip_address = $this->input->ip_address();
            $this->doc_model->record_view($id, $employee_id, $ip_address);
        }
        
        // Force download
        $this->load->helper('download');
        force_download($document['original_name'], file_get_contents($full_path));
    }

    public function view_document($id)
    {
        $document = $this->doc_model->get_document_by_id($id);
        
        if (!$document) {
            show_404();
        }
        
        // Record view
        if ($this->_logindata['id']) {
            $employee_id = $this->_logindata['id'];
            $ip_address = $this->input->ip_address();
            $this->doc_model->record_view($id, $employee_id, $ip_address);
        }
        
        redirect('home/coc/' . $id);
    }

    public function delete_document()
    {
        // Check if user has delete permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete documents.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        
        if (!$doc_id) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID is required.']);
            return;
        }

        // Get document details before deletion for logging
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        if ($this->doc_model->delete_document($doc_id)) {
            echo json_encode(['status' => 'success', 'message' => 'Document deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete document.']);
        }
    }

    public function set_featured()
    {
        // Check if user has manage permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to manage documents.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        
        if (!$doc_id) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID is required.']);
            return;
        }

        // Get document details
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        // First, remove featured status from all other documents
        $this->doc_model->update('documents', array('is_featured' => 0), "id != '$doc_id'");
        
        // Set this document as featured
        if ($this->doc_model->update_document($doc_id, array('is_featured' => 1))) {
            echo json_encode(['status' => 'success', 'message' => 'Document set as featured successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to set document as featured.']);
        }
    }

    public function remove_featured()
    {
        // Check if user has manage permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to manage documents.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        
        if (!$doc_id) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID is required.']);
            return;
        }

        // Get document details
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        // Remove featured status
        if ($this->doc_model->update_document($doc_id, array('is_featured' => 0))) {
            echo json_encode(['status' => 'success', 'message' => 'Featured status removed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove featured status.']);
        }
    }

    public function toggle_featured()
    {
        // Check if user has manage permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to manage documents.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        
        if (!$doc_id) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID is required.']);
            return;
        }

        // Get document details
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        $new_featured_status = $document['is_featured'] ? 0 : 1;
        $message = $new_featured_status ? 'Document set as featured successfully.' : 'Featured status removed successfully.';

        // If setting as featured, remove featured status from all other documents
        if ($new_featured_status) {
            $this->doc_model->update('documents', array('is_featured' => 0), "id != '$doc_id'");
        }
        
        // Update the document's featured status
        if ($this->doc_model->update_document($doc_id, array('is_featured' => $new_featured_status))) {
            echo json_encode([
                'status' => 'success', 
                'message' => $message,
                'is_featured' => $new_featured_status
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update featured status.']);
        }
    }

    public function get_document_data()
    {
        // Check if user has view permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to view document details.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        
        if (!$doc_id) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID is required.']);
            return;
        }

        // Get document details
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        // Return document data
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $document['id'],
                'title' => $document['title'],
                'description' => $document['description'] ?? '',
                'category' => $document['category'],
                'is_featured' => $document['is_featured']
            ]
        ]);
    }

    public function update_document_info()
    {
        // Check if user has manage permissions
        if (!check_function('manage_coc_memo')) {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to manage documents.']);
            return;
        }

        $doc_id = $this->input->post('document_id');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $category = $this->input->post('category');
        
        if (!$doc_id || !$title) {
            echo json_encode(['status' => 'error', 'message' => 'Document ID and title are required.']);
            return;
        }

        // Get document details
        $document = $this->doc_model->get_document_by_id($doc_id);
        
        if (!$document) {
            echo json_encode(['status' => 'error', 'message' => 'Document not found.']);
            return;
        }

        // Prepare update data
        $update_data = array(
            'title' => $title,
            'description' => $description,
            'category' => $category
        );
        
        // Update the document
        if ($this->doc_model->update_document($doc_id, $update_data)) {
            echo json_encode(['status' => 'success', 'message' => 'Document information updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update document information.']);
        }
    }

    // SIL Calculation Methods (copied from Leavesil controller)
    
    // computed earned SIL
    private function computeEarnedSIL($employeeId, $dateToday = null)
    {
        if (!$dateToday) {
            $dateToday = date('Y-m-d');
        }

        $employeeId = (int) $employeeId;

        // If employee is not active or has no hiring date, return 0
        if ($employeeId <= 0) {
            return 0;
        }

        // Get hiring date
        $result = $this->model->getBySQL("SELECT id, hiring_date FROM employees WHERE id = $employeeId");
        if (!$result || empty($result[0]['hiring_date'])) {
            return 0;
        }
        $hiringDate = new DateTime($result[0]['hiring_date']);
        $today =  new DateTime($dateToday);

        // Reset reference to Jan 1st of current year
        $janFirst = new DateTime($today->format('Y') . '-01-01');

        // If hired this year, use Jan 1st or hiring date whichever is later
        $startDate = ($hiringDate > $janFirst) ? clone $hiringDate : clone $janFirst;

        // Check if employee has at least 6 months of service
        $sixMonthsFromHire = (clone $hiringDate)->modify('+6 months');
        if ($today < $sixMonthsFromHire) {
            return 0;
        }

        // If 6-month mark is later than Jan 1st, use that as starting point
        if ($sixMonthsFromHire > $startDate) {
            $startDate = clone $sixMonthsFromHire;
        }

        // Calculate months of service since start date
        $months = (($today->format('Y') - $startDate->format('Y')) * 12)
            + ($today->format('n') - $startDate->format('n'));

        if ($today->format('d') >= $startDate->format('d')) {
            $months += 1; // Count current month if past start day
        }

        // Earned SIL: 10 hours per month
        return $months * $this->_sil_per_month;
    }

    private function silUsed($employeeId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $employeeId = (int) $employeeId;

        // Get first day of the year based on $date
        $yearStart = date('Y-01-01', strtotime($date));

        $rows = $this->model->getBySQL(" SELECT date_from, date_to, actual_date_from, actual_date_to, date_filed, sil FROM employee_leave WHERE employee_id = $employeeId AND status = 'confirmed' AND date_to >= '$yearStart' AND date_from <= '$date' ");

        $totalUsed = 0;

        if (!empty($rows)) {
            foreach ($rows as $r) {
                // 1) If SIL is explicitly recorded, trust it
                if ($r['sil'] !== null && $r['sil'] !== '' && is_numeric($r['sil'])) {
                    $totalUsed += (float)$r['sil'];
                    continue;
                }

                // 2) Determine start & end dates for counting
                $start = !empty($r['actual_date_from']) ? $r['actual_date_from'] : $r['date_from'];
                $end   = !empty($r['actual_date_to'])   ? $r['actual_date_to']   : $r['date_to'];

                // Clip the range to be within the current year and up to $date
                if ($start < $yearStart) $start = $yearStart;
                if ($end > $date) $end = $date;

                // Fallback if only filed date exists
                if (empty($start) || empty($end)) {
                    if (!empty($r['date_filed']) && $r['date_filed'] >= $yearStart && $r['date_filed'] <= $date) {
                        $totalUsed += 1;
                    }
                    continue;
                }

                // Count inclusive days
                if ($start <= $end) {
                    $d1 = new DateTime($start);
                    $d2 = new DateTime($end);
                    $days = $d1->diff($d2)->days + 1;
                    $totalUsed += $days;
                }
            }
        }

        return $totalUsed;
    }

    private function remainingSIL($employeeId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $earnedSIL = $this->computeEarnedSIL($employeeId);
        $usedSIL = $this->silUsed($employeeId, $date);

        return $earnedSIL - $usedSIL;
    }

    private function getEmployeeAttendanceStats($employeeId)
    {
        $employeeId = (int) $employeeId;
        $today = date('Y-m-d');
        $currentMonth = date('Y-m');
        $last7Days = date('Y-m-d', strtotime('-7 days'));
        
        // Get current month attendance stats
        $monthAttendance = $this->model->getBySQL("
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN (punch_in IS NOT NULL AND punch_out IS NOT NULL) AND absent != 'TRUE' AND type NOT IN ('absent', 'ncns', 'sick_leave', 'vacation_leave', 'emergency_leave', 'leave_without_pay') THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN absent = 'TRUE' OR type IN ('absent', 'ncns') OR (punch_in IS NULL AND punch_out IS NULL) THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN late != '00:00:00' AND late IS NOT NULL THEN 1 ELSE 0 END) as late_days
            FROM attendance 
            WHERE employee_id = $employeeId 
            AND date LIKE '$currentMonth%'
        ", 'row');

        // Get last 7 days attendance stats
        $last7DaysAttendance = $this->model->getBySQL("
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN (punch_in IS NOT NULL AND punch_out IS NOT NULL) AND absent != 'TRUE' AND type NOT IN ('absent', 'ncns', 'sick_leave', 'vacation_leave', 'emergency_leave', 'leave_without_pay') THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN absent = 'TRUE' OR type IN ('absent', 'ncns') OR (punch_in IS NULL AND punch_out IS NULL) THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN late != '00:00:00' AND late IS NOT NULL THEN 1 ELSE 0 END) as late_days
            FROM attendance 
            WHERE employee_id = $employeeId 
            AND date BETWEEN '$last7Days' AND '$today'
        ", 'row');

        // Calculate total hours this month
        $hoursThisMonth = $this->model->getBySQL("
            SELECT 
                SUM(
                    CASE 
                        WHEN punch_in IS NOT NULL AND punch_out IS NOT NULL 
                        THEN TIMESTAMPDIFF(MINUTE, punch_in, punch_out) / 60 
                        ELSE 0 
                    END
                ) as total_hours
            FROM attendance 
            WHERE employee_id = $employeeId 
            AND date LIKE '$currentMonth%'
            AND absent != 'TRUE'
            AND punch_in IS NOT NULL 
            AND punch_out IS NOT NULL
        ", 'row');

        // Calculate attendance percentage for current month
        $attendancePercentage = 0;
        if ($monthAttendance['total_days'] > 0) {
            $attendancePercentage = round(($monthAttendance['present_days'] / $monthAttendance['total_days']) * 100);
        }

        return array(
            'attendance_percentage' => $attendancePercentage,
            'hours_this_month' => round($hoursThisMonth['total_hours'] ?? 0),
            'last_7_days' => array(
                'present' => $last7DaysAttendance['present_days'] ?? 0,
                'absent' => $last7DaysAttendance['absent_days'] ?? 0,
                'late' => $last7DaysAttendance['late_days'] ?? 0
            ),
            'current_month' => array(
                'present' => $monthAttendance['present_days'] ?? 0,
                'absent' => $monthAttendance['absent_days'] ?? 0,
                'late' => $monthAttendance['late_days'] ?? 0,
                'total' => $monthAttendance['total_days'] ?? 0
            )
        );
    }
}
