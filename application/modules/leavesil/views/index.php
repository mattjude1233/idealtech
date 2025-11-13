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
                    <li class="breadcrumb-item active">Service Incentive Leave</li>
                </ol>
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
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table" id="payrollTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Emp. ID</th>
                                        <th>Hiring Date</th>
                                        <th>Earned SIL</th>
                                        <th>Used SIL</th>
                                        <th>Remaining SIL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($employee)) : ?>
                                        <?php foreach ($employee as $emp) :

                                            $tenure = (new DateTime())->diff(new DateTime($emp['hiring_date']));

                                            // remove if 0
                                            $tenureText = '';
                                            if ($tenure->y > 0) {
                                                $tenureText .= $tenure->y . 'yr' . ($tenure->y > 1 ? 's' : '');
                                            }
                                            if ($tenure->m > 0) {
                                                $tenureText .= ($tenureText ? ', ' : '') . $tenure->m . 'mo' . ($tenure->m > 1 ? 's' : '');
                                            }
                                            if ($tenure->d > 0) {
                                                $tenureText .= ($tenureText ? ', ' : '') . $tenure->d . 'd' . ($tenure->d > 1 ? 's' : '');
                                            }

                                        ?>
                                            <tr data-hid="<?= $this->mysecurity->encrypt_url($emp['id']); ?>">

                                                <td><a href="<?= base_url('leaves/index/' . $emp['emp_id']) ?>" class="d-block" target="_blank"> <?= "{$emp['emp_lname']}, {$emp['emp_fname']}" ?> </a></td>

                                                <td><?= $emp['emp_id'] ?></td>
                                                <td data-order="<?= strtotime($emp['hiring_date']) ?>">
                                                    <span class="d-block"><?= date('M j, Y', strtotime($emp['hiring_date'])) ?></span>
                                                    <small class="text-red">[ <?= $tenureText ?> ]</small>
                                                </td>
                                                <td> <span><?= $emp['earned_sil'] ?></span> </td>
                                                <td> <span><?= $emp['used_sil'] ?></span> </td>
                                                <td> <span><?= $emp['remaining_sil'] ?></span> </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No holidays found for the selected year.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php if (check_function('manage_payroll')) : ?>
    <div class="modal fade" id="payroll_slip-modal" data-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">

                    <?php if (!empty($page_title)) : ?>
                        <h4 class="modal-title"><?= ucfirst(str_replace("Payroll", "Payslip", $page_title)) ?></h4>
                    <?php else: ?>
                        <h4 class="modal-title">Payslip</h4>
                    <?php endif; ?>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="payroll_slip-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <h3 id="employee_name" class="form-control"></h3>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cut Off Period</label>
                                    <h3 id="employee_name" class="form-control"><?= !empty($period) ? admin__lang_select('payroll', 'period', $period, 'Payroll Period') : '' ?></h3>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Payroll_File">Upload Payslip File</label>
                            <input type="file" name="Payroll_File" id="Payroll_File" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="form-text text-muted">Upload the payslip file in JPG, JPEG, PNG, or PDF format.</small>
                        </div>

                        <hr>

                        <div id="payslip_file_preview"></div>
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

            $(document).on('click', '.payroll_slip-btn', function(e) {
                e.preventDefault();
                var empid = $(this).closest('tr').data('hid');
                var period = $('#period_payroll').val();
                var yearmonth = $('#yearmonth_payroll').val();

                // reset the form
                $('#payroll_slip-form')[0].reset();
                $('#payslip_file_preview').html('');

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('payrollgenerate/getpayroll') ?>',
                    data: {
                        empid: empid,
                        period: period,
                        yearmonth: yearmonth,
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#payroll_slip-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        $('#payroll_slip-form').find('#employee_name').text(response.employee_name);
                        $('#payroll_slip-modal').modal('show');

                        // if status is success, populate the form with existing data
                        if (response.status === 'success') {
                            var data = response.data;
                            var details = JSON.parse(data.details);
                            var filePath = "<?= base_url() ?>" + details.file_path;
                            var fileExt = filePath.split('.').pop().toLowerCase();

                            console.log('File Path:', filePath);
                            console.log('File Extension:', fileExt);

                            $('#payroll_slip-form').find('#Payroll_File').val('');

                            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                                $('#payslip_file_preview').html(
                                    '<img src="' + filePath + '" style="width: 100%; height: 100%; max-height:550px; object-fit: contain;">'
                                );
                            } else if (fileExt === 'pdf') {
                                $('#payslip_file_preview').html(
                                    '<embed src="' + filePath + '" type="application/pdf" width="100%" height="550px" />'
                                );
                            } else {
                                $('#payslip_file_preview').html('Unsupported file type.');
                            }
                        } else {
                            $('#payslip_file_preview').html('');
                        }

                        // submit the form
                        $('#payroll_slip-form').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            var formData = new FormData(this);
                            formData.append('empid', empid);
                            formData.append('period', period);
                            formData.append('yearmonth', yearmonth);

                            if (response.status === 'success') {
                                formData.append('payroll_id', response.data.id); // append existing payroll ID if available
                            }

                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('payrollgenerate/save') ?>',
                                data: formData,
                                contentType: false,
                                processData: false,
                                dataType: 'json',
                                beforeSend: function() {
                                    page_loader_show();
                                    $('#payroll_slip-form button[type="submit"]').prop('disabled', true);
                                },
                                success: function(result) {
                                    if (result.status === 'success') {
                                        reloadPayrollTable();
                                        $('#payroll_slip-modal').modal('hide');

                                        $.alert({
                                            title: 'Success!',
                                            content: result.message,
                                            type: 'green',
                                            animateFromElement: false // prevents animation based on trigger element
                                        });
                                    } else {
                                        showError(result.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    showError('An error occurred. Please try again.');
                                },
                                complete: function() {
                                    page_loader_hide();
                                    $('#payroll_slip-form button[type="submit"]').prop('disabled', false);
                                }
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        showError('An error occurred. Please try again.');
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#payroll_slip-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

            // on payroll_file change, preview the file
            $('#Payroll_File').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var fileExt = file.name.split('.').pop().toLowerCase();

                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#payslip_file_preview').html(
                                '<img src="' + e.target.result + '" style="width: 100%; height: 100%; max-height:550px; object-fit: contain;">'
                            );
                        };
                        reader.readAsDataURL(file);
                    } else if (fileExt === 'pdf') {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#payslip_file_preview').html(
                                '<embed src="' + e.target.result + '" type="application/pdf" width="100%" height="550px" />'
                            );
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#payslip_file_preview').html('Unsupported file type.');
                    }
                } else {
                    $('#payslip_file_preview').html('');
                }
            });


        })
    </script>
