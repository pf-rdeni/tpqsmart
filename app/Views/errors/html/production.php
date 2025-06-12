<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            background: #fafafa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .headline {
            font-size: 48px;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .lead {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .actions {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>
        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>
        <div class="actions">
            <a href="<?= base_url() ?>" class="btn"><?= lang('Errors.backToHome') ?></a>
            <a href="javascript:history.back()" class="btn"><?= lang('Errors.backToPrevious') ?></a>
        </div>
    </div>
</body>

</html>