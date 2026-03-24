<?php

function hs_sanitize_text($value)
{
    $value = trim((string) $value);
    $value = strip_tags($value);
    $value = preg_replace('/[\r\n\t]+/u', ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value);

    return trim((string) $value);
}

function hs_sanitize_textarea($value)
{
    $value = trim((string) $value);
    $value = strip_tags($value);
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $lines = array_map('trim', explode("\n", $value));
    $lines = array_filter($lines, static function ($line) {
        return $line !== '';
    });

    return implode("\n", $lines);
}

function hs_sanitize_email($value)
{
    $value = trim((string) $value);
    $value = filter_var($value, FILTER_SANITIZE_EMAIL);

    return $value ?: '';
}

function hs_validate_email($value)
{
    return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
}

function hs_validate_phone($value)
{
    $value = trim((string) $value);

    if ($value === '' || !preg_match('/^[0-9+\s().-]{9,20}$/', $value)) {
        return false;
    }

    $digits = preg_replace('/\D/', '', $value);

    return strlen($digits) >= 9 && strlen($digits) <= 15;
}

function hs_validate_year($value)
{
    if ($value === '') {
        return true;
    }

    return (bool) preg_match('/^(19|20)\d{2}$/', $value);
}

function hs_post_checked($value)
{
    return in_array((string) $value, ['1', 'on', 'true'], true);
}

function hs_mail_subject($value)
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function hs_mail_value($label, $value)
{
    if ($value === '') {
        $value = 'No indicado';
    }

    return $label . ': ' . $value;
}

function hs_redirect($path)
{
    header('Location: ' . $path);
    exit;
}