<?php endif; ?>

<script>
    $(document).ready(function() {

        // on change yearmonth_payroll, period_payroll
        $('#yearmonth_payroll, #period_payroll').on('change', function() {
            var yearmonth = $('#yearmonth_payroll').val();
            var period = $('#period_payroll').val();

            // format yearmonth to YYYY-MM
            yearmonth = moment(yearmonth, 'MMMM YYYY').format('YYYY-MM');

            window.location.href = '<?= base_url('payrollgenerate/index') ?>/' + yearmonth + '/' + period;
        });

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        });

        $('.monthpicker').datepicker({
            format: 'MM yyyy',
            autoclose: true,
            viewMode: 'months',
            minViewMode: 'months',
            todayHighlight: true,
        });


        $('.show_payslip-btn').on('click', function() {
            var period = $('#period_payroll').val();
            var yearmonth = $('#yearmonth_payroll').val();

            $.confirm({
                title: 'Show Payslip',
                content: 'Are you sure you want to show the payslip to all employees?',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Show Payslip',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                url: '<?= base_url('payrollgenerate/showpayslip') ?>',
                                type: 'POST',
                                data: {
                                    period: period,
                                    yearmonth: yearmonth,
                                },
                                dataType: 'json',
                                beforeSend: function() {
                                    page_loader_show();
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        reloadPayrollTable();
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            animateFromElement: false // prevents animation based on trigger element
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error',
                                            content: response.message || 'Failed to show payslip.',
                                            type: 'red',
                                            backgroundDismiss: true
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    showError('An error occurred. Please try again.');
                                },
                                complete: function() {
                                    page_loader_hide();
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-secondary'
                    }
                }
            });
        });
    });

    <?php if (!empty($employee)) : ?>

        function reloadPayrollTable() {
            const $table = $('#payrollTable');

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            $("#payrollTable tbody").load(location.href + " #payrollTable tbody>*", function() {
                $table.DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });
            });
        }

        $(document).ready(function() {
            reloadPayrollTable();
        })
    <?php endif; ?>
</script>