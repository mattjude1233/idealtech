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
                    <li class="breadcrumb-item active">Disciplinary</li>
                </ol>
            </div>

            <?php if (check_function('manage_disciplinary')) : ?>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="javascript:;" class="btn btn-pill btn-primary btn-md text-white add_disciplinary-btn"> <i class="fa fa-user-plus"></i> Add Disciplinary</a>
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
                <!-- <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="search_status" id="Search_Status" class="form-control" required>
                                        <option value="all">All Status</option>
                                        < ?= admin__lang_select('disciplinary', 'status') ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-12 col-6">
                                <input type="text" name="search_leave_from" placeholder="From" class="form-control datepicker">
                            </div>

                            <div class="col-md-3 col-sm-12 col-6">
                                <input type="text" name="search_leave_to" placeholder="To" class="form-control datepicker">
                            </div>

                            <div class="col-md-3 col-sm-12 col-6">
                                <button class="btn btn-success btn-block">Search</button>
                            </div>
                        </div>

                    </div>
                </div> -->

                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="disciplinaryTable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Date of Incident</th>
                                        <th>Violation</th>
                                        <th>Level of Offense</th>
                                        <th>NTE Deadline</th>
                                        <th>Sanction</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($list)) : ?>
                                        <?php foreach ($list as $disciplinary) : ?>
                                            <tr data-did="<?= $this->mysecurity->encrypt_url($disciplinary['id']); ?>">
                                                <td><?= $disciplinary['employee_name'] ?></td>
                                                <td data-sort="<?= strtotime($disciplinary['date_of_incident']) ?>"><?= date('F d, Y', strtotime($disciplinary['date_of_incident'])) ?></td>
                                                <td><?= $disciplinary['violation'] ?></td>
                                                <td><?= $disciplinary['offense_level'] ?></td>

                                                <td data-sort="<?= ($disciplinary['nte_deadline']) ? strtotime($disciplinary['nte_deadline']) : '' ?>"><?= !empty($disciplinary['nte_deadline']) ? date('F d, Y', strtotime($disciplinary['nte_deadline'])) : '' ?></td>

                                                <td><?= $disciplinary['offense_sanction'] ?></td>
                                                <td><?= admin__lang('disciplinary', 'status', $disciplinary['status']) ?></td>


                                                <td>
                                                    <a href="javascript:;" class="btn btn-xs btn-primary notice_to_explain-btn" data-toggle="tooltip" title="Print Disciplinary"> <i class="fa fa-print"></i> </a>

                                                    <a href="javascript:;" class="btn btn-xs btn-warning update_disciplinary-btn m-r-5" data-toggle="tooltip" title="Update Disciplinary"> <i class="fa fa-edit"></i></a>

                                                    <?php if (check_function('manage_disciplinary')) : ?>
                                                        <a href="javascript:;" class="btn btn-xs btn-danger cancel_disciplinary-btn" data-toggle="tooltip" title="Cancel Disciplinary"> <i class="fa fa-times"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No disciplinary records found.</td>
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

<div class="modal fade" id="notice_to_explain-modal" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Notice to Explain Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <iframe id="print_docs-iframe" src="" height="700px" width="100%" style="overflow:hidden;"></iframe>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="javascript:;" class="btn btn-success print_docs-btn">Download</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', '.notice_to_explain-btn', function(e) {
            e.preventDefault();

            const $row = $(this).closest('tr');
            const disciplinaryId = $row.data('did');

            // reset the iframe source
            $('#print_docs-iframe').attr('src', '');
            $('#print_docs-iframe').attr('src', '<?= base_url('disciplinary/disciplinary_pdf/') ?>' + disciplinaryId);
            $('#notice_to_explain-modal').modal('show');

            $('.print_docs-btn').off('click').on('click', function() {
                const iframe = document.getElementById('print_docs-iframe');
                iframe.contentWindow.print();
            });
        });

        $(document).on('click', '.print_docs-btn', function() {
            var iframeWindow = $('#print_docs-iframe')[0].contentWindow;
            iframeWindow.print();
        })
    });
