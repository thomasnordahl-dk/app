<?php

declare(strict_types=1);

/** @var Error $error */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-800">500</h1>
        <p class="text-2xl text-gray-600 mt-4">Oops! Something went wrong.</p>
        <p class="text-gray-500 mt-2">The server encountered an error and couldn’t complete your request.</p>
        <a href="/" class="mt-6 inline-block px-6 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
            Go Home
        </a>
    </div>

    <!-- Debug Info: only visible in development -->
    <div class="max-w-4xl w-full mt-8 border-t pt-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Debug Details</h2>
        <div class="bg-gray-800 text-gray-100 p-4 rounded overflow-auto">
        <p><strong>Type:</strong> <?php echo htmlspecialchars(get_class($error), ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>
            <strong>File:</strong>
            <?php echo htmlspecialchars($error->getFile(), ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p>
            <strong>Line:</strong>
            <?php echo htmlspecialchars((string) $error->getLine(), ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <pre class="mt-4 text-sm"><?php echo htmlspecialchars($error->getTraceAsString(), ENT_QUOTES, 'UTF-8'); ?></pre>
        </div>
    </div>
</body>
</html>
