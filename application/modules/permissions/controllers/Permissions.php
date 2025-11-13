<?php

class Permissions extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // Check if user has admin access and permission to manage permissions
        if (!check_function('manage_permissions') && !check_function('admin')) {
            show_404();
        }
    }

    function index()
    {
        // Get all admin tabs with their permissions
        $data['permissions'] = $this->model->getBySQL("
            SELECT * FROM admin_tabs 
            ORDER BY grouping ASC, position ASC, name ASC
        ", 'result');

        // Get all user levels for dropdown
        $data['user_levels'] = $this->model->getBySQL("
            SELECT DISTINCT keyid as level_key, value as level_name 
            FROM admin_lang 
            WHERE keyword = 'user|level' AND status = 1 
            ORDER BY value ASC
        ", 'result');

        // Get all employees for special user assignment
        $data['employees'] = $this->model->getBySQL("
            SELECT emp_id, emp_fname, emp_lname, emp_level 
            FROM employees 
            WHERE status = 0 
            ORDER BY emp_lname ASC, emp_fname ASC
        ", 'result');

        $data['page_title'] = 'Permissions Management';
        $data['content'] = 'permissions/index';
        $this->display($data);
    }

    function add_permission()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();

        // Debug: log the received data (remove this line after testing)
        error_log('Received form data: ' . print_r($formdata, true));

        $notRequiredFields = array('special_user', 'exclude_user', 'icon', 'link');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to add new permission.');

        // Validate required fields
        if (empty($formdata['keyword'])) {
            $errormsg['keyword'] = 'Keyword is required.';
        }
        if (empty($formdata['name'])) {
            $errormsg['name'] = 'Name is required.';
        }
        // Check for level array (since form sends level[])
        if (empty($formdata['level']) || (is_array($formdata['level']) && empty(array_filter($formdata['level'])))) {
            $errormsg['level'] = 'Level is required.';
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            // Handle multiple levels
            $levels = is_array($formdata['level']) ? implode(',', $formdata['level']) : $formdata['level'];

            // Prepare data for insertion
            $to_insert = array(
                'keyword' => trim($formdata['keyword']),
                'name' => trim($formdata['name']),
                'link' => trim($formdata['link']),
                'grouping' => (int) $formdata['grouping'],
                'level' => trim($levels),
                'special_user' => trim($formdata['special_user']),
                'exclude_user' => trim($formdata['exclude_user']),
                'icon' => trim($formdata['icon']),
                'position' => (int) $formdata['position'],
                'type' => (int) $formdata['type'],
                'status' => 1
            );

            if ($this->model->insert('admin_tabs', $to_insert)) {
                $result['status'] = 'success';
                $result['message'] = 'Permission added successfully.';
            }
        }

        die(json_encode($result));
    }

    function get_permission()
    {
        if (!IS_AJAX) show_404();
        $permission_id = $this->input->post('permission_id');

        if (empty($permission_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Permission ID is required.')));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to get permission details.');

        $permission = $this->model->getBySQL("SELECT * FROM admin_tabs WHERE id = '{$permission_id}'", 'row');

        if (!empty($permission)) {
            $result['status'] = 'success';
            $result['message'] = 'Permission details retrieved successfully.';
            $result['data'] = $permission;
        }

        die(json_encode($result));
    }

    function update_permission()
    {
        if (!IS_AJAX) show_404();
        $formdata = $this->input->post();
        $permission_id = $this->input->post('permission_id');

        // Debug: log the received data (remove this line after testing)
        error_log('Update - Received form data: ' . print_r($formdata, true));

        if (empty($permission_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Permission ID is required.')));
        }

        $notRequiredFields = array('special_user', 'exclude_user', 'icon', 'link', 'level');
        $errormsg = $this->validatefields($formdata, $notRequiredFields);

        $result = array('status' => 'failed', 'message' => 'Failed to update permission.');

        // Validate required fields
        if (empty($formdata['keyword'])) {
            $errormsg['keyword'] = 'Keyword is required.';
        }
        if (empty($formdata['name'])) {
            $errormsg['name'] = 'Name is required.';
        }

        if (
            empty($formdata['level']) ||
            (is_array($formdata['level']) && empty(array_filter($formdata['level'], fn($v) => trim($v) !== '')))
        ) {
            $errormsg['level'] = 'Level is required.';
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
        } else {
            // Handle multiple levels
            $levels = is_array($formdata['level']) ? implode(',', array_unique($formdata['level'])) : $formdata['level'];

            // Prepare data for update
            $to_update = array(
                'keyword' => trim($formdata['keyword']),
                'name' => trim($formdata['name']),
                'link' => trim($formdata['link']),
                'grouping' => (int) $formdata['grouping'],
                'level' => trim($levels),
                'special_user' => trim($formdata['special_user']),
                'exclude_user' => trim($formdata['exclude_user']),
                'icon' => trim($formdata['icon']),
                'position' => (int) $formdata['position'],
                'type' => (int) $formdata['type'],
                'status' => (int) $formdata['status']
            );

            if ($this->model->update('admin_tabs', $to_update, array('id' => $permission_id))) {
                $result['status'] = 'success';
                $result['message'] = 'Permission updated successfully.';
            }
        }

        die(json_encode($result));
    }

    function delete_permission()
    {
        if (!IS_AJAX) show_404();
        $permission_id = $this->input->post('permission_id');

        if (empty($permission_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Permission ID is required.')));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to delete permission.');

        if ($this->model->delete('admin_tabs', array('id' => $permission_id))) {
            $result['status'] = 'success';
            $result['message'] = 'Permission deleted successfully.';
        }

        die(json_encode($result));
    }

    function toggle_status()
    {
        if (!IS_AJAX) show_404();
        $permission_id = $this->input->post('permission_id');

        if (empty($permission_id)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Permission ID is required.')));
        }

        $result = array('status' => 'failed', 'message' => 'Failed to toggle permission status.');

        // Get current status
        $current = $this->model->getBySQL("SELECT status FROM admin_tabs WHERE id = '{$permission_id}'", 'row');

        if (!empty($current)) {
            $new_status = $current['status'] == 1 ? 0 : 1;

            if ($this->model->update('admin_tabs', array('status' => $new_status), array('id' => $permission_id))) {
                $result['status'] = 'success';
                $result['message'] = 'Permission status updated successfully.';
                $result['new_status'] = $new_status;
            }
        }

        die(json_encode($result));
    }
}
