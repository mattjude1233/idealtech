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
                    <li class="breadcrumb-item active">Payroll</li>
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
                        <div class="text-right d-block">
                            <select name="period_payroll" id="period_payroll" class="form-control d-inline-block" style="width: 175px; margin-right: 10px;">
                                <?= admin__lang_select('payroll', 'period', '', 'Cut off period', $period) ?>
                            </select>

                            <input type="text" name="yearmonth_payroll" id="yearmonth_payroll" placeholder="From" class="form-control d-inline-block monthpicker" style="width: 150px;" value="<?= !empty($yearmonth) ? date('F Y', strtotime($yearmonth)) : date('F Y') ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <?php if (!empty($payroll)): ?>
                            <!-- display image -->
                            <div class="text-center mb-3">
                                <?php
                                $payroll_details = json_decode($payroll['details'], true);

                                if (!empty($payroll_details['file_path'])) {
                                    $file_path = base_url($payroll_details['file_path']);
                                    $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

                                    if (strtolower($file_ext) === 'pdf') {
                                        echo '<embed src="' . $file_path . '" type="application/pdf" width="100%" height="600px" />';
                                    } else {
                                        echo '<img src="' . $file_path . '" class="img-fluid" alt="Payroll Slip">';
                                    }
                                } else {
                                    echo '<i class="fa fa-file-text-o fa-5x"></i>';
                                }
                                ?>
                            </div>

                            <div class="text-center italic text-red"><i>Payslips are automatically removed after six months.</i></div>
                        <?php else: ?>
                            <div class="text-center"><i>Payroll is not yet uploaded for this period.</i></div>
                        <?php endif; ?>

                    </div>

                    <?php if (!empty($payroll)):
                        $file_path = !empty($payroll_details['file_path']) ? base_url($payroll_details['file_path']) : '';
                        $file_name = !empty($payroll_details['file_name']) ? $payroll_details['file_name'] : 'Payroll Slip';
                    ?>
                        <div class="card-footer text-right">
                            <a href="<?= $file_path ?>" class="btn btn-primary" target="_blank" download="<?= $file_name ?>">
                                <i class="fa fa-download"></i> Download Payroll Slip
                            </a>
                        </div>
                    <?php endif; ?>


                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<script>
    $(document).ready(function() {
        $('#yearmonth_payroll, #period_payroll').on('change', function() {
            var yearmonth = $('#yearmonth_payroll').val();
            var period = $('#period_payroll').val();

            // format yearmonth to YYYY-MM
            yearmonth = moment(yearmonth, 'MMMM YYYY').format('YYYY-MM');

            window.location.href = '<?= base_url('payroll/index') ?>/' + yearmonth + '/' + period;
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

        <?php if (!empty($employee)) : ?>
            $('#payrollTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        <?php endif; ?>
    });
</script>