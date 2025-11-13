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
                    <li class="breadcrumb-item active">Holiday</li>
                </ol>
            </div>

            <?php if (check_function('manage_holiday')) : ?>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="javascript:;" class="btn btn-pill btn-warning btn-md text-white add_holiday-btn"> <i class="fa fa-user-plus"></i> Add Holiday</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row mb-3">
                            <!-- View Toggle Buttons -->
                            <div class="col-12 col-md-4 col-lg-6">
                                <div class="btn-group" role="group" aria-label="View Toggle">
                                    <button type="button" class="btn btn-outline-primary active" id="table-view-btn">
                                        <i class="fa fa-table"></i> Table View
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="calendar-view-btn">
                                        <i class="fa fa-calendar"></i> Month View
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="year-view-btn">
                                        <i class="fa fa-calendar-alt"></i> Year View
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 col-lg-3 text-right" id="search-controls">
                                <input type="text" id="table-search-input" placeholder="Search Holiday" class="form-control form-control-sm m-b-5" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-4 col-lg-3 text-right">
                                <input type="text" name="year_holiday" placeholder="From" class="form-control form-control-sm yearpicker" style="width: 100%; float: right;" value="<?= $year ?>" readonly>
                            </div>
                        </div>

                        <table class="table" id="holidayTable">
                            <thead>
                                <tr>
                                    <th>Holiday</th>
                                    <th>Date</th>
                                    <th>Type</th>

                                    <?php if (check_function('manage_holiday')) : ?>
                                        <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)) : ?>
                                    <?php foreach ($list as $holiday) : ?>
                                        <tr data-hid="<?= $this->mysecurity->encrypt_url($holiday['id']); ?>">
                                            <td><?= $holiday['name'] ?></td>
                                            <td data-sort="<?= strtotime($holiday['date']) ?>"><?= date('F d, Y', strtotime($holiday['date'])) ?></td>
                                            <td><?= $holiday['type'] ?></td>
                                            <?php if (check_function('manage_holiday')) : ?>
                                                <td>
                                                    <a href="javascript:;" class="btn btn-xs btn-warning update_holiday-btn m-r-5" data-toggle="tooltip" title="Update Holiday"> <i class="fa fa-edit"></i></a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger cancel_holiday-btn" data-toggle="tooltip" title="Cancel Holiday"> <i class="fa fa-times"></i></a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No holidays found for the selected year.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Calendar View -->
                        <div id="calendar-view" style="display: none;">
                            <div class="calendar-header mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary" id="prev-month-btn">
                                                <i class="fa fa-chevron-left"></i> Previous
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="next-month-btn">
                                                Next <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h4 id="calendar-month-year" class="mb-0"></h4>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-outline-info" id="today-btn">
                                            <i class="fa fa-calendar-day"></i> Today
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="calendar-grid">
                                <div class="calendar-days-header">
                                    <div class="calendar-day-header">Sun</div>
                                    <div class="calendar-day-header">Mon</div>
                                    <div class="calendar-day-header">Tue</div>
                                    <div class="calendar-day-header">Wed</div>
                                    <div class="calendar-day-header">Thu</div>
                                    <div class="calendar-day-header">Fri</div>
                                    <div class="calendar-day-header">Sat</div>
                                </div>
                                <div id="calendar-days-container" class="calendar-days">
                                    <!-- Calendar days will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Holiday Legend -->
                            <div class="calendar-legend mt-3">
                                <div class="row">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #28a745;"></span>
                                                Holiday
                                            </span>
                                            <span class="legend-item ml-3">
                                                <span class="legend-color" style="background-color: #6c757d;"></span>
                                                Other Month
                                            </span>
                                            <span class="legend-item ml-3">
                                                <span class="legend-color" style="background-color: #007bff;"></span>
                                                Today
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Year View -->
                        <div id="year-view" style="display: none;">
                            <div class="year-header mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary" id="prev-year-btn">
                                                <i class="fa fa-chevron-left"></i> Previous Year
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="next-year-btn">
                                                Next Year <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h4 id="year-display" class="mb-0"></h4>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-outline-info" id="current-year-btn">
                                            <i class="fa fa-calendar-day"></i> Current Year
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="year-grid">
                                <div class="row" id="year-months-container">
                                    <!-- Year months will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Year Holiday Legend -->
                            <div class="year-legend mt-3">
                                <div class="row">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #28a745;"></span>
                                                Holiday
                                            </span>
                                            <span class="legend-item ml-3">
                                                <span class="legend-color" style="background-color: #007bff;"></span>
                                                Today
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php if (check_function('manage_holiday')) : ?>
    <div class="modal fade" id="add_holiday-modal" data-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Holiday</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="add_holiday-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="holiday">Holiday <span class="text-danger">*</span></label>
                            <input type="text" name="Holiday_Name" id="holiday" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="Holiday_Date">Date <span class="text-danger">*</span></label>
                            <input type="text" name="Holiday_Date" id="holiday_Date" class="form-control datepicker" required>
                        </div>

                        <div class="form-group">
                            <label for="holiday_type">Type <span class="text-danger">*</span></label>
                            <select name="Holiday_Type" id="holiday_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <?= admin__lang_select('holiday', 'type') ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <script>
        $(document).ready(function() {

            $(document).on('click', '.add_holiday-btn', function() {
                $('#add_holiday-modal .modal-title').text('Add Holiday');
                $('#add_holiday-form')[0].reset();
                $('#add_holiday-modal').modal('show');

                $(document).off('submit', '#add_holiday-form').on('submit', '#add_holiday-form', function(e) {
                    e.preventDefault();

                    var formdata = $(this).serializeArray();
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('holiday/addholiday') ?>',
                        data: formdata,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {

                                // success alert and reload
                                $.alert({
                                    title: 'Success!',
                                    content: response.message,
                                    type: 'green',
                                    buttons: {
                                        OK: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                location.reload();
                                            }
                                        }
                                    }
                                });

                            } else {
                                $.alert({
                                    title: 'Error!',
                                    content: response.message,
                                    type: 'red',
                                    buttons: {
                                        Ok: {
                                            text: 'Ok',
                                            btnClass: 'btn-red',
                                            action: function() {
                                                $('#btn-save').buttonLoader('stop');
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $.alert({
                                title: 'Error!',
                                content: 'An error occurred while processing your request. Please try again later.',
                                type: 'red',
                                buttons: {
                                    Ok: {
                                        text: 'Ok',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            $('#btn-save').buttonLoader('stop');
                                        }
                                    }
                                }
                            });
                        }
                    });
                });
            });

            $(document).on('click', '.update_holiday-btn', function() {
                var id = $(this).closest('tr').data('hid');

                // change modal title
                $('#add_holiday-modal .modal-title').text('Update Holiday');

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('holiday/getholiday') ?>',
                    data: {
                        holidayid: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#add_holiday-form')[0].reset();
                            $('#add_holiday-modal').modal('show');

                            // populate form fields with response data
                            $('#holiday').val(response.data.name);
                            $('#holiday_Date').val(response.data.date);
                            $('#holiday_type').val(response.data.type);

                            $(document).off('submit', '#add_holiday-form').on('submit', '#add_holiday-form', function(e) {
                                e.preventDefault();

                                var formdata = $(this).serializeArray();
                                formdata.push({
                                    name: 'holidayid',
                                    value: id
                                });


                                // process form submission ajax
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url('holiday/updateholiday') ?>',
                                    data: formdata,
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status == 'success') {

                                            // success alert and reload
                                            $.alert({
                                                title: 'Success!',
                                                content: response.message,
                                                type: 'green',
                                                buttons: {
                                                    OK: {
                                                        text: 'OK',
                                                        btnClass: 'btn-green',
                                                        action: function() {
                                                            location.reload();
                                                        }
                                                    }
                                                }
                                            });

                                        } else {
                                            $.alert({
                                                title: 'Error!',
                                                content: response.message,
                                                type: 'red',
                                                buttons: {
                                                    Ok: {
                                                        text: 'Ok',
                                                        btnClass: 'btn-red',
                                                        action: function() {
                                                            $('#btn-save').buttonLoader('stop');
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        $.alert({
                                            title: 'Error!',
                                            content: 'An error occurred while processing your request. Please try again later.',
                                            type: 'red',
                                            buttons: {
                                                Ok: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-red',
                                                    action: function() {
                                                        $('#btn-save').buttonLoader('stop');
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });


                            });

                        } else {
                            $.alert({
                                title: 'Error!',
                                content: response.message,
                                type: 'red'
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.cancel_holiday-btn', function() {
                var id = $(this).closest('tr').data('hid');

                $.confirm({
                    title: 'Cancel Holiday',
                    content: 'Are you sure you want to cancel this holiday?',
                    type: 'red',
                    buttons: {
                        confirm: {
                            text: 'Yes',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url('holiday/cancelholiday') ?>',
                                    data: {
                                        holidayid: id
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            $.alert({
                                                title: 'Success!',
                                                content: response.message,
                                                type: 'green'
                                            });
                                            location.reload();
                                        } else {
                                            $.alert({
                                                title: 'Error!',
                                                content: response.message,
                                                type: 'red'
                                            });
                                        }
                                    }
                                });
                            }
                        },
                        cancel: function() {}
                    }
                });
            });
        })
    </script>
<?php endif; ?>

<script>
    $(document).ready(function() {

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        });

        $('.yearpicker').datepicker({
            format: 'yyyy',
            viewMode: 'years',
            minViewMode: 'years',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function(e) {
            var year = e.date.getFullYear();
            window.location.href = "<?= base_url('holiday/index/') ?>" + year;
        });

        // Update calendar when year changes
        $('input[name="year_holiday"]').on('change', function() {
            const year = parseInt($(this).val());
            if (year && year !== currentCalendarDate.getFullYear()) {
                currentCalendarDate = new Date(year, currentCalendarDate.getMonth(), 1);
                if ($('#calendar-view').is(':visible')) {
                    renderCalendar();
                }
            }
        });

        <?php if (!empty($list)) : ?>
            $('#holidayTable').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,

                // order by date ascending
                "order": [
                    [1, 'asc']
                ],
                initComplete: function() {
                    const $wrap = $(this.api().table().container());
                    $wrap.find('.dataTables_filter').hide(); // hide default search
                }
            });

            $('#table-search-input').on('input', function() {
                $('#holidayTable').DataTable().search($(this).val()).draw();
            });
        <?php endif; ?>

        // Calendar functionality
        let currentCalendarDate = new Date(); // Start with today's date
        let holidays = <?= json_encode($list ?? []) ?>;

        // View toggle functionality
        $('#table-view-btn').click(function() {
            $(this).addClass('active');
            $('#calendar-view-btn, #year-view-btn').removeClass('active');
            $('#holidayTable').show();
            $('#calendar-view, #year-view').hide();
            $('#search-controls').show();
        });

        $('#calendar-view-btn').click(function() {
            $(this).addClass('active');
            $('#table-view-btn, #year-view-btn').removeClass('active');
            $('#holidayTable, #year-view').hide();
            $('#calendar-view').show();
            $('#search-controls').hide();
            renderCalendar();
        });

        $('#year-view-btn').click(function() {
            $(this).addClass('active');
            $('#table-view-btn, #calendar-view-btn').removeClass('active');
            $('#holidayTable, #calendar-view').hide();
            $('#year-view').show();
            $('#search-controls').hide();
            renderYearView();
        });

        // Calendar navigation
        $('#prev-month-btn').click(function() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
            renderCalendar();
        });

        $('#next-month-btn').click(function() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
            renderCalendar();
        });

        $('#today-btn').click(function() {
            currentCalendarDate = new Date();
            renderCalendar();
        });

        // Year view navigation
        $('#prev-year-btn').click(function() {
            currentCalendarDate.setFullYear(currentCalendarDate.getFullYear() - 1);
            renderYearView();
        });

        $('#next-year-btn').click(function() {
            currentCalendarDate.setFullYear(currentCalendarDate.getFullYear() + 1);
            renderYearView();
        });

        $('#current-year-btn').click(function() {
            currentCalendarDate = new Date();
            renderYearView();
        });

        function renderCalendar() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            
            // Update month/year display
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            $('#calendar-month-year').text(monthNames[month] + ' ' + year);

            // Calculate calendar dates
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay()); // Start from Sunday

            const endDate = new Date(lastDay);
            endDate.setDate(endDate.getDate() + (6 - lastDay.getDay())); // End on Saturday

            // Clear calendar
            $('#calendar-days-container').empty();

            // Generate calendar days
            const currentDate = new Date(startDate);
            const today = new Date();

            while (currentDate <= endDate) {
                const dayElement = $('<div class="calendar-day"></div>');
                const dateStr = currentDate.getFullYear() + '-' + 
                    String(currentDate.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(currentDate.getDate()).padStart(2, '0');

                // Add day number
                dayElement.append('<div class="day-number">' + currentDate.getDate() + '</div>');

                // Check if this date has holidays
                const dayHolidays = holidays.filter(function(holiday) {
                    return holiday.date === dateStr;
                });

                // Add holiday markers
                if (dayHolidays.length > 0) {
                    dayElement.addClass('has-holiday');
                    const holidayContainer = $('<div class="holiday-list"></div>');
                    dayHolidays.forEach(function(holiday) {
                        const holidayItem = $('<div class="holiday-item" title="' + holiday.name + ' (' + holiday.type + ')"></div>');
                        const holidayText = $('<span class="holiday-text">' + holiday.name + '</span>');
                        holidayItem.append(holidayText);
                        
                        // Add action buttons for holiday management
                        <?php if (check_function('manage_holiday')) : ?>
                        const actionButtons = $('<div class="holiday-actions"></div>');
                        const editBtn = $('<button class="btn-holiday-edit" title="Edit Holiday"><i class="fa fa-edit"></i></button>');
                        const deleteBtn = $('<button class="btn-holiday-delete" title="Delete Holiday"><i class="fa fa-times"></i></button>');
                        
                        editBtn.click(function(e) {
                            e.stopPropagation();
                            editHolidayById(holiday);
                        });
                        
                        deleteBtn.click(function(e) {
                            e.stopPropagation();
                            deleteHolidayById(holiday);
                        });
                        
                        actionButtons.append(editBtn);
                        actionButtons.append(deleteBtn);
                        holidayItem.append(actionButtons);
                        <?php else: ?>
                        // Add click handler for viewing holiday details (non-management users)
                        holidayItem.click(function(e) {
                            e.stopPropagation();
                            $.alert({
                                title: holiday.name,
                                content: 'Date: ' + holiday.date + '<br>Type: ' + holiday.type,
                                type: 'blue'
                            });
                        });
                        <?php endif; ?>
                        
                        holidayContainer.append(holidayItem);
                    });
                    dayElement.append(holidayContainer);
                }

                // Add classes for styling
                if (currentDate.getMonth() !== month) {
                    dayElement.addClass('other-month');
                }

                if (currentDate.toDateString() === today.toDateString()) {
                    dayElement.addClass('today');
                }

                // Add click handler for adding holidays
                <?php if (check_function('manage_holiday')) : ?>
                dayElement.click(function() {
                    if (!$(this).hasClass('other-month')) {
                        const clickedDate = new Date(currentDate);
                        const formattedDate = String(clickedDate.getDate()).padStart(2, '0') + '-' + 
                            String(clickedDate.getMonth() + 1).padStart(2, '0') + '-' + 
                            clickedDate.getFullYear();
                        
                        $('#add_holiday-modal .modal-title').text('Add Holiday');
                        $('#add_holiday-form')[0].reset();
                        $('#holiday_Date').val(formattedDate);
                        $('#add_holiday-modal').modal('show');
                        
                        // Set up form submission
                        $(document).off('submit', '#add_holiday-form').on('submit', '#add_holiday-form', function(e) {
                            e.preventDefault();
                            var formdata = $(this).serializeArray();
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('holiday/addholiday') ?>',
                                data: formdata,
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status == 'success') {
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            buttons: {
                                                OK: {
                                                    text: 'OK',
                                                    btnClass: 'btn-green',
                                                    action: function() {
                                                        location.reload();
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error!',
                                            content: response.message,
                                            type: 'red'
                                        });
                                    }
                                }
                            });
                        });
                    }
                });
                <?php endif; ?>

                $('#calendar-days-container').append(dayElement);
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }

        function renderYearView() {
            const year = currentCalendarDate.getFullYear();
            $('#year-display').text(year);

            // Clear year view
            $('#year-months-container').empty();

            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];

            for (let month = 0; month < 12; month++) {
                const monthContainer = $('<div class="col-lg-3 col-md-4 col-sm-6 mb-4"></div>');
                const monthCard = $('<div class="mini-calendar card"></div>');
                const monthHeader = $('<div class="mini-calendar-header card-header py-2 text-center"></div>');
                monthHeader.text(monthNames[month]);
                monthCard.append(monthHeader);

                const monthBody = $('<div class="mini-calendar-body card-body p-2"></div>');
                
                // Create mini calendar grid
                const miniGrid = $('<div class="mini-calendar-grid"></div>');
                
                // Add day headers
                const dayHeaders = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                dayHeaders.forEach(day => {
                    miniGrid.append('<div class="mini-day-header">' + day + '</div>');
                });

                // Calculate month dates
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());

                const endDate = new Date(lastDay);
                endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));

                const currentDate = new Date(startDate);
                const today = new Date();

                while (currentDate <= endDate) {
                    const dayElement = $('<div class="mini-day"></div>');
                    const dateStr = currentDate.getFullYear() + '-' + 
                        String(currentDate.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(currentDate.getDate()).padStart(2, '0');

                    dayElement.text(currentDate.getDate());

                    // Check if this date has holidays
                    const dayHolidays = holidays.filter(function(holiday) {
                        return holiday.date === dateStr;
                    });

                    if (dayHolidays.length > 0) {
                        dayElement.addClass('mini-has-holiday');
                        let tooltipText = dayHolidays.map(h => h.name + ' (' + h.type + ')').join('\n');
                        dayElement.attr('title', tooltipText);
                        dayElement.attr('data-toggle', 'tooltip');
                        
                        // Add context menu for holiday management
                        <?php if (check_function('manage_holiday')) : ?>
                        dayElement.off('contextmenu').on('contextmenu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            // Show context menu for multiple holidays
                            if (dayHolidays.length === 1) {
                                const holiday = dayHolidays[0];
                                showHolidayContextMenu(e, holiday);
                            } else {
                                showMultipleHolidaysMenu(e, dayHolidays);
                            }
                        });
                        
                        // Double click to edit first holiday
                        dayElement.off('dblclick').on('dblclick', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (dayHolidays.length > 0) {
                                editHolidayById(dayHolidays[0]);
                            }
                        });
                        <?php endif; ?>
                    }

                    // Add classes for styling
                    if (currentDate.getMonth() !== month) {
                        dayElement.addClass('mini-other-month');
                    }

                    if (currentDate.toDateString() === today.toDateString()) {
                        dayElement.addClass('mini-today');
                    }

                    // Add click handler to navigate to month view
                    dayElement.click(function() {
                        if (!$(this).hasClass('mini-other-month')) {
                            currentCalendarDate = new Date(year, month, parseInt($(this).text()));
                            $('#calendar-view-btn').trigger('click');
                        }
                    });

                    miniGrid.append(dayElement);
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                monthBody.append(miniGrid);
                monthCard.append(monthBody);
                monthContainer.append(monthCard);
                $('#year-months-container').append(monthContainer);
            }

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Helper functions for holiday management
        function editHolidayById(holiday) {
            if (!holiday.encrypted_id) {
                console.error('Holiday encrypted ID not found');
                return;
            }

            // Change modal title
            $('#add_holiday-modal .modal-title').text('Update Holiday');

            $.ajax({
                type: 'POST',
                url: '<?= base_url('holiday/getholiday') ?>',
                data: {
                    holidayid: holiday.encrypted_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        $('#add_holiday-form')[0].reset();
                        $('#add_holiday-modal').modal('show');

                        // populate form fields with response data
                        $('#holiday').val(response.data.name);
                        $('#holiday_Date').val(response.data.date);
                        $('#holiday_type').val(response.data.type);

                        $(document).off('submit', '#add_holiday-form').on('submit', '#add_holiday-form', function(e) {
                            e.preventDefault();

                            var formdata = $(this).serializeArray();
                            formdata.push({
                                name: 'holidayid',
                                value: holiday.encrypted_id
                            });

                            // process form submission ajax
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('holiday/updateholiday') ?>',
                                data: formdata,
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status == 'success') {
                                        // success alert and reload
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            buttons: {
                                                OK: {
                                                    text: 'OK',
                                                    btnClass: 'btn-green',
                                                    action: function() {
                                                        location.reload();
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error!',
                                            content: response.message,
                                            type: 'red'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $.alert({
                                        title: 'Error!',
                                        content: 'An error occurred while processing your request. Please try again later.',
                                        type: 'red'
                                    });
                                }
                            });
                        });
                    } else {
                        $.alert({
                            title: 'Error!',
                            content: response.message,
                            type: 'red'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $.alert({
                        title: 'Error!',
                        content: 'An error occurred while fetching holiday details. Please try again later.',
                        type: 'red'
                    });
                }
            });
        }

        function deleteHolidayById(holiday) {
            if (!holiday.encrypted_id) {
                console.error('Holiday encrypted ID not found');
                return;
            }

            $.confirm({
                title: 'Cancel Holiday',
                content: 'Are you sure you want to cancel "' + holiday.name + '"?',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('holiday/cancelholiday') ?>',
                                data: {
                                    holidayid: holiday.encrypted_id
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            buttons: {
                                                OK: {
                                                    text: 'OK',
                                                    btnClass: 'btn-green',
                                                    action: function() {
                                                        location.reload();
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error!',
                                            content: response.message,
                                            type: 'red'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $.alert({
                                        title: 'Error!',
                                        content: 'An error occurred while cancelling the holiday. Please try again later.',
                                        type: 'red'
                                    });
                                }
                            });
                        }
                    },
                    cancel: function() {}
                }
            });
        }

        function showHolidayContextMenu(e, holiday) {
            // Remove any existing context menu
            $('.holiday-context-menu').remove();
            
            const contextMenu = $('<div class="holiday-context-menu"></div>');
            contextMenu.css({
                position: 'fixed',
                top: e.clientY + 'px',
                left: e.clientX + 'px',
                zIndex: 9999
            });
            
            const editOption = $('<div class="context-menu-item"><i class="fa fa-edit"></i> Edit Holiday</div>');
            const deleteOption = $('<div class="context-menu-item text-danger"><i class="fa fa-trash"></i> Delete Holiday</div>');
            
            editOption.click(function() {
                editHolidayById(holiday);
                $('.holiday-context-menu').remove();
            });
            
            deleteOption.click(function() {
                deleteHolidayById(holiday);
                $('.holiday-context-menu').remove();
            });
            
            contextMenu.append('<div class="context-menu-header">' + holiday.name + '</div>');
            contextMenu.append(editOption);
            contextMenu.append(deleteOption);
            
            $('body').append(contextMenu);
            
            // Remove context menu when clicking elsewhere
            $(document).one('click', function() {
                $('.holiday-context-menu').remove();
            });
        }

        function showMultipleHolidaysMenu(e, holidays) {
            // Remove any existing context menu
            $('.holiday-context-menu').remove();
            
            const contextMenu = $('<div class="holiday-context-menu"></div>');
            contextMenu.css({
                position: 'fixed',
                top: e.clientY + 'px',
                left: e.clientX + 'px',
                zIndex: 9999
            });
            
            contextMenu.append('<div class="context-menu-header">Multiple Holidays</div>');
            
            holidays.forEach(function(holiday) {
                const holidayItem = $('<div class="context-menu-subitem">' + holiday.name + '</div>');
                const subMenu = $('<div class="context-submenu"></div>');
                
                const editOption = $('<div class="context-menu-item"><i class="fa fa-edit"></i> Edit</div>');
                const deleteOption = $('<div class="context-menu-item text-danger"><i class="fa fa-trash"></i> Delete</div>');
                
                editOption.click(function() {
                    editHolidayById(holiday);
                    $('.holiday-context-menu').remove();
                });
                
                deleteOption.click(function() {
                    deleteHolidayById(holiday);
                    $('.holiday-context-menu').remove();
                });
                
                subMenu.append(editOption);
                subMenu.append(deleteOption);
                holidayItem.append(subMenu);
                contextMenu.append(holidayItem);
            });
            
            $('body').append(contextMenu);
            
            // Remove context menu when clicking elsewhere
            $(document).one('click', function() {
                $('.holiday-context-menu').remove();
            });
        }

        // Initialize views
        if ($('#calendar-view').is(':visible')) {
            renderCalendar();
        } else if ($('#year-view').is(':visible')) {
            renderYearView();
        }
    });
