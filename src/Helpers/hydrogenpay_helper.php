<?php

declare(strict_types=1);

// Check if the function doesn't already exist to avoid redeclaration errors
if (! function_exists('generateDeviceFingerprint')) {
    
    /**
     * Generate a device fingerprint hash based on the user's details.
     *
     * This creates a unique hash by combining:
     * - User agent
     * - IP address
     * - Browser language
     * - (Note: screen resolution is actually set to user agent here; adjust if needed)
     *
     * @return string MD5 hash representing the device fingerprint.
     */
    function generateDeviceFingerprint(): string
    {
        $userAgent        = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ipAddress        = $_SERVER['REMOTE_ADDR'] ?? '';
        $language         = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $screenResolution = $_SERVER['HTTP_USER_AGENT'] ?? ''; // You might replace this with actual screen resolution if available

        // Combine all parts into a single string
        $combinedString = $userAgent . $ipAddress . $language . $screenResolution;

        // Generate MD5 hash from the combined string
        $fingerprint = md5($combinedString);

        return $fingerprint;
    }
}
