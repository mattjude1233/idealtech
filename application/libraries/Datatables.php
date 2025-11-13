<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Datatables
{
    protected $ci;
    protected $table;
    protected $select = [];
    protected $joins = [];
    protected $where = [];
    protected $columns = [];
    protected $unset_columns = [];
    protected $edit_columns = [];

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function select($columns)
    {
        foreach (explode(',', $columns) as $column) {
            $column = trim($column);
            $this->columns[] = $column;
            $this->select[] = $column;
        }
        $this->ci->db->select($columns);
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        $this->ci->db->from($table);
        return $this;
    }

    public function join($table, $condition, $type = 'left')
    {
        $this->joins[] = [$table, $condition, $type];
        $this->ci->db->join($table, $condition, $type);
        return $this;
    }

    public function where($condition, $value = null)
    {
        $this->where[] = [$condition, $value];
        $this->ci->db->where($condition, $value);
        return $this;
    }

    public function unsetColumn($column)
    {
        $this->unset_columns[] = $column;
        return $this;
    }

    public function editColumn($column, $callback)
    {
        $this->edit_columns[$column] = $callback;
        return $this;
    }

    public function showSQL()
    {
        $this->ci->db->save_queries = true;
        return $this;
    }

    public function generate()
    {
        $this->applyFiltering();
        $this->applyOrdering();
        $this->applyPaging();

        $query = $this->ci->db->get();
        $data = $query->result_array();

        foreach ($data as &$row) {
            foreach ($this->edit_columns as $column => $callback) {
                if (isset($row[$column])) {
                    $row[$column] = call_user_func($callback, $row[$column], $row);
                }
            }
        }

        return json_encode([
            'data' => $data,
            'recordsTotal' => $this->getTotalRecords(),
            'recordsFiltered' => $this->getTotalRecords(true),
            'sql' => $this->ci->db->last_query(),
        ]);
    }

    protected function applyPaging()
    {
        $start = $this->ci->input->post('start');
        $length = $this->ci->input->post('length');
        $this->ci->db->limit($length, $start);
    }

    protected function applyOrdering()
    {
        $order = $this->ci->input->post('order');
        $columns = $this->ci->input->post('columns');

        if ($order) {
            foreach ($order as $o) {
                $column = $columns[$o['column']]['data'];
                $dir = $o['dir'];
                if (in_array($column, $this->columns)) {
                    $this->ci->db->order_by($column, $dir);
                }
            }
        }
    }

    protected function applyFiltering()
    {
        $search = $this->ci->input->post('search')['value'];
        if ($search) {
            $this->ci->db->group_start();
            foreach ($this->columns as $column) {
                $this->ci->db->or_like($column, $search);
            }
            $this->ci->db->group_end();
        }
    }

    protected function getTotalRecords($filtered = false)
    {
        if ($filtered) {
            $this->applyFiltering();
        }
        foreach ($this->joins as $join) {
            $this->ci->db->join($join[0], $join[1], $join[2]);
        }
        foreach ($this->where as $where) {
            $this->ci->db->where($where[0], $where[1]);
        }
        return $this->ci->db->count_all_results($this->table);
    }
}

/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */
