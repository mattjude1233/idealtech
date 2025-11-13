<?php

class Payrollgenerate extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($yearmonth = '', $period = '')
    {
        $yearmonth = ($yearmonth == '') ? date('Y-m') : $yearmonth;
        if (empty($period)) {
            $day = date('d', strtotime($yearmonth . '-01'));
            $period = ($day > 15) ? 1 : 2;
        }

        $data['employee'] = $this->model->getBySQL("SELECT e.id, e.emp_id, e.emp_fname, e.emp_mname, e.emp_lname, e.emp_level, e.hiring_date, p.id AS payroll_id FROM employees AS e LEFT JOIN payroll AS p ON e.id = p.employee_id AND payout_month = '$yearmonth' AND period = '$period' WHERE e.status != '3' ORDER BY FIELD(e.status, 1, 0) DESC, e.emp_lname ASC, e.emp_fname ASC");

        $data['page_title'] = "Payroll: " . admin__lang('payroll', 'period', $period) . " - " . date('F Y', strtotime($yearmonth));
        $data['yearmonth'] = $yearmonth;
        $data['period'] = $period;
        $data['content'] = 'payrollgenerate/index';
        $this->display($data);
    }

    public function save()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new payroll.');

        // check if $_FILES is set and not empty
        $payroll_details = [];
        if (isset($_FILES['Payroll_File']) && !empty($_FILES['Payroll_File']['name'])) {
            $path = 'uploads/payslip/';
            $folder = create_date_folder($path);

            $filename = sha1(pathinfo($_FILES['Payroll_File']['name'], PATHINFO_FILENAME) . time()) . '.' . pathinfo($_FILES['Payroll_File']['name'], PATHINFO_EXTENSION);
            $file_path = $folder['day'] . $filename;


            if (move_uploaded_file($_FILES['Payroll_File']['tmp_name'], FCPATH . $file_path)) {
                $payroll_details = array(
                    'file_name' => $_FILES['Payroll_File']['name'],
                    'file_path' => $file_path,
                );
            } else {
                $errormsg['Payroll_File'] = 'Failed to upload payroll file.';
            }
        } else {
            $errormsg['Payroll_File'] = 'Payroll file is required.';
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_insert = array(
                'employee_id' => $this->mysecurity->decrypt_url($formdata['empid']),
                'details' => json_encode($payroll_details),
                'payout_month' => date('Y-m', strtotime($formdata['yearmonth'])),
                'period' => $formdata['period'],
                'added_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
            );

            $payrollid =  !empty($formdata['payroll_id']) ? $this->mysecurity->decrypt_url($formdata['payroll_id']) : null;

            if ($payrollid) {
                // update existing payroll
                $this->model->update('payroll', $to_insert, array('id' => $payrollid));
                $result['status'] = 'success';
                $result['message'] = 'Payroll updated successfully.';
            } else {
                // add new payroll
                if ($this->model->insert('payroll', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Payroll added successfully.';
                } else {
                    $result['message'] = 'Failed to add new payroll.';
                }
            }
        }

        die(json_encode($result));
    }

    public function getpayroll()
    {
        if (!IS_AJAX) show_404();
        $empid = $this->mysecurity->decrypt_url($this->input->post('empid'));
        $period = !empty($this->input->post('period')) ? $this->input->post('period') : 1;
        $yearmonth = !empty($this->input->post('yearmonth')) ? date('Y-m', strtotime($this->input->post('yearmonth'))) : date('Y-m');

        if (empty($empid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Employee ID is required.')));
        }

        $return = array('status' => 'failed', 'message' => 'Failed to get payroll details.');
        $payroll = $this->model->getBySQL("SELECT id, details FROM payroll WHERE employee_id = '$empid' AND period = '$period' AND payout_month = '$yearmonth'", 'row');

        // get employee details
        $employee = $this->model->getBySQL("SELECT emp_id, emp_fname, emp_mname, emp_lname, emp_level FROM employees WHERE id = '{$empid}'", 'row');

        if (!empty($employee)) {
            $return['employee_name'] = "{$employee['emp_lname']}, {$employee['emp_fname']}";
        }

        if (!empty($payroll)) {
            // encrypt the payroll ID
            $payroll['id'] = $this->mysecurity->encrypt_url($payroll['id']);

            $return['status'] = 'success';
            $return['message'] = 'Payroll details retrieved successfully.';
            $return['data'] = $payroll;
        } else {
            $return['status'] = 'nopayslip';
            $return['message'] = 'No payslip found for the selected employee.';
        }

        die(json_encode($return));
    }

    public function showpayslip()
    {
        if (!IS_AJAX) show_404();
        $period = $this->input->post('period');
        $yearmonth = $this->input->post('yearmonth');

        if (empty($period) || empty($yearmonth)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Employee ID, period, and yearmonth are required.')));
        }

        $yearmonth = date('Y-m', strtotime($yearmonth));

        // update all payrolls status to 1
        if ($this->model->update('payroll', array('status' => 1), "period = '$period' AND payout_month = '$yearmonth'")) {
            $return = array(
                'status' => 'success',
                'message' => 'Payroll status updated successfully.',
            );
        } else {
            $return = array(
                'status' => 'failed',
                'message' => 'Failed to update payroll status.',
            );
        }

        die(json_encode($return));
    }
}
