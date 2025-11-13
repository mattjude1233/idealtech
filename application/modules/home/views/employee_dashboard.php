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
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">My Attendance</span>
                        <span class="info-box-number"><?= isset($employee_attendance['attendance_percentage']) ? $employee_attendance['attendance_percentage'] . '%' : '0%' ?></span>
                        <small class="text-muted">This month</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Hours This Month</span>
                        <span class="info-box-number"><?= isset($employee_attendance['hours_this_month']) ? $employee_attendance['hours_this_month'] : 0 ?></span>
                        <small class="text-muted">Total hours worked</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-umbrella-beach"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">SIL Remaining</span>
                        <span class="info-box-number"><?= isset($employee_sil['remaining']) ? $employee_sil['remaining'] : 0 ?></span>
                        <small class="text-muted">Hours available</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Upcoming Holidays</span>
                        <span class="info-box-number"><?= number_format($stats['total_holidays']) ?></span>
                    </div>
                </div>
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

        <!-- Attendance & Personal Info -->
        <div class="row equal">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-clock"></i> My Leave Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h5 class="text-primary">Available</h5>
                                    <h3 class="text-primary"><?= isset($employee_sil['remaining']) ? $employee_sil['remaining'] : 0 ?></h3>
                                    <small class="text-muted">SIL Hours remaining</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h5 class="text-info">Used</h5>
                                    <h3 class="text-info"><?= isset($employee_sil['used']) ? $employee_sil['used'] : 0 ?></h3>
                                    <small class="text-muted">SIL Hours this year</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <a href="<?= base_url('leaves') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Apply for Leave
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i> My Recent Attendance
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h5 class="text-muted">Last 7 Days</h5>
                            <div class="row">
                                <div class="col-4">
                                    <div class="text-success">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                        <p>Present<br><strong><?= isset($employee_attendance['last_7_days']['present']) ? $employee_attendance['last_7_days']['present'] : 0 ?> days</strong></p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-warning">
                                        <i class="fas fa-clock fa-2x"></i>
                                        <p>Late<br><strong><?= isset($employee_attendance['last_7_days']['late']) ? $employee_attendance['last_7_days']['late'] : 0 ?> days</strong></p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-danger">
                                        <i class="fas fa-times-circle fa-2x"></i>
                                        <p>Absent<br><strong><?= isset($employee_attendance['last_7_days']['absent']) ? $employee_attendance['last_7_days']['absent'] : 0 ?> days</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Monthly Attendance Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="text-center">
                                    <h6 class="text-success">Present</h6>
                                    <h4 class="text-success"><?= isset($employee_attendance['current_month']['present']) ? $employee_attendance['current_month']['present'] : 0 ?></h4>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="text-center">
                                    <h6 class="text-warning">Late</h6>
                                    <h4 class="text-warning"><?= isset($employee_attendance['current_month']['late']) ? $employee_attendance['current_month']['late'] : 0 ?></h4>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="text-center">
                                    <h6 class="text-danger">Absent</h6>
                                    <h4 class="text-danger"><?= isset($employee_attendance['current_month']['absent']) ? $employee_attendance['current_month']['absent'] : 0 ?></h4>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="text-center">
                                    <h6 class="text-info">Total</h6>
                                    <h4 class="text-info"><?= isset($employee_attendance['current_month']['total']) ? $employee_attendance['current_month']['total'] : 0 ?></h4>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <a href="<?= base_url('attendance/records') ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View Detailed Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
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