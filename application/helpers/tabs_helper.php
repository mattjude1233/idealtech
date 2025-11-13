<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('display__tabs')) {
    function display__tabs($type = 1)
    {
        $CI = &get_instance();
        $logindata = $CI->session->userdata('hridealtech_login');

        if (empty($logindata)) return [];

        $sql = "
            SELECT *
            FROM admin_tabs
            WHERE status = 1
              AND type = ?
              AND (
                    level = 'all'
                 OR FIND_IN_SET('all', level) > 0
                 OR FIND_IN_SET(?, level) > 0 
                 OR FIND_IN_SET(?, special_user) > 0
              )
              AND (
                    exclude_user IS NULL
                 OR exclude_user = ''
                 OR FIND_IN_SET(?, exclude_user) = 0
              )
            ORDER BY position ASC
        ";

        $params = [
            (int)$type,
            $logindata['emp_level'],
            $logindata['id'],
            $logindata['id'],
        ];

        return $CI->db->query($sql, $params)->result_array();
    }
}

if (!function_exists('tab__groups')) {
    function tab__groups($groupid = 0)
    {
        $tabs = display__tabs();

        return array_filter($tabs, function ($tab) use ($groupid) {
            return $tab['grouping'] &&  !in_array($tab['grouping'], ['-1']) == $groupid;
        });
    }
}

if (!function_exists('check_function')) {
    function check_function($keyword)
    {
        $array = display__tabs(2);

        foreach ($array as $item) {
            if (isset($item['keyword']) && $item['keyword'] === $keyword) {
                return true;
            }
        }
        return false;
    }
}
