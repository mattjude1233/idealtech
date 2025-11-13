<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= COMPANY_NAME . (!empty($page_title) ? " | $page_title" : "") ?></title>

    <?php if (!empty($header__links['css'])) : ?>
        <?php foreach ($header__links['css'] as $name => $url) :

            // check if url needs base_url
            if (strpos($url, 'http') === false && strpos($url, 'www') === false) {
                $url = base_url($url);
            }
        ?>
            <?= IS_ADMIN ? "<!-- [$name] -->\n" : ""; ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($url); ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= site_url() ?>";
        var current_url = "<?= current_url() ?>";
    </script>

    <?php if (!empty($header__links['js'])) : ?>
        <?php foreach ($header__links['js'] as $name => $url) :
            // check if url needs base_url
            if (strpos($url, 'http') === false && strpos($url, 'www') === false) {
                $url = base_url($url);
            }
        ?>
            <?= IS_ADMIN ? "<!-- [$name] -->\n" : ""; ?>
            <script src="<?= htmlspecialchars($url); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">