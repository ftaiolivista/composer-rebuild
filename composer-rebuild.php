<?php

namespace ComposerRebuild;

$installed = json_decode(
    file_get_contents('vendor/composer/installed.json'),
    true
);

$required = array_flip(
    array_unique(
        array_reduce(
            $installed,
            function ($required, $item) {
                if (empty($item['require'])) {
                    return $required;
                }
                return array_merge(
                    $required,
                    array_keys($item['require'])
                );
            },
            []
        )
    )
);

$notRequired = array_filter(
    $installed,
    function ($v) use ($required) {
        return !array_key_exists($v['name'], $required);
    }
);

$deps = array_reduce(
    $notRequired,
    function ($deps, $item) {
        $deps[$item['name']] = '^'.$item['version_normalized'];
        return $deps;
    },
    []
);

$composer = [
    "require" => $deps
];

echo json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
