<?php

declare(strict_types=1);

use Ricotta\App\Module\Configuration\Configuration;

    return function (Configuration $config) {
        ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $config->string("ricotta.demo.frontpage.title")?></title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="flex items-center justify-center h-screen bg-gray-100">
        <div class="text-center">
            <h1 class="text-5xl font-bold text-gray-800">
                <?= $config->string("ricotta.demo.frontpage.title")?>
            </h1>
            <p class="text-lg text-gray-600 mt-4">
                <?= $config->string("ricotta.demo.frontpage.message")?>
            </p>
            <div class="mt-6">
                <a 
                    href="https://github.com/thomasnordahl-dk/app" 
                    class="px-6 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700"
                >
                    Read Docs
                </a>
            </div>
        </div>
    </body>
    </html>

        <?php
    };
