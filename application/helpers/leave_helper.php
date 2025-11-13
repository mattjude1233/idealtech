<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// leave status button
if (!function_exists('leave_status_button')) {
    function leave_status_button($status = '')
    {
        $status = strtolower($status);
        $badge_classes = [
            'approved' => 'success',
            'pending'  => 'warning',
            'denied' => 'danger',
            'confirmed' => 'info',
        ];

        $badge_class = isset($badge_classes[$status]) ? $badge_classes[$status] : 'secondary';

        $status_text = admin__lang('leave', 'status', $status);

        return '<span class="badge badge-' . $badge_class . '">' . ($status_text && $status_text != 'unknown' ? $status_text : ucfirst($status)) . '</span>';
    }
}
