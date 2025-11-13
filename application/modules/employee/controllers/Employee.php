<?php

class Employee extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data['list'] = $this->model->getBySQL("
            SELECT 
                e.id, 
                e.emp_id, 
                e.emp_fname, 
                e.emp_mname, 
                e.emp_lname, 
                e.emp_level, 
                e.hiring_date, 
                e.email, 
                e.phone, 
                e.status,
                e.emp_supervisor,
                s.emp_fname as sv_fname,
                s.emp_lname as sv_lname
            FROM employees e
            LEFT JOIN employees s ON e.emp_supervisor = s.emp_id
            WHERE e.status != '3' 
            ORDER BY FIELD(e.status, 0, 1) DESC, e.emp_lname ASC, e.emp_fname ASC
        ");

        $data['page_title'] = 'Employee';
        $data['content'] = 'employee/index';
        $this->display($data);
    }

    public function addemployee()
    {
        $data['content'] = 'employee/addemployee';
        $data['page_title'] = 'Add Employee';
        $this->display($data);
    }

    public function updateemployee($userid = '')
    {
        $userid = $this->mysecurity->decrypt_url($userid);

        // Check if employee exists 
        $data['employee'] = $employee = $this->model->getBySQL("SELECT * FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($data['employee'])) redirect('employee');

        $list_details = array(
            'Employee_ID' => $employee['emp_id'],
            'First_Name' => $employee['emp_fname'],
            'Last_Name' => $employee['emp_lname'],
            'Middle_Name' => $employee['emp_mname'],
            'Gender' => !empty($employee['gender']) ? $employee['gender'] : '',
            'Emp_Designation' => !empty($employee['designation']) ? $employee['designation'] : '',
            'Email' => !empty($employee['email']) ? $employee['email'] : '',
            'Date_of_Birth' => !empty($employee['birthdate']) ? date('m/d/Y', strtotime($employee['birthdate'])) : '',
            'Emp_Role' => $employee['emp_level'],
            'Monthly_Salary' => !empty($employee['salary']) ? $this->mysecurity->decrypt_url($employee['salary']) : '',
            'Hired_Date' => $employee['hiring_date'],
        );
        $data['list_details'] = json_encode($list_details);

        $data['content'] = 'employee/addemployee';
        $this->display($data);
    }

    public function processaddemployee()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array('Middle_Name', 'suffix', 'badge_number', 'locker_number', 'password', 'account', 'emp_supervisor', 'Rest_Days');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new employee.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $forupdates = false;
            if (isset($formdata['userid'])) {
                $userid = $this->mysecurity->decrypt_url($formdata['userid']);
                $employee = $this->model->getBySQL("SELECT id, emp_id FROM employees WHERE id = '{$userid}'", 'row');

                // Check if employee exists
                if (empty($employee)) {
                    $result['message'] = 'Employee not found.';
                    die(json_encode($result));
                }

                $forupdates = true;
            }

            if (!$forupdates || $employee['emp_id'] != $formdata['Employee_ID']) {
                if (!empty($this->model->getBySQL("SELECT id FROM employees WHERE emp_id = '{$formdata['Employee_ID']}'"))) {
                    die(json_encode(['message' => 'Employee ID already exists.']));
                }
            }

            // Check for duplicate badge number if provided
            if (!empty($formdata['badge_number'])) {
                $badge_condition = $forupdates ? "AND id != '{$userid}'" : "";
                if (!empty($this->model->getBySQL("SELECT id FROM employees WHERE badge_number = '{$formdata['badge_number']}' {$badge_condition}"))) {
                    die(json_encode(['message' => 'Badge Number already exists.']));
                }
            }

            // format monthly salary remove comma and spaces
            $formdata['Monthly_Salary'] = preg_replace('/[^\d]/', '', $formdata['Monthly_Salary']);

            $to_insert = array(
                'emp_id' => $formdata['Employee_ID'],
                'emp_fname' => $formdata['First_Name'],
                'emp_mname' => $formdata['Middle_Name'],
                'emp_lname' => $formdata['Last_Name'],
                'emp_suffix' => !empty($formdata['suffix']) ? $formdata['suffix'] : '',
                'gender' => $formdata['Gender'],
                'email' => $formdata['Email'],
                'birthdate' => date('Y-m-d', strtotime($formdata['Date_of_Birth'])),
                'emp_level' => strtolower($formdata['Emp_Role']),
                'designation' => strtolower($formdata['Emp_Designation']),
                'hiring_date' => date('Y-m-d', strtotime($formdata['Hired_Date'])),
                'salary' => $this->mysecurity->encrypt_url(!empty($formdata['Monthly_Salary']) ? $formdata['Monthly_Salary'] : 0),
                'badge_number' => !empty($formdata['badge_number']) ? $formdata['badge_number'] : '',
                'locker_number' => !empty($formdata['locker_number']) ? $formdata['locker_number'] : '',
                'emp_password' => !empty($formdata['password']) ? sha1($formdata['password']) : '',
                'account' => !empty($formdata['account']) ? $formdata['account'] : '',
                'emp_supervisor' => !empty($formdata['emp_supervisor']) ? $formdata['emp_supervisor'] : null,
                'added_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
            );

            // Handle Rest Days if provided
            if (!empty($formdata['Rest_Days']) && is_array($formdata['Rest_Days'])) {
                // limit rest days to 2 only
                $formdata['Rest_Days'] = array_slice($formdata['Rest_Days'], 0, 2);

                // convert rest days array to php date number
                $rest_days = array();
                foreach ($formdata['Rest_Days'] as $day) {
                    $rest_days[] = date('N', strtotime($day));
                }

                $to_insert['rest_day'] = implode(',', $rest_days);
            } else {
                // default rest days to Saturday and Sunday
                $to_insert['rest_day'] = '6,7';
            }

            if ($forupdates) {
                unset($to_insert['added_by'], $to_insert['date_added']);

                if ($this->model->update('employees', $to_insert, array('id' => $userid))) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee updated successfully.';
                }
            } else {
                if ($this->model->insert('employees', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'New employee added successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function profile($userid = '')
    {
        $userid = $this->mysecurity->decrypt_url($userid);
        if (empty($userid)) {
            $userid = $this->_logindata['id'];
        }

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT * FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) redirect('employee');

        // get employee details
        $details = ['personal_info', 'emergency_contact', 'educ_background', 'employment_history', 'bank_details'];
        foreach ($details as $detail) {
            $result = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = '{$detail}'", 'row');
            $data[$detail] = !empty($result) ? json_decode($result['value'], true) : [];
        }

        // get employee_requirements
        $employee_requirements = $this->model->getBySQL("SELECT id, value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'employee_requirements'");
        if (!empty($employee_requirements)) {
            foreach ($employee_requirements as $rkey => $req) {
                $employee_requirements[$rkey] = json_decode($req['value'], true);
                $employee_requirements[$rkey]['id'] = $req['id'];
            }
        }
        $data['employee_requirements'] = $employee_requirements;

        $supervisor_name = '';
        if (!empty($employee['emp_supervisor'])) {
            $supervisor = $this->model->getBySQL("SELECT emp_fname, emp_mname, emp_lname, emp_id FROM employees WHERE emp_id = '{$employee['emp_supervisor']}'", 'row');
            if (!empty($supervisor)) {
                $supervisor_name = trim($supervisor['emp_lname'] . ', '  . $supervisor['emp_fname']);
            }
        }

        $employee['supervisor_name'] = $supervisor_name;

        // get employee documents
        $documents = $this->model->getBySQL("SELECT id, value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'employee_document'");
        if (!empty($documents)) {
            $emp_documents = [];
            foreach ($documents as $doc) {
                $doc_info = json_decode($doc['value'], true);
                if (!empty($doc_info)) {
                    $emp_documents[] = array(
                        'id' => $doc['id'],
                        'file_name' => $doc_info['file_name'],
                        'upload_name' => $doc_info['upload_name'],
                        'upload_path' => $doc_info['upload_path'],
                    );
                }
            }

            $data['documents'] = $emp_documents;
        } else {
            $data['documents'] = [];
        }

        $data['employee'] = $employee;
        $data['page_title'] = 'Employee Profile';
        $data['content'] = 'employee/profile';
        $this->display($data);
    }

    public function get_empdetails()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee details.');

        // Check if employee exists 
        // $employee = $this->model->getBySQL("SELECT * FROM employees WHERE id = '{$userid}'", 'row');
        $employee = $this->model->getBySQL("SELECT emp_id, emp_fname, emp_mname, emp_lname, emp_suffix, badge_number, locker_number, hiring_date, account, emp_level, designation, emp_supervisor, profile, address_present, address_permanent FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) die(json_encode($result));

        // Get supervisor name if supervisor is set
        $supervisor_name = '';
        if (!empty($employee['emp_supervisor'])) {
            $supervisor = $this->model->getBySQL("SELECT emp_fname, emp_mname, emp_lname, emp_id FROM employees WHERE emp_id = '{$employee['emp_supervisor']}'", 'row');
            if (!empty($supervisor)) {
                $supervisor_name = trim($supervisor['emp_fname'] . ' ' . ($supervisor['emp_mname'] ? $supervisor['emp_mname'] . ' ' : '') . $supervisor['emp_lname']) . ' (' . $supervisor['emp_id'] . ')';
            }
        }

        if (!empty($employee)) {
            $result['status'] = 'success';
            $result['data'] = array(
                'first_name' => $employee['emp_fname'],
                'last_name' => $employee['emp_lname'],
                'middle_name' => $employee['emp_mname'],
                'suffix' => $employee['emp_suffix'],
                'employee_id' => $employee['emp_id'],
                'badge_number' => $employee['badge_number'],
                'locker_number' => $employee['locker_number'],
                'date_hired' => $employee['hiring_date'],
                'account' => $employee['account'],
                'emp_level' => $employee['emp_level'],
                'emp_designation' => $employee['designation'],
                'emp_supervisor' => $employee['emp_supervisor'],
                'supervisor_name' => $supervisor_name,
                'profile' => $employee['profile'],
                'address_present' => $employee['address_present'],
                'address_permanent' => $employee['address_permanent']
            );
        }

        die(json_encode($result));
    }

    public function update_empdetails()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array('middle_name', 'suffix', 'badge_number', 'locker_number', 'password', 'date_hired', 'account', 'emp_level', 'emp_designation', 'emp_supervisor', 'profile_image');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee details.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['empid']);

            // Check if employee exists
            $employee = $this->model->getBySQL("SELECT id, emp_password FROM employees WHERE id = '{$userid}'", 'row');
            if (empty($employee)) {
                $result['message'] = 'Employee not found.';
                die(json_encode($result));
            }

            // Check for duplicate emp_id
            $checkEmpID = $this->model->getBySQL("SELECT id FROM employees WHERE emp_id = '{$formdata['employee_id']}' AND id != '{$userid}'", 'row');
            if (!empty($checkEmpID)) {
                $result['message'] = 'Duplicate Employee ID found.';
                die(json_encode($result));
            }

            // Check for duplicate badge_number
            if (!empty($formdata['badge_number'])) {
                $checkBadge = $this->model->getBySQL("SELECT id FROM employees WHERE badge_number = '{$formdata['badge_number']}' AND id != '{$userid}'", 'row');
                if (!empty($checkBadge)) {
                    $result['message'] = 'Duplicate Badge Number found.';
                    die(json_encode($result));
                }
            }

            $to_update = array(
                'emp_fname'      => $formdata['first_name'],
                'emp_mname'      => $formdata['middle_name'],
                'emp_lname'      => $formdata['last_name'],
                'emp_suffix'     => $formdata['suffix'],
                'emp_id'         => $formdata['employee_id'],
                'badge_number'   => $formdata['badge_number'],
                'locker_number'   => $formdata['locker_number'],
                'hiring_date'    => date('Y-m-d', strtotime($formdata['date_hired'])),
                'emp_password'  => !empty($formdata['password']) ? sha1($formdata['password']) : $employee['emp_password'],
                'account'        => $formdata['account'],
                'emp_level'      => strtolower($formdata['emp_level']),
                'designation'    => strtolower($formdata['emp_designation']),
                'emp_supervisor' => !empty($formdata['emp_supervisor']) ? $formdata['emp_supervisor'] : null,
            );

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $path = 'uploads/employee_profiles/';
                $folder = create_date_folder($path);

                $filename = sha1(pathinfo($_FILES['profile_image']['name'], PATHINFO_FILENAME) . time()) . '.' . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $file_path = $folder['day'] . $filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], FCPATH . $file_path)) {
                    $profile = array(
                        'file_name' => $_FILES['profile_image']['name'],
                        'file_path' => $file_path,
                    );

                    $to_update['profile'] = json_encode($profile);
                }
            }

            if ($this->model->update('employees', $to_update, array('id' => $userid))) {
                $result['status'] = 'success';
                $result['message'] = 'Employee details updated successfully.';
            }
        }

        die(json_encode($result));
    }

    public function get_empbasicinfo()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee basic info.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id, phone, email, gender, birthdate, address_present, address_permanent FROM employees WHERE id = '{$userid}'", 'row');

        if (!empty($employee)) {
            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'phone' => $employee['phone'],
                'email' => $employee['email'],
                'gender' => $employee['gender'],
                'birthday' => $employee['birthdate'],
                'present_address' => $employee['address_present'],
                'permanent_address' => $employee['address_permanent'],
            );
        } else {
            $result['message'] = 'Employee not found.';
        }
        die(json_encode($result));
    }

    public function update_empbasicinfo()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('phone', 'email', 'gender', 'birthday', 'present_address', 'permanent_address');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee basic info.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);

            // Check if employee exists
            $employee = $this->model->getBySQL("SELECT id FROM employees WHERE id = '{$userid}'", 'row');
            if (empty($employee)) {
                $result['message'] = 'Employee not found.';
                die(json_encode($result));
            }

            $to_update = array(
                'phone' => $formdata['phone'],
                'email' => $formdata['email'],
                'gender' => $formdata['gender'],
                'birthdate' => $formdata['birthday'],
                'address_present' => $formdata['present_address'],
                'address_permanent' => $formdata['permanent_address'],
            );
            if ($this->model->update('employees', $to_update, array('id' => $userid))) {
                $result['status'] = 'success';
                $result['message'] = 'Employee basic info updated successfully.';
            } else {
                $result['message'] = 'Failed to update employee basic info.';
            }
        }

        die(json_encode($result));
    }

    public function get_emppersonalinfo()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee basic info.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }


        $info = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'personal_info'", 'row');

        if (!empty($info) && !empty($info['value'])) {
            $info_details = json_decode($info['value'], true);

            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'tin' => !empty($info_details['tin']) ? $info_details['tin'] : '',
                'sss' => !empty($info_details['sss']) ? $info_details['sss'] : '',
                'pag_ibig' => !empty($info_details['pag_ibig']) ? $info_details['pag_ibig'] : '',
                'phil_health' => !empty($info_details['phil_health']) ? $info_details['phil_health'] : '',
                'hmo_account' => !empty($info_details['hmo_account']) ? $info_details['hmo_account'] : '',
            );
        } else {
            $result['message'] = 'Employee personal info not found.';
        }
        die(json_encode($result));
    }

    public function update_emppersonalinfo()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('tin', 'sss', 'pag_ibig', 'phil_health', 'hmo_account');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee basic info.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
            $datainfo = array(
                'tin' => $formdata['tin'],
                'sss' => $formdata['sss'],
                'pag_ibig' => $formdata['pag_ibig'],
                'phil_health' => $formdata['phil_health'],
                'hmo_account' => $formdata['hmo_account'],
            );

            // Check if employee exists
            $info = $this->model->getBySQL("SELECT id FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'personal_info'", 'row');

            if (empty($info)) {
                $to_insert = array(
                    'emp_id' => $userid,
                    'detail' => 'personal_info',
                    'value' => json_encode($datainfo),
                    'added_by' => $this->_logindata['id'],
                    'updated_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_details', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee personal info added successfully.';
                }
            } else {
                $to_update = array(
                    'value' => json_encode($datainfo),
                    'updated_by' => $this->_logindata['id'],
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_details', $to_update, "id= '{$info['id']}'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee personal info updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function get_emp_bank()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee bank details.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }


        $info = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'bank_details'", 'row');

        if (!empty($info) && !empty($info['value'])) {
            $info_details = json_decode($info['value'], true);

            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'primary_bank' => !empty($info_details['primary_bank']) ? $info_details['primary_bank'] : '',
            );
        } else {
            $result['message'] = 'Employee bank details not found.';
        }
        die(json_encode($result));
    }

    public function update_emp_bank()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('primary_bank', 'secondary_bank');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee bank details.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
            $datainfo = array(
                'primary_bank' => $formdata['primary_bank'],
                'secondary_bank' => $formdata['secondary_bank'],
            );

            // Check if employee exists
            $info = $this->model->getBySQL("SELECT id FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'bank_details'", 'row');

            if (empty($info)) {
                $to_insert = array(
                    'emp_id' => $userid,
                    'detail' => 'bank_details',
                    'value' => json_encode($datainfo),
                    'added_by' => $this->_logindata['id'],
                    'updated_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_details', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee bank details added successfully.';
                }
            } else {
                $to_update = array(
                    'value' => json_encode($datainfo),
                    'updated_by' => $this->_logindata['id'],
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_details', $to_update, "id= '{$info['id']}'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee bank details updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function get_emp_contact()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee emergency contact info.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }


        $info = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'emergency_contact'", 'row');

        if (!empty($info) && !empty($info['value'])) {
            $info_details = json_decode($info['value'], true);

            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'primary_contact' => !empty($info_details['primary_contact']) ? $info_details['primary_contact'] : '',
                'secondary_contact' => !empty($info_details['secondary_contact']) ? $info_details['secondary_contact'] : '',
            );
        } else {
            $result['message'] = 'Employee emergency contact info not found.';
        }
        die(json_encode($result));
    }

    public function update_emp_contact()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('primary_contact', 'secondary_contact');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee emergency contact info.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
            $datainfo = array(
                'primary_contact' => $formdata['primary_contact'],
                'secondary_contact' => $formdata['secondary_contact'],
            );

            // Check if employee exists
            $info = $this->model->getBySQL("SELECT id FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'emergency_contact'", 'row');

            if (empty($info)) {
                $to_insert = array(
                    'emp_id' => $userid,
                    'detail' => 'emergency_contact',
                    'value' => json_encode($datainfo),
                    'added_by' => $this->_logindata['id'],
                    'updated_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_details', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee emergency contact info added successfully.';
                }
            } else {
                $to_update = array(
                    'value' => json_encode($datainfo),
                    'updated_by' => $this->_logindata['id'],
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_details', $to_update, "id= '{$info['id']}'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee emergency contact info updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function get_educ_background()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee educational background info.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }


        $info = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'educ_background'", 'row');

        if (!empty($info) && !empty($info['value'])) {
            $info_details = json_decode($info['value'], true);

            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'educ_background' => !empty($info_details) ? $info_details : [],
            );
        } else {
            $result['message'] = 'Employee educational background info not found.';
        }
        die(json_encode($result));
    }

    public function update_educ_background()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('educ_background');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee educational background info.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);

            $educ_input = $formdata['educ_background'];
            $educ_background = [];

            if (!empty($educ_input['institution_name'])) {
                foreach ($educ_input['institution_name'] as $index => $val) {
                    $educ_background[] = [
                        'institution_name' => $educ_input['institution_name'][$index],
                        'course'           => $educ_input['course'][$index],
                        'start_date'       => $educ_input['start_date'][$index],
                        'end_date'         => $educ_input['end_date'][$index],
                    ];
                }
            }

            // Check if employee exists
            $info = $this->model->getBySQL("SELECT id FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'educ_background'", 'row');

            if (empty($info)) {
                $to_insert = array(
                    'emp_id' => $userid,
                    'detail' => 'educ_background',
                    'value' => json_encode($educ_background),
                    'added_by' => $this->_logindata['id'],
                    'updated_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_details', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee educational background info added successfully.';
                }
            } else {
                $to_update = array(
                    'value' => json_encode($educ_background),
                    'updated_by' => $this->_logindata['id'],
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_details', $to_update, "id= '{$info['id']}'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee educational background info updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function get_employment_history()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));
        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid employee ID.']));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve employee employment history info.');

        // Check if employee exists 
        $employee = $this->model->getBySQL("SELECT emp_id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }


        $info = $this->model->getBySQL("SELECT value FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'employment_history'", 'row');

        if (!empty($info) && !empty($info['value'])) {
            $info_details = json_decode($info['value'], true);

            $result['status'] = 'success';
            $result['data'] = array(
                'emp_id' => $employee['emp_id'],
                'employment_history' => !empty($info_details) ? $info_details : [],
            );
        } else {
            $result['message'] = 'Employee employment history info not found.';
        }
        die(json_encode($result));
    }

    public function update_employment_history()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('employment_history');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update employee employment history info.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);

            $educ_input = $formdata['employment_history'];
            $employment_history = [];

            if (!empty($educ_input['company_name'])) {
                foreach ($educ_input['company_name'] as $index => $val) {
                    $employment_history[] = [
                        'company_name' => $educ_input['company_name'][$index],
                        'designation'           => $educ_input['designation'][$index],
                        'start_date'       => $educ_input['start_date'][$index],
                        'end_date'         => $educ_input['end_date'][$index],
                    ];
                }
            }

            // Check if employee exists
            $info = $this->model->getBySQL("SELECT id FROM employee_details WHERE emp_id = '{$userid}' AND detail = 'employment_history'", 'row');

            if (empty($info)) {
                $to_insert = array(
                    'emp_id' => $userid,
                    'detail' => 'employment_history',
                    'value' => json_encode($employment_history),
                    'added_by' => $this->_logindata['id'],
                    'updated_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_details', $to_insert)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee employment history info added successfully.';
                }
            } else {
                $to_update = array(
                    'value' => json_encode($employment_history),
                    'updated_by' => $this->_logindata['id'],
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                if ($this->model->update('employee_details', $to_update, "id= '{$info['id']}'")) {
                    $result['status'] = 'success';
                    $result['message'] = 'Employee employment history info updated successfully.';
                }
            }
        }

        die(json_encode($result));
    }

    public function employee_document_upload()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('empId'));
        $filename = $this->input->post('document');
        $fileAttachment = $_FILES['File_Attachment'];

        $result = array('status' => 'failed', 'message' => 'Failed to update employee employment history info.');

        if (empty($userid) || empty($filename) || empty($fileAttachment)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid input data.']));
        }
        // Check if employee exists
        $employee = $this->model->getBySQL("SELECT id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            die(json_encode(['status' => 'failed', 'message' => 'Employee not found.']));
        }

        // Process file upload
        $path = 'uploads/employee_documents/';
        $folder = create_date_folder($path);
        $file_path = $folder['day'] . sha1(pathinfo($fileAttachment['name'], PATHINFO_FILENAME) . time()) . '.' . pathinfo($fileAttachment['name'], PATHINFO_EXTENSION);

        if (move_uploaded_file($fileAttachment['tmp_name'], FCPATH . $file_path)) {

            $datainfo = array(
                'file_name' => $filename,
                'upload_name' => $fileAttachment['name'],
                'upload_path' => $file_path,
            );

            // Save document details in the database
            $to_insert = array(
                'emp_id' => $employee['id'],
                'detail' => 'employee_document',
                'value' => json_encode($datainfo),
                'added_by' => $this->_logindata['id'],
                'updated_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            if ($this->model->insert('employee_details', $to_insert)) {
                $result = array('status' => 'success', 'message' => 'Document uploaded successfully.');
            } else {
                $result = array('status' => 'failed', 'message' => 'Failed to save document details in the database.');
            }
        }

        die(json_encode($result));
    }

    public function employee_document_remove()
    {
        if (!IS_AJAX) show_404();
        $docId = $this->mysecurity->decrypt_url($this->input->post('docid'));
        $doctype = $this->input->post('doctype');
        $detailkey = !empty($doctype) ? $this->mysecurity->decrypt_url($doctype) : 'employee_document';

        $result = array('status' => 'failed', 'message' => 'Failed to remove employee document.');

        if (empty($docId)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid document ID.']));
        }

        // Check if document exists
        $document = $this->model->getBySQL("SELECT id, value FROM employee_details WHERE id = '{$docId}' AND detail = '{$detailkey}'", 'row');
        if (empty($document)) {
            die(json_encode(['status' => 'failed', 'message' => 'Document not found.']));
        }

        $doc_details = json_decode($document['value'], true);
        // Delete file from server
        if (file_exists(FCPATH . $doc_details['upload_path'])) {
            unlink(FCPATH . $doc_details['upload_path']);
        }

        // Delete record from database
        if ($this->model->delete('employee_details', array('id' => $docId))) {
            $result = array('status' => 'success', 'message' => 'Document removed successfully.');
        }

        die(json_encode($result));
    }

    public function employee_requirements_upload()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('empId'));
        $filename = $this->input->post('requirements');
        $fileAttachment = $_FILES['File_Attachment'];

        $result = array('status' => 'failed', 'message' => 'Failed to upload employee requirements document.');

        if (empty($userid) || empty($filename) || empty($fileAttachment)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid input data.']));
        }
        // Check if employee exists
        $employee = $this->model->getBySQL("SELECT id FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            die(json_encode(['status' => 'failed', 'message' => 'Employee not found.']));
        }

        // Process file upload
        $path = 'uploads/employee_requirements/';
        $folder = create_date_folder($path);
        $file_path = $folder['day'] . sha1(pathinfo($fileAttachment['name'], PATHINFO_FILENAME) . time()) . '.' . pathinfo($fileAttachment['name'], PATHINFO_EXTENSION);

        if (move_uploaded_file($fileAttachment['tmp_name'], FCPATH . $file_path)) {

            $datainfo = array(
                'file_name' => $filename,
                'upload_name' => $fileAttachment['name'],
                'upload_path' => $file_path,
                'remarks' => $this->input->post('remarks'),
            );

            // Save document details in the database
            $to_insert = array(
                'emp_id' => $employee['id'],
                'detail' => 'employee_requirements',
                'value' => json_encode($datainfo),
                'added_by' => $this->_logindata['id'],
                'updated_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            if ($this->model->insert('employee_details', $to_insert)) {
                $result = array('status' => 'success', 'message' => 'Requirements uploaded successfully.');
            } else {
                $result = array('status' => 'failed', 'message' => 'Failed to save requirements details in the database.');
            }
        }

        die(json_encode($result));
    }

    public function schedule()
    {
        $data['page_title'] = 'Employee Schedule';
        $data['content'] = 'employee/schedule';
        $this->display($data);
    }

    public function get_current_salary()
    {
        if (!IS_AJAX) show_404();
        $userid = $this->mysecurity->decrypt_url($this->input->post('userid'));

        $result = array('status' => 'failed', 'message' => 'Failed to retrieve current salary.');

        if (empty($userid)) {
            die(json_encode(['status' => 'failed', 'message' => 'Invalid user ID.']));
        }

        // Fetch current salary from the database
        $salary = $this->model->getBySQL("SELECT emp_id, salary FROM employees WHERE id = '{$userid}'", 'row');

        $current_salary = 0;
        if (!empty($salary)) {
            $current_salary = $this->mysecurity->decrypt_url($salary['salary']);
            $result = array('status' => 'success', 'data' => ['emp_id' => $salary['emp_id'], 'salary' => !empty($current_salary) ? number_format($current_salary, 2) : '0.00']);
        }

        die(json_encode($result));
    }

    public function update_salary_increase()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $notRequiredFields = array('new_salary', 'effective_date', 'remarks');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update salary increase.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
            $new_salary = floatval(str_replace(',', '', $formdata['new_salary']));
            $effective_date = date('Y-m-d', strtotime($formdata['effective_date']));
            $remarks = $formdata['remarks'];

            // Check if employee exists
            $employee = $this->model->getBySQL("SELECT id, salary FROM employees WHERE id = '{$userid}'", 'row');
            if (empty($employee)) {
                $result['message'] = 'Employee not found.';
                die(json_encode($result));
            }

            // Decrypt current salary
            $current_salary = floatval($this->mysecurity->decrypt_url($employee['salary']));

            if ($new_salary <= $current_salary) {
                $result['message'] = 'New salary must be greater than the current salary.';
                die(json_encode($result));
            }

            // Only update salary if effective date is today or earlier
            if (strtotime($effective_date) <= strtotime(date('Y-m-d'))) {
                $to_update = array(
                    'salary' => $this->mysecurity->encrypt_url($new_salary),
                );

                if ($this->model->update('employees', $to_update, array('id' => $userid))) {
                    // Log salary increase
                    $to_insert = array(
                        'employee_id' => $userid,
                        'old_salary' => $this->mysecurity->encrypt_url($current_salary),
                        'new_salary' => $this->mysecurity->encrypt_url($new_salary),
                        'effective_date' => $effective_date,
                        'remarks' => $remarks,
                        'added_by' => $this->_logindata['id'],
                        'date_added' => date('Y-m-d H:i:s'),
                        'updated_by' => $this->_logindata['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    );

                    if ($this->model->insert('employee_salary', $to_insert)) {
                        $result = array('status' => 'success', 'message' => 'Salary increase updated successfully.');
                    } else {
                        $result['message'] = 'Failed to log salary increase.';
                    }
                }
            } else {
                // Only log the salary increase, do not update salary yet
                $to_insert = array(
                    'employee_id' => $userid,
                    'old_salary' => $this->mysecurity->encrypt_url($current_salary),
                    'new_salary' => $this->mysecurity->encrypt_url($new_salary),
                    'effective_date' => $effective_date,
                    'remarks' => $remarks,
                    'added_by' => $this->_logindata['id'],
                    'date_added' => date('Y-m-d H:i:s'),
                );

                if ($this->model->insert('employee_salary', $to_insert)) {
                    $result = array('status' => 'success', 'message' => 'Salary increase scheduled for effective date.');
                } else {
                    $result['message'] = 'Failed to log salary increase.';
                }
            }
        }

        die(json_encode($result));
    }

    public function check_salary_increase()
    {
        // check all records if salary increase is effective
        $today = date('Y-m-d');
        $pending_increase = $this->model->getBySQL("SELECT * FROM employee_salary WHERE effective_date <= '{$today}' AND status = 0", 'result');

        if (empty($pending_increase)) {
            $result = array('status' => 'failed', 'message' => 'No effective salary increases found.');
            die(json_encode($result));
        }

        // Process each effective salary increase
        foreach ($pending_increase as $increase) {
            // Update employee salary
            $this->model->update('employees', array('salary' => $increase['new_salary']), array('id' => $increase['employee_id']));
        }

        $result = array('status' => 'success', 'message' => 'Salary increases processed successfully.');
        die(json_encode($result));
    }

    public function verify_password()
    {
        $password = $this->input->post('password');
        $user_id = $this->_logindata['id'];
        $verify = $this->model->getBySQL("SELECT id, salary FROM employees WHERE id = '{$user_id}' AND emp_password = SHA1('{$password}')", 'row');

        // Verify password
        if ($verify) {

            $salary = '0.00';
            if (!empty($verify['salary'])) {
                $salary = number_format($this->mysecurity->decrypt_url($verify['salary']), 2);
            }

            $result = array('status' => 'success', 'message' => 'Password verified successfully.', 'salary' => $salary);
        } else {
            $result = array('status' => 'failed', 'message' => 'Incorrect password.');
        }

        die(json_encode($result));
    }

    public function deactivate_employee()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $result = array('status' => 'failed', 'message' => 'Failed to deactivate employee.');

        // Validate required fields
        if (empty($formdata['emp_id']) || empty($formdata['deactivation_status']) || empty($formdata['eligible_for_rehire'])) {
            $result['message'] = 'Missing required fields.';
            die(json_encode($result));
        }

        $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
        if (empty($userid)) {
            $result['message'] = 'Invalid employee ID.';
            die(json_encode($result));
        }

        // Check if employee exists and is active
        $employee = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_lname, status FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }

        if ($employee['status'] == '1') {
            $result['message'] = 'Employee is already deactivated.';
            die(json_encode($result));
        }

        // Prepare deactivation data
        $deactivation_data = array(
            'deactivation_status' => $formdata['deactivation_status'],
            'eligible_for_rehire' => $formdata['eligible_for_rehire'],
            'remarks' => !empty($formdata['remarks']) ? $formdata['remarks'] : '',
            'deactivated_by' => $this->_logindata['id'],
            'deactivated_date' => date('Y-m-d H:i:s')
        );

        // Update employee status to deactivated (status = 3)
        $to_update = array(
            'status' => '1',
            'deactivation_date' => date('Y-m-d H:i:s'),
            'deactivated_by' => $this->_logindata['id'],
            'deactivation_info' => json_encode($deactivation_data)
        );

        // Start transaction
        $this->db->trans_start();

        // Update employee status
        $update_employee = $this->model->update('employees', $to_update, array('id' => $userid));

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['message'] = 'Failed to deactivate employee. Database error occurred.';
        } else {
            $result['status'] = 'success';
            $result['message'] = "Employee {$employee['emp_fname']} {$employee['emp_lname']} has been deactivated successfully.";
        }

        die(json_encode($result));
    }

    public function reactivate_employee()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $result = array('status' => 'failed', 'message' => 'Failed to reactivate employee.');

        // Validate required fields
        if (empty($formdata['emp_id'])) {
            $result['message'] = 'Missing required fields.';
            die(json_encode($result));
        }

        $userid = $this->mysecurity->decrypt_url($formdata['emp_id']);
        if (empty($userid)) {
            $result['message'] = 'Invalid employee ID.';
            die(json_encode($result));
        }

        // Check if employee exists and is deactivated
        $employee = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_lname, status FROM employees WHERE id = '{$userid}'", 'row');
        if (empty($employee)) {
            $result['message'] = 'Employee not found.';
            die(json_encode($result));
        }

        if ($employee['status'] == '0') {
            $result['message'] = 'Employee is already active.';
            die(json_encode($result));
        }

        // Update employee status to active (status = 0)
        $to_update = array(
            'status' => '0',
            'deactivation_date' => NULL,
            'deactivated_by' => NULL,
            'deactivation_info' => NULL
        );

        if ($this->model->update('employees', $to_update, array('id' => $userid))) {
            $result['status'] = 'success';
            $result['message'] = "Employee {$employee['emp_fname']} {$employee['emp_lname']} has been reactivated successfully.";
        } else {
            $result['message'] = 'Failed to reactivate employee. Database error occurred.';
        }

        die(json_encode($result));
    }

    public function get_supervisors()
    {
        if (!IS_AJAX) show_404();

        $search = $this->input->post('q') ?: '';
        $page = (int)($this->input->post('page') ?: 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Build WHERE clause for search
        $whereClause = "status = '0' AND emp_level IN('supervisor')"; // Only active employees
        if (!empty($search)) {
            $whereClause .= " AND (CONCAT(emp_fname, ' ', emp_lname) LIKE '%{$search}%' OR emp_id LIKE '%{$search}%')";
        }

        // Get total count for pagination
        $totalCount = $this->model->getBySQL("SELECT COUNT(*) as count FROM employees WHERE {$whereClause}", 'row')['count'];

        // Get supervisors with pagination
        $supervisors = $this->model->getBySQL("
            SELECT id, emp_id, emp_fname, emp_mname, emp_lname 
            FROM employees 
            WHERE {$whereClause} 
            ORDER BY emp_fname ASC, emp_lname ASC 
            LIMIT {$limit} OFFSET {$offset}
        ");

        $results = [];
        if (!empty($supervisors)) {
            foreach ($supervisors as $supervisor) {
                $fullName = trim($supervisor['emp_fname'] . ' ' . ($supervisor['emp_mname'] ? $supervisor['emp_mname'] . ' ' : '') . $supervisor['emp_lname']);
                $results[] = [
                    'id' => $supervisor['emp_id'],
                    'text' => $fullName . ' (' . $supervisor['emp_id'] . ')'
                ];
            }
        }

        $response = [
            'results' => $results,
            'total_count' => (int)$totalCount
        ];

        header('Content-Type: application/json');
        die(json_encode($response));
    }

    public function get_accounts()
    {
        if (!IS_AJAX) show_404();

        $search = $this->input->post('q') ?: '';
        $page = (int)($this->input->post('page') ?: 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Build WHERE clause for search
        $whereClause = "status = '0' AND account IS NOT NULL AND account != ''"; // Only active employees with accounts
        if (!empty($search)) {
            $whereClause .= " AND (account LIKE '%{$search}%')";
        }

        // Get total count of distinct accounts
        $totalCountQuery = $this->model->getBySQL("SELECT COUNT(DISTINCT account) as count FROM employees WHERE {$whereClause}", 'row');
        $totalCount = !empty($totalCountQuery) ? $totalCountQuery['count'] : 0;

        // Get distinct accounts with pagination
        $accounts = $this->model->getBySQL("
            SELECT DISTINCT account
            FROM employees 
            WHERE {$whereClause} 
            ORDER BY account ASC 
            LIMIT {$limit} OFFSET {$offset}
        ");

        $results = [];
        if (!empty($accounts)) {
            foreach ($accounts as $account) {
                $results[] = [
                    'id' => $account['account'],
                    'text' => $account['account']
                ];
            }
        }

        $response = [
            'results' => $results,
            'total_count' => (int)$totalCount
        ];

        header('Content-Type: application/json');
        die(json_encode($response));
    }

    public function getLatestEmpId()
    {
        if (!IS_AJAX) show_404();
        die(get_latest_empid());
    }
}
