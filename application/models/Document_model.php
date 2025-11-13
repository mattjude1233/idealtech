<?php

class Document_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get documents by category with pagination
     */
    public function get_documents($category = '', $limit = 0, $offset = 0, $active_only = true)
    {
        $this->db->select('d.*, e.emp_fname, e.emp_lname');
        $this->db->from('documents d');
        $this->db->join('employees e', 'd.uploaded_by = e.id', 'left');
        
        if (!empty($category)) {
            $this->db->where('d.category', $category);
        }
        
        if ($active_only) {
            $this->db->where('d.is_active', 1);
            $this->db->where('d.archived', 0);
        }
        
        $this->db->order_by('d.is_featured', 'DESC');
        $this->db->order_by('d.sort_order', 'ASC');
        $this->db->order_by('d.upload_date', 'DESC');
        
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Get document by ID
     */
    public function get_document_by_id($id)
    {
        $this->db->select('d.*, e.emp_fname, e.emp_lname');
        $this->db->from('documents d');
        $this->db->join('employees e', 'd.uploaded_by = e.id', 'left');
        $this->db->where('d.id', $id);
        $this->db->where('d.archived', 0);
        
        return $this->db->get()->row_array();
    }

    /**
     * Insert new document
     */
    public function insert_document($data)
    {
        return $this->insert('documents', $data, true);
    }

    /**
     * Update document
     */
    public function update_document($id, $data)
    {
        return $this->update('documents', $data, "id = '$id'");
    }

    /**
     * Delete document (soft delete)
     */
    public function delete_document($id)
    {
        return $this->update('documents', array('archived' => 1), "id = '$id'");
    }

    /**
     * Record document view
     */
    public function record_view($document_id, $employee_id, $ip_address = '')
    {
        // Check if view already recorded today
        $today = date('Y-m-d');
        $existing = $this->getRow('id', 'document_views', 
            "document_id = '$document_id' AND employee_id = '$employee_id' AND DATE(view_date) = '$today'");
        
        if (!$existing) {
            // Insert new view record
            $view_data = array(
                'document_id' => $document_id,
                'employee_id' => $employee_id,
                'ip_address' => $ip_address
            );
            $this->insert('document_views', $view_data);
            
            // Update view count
            $this->db->query("UPDATE documents SET view_count = view_count + 1 WHERE id = '$document_id'");
        }
    }

    /**
     * Get document categories
     */
    public function get_categories()
    {
        return $this->getRows('keyid, value', 'admin_lang', "keyword = 'document|category' AND status = 1", 'value ASC');
    }

    /**
     * Count documents by category
     */
    public function count_documents($category = '', $active_only = true)
    {
        $this->db->from('documents');
        
        if (!empty($category)) {
            $this->db->where('category', $category);
        }
        
        if ($active_only) {
            $this->db->where('is_active', 1);
            $this->db->where('archived', 0);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Get recent documents
     */
    public function get_recent_documents($limit = 5)
    {
        $this->db->select('d.*, e.emp_fname, e.emp_lname, al.value as category_name');
        $this->db->from('documents d');
        $this->db->join('employees e', 'd.uploaded_by = e.id', 'left');
        $this->db->join('admin_lang al', "d.category = al.keyid AND al.keyword = 'document|category'", 'left');
        $this->db->where('d.is_active', 1);
        $this->db->where('d.archived', 0);
        $this->db->order_by('d.upload_date', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    /**
     * Search documents
     */
    public function search_documents($search_term, $category = '')
    {
        $this->db->select('d.*, e.emp_fname, e.emp_lname, al.value as category_name');
        $this->db->from('documents d');
        $this->db->join('employees e', 'd.uploaded_by = e.id', 'left');
        $this->db->join('admin_lang al', "d.category = al.keyid AND al.keyword = 'document|category'", 'left');
        
        $this->db->group_start();
        $this->db->like('d.title', $search_term);
        $this->db->or_like('d.description', $search_term);
        $this->db->or_like('d.original_name', $search_term);
        $this->db->group_end();
        
        if (!empty($category)) {
            $this->db->where('d.category', $category);
        }
        
        $this->db->where('d.is_active', 1);
        $this->db->where('d.archived', 0);
        $this->db->order_by('d.upload_date', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Validate file upload
     */
    public function validate_file($file_info)
    {
        $allowed_types = array('pdf', 'jpg', 'jpeg', 'png', 'gif');
        $max_size = 10 * 1024 * 1024; // 10MB
        
        $errors = array();
        
        // Check file size
        if ($file_info['size'] > $max_size) {
            $errors[] = 'File size exceeds 10MB limit';
        }
        
        // Check file type
        $file_ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = 'File type not allowed. Only PDF, JPG, JPEG, PNG, and GIF files are allowed';
        }
        
        // Check for upload errors
        if ($file_info['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error occurred';
        }
        
        return $errors;
    }
}