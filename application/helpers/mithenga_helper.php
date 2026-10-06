<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function normalize_mithenga_phone($phone, $country_code = '260')
{
    $phone = preg_replace('/\D+/', '', (string) $phone);
    $country_code = preg_replace('/\D+/', '', (string) $country_code);
    if ($phone === '') return '';
    if (strpos($phone, '00') === 0) return substr($phone, 2);
    if (strpos($phone, '0') === 0) return $country_code . ltrim($phone, '0');
    if (strlen($phone) === 9) return $country_code . $phone;
    return $phone;
}

function send_mithenga_whatsapp($phone, $message)
{
    if (!function_exists('curl_init')) {
        return array('success' => FALSE, 'message_id' => NULL, 'to' => '', 'http_code' => 0, 'error' => 'PHP cURL extension is not enabled.');
    }
    $ci =& get_instance();
    $ci->config->load('mithenga', TRUE);
    $settings = $ci->config->item('mithenga');
    $api_key = trim($settings['mithenga_api_key']);
    $account_id = (int) $settings['mithenga_account_id'];
    $base_url = rtrim($settings['mithenga_base_url'], '/');
    $to = normalize_mithenga_phone($phone, $settings['mithenga_default_country_code']);
    $result = array('success' => FALSE, 'message_id' => NULL, 'to' => $to, 'http_code' => 0, 'error' => NULL);

    if ($api_key === '') {
        $result['error'] = 'MITHENGA_API_KEY is not configured.';
        return $result;
    }
    if ($account_id <= 0 || $to === '' || trim((string) $message) === '') {
        $result['error'] = 'Mithenga account, recipient, or message is invalid.';
        return $result;
    }

    $ch = curl_init($base_url . '/accounts/' . $account_id . '/messages/send');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => json_encode(array('to' => $to, 'text' => trim((string) $message))),
        CURLOPT_HTTPHEADER => array('X-API-Key: ' . $api_key, 'Content-Type: application/json', 'Accept: application/json'),
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => max(1, (int) $settings['mithenga_timeout']),
    ));
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $result['http_code'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === FALSE) {
        $result['error'] = $curl_error ?: 'Mithenga request failed.';
        return $result;
    }
    $decoded = json_decode($response, TRUE);
    if ($result['http_code'] >= 200 && $result['http_code'] < 300 && !empty($decoded['messageId'])) {
        $result['success'] = TRUE;
        $result['message_id'] = $decoded['messageId'];
        $result['to'] = isset($decoded['to']) ? $decoded['to'] : $to;
        return $result;
    }
    $result['error'] = !empty($decoded['message']) ? $decoded['message'] : 'Mithenga returned HTTP ' . $result['http_code'] . '.';
    return $result;
}

function send_mithenga_whatsapp_bulk($messages)
{
    if (!function_exists('curl_init')) {
        return array('success' => FALSE, 'results' => array(), 'http_code' => 0, 'error' => 'PHP cURL extension is not enabled.');
    }
    $ci =& get_instance();
    $ci->config->load('mithenga', TRUE);
    $settings = $ci->config->item('mithenga');
    $prepared = array();
    foreach ((array) $messages as $message) {
        $to = normalize_mithenga_phone(isset($message['to']) ? $message['to'] : '', $settings['mithenga_default_country_code']);
        $text = trim(isset($message['text']) ? $message['text'] : '');
        if ($to !== '' && $text !== '') $prepared[] = array('to' => $to, 'text' => $text);
    }
    $result = array('success' => FALSE, 'results' => array(), 'http_code' => 0, 'error' => NULL);
    if (empty($settings['mithenga_api_key']) || empty($prepared)) {
        $result['error'] = empty($prepared) ? 'No valid WhatsApp messages supplied.' : 'MITHENGA_API_KEY is not configured.';
        return $result;
    }
    $url = rtrim($settings['mithenga_base_url'], '/') . '/accounts/' . (int) $settings['mithenga_account_id'] . '/messages/send-bulk';
    $ch = curl_init($url);
    curl_setopt_array($ch, array(CURLOPT_POST => TRUE, CURLOPT_POSTFIELDS => json_encode(array('messages' => $prepared)), CURLOPT_HTTPHEADER => array('X-API-Key: ' . trim($settings['mithenga_api_key']), 'Content-Type: application/json', 'Accept: application/json'), CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => max(15, (int) $settings['mithenga_timeout'] * count($prepared))));
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $result['http_code'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === FALSE) { $result['error'] = $curl_error ?: 'Mithenga bulk request failed.'; return $result; }
    $decoded = json_decode($response, TRUE);
    if ($result['http_code'] >= 200 && $result['http_code'] < 300 && isset($decoded['results'])) { $result['success'] = TRUE; $result['results'] = $decoded['results']; return $result; }
    $result['error'] = !empty($decoded['message']) ? $decoded['message'] : 'Mithenga returned HTTP ' . $result['http_code'] . '.';
    return $result;
}

function mithenga_phone_for_email($email)
{
    $ci =& get_instance();
    $email = strtolower(trim((string) $email));
    if ($email === '' || strpos($email, ',') !== FALSE) return '';
    $row = $ci->db->select('PhoneNumber')->where('LOWER(TRIM(EmailAddress))', $email)->get('employees')->row();
    if ($row && !empty($row->PhoneNumber)) return $row->PhoneNumber;
    $row = $ci->db->select('PhoneNumber')->where('LOWER(TRIM(EmailAddress))', $email)->get('individual_customers')->row();
    if ($row && !empty($row->PhoneNumber)) return $row->PhoneNumber;
    $row = $ci->db->select('phone_number')->where('LOWER(TRIM(contact_email))', $email)->get('corporate_customers')->row();
    return $row && !empty($row->phone_number) ? $row->phone_number : '';
}

function mithenga_text_from_email($subject, $html)
{
    $text = preg_replace('/<\s*br\s*\/?\s*>/i', PHP_EOL, (string) $html);
    $text = preg_replace('/<\/\s*(p|div|h[1-6]|tr|li)\s*>/i', PHP_EOL, $text);
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\R{3,}/', PHP_EOL . PHP_EOL, $text);
    return '*' . trim((string) $subject) . '*' . PHP_EOL . PHP_EOL . trim($text);
}

function mithenga_reset_signature($employee_id, $reset_code, $expires)
{
    $ci =& get_instance();
    $ci->config->load('mithenga', TRUE);
    $settings = $ci->config->item('mithenga');
    return hash_hmac('sha256', $employee_id . '|' . $reset_code . '|' . $expires, $settings['mithenga_api_key']);
}

function verify_mithenga_reset_signature($employee_id, $reset_code, $expires, $signature)
{
    if ((int) $expires < time() || empty($signature)) return FALSE;
    return hash_equals(mithenga_reset_signature($employee_id, $reset_code, $expires), (string) $signature);
}
