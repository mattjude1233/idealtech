<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">Salary Increase</li>
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

                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-6">
                                <select name="search_employee" class="form-control select2-search">
                                    <option value="all">Select Employee</option>
                                    <?php if (!empty($employee)) : ?>
                                        <?php foreach ($employee as $emp) : ?>
                                            <option value="<?= $this->mysecurity->encrypt_url($emp['id']) ?>" <?= $emp_id == $emp['id'] ? "selected" : "" ?>><?= $emp['emp_fname'] . ' ' . $emp['emp_lname'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>


                            <div class="col-md-3 col-sm-4 col-6">
                                <input type="text" name="search_increase_from" placeholder="From" class="form-control datepicker" value="<?= !empty($date_from) ? date('m/d/Y', strtotime($date_from)) : '' ?>">
                            </div>

                            <div class="col-md-3 col-sm-4 col-6">
                                <input type="text" name="search_increase_to" placeholder="To" class="form-control datepicker" value="<?= !empty($date_to) ? date('m/d/Y', strtotime($date_to)) : '' ?>">
                            </div>

                            <div class="col-md-3 col-sm-4 col-6">
                                <button class="btn btn-success btn-block" id="submit-search">Search</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <table class="table table-hover" id="attendanceNew">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Effective Date</th>
                                    <th>New Salary</th>
                                    <th>Remarks</th>

                                    <th>Date Added</th>
                                    <th>Added By</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($records)) : ?>
                                    <?php foreach ($records as $record) : ?>
                                        <tr class="<?= !empty($record['absent']) ? 'danger-bg-subtle' : '' ?>" data-id="<?= $this->mysecurity->encrypt_url($record['employee_id']) ?>">
                                            <td><a href="<?= base_url('employee/profile/' . $this->mysecurity->encrypt_url($record['employee_id'])) ?>"><?= "{$record['emp_lname']}, {$record['emp_fname']}" ?></a></td>
                                            <td><?= date('F j, Y', strtotime($record['effective_date'])) ?></td>
                                            <td><?= number_format($this->mysecurity->decrypt_url($record['new_salary']), 2) ?></td>
                                            <td><?= $record['remarks'] ?></td>

                                            <td><?= date('F j, Y', strtotime($record['date_added'])) ?></td>
                                            <td><?= $record['added_by_fname'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<script>
    $(document).ready(function() {
        $('.select2-search').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Employee',
            allowClear: true
        });

        $('.select2-search-add').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Employee',
            allowClear: true,
            dropdownParent: $('#add_attendance-modal')
        });

        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true
        });


        $('#submit-search').on('click', function() {
            var emp_id = $('select[name="search_employee"]').val();
            var date_from = $('input[name="search_increase_from"]').val();
            var date_to = $('input[name="search_increase_to"]').val();

            // format date to YYYY-MM-DD for URL
            if (date_from) {
                date_from = moment(date_from, 'MM/DD/YYYY').format('YYYY-MM-DD');
            }

            if (date_to) {
                date_to = moment(date_to, 'MM/DD/YYYY').format('YYYY-MM-DD');
            }

            if (emp_id || date_from || date_to) {
                var url = '<?= base_url('salaryincrease/index') ?>';
                if (emp_id) {
                    url += '/' + emp_id;
                } else {
                    // get the first employee if none selected
                    var firstEmp = $('.select2-search option:first').val();
                    if (firstEmp) {
                        url += '/' + firstEmp;
                    }
                }



                if (date_from && date_to) {
                    url += '/' + date_from + '/' + date_to;
                }
                window.location.href = url;
            } else {
                alert('Please select at least one search criteria.');
            }
        });
    })
</script>

<script>
    $(document).ready(function() {

    });
</script>