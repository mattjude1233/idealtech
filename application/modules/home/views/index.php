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
                    <li class="breadcrumb-item active">Home</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-6 col-md-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Employees</span>
                        <span class="info-box-number"><?= number_format($stats['active_employees']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Regular</span>
                        <span class="info-box-number"><?= number_format($stats['regular_employees']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-user-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Probationary</span>
                        <span class="info-box-number"><?= number_format($stats['probee_employees']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <a class="info-box" href="<?= base_url('leaves') ?>">
                    <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Leaves</span>
                        <span class="info-box-number"><?= number_format($stats['pending_leaves']) ?></span>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a class="info-box" href="<?= base_url('holiday') ?>">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Holidays</span>
                        <span class="info-box-number"><?= number_format($stats['total_holidays']) ?></span>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a class="info-box" href="<?= base_url('disciplinary') ?>">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Disciplinary</span>
                        <span class="info-box-number"><?= number_format($stats['disciplinary_cases']) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Active Kudos Board -->
        <?php if (!empty($active_kudos)) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-star"></i> Active Kudos
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-md-4 offset-md-4">
                                    <div class="kudos-display">
                                        <?php if (!empty($active_kudos['path'])) : ?>
                                            <img src="<?= base_url($active_kudos['path']) ?>" alt="<?= $active_kudos['name'] ?>" class="img-fluid rounded shadow" style="max-height: 300px; object-fit: contain;">
                                        <?php else : ?>
                                            <img src="<?= base_url('assets/images/no-image.png') ?>" alt="No Kudos Image" class="img-fluid rounded shadow" style="max-height: 300px; object-fit: contain;">
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <h4 class="text-primary"><?= $active_kudos['name'] ?></h4>
                                            <p class="text-muted">
                                                <i class="fas fa-tag"></i> <?= $active_kudos['category'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Attendance & Leaves -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-times"></i> Today's Absent
                        </h3>

                        <div class="card-tools">
                            <a href="<?= base_url('attendance') ?>" class="btn btn-primary btn-xs" title="View All Attendance">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($today_absent)) : ?>
                            <?php foreach ($today_absent as $absent) : ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?= $absent['emp_fname'] . ' ' . $absent['emp_lname'] ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($absent['date'])) ?>
                                        </small>
                                        <br>
                                        <small class="text-info">
                                            <?= !empty($absent['type']) ? ucfirst(str_replace('_', ' ', $absent['type'])) : 'Absent' ?>
                                        </small>
                                        <?php if (!empty($absent['notes'])) : ?>
                                            <br>
                                            <small class="text-secondary"><?= $absent['notes'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php if ($absent['absent'] == 'TRUE') : ?>
                                            <span class="badge badge-danger"><?= $absent['type_label'] ?></span>
                                        <?php else : ?>
                                            <span class="badge badge-secondary">No Record</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr class="my-2">
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted text-center">No absent employees today.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i> Upcoming Leaves
                        </h3>

                        <!-- view leaves -->
                        <div class="card-tools">
                            <a href="<?= base_url('leaves') ?>" class="btn btn-primary btn-xs" title="View All Leaves">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcoming_leaves)) : ?>
                            <?php foreach ($upcoming_leaves as $leave) : ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?= $leave['emp_fname'] . ' ' . $leave['emp_lname'] ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($leave['date_from'])) ?> - <?= date('d M Y', strtotime($leave['date_to'])) ?>
                                        </small>
                                        <br>
                                        <small class="text-info"><?= ucfirst($leave['leave_type'] ?? 'Leave') ?></small>
                                    </div>
                                    <div>
                                        <?php if ($leave['sv_status'] == 'approved' || $leave['mgr_status'] == 'approved') : ?>
                                            <span class="badge badge-success">Approved</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr class="my-2">
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted text-center">No upcoming leaves.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gift"></i> Upcoming Holidays
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcoming_holidays)) : ?>
                            <?php foreach ($upcoming_holidays as $holiday) : ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?= $holiday['name'] ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('F d, Y', strtotime($holiday['date'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge badge-info"><?= ucfirst($holiday['type'] ?? 'Holiday') ?></span>
                                    </div>
                                </div>
                                <hr class="my-2">
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted text-center">No upcoming holidays.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>