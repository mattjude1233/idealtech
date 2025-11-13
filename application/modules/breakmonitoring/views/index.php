<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">Break Monitoring</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Violation Alerts -->
<?php if (!empty($list)) {
    $critical_violations = [];
    $warnings = [];

    foreach ($list as $row) {
        if (!empty($row['break_start']) && !empty($row['break_end'])) {
            $start = new DateTime($row['break_start']);
            $end = new DateTime($row['break_end']);
            $interval = $start->diff($end);
            $total_minutes = ($interval->h * 60) + $interval->i;

            $employee_name = $row['emp_fname'] . ' ' . $row['emp_lname'];
            $date = date('M d, Y', strtotime($row['date']));

            if ($row['break_type'] == 'break' && $total_minutes > 30) {
                $critical_violations[] = ["employee" => $employee_name, "details" => "Extended break of {$total_minutes} minutes on {$date}", "level" => "Critical"];
            } elseif ($row['break_type'] == 'lunch' && $total_minutes > 90) {
                $critical_violations[] = ["employee" => $employee_name, "details" => "Extended lunch of {$total_minutes} minutes on {$date}", "level" => "Critical"];
            } elseif ($row['break_type'] == 'break' && $total_minutes > 15) {
                $warnings[] = ["employee" => $employee_name, "details" => "Exceeded break time by " . ($total_minutes - 15) . " minutes on {$date}", "level" => "Warning"];
            } elseif ($row['break_type'] == 'lunch' && $total_minutes > 60) {
                $warnings[] = ["employee" => $employee_name, "details" => "Exceeded lunch time by " . ($total_minutes - 60) . " minutes on {$date}", "level" => "Warning"];
            }
        }
    }
}
?>
<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">



        <!-- Compact Violation Notifications -->
        <?php if (!empty($critical_violations) || !empty($warnings)) : ?>
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="d-flex align-items-center justify-content-between violation-notification-bar p-3 rounded">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-bell text-warning mr-2 fa-lg"></i>
                            <div>
                                <span class="font-weight-bold text-dark">Break Monitoring Alerts</span>
                                <br><small class="text-muted">
                                    <?php if (!empty($critical_violations)) : ?>
                                        <?= count($critical_violations) ?> critical violation(s)
                                    <?php endif; ?>
                                    <?php if (!empty($critical_violations) && !empty($warnings)) : ?>
                                        and
                                    <?php endif; ?>
                                    <?php if (!empty($warnings)) : ?>
                                        <?= count($warnings) ?> warning(s) detected
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <div class="btn-group btn-group-xs">
                            <?php if (!empty($critical_violations)) : ?>
                                <button type="button" class="btn btn-xs m-l-5 btn-danger btn-pulse" data-toggle="modal" data-target="#criticalViolationsModal" title="View Critical Violations">
                                    <i class="fa fa-exclamation-triangle"></i> Critical (<?= count($critical_violations) ?>)
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($warnings)) : ?>
                                <button type="button" class="btn btn-xs m-l-5 btn-warning" data-toggle="modal" data-target="#warningViolationsModal" title="View Warning Violations">
                                    <i class="fa fa-exclamation-circle"></i> Warnings (<?= count($warnings) ?>)
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-xs m-l-5 btn-outline-secondary" onclick="dismissAllAlerts()" title="Dismiss notifications">
                                <i class="fa fa-times"></i> Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Violations Modal -->
            <?php if (!empty($critical_violations)) : ?>
                <div class="modal fade violation-modal" id="criticalViolationsModal" tabindex="-1" role="dialog" aria-labelledby="criticalViolationsModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="criticalViolationsModalLabel">
                                    <i class="fa fa-exclamation-triangle"></i> Critical Break Violations Detected
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped modal-violation-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="25%">Employee</th>
                                                <th width="70%">Violation Details</th>
                                                <th>Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($critical_violations as $index => $violation) :

                                                $badge_class = '';
                                                if ($violation['level'] == 'Warning') {
                                                    $badge_class = 'warning';
                                                } elseif ($violation['level'] == 'Critical') {
                                                    $badge_class = 'danger';
                                                }

                                            ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><strong><?= $violation['employee'] ?></strong></td>
                                                    <td><strong><?= $violation['details'] ?></strong></td>
                                                    <td><span class="badge badge-<?= $badge_class ?>"><?= $violation['level'] ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Close
                                </button>
                                <button type="button" class="btn btn-danger" onclick="exportViolations('critical')">
                                    <i class="fa fa-download"></i> Export Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Warning Violations Modal -->
            <?php if (!empty($warnings)) : ?>
                <div class="modal fade violation-modal" id="warningViolationsModal" tabindex="-1" role="dialog" aria-labelledby="warningViolationsModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title" id="warningViolationsModalLabel">
                                    <i class="fa fa-exclamation-circle"></i> Break Time Violations - Warning Level
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped modal-violation-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="25%">Employee</th>
                                                <th width="70%">Violation Details</th>
                                                <th>Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $display_warnings = array_slice($warnings, 0, 10); // Show first 10
                                            foreach ($display_warnings as $index => $warning) : ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><strong><?= $warning['employee'] ?></strong></td>
                                                    <td><?= $warning['details'] ?></td>
                                                    <td><span class="badge badge-warning">Warning</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (count($warnings) > 10) : ?>
                                                <tr class="table-info">
                                                    <td colspan="3" class="text-center py-3">
                                                        <i class="fa fa-info-circle text-info mr-2"></i>
                                                        <strong>... and <?= count($warnings) - 10 ?> more warning(s)</strong>
                                                        <br><small class="text-muted">Use table filters to view specific violations</small>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Close
                                </button>
                                <button type="button" class="btn btn-warning" onclick="exportViolations('warnings')">
                                    <i class="fa fa-download"></i> Export Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-filter"></i> Filters
                        </h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-sm m-l-5 btn-outline-secondary float-right" id="resetFilters">
                                <i class="fa fa-refresh"></i> Reset All
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-secondary float-right" id="quickToday">
                                <i class="fa fa-calendar-day"></i> <span class="d-none d-md-inline">Today</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row">
                                <!-- Date From -->
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_from">
                                            <i class="fa fa-calendar"></i> Date From
                                        </label>
                                        <input type="date"
                                            class="form-control"
                                            id="date_from"
                                            name="date_from"
                                            value="<?= $selected_date_from ?>"
                                            max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>

                                <!-- Date To -->
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_to">
                                            <i class="fa fa-calendar"></i> Date To
                                        </label>
                                        <input type="date"
                                            class="form-control"
                                            id="date_to"
                                            name="date_to"
                                            value="<?= $selected_date_to ?>"
                                            max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>

                                <!-- Employee Filter (if has permission) -->
                                <?php if (check_function('manage_attendance')) : ?>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="employee_id">
                                                <i class="fa fa-user"></i> Employee
                                            </label>
                                            <select class="form-control select2" id="employee_id" name="employee_id">
                                                <option value="">All Employees</option>
                                                <?php if (!empty($employees)) : ?>
                                                    <?php foreach ($employees as $employee) : ?>
                                                        <option value="<?= $employee['id'] ?>" <?= ($selected_employee == $employee['id']) ? 'selected' : '' ?>>
                                                            <?= $employee['emp_id'] . ' - ' . $employee['emp_fname'] . ' ' . $employee['emp_lname'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="col-lg-1 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="btn-group-vertical d-block">
                                            <button type="submit" class="btn btn-primary btn-block mb-1" id="applyFilters">
                                                <i class="fa fa-search"></i> <span class="d-none d-md-inline">Filter</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Date Filters -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="btn-group btn-group-xs" role="group" aria-label="Quick date filters">
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-days="0">Today</button>
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-days="1">Yesterday</button>
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-days="7">Last 7 Days</button>
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-days="30">Last 30 Days</button>
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-type="week">This Week</button>
                                        <button type="button" class="btn btn-xs m-r-5 btn-outline-info quick-date" data-type="month">This Month</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <?php if (!empty($list)) :
            $total_records = count($list);
            $overbreak_count = 0;
            $overlunch_count = 0;
            $normal_count = 0;
            $ongoing_count = 0;

            foreach ($list as $row) {
                if (!empty($row['break_start']) && !empty($row['break_end'])) {
                    $start = new DateTime($row['break_start']);
                    $end = new DateTime($row['break_end']);
                    $interval = $start->diff($end);
                    $total_minutes = ($interval->h * 60) + $interval->i;

                    if ($row['break_type'] == 'break' && $total_minutes > 15) {
                        $overbreak_count++;
                    } elseif ($row['break_type'] == 'lunch' && $total_minutes > 60) {
                        $overlunch_count++;
                    } else {
                        $normal_count++;
                    }
                } elseif (!empty($row['break_start']) && empty($row['break_end'])) {
                    $ongoing_count++;
                }
            }

            $violation_rate = $total_records > 0 ? round((($overbreak_count + $overlunch_count) / $total_records) * 100, 1) : 0;
        ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="violation-summary">
                        <h5 class="mb-3"><i class="fa fa-chart-bar text-primary"></i> Break Monitoring Summary</h5>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="stat-card danger">
                                    <h4 class="text-danger mb-1"><?= $overbreak_count ?></h4>
                                    <small class="text-muted">Overbreak</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card danger">
                                    <h4 class="text-danger mb-1"><?= $overlunch_count ?></h4>
                                    <small class="text-muted">Over Lunch</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card success">
                                    <h4 class="text-success mb-1"><?= $normal_count ?></h4>
                                    <small class="text-muted">Normal</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card warning">
                                    <h4 class="text-info mb-1"><?= $ongoing_count ?></h4>
                                    <small class="text-muted">Ongoing</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card">
                                    <h4 class="text-primary mb-1"><?= $total_records ?></h4>
                                    <small class="text-muted">Total Records</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card <?= $violation_rate > 20 ? 'danger' : ($violation_rate > 10 ? 'warning' : 'success') ?>">
                                    <h4 class="text-<?= $violation_rate > 20 ? 'danger' : ($violation_rate > 10 ? 'warning' : 'success') ?> mb-1"><?= $violation_rate ?>%</h4>
                                    <small class="text-muted">Violation Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">

                <div class="card break-monitoring-card">
                    <div class="card-body">

                        <table class="table table-hover" id="breakMonitoringTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($list)) : ?>
                                    <?php foreach ($list as $row) :
                                        $break_duration = '';
                                        $status_class = '';
                                        $status_text = '';
                                        $violation_type = '';

                                        if (!empty($row['break_start']) && !empty($row['break_end'])) {
                                            $start = new DateTime($row['break_start']);
                                            $end = new DateTime($row['break_end']);
                                            $interval = $start->diff($end);

                                            // Calculate total minutes
                                            $total_minutes = ($interval->h * 60) + $interval->i;

                                            $parts = [];
                                            if ($interval->h > 0) {
                                                $parts[] = sprintf('%02d hrs.', $interval->h);
                                            }
                                            if ($interval->i > 0 || empty($parts)) {
                                                $parts[] = sprintf('%02d min.', $interval->i);
                                            }

                                            $break_duration = implode(' ', $parts);

                                            // Determine break violation based on type and duration
                                            if ($row['break_type'] == 'break') {
                                                // Regular breaks should be 15 minutes or less
                                                if ($total_minutes > 15) {
                                                    $status_class = 'danger';
                                                    $status_text = 'Overbreak';
                                                    $violation_type = 'overbreak';
                                                    $excess_minutes = $total_minutes - 15;

                                                    if ($excess_minutes >= 60) {
                                                        $excess_hours = floor($excess_minutes / 60);
                                                        $excess_mins = $excess_minutes % 60;
                                                        $excess_text = $excess_hours . 'h ' . $excess_mins . 'm';
                                                    } else {
                                                        $excess_text = $excess_minutes . 'm';
                                                    }

                                                    $break_duration = "<span class='text-{$status_class} font-weight-bold'>{$break_duration}</span>";
                                                } elseif ($total_minutes > 10) {
                                                    $status_class = 'warning';
                                                    $status_text = 'Extended Break';
                                                    $break_duration = "<span class='text-{$status_class}'>{$break_duration}</span>";
                                                } else {
                                                    $status_class = 'success';
                                                    $status_text = 'Normal';
                                                }
                                            } elseif ($row['break_type'] == 'lunch') {
                                                // Lunch breaks should be 60 minutes or less
                                                if ($total_minutes > 60) {
                                                    $status_class = 'danger';
                                                    $status_text = 'Over Lunch';
                                                    $violation_type = 'overlunch';
                                                    $excess_minutes = $total_minutes - 60;

                                                    if ($excess_minutes >= 60) {
                                                        $excess_hours = floor($excess_minutes / 60);
                                                        $excess_mins = $excess_minutes % 60;
                                                        $excess_text = $excess_hours . 'h ' . $excess_mins . 'm';
                                                    } else {
                                                        $excess_text = $excess_minutes . 'm';
                                                    }

                                                    $break_duration = "<span class='text-{$status_class} font-weight-bold'>{$break_duration}</span>";
                                                } elseif ($total_minutes > 45) {
                                                    $status_class = 'warning';
                                                    $status_text = 'Extended Lunch';
                                                    $break_duration = "<span class='text-{$status_class}'>{$break_duration}</span>";
                                                } else {
                                                    $status_class = 'success';
                                                    $status_text = 'Normal';
                                                }
                                            }
                                        }

                                        // Handle cases where break is not ended
                                        if (!empty($row['break_start']) && empty($row['break_end'])) {
                                            $break_duration = '<span class="text-info">Ongoing</span>';
                                            $status_class = 'info';
                                            $status_text = 'Ongoing';
                                        }

                                        // Fallback for empty duration
                                        if (empty($break_duration)) {
                                            $break_duration = '<span class="text-muted">N/A</span>';
                                            $status_class = 'secondary';
                                            $status_text = 'N/A';
                                        }

                                        // Handle total break calculation for multiple breaks in a day
                                        $total_break = !empty($row['total_break']) ? $row['total_break'] : 0;
                                        $daily_violation = '';

                                        if ($total_break && strtotime($total_break) > strtotime('00:15:00') && $row['break_type'] == 'break') {
                                            $daily_violation = ' (Total Daily: ';

                                            $total_break_parts = explode(':', $total_break);
                                            if (count($total_break_parts) == 3) {
                                                $hours = (int)$total_break_parts[0];
                                                $mins = (int)$total_break_parts[1];
                                                $parts = [];
                                                if ($hours > 0) {
                                                    $parts[] = sprintf('%dh', $hours);
                                                }
                                                if ($mins > 0) {
                                                    $parts[] = sprintf('%dm', $mins);
                                                }
                                                $daily_violation .= implode(' ', $parts) . ')';
                                            }

                                            if ($status_class !== 'danger') {
                                                $status_class = 'warning';
                                                $status_text = 'Daily Overbreak';
                                            }
                                        }

                                    ?>
                                        <tr class="<?= $status_class && !in_array($status_class, ['success']) ? 'table-' . $status_class : '' ?>">
                                            <td>
                                                <strong><?= $row['emp_fname'] . ' ' . $row['emp_lname'] ?></strong>
                                                <br><small class="text-muted"><?= $row['emp_id'] ?></small>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
                                            <td><?= !empty($row['break_start']) ? date('h:i A', strtotime($row['break_start'])) : '<span class="text-muted">--</span>' ?></td>
                                            <td><?= !empty($row['break_end']) ? date('h:i A', strtotime($row['break_end'])) : '<span class="text-muted">--</span>' ?></td>
                                            <td>
                                                <?= $break_duration ?>
                                                <?php if (isset($excess_text)) : ?>
                                                    <br><small class="text-muted">Excess: <?= $excess_text ?></small>
                                                <?php endif; ?>
                                                <?= $daily_violation ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $row['break_type'] == 'lunch' ? 'primary' : 'secondary' ?>"
                                                    data-toggle="tooltip"
                                                    title="<?= $row['break_type'] == 'lunch' ? 'Lunch break (Standard: 60 min)' : 'Regular break (Standard: 15 min)' ?>">
                                                    <?= addOrdinalSuffix($row['break_count']) . " " . ucfirst($row['break_type']) ?>
                                                </span>
                                                <?php if (!empty($row['notes'])) : ?>
                                                    <br><small class="text-muted" data-toggle="tooltip" title="<?= htmlspecialchars($row['notes']) ?>">
                                                        <i class="fa fa-sticky-note"></i> Note
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = 'secondary';
                                                switch ($status_class) {
                                                    case 'danger':
                                                        $badge_class = 'danger';
                                                        break;
                                                    case 'warning':
                                                        $badge_class = 'warning';
                                                        break;
                                                    case 'success':
                                                        $badge_class = 'success';
                                                        break;
                                                    case 'info':
                                                        $badge_class = 'info';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $badge_class ?>"
                                                    data-toggle="tooltip"
                                                    title="<?php
                                                            echo $status_text;
                                                            if ($violation_type && isset($excess_text)) {
                                                                echo ' - Exceeded by ' . $excess_text;
                                                            }
                                                            if ($row['break_type'] == 'break') {
                                                                echo ' (Limit: 15 min)';
                                                            } elseif ($row['break_type'] == 'lunch') {
                                                                echo ' (Limit: 60 min)';
                                                            }
                                                            ?>">
                                                    <?= $status_text ?>
                                                </span>
                                                <?php if ($violation_type && isset($excess_text)) : ?>
                                                    <br><small class="text-<?= $status_class ?> font-weight-bold">+<?= $excess_text ?> over limit</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php
                                        // Reset excess_text for next iteration
                                        unset($excess_text);
                                    endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa fa-info-circle fa-2x mb-2"></i>
                                            <br>No break records found for the selected criteria
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<style>
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .table-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }

    .table-info {
        background-color: rgba(23, 162, 184, 0.1) !important;
    }

    .break-monitoring-card {
        border-left: 4px solid #007bff;
    }

    .violation-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        border-left: 4px solid;
    }

    .stat-card.danger {
        border-left-color: #dc3545;
    }

    .stat-card.warning {
        border-left-color: #ffc107;
    }

    .stat-card.success {
        border-left-color: #28a745;
    }

    .badge-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .break-type-lunch .badge {
        background: linear-gradient(45deg, #007bff, #0056b3) !important;
    }

    .break-type-break .badge {
        background: linear-gradient(45deg, #6c757d, #495057) !important;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 10px;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group {
            flex-direction: column;
            width: 100%;
        }

        .btn-group .btn {
            margin-bottom: 5px;
            border-radius: 4px !important;
        }

        .card-body {
            padding: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .violation-summary .row {
            text-align: center;
        }

        .stat-card {
            margin: 0 auto 15px;
            max-width: 150px;
        }

        .filter-btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }

    .pulse-animation {
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, .075);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    /* Enhanced tooltips */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        background-color: #333;
        border-radius: 6px;
    }

    /* Compact violation notification bar */
    .violation-notification-bar {
        border-left: 4px solid #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .violation-notification-bar:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    .modal-violation-table {
        border-radius: 8px;
        overflow: hidden;
    }

    .modal-violation-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .violation-modal .modal-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .violation-modal .modal-body {
        padding: 1.5rem;
    }

    .btn-pulse {
        animation: pulse-btn 2s infinite;
    }

    @keyframes pulse-btn {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Loading spinner for table */
    .table-loading {
        position: relative;
    }

    .table-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 32px;
        height: 32px;
        margin: -16px 0 0 -16px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize filter functionality
        initializeFilters();

        $('#quickToday').on('click', function() {
            var today = new Date().toISOString().split('T')[0];
            $('#date_from').val(today);
            $('#date_to').val(today);

            // Highlight Today button
            $('.quick-date').removeClass('btn-info').addClass('btn-outline-info');
            $(this).removeClass('btn-outline-info').addClass('btn-info');

            applyFilters();
        });

        // Form submission handler
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });

        // Quick date filter handlers
        $('.quick-date').on('click', function() {
            var days = $(this).data('days');
            var type = $(this).data('type');
            var dateFrom = '';
            var dateTo = '';

            if (type === 'week') {
                // This week
                var today = new Date();
                var firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                var lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                dateFrom = formatDate(firstDay);
                dateTo = formatDate(lastDay);
            } else if (type === 'month') {
                // This month
                var today = new Date();
                var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                dateFrom = formatDate(firstDay);
                dateTo = formatDate(lastDay);
            } else {
                // Days-based filters
                var today = new Date();
                if (days === 0) {
                    // Today
                    dateFrom = dateTo = formatDate(today);
                } else if (days === 1) {
                    // Yesterday
                    var yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    dateFrom = dateTo = formatDate(yesterday);
                } else {
                    // Last X days
                    var fromDate = new Date(today);
                    fromDate.setDate(fromDate.getDate() - days + 1);
                    dateFrom = formatDate(fromDate);
                    dateTo = formatDate(today);
                }
            }

            $('#date_from').val(dateFrom);
            $('#date_to').val(dateTo);

            // Highlight selected button
            $('.quick-date').removeClass('btn-info').addClass('btn-outline-info');
            $(this).removeClass('btn-outline-info').addClass('btn-info');

            // Auto-apply filter
            applyFilters();
        });

        // Reset filters
        $('#resetFilters, #clearAllFilters').on('click', function() {
            resetAllFilters();
        });

        // Real-time filter change handlers
        $('#employee_id').on('change', function() {
            updateFilterStatus();
        });

        $('#date_from, #date_to').on('change', function() {
            validateDateRange();
            updateFilterStatus();
        });

        function initializeFilters() {
            // Initialize Select2 for employee dropdown
            <?php if (check_function('manage_attendance')) : ?>
                $('#employee_id').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select an employee',
                    allowClear: true,
                    templateResult: function(data) {
                        if (data.loading) {
                            return data.text;
                        }
                        var $result = $('<span>' + data.text + '</span>');
                        return $result;
                    }
                });
            <?php endif; ?>

            var today = new Date().toISOString().split('T')[0];
            $('#date_from, #date_to').attr('max', today);

            // Update filter status on load
            updateFilterStatus();
        }

        function applyFilters() {
            // Validate date range first
            if (!validateDateRange()) {
                return false;
            }

            // Show loading state
            showLoadingState();

            // Get filter values
            var filters = getFilterValues();

            // Build URL with proper parameter order
            var url = '<?= base_url('breakmonitoring/index') ?>';
            var params = [];

            // Add parameters in the expected order: date_from, date_to, employee_id, break_type
            if (filters.date_from) params.push(encodeURIComponent(filters.date_from));
            if (filters.date_to) params.push(encodeURIComponent(filters.date_to));
            if (filters.employee_id) params.push(encodeURIComponent(filters.employee_id));
            if (filters.break_type) params.push(encodeURIComponent(filters.break_type));

            if (params.length > 0) {
                url += '/' + params.join('/');
            }

            // Navigate to filtered URL
            window.location.href = url;
        }

        function getFilterValues() {
            return {
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                employee_id: $('#employee_id').val(),
            };
        }

        function validateDateRange() {
            var dateFrom = $('#date_from').val();
            var dateTo = $('#date_to').val();

            if (dateFrom && dateTo && dateFrom > dateTo) {
                showAlert('error', 'Date From cannot be later than Date To');
                $('#date_from').addClass('is-invalid');
                $('#date_to').addClass('is-invalid');
                return false;
            }

            $('#date_from, #date_to').removeClass('is-invalid');
            return true;
        }

        function updateFilterStatus() {
            var filters = getFilterValues();
            var activeFilters = [];

            if (filters.date_from) activeFilters.push('From: ' + formatDisplayDate(filters.date_from));
            if (filters.date_to) activeFilters.push('To: ' + formatDisplayDate(filters.date_to));
            if (filters.employee_id) {
                var employeeName = $('#employee_id option:selected').text().split(' - ')[1] || 'Selected Employee';
                activeFilters.push('Employee: ' + employeeName);
            }
            if (filters.break_type) activeFilters.push('Type: ' + capitalizeFirst(filters.break_type));

            var $filterStatus = $('#filterStatus');
            var $activeFilters = $('#activeFilters');

            if (activeFilters.length > 0) {
                $activeFilters.html(activeFilters.map(filter =>
                    '<span class="badge badge-primary mr-1 mb-1">' + filter + '</span>'
                ).join(''));
                $filterStatus.show();
            } else {
                $filterStatus.hide();
            }
        }

        function resetAllFilters() {
            $('#filterForm')[0].reset();
            <?php if (check_function('manage_attendance')) : ?>
                $('#employee_id').val(null).trigger('change');
            <?php endif; ?>
            $('.quick-date').removeClass('btn-info').addClass('btn-outline-info');
            updateFilterStatus();

            // Navigate to clean URL
            window.location.href = '<?= base_url('breakmonitoring') ?>';
        }

        function showLoadingState() {
            $('#applyFilters').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function formatDisplayDate(dateString) {
            return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function showAlert(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                alert(message);
            }
        }

        // Initialize DataTable
        <?php if (!empty($list)) : ?>
            var table = $('#breakMonitoringTable').DataTable({
                ordering: true,
                searching: true,
                paging: true,
                pageLength: 25,
                info: true,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel"></i> Export Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Break Monitoring Report - <?= date('Y-m-d') ?>',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Break Monitoring Report',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    {
                        text: '<i class="fa fa-download"></i> Custom Export',
                        className: 'btn btn-info btn-sm',
                        action: function(e, dt, node, config) {
                            showExportModal();
                        }
                    }
                ],
                columnDefs: [{
                    targets: 6, // Status column
                    render: function(data, type, row) {
                        if (type === 'export') {
                            return $(data).text();
                        }
                        return data;
                    }
                }],
                order: [
                    [1, 'desc'],
                    [2, 'desc']
                ], // Sort by date, then by start time
                language: {
                    search: "Search records:",
                    lengthMenu: "Show _MENU_ records per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ break records",
                    emptyTable: "No break monitoring data available",
                    zeroRecords: "No matching records found"
                },
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Add custom filtering
            $('#breakMonitoringTable_wrapper').prepend(`
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0"><i class="fa fa-filter"></i> Quick Table Filters</h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary table-filter-btn active" data-filter="all">All Records</button>
                                            <button type="button" class="btn btn-outline-danger table-filter-btn" data-filter="overbreak">Overbreak</button>
                                            <button type="button" class="btn btn-outline-danger table-filter-btn" data-filter="overlunch">Over Lunch</button>
                                            <button type="button" class="btn btn-outline-warning table-filter-btn" data-filter="extended">Extended</button>
                                            <button type="button" class="btn btn-outline-success table-filter-btn" data-filter="normal">Normal</button>
                                            <button type="button" class="btn btn-outline-info table-filter-btn" data-filter="ongoing">Ongoing</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary table-filter-btn" data-filter="break">Breaks Only</button>
                                            <button type="button" class="btn btn-outline-primary table-filter-btn" data-filter="lunch">Lunch Only</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Handle table filter button clicks
            $(document).on('click', '.table-filter-btn', function() {
                var filterValue = $(this).data('filter');

                $('.table-filter-btn').removeClass('btn-primary btn-danger btn-warning btn-success btn-info btn-secondary')
                    .addClass(function() {
                        return $(this).hasClass('active') ? 'btn-outline-secondary' :
                            $(this).attr('class').match(/btn-outline-\w+/) ? $(this).attr('class').match(/btn-outline-\w+/)[0] : 'btn-outline-secondary';
                    });

                $('.table-filter-btn').removeClass('active');
                $(this).addClass('active');

                switch (filterValue) {
                    case 'all':
                        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                        table.search('').columns().search('').draw();
                        break;
                    case 'overbreak':
                        $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                        table.columns(6).search('Overbreak').draw();
                        break;
                    case 'overlunch':
                        $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                        table.columns(6).search('Over Lunch').draw();
                        break;
                    case 'extended':
                        $(this).removeClass('btn-outline-warning').addClass('btn-warning');
                        table.columns(6).search('Extended').draw();
                        break;
                    case 'normal':
                        $(this).removeClass('btn-outline-success').addClass('btn-success');
                        table.columns(6).search('Normal').draw();
                        break;
                    case 'ongoing':
                        $(this).removeClass('btn-outline-info').addClass('btn-info');
                        table.columns(6).search('Ongoing').draw();
                        break;
                    case 'break':
                        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                        table.columns(5).search('Break').draw();
                        break;
                    case 'lunch':
                        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                        table.columns(5).search('Lunch').draw();
                        break;
                }
            });
        <?php endif; ?>

        function showExportModal() {
            var filters = getFilterValues();
            var exportUrl = '<?= base_url('breakmonitoring/export') ?>';

            var modal = `
                <div class="modal fade" id="exportModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export Break Monitoring Data</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p>Current filters will be applied to the export:</p>
                                <ul class="list-unstyled">
                                    ${filters.date_from ? '<li><strong>Date From:</strong> ' + formatDisplayDate(filters.date_from) + '</li>' : ''}
                                    ${filters.date_to ? '<li><strong>Date To:</strong> ' + formatDisplayDate(filters.date_to) + '</li>' : ''}
                                    ${filters.employee_id ? '<li><strong>Employee:</strong> ' + $('#employee_id option:selected').text() + '</li>' : ''}
                                    ${filters.break_type ? '<li><strong>Type:</strong> ' + capitalizeFirst(filters.break_type) + '</li>' : ''}
                                </ul>
                                <p>Choose export format:</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" onclick="exportData('excel')">
                                    <i class="fa fa-file-excel"></i> Excel
                                </button>
                                <button type="button" class="btn btn-info" onclick="exportData('csv')">
                                    <i class="fa fa-file-csv"></i> CSV
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modal);
            $('#exportModal').modal('show');

            $('#exportModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }

        function exportData(format) {
            var filters = getFilterValues();
            var url = '<?= base_url('breakmonitoring/export') ?>/' + format;
            var params = [];

            if (filters.date_from) params.push(encodeURIComponent(filters.date_from));
            if (filters.date_to) params.push(encodeURIComponent(filters.date_to));
            if (filters.employee_id) params.push(encodeURIComponent(filters.employee_id));
            if (filters.break_type) params.push(encodeURIComponent(filters.break_type));

            if (params.length > 0) {
                url += '/' + params.join('/');
            }

            window.open(url, '_blank');
            $('#exportModal').modal('hide');
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip({
            delay: {
                show: 500,
                hide: 100
            },
            placement: 'auto'
        });

        // Add auto-refresh functionality for ongoing breaks
        <?php if (check_function('manage_attendance')) : ?>
            var refreshInterval = setInterval(function() {
                var ongoingBreaks = $('.badge:contains("Ongoing")').length;
                if (ongoingBreaks > 0) {
                    $('.badge:contains("Ongoing")').addClass('badge-pulse');

                    // Update page title with ongoing count
                    if (ongoingBreaks > 0) {
                        document.title = '(' + ongoingBreaks + ') Break Monitoring - Ongoing Breaks';
                    }
                } else {
                    document.title = 'Break Monitoring';
                }
            }, 30000); // Check every 30 seconds
        <?php endif; ?>

        // Notification for violations
        var violationCount = $('.text-danger').length;
        if (violationCount > 0) {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "positionClass": "toast-top-right",
                    "timeOut": "10000"
                };
                toastr.warning(violationCount + ' break time violation(s) detected. Please review.', 'Break Monitoring Alert');
            }

            // Add to browser notification if permission granted
            if ("Notification" in window && Notification.permission === "granted") {
                new Notification("Break Monitoring Alert", {
                    body: violationCount + " violation(s) detected",
                    icon: "<?= base_url('plugins/fontawesome-free/svgs/solid/exclamation-triangle.svg') ?>"
                });
            }
        }

        // Enhanced table interactions
        $('#breakMonitoringTable tbody tr').on('click', function() {
            $(this).addClass('table-info').siblings().removeClass('table-info');
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            if (e.ctrlKey) {
                switch (e.which) {
                    case 70: // Ctrl+F
                        e.preventDefault();
                        $('#breakMonitoringTable_filter input').focus();
                        break;
                    case 82: // Ctrl+R
                        e.preventDefault();
                        resetAllFilters();
                        break;
                }
            }
        });

        // Auto-save filter preferences
        function saveFilterPreferences() {
            var filters = getFilterValues();
            localStorage.setItem('breakMonitoringFilters', JSON.stringify(filters));
        }

        function loadFilterPreferences() {
            var saved = localStorage.getItem('breakMonitoringFilters');
            if (saved) {
                try {
                    var filters = JSON.parse(saved);
                    updateFilterStatus();
                } catch (e) {
                    console.log('Error loading filter preferences:', e);
                }
            }
        }

        // Load preferences on page load
        loadFilterPreferences();

        // Modal functions for violation handling
        window.dismissAllAlerts = function() {
            localStorage.setItem('violationsDismissed', new Date().getTime());
            $('.alert').fadeOut();
            $('.row:has(.bg-light)').fadeOut();
        };

        window.markAsAddressed = function(index) {
            var button = $('button[onclick="markAsAddressed(' + index + ')"]');
            button.removeClass('btn-outline-primary').addClass('btn-success')
                .html('<i class="fa fa-check"></i> Addressed');
            button.prop('disabled', true);

            if (typeof toastr !== 'undefined') {
                toastr.success('Violation marked as addressed');
            }
        };

        window.markAsNotified = function(index) {
            var button = $('button[onclick="markAsNotified(' + index + ')"]');
            button.removeClass('btn-outline-warning').addClass('btn-success')
                .html('<i class="fa fa-check"></i> Notified');
            button.prop('disabled', true);

            if (typeof toastr !== 'undefined') {
                toastr.info('Employee notified of violation');
            }
        };

        window.exportViolations = function(type) {
            var filename = 'Break_Violations_' + type + '_' + new Date().toISOString().split('T')[0] + '.txt';
            var content = '';

            if (type === 'critical') {
                content = 'CRITICAL BREAK VIOLATIONS REPORT\n';
                content += '================================\n';
                content += 'Generated: ' + new Date().toLocaleString() + '\n\n';

                $('#criticalViolationsModal tbody tr').each(function() {
                    var violation = $(this).find('td:eq(1)').text();
                    if (violation) {
                        content += '• ' + violation + '\n';
                    }
                });
            } else {
                content = 'BREAK TIME WARNINGS REPORT\n';
                content += '==========================\n';
                content += 'Generated: ' + new Date().toLocaleString() + '\n\n';

                $('#warningViolationsModal tbody tr').each(function() {
                    var violation = $(this).find('td:eq(1)').text();
                    if (violation && !$(this).hasClass('table-info')) {
                        content += '• ' + violation + '\n';
                    }
                });
            }

            // Create and download file
            var blob = new Blob([content], {
                type: 'text/plain'
            });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            if (typeof toastr !== 'undefined') {
                toastr.success('Violations report downloaded');
            }
        };

        // Auto-show modals if violations exist and haven't been dismissed recently
        var lastDismissed = localStorage.getItem('violationsDismissed');
        var timeSinceDismissed = lastDismissed ? (new Date().getTime() - parseInt(lastDismissed)) : 0;
        var oneHourInMs = 60 * 60 * 1000;

        <?php if (!empty($critical_violations)) : ?>
            if (!lastDismissed || timeSinceDismissed > oneHourInMs) {
                setTimeout(function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Critical break violations detected! Click the red button to view details.', 'Urgent Alert', {
                            timeOut: 10000,
                            onclick: function() {
                                $('#criticalViolationsModal').modal('show');
                            }
                        });
                    }
                }, 2000);
            }
        <?php endif; ?>
    });
</script>