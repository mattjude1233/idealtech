<?php

class Holiday extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($year = '')
    {
        $year = ($year == '') ? date('Y') : $year;

        $holidays = $this->model->getBySQL("SELECT h.id, h.date, h.name, l.value AS type FROM holidays AS h LEFT JOIN admin_lang AS l ON l.keyid = h.type AND l.keyword = 'holiday|type' AND l.status = 1 WHERE h.date LIKE '$year%' AND h.archived = 0 ORDER BY h.date ASC");

        // Add encrypted IDs for frontend use
        if (!empty($holidays)) {
            foreach ($holidays as &$holiday) {
                $holiday['encrypted_id'] = $this->mysecurity->encrypt_url($holiday['id']);
            }
        }

        $data['list'] = $holidays;
        $data['page_title'] = "$year Holiday";
        $data['year'] = $year;
        $data['content'] = 'holiday/index';
        $this->display($data);
    }

    public function addholiday()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new holiday.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_insert = array(
                'name' => $formdata['Holiday_Name'],
                'date' => date('Y-m-d', strtotime($formdata['Holiday_Date'])),
                'type' => $formdata['Holiday_Type'],
                'added_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
            );

            if ($this->model->insert('holidays', $to_insert)) {
                $result['status'] = 'success';
                $result['message'] = 'Holiday added successfully.';
            }
        }

        die(json_encode($result));
    }

    public function getholiday()
    {
        if (!IS_AJAX) show_404();
        $holidayid = $this->mysecurity->decrypt_url($this->input->post('holidayid'));
        if (empty($holidayid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Holiday ID is required.')));
        }

        $return = array('status' => 'failed', 'message' => 'Failed to get holiday details.');

        $holiday_list = $this->model->getBySQL("SELECT h.id, h.date, h.name, h.type FROM holidays AS h WHERE h.id LIKE '$holidayid'", 'row');

        if (!empty($holiday_list)) {
            $return['status'] = 'success';
            $return['message'] = 'Holiday details retrieved successfully.';
            $return['data'] = $holiday_list;
        }

        die(json_encode($return));
    }

    public function updateholiday()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $holidayid = $this->mysecurity->decrypt_url($this->input->post('holidayid'));
        if (empty($holidayid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Holiday ID is required.')));
        }

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update holiday.');

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_update = array(
                'name' => $formdata['Holiday_Name'],
                'date' => date('Y-m-d', strtotime($formdata['Holiday_Date'])),
                'type' => $formdata['Holiday_Type'],
            );

            if ($this->model->update('holidays', $to_update, array('id' => $holidayid))) {
                $result['status'] = 'success';
                $result['message'] = 'Holiday updated successfully.';
            }
        }

        die(json_encode($result));
    }

    public function cancelholiday()
    {
        if (!IS_AJAX) show_404();
        $holidayid = $this->mysecurity->decrypt_url($this->input->post('holidayid'));
        if (empty($holidayid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Holiday ID is required.')));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to cancel holiday.');

        $archive_details = array(
            'archived' => 1,
        );

        if ($this->model->update('holidays', $archive_details, array('id' => $holidayid))) {
            $result['status'] = 'success';
            $result['message'] = 'Holiday cancelled successfully.';
        }

        die(json_encode($result));
    }
}
