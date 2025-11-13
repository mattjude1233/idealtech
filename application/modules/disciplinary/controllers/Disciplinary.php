<?php

class Disciplinary extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {


        $where_con = 'ed.status != 2 AND archived != 1';
        if (!check_function('manage_disciplinary')) {
            $where_con .= ' AND ed.employee_id = ' . $this->_logindata['id'];
        }


        $data['list'] = $this->model->getBySQL("SELECT ed.id, ed.employee_id, CONCAT(e.emp_fname, ' ', e.emp_lname) AS employee_name, ed.date_of_incident, ed.violation, ed.violation_details, ed.nte_date, ed.nte_deadline, ed.nte_reply_date, ed.employee_explanation, ed.notice_of_decision, ed.employee_action_plan, ed.offense, ed.offense_level, ed.offense_sanction, ed.suspension_dates, ed.status FROM employee_discipline AS ed LEFT JOIN employees AS e ON e.id = ed.employee_id WHERE $where_con ORDER BY ed.date_added DESC");

        $data['employees'] = $this->model->getBySQL("SELECT id, emp_fname, emp_lname FROM employees WHERE status = 0 ORDER BY emp_lname ASC, emp_fname ASC");
        $data['page_title'] = "Disciplinary";
        $data['content'] = 'disciplinary/index';
        $this->display($data);
    }

    public function disciplinary_pdf($disciplinaryid = null)
    {
        if (empty($disciplinaryid)) {
            show_404();
        }

        $disciplinaryid = $this->mysecurity->decrypt_url($disciplinaryid);
        if (empty($disciplinaryid)) {
            show_404();
        }

        $data = $this->model->getBySQL("SELECT d.*, ed.value AS emp_level, e.account, CONCAT(e.emp_fname, ' ', e.emp_lname) AS employee_name, CONCAT(ld.emp_fname, ' ', ld.emp_lname) AS lead_name FROM employee_discipline AS d LEFT JOIN employees AS e ON d.employee_id = e.id LEFT JOIN admin_lang AS ed ON ed.keyid = e.emp_level AND ed.keyword = 'user|level' LEFT JOIN employees AS ld ON ld.emp_id = e.emp_supervisor WHERE d.id = '$disciplinaryid' AND d.archived != 1", "row");
        if (empty($data)) {
            show_404();
        }

        $data['attachments'] = !empty($data['attachments']) ? json_decode($data['attachments'], true) : [];
        $data['suspension_dates'] = !empty($data['suspension_dates']) ? json_decode($data['suspension_dates'], true) : array();

        // sort suspension dates ASC
        sort($data['suspension_dates']);

        if (!empty($data['suspension_dates'])) {
            foreach ($data['suspension_dates'] as $key => $value) {
                $data['suspension_dates'][$key] = date('F d, Y', strtotime($value));
            }
            $data['suspension_dates'] = json_encode($data['suspension_dates']);
        }

        $this->load->view('disciplinary/pdf_template', @$data);
    }

    public function getdisciplinary()
    {
        if (!IS_AJAX) show_404();
        $disciplinaryid = $this->mysecurity->decrypt_url($this->input->post('disciplinaryid'));
        if (empty($disciplinaryid)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Disciplinary ID is required.')));
        }

        $data = $this->model->getBySQL("SELECT * FROM employee_discipline WHERE id = '$disciplinaryid'", "row");
        if (empty($data)) {
            die(json_encode(array('status' => 'failed', 'message' => 'Disciplinary action not found.')));
        } else {
            $data['id'] = $this->mysecurity->encrypt_url($data['id']);
            $data['attachments'] = !empty($data['attachments']) ? json_decode($data['attachments'], true) : [];


            $data['suspension_dates'] = !empty($data['suspension_dates']) ? json_decode($data['suspension_dates'], true) : array();

            // sort suspension dates ASC
            sort($data['suspension_dates']);

            if (!empty($data['suspension_dates'])) {
                foreach ($data['suspension_dates'] as $key => $value) {
                    $data['suspension_dates'][$key] = date('F d, Y', strtotime($value));
                }
                $data['suspension_dates'] = json_encode($data['suspension_dates']);
            }
        }

        die(json_encode(array('status' => 'success', 'data' => $data)));
    }

    function save()
    {
        if (!IS_AJAX) show_404();

        $formdata = $this->input->post();

        $notRequiredFields = array('Employee', 'Date_Of_Incident', 'Violations', 'Details_of_Violations', 'NTE_Date', 'NTE_Deadline', 'NTE_Reply_Date', 'Employee_Explanation', 'Employee_Action_Plan', 'Notice_of_Decision', 'Offense', 'Level_of_Offense', 'Sanction', 'Suspension', 'Status', 'removed_attachments');

        $errormsg = $this->validatefields($formdata, $notRequiredFields);
        $result   = array('status' => 'failed', 'message' => 'Failed to save disciplinary action.');

        // 🔎 Check if record exists (when editing)
        $disciplinaryId = !empty($formdata['disciplinary_id']) ? $this->mysecurity->decrypt_url($formdata['disciplinary_id']) : null;

        $existingDisciplinary = [];
        if ($disciplinaryId) {
            $existingDisciplinary = $this->model->getBySQL("SELECT id, attachments FROM employee_discipline WHERE id = '$disciplinaryId' AND archived != 1", "row");
            if (empty($existingDisciplinary)) {
                $errormsg['Not_Exist'] = 'Disciplinary action not found or has been archived.';
            }
        }

        if (!empty($errormsg)) {
            $result['message'] = showError($errormsg);
            die(json_encode($result));
        }

        // 🗓️ Normalize suspension dates
        $formdata["Suspension"] = !empty($formdata["Suspension"]) ? array_filter($formdata["Suspension"]) : array();
        if (!empty($formdata["Suspension"])) {
            foreach ($formdata["Suspension"] as $key => $value) {
                $formdata["Suspension"][$key] = date('Y-m-d', strtotime($value));
            }
        }

        // 🖼️ Process base64 images inside CKEditor fields
        $ckeditorFields = ["Details_of_Violations", "Employee_Explanation", "Employee_Action_Plan", "Notice_of_Decision"];
        foreach ($ckeditorFields as $field) {
            if (!empty($formdata[$field])) {
                $formdata[$field] = $this->processInlineImages($formdata[$field], 'uploads/disciplinary_inline/');
            }
        }

        // 🧾 Build save payload
        // Build $to_save, but for edit (when $disciplinaryId is set), only include fields that are present and not empty in $formdata
        $fields = [
            'employee_id'          => ["Employee", 0],
            'date_of_incident'     => ["Date_Of_Incident", date('Y-m-d 00:00:00')],
            'violation'            => ["Violations", ''],
            'violation_details'    => ["Details_of_Violations", ''],
            'nte_date'             => ["NTE_Date", null],
            'nte_deadline'         => ["NTE_Deadline", null],
            'employee_explanation' => ["Employee_Explanation", ''],
            'nte_reply_date'       => ["NTE_Reply_Date", null],
            'notice_of_decision'   => ["Notice_of_Decision", ''],
            'employee_action_plan' => ["Employee_Action_Plan", ''],
            'offense'              => ["Offense", ''],
            'offense_level'        => ["Level_of_Offense", ''],
            'offense_sanction'     => ["Sanction", ''],
            'suspension_dates'     => ["Suspension", ''],
            'status'               => ["Status", '']
        ];

        $to_save = [];

        foreach ($fields as $db_field => $info) {
            $form_field = $info[0];
            $default    = $info[1];

            // Special handling for date fields and arrays
            if ($db_field === 'date_of_incident' || $db_field === 'nte_date' || $db_field === 'nte_deadline' || $db_field === 'nte_reply_date') {
                if (!empty($formdata[$form_field])) {
                    $to_save[$db_field] = date('Y-m-d 00:00:00', strtotime($formdata[$form_field]));
                } elseif (!$disciplinaryId) {
                    $to_save[$db_field] = $default;
                }
                // If editing and empty, skip (do not update)
            } elseif ($db_field === 'suspension_dates') {
                if (!empty($formdata[$form_field])) {
                    $to_save[$db_field] = json_encode($formdata[$form_field]);
                } elseif (!$disciplinaryId) {
                    $to_save[$db_field] = $default;
                }
            } else {
                if (isset($formdata[$form_field]) && $formdata[$form_field] !== '') {
                    $to_save[$db_field] = $formdata[$form_field];
                } elseif (!$disciplinaryId) {
                    $to_save[$db_field] = $default;
                }
            }
        }

        // 🔄 Handle old attachments
        $existing_attachments = !empty($existingDisciplinary['attachments']) ? json_decode($existingDisciplinary['attachments'], true) : [];
        $removed_attachments  = !empty($formdata['removed_attachments']) ? json_decode($formdata['removed_attachments'], true) : [];

        if (!empty($removed_attachments)) {
            foreach ($removed_attachments as $encoded) {
                $decoded = explode('||', urldecode(base64_decode($encoded)));
                if (count($decoded) === 2) {
                    $remove_path = $decoded[0];

                    if (file_exists(FCPATH . $remove_path)) {
                        @unlink(FCPATH . $remove_path);
                    }

                    $existing_attachments = array_filter($existing_attachments, function ($item) use ($remove_path) {
                        return $item['file_path'] !== $remove_path;
                    });
                }
            }
        }

        // 📎 Handle new file uploads (non-CKEditor attachments)
        $uploaded_attachments = [];
        if (!empty($_FILES['New_Attachments']['name'][0])) {
            $path   = 'uploads/disciplinary_attachments/';
            $folder = create_date_folder($path);

            foreach ($_FILES['New_Attachments']['name'] as $i => $name) {
                if ($_FILES['New_Attachments']['error'][$i] === 0) {
                    $ext        = pathinfo($name, PATHINFO_EXTENSION);
                    $safeName   = sha1(pathinfo($name, PATHINFO_FILENAME) . time() . $i) . '.' . $ext;
                    $target_rel = $folder['day'] . $safeName;

                    if (move_uploaded_file($_FILES['New_Attachments']['tmp_name'][$i], FCPATH . $target_rel)) {
                        $uploaded_attachments[] = [
                            'file_name' => $name,
                            'file_path' => $target_rel
                        ];
                    }
                }
            }
        }

        // 🧩 Combine old + new
        $all_attachments        = array_merge($existing_attachments, $uploaded_attachments);
        $to_save['attachments'] = json_encode(array_values($all_attachments));

        // 💾 Insert/Update
        if ($disciplinaryId) {
            $this->model->update('employee_discipline', $to_save, ['id' => $disciplinaryId]);
            $result['status']  = 'success';
            $result['message'] = 'Disciplinary action updated successfully.';
        } else {
            $to_save['added_by']   = $this->_logindata['id'];
            $to_save['date_added'] = date('Y-m-d H:i:s');

            if ($this->model->insert('employee_discipline', $to_save)) {
                $result['status']  = 'success';
                $result['message'] = 'Disciplinary action added successfully.';
            }
        }

        die(json_encode($result));
    }

    function cancel()
    {
        if (!IS_AJAX) show_404();

        $formdata = $this->input->post();
        $disciplinaryId = $this->mysecurity->decrypt_url($formdata['disciplinaryid']);

        if (empty($disciplinaryId)) {
            die(json_encode(['status' => 'failed', 'message' => 'Disciplinary ID is required.']));
        }

        $updateData = [
            'archived' => 1,
            'archived_by' => $this->_logindata['id'],
            'archived_at' => date('Y-m-d H:i:s')
        ];

        $result = ['status' => 'failed', 'message' => 'Failed to delete disciplinary action.'];

        if ($this->model->update('employee_discipline', $updateData, ['id' => $disciplinaryId])) {
            $result = ['status' => 'success', 'message' => 'Disciplinary action deleted successfully.'];
        }

        die(json_encode($result));
    }

    private function processInlineImages($html, $basePath = 'uploads/disciplinary_inline/')
    {
        if (trim($html) === '') return $html;

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $imgs = $doc->getElementsByTagName('img');
        if (!$imgs || $imgs->length === 0) return $html;

        $folder = create_date_folder($basePath); // ['day' => 'uploads/.../YYYY/mm/dd/']

        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, 'data:image') !== 0) continue;

            if (!preg_match('#^data:image/(png|jpe?g|gif|webp);base64,(.+)$#i', $src, $m)) continue;
            $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
            $bin = base64_decode(str_replace(' ', '+', $m[2]));
            if ($bin === false) continue;

            $safeName = sha1(uniqid('', true)) . '.' . $ext;
            $relPath  = $folder['day'] . $safeName;         // e.g. uploads/disciplinary_inline/2025/08/18/xxx.jpg
            $absPath  = FCPATH . $relPath;
            if (!is_dir(dirname($absPath))) @mkdir(dirname($absPath), 0755, true);
            if (@file_put_contents($absPath, $bin) !== false) {
                // ✅ Use absolute URL so CKEditor won’t prepend /disciplinary/
                $img->setAttribute('src', base_url($relPath));
                $img->removeAttribute('onerror');
                $img->removeAttribute('onclick');
            }
        }
        return $doc->saveHTML();
    }

    public function search_violations()
    {
        if (!IS_AJAX) show_404();
        
        $searchTerm = $this->input->get('q');
        $page = (int)$this->input->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $result = array(
            'items' => array(),
            'pagination' => array('more' => false)
        );
        
        if (empty($searchTerm)) {
            // Return recent violations if no search term
            $violations = $this->model->getBySQL("
                SELECT DISTINCT violation 
                FROM employee_discipline 
                WHERE violation IS NOT NULL AND violation != '' 
                ORDER BY id DESC 
                LIMIT $limit OFFSET $offset
            ");
        } else {
            // Search for violations containing the search term
            $searchTerm = $this->db->escape_like_str($searchTerm);
            $violations = $this->model->getBySQL("
                SELECT DISTINCT violation 
                FROM employee_discipline 
                WHERE violation LIKE '%$searchTerm%' 
                AND violation IS NOT NULL AND violation != '' 
                ORDER BY 
                    CASE WHEN violation LIKE '$searchTerm%' THEN 1 ELSE 2 END,
                    violation ASC
                LIMIT $limit OFFSET $offset
            ");
        }
        
        if (!empty($violations)) {
            foreach ($violations as $violation) {
                $result['items'][] = array(
                    'violation' => $violation['violation']
                );
            }
            
            // Check if there are more results
            $totalCount = count($violations);
            $result['pagination']['more'] = $totalCount >= $limit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function search_offenses()
    {
        if (!IS_AJAX) show_404();
        
        $searchTerm = $this->input->get('q');
        $page = (int)$this->input->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $result = array(
            'items' => array(),
            'pagination' => array('more' => false)
        );
        
        if (empty($searchTerm)) {
            // Return recent offenses if no search term
            $offenses = $this->model->getBySQL("
                SELECT DISTINCT offense 
                FROM employee_discipline 
                WHERE offense IS NOT NULL AND offense != '' 
                ORDER BY id DESC 
                LIMIT $limit OFFSET $offset
            ");
        } else {
            // Search for offenses containing the search term
            $searchTerm = $this->db->escape_like_str($searchTerm);
            $offenses = $this->model->getBySQL("
                SELECT DISTINCT offense 
                FROM employee_discipline 
                WHERE offense LIKE '%$searchTerm%' 
                AND offense IS NOT NULL AND offense != '' 
                ORDER BY 
                    CASE WHEN offense LIKE '$searchTerm%' THEN 1 ELSE 2 END,
                    offense ASC
                LIMIT $limit OFFSET $offset
            ");
        }
        
        if (!empty($offenses)) {
            foreach ($offenses as $offense) {
                $result['items'][] = array(
                    'offense' => $offense['offense']
                );
            }
            
            // Check if there are more results
            $totalCount = count($offenses);
            $result['pagination']['more'] = $totalCount >= $limit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function search_level_of_offense()
    {
        if (!IS_AJAX) show_404();
        
        $searchTerm = $this->input->get('q');
        $page = (int)$this->input->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $result = array(
            'items' => array(),
            'pagination' => array('more' => false)
        );
        
        if (empty($searchTerm)) {
            // Return recent offense levels if no search term
            $levels = $this->model->getBySQL("
                SELECT DISTINCT offense_level 
                FROM employee_discipline 
                WHERE offense_level IS NOT NULL AND offense_level != '' 
                ORDER BY id DESC 
                LIMIT $limit OFFSET $offset
            ");
        } else {
            // Search for offense levels containing the search term
            $searchTerm = $this->db->escape_like_str($searchTerm);
            $levels = $this->model->getBySQL("
                SELECT DISTINCT offense_level 
                FROM employee_discipline 
                WHERE offense_level LIKE '%$searchTerm%' 
                AND offense_level IS NOT NULL AND offense_level != '' 
                ORDER BY 
                    CASE WHEN offense_level LIKE '$searchTerm%' THEN 1 ELSE 2 END,
                    offense_level ASC
                LIMIT $limit OFFSET $offset
            ");
        }
        
        if (!empty($levels)) {
            foreach ($levels as $level) {
                $result['items'][] = array(
                    'offense_level' => $level['offense_level']
                );
            }
            
            // Check if there are more results
            $totalCount = count($levels);
            $result['pagination']['more'] = $totalCount >= $limit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function search_sanctions()
    {
        if (!IS_AJAX) show_404();
        
        $searchTerm = $this->input->get('q');
        $page = (int)$this->input->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $result = array(
            'items' => array(),
            'pagination' => array('more' => false)
        );
        
        if (empty($searchTerm)) {
            // Return recent sanctions if no search term
            $sanctions = $this->model->getBySQL("
                SELECT DISTINCT offense_sanction 
                FROM employee_discipline 
                WHERE offense_sanction IS NOT NULL AND offense_sanction != '' 
                ORDER BY id DESC 
                LIMIT $limit OFFSET $offset
            ");
        } else {
            // Search for sanctions containing the search term
            $searchTerm = $this->db->escape_like_str($searchTerm);
            $sanctions = $this->model->getBySQL("
                SELECT DISTINCT offense_sanction 
                FROM employee_discipline 
                WHERE offense_sanction LIKE '%$searchTerm%' 
                AND offense_sanction IS NOT NULL AND offense_sanction != '' 
                ORDER BY 
                    CASE WHEN offense_sanction LIKE '$searchTerm%' THEN 1 ELSE 2 END,
                    offense_sanction ASC
                LIMIT $limit OFFSET $offset
            ");
        }
        
        if (!empty($sanctions)) {
            foreach ($sanctions as $sanction) {
                $result['items'][] = array(
                    'sanction' => $sanction['offense_sanction']
                );
            }
            
            // Check if there are more results
            $totalCount = count($sanctions);
            $result['pagination']['more'] = $totalCount >= $limit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
