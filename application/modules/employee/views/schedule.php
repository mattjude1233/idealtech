<?php $process = 'add';
$encrypted_empid = $this->mysecurity->encrypt_url($employee['id']);
?>

<div class="row m0">
    <div class="col-md-12">
        <div class="content-header p-x-0">
            <a href="javascript:;" class="btn btn-info btn-sm float-right" onclick="history.back()">Back</a>

            <h1 class="m-0">DTR Schedule - [ <strong class="text-info"><?= "{$employee['last_name']}, {$employee['first_name']}" ?></strong> ] </h1>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Schedule List</h3>

                <div class="card-tools">
                    <a href="javascript:;" class="btn btn-sm btn-success addschedule--btn"> <i class="fa fa-user-plus"></i> Add Schedule</a>
                </div>
            </div>

            <div class="card-body">
                <!-- Schedule Table -->

                <table id="schedule-datatable" class="table table-hover"></table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addschedule--modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Schedule</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="addschedule--form">
                <div class="modal-body">

                    <!-- Schedule Date From / Date To -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Date_From">Date From: <i class="text-red">*</i></label>
                                <input name="Date_From" id="Date_From" class="form-control datepicker" type="text" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Date_To">Date To:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" id="Date_To-checkbox">
                                        </span>
                                    </div>

                                    <input name="Date_To" id="Date_To" class="form-control datepicker" type="text" disabled>
                                </div>
                                <small class="text-red font-italic">( Leave <strong>unchecked</strong> for schedules with <strong>No End Date</strong>. )</small>
                            </div>

                        </div>
                    </div>


                    <!-- Schedule Days of Week -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Days of Week: <i class="text-red">*</i></label>

                                <select name="Days_Of_Week[]" class="select2-multiple" multiple="multiple">
                                    <option value="monday" selected>Monday</option>
                                    <option value="tuesday" selected>Tuesday</option>
                                    <option value="wednesday" selected>Wednesday</option>
                                    <option value="thursday" selected>Thursday</option>
                                    <option value="friday" selected>Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Time On, Time Off -->
                    <div class="row m-b-30">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Time_On">Time On: <i class="text-red">*</i></label>
                                <input name="Time_On" value="09:00 am" id="Time_On" class="form-control timepicker" type="text" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Time_Off">Time Off: <i class="text-red">*</i></label>
                                <input name="Time_Off" value="06:00 pm" id="Time_Off" class="form-control timepicker" type="text" required>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Lunch -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Lunch_Start">Lunch Start: </label>
                                <input name="Lunch_Start" value="12:00 am" id="Lunch_Start" class="form-control timepicker" type="text" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Lunch_End">Lunch End: </label>
                                <input name="Lunch_End" value="01:00 pm" id="Lunch_End" class="form-control timepicker" type="text" required>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Break -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Break_Start">Break Start: </label>
                                <input name="Break_Start" value="03:00 pm" id="Break_Start" class="form-control timepicker" type="text" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Break_End">Break End: </label>
                                <input name="Break_End" value="03:30 pm" id="Break_End" class="form-control timepicker" type="text" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-success has-spinner" id="btn-save">Save Schedule</button>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('.select2-multiple').select2();

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'MM dd, yyyy',
        });

        $('#Date_To-checkbox').change(function() {
            if ($(this).is(':checked')) {
                $('#Date_To').prop('disabled', false);
            } else {
                $('#Date_To').val('').prop('disabled', true);
            }
        });


        $(document).on('click', '.addschedule--btn', function() {
            // change addschedule--modal to add mode
            var modalBox = $('#addschedule--modal');
            modalBox.find('.modal-title').text('Add Schedule');
            modalBox.find('button[type="submit"]').text('Save Schedule').removeClass('btn-warning').addClass('btn-success');
            modalBox.find('input, select').prop('disabled', false);

            modalBox.find('#Date_To-checkbox').prop('checked', false);
            modalBox.find('#Date_To').val('').prop('disabled', true);

            // reset form
            modalBox.find('form')[0].reset();

            // set default values for days of week select2
            modalBox.find('select[name="Days_Of_Week[]"]').val(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']).trigger('change');

            modalBox.modal('show');
        });


        // ? Process Schedule Form
        $(document).on('submit', '#addschedule--form', function(e) {
            e.preventDefault();

            var form = $(this);
            var data = form.serializeArray();

            data.push({
                name: 'userid',
                value: '<?= $encrypted_empid ?>'
            });

            $.ajax({
                url: '<?= base_url('employee/scheduleprocess') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-save').buttonLoader('start');
                }
            }).done(function(response) {
                if (response.status == 'success') {

                    <?php if ($process == 'update') : ?>

                        $.alert({
                            title: 'Success!',
                            content: 'Schedule updated successfully!',
                            type: 'green',
                            buttons: {
                                Ok: {
                                    text: 'Ok',
                                    btnClass: 'btn-green',
                                    action: function() {
                                        window.location.href = '<?= base_url('employee') ?>';
                                    }
                                }
                            }
                        });


                    <?php else : ?>
                        $.alert({
                            title: 'Schedule added successfully!',
                            content: 'Would you like to add another schedule?',
                            type: 'green',
                            buttons: {
                                Addanother: {
                                    text: 'Add Another',
                                    btnClass: 'btn-blue',
                                    action: function() {
                                        form[0].reset();
                                        $('#btn-save').buttonLoader('stop');
                                    }
                                },
                                Nope: {
                                    action: function() {
                                        form[0].reset();
                                        $('#btn-save').buttonLoader('stop');
                                        $('#addschedule--modal').modal('hide');
                                    }
                                }
                            }
                        });
                    <?php endif; ?>

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

                // reload schedule datatable
                $('#schedule-datatable').DataTable().ajax.reload();

            }).fail(function() {
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
            });
        });
    });
