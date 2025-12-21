<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Serikat Pekerja Kampus">
    <meta name="keywords" content="serikat,pekerja,dashboard,admin">
    <meta name="author" content="SPK">
    <?= csrf_meta() ?>

    <title><?= $this->renderSection('title', true) ?: 'Dashboard - Serikat Pekerja Kampus' ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

    <!-- Neptune Plugins CSS -->
    <link href="<?= base_url('assets/neptune/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/neptune/plugins/perfectscroll/perfect-scrollbar.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/neptune/plugins/pace/pace.css') ?>" rel="stylesheet">

    <!-- Neptune Theme Styles -->
    <link href="<?= base_url('assets/neptune/css/main.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/neptune/css/custom.css') ?>" rel="stylesheet">

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Additional Page CSS -->
    <?= $this->renderSection('styles') ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/neptune/images/neptune.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/neptune/images/neptune.png') ?>" />
</head>
<body>
    <div class="app align-content-stretch d-flex flex-wrap">
        <!-- Sidebar -->
        <?= $this->include('layouts/neptune_sidebar') ?>

        <div class="app-container">
            <!-- Search -->
            <div class="search">
                <form>
                    <input class="form-control" type="text" placeholder="Ketik untuk mencari..." aria-label="Search">
                </form>
                <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
            </div>

            <!-- Header -->
            <?= $this->include('layouts/neptune_header') ?>

            <!-- Main Content -->
            <div class="app-content">
                <div class="content-wrapper">
                    <div class="container-fluid">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/neptune/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/plugins/perfectscroll/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/plugins/pace/pace.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/js/main.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/js/custom.js') ?>"></script>

    <!-- Additional Page Scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
