<?php

class MY_Model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getRows($field = '*', $table = '', $where = '', $orderby = '')
    {
        $this->db->select($field);
        $this->db->from($table);
        if (!empty($where))
            $this->db->where($where);

        if (!empty($orderby))
            $this->db->order_by($orderby);

        return $this->db->get()->result_array();
    }

    public function getRow($field = '*', $table = '', $where = '', $orderby = '')
    {
        $this->db->select($field);
        $this->db->from($table);
        if (!empty($where))
            $this->db->where($where);

        if (!empty($orderby))
            $this->db->order_by($orderby);

        return $this->db->get()->row_array();
    }

    public function getBySQL($query = '', $result = '')
    {
        $query = $this->db->query($query);

        if ($query->num_rows() > 0) {
            if (!empty($result) && $result == 'row') {
                return $query->row_array();
            } else {
                return $query->result_array();
            }
        } else {
            return false;
        }
    }

    public function insert($table = '', $form_data = '', $return_last_id = false)
    {

        if ($this->db->insert($table, $form_data))
            return $return_last_id ? $this->db->insert_id() : true;

        return false;
    }

    public function update($table, $form_data,  $id = '')
    {

        if (!empty($id)) {
            $this->db->where($id);
        }

        if ($this->db->update($table, $form_data))
            return true;

        return false;
    }

    public function delete($table, $where)
    {

        if ($this->db->delete($table, $where))
            return true;

        return false;
    }

    public function count_rows($sql = '', $table = '', $database = '')
    {

        if (!empty($sql)) {

            if (!empty($database)) {
                $db = $this->load->database($database, TRUE);
                $query = $db->query($sql);
            } else {
                $query = $this->db->query($sql);
            }

            return $query->num_rows();
        }

        return $this->db->count_all($table);
    }
}