</script>

<script>
    $(document).ready(function() {

        var schedule_table = $('#schedule-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "<?= base_url('employee/schedulelist') ?>",
                type: "POST",
                data: {
                    "userid": "<?= $encrypted_empid; ?>"
                }
            },
            columns: [{
                "data": {
                    date_start: "date_start",
                    date_end: "date_end"
                },
                "title": "Schedule Date",
                render: function(data) {
                    var date_start = moment(data.date_start).format('MMM DD, YYYY');
                    var date_end = !data.date_end ? "<span class='badge bg-info'>Present</span>" : moment(data.date_end).format('MMM DD, YYYY');

                    return date_start + ' - ' + date_end;
                }
            }, {
                "data": "day_of_week",
                "title": "Day of Week",
                render: function(data) {
                    var days = JSON.parse(data);

                    var daysOfWeek = "";
                    for (var i = 0; i < days.length; i++) {
                        daysOfWeek += "<span class='badge " + `bg--days-${days[i]}` + " m-r-3'>" + days[i].charAt(0).toUpperCase() + days[i].slice(1) + "</span>";
                    }

                    return daysOfWeek;
                }
            }, {
                "data": {
                    time_on: "time_on",
                    time_off: "time_off"
                },
                "title": "Shift Schedule",
                "className": "text-center text-nowrap",
                render: function(data) {
                    var time_on = data.time_on == null ? "" : moment(data.time_on, 'HH:mm:ss').format('hh:mm A');
                    var time_off = data.time_off == null ? "" : moment(data.time_off, 'HH:mm:ss').format('hh:mm A');

                    return `<span class="badge bg-info">${time_on}</span> - <span class="badge bg-red">${time_off}</span>`;
                }
            }, {
                "data": {
                    lunch_start: "lunch_start",
                    lunch_end: "lunch_end"
                },
                "title": "Lunch Schedule",
                "className": "text-center text-nowrap",
                render: function(data) {
                    var lunch_start = data.lunch_start == null ? "" : moment(data.lunch_start, 'HH:mm:ss').format('hh:mm A');
                    var lunch_end = data.lunch_end == null ? "" : moment(data.lunch_end, 'HH:mm:ss').format('hh:mm A');

                    return `<span class="badge bg-info">${lunch_start}</span> - <span class="badge bg-red">${lunch_end}</span>`;
                }
            }, {
                "data": {
                    break_start: "break_start",
                    break_end: "break_end"
                },
                "title": "Break Schedule",
                "className": "text-center text-nowrap",
                render: function(data) {
                    var break_start = data.break_start == null ? "" : moment(data.break_start, 'HH:mm:ss').format('hh:mm A');
                    var break_end = data.break_end == null ? "" : moment(data.break_end, 'HH:mm:ss').format('hh:mm A');

                    return `<span class="badge bg-info">${break_start}</span> - <span class="badge bg-red">${break_end}</span>`;
                }
            }, {
                "data": "date_added",
                "title": "Date Added",
                render: function(data) {
                    return data == null ? '--/--/--' : moment(data).format('MMM DD, YYYY');
                }
            }, {
                "data": "id",
                "title": "Action",
                render: function(data) {
                    return `<a href="javascript:;" class="btn btn-xs btn-warning listuser_update--btn m-r-5" data-schid='${data}'> <i class="fa fa-edit"></i> Edit</a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"  data-schid='${data}'> <i class="fa fa-times"></i> Remove</a>`;
                }
            }]
        });

        $('#schedule-datatable_filter input').unbind().on('keyup', debounce(function() {
            schedule_table.search(this.value).draw();
        }, 500));

        $(document).on('click', '.listuser_update--btn', function() {
            var schid = $(this).data('schid');

            // change addschedule--modal to edit mode
            var modalBox = $('#addschedule--modal');
            modalBox.modal('show');
            modalBox.find('.modal-title').text('Edit Schedule');
            modalBox.find('button[type="submit"]').text('Update Schedule').removeClass('btn-success').addClass('btn-warning');
            modalBox.find('input, select').prop('disabled', true);

            // get schedule details
            $.ajax({
                url: '<?= base_url('employee/getscheduledetails') ?>',
                type: 'POST',
                data: {
                    schedule_id: schid
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-save').buttonLoader('start');
                }
            }).done(function(response) {
                if (response.status == 'success') {
                    var data = response.data;

                    // set schedule details
                    modalBox.find('input[name="Date_From"]').val(data.Date_From);
                    modalBox.find('select[name="Days_Of_Week[]"]').val(data.Days_Of_Week).trigger('change');
                    modalBox.find('input[name="Time_On"]').val(data.Time_On);
                    modalBox.find('input[name="Time_Off"]').val(data.Time_Off);
                    modalBox.find('input[name="Lunch_Start"]').val(data.Lunch_Start);
                    modalBox.find('input[name="Lunch_End"]').val(data.Lunch_End);
                    modalBox.find('input[name="Break_Start"]').val(data.Break_Start);
                    modalBox.find('input[name="Break_End"]').val(data.Break_End);
                    modalBox.find('input, select').prop('disabled', false);

                    if (data.Date_To != null) {
                        modalBox.find('#Date_To-checkbox').prop('checked', true);
                        modalBox.find('#Date_To').val(data.Date_To).prop('disabled', false)
                    } else {
                        modalBox.find('#Date_To-checkbox').prop('checked', false);
                        modalBox.find('#Date_To').val('').prop('disabled', true);
                    }

                    $('#btn-save').buttonLoader('stop');
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
            })

        });

    })
</script>