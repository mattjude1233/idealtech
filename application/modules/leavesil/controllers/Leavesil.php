<?php

class Leavesil extends MY_Controller
{
    protected $_sil_per_month = 10; // 10 hours of SIL per month

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $employee_list = $this->model->getBySQL("SELECT e.id, e.emp_id, e.emp_fname, e.emp_mname, e.emp_lname, e.emp_level, e.hiring_date FROM employees AS e WHERE e.status != '3' ORDER BY FIELD(e.status, 1, 0) DESC, e.emp_lname ASC, e.emp_fname ASC");

        // Compute earned SIL for each employee

        if (!empty($employee_list)) {
            foreach ($employee_list as &$employee) {
                $earnedSIL = $this->computeEarnedSIL($employee['id']);
                $employee['earned_sil'] = $earnedSIL;

                // calculate SIL used
                $usedSIL = $this->silUsed($employee['id'], date('Y-m-d'));
                $employee['used_sil'] = $usedSIL;

                // Calculate remaining SIL
                $employee['remaining_sil'] = $this->remainingSIL($employee['id'], date('Y-m-d'));
            }
        }

        $data['employee'] = $employee_list;
        $data['page_title'] = "Service Incentive Leave";
        $data['content'] = 'leavesil/index';
        $this->display($data);
    }


    // computed earned SIL
    private function computeEarnedSIL($employeeId, $dateToday = null)
    {
        if (!$dateToday) {
            $dateToday = date('Y-m-d');
        }

        $employeeId = (int) $employeeId;

        // If employee is not active or has no hiring date, return 0
        if ($employeeId <= 0) {
            return 0;
        }

        // Get hiring date
        $result = $this->model->getBySQL("SELECT id, hiring_date FROM employees WHERE id = $employeeId");
        if (!$result || empty($result[0]['hiring_date'])) {
            return 0;
        }
        $hiringDate = new DateTime($result[0]['hiring_date']);
        $today =  new DateTime($dateToday);

        // Reset reference to Jan 1st of current year
        $janFirst = new DateTime($today->format('Y') . '-01-01');

        // If hired this year, use Jan 1st or hiring date whichever is later
        $startDate = ($hiringDate > $janFirst) ? clone $hiringDate : clone $janFirst;

        // Check if employee has at least 6 months of service
        $sixMonthsFromHire = (clone $hiringDate)->modify('+6 months');
        if ($today < $sixMonthsFromHire) {
            return 0;
        }

        // If 6-month mark is later than Jan 1st, use that as starting point
        if ($sixMonthsFromHire > $startDate) {
            $startDate = clone $sixMonthsFromHire;
        }

        // Calculate months of service since start date
        $months = (($today->format('Y') - $startDate->format('Y')) * 12)
            + ($today->format('n') - $startDate->format('n'));

        if ($today->format('d') >= $startDate->format('d')) {
            $months += 1; // Count current month if past start day
        }

        // Earned SIL: 10 hours per month
        return $months * $this->_sil_per_month;
    }

    private function silUsed($employeeId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $employeeId = (int) $employeeId;

        // Get first day of the year based on $date
        $yearStart = date('Y-01-01', strtotime($date));

        $rows = $this->model->getBySQL(" SELECT date_from, date_to, actual_date_from, actual_date_to, date_filed, sil FROM employee_leave WHERE employee_id = $employeeId AND status = 'confirmed' AND date_to >= '$yearStart' AND date_from <= '$date' ");

        $totalUsed = 0;

        if (!empty($rows)) {
            foreach ($rows as $r) {
                // 1) If SIL is explicitly recorded, trust it
                if ($r['sil'] !== null && $r['sil'] !== '' && is_numeric($r['sil'])) {
                    $totalUsed += (float)$r['sil'];
                    continue;
                }

                // 2) Determine start & end dates for counting
                $start = !empty($r['actual_date_from']) ? $r['actual_date_from'] : $r['date_from'];
                $end   = !empty($r['actual_date_to'])   ? $r['actual_date_to']   : $r['date_to'];

                // Clip the range to be within the current year and up to $date
                if ($start < $yearStart) $start = $yearStart;
                if ($end > $date) $end = $date;

                // Fallback if only filed date exists
                if (empty($start) || empty($end)) {
                    if (!empty($r['date_filed']) && $r['date_filed'] >= $yearStart && $r['date_filed'] <= $date) {
                        $totalUsed += 1;
                    }
                    continue;
                }

                // Count inclusive days
                if ($start <= $end) {
                    $d1 = new DateTime($start);
                    $d2 = new DateTime($end);
                    $days = $d1->diff($d2)->days + 1;
                    $totalUsed += $days;
                }
            }
        }

        return $totalUsed;
    }


    private function remainingSIL($employeeId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $earnedSIL = $this->computeEarnedSIL($employeeId);
        $usedSIL = $this->silUsed($employeeId, $date);

        return $earnedSIL - $usedSIL;
    }
}
