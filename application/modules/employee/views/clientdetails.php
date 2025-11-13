<?php

$pets_list = [
    [
        'name' => 'Mep',
        'breed' => 'Bulldog',
        'image' => base_url('dist/img/furrs-user.png')
    ],
    [
        'name' => 'Finn',
        'breed' => 'Shih Tzu',
        'image' => base_url('dist/img/furrs-user.png')
    ],
    [
        'name' => 'Sky',
        'breed' => 'Shih Tzu',
        'image' => base_url('dist/img/furrs-user.png')
    ],
];

?>

<div class="row">
    <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('dist/img/furrs-user.png') ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">Nina Mcintire</h3>
                <p class="text-muted text-center">( Tata )</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <strong>Data</strong>
                        <span class="float-right">Data Text</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Data</strong>
                        <span class="float-right">Data Text</span>
                    </li>
                </ul>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- About Me Box -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Pets</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="pets--list row">
                    <?php foreach ($pets_list as $pet) : ?>
                        <a href="javascript:;" class="pet--item col-md-12">
                            <div class="pet--image">
                                <img src="<?= $pet['image'] ?>" alt="Pet Image">
                            </div>

                            <div class="pet--details">
                                <h3 class="pet--name"><?= $pet['name'] ?></h3>
                                <p class="pet--type"><?= $pet['breed'] ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Details</a></li>
                    <li class="nav-item"><a class="nav-link" href="#appointments" data-toggle="tab">Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#vetdetails" data-toggle="tab">Vet Details</a></li>
                </ul>
            </div><!-- /.card-header -->

            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="details">
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" id="inputName" placeholder="First Name">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" id="inputName" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Mobile</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputName2" placeholder="Mobile">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Address</label>
                                <div class="col-sm-10">
                                    <textarea name="Address" class="form-control" id="Address" placeholder="Address"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="appointments">


                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Pet</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    <th>Option</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php

                                $pet_names = array_column($pets_list, 'name');

                                for ($x = 1; $x <= 5; $x++) :

                                    // get random pet name
                                    $rand_pet_name = $pet_names[array_rand($pet_names)];
                                ?>
                                    <tr>
                                        <td><?= $rand_pet_name ?></td>
                                        <td>01/01/2021</td>
                                        <td>10:00 AM</td>
                                        <td>Active</td>
                                        <td>01/01/2021</td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-sm btn-info"><i class="fas fa-solid fa-eye mr-1"></i> View</a>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>

                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="vetdetails">
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputName" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputName2" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>