<?php

class MY_Controller extends MX_Controller
{
    protected $_path;
    protected $_page_title;
    public $_name;
    public $_logindata;
    public $_allowed_tabs = array();
    public $_allowed_function = array();
    public $_breaktime = 15; // in minutes, default is 15 minutes
    public $_lunchtime = 60; // in minutes, default is 60 minutes
    public $_lunchstart = '12';
    public $_lunchperiod = 'am';

    function __construct()
    {
        parent::__construct();

        $this->load->model('MY_Model', 'model');
        $this->_path    = "./";

        if (!defined('IS_ADMIN')) {
            define('IS_ADMIN', TRUE);
        }

        // check if user is logged in
        if (empty($this->session->userdata('hridealtech_login'))) {
            $page__login = $this->uri->rsegments[1] == 'login' ? true : false;
            $is__login_page = in_array($this->uri->rsegments[2], array('logout')) ? true : false;

            if (!$page__login && !$is__login_page) {
                redirect(base_url('login'), 'refresh');
            }
        } else {
            $this->_logindata = $this->session->userdata('hridealtech_login');
            $this->_name = "{$this->_logindata['emp_lname']}, {$this->_logindata['emp_fname']}";
            $this->_allowed_tabs = display__tabs(1);
            $this->_allowed_function = display__tabs(2);

            // get current page url
            $this->_path = $current_url = !empty($this->uri->uri_string) ? $this->uri->uri_string : 'home';
            $is_allowed = false;

            if (!empty($this->_allowed_tabs)) {
                foreach ($this->_allowed_tabs as $rule) {
                    $rule = $rule['link'];

                    // If rule matches exactly
                    if ($current_url === $rule) {
                        $is_allowed = true;
                        break;
                    }

                    // Allow all subpaths (e.g. attendance/records/..., employee/...)
                    if (strpos($current_url, $rule . '/') === 0) {
                        $is_allowed = true;
                        break;
                    }
                }
            }

            if (
                strpos($current_url, 'timeclock') === 0 ||
                strpos($current_url, 'login') === 0 ||
                strpos($current_url, 'employee/profile') === 0
            ) {
                $is_allowed = true;
            }

            if (!$is_allowed && !IS_AJAX) {
                show_404(); // or redirect, or exit
            }

            $this->_page_title = $this->router->fetch_module();

            if ($this->uri->uri_string == 'login') {
                redirect(base_url(), 'refresh');
            }

            // $this->AutoAbsentRecord();
        }
    }

    function display($data = NULL)
    {

        // * Initialize default CSS and JS links
        $links_css = array(
            'googleFonts' => 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
            'fontawesome' => 'plugins/fontawesome-free/css/all.min.css',

            // select2
            'select2' => 'plugins/select2/css/select2.min.css',
            'select2-bootstrap4' => 'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',

            // datatable
            'datatables-bs4' => "plugins/datatables-bs4/css/dataTables.bootstrap4.min.css",
            'datatables-responsive' => "plugins/datatables/fixedcolumns/fixedColumns.dataTables.min.css",

            // daterange picker
            'daterangepicker__daterangepicker' => 'plugins/daterangepicker/daterangepicker.css',
            'datepicker__datepicker3' => 'plugins/datepicker/datepicker3.css',
            'datetimepicker' => 'plugins/datetimepicker/jquery.datetimepicker.min.css',
            'bootstrap-clockpicker' => 'plugins/bootstrap-clockpicker/bootstrap-clockpicker.min.css',

            // jquery-confirm
            'jquery-confirm' => 'plugins/jquery-confirm/jquery-confirm.min.css',

            // jQueryButtonLoader
            'jQueryButtonLoader' => 'plugins/jQueryButtonLoader/css/buttonLoader.css',

            'adminlte' => 'dist/css/adminlte.min.css',
            'timeclock' => 'dist/css/timeclock.css?v=' . time(),
            'custom' => 'dist/css/custom.css?v=' . time(),
        );

        $links_js = array(
            'jquery' => 'plugins/jquery/jquery.min.js',
            'bootstrap' => 'plugins/bootstrap/js/bootstrap.bundle.min.js',
            'select2' => 'plugins/select2/js/select2.full.min.js',

            // datatable
            'datatables' => "plugins/datatables/jquery.dataTables.min.js",
            'datatables-bs4' => "plugins/datatables-bs4/js/dataTables.bootstrap4.min.js",
            'datatables-responsive' => "plugins/datatables/fixedcolumns/dataTables.fixedColumns.min.js",

            // daterange picker
            'moment' => 'plugins/moment/moment.min.js',
            'datepicker__bootstrap-datepicker' => 'plugins/datepicker/bootstrap-datepicker.js',
            'daterangepicker__daterangepicker' => 'plugins/daterangepicker/daterangepicker.js',
            'datetimepicker' => 'plugins/datetimepicker/jquery.datetimepicker.full.js',

            'bootstrap-clockpicker' => 'plugins/bootstrap-clockpicker/bootstrap-clockpicker.min.js',

            'inputmask' => 'plugins/input-mask/jquery.inputmask.js',
            'inputmask__date-extensions' => 'plugins/input-mask/jquery.inputmask.date.extensions.js',
            'inputmask__extensions' => 'plugins/input-mask/jquery.inputmask.extensions.js',

            // jquery-confirm
            'jquery-confirm' => 'plugins/jquery-confirm/jquery-confirm.min.js',

            // jQueryButtonLoader
            'jQueryButtonLoader' => 'plugins/jQueryButtonLoader/js/jquery.buttonLoader.min.js',

            // ckeditor
            'ckeditor' => 'plugins/ckeditor/ckeditor.js?v=' . time(),
            'ckeditor__adapters' => 'plugins/ckeditor/adapters/jquery.js?v=' . time(),

            // chartJS
            'chartjs' => 'plugins/chart.js/Chart.min.js',
            'chartjs_datalabels' => 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0',


            'adminlte' => 'dist/js/adminlte.min.js',
            'timeclock' => 'dist/js/timeclock.js?v=' . time(),
            'global' => 'dist/js/global.js?v=' . time(),
        );


        // Merge additional CSS and JS links if provided
        if (!empty($data['links__css'])) {
            $this->_linksMerge($links_css, $data['links__css']);
        }

        if (!empty($data['links__js'])) {
            $this->_linksMerge($links_js, $data['links__js']);
        }

        // set final links to data
        $data['header__links'] = array('css' => $links_css, 'js' => $links_js);

        $data['show_page_title'] = !empty($data['show_page_title']) ? $data['show_page_title'] : true;

        $this->load->view('template/template_head', @$data);
        $this->load->view('template/template_nav', @$data);
        $this->load->view('template/template_sidebar', @$data);
        $this->load->view('template/template_main', @$data);
        $this->load->view('template/template_footer', @$data);
    }