</script>

<style>
    /* Calendar Styles */
    .calendar-grid {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .calendar-days-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-day-header {
        padding: 10px;
        text-align: center;
        font-weight: bold;
        color: #495057;
        border-right: 1px solid #dee2e6;
    }

    .calendar-day-header:last-child {
        border-right: none;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        min-height: 400px;
    }

    .calendar-day {
        min-height: 80px;
        padding: 5px;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        background-color: #fff;
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
    }

    .calendar-day:hover {
        background-color: #f8f9fa;
    }

    .calendar-day:nth-child(7n) {
        border-right: none;
    }

    .calendar-day.other-month {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .calendar-day.other-month:hover {
        background-color: #e9ecef;
    }

    .calendar-day.today {
        background-color: #007bff;
        color: white;
    }

    .calendar-day.today:hover {
        background-color: #0056b3;
    }

    .calendar-day.has-holiday {
        background-color: #d4edda;
    }

    .calendar-day.has-holiday:hover {
        background-color: #c3e6cb;
    }

    .calendar-day.today.has-holiday {
        background-color: #28a745;
    }

    .calendar-day.today.has-holiday:hover {
        background-color: #1e7e34;
    }

    .day-number {
        font-weight: bold;
        margin-bottom: 3px;
    }

    .holiday-list {
        font-size: 10px;
        line-height: 1.2;
    }

    .holiday-item {
        background-color: #28a745;
        color: white;
        padding: 1px 3px;
        margin: 1px 0;
        border-radius: 2px;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: background-color 0.2s;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .holiday-item:hover {
        background-color: #1e7e34;
    }

    .holiday-item:hover .holiday-actions {
        display: flex;
    }

    .holiday-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .holiday-actions {
        display: none;
        margin-left: 3px;
        align-items: center;
    }

    .btn-holiday-edit,
    .btn-holiday-delete {
        background: none;
        border: none;
        color: white;
        padding: 1px 2px;
        margin: 0 1px;
        border-radius: 2px;
        cursor: pointer;
        font-size: 8px;
        line-height: 1;
        transition: background-color 0.2s;
    }

    .btn-holiday-edit:hover {
        background-color: rgba(255,255,255,0.2);
    }

    .btn-holiday-delete:hover {
        background-color: #dc3545;
    }

    .other-month .holiday-item {
        background-color: #6c757d;
    }

    .other-month .holiday-item:hover {
        background-color: #5a6268;
    }

    /* Context Menu Styles */
    .holiday-context-menu {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        min-width: 150px;
        z-index: 9999;
    }

    .context-menu-header {
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-bottom: 1px solid #dee2e6;
        font-weight: bold;
        font-size: 0.875rem;
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .context-menu-item {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.2s;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .context-menu-item i {
        margin-right: 8px;
        width: 12px;
    }

    .context-menu-item:hover {
        background-color: #f8f9fa;
    }

    .context-menu-item.text-danger:hover {
        background-color: #f8d7da;
        color: #721c24;
    }

    .context-menu-subitem {
        padding: 6px 12px;
        font-weight: 500;
        font-size: 0.8rem;
        border-bottom: 1px solid #dee2e6;
        position: relative;
    }

    .context-menu-subitem:last-child {
        border-bottom: none;
    }

    .context-submenu {
        margin-top: 4px;
        padding-left: 12px;
    }

    .context-submenu .context-menu-item {
        padding: 4px 8px;
        font-size: 0.8rem;
    }

    .calendar-legend {
        padding: 10px 0;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .calendar-day {
            min-height: 60px;
            font-size: 12px;
        }
        
        .holiday-item {
            font-size: 9px;
        }
        
        .calendar-header .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }

    /* Year View Styles */
    .year-grid {
        margin: 0 -10px;
    }

    .mini-calendar {
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .mini-calendar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .mini-calendar-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .mini-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background-color: #dee2e6;
    }

    .mini-day-header {
        background-color: #e9ecef;
        text-align: center;
        padding: 3px 2px;
        font-size: 0.7rem;
        font-weight: bold;
        color: #495057;
    }

    .mini-day {
        background-color: #fff;
        text-align: center;
        padding: 4px 2px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: background-color 0.2s;
        min-height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mini-day:hover {
        background-color: #f8f9fa;
    }

    .mini-day.mini-other-month {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: default;
    }

    .mini-day.mini-today {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .mini-day.mini-today:hover {
        background-color: #0056b3;
    }

    .mini-day.mini-has-holiday {
        background-color: #28a745;
        color: white;
        font-weight: bold;
    }

    .mini-day.mini-has-holiday:hover {
        background-color: #1e7e34;
    }

    .mini-day.mini-today.mini-has-holiday {
        background-color: #17a2b8;
    }

    .mini-day.mini-today.mini-has-holiday:hover {
        background-color: #117a8b;
    }

    /* Year view responsive adjustments */
    @media (max-width: 768px) {
        .year-header .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .mini-calendar-header {
            font-size: 0.8rem;
            padding: 0.25rem;
        }
        
        .mini-day {
            font-size: 0.6rem;
            min-height: 18px;
        }
        
        .mini-day-header {
            font-size: 0.6rem;
            padding: 2px 1px;
        }
    }
</style>