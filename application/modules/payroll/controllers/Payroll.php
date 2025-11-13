<?php

class Payroll extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($yearmonth = '', $period = '')
    {
        $yearmonth = ($yearmonth == '') ? date('Y-m') : $yearmonth;
        if (empty($period)) {
            $day = date('d', strtotime($yearmonth . '-01'));
            $period = ($day > 15) ? 1 : 2;
        }

        $employee =  $this->_logindata['id'];

        $data['payroll'] = $this->model->getBySQL("SELECT * FROM payroll WHERE employee_id = '$employee' AND payout_month = '$yearmonth' AND period = '$period' AND status = 1", "row");

        $data['page_title'] = "Payroll: " . admin__lang('payroll', 'period', $period) . " - " . date('F Y', strtotime($yearmonth));
        $data['yearmonth'] = $yearmonth;
        $data['period'] = $period;
        $data['content'] = 'payroll/index';
        $this->display($data);
    }
}
