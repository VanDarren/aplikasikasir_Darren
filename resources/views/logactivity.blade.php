<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="h3 mb-3 page-title">Log Activity</h2>
                <div class="row mb-4 items-align-center">
                    <div class="col-md">
                        <ul class="nav nav-pills justify-content-start">
                            <li class="nav-item">
                                <a class="nav-link active bg-transparent pr-2 pl-0 text-primary" href="#">All <span class="badge badge-pill bg-primary text-white ml-2">{{ $logs->count() }}</span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-auto ml-auto text-right">
                        <button type="button" class="btn"><span class="fe fe-refresh-ccw fe-16 text-muted"></span></button>
                    </div>
                </div>
                <table class="table border table-hover bg-white">
                    <thead>
                        <tr role="row">

                            <th>User</th>
                            <th>Activity</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
              <?php foreach ($logs as $log) : ?>
                <tr>
                  <td><?= $log->username ?></td>
                  <td><?= $log->activity ?></td>
                  <td><?= date('Y-m-d H:i:s', strtotime($log->time)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
