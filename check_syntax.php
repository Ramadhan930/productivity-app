<?php
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));

$errors = 0;

foreach ($rii as $file) {
    if ($file->isDir())
        continue;

    if (pathinfo($file->getPathname(), PATHINFO_EXTENSION) === 'php') {
        $output = null;
        $return_var = null;
        exec("php -l " . escapeshellarg($file->getPathname()), $output, $return_var);
        if ($return_var !== 0) {
            echo implode("\n", $output) . "\n";
            $errors++;
        }
    }
}

if ($errors > 0) {
    exit(1);
} else {
    echo "All PHP files passed syntax check.\n";
}