</script>

<div class="modal fade" id="add_disciplinary-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Disciplinary</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="add_disciplinary-form">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Employee">Employee <span class="text-danger">*</span></label>
                                <select id="Employee" class="form-control select2" required <?= check_function('manage_disciplinary') ? 'name="Employee"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?php if (!empty($employees)) : ?>
                                        <?php foreach ($employees as $employee) : ?>
                                            <option value="<?= $employee['id'] ?>"><?= $employee['emp_fname'] . ' ' . $employee['emp_lname'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Date_Of_Incident">Date of Incident <span class="text-danger">*</span></label>
                                <input type="text" id="Date_Of_Incident" class="form-control datepicker" required <?= check_function('manage_disciplinary') ? 'name="Date_Of_Incident"' : 'disabled readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="Violations">Violations <span class="text-danger">*</span></label>
                                <select id="Violations" class="form-control select2-violation" required <?= check_function('manage_disciplinary') ? 'name="Violations"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?php if (!empty($violations)) : ?>
                                        <?php foreach ($violations as $violation) : ?>
                                            <option><?= $violation ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="Details_of_Violations">Details of Violations</label>
                                <textarea id="Details_of_Violations" class="form-control ckeditor-textarea" <?= check_function('manage_disciplinary') ? 'name="Details_of_Violations"' : 'disabled readonly' ?>></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NTE_Date">NTE Date</label>
                                <input type="text" id="NTE_Date" class="form-control datepicker" <?= check_function('manage_disciplinary') ? 'name="NTE_Date"' : 'disabled readonly' ?>>
                            </div>
                        </div>

                        <div class="col-6">

                            <div class="form-group">
                                <label for="NTE_Deadline">NTE Deadline</label>
                                <input type="text" id="NTE_Deadline" class="form-control datepicker" <?= check_function('manage_disciplinary') ? 'name="NTE_Deadline"' : 'disabled readonly' ?>>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="NTE_Reply_Date">NTE Reply Date</label>
                                <input type="text" id="NTE_Reply_Date" class="form-control datepicker" <?= check_function('manage_disciplinary') ? 'name="NTE_Reply_Date"' : 'disabled readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="Employee_Explanation">Employee Explanation</label>
                                <textarea name="Employee_Explanation" id="Employee_Explanation" class="form-control ckeditor-textarea"></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="Employee_Action_Plan">Employee Action Plan</label>
                                <textarea name="Employee_Action_Plan" id="Employee_Action_Plan" class="form-control ckeditor-textarea"></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="Notice_of_Decision">Notice of Decision</label>
                                <textarea id="Notice_of_Decision" class="form-control ckeditor-textarea" <?= check_function('manage_disciplinary') ? 'name="Notice_of_Decision"' : 'disabled readonly' ?>></textarea>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="Offense">Offense</label>
                                <select id="Offense" class="form-control select2-offense" <?= check_function('manage_disciplinary') ? 'name="Offense"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?php if (!empty($level_of_offense)) : ?>
                                        <?php foreach ($level_of_offense as $offense) : ?>
                                            <option><?= $offense ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="Level_of_Offense">Level of Offense</label>
                                <select id="Level_of_Offense" class="form-control select2-level_of_offense" <?= check_function('manage_disciplinary') ? 'name="Level_of_Offense"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?php if (!empty($level_of_offense)) : ?>
                                        <?php foreach ($level_of_offense as $offense) : ?>
                                            <option><?= $offense ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="Sanction">Sanction</label>
                                <select id="Sanction" class="form-control select2-sanction" <?= check_function('manage_disciplinary') ? 'name="Sanction"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?php if (!empty($sanction)) : ?>
                                        <?php foreach ($sanction as $offense) : ?>
                                            <option><?= $offense ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">

                                <?php if (check_function('manage_disciplinary')): ?>
                                    <a href="javascript:;" class="btn btn-xs btn-info float-right" id="suspension_opencalendar-btn">
                                        <i class="fas fa-calendar"></i> Select Dates
                                    </a>
                                <?php endif; ?>

                                <label for="Suspension">Suspension </label>
                                <input type="text" id="suspension_datepicker" style="position:absolute; opacity:0; z-index:-1;" />
                                <select id="Suspension" class="form-control select2-suspension" multiple <?= check_function('manage_disciplinary') ? 'name="Suspension[]"' : 'disabled readonly' ?>></select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="Status">Status <span class="text-danger">*</span></label>
                                <select id="Status" class="form-control" required <?= check_function('manage_disciplinary') ? 'name="Status"' : 'disabled readonly' ?>>
                                    <option value=""></option>
                                    <?= admin__lang_select('disciplinary', 'status') ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-6">
                            <div class="form-group">
                                <label for="Attachments">Attachments</label>
                                <ul class="nlist-group nlist-group-bordered" id="Attachments_Container">
                                </ul>

                                <input type="hidden" name="removed_attachments" id="removed_attachments">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="New_Attachments">New Attachments</label>
                                <input type="file" name="New_Attachments[]" id="New_Attachments" class="form-control-file" multiple>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    $(document).ready(function() {

        // on change NTE_Date add 5 days to NTE_Deadline
        $('#NTE_Date').on('change', function() {
            var nteDate = $(this).val();
            if (nteDate) {
                var date = new Date(nteDate);
                date.setDate(date.getDate() + 5);
                $('#NTE_Deadline').datepicker('setDate', date);
            } else {
                $('#NTE_Deadline').val('');
            }
        });

        $(document).on('click', '.add_disciplinary-btn', function() {
            $('#add_disciplinary-form')[0].reset();
            $('#add_disciplinary-form select[class*=select2-]').val(null).trigger('change');
            $('#Attachments_Container').empty();

            $('.ckeditor-textarea').each(function() {
                CKEDITOR.instances[$(this).attr('id')].setData('');
            });

            $('#add_disciplinary-modal .modal-title').text('Add Disciplinary');
            $('#add_disciplinary-modal').modal('show');

            $('#add_disciplinary-form').off('submit').on('submit', function(e) {
                e.preventDefault();

                const form = $('#add_disciplinary-form')[0];
                const formData = new FormData(form);

                // If using CKEditor fields, manually append their data
                $('.ckeditor-textarea').each(function() {
                    formData.set($(this).attr('name'), CKEDITOR.instances[$(this).attr('id')].getData());
                });

                $.ajax({
                    url: '<?= base_url('disciplinary/save') ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#add_disciplinary-form button[type="submit"]').prop('disabled', true);
                        page_loader_show();
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            reloadDisciplinaryTable();
                            $('#add_disciplinary-modal').modal('hide');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('An error occurred while adding the disciplinary.');
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#add_disciplinary-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        });

        $(document).on('click', '.update_disciplinary-btn', function(e) {
            e.preventDefault();

            const $row = $(this).closest('tr');
            const disciplinaryId = $row.data('did');
            const $form = $('#add_disciplinary-form')[0];

            $form.reset();
            $('#add_disciplinary-form select[class*=select2-]').val(null).trigger('change');
            $('#Attachments_Container').empty();

            $.ajax({
                url: '<?= base_url('disciplinary/getdisciplinary') ?>',
                type: 'POST',
                data: {
                    disciplinaryid: disciplinaryId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#add_disciplinary-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;

                        setSelect2Value('#Employee', data.employee_id);
                        $('#Date_Of_Incident').datepicker('setDate', data.date_of_incident ? dateFormat(data.date_of_incident) : '');
                        setSelect2Value('#Violations', data.violation);
                        $('#Details_of_Violations').val(data.violation_details);

                        $('#NTE_Date').datepicker('setDate', data.nte_date ? dateFormat(data.nte_date) : '');
                        $('#NTE_Deadline').datepicker('setDate', data.nte_deadline ? dateFormat(data.nte_deadline) : '');
                        $('#NTE_Reply_Date').datepicker('setDate', data.nte_reply_date ? dateFormat(data.nte_reply_date) : '');

                        $('#Employee_Explanation').val(data.employee_explanation);
                        $('#Notice_of_Decision').val(data.notice_of_decision);
                        $('#Employee_Action_Plan').val(data.employee_action_plan);

                        setSelect2Value('#Offense', data.offense);
                        setSelect2Value('#Level_of_Offense', data.offense_level);
                        setSelect2Value('#Sanction', data.offense_sanction);

                        if (data.suspension_dates) {
                            $('#Suspension').empty();
                            if (Array.isArray(data.suspension_dates)) {
                                $('#Suspension').empty();
                                data.suspension_dates.forEach(date => date && setSelect2Value('#Suspension', date));
                            }
                        }

                        // Render old attachments
                        $('#Attachments_Container').empty();
                        let removedFiles = [];

                        if (data.attachments && data.attachments.length > 0) {
                            data.attachments.forEach((file, index) => {
                                const id = btoa(unescape(encodeURIComponent(file.file_path + '||' + file.file_name)));

                                const html = `
                                    <li class="nlist-group-item" data-fileid="${id}">
                                        <div>
                                            <a href="<?= base_url() ?>${file.file_path}" download="${file.file_name}" class="badge badge-soft-primary" target="_blank">
                                                <i class="fa fa-file"></i> ${file.file_name}</a>
                                        </div>
                                        <div class="text-right">
                                            <a href="<?= base_url() ?>${file.file_path}" target="_blank" download="${file.file_name}" class="btn btn-xs btn-primary m-t-5">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a href="javascript:;" class="btn btn-xs btn-danger m-t-5 remove-old-attachment" data-id="${id}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </li>`;
                                $('#Attachments_Container').append(html);
                            });
                        }

                        $(document).on('click', '.remove-old-attachment', function() {
                            const id = $(this).data('id');
                            removedFiles.push(id);
                            $(this).closest('li').remove();

                            console.log(removedFiles);
                        });

                        setSelect2Value('#Status', data.status);

                        $('.ckeditor-textarea').each(function() {
                            const id = $(this).attr('id');
                            if (CKEDITOR.instances[id]) {
                                CKEDITOR.instances[id].setData($(this).val());
                            }
                        });

                        $('#add_disciplinary-modal .modal-title').text('Update Disciplinary');
                        $('#add_disciplinary-modal').modal('show');

                        $('#add_disciplinary-form').off('submit').on('submit', function(e) {
                            e.preventDefault();

                            const form = this;
                            const formData = new FormData(form);
                            formData.append('disciplinary_id', data.id);

                            // append CKEditor data
                            $('.ckeditor-textarea').each(function() {
                                formData.set($(this).attr('name'), CKEDITOR.instances[$(this).attr('id')].getData());
                            });

                            if (removedFiles.length > 0) {
                                formData.append('removed_attachments', JSON.stringify(removedFiles));
                            }

                            $.ajax({
                                url: '<?= base_url('disciplinary/save') ?>',
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                                dataType: 'json',
                                beforeSend: function() {
                                    page_loader_show();
                                    $('#add_disciplinary-form button[type="submit"]').prop('disabled', true);
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        reloadDisciplinaryTable();
                                        $('#add_disciplinary-modal').modal('hide');
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                    alert('An error occurred while updating the disciplinary.');
                                },
                                complete: function() {
                                    page_loader_hide();
                                    $('#add_disciplinary-form button[type="submit"]').prop('disabled', false);
                                }
                            });
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while fetching the disciplinary details.');
                },
                complete: function() {
                    page_loader_hide();
                    $('#add_disciplinary-form button[type="submit"]').prop('disabled', false);
                }
            });
        });


        $(document).on('click', '.cancel_disciplinary-btn', function(e) {
            e.preventDefault();

            const $row = $(this).closest('tr');
            const disciplinaryId = $row.data('did');

            $.confirm({
                title: 'Cancel Disciplinary Action',
                content: 'Are you sure you want to cancel this disciplinary action?',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('disciplinary/cancel') ?>',
                                beforeSend: function() {
                                    page_loader_show();
                                },
                                data: {
                                    disciplinaryid: disciplinaryId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        reloadDisciplinaryTable();

                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            animateFromElement: false // prevents animation based on trigger element
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
                                    console.error(error);
                                    $.alert({
                                        title: 'Error!',
                                        content: 'An error occurred while canceling the disciplinary action.',
                                        type: 'red'
                                    });
                                },
                                complete: function() {
                                    page_loader_hide();
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

<script>
    $(document).ready(function() {
        $(".select2").select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select Employee",
            dropdownParent: $(".select2").parent(),
            allowClear: true,
        });

        $(".select2-status").select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select Status",
            dropdownParent: $(".select2").parent(),
            allowClear: true,
        });

        $('.select2-violation').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select or type Violation",
            tags: true,
            tokenSeparators: [],
            dropdownParent: $('.select2-violation').parent(),
            allowClear: true,
            ajax: {
                url: '<?= base_url('disciplinary/search_violations') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.violation,
                                text: item.violation
                            };
                        }),
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true // add additional parameters
                };
            }
        });

        $('.select2-offense').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select or type Offense",
            tags: true,
            tokenSeparators: [],
            dropdownParent: $('.select2-offense').parent(),
            allowClear: true,
            ajax: {
                url: '<?= base_url('disciplinary/search_offenses') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.offense,
                                text: item.offense
                            };
                        }),
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true // add additional parameters
                };
            }
        });

        $('.select2-level_of_offense').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select or type Level of Offense",
            tags: true,
            tokenSeparators: [],
            dropdownParent: $('.select2-level_of_offense').parent(),
            allowClear: true,
            ajax: {
                url: '<?= base_url('disciplinary/search_level_of_offense') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.offense_level,
                                text: item.offense_level
                            };
                        }),
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true // add additional parameters
                };
            }
        });

        $('.select2-sanction').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select or type Sanction",
            tags: true,
            tokenSeparators: [],
            dropdownParent: $('.select2-sanction').parent(),
            allowClear: true,
            ajax: {
                url: '<?= base_url('disciplinary/search_sanctions') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.sanction,
                                text: item.sanction
                            };
                        }),
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true // add additional parameters
                };
            }
        });

        const $suspension = $('.select2-suspension').select2({
            tags: false,
            placeholder: 'Add dates',
            tokenSeparators: []
        });

        $('#suspension_datepicker').datepicker({
            autoclose: true,
            format: 'M dd, yyyy',
            todayHighlight: true,
        }).on('changeDate', function(e) {
            const selectedDate = $(this).datepicker('getFormattedDate').trim();
            const $suspension = $('#Suspension');
            const currentValues = $suspension.val() || [];

            if (!selectedDate) return;
            const optionExists = $suspension.find(`option[value="${selectedDate}"]`).length > 0;
            const index = currentValues.indexOf(selectedDate);

            if (index === -1) {
                // Not selected
                if (!optionExists) {
                    // Add and select
                    const newOption = new Option(selectedDate, selectedDate, true, true);
                    $suspension.append(newOption).trigger('change');
                } else {
                    // Just select existing option
                    currentValues.push(selectedDate);
                    $suspension.val(currentValues).trigger('change');
                }
            } else {
                // Already selected, unselect
                currentValues.splice(index, 1);
                $suspension.val(currentValues).trigger('change');
            }

            $(this).datepicker('hideWidget');
        });

        // open calendar when icon clicked
        $('#suspension_opencalendar-btn').on('click', function(e) {
            e.preventDefault();
            $('#suspension_datepicker').datepicker('show');
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: 'M dd, yyyy',
            todayHighlight: true,
        });

        // All CKEditor instances
        CKEDITOR.replaceAll('ckeditor-textarea', {
            height: 200,
            skin: 'bootstrapck',
            toolbar: [
                []
            ]
        });
    });
    <?php if (!empty($list)) : ?>

        function reloadDisciplinaryTable() {
            const $table = $('#disciplinaryTable');

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            $("#disciplinaryTable tbody").load(location.href + " #disciplinaryTable tbody>*", function() {
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
            reloadDisciplinaryTable();
        })

    <?php endif; ?>
</script>