<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </div>


            <div class="col-sm-6">
                <div class="float-sm-right">

                    <!-- summary button -->
                    <button type="button" class="btn btn-primary btn-md btn-pill" id="attendance_summary-btn">
                        <i class="fa fa-chart-pie"></i> Summary
                    </button>

                    <!-- fullscreen toggle button -->
                    <button type="button" class="btn btn-secondary btn-md btn-pill ml-2" id="toggle_fullscreen-table-btn">
                        <i class="fa fa-expand"></i> Fullscreen Table
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-15 p-b-0">
                        <div class="row">

                            <!-- Present Today -->
                            <?php

                            $presentToday = countPresentPerDate(date('Y-m-d', strtotime('-1 day')));
                            $presentYesterday = countPresentPerDate(date('Y-m-d', strtotime('-2 day')));
                            $todayCount     = is_array($presentToday) ? count($presentToday) : (int)($presentToday ?? 0);
                            $yesterdayCount = is_array($presentYesterday) ? count($presentYesterday) : (int)($presentYesterday ?? 0);

                            $diff    = $todayCount - $yesterdayCount;
                            $absDiff = abs($diff);

                            if ($diff > 0) {
                                $footerClass = 'text-success';
                                $footerIcon  = 'fa-arrow-circle-up';
                                $footerText  = $yesterdayCount === 0
                                    ? 'Up from 0 yesterday'
                                    : "{$absDiff} more than yesterday";
                            } elseif ($diff < 0) {
                                $footerClass = 'text-red';
                                $footerIcon  = 'fa-arrow-circle-down';
                                $footerText  = "{$absDiff} fewer than yesterday";
                            } else {
                                $footerClass = 'text-muted';
                                $footerIcon  = 'fa-minus-circle';
                                $footerText  = 'Same as yesterday';
                            }
                            ?>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= $todayCount ?></h3>
                                        <p>Present Today</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="small-box-footer bg-white">
                                        <i class="fas <?= $footerClass ?> <?= $footerIcon ?>"></i> <?= $footerText ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Absent Today -->
                            <?php
                            $absentToday     = countAbsentPerDate(date('Y-m-d', strtotime('-1 day')));
                            $absentYesterday = countAbsentPerDate(date('Y-m-d', strtotime('-2 day')));

                            $todayAbsent     = is_array($absentToday) ? count($absentToday) : (int)($absentToday ?? 0);
                            $yesterdayAbsent = is_array($absentYesterday) ? count($absentYesterday) : (int)($absentYesterday ?? 0);

                            $diff    = $todayAbsent - $yesterdayAbsent;
                            $absDiff = abs($diff);

                            if ($diff > 0) {
                                $footerClass = 'text-danger';
                                $footerIcon  = 'fa-arrow-circle-up';
                                $footerText  = $yesterdayAbsent === 0
                                    ? 'Up from 0 yesterday'
                                    : "{$absDiff} more than yesterday";
                            } elseif ($diff < 0) {
                                $footerClass = 'text-success';
                                $footerIcon  = 'fa-arrow-circle-down';
                                $footerText  = "{$absDiff} fewer than yesterday";
                            } else {
                                $footerClass = 'text-muted';
                                $footerIcon  = 'fa-minus-circle';
                                $footerText  = 'Same as yesterday';
                            }
                            ?>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3><?= $todayAbsent ?></h3>
                                        <p>Absent Today</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="small-box-footer bg-white <?= $footerClass ?>">
                                        <i class="fas <?= $footerIcon ?>"></i> <?= $footerText ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Late / Undertime -->
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>5</h3>
                                        <p>Late / Undertime</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="small-box-footer bg-white text-warning">
                                        <i class="fas fa-arrow-circle-down"></i> 1 less than yesterday
                                    </div>
                                </div>
                            </div>

                            <!-- Leave Today -->
                            <?php
                            $leaveToday     = countLeavePerDate(date('Y-m-d'));
                            $common_text = '';
                            $count_leave = 0;
                            if (!empty($leaveToday)) {
                                $count_leave = count($leaveToday);
                                $leave_type = array_column($leaveToday, 'type');
                                $most_common_type = array_count_values($leave_type);
                                arsort($most_common_type);
                                $most_common_type = key($most_common_type);

                                $common_text = system__lang('leave', $most_common_type);
                                if (!$common_text || $common_text == 'unknown') {
                                    $common_text = ucwords(str_replace('_', ' ', $most_common_type));
                                    $common_text = 'Mostly ' . $common_text;
                                }
                            }
                            ?>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= $count_leave ?></h3>
                                        <p>Leave Today</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <div class="small-box-footer bg-white text-info">
                                        <i class="fas fa-info-circle"></i> <?= $common_text ?: 'No leaves today' ?>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <!-- dummy div -->
                            <div class="col-12 col-md-4 col-lg-6"></div>

                            <div class="col-12 col-md-4 col-lg-3 text-right">
                                <input type="text" id="table-search-input" placeholder="Search Employee" class="form-control form-control-sm m-b-5" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-4 col-lg-3 text-right">
                                <div class="input-group ">
                                    <input type="text" name="search_leave_to" placeholder="To" class="form-control form-control-sm monthpicker" value="<?= date('F Y', strtotime($searchDate ? $searchDate : date('Y-m-d'))) ?>" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-success btn-sm">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" id="attendanceTableContainer">

                            <?php
                            $grand_labelCount = array(
                                'present' => 0,
                                'absent' => 0,
                                'undertime' => 0,
                                'late' => 0,
                                'ncns' => 0,
                                'vacation_leave' => 0,
                                'sick_leave' => 0,
                                'emergency_leave' => 0,
                                'account_holiday' => 0,
                                'leave_without_pay' => 0,
                                'half_day' => 0,
                                'suspension' => 0,
                            );
                            ?>

                            <table class="table table-hover" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th><span data-toggle="tooltip" title="Lost Time Incident">LTI</span></th>

                                        <!-- loop cuurent days of the month -->
                                        <?php
                                        $daysInMonth = date('t', strtotime($searchDate ? $searchDate : date('Y-m-d')));
                                        $currentMonth = date('M', strtotime($searchDate ? $searchDate : date('Y-m-d')));
                                        $currentYear = date('Y', strtotime($searchDate ? $searchDate : date('Y-m-d')));

                                        for ($day = 1; $day <= $daysInMonth; $day++) {
                                            $dateString = sprintf('%s %02d, %s', $currentMonth, $day, $currentYear);
                                            $dayOfWeek = date('D', strtotime($dateString));
                                            echo "<th class='text-center'><small>$currentMonth</small><br>$day<br><small>$dayOfWeek</small></th>";
                                        }

                                        $att_labels = attStatusLabels();
                                        foreach ($att_labels as $key => $label) {
                                            if (in_array($key, ['absent', 'rest_day'])) continue; // skip absent and rest day
                                            echo "<th class='text-center'><strong data-toggle='tooltip' title='{$label}'>" . strtoupper($key) . "</strong></th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employee as $emp) : ?>
                                        <tr>
                                            <td class="p-5">
                                                <a href="<?= base_url('employee/profile/' . $this->mysecurity->encrypt_url($emp['id'])) ?>" class="d-block" target="_blank"> <?= "{$emp['emp_lname']}, {$emp['emp_fname']}" ?> </a>

                                                <?php
                                                // Determine current day attendance status for badge
                                                $currentDay = date('Y-m-d');
                                                $empAttendanceToday = $attendance[$emp['id']][$currentDay] ?? null;
                                                $badgeText = 'No Record';
                                                $badgeClass = 'badge-secondary';

                                                if ($empAttendanceToday) {
                                                    if (!empty($empAttendanceToday['absent'])) {
                                                        $badgeText = 'Absent';
                                                        $badgeClass = 'badge-danger';
                                                    } elseif (!empty($empAttendanceToday['type'])) {
                                                        $badgeText = ucwords(str_replace('_', ' ', $empAttendanceToday['type']));
                                                        $badgeClass = 'badge-info';
                                                    } else {
                                                        $badgeText = 'Present';
                                                        $badgeClass = 'badge-success';
                                                    }
                                                }
                                                ?>
                                                <small class="badge badge-pill <?= $badgeClass ?> badge-xs"><?= $badgeText ?></small>
                                            </td>

                                            <?php

                                            $employeeAttendance = $attendance[$emp['id']] ?? null;

                                            $totalLTI = 0;
                                            if ($employeeAttendance) {
                                                $totalLTI = array_sum(array_column($employeeAttendance, 'total_lti'));
                                            }

                                            $totallti_formatted = '0';
                                            if ($totalLTI > 0) {
                                                $h = floor($totalLTI / 60);
                                                $m = $totalLTI % 60;

                                                $totallti_formatted = trim(
                                                    ($h > 0 ? "{$h}h"  : '') .
                                                        ($m > 0 ? " {$m}m"  : '')
                                                ) ?: '0 min';
                                            }

                                            ?>

                                            <td class="text-center"><span class="badge badge-pill badge-danger"><?= $totallti_formatted ?></span></td>

                                            <!-- loop cuurent days of the month -->
                                            <?php
                                            $emp_rest_day = explode(',', $emp['rest_day']);

                                            $total_labelCount = array_fill_keys(array_keys($grand_labelCount), 0);

                                            for ($day = 1; $day <= $daysInMonth; $day++) :
                                                $dateString = sprintf('%s %02d, %s', $currentMonth, $day, $currentYear);
                                                $dateFormatted = date('Y-m-d', strtotime($dateString));
                                                $dayOfWeekNum = date('N', strtotime($dateFormatted));

                                                $isRestDay = in_array($dayOfWeekNum, $emp_rest_day);
                                                $employeeAttendance = $attendance[$emp['id']][$dateFormatted] ?? null;
                                                $attType_m = $employeeAttendance['type'] ?? null;

                                                $extraClass = $extraAttrs = $extraDetails = '';

                                                if ($employeeAttendance) {
                                                    $attType = !empty($employeeAttendance['absent']) ? 'absent' : 'present';
                                                    $attId = $this->mysecurity->encrypt_url($employeeAttendance['id']);
                                                    $extraAttrs = ' data-attid="' . $attId . '"';

                                                    // check if attendance is late
                                                    if ((!empty($employeeAttendance['late']) && $employeeAttendance['late'] != '00:00:00') || (!empty($employeeAttendance['over_break']) && $employeeAttendance['over_break'] === true)) {
                                                        $extraClass .= ' att-box-notif ';
                                                    }

                                                    $extraClass .= ' attendance_info-btn ';
                                                } else {
                                                    $attType = $isRestDay ? 'rest_day' : '';
                                                }

                                                if (!empty($attType_m)) {
                                                    $attType = $attType_m;
                                                }

                                                // check if on leave
                                                $leave_today = countLeavePerDate($dateFormatted, $emp['id']);
                                                if (!empty($leave_today)) {
                                                    $leave_type = $leave_today[0]['type'] ?? '';
                                                    if (!empty($leave_type)) {
                                                        $attType = $leave_type;
                                                        $extraClass .= ' att-box-leave ';
                                                    }
                                                }

                                                if (!empty($attType)) {
                                                    $attLabel = ucwords(str_replace('_', ' ', $attType));
                                                    $attText = implode('', array_map(fn($word) => $word[0], explode(' ', $attLabel)));

                                                    $attendanceBox = '<div class="att-box ' . $extraClass . ' att-' . $attType . '" data-toggle="tooltip" data-placement="right" title="' . $attLabel . '"' . $extraAttrs . '>' . $attText . $extraDetails . '</div>';
                                                } else {
                                                    $attendanceBox = '<div class="att-box ' . $extraClass . ' att-no_record" data-toggle="tooltip" data-placement="right" title="No Record"' . $extraAttrs . '><small>N</small></div>';
                                                }

                                                echo '<td class="text-center"' . $extraAttrs . '>' . $attendanceBox . '</td>';

                                                $data_label = $employeeAttendance['label_data'] ?? [];
                                                foreach ($total_labelCount as $key => $value) {
                                                    if (isset($data_label[$key]) && $data_label[$key] > 0) {
                                                        $total_labelCount[$key]++;
                                                        $grand_labelCount[$key]++;
                                                    }
                                                }
                                            endfor;

                                            $total_labels = attStatusLabels('', 'text');
                                            foreach ($total_labels as $key => $label) {
                                                $labelCount = $total_labelCount[$key] ?? 0;
                                                $labelText = $labelCount > 0
                                                    ? "<div class='att-box'><span>{$labelCount}</span></div>"
                                                    : "<div class='att-box att-no_record'><small>0</small></div>";
                                                echo "<td class='text-center' style='border:1px solid #dee2e6;'>{$labelText}</td>";
                                            }
                                            ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Grand Total</th>
                                        <th></th>
                                        <?php
                                        // same day loop placeholder
                                        for ($day = 1; $day <= $daysInMonth; $day++) {
                                            echo "<th class='text-center'>-</th>";
                                        }

                                        // display grand totals
                                        foreach ($total_labels as $key => $label) {
                                            $grandCount = $grand_labelCount[$key] ?? 0;
                                            echo "<th class='text-center'><strong>{$grandCount}</strong></th>";
                                        }
                                        ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<div class="modal fade" id="attendance_info-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Attendance Info</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="attendance_info-form">

                <div class="modal-body">
                    <!-- Edit Form Fields (hidden by default) -->
                    <div id="edit-form-fields" style="display: none;">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="edit_Punch_IN">Punch IN</label>
                                <input id="edit_Punch_IN" name="Punch_IN" type="text" class="form-control datetimepicker" placeholder="Enter Punch IN time">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_Punch_OUT">Punch OUT</label>
                                <input id="edit_Punch_OUT" name="Punch_OUT" type="text" class="form-control datetimepicker" placeholder="Enter Punch OUT time">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_Late">Late</label>
                                <input name="Late" value="00:00:00" id="edit_Late" class="form-control timepickernew" type="text">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_attendance_type">Attendance Type <span class="text-danger">*</span></label>
                                <select name="attendance_type" id="edit_attendance_type" class="form-control" required>
                                    <option value="" selected disabled hidden>Select Attendance Type</option>
                                    <?= admin__lang_select('attendance', 'type') ?>
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label for="edit_Remarks">Remarks</label>
                                <textarea id="edit_Remarks" name="Remarks" class="form-control" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <!-- Display Fields (shown by default) -->
                    <div id="display-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="font-18 text-bold" id="timesheet_date">Timesheet <span class="att_date fw-normal">---</span></h3>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="att_punch_details" id="att_punch_in">
                                            <h3>Punch In at</h3>
                                            <p>---</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="att_chart text-center">
                                            <canvas id="employeeTimeChart" style="min-height: 190px; height: 190px; max-height: 190px; width:190px; max-width: 190px;"></canvas>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="att_punch_details" id="att_punch_out">
                                            <h3>Punch Out at</h3>
                                            <p>---</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="att_punch_details" id="att_punch_late">
                                            <h3>Late</h3>
                                            <p>---</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="att_punch_details" id="att_punch_lti">
                                            <h3>LTI</h3>
                                            <p>---</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="att_punch_details" id="att_punch_break">
                                            <h3>Break</h3>
                                            <p>---</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="att_punch_details" id="att_punch_lunch">
                                            <h3>Lunch</h3>
                                            <p>---</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h3 class="font-18 text-bold">Activity</h3>
                                <div class="timeline timelime-small" id="timeline"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <?php if (check_function('manage_attendance')) : ?>
                            <button type="button" class="btn btn-warning" id="edit_attendance-btn">Edit Attendance</button>
                            <button type="button" class="btn btn-secondary" id="cancel_edit-btn" style="display: none;">Cancel Edit</button>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                        <div>
                            <button type="submit" class="btn btn-primary" id="save_attendance-btn" style="display: none;">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_modal-btn">Close</button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('.monthpicker').datepicker({
            format: 'MM yyyy',
            viewMode: 'months',
            minViewMode: 'months',
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            const yearMonth = e.format(0, 'yyyy-mm');
            var url = '<?= base_url('attendance/index/') ?>' + yearMonth;
            window.location.href = url;
        });

        // --- Helpers ----------------------------------------------------
        const calcScrollY = () => Math.max(220, window.innerHeight - 450) + 'px';
        const useFixedColumns = () => window.innerWidth >= 768;

        // Apply height to .att-con only when it has >=3 .att-box (runs on every draw)
        function applyAttConHeights() {
            const $root = $(table.table().body());
            $root.find('.att-con').each(function() {
                const $el = $(this);
                if ($el.data('checked')) return;
                if ($el.find('.att-box').slice(0, 3).length === 3) {
                    $el.css('height', '75px');
                }
                $el.data('checked', true);
            });
        }

        // --- Init -------------------------------------------------------
        let fixedColsState = useFixedColumns();

        function initDataTable() {
            return $('#attendanceTable').DataTable({
                ordering: true,
                searching: true,
                dom: 'rt',
                paging: false,
                info: false,
                autoWidth: false,
                responsive: true,
                scrollX: true,
                scrollY: calcScrollY(),
                fixedHeader: true,
                fixedColumns: fixedColsState ? {
                    leftColumns: 2
                } : false,
                columnDefs: [{
                        orderable: true,
                        targets: [0, 1]
                    },
                    {
                        orderable: false,
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    const $wrap = $(this.api().table().container());
                    $wrap.find('#attendanceTable_filter').remove();
                }
            });
        }

        let table = initDataTable();

        // custom search input
        $('#table-search-input').on('input', function() {
            table.search(this.value).draw();
        });

        // Run once and on every draw (order/search/responsive)
        applyAttConHeights();
        table.on('draw.dt', applyAttConHeights);

        // --- Resize (debounced) ----------------------------------------
        let resizeTimer = null;
        $(window).on('resize', () => {
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const newFixed = useFixedColumns();

                // update scroll body height (cheaper)
                const newY = calcScrollY();
                const $scrollBody = $(table.table().container()).find('div.dataTables_scrollBody');
                $scrollBody.css({
                    height: newY,
                    maxHeight: newY
                });
                table.columns.adjust().draw(false);

                // If fixedColumns breakpoint changed, re-init once
                if (newFixed !== fixedColsState) {
                    fixedColsState = newFixed;
                    const state = table.state ? table.state() : null;
                    table.destroy();
                    table = initDataTable();
                    if (state) table.state.clear();
                    applyAttConHeights();
                    table.on('draw.dt', applyAttConHeights);
                }
            }, 120);
        });
    });
