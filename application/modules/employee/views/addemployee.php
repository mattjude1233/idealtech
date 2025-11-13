<?php

$process = 'add';
if (!empty($list_details) && !empty($employee)) {
    $process = 'update';
}

?>

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
                    <li class="breadcrumb-item"><a href="<?= base_url('employee') ?>">Employee</a></li>
                    <li class="breadcrumb-item active"><?= $process == 'update' ? 'Update Employee' : 'Add Employee' ?></li>
                </ol>
            </div>


            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="<?= base_url('employee') ?>" class="btn btn-pill btn-success btn-md text-white add_employee-btn"> <i class="fa fa-users"></i> Employee List</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">


        <div class="row m0">

            <div class="col-md-12">
                <div class="content-header p-x-0">
                    <?php if ($process == 'update') : ?>
                        <h1 class="m-0">Update Employee</h1>
                    <?php else : ?>
                        <h1 class="m-0">Add Employee</h1>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-12">

                <form id="addemployee-form" action="" class=" form-horizontal">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">General Information</h3>
                        </div>

                        <div class="card-body">

                            <div class="row m-b-10">
                                <div class="form-group row col-md-6 col-12">
                                    <label for="employeeID" class="col-sm-3 col-form-label text-right">Employee ID : <i class="text-red">*</i></label>

                                    <div class="col-sm-9 row">
                                        <input name="Employee_ID" placeholder="Employee ID" id="employeeID" class="form-control" type="text" required>
                                    </div>
                                </div>

                                <div class="form-group row col-md-6 col-12">
                                    <label for="employeeID" class="col-sm-3 col-form-label text-right">Email : <i class="text-red">*</i></label>

                                    <div class="col-sm-9 row">
                                        <input name="Email" placeholder="Email" id="employeeID" class="form-control" type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group row col-md-6">
                                    <label for="firstName" class="col-sm-3 col-form-label text-right">First Name : <i class="text-red">*</i></label>

                                    <div class="col-sm-9 row">
                                        <input name="First_Name" placeholder="First Name" id="firstName" class="form-control" type="text" required>
                                    </div>
                                </div>

                                <div class="form-group row col-md-6">
                                    <label for="firstName" class="col-sm-3 col-form-label text-right">Last Name :<i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">
                                        <input name="Last_Name" placeholder="Last Name" id="lastName" class="form-control" type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group row col-md-6">
                                    <label for="middleName" class="col-sm-3 col-form-label text-right">Middle Name :</label>
                                    <div class="col-sm-9 row">
                                        <input name="Middle_Name" placeholder="Middle Name" id="middleName" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-10">
                                <div class="form-group row col-md-6">
                                    <label for="gender" class="col-sm-3 col-form-label text-right">Gender : <i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">
                                        <select name="Gender" id="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row col-md-6">
                                    <label for="DateOfBirth" class="col-sm-3 col-form-label text-right">Date of Birth : <i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">
                                        <input name="Date_of_Birth" placeholder="Date of Birth" id="DateOfBirth" class="form-control datepicker" type="text" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employee Information</h3>
                        </div>

                        <div class="card-body">

                            <div class="row m-b-10">
                                <div class="form-group row col-lg-6 col-md-12">
                                    <label for="emp_role" class="col-sm-3 col-form-label text-right">Role : <i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">

                                        <?php
                                        $items = users__lang('level');
                                        ?>

                                        <select name="Emp_Role" id="emp_role" class="form-control" required>
                                            <option value="">Select Role</option>
                                            <?php if (!empty($items)) : ?>
                                                <?php foreach ($items as $item) : ?>
                                                    <option value="<?= htmlspecialchars($item['keyid'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div class="form-group row col-lg-6 col-md-12">
                                    <label for="emp_designation" class="col-sm-3 col-form-label text-right">Designation : <i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">

                                        <?php
                                        $items = users__lang('designation');
                                        ?>

                                        <select name="Emp_Designation" id="emp_designation" class="form-control" required>
                                            <option value="">Select Designation</option>
                                            <?php if (!empty($items)) : ?>
                                                <?php foreach ($items as $item) : ?>
                                                    <option value="<?= htmlspecialchars($item['keyid'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row col-md-12 col-lg-6">
                                    <label for="Salary" class="col-sm-3 col-form-label text-right">Monthly Salary : <i class="text-red">*</i></label>

                                    <div class="col-sm-9 row">
                                        <input name="Monthly_Salary" placeholder="Salary" type="text" id="Salary" class="form-control number_only" required>
                                    </div>
                                </div>

                                <div class="form-group row col-md-12 col-lg-6">
                                    <label for="SemiMonthlyRate" class="col-sm-3 col-form-label text-right">Semi-Monthly Rate :</label>
                                    <div class="col-sm-9 row">
                                        <input placeholder="Semi-Monthly Rate" id="SemiMonthlyRate" class="form-control number_only" type="text" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group row col-md-12 col-lg-6">
                                    <label for="hiredDate" class="col-sm-3 col-form-label text-right">Hired Date : <i class="text-red">*</i></label>
                                    <div class="col-sm-9 row">
                                        <input name="Hired_Date" placeholder="Hired Date" id="hiredDate" class="form-control datepicker" type="text" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 m-b-15 text-right">
                        <?php if ($process == 'update') : ?>
                            <button type="submit" class="btn btn-warning has-spinner" id="btn-save"> <i class="fa fa-plus m-r-5"></i> Update Details</button>
                        <?php else : ?>
                            <button type="submit" class="btn btn-success has-spinner" id="btn-save"> <i class="fa fa-plus m-r-5"></i> Add Employee</button>
                        <?php endif; ?>

                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'MM dd, yyyy',
        });

        // compute semi monthly rate based on monthly salary
        $(document).on('keyup', 'input[name="Monthly_Salary"]', function() {
            var monthly_salary = $(this).val();

            monthly_salary = monthly_salary.replace(/,/g, '');
            var semi_monthly_rate = monthly_salary / 2;
            semi_monthly_rate = semi_monthly_rate.toFixed(2).replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

            $('input#SemiMonthlyRate').val(semi_monthly_rate);
        });

        $(document).on('submit', '#addemployee-form', function(e) {
            e.preventDefault();

            var form = $(this);
            var data = form.serializeArray();

            <?php if ($process == 'update') : ?>
                data.push({
                    name: 'userid',
                    value: '<?= $this->mysecurity->encrypt_url($employee['id']) ?>'
                });
            <?php endif; ?>

            $.ajax({
                url: '<?= base_url('employee/processaddemployee') ?>',
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
                            content: 'Employee details updated successfully!',
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
                            title: 'User added successfully!',
                            content: 'Would you like to add another user?',
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
                                        // redirect to employee list
                                        window.location.href = '<?= base_url('employee') ?>';
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

        <?php if ($process == 'update') : ?>
            var list_details = JSON.parse('<?= $list_details ?>');

            $.each(list_details, function(key, value) {

                if (key == 'Date_of_Birth' || key == 'Hired_Date') {
                    if (value == '') {
                        value = '';
                    } else {
                        value = moment(value).format('MM/DD/YYYY');
                    }
                } else if (key == 'Monthly_Salary') {
                    $('input[name="Monthly_Salary"]').val(value).trigger('keyup');
                } else if (key == 'Position' || key == 'Gender') {
                    $('[name="' + key + '"]').select2().val(value).trigger('change.select2');
                }

                $('[name="' + key + '"]').val(value);
            });
        <?php endif; ?>
    });
</script>