<?php
function getAPIKey($vendeur) {
    $api_keys = [
        'MIM_C' => 'C8B2E4F6A9D1G3H5' // Clé fictive, à remplacer par la vraie clé CY Bank
    ];
    return $api_keys[$vendeur] ?? '';
}
?>