</script>

<script>
    // Attendance info opener + population
    $(document).ready(function() {
        $(document).on('click', '.attendance_info-btn', function() {
            const attendanceId = $(this).data('attid');

            // Reset static display areas
            $('#timeline').empty();
            $('#timesheet_date .att_date').text('---');
            $('#att_punch_in p, #att_punch_out p, #att_punch_lti p, #att_punch_lunch p, #att_punch_break p, #att_punch_late p')
                .removeClass('text-red').text('---');

            $.ajax({
                url: '<?= base_url('attendance/getAttendanceDetails') ?>',
                type: 'POST',
                data: {
                    attendance_id: attendanceId
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status !== 'success') {
                        alert(res.message);
                        return;
                    }

                    const data = res.data || {};

                    $('#timesheet_date .att_date').text(data.date || '---');

                    if (data.summary) {
                        const wmin = data.total_working_hours_minutes || 0;
                        const bmin = data.summary.total_breaks_minutes || 0;
                        const over = data.summary.total_over_by_minutes || 0;
                        if (typeof renderEmployeeTimeChart === 'function') {
                            renderEmployeeTimeChart('employeeTimeChart', [wmin, Math.max(0, bmin - over), Math.max(0, over)]);
                        }
                    }

                    $('#att_punch_in p').text(data.punch_in || '---');
                    $('#att_punch_out p').text(data.punch_out || '---');

                    if (data.summary?.total_lti_formatted && data.summary.total_lti_formatted !== '---' && data.summary.total_lti_formatted !== '0') {
                        $('#att_punch_lti p').addClass('text-red').text(data.summary.total_lti_formatted);
                    }

                    const lunch = data.summary?.lunch;
                    if (lunch) {
                        $('#att_punch_lunch p')
                            .toggleClass('text-red', lunch.status === 'overlunch')
                            .text(lunch.total_formatted || '---');
                    }

                    const brk = data.summary?.break;
                    if (brk) {
                        $('#att_punch_break p')
                            .toggleClass('text-red', brk.status === 'overbreak')
                            .text(brk.total_formatted || '---');
                    }

                    if (data.late && data.late !== '00:00:00') {
                        const parts = data.late.split(':');
                        $('#att_punch_late p').addClass('text-red').text(`${parts[0] ?? '00'}:${parts[1] ?? '00'}`);
                    }

                    // timeline
                    let timelineHtml = '';
                    if (data.punch_in) {
                        timelineHtml += `<div class="time-label"><span class="bg-green">${data.punch_in}</span></div>`;
                    }
                    if (Array.isArray(data.timeline) && data.timeline.length) {
                        data.timeline.forEach(item => {
                            const isStart = (item.label || '').includes('Start');
                            const itemColor = isStart ? 'green' : 'red';
                            const iconColor = isStart ? 'bg-green1' : 'bg-red1';
                            const icon = (item.label || '').includes('Lunch') ? 'fas fa-utensils' : 'fas fa-clock';
                            timelineHtml += `
                <div>
                  <i class="${icon} ${iconColor} text-white"></i>
                  <div class="timeline-item">
                    <h3 class="timeline-header no-border">
                      <strong class="text-${itemColor}">${item.label}:</strong> ${item.time}
                    </h3>
                  </div>
                </div>`;
                        });
                    } else {
                        timelineHtml += `
              <div>
                <i class="fas fa-clock bg-gray text-white"></i>
                <div class="timeline-item">
                  <h3 class="timeline-header no-border">No Activity</h3>
                </div>
              </div>`;
                    }
                    if (data.punch_out) {
                        timelineHtml += `<div class="time-label"><span class="bg-gray">${data.punch_out}</span></div>`;
                    }
                    $('#timeline').html(timelineHtml);

                    // Store the ID on the info modal and show it
                    $('#attendance_info-modal')
                        .data('attendance-id', attendanceId)
                        .modal({
                            show: true,
                            backdrop: 'static',
                            keyboard: false
                        });
                },
                error: function() {
                    alert('Failed to fetch attendance details.');
                }
            });

            // Submit handler for the info modal (kept as-is; adjust if you actually submit from here)
            $('#attendance_info-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                formData.push({
                    name: 'attendance_id',
                    value: $('#attendance_info-modal').data('attendance-id')
                });

                $.ajax({
                    url: '<?= base_url('attendance/updateAttendance') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#attendance_info-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(res) {
                        page_loader_hide();
                        $.alert({
                            title: res.status === 'success' ? 'Success' : 'Error',
                            content: res.message || (res.status === 'success' ? 'Attendance updated.' : 'Failed to update.'),
                            type: res.status === 'success' ? 'green' : 'red',
                            backgroundDismiss: true,
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: `btn-${res.status === 'success' ? 'green' : 'red'}`,
                                    action: function() {
                                        if (res.status === 'success') location.reload();
                                    }
                                }
                            }
                        });
                    },
                    error: function() {
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating attendance.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        $('#attendance_info-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        let isEditMode = false;

        // Handle edit attendance button click
        $(document).on('click', '#edit_attendance-btn', function() {
            console.log('Edit button clicked');
            const attendanceId = $('#attendance_info-modal').data('attendance-id');

            if (!attendanceId) {
                alert('No attendance record selected.');
                return;
            }

            // Toggle to edit mode
            toggleEditMode(true);

            // Get current attendance data to populate the edit form
            $.ajax({
                url: '<?= base_url('attendance/getAttendanceDetails') ?>',
                type: 'POST',
                data: {
                    attendance_id: attendanceId
                },
                dataType: 'json',
                success: function(res) {
                    console.log('AJAX response for edit:', res);
                    if (res.status !== 'success') {
                        alert(res.message);
                        toggleEditMode(false);
                        return;
                    }

                    const data = res.data;
                    populateEditForm(data);

                    // Initialize date/time pickers
                    initializePickers();
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    alert('Failed to fetch attendance details.');
                    toggleEditMode(false);
                }
            });
        });

        // Handle cancel edit button click
        $(document).on('click', '#cancel_edit-btn', function() {
            toggleEditMode(false);
        });

        // Handle form submission
        $('#attendance_info-form').on('submit', function(e) {
            e.preventDefault();

            if (!isEditMode) return;

            const attendanceId = $('#attendance_info-modal').data('attendance-id');
            const formData = $(this).serializeArray();
            formData.push({
                name: 'attendance_id',
                value: attendanceId
            });

            $.ajax({
                url: '<?= base_url('attendance/updateAttendance') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#save_attendance-btn').prop('disabled', true);
                },
                success: function(res) {
                    page_loader_hide();
                    $.alert({
                        title: res.status === 'success' ? 'Success' : 'Error',
                        content: res.message || (res.status === 'success' ? 'Attendance updated successfully.' : 'Failed to update attendance.'),
                        type: res.status === 'success' ? 'green' : 'red',
                        backgroundDismiss: true,
                        buttons: {
                            ok: {
                                text: 'OK',
                                btnClass: `btn-${res.status === 'success' ? 'green' : 'red'}`,
                                action: function() {
                                    if (res.status === 'success') {
                                        $('#attendance_info-modal').modal('hide');
                                        location.reload();
                                    }
                                }
                            }
                        }
                    });
                },
                error: function() {
                    page_loader_hide();
                    $.alert({
                        title: 'Error',
                        content: 'An error occurred while updating attendance.',
                        type: 'red',
                        backgroundDismiss: true
                    });
                },
                complete: function() {
                    $('#save_attendance-btn').prop('disabled', false);
                }
            });
        });

        // Function to toggle between display and edit mode
        function toggleEditMode(editMode) {
            isEditMode = editMode;

            if (editMode) {
                // Show edit fields, hide display fields
                $('#edit-form-fields').show();
                $('#display-fields').hide();

                // Update buttons
                $('#edit_attendance-btn').hide();
                $('#cancel_edit-btn').show();
                $('#save_attendance-btn').show();

                // Update modal title
                $('.modal-title').text('Edit Attendance');
            } else {
                // Show display fields, hide edit fields
                $('#edit-form-fields').hide();
                $('#display-fields').show();

                // Update buttons
                $('#edit_attendance-btn').show();
                $('#cancel_edit-btn').hide();
                $('#save_attendance-btn').hide();
                $('#close_modal-btn').text('Close');

                // Update modal title
                $('.modal-title').text('Attendance Info');

                // Destroy any existing pickers
                destroyPickers();
            }
        }

        // Function to populate edit form with data
        function populateEditForm(data) {
            // Convert timestamps back to input format
            let punchInFormatted = '';
            let punchOutFormatted = '';

            // Populate the edit form
            $('#edit_Punch_IN').val(data.punch_in_date);
            $('#edit_Punch_OUT').val(data.punch_out_date);
            $('#edit_Late').val(data.late || '00:00:00');
            $('#edit_attendance_type').val(data.type || '').trigger('change');
            $('#edit_Remarks').val(data.notes || '');
        }

        // Function to initialize date/time pickers
        function initializePickers() {
            console.log('Initializing pickers');

            // Initialize timepicker for Late field
            $('#edit_Late').inputmask("99:99:99", {
                placeholder: "HH:MM:SS",
                insertMode: false,
                showMaskOnHover: false
            });
        }

        // Function to destroy pickers
        function destroyPickers() {
            $('#edit_Punch_IN, #edit_Punch_OUT').each(function() {
                if ($(this).data('DateTimePicker')) {
                    $(this).data('DateTimePicker').destroy();
                }
            });

            $('#edit_Late').inputmask('remove');
        }

        // Reset edit mode when modal is closed
        $('#attendance_info-modal').on('hidden.bs.modal', function() {
            toggleEditMode(false);
        });
    });
