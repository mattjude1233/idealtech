<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('cute_print')) {
    function cute_print($data)
    {
        echo '<div style="padding: 30px;">';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        echo '</div>';
    }
}

if (!function_exists('truncate')) {
    function truncate($string, $limit, $pad = '...')
    {
        if (strlen($string) <= $limit) {
            return $string;
        } else {
            return substr($string, 0, $limit) . $pad;
        }
    }
}

if (!function_exists('get_randomKeys')) {
    function get_randomKeys($p = 'K', $length = '10')
    {
        $pattern = "abcdefghijklmnopqrstuvwxyz0123456789";
        $key  = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern[mt_rand(0, strlen($pattern) - 1)];
        }

        return strtotime(date('Y-m-d H:i:s')) . $p . $key;
    }
}

if (!function_exists('showError')) {
    function showError($arr = array(), $type = 'ul')
    {
        $html = '';
        if (!empty($arr)) {
            $html .= '<ul class="alertbox__list">';
            foreach ($arr as $r) {
                $html .= "<li>" . strip_tags($r) . "</li>";
            }
            $html .= '</ul>';
        }

        if ($type != 'ul') {
            $html = '';
            if (!empty($arr)) {
                foreach ($arr as $r) {
                    $html .= "<{$type}>$r</{$type}>";
                }
            }
        }

        return $html;
    }
}


if (!function_exists('create_date_folder')) {
    function create_date_folder($path)
    {
        $year_folder  = $path . date('Y') . '/';
        $month_folder = $year_folder . date('m') . '/';
        $day_folder   = $month_folder . date('d') . '/';

        if (!file_exists($year_folder)) {
            mkdir($year_folder, 0777, true);
        }
        if (!file_exists($month_folder)) {
            mkdir($month_folder, 0777, true);
        }
        if (!file_exists($day_folder)) {
            mkdir($day_folder, 0777, true);
        }

        $folder['year']  = $year_folder;
        $folder['month'] = $month_folder;
        $folder['day']   = $day_folder;

        return $folder;
    }
}

if (!function_exists('clean_filepath_str')) {
    function clean_filepath_str($filepath)
    {
        return !empty($filepath) ? str_replace(array('./'), array('', '', ''), $filepath) : $filepath;
    }
}

if (!function_exists('dateDiff')) {
    function dateDiff($start, $end)
    {
        $date1  = date_create(date('Y-m-d', strtotime($start)));
        $date2  = date_create(date('Y-m-d', strtotime($end)));
        $diff   = date_diff($date1, $date2);
        $day = $diff->format("%a");

        return ($day > 1 ? $day . ' Day/s Later' : $day . ' Day After');
    }
}

if (!function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        // Remove any commas from the input
        $amount = str_replace(',', '', $amount);

        // Convert the amount to a float and format it to two decimal places
        $formattedAmount = number_format((float)$amount, 2, '.', '');

        return $formattedAmount;
    }
}


if (!function_exists('addOrdinalSuffix')) {
    function addOrdinalSuffix($number)
    {
        if (!is_numeric($number)) return $number;

        $n = $number % 100;

        if ($n >= 11 && $n <= 13) {
            return $number . 'th';
        }

        switch ($number % 10) {
            case 0:
                return '';
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
            default:
                return $number . 'th';
        }
    }
}