    // * Merge additional CSS and JS links if provided
    private function _linksMerge(&$defaultLinks, $additionalLinks)
    {
        foreach ($additionalLinks as $key => $value) {
            if (!empty($value)) {
                $defaultLinks[$key] = $value;
            } else {
                unset($defaultLinks[$key]);
            }
        }
    }



    /****** private methods ****/
    /**
	@formdata: data Array
	@notrequiredfield: array key not required
	@customrules: array key with custom form validation
	@customkey: array key with custom key name
     **/
    public function validatefields($formdata, $notrequiredfield = array(), $customrules = array(), $customkey  = array())
    {
        $fielderror = array();
        foreach ($formdata as $key => $val) {
            if (!empty($notrequiredfield) && in_array($key, $notrequiredfield))
                continue;

            $key2 = str_replace('_', ' ', $key);
            if (!empty($customkey) && array_key_exists($key, $customkey)) {
                $key2 = $customkey[$key];
            }
            $key2 = ucwords($key2);

            if (!empty($customrules) && array_key_exists($key, $customrules)) {
                $this->form_validation->set_rules($key, $key2, $customrules[$key]);
            } else {
                $this->form_validation->set_rules($key, $key2, 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            foreach ($formdata as $key => $value) {
                if (in_array($key, $notrequiredfield))
                    continue;

                if (form_error($key))
                    $fielderror[$key] = form_error($key);
            }
        }

        return $fielderror;
    }

    // Auto generate absent record for employees with no attendance record for the date
    private function AutoAbsentRecord($date = '')
    {
        $timeclock = Modules::load('timeclock/Timeclock');

        // default date yesterday
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d', strtotime('-1 day'));
        if (strtotime($date) >= strtotime(date('Y-m-d'))) show_404();

        // check if date is sunday or saturday
        $day_of_week = date('N', strtotime($date));

        // get all employees with no attendance record for the date
        $employees = $this->model->getBySQL("SELECT id, emp_id, emp_fname, emp_mname, emp_lname, rest_day FROM employees WHERE rest_day NOT LIKE '%$day_of_week%' AND id NOT IN (SELECT employee_id FROM attendance WHERE date = '$date')");

        if (!empty($employees)) {
            foreach ($employees as $emp) {
                $schedule = $timeclock->emp_schedule($emp['id'], "$date 22:00:00");
                $yesterday_schedule_end = $schedule ? $schedule['yesterday_schedule_end'] : "$date  12:00:00";

                // check if current time is greater than schedule end time
                if (strtotime(date('Y-m-d H:i:s')) >= strtotime($yesterday_schedule_end)) {
                    // insert absent record
                    $to_insert = array(
                        'employee_id' => $emp['id'],
                        'date' => $date,
                        'shift_start' => $schedule ? date('H:i:s', strtotime($schedule['schedule_start'])) : '22:00:00',
                        'shift_end' => $schedule ? date('H:i:s', strtotime($schedule['schedule_end'])) : '07:00:00',
                        'punch_in' => null,
                        'punch_out' => null,
                        'late' => '00:00:00',
                        'absent' => 'TRUE',
                        'type' => 'absent',
                        'notes' => 'Auto generated absent record',
                    );

                    $this->model->insert('attendance', $to_insert);
                }
            }
        }
    }
}
