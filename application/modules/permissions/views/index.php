<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">System</li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </div>

            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="javascript:;" class="btn btn-pill btn-success btn-md text-white add_permission-btn">
                        <i class="fa fa-plus"></i> Add Permission
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .permission-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .bg--danger-soft {
        background-color: #f8d7da;
        color: #721c24;
    }

    .grouping-badge {
        background-color: #e9ecef;
        color: #495057;
        font-weight: 500;
    }

    .level-badge {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .level-badge.mr-1 {
        margin-right: 0.25rem !important;
    }

    .level-badge.mb-1 {
        margin-bottom: 0.25rem !important;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        background-color: #f8f9fa;
        border-top: none;
    }

    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .icon-preview {
        width: 20px;
        text-align: center;
    }

    /* Select2 custom styling */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        border-radius: 0.25rem;
        padding: 2px 8px;
        margin: 2px;
    }
</style>

<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Permissions</h3>
                        <div class="card-tools">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                Manage system permissions and user access levels
                                <br>
                                <strong>Total Permissions:</strong> <?= count($permissions) ?>
                                | <strong>Active:</strong> <?= count(array_filter($permissions, function ($p) {
                                                                return $p['status'] == 1;
                                                            })) ?>
                                | <strong>Pages:</strong> <?= count(array_filter($permissions, function ($p) {
                                                                return $p['type'] == 1;
                                                            })) ?>
                                | <strong>Functions:</strong> <?= count(array_filter($permissions, function ($p) {
                                                                    return $p['type'] == 2;
                                                                })) ?>
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="permissionsTable">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Keyword</th>
                                        <th width="15%">Name</th>
                                        <th width="12%">Link</th>
                                        <th width="8%">Group</th>
                                        <th width="10%">Level</th>
                                        <th width="8%">Type</th>
                                        <th width="5%">Position</th>
                                        <th width="5%">Icon</th>
                                        <th width="8%">Status</th>
                                        <th width="9%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($permissions)) : ?>
                                        <?php foreach ($permissions as $permission) : ?>
                                            <tr data-permission-id="<?= $permission['id'] ?>">
                                                <td><?= $permission['id'] ?></td>
                                                <td>
                                                    <code><?= htmlspecialchars($permission['keyword']) ?></code>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($permission['name']) ?></strong>
                                                    <?php if (!empty($permission['special_user'])) : ?>
                                                        <br><small class="text-info">
                                                            <i class="fa fa-user"></i> Special: <?= htmlspecialchars($permission['special_user']) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($permission['exclude_user'])) : ?>
                                                        <br><small class="text-warning">
                                                            <i class="fa fa-user-times"></i> Exclude: <?= htmlspecialchars($permission['exclude_user']) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($permission['link'])) : ?>
                                                        <span class="badge badge-primary"><?= htmlspecialchars($permission['link']) ?></span>
                                                    <?php else : ?>
                                                        <span class="text-muted">---</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge grouping-badge"><?= $permission['grouping'] ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $levels = explode(',', $permission['level']);
                                                    foreach ($levels as $level) :
                                                        $level = trim($level);
                                                        if (empty($level)) continue;

                                                        $level_class = 'secondary';
                                                        $display_name = ucfirst(str_replace('_', ' ', $level));

                                                        // Map levels to appropriate colors and names
                                                        if ($level === 'admin') {
                                                            $level_class = 'danger';
                                                            $display_name = 'System Admin';
                                                        } elseif ($level === 'all') {
                                                            $level_class = 'success';
                                                            $display_name = 'All Users';
                                                        } elseif ($level === 'hr_admin') {
                                                            $level_class = 'warning';
                                                            $display_name = 'HR Admin';
                                                        } elseif ($level === 'employee') {
                                                            $level_class = 'info';
                                                            $display_name = 'Employee';
                                                        } else {
                                                            $level_class = 'info';
                                                            // Look up the display name from user_levels if available
                                                            if (!empty($user_levels)) {
                                                                foreach ($user_levels as $ul) {
                                                                    if ($ul['level_key'] === $level) {
                                                                        $display_name = $ul['level_name'];
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                        <span class="badge badge-<?= $level_class ?> level-badge mr-1 mb-1"
                                                            title="Access Level: <?= htmlspecialchars($display_name) ?>">
                                                            <?= htmlspecialchars($display_name) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td>
                                                    <span class="badge permission-type-badge <?= $permission['type'] == 1 ? 'badge-info' : 'badge-warning' ?>">
                                                        <?= $permission['type'] == 1 ? 'Page' : 'Function' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge grouping-badge"><?= $permission['position'] ?></span>
                                                </td>
                                                <td class="icon-preview">
                                                    <?php if (!empty($permission['icon'])) : ?>
                                                        <i class="<?= htmlspecialchars($permission['icon']) ?>" title="<?= htmlspecialchars($permission['icon']) ?>"></i>
                                                    <?php else : ?>
                                                        <span class="text-muted">---</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm toggle-status-btn <?= $permission['status'] == 1 ? 'btn-success' : 'btn-secondary' ?>"
                                                        title="Click to toggle status">
                                                        <?= $permission['status'] == 1 ? '<i class="fa fa-check"></i> Active' : '<i class="fa fa-times"></i> Inactive' ?>
                                                    </button>
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="javascript:;" class="btn btn-xs btn-warning edit_permission-btn" title="Edit Permission">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger delete_permission-btn" title="Delete Permission">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="11" class="text-center">No permissions found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Permission Modal -->
<div class="modal fade" id="permission-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Permission</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="permission-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="keyword">Keyword <span class="text-danger">*</span></label>
                                <input type="text" name="keyword" id="keyword" class="form-control" required
                                    placeholder="e.g., tab_dashboard or manage_users">
                                <small class="form-text text-muted">Unique identifier for the permission</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required
                                    placeholder="e.g., Dashboard or Manage Users">
                                <small class="form-text text-muted">Display name for the permission</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="link">Link/Route</label>
                                <input type="text" name="link" id="link" class="form-control"
                                    placeholder="e.g., dashboard or admin/users">
                                <small class="form-text text-muted">URL path (leave empty for functions)</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon">Icon Class</label>
                                <input type="text" name="icon" id="icon" class="form-control"
                                    placeholder="e.g., fas fa-dashboard">
                                <small class="form-text text-muted">FontAwesome icon class</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grouping">Grouping <span class="text-danger">*</span></label>
                                <input type="number" name="grouping" id="grouping" class="form-control"
                                    value="1" min="0" required>
                                <small class="form-text text-muted">Menu grouping (0 for ungrouped)</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="position">Position <span class="text-danger">*</span></label>
                                <input type="number" name="position" id="position" class="form-control"
                                    value="1" min="0" required>
                                <small class="form-text text-muted">Display order position</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="1">Page</option>
                                    <option value="2">Function</option>
                                </select>
                                <small class="form-text text-muted">Page or Function permission</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="level">Access Levels <span class="text-danger">*</span></label>
                        <select name="level[]" id="level" class="form-control" multiple required>
                            <option value="all">All Users</option>
                            <?php if (!empty($user_levels)) : ?>
                                <?php foreach ($user_levels as $level) : ?>
                                    <option value="<?= $level['level_key'] ?>"><?= $level['level_name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fa fa-info-circle"></i>
                            Select multiple access levels. Use <strong>Ctrl+Click</strong> (Windows) or <strong>Cmd+Click</strong> (Mac) to select multiple options.
                            <br><strong>Note:</strong> If "All Users" is selected, it will override other selections.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="special_user">Special Users</label>
                                <input type="text" name="special_user" id="special_user" class="form-control"
                                    placeholder="Comma-separated employee IDs">
                                <small class="form-text text-muted">Specific users with access</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exclude_user">Exclude Users</label>
                                <input type="text" name="exclude_user" id="exclude_user" class="form-control"
                                    placeholder="Comma-separated employee IDs">
                                <small class="form-text text-muted">Users to exclude from access</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="status-group" style="display: none;">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Save Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Initialize Select2 for multi-select
        function initializeSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#level').select2({
                    placeholder: 'Select access levels',
                    allowClear: false,
                    width: '100%',
                    tags: false
                });
            } else {
                // Fallback: Add multiple attribute and improve styling
                $('#level').attr('multiple', 'multiple').css({
                    'height': 'auto',
                    'min-height': '100px'
                });
            }
        }

        // Initialize DataTable
        $('#permissionsTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pageLength: 25,
            order: [
                [3, 'asc'],
                [7, 'asc']
            ], // Order by grouping, then position
            columnDefs: [{
                    orderable: false,
                    targets: [-1]
                } // Disable ordering on Actions column
            ],
            language: {
                search: "Search permissions:",
                lengthMenu: "Show _MENU_ permissions per page",
                info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                infoEmpty: "Showing 0 to 0 of 0 permissions",
                infoFiltered: "(filtered from _MAX_ total permissions)"
            }
        });

        // Add Permission
        $(document).on('click', '.add_permission-btn', function() {
            $('#permission-modal .modal-title').text('Add Permission');
            $('#permission-form')[0].reset();
            $('#status-group').hide();

            // Clear and re-initialize Select2
            $('#level').val([]).trigger('change');
            initializeSelect2();

            $('#permission-modal').modal('show');

            $(document).off('submit', '#permission-form').on('submit', '#permission-form', function(e) {
                e.preventDefault();

                // Get form data using serialize() and handle levels separately
                var formData = new FormData(this);

                // Get selected levels
                var selectedLevels = $('#level').val();

                // Validate that at least one level is selected
                if (!selectedLevels || selectedLevels.length === 0) {
                    $.alert({
                        title: 'Validation Error!',
                        content: 'Please select at least one access level.',
                        type: 'red'
                    });
                    return false;
                }

                // Clear existing level data and add fresh values
                formData.delete('level[]');
                if (selectedLevels && selectedLevels.length > 0) {
                    selectedLevels.forEach(function(level) {
                        formData.append('level[]', level);
                    });
                }

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('permissions/add_permission') ?>',
                    data: formData,
                    processData: false,
                    contentType: false,
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
                    },
                    error: function() {
                        $.alert({
                            title: 'Error!',
                            content: 'An error occurred while processing your request.',
                            type: 'red'
                        });
                    }
                });
            });
        });

        // Edit Permission
        $(document).on('click', '.edit_permission-btn', function() {
            var permission_id = $(this).closest('tr').data('permission-id');

            $('#permission-modal .modal-title').text('Edit Permission');
            $('#status-group').show();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('permissions/get_permission') ?>',
                data: {
                    permission_id: permission_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        var data = response.data;
                        $('#keyword').val(data.keyword);
                        $('#name').val(data.name);
                        $('#link').val(data.link);
                        $('#grouping').val(data.grouping);

                        // Handle multiple levels
                        var levels = data.level ? data.level.split(',') : [];
                        levels = levels.map(function(level) {
                            return level.trim();
                        });

                        $('#special_user').val(data.special_user);
                        $('#exclude_user').val(data.exclude_user);
                        $('#icon').val(data.icon);
                        $('#position').val(data.position);
                        $('#type').val(data.type);
                        $('#status').val(data.status);

                        // Initialize Select2 and set values
                        initializeSelect2();

                        // Set values with a small delay to ensure Select2 is ready
                        setTimeout(function() {
                            $('#level').val(levels).trigger('change');
                        }, 100);

                        $('#permission-modal').modal('show');

                        $(document).off('submit', '#permission-form').on('submit', '#permission-form', function(e) {
                            e.preventDefault();

                            // Get form data using FormData and handle levels separately
                            var formData = new FormData(this);
                            formData.append('permission_id', permission_id);

                            // Get selected levels
                            var selectedLevels = $('#level').val();

                            // Validate that at least one level is selected
                            if (!selectedLevels || selectedLevels.length === 0) {
                                $.alert({
                                    title: 'Validation Error!',
                                    content: 'Please select at least one access level.',
                                    type: 'red'
                                });
                                return false;
                            }

                            // Clear existing level data and add fresh values
                            formData.delete('level[]');
                            if (selectedLevels && selectedLevels.length > 0) {
                                selectedLevels.forEach(function(level) {
                                    formData.append('level[]', level);
                                });
                            }

                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('permissions/update_permission') ?>',
                                data: formData,
                                processData: false,
                                contentType: false,
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
                                },
                                error: function() {
                                    $.alert({
                                        title: 'Error!',
                                        content: 'An error occurred while processing your request.',
                                        type: 'red'
                                    });
                                }
                            });
                        });
                    }
                }
            });
        });

        // Toggle Status
        $(document).on('click', '.toggle-status-btn', function() {
            var $btn = $(this);
            var permission_id = $btn.closest('tr').data('permission-id');

            $.ajax({
                type: 'POST',
                url: '<?= base_url('permissions/toggle_status') ?>',
                data: {
                    permission_id: permission_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.new_status == 1) {
                            $btn.removeClass('btn-secondary').addClass('btn-success')
                                .html('<i class="fa fa-check"></i> Active');
                        } else {
                            $btn.removeClass('btn-success').addClass('btn-secondary')
                                .html('<i class="fa fa-times"></i> Inactive');
                        }

                        $.alert({
                            title: 'Success!',
                            content: response.message,
                            type: 'green',
                            backgroundDismiss: true
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

        // Delete Permission
        $(document).on('click', '.delete_permission-btn', function() {
            var permission_id = $(this).closest('tr').data('permission-id');

            $.confirm({
                title: 'Delete Permission',
                content: 'Are you sure you want to delete this permission? This action cannot be undone.',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes, Delete',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('permissions/delete_permission') ?>',
                                data: {
                                    permission_id: permission_id
                                },
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
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-default'
                    }
                }
            });
        });
    });
</script>