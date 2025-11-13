<?php

class Leaves extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // load leave helper
        $this->load->helper('leave');
    }

    function index($emp_id = '', $date_from = '', $date_to = '')
    {
        // Get filter parameters from GET request
        $filter_emp_id = $this->input->get('emp_id');
        $filter_leave_type = $this->input->get('leave_type');
        $filter_status = $this->input->get('status');
        $filter_date_from = $this->input->get('date_from');
        $filter_date_to = $this->input->get('date_to');

        // Use URL parameters if no GET parameters
        if (empty($filter_emp_id) && !empty($emp_id)) {
            $filter_emp_id = $emp_id;
        }
        if (empty($filter_date_from) && !empty($date_from)) {
            $filter_date_from = $date_from;
        }
        if (empty($filter_date_to) && !empty($date_to)) {
            $filter_date_to = $date_to;
        }

        if (!empty($filter_emp_id)) {
            $employee = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_lname FROM employees WHERE emp_id = '{$filter_emp_id}'", 'row');
            if (empty($employee)) {
                redirect('leaves');
            }
        } else {
            $employee = array();
        }

        $where_con = 'l.archived = 0';

        if (!check_function('manage_leave')) {
            $where_con .= " AND l.employee_id = {$this->_logindata['id']}";
        } else {
            if (!empty($filter_emp_id)) {
                $where_con .= " AND l.employee_id = {$employee['id']}";
            }
        }

        // Apply leave type filter
        if (!empty($filter_leave_type)) {
            $where_con .= " AND l.type = '{$filter_leave_type}'";
        }

        // Apply status filter
        if (!empty($filter_status)) {
            if ($filter_status == 'approved') {
                $where_con .= " AND (l.sv_status = 'approved' OR l.mgr_status = 'approved')";
            } elseif ($filter_status == 'denied') {
                $where_con .= " AND (l.sv_status = 'denied' OR l.mgr_status = 'denied')";
            } elseif ($filter_status == 'confirmed') {
                $where_con .= " AND l.status = 'confirmed'";
            } elseif ($filter_status == 'pending') {
                $where_con .= " AND l.status = 'pending'";
            }
        }

        // if date_from and date_to are not empty, validate them
        if (!empty($filter_date_from) && !empty($filter_date_to)) {
            if (strtotime($filter_date_from) > strtotime($filter_date_to)) {
                show_error('Invalid date range.');
            }
        } else {
            $filter_date_from = date('Y-01-01');
            $filter_date_to = date('Y-12-t');
        }

        if (!empty($filter_date_from) && !empty($filter_date_to)) {
            $where_con .= " AND l.date_from >= '{$filter_date_from}' AND l.date_to <= '{$filter_date_to}'";
        }

        $data['list'] = $this->model->getBySQL("SELECT l.leave_id, l.employee_id, l.sil, ee.emp_id, ee.emp_fname, ee.emp_lname, ee.hiring_date, lt.value AS leave_type, l.date_from, l.date_to, l.actual_date_from, l.actual_date_to, l.status AS hr_status, l.date_confirmed AS hr_confirm, l.reason, l.sv_status, l.sv_detail, l.mgr_status, l.mgr_detail, l.date_filed FROM employee_leave AS l LEFT JOIN employees AS ee ON ee.id = l.employee_id LEFT JOIN admin_lang AS lt ON lt.keyid = l.type AND lt.keyword = 'leave|type' WHERE {$where_con} ORDER BY l.date_filed DESC", 'result');

        // Add SIL calculations for each leave record
        if (!empty($data['list'])) {
            foreach ($data['list'] as &$leave) {
                $leave['current_sil'] = $this->computeCurrentSIL($leave['employee_id']);
            }
        }

        $data['page_title'] = 'Leaves';
        $data['employee'] = $employee;
        $data['date_from'] = $filter_date_from;
        $data['date_to'] = $filter_date_to;
        $data['content'] = 'leaves/index';
        $this->display($data);
    }

    // Compute current SIL balance for an employee
    private function computeCurrentSIL($employeeId)
    {
        $earnedSIL = $this->computeEarnedSIL($employeeId);
        $usedSIL = $this->silUsed($employeeId, date('Y-m-d'));
        return $earnedSIL - $usedSIL;
    }

    // Helper methods from leavesil controller
    private function computeEarnedSIL($employeeId, $dateToday = null)
    {
        if (!$dateToday) {
            $dateToday = date('Y-m-d');
        }

        $employeeId = (int) $employeeId;
        $sil_per_month = 10; // 10 hours of SIL per month

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
        return $months * $sil_per_month;
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

    function addleave()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new leave.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_insert = array(
                'employee_id' => $this->_logindata['id'],
                'type' => $formdata['leave_type'],
                'date_from' => date('Y-m-d H:i:00', strtotime($formdata['leave_from'])),
                'date_to' => date('Y-m-d H:i:00', strtotime($formdata['leave_to'])),
                'reason' => $formdata['leave_reason'],
                'date_filed' => date('Y-m-d H:i:s'),
            );

            if ($this->model->insert('employee_leave', $to_insert)) {
                $result['status'] = 'success';
                $result['message'] = 'Leave added successfully.';
            }
        }

        die(json_encode($result));
    }

    public function getleave()
    {
        if (!IS_AJAX) show_404();
        $leaveid = $this->mysecurity->decrypt_url($this->input->post('leaveid'));
        if (empty($leaveid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Leave ID is required.')));
        }

        $return = array('status' => 'failed', 'message' => 'Failed to get leave details.');

        $leave_list = $this->model->getBySQL("SELECT l.type, l.sil, DATE_FORMAT( l.date_from, '%M %e, %Y %h:%i %p' ) AS leave_from, DATE_FORMAT( l.date_to, '%M %e, %Y %h:%i %p' ) AS leave_to, DATE_FORMAT( l.actual_date_from, '%M %e, %Y %h:%i %p' ) AS actual_date_from, DATE_FORMAT( l.actual_date_to, '%M %e, %Y %h:%i %p' ) AS actual_date_to, l.reason, l.sv_status, l.mgr_status FROM employee_leave AS l WHERE l.leave_id = '{$leaveid}'", 'row');

        if (!empty($leave_list)) {
            $return['status'] = 'success';
            $return['message'] = 'Leave details retrieved successfully.';
            $return['data'] = $leave_list;
        }

        die(json_encode($return));
    }

    public function updateleave()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $leaveid = $this->mysecurity->decrypt_url($this->input->post('leaveid'));
        if (empty($leaveid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Leave ID is required.')));
        }

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update leave.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_update = array(
                'type' => $formdata['leave_type'],
                'date_from' => date('Y-m-d H:i:00', strtotime($formdata['leave_from'])),
                'date_to' => date('Y-m-d H:i:00', strtotime($formdata['leave_to'])),
                'actual_date_from' => !empty($formdata['actual_date_from']) ? date('Y-m-d H:i:00', strtotime($formdata['actual_date_from'])) : null,
                'actual_date_to' => !empty($formdata['actual_date_to']) ? date('Y-m-d H:i:00', strtotime($formdata['actual_date_to'])) : null,
                'sil' => !empty($formdata['sil_used']) ? $formdata['sil_used'] : 0,
                'reason' => $formdata['leave_reason'],
            );

            if ($this->model->update('employee_leave', $to_update, array('leave_id' => $leaveid))) {
                $result['status'] = 'success';
                $result['message'] = 'Leave updated successfully.';
            }
        }

        die(json_encode($result));
    }

    public function leaveapproval()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $leaveid = $this->mysecurity->decrypt_url($this->input->post('leaveid'));
        $leavetype = $this->mysecurity->decrypt_url($this->input->post('type'));

        if (empty($leaveid) || empty($leavetype)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Some required fields are missing.')));
        }

        $notRequiredFields = array('remarks');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update leave status.');

        // if empty leave is deny require remarks
        if ($formdata['approval'] == 'denied' && empty($formdata['remarks'])) {
            $errormsg['remarks'] = 'Leave Remarks is required.';
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_update = [];

            if ($leavetype == 'sv') {
                $details = [
                    'approved_by' => $this->_logindata['id'],
                    'approved_date' => date('Y-m-d H:i:s'),
                    'approved_comment' => !empty($formdata['details']) ? $formdata['details'] : '',
                ];

                $to_update = array(
                    'sv_status' => $formdata['approval'] == 'approved' ? "approved" : "denied",
                    'sv_detail' => json_encode($details),
                );
            } elseif ($leavetype == 'mgr') {

                // get leave details
                $leave = $this->model->getBySQL("SELECT date_from, date_to, status FROM employee_leave WHERE leave_id = '{$leaveid}'", 'row');


                $details = [
                    'approved_by' => $this->_logindata['id'],
                    'approved_date' => date('Y-m-d H:i:s'),
                    'approved_comment' => !empty($formdata['details']) ? $formdata['details'] : '',
                ];

                $to_update = array(
                    'mgr_status' => $formdata['approval'] == 'approved' ? "approved" : "denied",
                    'mgr_detail' => json_encode($details),
                );

                if ($leave['status'] == 'pending') {
                    $to_update['actual_date_from'] = !empty($leave['date_from']) ? date('Y-m-d H:i:s', strtotime($leave['date_from'])) : null;
                    $to_update['actual_date_to'] = !empty($leave['date_to']) ? date('Y-m-d H:i:s', strtotime($leave['date_to'])) : null;
                }
            }

            if ($this->model->update('employee_leave', $to_update, array('leave_id' => $leaveid))) {
                $result['status'] = 'success';
                $result['message'] = $formdata['approval'] == 'approved' ? 'Leave approved successfully.' : 'Leave denied successfully.';
            }
        }

        die(json_encode($result));
    }

    public function hrconfirm()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $leaveid = $this->mysecurity->decrypt_url($this->input->post('leaveid'));

        if (empty($leaveid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Leave ID is required.')));
        }


        $result = array('status' => 'failed', 'message' => 'Failed to update leave status.');
        $to_update = [];

        // check if leave exists
        $leave = $this->model->getBySQL("SELECT leave_id, status, actual_date_from, actual_date_to, date_from, date_to FROM employee_leave WHERE leave_id = '{$leaveid}' AND archived = 0", 'row');
        if (empty($leave)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Leave not found or already archived.')));
        }

        //  if leave is already confirmed, change status to pending
        if ($leave['status'] == 'confirmed') {
            $to_update['status'] = 'pending';
            $to_update['date_confirmed'] = null;
        } else {
            $to_update['status'] = 'confirmed';
            $to_update['date_confirmed'] = date('Y-m-d H:i:s');

            // if actual dates are not set or is null, set them to the leave dates
            if (empty($leave['actual_date_from']) || empty($leave['actual_date_to'])) {
                $to_update['actual_date_from'] = !empty($leave['date_from']) ? date('Y-m-d H:i:s', strtotime($leave['date_from'])) : null;
                $to_update['actual_date_to'] = !empty($leave['date_to']) ? date('Y-m-d H:i:s', strtotime($leave['date_to'])) : null;
            }
        }

        if ($this->model->update('employee_leave', $to_update, array('leave_id' => $leaveid))) {
            $result['status'] = 'success';
            $result['message'] = 'Leave status updated successfully.';
        } else {
            $result['message'] = 'Failed to update leave status.';
        }

        die(json_encode($result));
    }

    public function cancelleave()
    {
        if (!IS_AJAX) show_404();
        $leaveid = $this->mysecurity->decrypt_url($this->input->post('leaveid'));
        if (empty($leaveid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Leave ID is required.')));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to cancel leave.');

        $archive_details = array(
            'archived' => 1,
            'archived_by' => $this->_logindata['id'],
            'date_archived' => date('Y-m-d H:i:s'),
        );

        if ($this->model->update('employee_leave', $archive_details, array('leave_id' => $leaveid))) {
            $result['status'] = 'success';
            $result['message'] = 'Leave cancelled successfully.';
        }

        die(json_encode($result));
    }

    public function getSILBalance()
    {
        if (!IS_AJAX) show_404();
        
        $employee_id = $this->input->post('employee_id');
        
        // If no employee_id provided, use current logged in user
        if (empty($employee_id)) {
            $employee_id = $this->_logindata['id'];
        }

        $result = array('status' => 'failed', 'message' => 'Failed to get SIL balance.');

        try {
            $earned_sil = $this->computeEarnedSIL($employee_id);
            $used_sil = $this->silUsed($employee_id, date('Y-m-d'));
            $current_sil = $earned_sil - $used_sil;

            $result['status'] = 'success';
            $result['message'] = 'SIL balance retrieved successfully.';
            $result['data'] = array(
                'earned_sil' => number_format($earned_sil, 2),
                'used_sil' => number_format($used_sil, 2),
                'current_sil' => number_format($current_sil, 2)
            );
        } catch (Exception $e) {
            $result['message'] = 'Error calculating SIL balance.';
        }

        die(json_encode($result));
    }
}
