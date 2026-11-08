<?php

if (!function_exists('qr_public_url')) {
    /**
     * Build a public URL that points to the asset info page.
     * Used to encode inside QR Code labels.
     */
    function qr_public_url(string $allId): string
    {
        $base = rtrim(base_url(), '/');
        return $base . '/qr/' . $allId;
    }
}

if (!function_exists('qr_image_src')) {
    /**
     * Return the URL of a QR Code image for the given payload.
     *
     * Uses a lightweight external QR image API (api.qrserver.com) so the
     * project does not need an extra PHP dependency. The image is fetched
     * by the browser when the public page is loaded or when the label is
     * printed, and the same URL is embedded in the printable label <img>.
     */
    function qr_image_src(string $payload, int $size = 300): string
    {
        $size = max(80, min(2000, $size));
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size
            . '&margin=1&data=' . rawurlencode($payload);
    }
}
