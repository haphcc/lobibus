<?php

return [
    'base_url' => getenv('PAYOS_BASE_URL') ?: 'https://api-merchant.payos.vn',
    'client_id' => getenv('PAYOS_CLIENT_ID') ?: '',
    'api_key' => getenv('PAYOS_API_KEY') ?: '',
    'checksum_key' => getenv('PAYOS_CHECKSUM_KEY') ?: '',
];
