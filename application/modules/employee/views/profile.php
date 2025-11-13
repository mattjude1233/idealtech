<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-12">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home text-primary"></i></a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12 col-xl-4">

                <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline">
                    <div class="card-body box-profile border-bottom">
                        <div class="text-center">

                            <?php
                            $emp_profile_img = base_url('dist/img/user-account-profile.png');
                            if (!empty($employee['profile'])) {
                                $profile_img = json_decode($employee['profile'], true);

                                if (!empty($profile_img['file_path'])) {
                                    $emp_profile_img = base_url($profile_img['file_path']);
                                } else {
                                    $emp_profile_img = base_url('dist/img/user-account-profile.png');
                                }
                            }
                            ?>

                            <img class="profile-user-img img-fluid img-circle" src="<?= $emp_profile_img ?>" alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center <?= ($employee['status'] == 1) ? "text-red text-bold" : "" ?>"><?= "{$employee['emp_lname']}, {$employee['emp_fname']} " . (!empty($employee['emp_mname']) ? strtoupper(substr($employee['emp_mname'], 0, 1)) . "." : '') ?></h3>

                        <?php if (!empty($employee['designation'])): ?>
                            <p class="text-center m-b-5"> <span class="badge badge-soft-primary"><?= users__lang('designation', $employee['designation'])  ?></span> </p>
                        <?php endif; ?>

                        <?php if (!empty($employee['emp_level'])): ?>
                            <p class="text-center m-b-5"> <span class="badge badge-soft-info"><?= users__lang('level', $employee['emp_level'])  ?></span> </p>
                        <?php endif; ?>

                        <?php if ($employee['status'] == 1) : ?>
                            <p class="text-center m-b-5"> <span class="badge badge-soft-danger">INACTIVE EMPLOYEE</span> </p>
                        <?php endif; ?>

                        <ul class="list-group list-group-nobordered mb-3 m-t-10">
                            <li class="list-group-item">
                                <span>Employee ID</span>
                                <p class="text-dark text-bold"><?= $employee['emp_id'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Badge Number</span>
                                <p class="text-dark text-bold"><?= $employee['badge_number'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Locker</span>
                                <p class="text-dark text-bold"><?= $employee['locker_number'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <hr>
                            </li>
                            <li class="list-group-item">
                                <span>Salary</span>

                                <?php if (!check_function('manage_salaryincrease')) : ?>
                                    <p><a href="javascript:;" class="btn btn-xs btn-primary" id="employee-salary_details-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-eye m-r-5"></i>Salary</a></p>
                                <?php else: ?>
                                    <?php

                                    $employee_salary = '0.00';
                                    if (!empty($this->mysecurity->decrypt_url($employee['salary']))) {
                                        $employee_salary = number_format($this->mysecurity->decrypt_url($employee['salary']), 2);
                                    }
                                    ?>

                                    <p class="text-dark text-bold"><?= $employee_salary ?></p>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item">
                                <span>Date Hired</span>
                                <p class="text-dark text-bold"><?= $employee['hiring_date'] ? date('F j, Y', strtotime($employee['hiring_date'])) : '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Account</span>
                                <p class="font-17 text-primary d-block m-t-3"><?= $employee['account'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Supervisor</span>
                                <p class="font-17 text-primary d-block m-t-3"><?= $employee['supervisor_name'] ?? '--' ?></p>
                            </li>
                        </ul>

                        <?php if (check_function('manage_profile')) : ?>
                            <a href="javascript:;" class="btn-block btn btn-sm btn-primary" id="edit_info_btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-pen m-r-5"></i>Edit Info</a>
                        <?php endif; ?>

                        <?php if (check_function('manage_salaryincrease')) : ?>
                            <a href="javascript:;" class="btn-block btn btn-sm btn-primary" id="employee-salary_increase-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-dollar-sign m-r-5"></i>Salary Increase</a>
                        <?php endif; ?>

                        <?php if (check_function('deactivate_employee')) : ?>

                            <?php if ($employee['status'] == 0) : ?>
                                <a href="javascript:;" class="btn-block btn btn-sm btn-primary" id="employee-deactivate-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-user-slash m-r-5"></i>Deactivate Employee</a>
                            <?php else: ?>
                                <a href="javascript:;" class="btn-block btn btn-sm btn-success" id="employee-reactivate-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-user-check m-r-5"></i>Reactivate Employee</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="card-body box-profile border-bottom">
                        <?php if (check_function('manage_profile')) : ?>
                            <a href="javascript:;" class="btn btn-xs btn-icon float-right" id="edit-basic_info-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                        <?php endif; ?>

                        <h6 class="text-bold">Basic Information</h6>

                        <ul class="list-group list-group-nobordered mb-3">
                            <li class="list-group-item">
                                <span>Phone</span>
                                <p class="text-dark"><?= $employee['phone'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Email</span>
                                <p class="text-dark"><?= $employee['email'] ?? '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Gender</span>
                                <p class="text-dark"><?= $employee['gender'] ? ucfirst($employee['gender']) : '--' ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>Birthday</span>
                                <p class="text-dark"><?= $employee['birthdate'] ? date('F j, Y', strtotime($employee['birthdate'])) : '--' ?></p>
                            </li>

                            <!-- divider -->
                            <li class="list-group-divider"></li>

                            <li class="list-group-item">
                                <span>Present Address</span>
                                <p class="text-dark"><?= $employee['address_present'] ?? '--' ?></p>
                            </li>

                            <?php if ($employee['address_permanent']): ?>
                                <li class="list-group-item">
                                    <span>Permanent Address</span>
                                    <p class="text-dark"><?= $employee['address_permanent'] ?? '--' ?></p>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="card-body box-profile border-bottom p-x-10">
                        <?php if (check_function('manage_profile')) : ?>
                            <a href="javascript:;" class="btn btn-xs btn-icon float-right" id="employee-bank_details-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                        <?php endif; ?>

                        <h6 class="text-bold">Bank Details</h6>

                        <?php if (!empty($bank_details)): ?>
                            <ul class="nlist-group">

                                <?php if (!empty($bank_details['primary_bank']) && !empty($bank_details['primary_bank']['name'])): ?>
                                    <li class="list-group-item">
                                        <span>Primary Bank</span>
                                        <div class="text-right">
                                            <div>
                                                <span class="font-18"><?= $bank_details['primary_bank']['number'] ?></span>
                                            </div>
                                            <div>
                                                <strong class="d-block"><?= $bank_details['primary_bank']['name'] ?></strong>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($bank_details['secondary_bank']) && !empty($bank_details['secondary_bank']['name'])): ?>
                                    <li class="list-group-item">
                                        <span>Secondary Bank</span>
                                        <div class="text-right">
                                            <div class="text-right">
                                                <span><?= $bank_details['secondary_bank']['number'] ?></span>
                                            </div>
                                            <div>
                                                <strong class="d-block"><?= $bank_details['secondary_bank']['name'] ?></strong>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center p-5">
                                <span class="text-muted">No bank details available.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-body box-profile border-bottom">
                        <?php if (check_function('manage_profile')) : ?>
                            <a href="javascript:;" class="btn btn-xs btn-icon float-right" id="edit-personal_info-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                        <?php endif; ?>

                        <h6 class="text-bold">Personal Information</h6>

                        <ul class="list-group list-group-nobordered mb-3">
                            <li class="list-group-item">
                                <span>TIN</span>
                                <p class="text-dark"><?= $personal_info['tin'] ?? "---" ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>SSS</span>
                                <p class="text-dark"><?= $personal_info['sss'] ?? "---" ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>MID</span>
                                <p class="text-dark"><?= $personal_info['pag_ibig'] ?? "---" ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>PH</span>
                                <p class="text-dark"><?= $personal_info['phil_health'] ?? "---" ?></p>
                            </li>
                            <li class="list-group-item">
                                <span>HMO</span>
                                <p class="text-dark"><?= $personal_info['hmo_account'] ?? "---" ?></p>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline">
                    <div class="card-header border-0">
                        <h3 class="card-title">Emergency Contact Number</h3>

                        <?php if (check_function('manage_profile')) : ?>
                            <div class="card-tools">
                                <a href="javascript:;" class="btn btn-xs btn-icon" id="employee-emergency_contact-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="card-body p-t-0 p-x-10">
                        <?php if (!empty($emergency_contact)): ?>
                            <ul class="nlist-group">

                                <?php if (!empty($emergency_contact['primary_contact']) && !empty($emergency_contact['primary_contact']['name'])): ?>
                                    <li class="list-group-item">
                                        <span>Primary</span>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong class="d-block"><?= $emergency_contact['primary_contact']['name'] ?></strong>
                                                <span class="badge badge badge-soft-primary"><?= $emergency_contact['primary_contact']['relationship'] ?></span>
                                            </div>
                                            <div class="text-right">
                                                <span><?= $emergency_contact['primary_contact']['phone1'] ?></span>
                                                <?php if (!empty($emergency_contact['primary_contact']['phone2'])): ?>
                                                    <br>
                                                    <span><?= $emergency_contact['primary_contact']['phone2'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>


                                        <!-- Address -->
                                        <?php if (!empty($emergency_contact['primary_contact']['address'])): ?>
                                            <div class="text-muted mt-2">
                                                <small><i class="fa fa-map-marker-alt m-r-5"></i> <?= $emergency_contact['primary_contact']['address'] ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($emergency_contact['secondary_contact']) && !empty($emergency_contact['secondary_contact']['name'])): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span>Secondary</span>
                                                <strong class="d-block"><?= $emergency_contact['secondary_contact']['name'] ?></strong>
                                                <span class="badge badge badge-soft-primary"><?= $emergency_contact['secondary_contact']['relationship'] ?></span>
                                            </div>
                                            <div class="text-right">
                                                <span><?= $emergency_contact['secondary_contact']['phone1'] ?></span>
                                                <?php if (!empty($emergency_contact['secondary_contact']['phone2'])): ?>
                                                    <br>
                                                    <span><?= $emergency_contact['secondary_contact']['phone2'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <!-- Address -->
                                        <?php if (!empty($emergency_contact['secondary_contact']['address'])): ?>
                                            <div class="text-muted mt-2">
                                                <small><i class="fa fa-map-marker-alt m-r-5"></i> <?= $emergency_contact['secondary_contact']['address'] ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center p-5">
                                <span class="text-muted">No emergency contact information available.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-lg-12 col-xl-8">

                <div class="row equal">
                    <div class="col-lg-12 col-xl-6">
                        <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline">
                            <div class="card-header border-0">
                                <h3 class="card-title">Educational Background</h3>

                                <?php if (check_function('manage_profile')) : ?>
                                    <div class="card-tools">
                                        <a href="javascript:;" class="btn btn-xs btn-icon" id="employee-educ_background-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body pt-0">
                                <?php if (!empty($educ_background)): ?>
                                    <ul class="nlist-group">
                                        <?php foreach ($educ_background as $education): ?>
                                            <li class="nlist-group-item">
                                                <div>
                                                    <span class="d-block"><?= $education['institution_name'] ?></span>
                                                    <span class="badge badge-soft-info"><?= $education['course'] ?></span>
                                                </div>
                                                <div class="text-right">
                                                    <span><?= !empty($education['start_date']) ? date('M Y', strtotime($education['start_date'])) : '' ?> - <?= !empty($education['end_date']) ? date('M Y', strtotime($education['end_date'])) : '' ?></span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center p-5">
                                        <span class="text-muted">No educational background information available.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-xl-6">
                        <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline">
                            <div class="card-header border-0">
                                <h3 class="card-title">Employment History</h3>

                                <?php if (check_function('manage_profile')) : ?>
                                    <div class="card-tools">
                                        <a href="javascript:;" class="btn btn-xs btn-icon" id="employee-employment_history-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <div class="card-body p-t-0">
                                <?php if (!empty($employment_history)): ?>
                                    <ul class="nlist-group">
                                        <?php foreach ($employment_history as $employment): ?>
                                            <li class="nlist-group-item">
                                                <div>
                                                    <span class="d-block"><?= $employment['company_name'] ?></span>
                                                    <span class="badge badge-soft-info"><?= $employment['designation'] ?></span>
                                                </div>
                                                <div class="text-right">
                                                    <span><?= !empty($employment['start_date']) ? date('M Y', strtotime($employment['start_date'])) : '' ?> - <?= !empty($employment['end_date']) ? date('M Y', strtotime($employment['end_date'])) : '' ?></span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center p-5">
                                        <span class="text-muted">No educational background information available.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-xl-6">
                        <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline">
                            <div class="card-header border-0">
                                <h3 class="card-title">Documents</h3>

                                <div class="card-tools">

                                    <?php if (check_function('manage_profile')) : ?>
                                        <a href="javascript:;" class="btn btn-xs btn-icon" id="employee-employee_document-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"><i class="fa fa-edit"></i></a>
                                    <?php endif; ?>

                                    <a href="javascript:;" class="btn btn-xs btn-icon" data-card-widget="collapse" title="Collapse"> <i class="fa fa-minus"></i></a>
                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <?php if (!empty($documents)): ?>
                                    <ul class="nlist-group nlist-group-bordered">
                                        <?php foreach ($documents as $document): ?>
                                            <li class="nlist-group-item">
                                                <div>
                                                    <span class="d-block text-black"><?= $document['file_name'] ?></span>
                                                    <a href="<?= base_url($document['upload_path']) ?>" class="badge badge-soft-primary" target="_blank">
                                                        <i class="fa fa-file"></i> <?= $document['upload_name'] ?></a>
                                                </div>
                                                <div class=" text-right">
                                                    <a href="<?= base_url($document['upload_path']) ?>" class="btn btn-xs btn-primary m-t-5 d-block" target="_blank" download="<?= $document['upload_name'] ?>">
                                                        <i class="fa fa-download"></i> Download </a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger m-t-5 remove-document-btn" data-docid="<?= $this->mysecurity->encrypt_url($document['id']) ?>"><i class="fa fa-trash"></i> Remove File</a>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center p-5">
                                        <span class="text-muted">No documents uploaded yet.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                    <?php if (check_function('requirements_checklist')) : ?>
                        <div class="col-lg-12 col-xl-6">
                            <div class="card <?= ($employee['status'] == 1) ? "card-danger" : "card-primary" ?> card-outline collapsed-card">
                                <div class="card-header border-0">
                                    <h3 class="card-title">Requirements Checklist <span id="requirements-checklist-count"></span></h3>

                                    <div class="card-tools">
                                        <a href="javascript:;" class="btn btn-xs btn-icon" data-card-widget="collapse" title="Collapse"> <i class="fa fa-plus"></i></a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <?php
                                        $sections = $this->common->requirements_checklist;
                                        ?>

                                        <tbody>
                                            <?php foreach ($sections as $section => $items):
                                            ?>
                                                <tr>
                                                    <th colspan="2" class="bg--gray-200"><?= htmlspecialchars($section) ?></th>
                                                </tr>
                                                <?php foreach ($items as $item):
                                                    $item_text = preg_replace('/\s*\(.*?\)\s*/', '', $item);

                                                    // check for record with the same 
                                                    // $requirement_record = $employee_requirements["{$section} - {$item}"] ?? [];
                                                    if (!empty($employee_requirements)) {
                                                        $search = "{$section} - {$item_text}";
                                                        $requirement_key = array_search($search, array_column($employee_requirements, 'file_name'));
                                                        $requirement_record = ($requirement_key !== false) ? $employee_requirements[$requirement_key] : null;
                                                    } else {
                                                        $requirement_record = null;
                                                    }
                                                ?>
                                                    <tr data-section="<?= htmlspecialchars($section) ?>" data-item="<?= htmlspecialchars($item_text) ?>">
                                                        <td><?= htmlspecialchars($item) ?></td>
                                                        <td class="text-center">
                                                            <!-- <span class="badge badge-soft-danger">Pending</span> -->

                                                            <!-- Upload -->
                                                            <?php if (!$requirement_record): ?>
                                                                <a href="javascript:;" class="btn btn-xs bg--danger-soft employee-employee_requirements-btn" data-empid="<?= $this->mysecurity->encrypt_url($employee['id']) ?>"> Pending </a>
                                                            <?php else: ?>
                                                                <!-- View File -->
                                                                <a href="<?= base_url($requirement_record['upload_path']) ?>" class="btn btn-xs bg--success-soft" target="_blank"> <i class="fa fa-file"></i> View </a>

                                                                <!-- Download File -->
                                                                <a href="<?= base_url($requirement_record['upload_path']) ?>" class="btn btn-xs btn-primary" target="_blank" download="<?= $requirement_record['upload_name'] ?>">
                                                                    <i class="fa fa-download"></i> Download </a>

                                                                <!-- Remove File -->
                                                                <a href="javascript:;" class="btn btn-xs btn-danger remove-document-btn" data-docid="<?= $this->mysecurity->encrypt_url($requirement_record['id']) ?>" data-doctype="<?= $this->mysecurity->encrypt_url('employee_requirements') ?>"> <i class="fa fa-trash"></i> Remove </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php if (check_function('manage_profile')) : ?>
    <!-- Employee Personal Info -->
    <?php $this->load->view('employee-personal_info'); ?>

    <!-- Employee Basic Info -->
    <?php $this->load->view('employee-basic_info'); ?>

    <!-- Employee Basic Info -->
    <?php $this->load->view('employee-bank_details'); ?>

    <!-- Employee Basic Info -->
    <?php $this->load->view('employee-emergency_contact'); ?>

    <!-- employee-employment_history -->
    <?php $this->load->view('employee-employment_history'); ?>

    <!-- educ_background -->
    <?php $this->load->view('employee-educ_background'); ?>

    <!-- employee-upload-document -->
    <?php $this->load->view('employee-upload-document'); ?>

    <!-- Employee Details -->
    <?php $this->load->view('employee_details'); ?>

    <!-- Salary Increase -->
    <?php $this->load->view('employee-salary_increase'); ?>
<?php endif; ?>

<?php if (check_function('requirements_checklist')) : ?>
    <!-- employee-upload-requirements -->
    <?php $this->load->view('employee-upload-requirements'); ?>
<?php endif; ?>

<script>
    $(document).ready(function() {


        <?php if (check_function('requirements_checklist')) : ?>
            // Count and populate requirements checklist
            function updateRequirementsCount() {
                let totalRequirements = 0;
                let completedRequirements = 0;

                // Count all requirement rows (excluding header rows)
                $('#requirements-checklist-count').closest('.card').find('tbody tr[data-section]').each(function() {
                    totalRequirements++;
                    // Check if this requirement has been completed (has view/download buttons instead of pending)
                    if ($(this).find('.bg--success-soft').length > 0) {
                        completedRequirements++;
                    }
                });

                // Update the count display
                let countText = '[ <strong>' + completedRequirements + '</strong>/' + totalRequirements + ' ]';
                $('#requirements-checklist-count').html(countText);

                // Optionally add color coding
                if (completedRequirements === totalRequirements) {
                    $('#requirements-checklist-count').addClass('text-success');
                } else if (completedRequirements === 0) {
                    $('#requirements-checklist-count').addClass('text-danger');
                } else {
                    $('#requirements-checklist-count').addClass('text-orange');
                }
            }

            // Initialize count on page load
            updateRequirementsCount();
        <?php endif; ?>

        $(document).on('click', '#employee-salary_details-btn', function() {
            let empId = $(this).data('empid');

            // show jquery alert to input password
            $.confirm({
                title: 'Enter Password to View Salary.',
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<input type="password" placeholder="Your password" class="name form-control" required />' +
                    '</div>' +
                    '</form>',
                buttons: {
                    confirm: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function() {
                            let password = this.$content.find('.name').val();
                            if (!password) {
                                $.alert('Please enter your password');
                                return false;
                            }
                            // Verify password via AJAX
                            $.ajax({
                                url: base_url + 'employee/verify_password',
                                method: 'POST',
                                data: {
                                    password: password
                                },
                                dataType: 'json'
                            }).done(function(response) {
                                if (response.status === 'success') {
                                    // show alert with salary details for 10 seconds
                                    let salaryAlert = $.alert({
                                        title: 'Salary Details',
                                        content: 'Salary: <strong>' + response.salary + '</strong>',
                                        buttons: {
                                            ok: function() {}
                                        },
                                        onClose: function() {
                                            // Clear the content after closing
                                            this.setContent('');
                                        }
                                    });

                                    // Auto close after 10 seconds
                                    setTimeout(function() {
                                        salaryAlert.close();
                                    }, 10000);
                                } else {
                                    $.alert('Incorrect password');
                                }
                            }).fail(function() {
                                $.alert('Request failed');
                            });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-red',
                        action: function() {
                            // Close the dialog
                        }
                    }
                }
            });
        });
    });
</script>

<?php if (check_function('deactivate_employee')) : ?>
    <script>
        $(document).ready(function() {
            $(document).on('click', '#employee-deactivate-btn', function() {
                let empId = $(this).data('empid');

                $.confirm({
                    title: 'Employee Deactivation',
                    content: '' +
                        '<form action="" class="deactivationForm">' +
                        '<div class="form-group">' +
                        '<label>Deactivation Status:</label>' +
                        '<select class="form-control deactivation-status" required>' +
                        '<option value="">Select Status</option>' +
                        '<option value="Cleared">Cleared</option>' +
                        '<option value="AWOL">AWOL</option>' +
                        '<option value="Terminated">Terminated</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Eligible for Rehire:</label>' +
                        '<select class="form-control eligible-rehire">' +
                        '<option value="">Select Option</option>' +
                        '<option value="Yes">Yes</option>' +
                        '<option value="No">No</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Remarks (Optional):</label>' +
                        '<textarea class="form-control deactivation-remarks" rows="3" placeholder="Enter any additional remarks..."></textarea>' +
                        '</div>' +
                        '</form>',
                    buttons: {
                        confirm: {
                            text: 'Deactivate Employee',
                            btnClass: 'btn-danger',
                            action: function() {
                                let status = this.$content.find('.deactivation-status').val();
                                let eligibleForRehire = this.$content.find('.eligible-rehire').val();
                                let remarks = this.$content.find('.deactivation-remarks').val();

                                if (!status) {
                                    $.alert('Please select a deactivation status');
                                    return false;
                                }

                                if (!eligibleForRehire) {
                                    $.alert('Please select an eligibility for rehire status');
                                    return false;
                                }

                                // Send AJAX request to deactivate employee
                                $.ajax({
                                    url: base_url + 'employee/deactivate_employee',
                                    method: 'POST',
                                    data: {
                                        emp_id: empId,
                                        deactivation_status: status,
                                        eligible_for_rehire: eligibleForRehire,
                                        remarks: remarks
                                    },
                                    dataType: 'json'
                                }).done(function(response) {
                                    if (response.status === 'success') {
                                        $.confirm({
                                            title: 'Success',
                                            content: 'Employee has been deactivated with status: ' + status,
                                            type: 'green',
                                            buttons: {
                                                ok: {
                                                    text: 'OK',
                                                    btnClass: 'btn-success',
                                                    action: function() {
                                                        location.reload();
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $.confirm({
                                            title: 'Error',
                                            content: 'Failed to deactivate employee. Please try again.',
                                            type: 'red',
                                            buttons: {
                                                ok: {
                                                    text: 'OK',
                                                    btnClass: 'btn-danger',
                                                    action: function() {}
                                                }
                                            }
                                        });
                                    }
                                }).fail(function() {
                                    $.confirm({
                                        title: 'Error',
                                        content: 'Request failed. Please try again.',
                                        type: 'red',
                                        buttons: {
                                            ok: {
                                                text: 'OK',
                                                btnClass: 'btn-danger',
                                                action: function() {}
                                            }
                                        }
                                    });
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-secondary',
                            action: function() {
                                // Close the dialog
                            }
                        }
                    }
                });
            });

            $(document).on('click', '#employee-reactivate-btn', function() {
                let empId = $(this).data('empid');

                $.confirm({
                    title: 'Reactivate Employee',
                    content: 'Are you sure you want to reactivate this employee?',
                    buttons: {
                        confirm: {
                            text: 'Yes, Reactivate',
                            btnClass: 'btn-success',
                            action: function() {
                                // Send AJAX request to reactivate employee
                                $.ajax({
                                    url: base_url + 'employee/reactivate_employee',
                                    method: 'POST',
                                    data: {
                                        emp_id: empId
                                    },
                                    dataType: 'json'
                                }).done(function(response) {
                                    if (response.status === 'success') {
                                        $.confirm({
                                            title: 'Success',
                                            content: 'Employee has been reactivated successfully.',
                                            type: 'green',
                                            buttons: {
                                                ok: {
                                                    text: 'OK',
                                                    btnClass: 'btn-success',
                                                    action: function() {
                                                        location.reload();
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $.confirm({
                                            title: 'Error',
                                            content: 'Failed to reactivate employee. Please try again.',
                                            type: 'red',
                                            buttons: {
                                                ok: {
                                                    text: 'OK',
                                                    btnClass: 'btn-danger',
                                                    action: function() {}
                                                }
                                            }
                                        });
                                    }
                                }).fail(function() {
                                    $.confirm({
                                        title: 'Error',
                                        content: 'Request failed. Please try again.',
                                        type: 'red',
                                        buttons: {
                                            ok: {
                                                text: 'OK',
                                                btnClass: 'btn-danger',
                                                action: function() {}
                                            }
                                        }
                                    });
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-secondary',
                            action: function() {
                                // Close the dialog
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php endif; ?>