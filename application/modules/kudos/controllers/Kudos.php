<?php

class Kudos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($year = '')
    {
        // check if valid year
        if (!empty($year) && !preg_match('/^\d{4}$/', $year)) {
            // reditect to current year
            redirect(base_url('kudos'), 'refresh');
        }


        $year = ($year == '') ? date('Y') : $year;

        $where = '';
        if (!empty($year)) {
            $where = " AND YEAR(k.date_added) = '" . $this->db->escape_str($year) . "' ";
        }
        $data['kudoslist'] = $this->model->getBySQL(" SELECT k.id, k.name AS kudos_name, k.category AS kudos_category, k.path AS kudos_image, k.active, k.status, k.date_added, k.added_by, e.emp_fname AS added_by_name FROM kudos k LEFT JOIN employees e ON k.added_by = e.id WHERE k.status = 1 $where ORDER BY k.active DESC, k.id DESC ");
        $data['kudos_categories'] = $this->model->getBySQL("SELECT DISTINCT category AS name FROM kudos WHERE status = 1");

        $data['page_title'] = "Kudos: " . $year;
        $data['yearsearch'] = $year;
        $data['content'] = 'kudos/index';
        $this->display($data);
    }

    function save()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        $notRequiredFields = array();
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new kudos.');

        // check if $_FILES is set and not empty
        $kudos_details = [];
        if (isset($_FILES['Kudos_File']) && !empty($_FILES['Kudos_File']['name'])) {
            $path = 'uploads/kudos/';
            if (!is_dir(FCPATH . $path)) {
                mkdir(FCPATH . $path, 0777, true);
            }

            $filename = sha1(pathinfo($_FILES['Kudos_File']['name'], PATHINFO_FILENAME) . time()) . '.' . pathinfo($_FILES['Kudos_File']['name'], PATHINFO_EXTENSION);
            $file_path = $path . $filename;

            if (move_uploaded_file($_FILES['Kudos_File']['tmp_name'], FCPATH . $file_path)) {
                $kudos_details = array(
                    'file_name' => $_FILES['Kudos_File']['name'],
                    'file_path' => $file_path,
                );
            } else {
                $errormsg['Kudos_File'] = 'Failed to upload kudos file.';
            }
        } else {
            $errormsg['Kudos_File'] = 'Kudos file is required.';
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            $to_insert = array(
                'name' => $formdata['kudoslist_name'],
                'category' => $formdata['kudoslist_category'],
                'path' => $kudos_details['file_path'],
                'active' => 1,
                'status' => 1,
                'added_by' => $this->_logindata['id'],
                'date_added' => date('Y-m-d H:i:s'),
            );

            if ($this->model->insert('kudos', $to_insert)) {

                // update other status to 0
                $this->model->update('kudos', array('active' => 0), array('id !=' => $this->db->insert_id()));

                $result['status'] = 'success';
                $result['message'] = 'Kudos added successfully.';
            } else {
                $result['message'] = 'Failed to add new kudos.';
            }
        }

        die(json_encode($result));
    }

    function set_active($id)
    {
        if (!IS_AJAX) show_404();
        $id = (int)$id;

        // Set all to inactive
        $this->model->update('kudos', array('active' => 0));
        // Set selected to active
        $this->model->update('kudos', array('active' => 1), array('id' => $id));

        die(json_encode(array('status' => 'success', 'message' => 'Kudos set as active.')));
    }

    function set_inactive($id)
    {
        if (!IS_AJAX) show_404();
        $id = (int)$id;

        // Set selected to inactive
        $this->model->update('kudos', array('active' => 0), array('id' => $id));

        die(json_encode(array('status' => 'success', 'message' => 'Kudos set as inactive.')));
    }

    function delete($id)
    {
        if (!IS_AJAX) show_404();
        $id = (int)$id;

        $kudos = $this->model->getBySQL("SELECT id, path FROM kudos WHERE id = '$id'", 'row');

        if (!empty($kudos)) {
            // Delete file
            if (!empty($kudos['path']) && file_exists(FCPATH . $kudos['path'])) {
                @unlink(FCPATH . $kudos['path']);
            }
            $this->model->delete('kudos', array('id' => $id));
            die(json_encode(array('status' => 'success', 'message' => 'Kudos deleted successfully.')));
        } else {
            die(json_encode(array('status' => 'error', 'message' => 'Kudos not found.')));
        }
    }
}