</script>

<div class="modal fade" id="attendance_summary-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Attendance Summary</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="5">Number Of Employees</th>
                                <th id="attendance_summary-total_employees" class="text-center">0</th>
                            </tr>
                            <tr>
                                <th>Week</th>
                                <th>Date</th>
                                <th colspan="2">Absenteeism Avg.</th>
                                <th colspan="2">Late / UT Avg.</th>
                            </tr>
                        </thead>
                        <tbody id="attendance_summary-tbody">
                            <!-- Summary data will be populated here via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-center"></th>
                                <th class="text-center" id="att-total_absent_count">0</th>
                                <th class="text-center" id="att-total_absent_rate">0%</th>
                                <th class="text-center" id="att-total_late_undertime_count">0</th>
                                <th class="text-center" id="att-total_late_undertime_rate">0%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_modal-btn">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#attendance_summary-btn').on('click', function() {
            const month = '<?= !empty($searchDate) ? $searchDate : date('Y-m') ?>';

            // Clear previous data
            $('#attendance_summary-tbody').empty();
            $('#attendance_summary-total_employees').text('0');

            $.ajax({
                url: '<?= base_url('attendance/weekssummary') ?>',
                type: 'POST',
                data: {
                    month: month
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                },
                success: function(res) {
                    page_loader_hide();
                    if (res.status !== 'success') {
                        alert(res.message || 'Failed to fetch summary.');
                        return;
                    }

                    const summary = res.data || [];
                    if (summary.length === 0) {
                        $('#attendance_summary-tbody').html('<tr><td colspan="6" class="text-center">No data available.</td></tr>');
                        // Reset footer totals
                        $('#att-total_absent_count').text('0');
                        $('#att-total_absent_rate').text('0%');
                        $('#att-total_late_undertime_count').text('0');
                        $('#att-total_late_undertime_rate').text('0%');
                    } else {
                        $('#attendance_summary-total_employees').text(res.employee_count || '0');

                        // Initialize totals
                        let totalAbsentCount = 0;
                        let totalLateUndertimeCount = 0;
                        let totalAbsentRateSum = 0;
                        let totalLateUndertimeRateSum = 0;
                        let weekCount = 0;

                        summary.forEach(weekData => {
                            const row = `
                                <tr>
                                    <td class="text-bold">Week ${weekData.week}</td>
                                    <td class="text-center"><span class="badge bg--primary-soft ">${weekData.start}</span> to <span class="badge bg--primary-soft ">${weekData.end}</span></td>
                                    <td class="text-center">${weekData.absent_count || '0'}</td>
                                    <td class="text-center">${weekData.absent_rate || '0'}</td>
                                    <td class="text-center">${weekData.late_undertime_count || '0'}</td>
                                    <td class="text-center">${weekData.late_undertime_rate || '0'}</td>
                                </tr>`;
                            $('#attendance_summary-tbody').append(row);

                            // Accumulate totals and rates
                            totalAbsentCount += parseInt(weekData.absent_count || '0');
                            totalLateUndertimeCount += parseInt(weekData.late_undertime_count || '0');

                            // Parse rates (remove % sign and convert to number)
                            const absentRate = parseFloat((weekData.absent_rate || '0').toString().replace('%', ''));
                            const lateUndertimeRate = parseFloat((weekData.late_undertime_rate || '0').toString().replace('%', ''));

                            totalAbsentRateSum += absentRate;
                            totalLateUndertimeRateSum += lateUndertimeRate;
                            weekCount++;
                        });

                        // Calculate average rates
                        const avgAbsentRate = weekCount > 0 ?
                            (totalAbsentRateSum / weekCount).toFixed(1) + '%' :
                            '0%';
                        const avgLateUndertimeRate = weekCount > 0 ?
                            (totalLateUndertimeRateSum / weekCount).toFixed(1) + '%' :
                            '0%';

                        // Update footer totals
                        $('#att-total_absent_count').text(totalAbsentCount);
                        $('#att-total_absent_rate').text(avgAbsentRate);
                        $('#att-total_late_undertime_count').text(totalLateUndertimeCount);
                        $('#att-total_late_undertime_rate').text(avgLateUndertimeRate);
                    }

                    // Show the modal
                    $('#attendance_summary-modal').modal('show');
                },
                error: function() {
                    page_loader_hide();
                    alert('An error occurred while fetching the summary.');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        var isTableFullscreen = false;
        var $tableCardBody = $('#attendanceTableContainer').closest('.card-body');
        var $body = $('body');
        var $btn = $('#toggle_fullscreen-table-btn');
        var $table = $('#attendanceTable');

        function setFullscreenTableHeight() {
            if (isTableFullscreen) {
                var winHeight = window.innerHeight || $(window).height();
                var offset = $tableCardBody.offset().top;
                var newHeight = winHeight - offset - 240; // 30px padding
                var dt = $table.DataTable();
                var $scrollBody = $(dt.table().container()).find('div.dataTables_scrollBody');
                $scrollBody.css({
                    height: newHeight + 'px',
                    maxHeight: newHeight + 'px'
                });
                dt.columns.adjust().draw(false);
            }
        }

        function enterFullscreen() {
            isTableFullscreen = true;
            $tableCardBody.addClass('fullscreen-table');
            $body.addClass('fullscreen-table-active');
            $btn.find('i').removeClass('fa-expand').addClass('fa-compress');
            $btn.text(' Exit Fullscreen');
            $btn.prepend('<i class="fa fa-compress"></i>');
            setFullscreenTableHeight();
        }

        function exitFullscreen() {
            isTableFullscreen = false;
            $tableCardBody.removeClass('fullscreen-table');
            $body.removeClass('fullscreen-table-active');
            $btn.find('i').removeClass('fa-compress').addClass('fa-expand');
            $btn.text(' Fullscreen Table');
            $btn.prepend('<i class="fa fa-expand"></i>');
            // Reset table height to default (let DataTable handle)
            var dt = $table.DataTable();
            var $scrollBody = $(dt.table().container()).find('div.dataTables_scrollBody');
            $scrollBody.css({
                height: '',
                maxHeight: ''
            });
            dt.columns.adjust().draw(false);
        }

        $btn.on('click', function() {
            if (!isTableFullscreen) {
                enterFullscreen();
            } else {
                exitFullscreen();
            }
        });

        $(document).on('keydown', function(e) {
            if (isTableFullscreen && (e.key === 'Escape' || e.keyCode === 27)) {
                exitFullscreen();
            }
        });

        $(window).on('resize', function() {
            if (isTableFullscreen) {
                setFullscreenTableHeight();
            }
        });
    });
</script>
<style>
    .fullscreen-table {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999;
        background: #fff;
        overflow: auto;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.2);
        padding: 30px 10px 10px 10px;
    }

    .fullscreen-table-active {
        overflow: hidden !important;
    }
</style>