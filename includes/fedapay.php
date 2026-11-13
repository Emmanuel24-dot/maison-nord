<?php
require_once __DIR__ . '/fedapay-config.php';

function mn_fedapay_base_url() {
    return FEDAPAY_SANDBOX
        ? 'https://sandbox-api.fedapay.com/v1'
        : 'https://api.fedapay.com/v1';
}

function mn_fedapay_request($method, $endpoint, $data = null) {
    $url = mn_fedapay_base_url() . $endpoint;
    $ch  = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => !FEDAPAY_SANDBOX,
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bearer ' . FEDAPAY_SECRET_KEY,
            'Content-Type: application/json',
            'Accept: application/json'
        ),
    ));
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data !== null ? json_encode($data) : '{}');
    }
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);
    if ($err) return array('error' => $err);
    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : array('error' => 'Réponse invalide');
}

/**
 * Crée une transaction FedaPay et retourne l'URL de paiement.
 * Retourne : array('success' => bool, 'transaction_id' => int|null, 'payment_url' => string|null, 'message' => string|null)
 */
function mn_fedapay_create_transaction($amount, $description, $customer, $order_id) {
    $data = array(
        'description'  => $description,
        'amount'       => intval($amount),
        'currency'     => array('iso' => FEDAPAY_CURRENCY),
        'callback_url' => FEDAPAY_CALLBACK_URL . '?order=' . intval($order_id),
        'customer'     => array(
            'firstname'    => $customer['prenom'],
            'lastname'     => $customer['nom'],
            'email'        => $customer['email'],
            'phone_number' => array(
                'number'  => preg_replace('/[^0-9]/', '', $customer['phone']),
                'country' => 'TG'
            )
        )
    );

    $result = mn_fedapay_request('POST', '/transactions', $data);

    if (!isset($result['v1/transaction']['id'])) {
        $msg = isset($result['message']) ? $result['message']
             : (isset($result['error']) ? $result['error'] : 'Erreur FedaPay');
        return array('success' => false, 'transaction_id' => null, 'payment_url' => null, 'message' => $msg);
    }

    $txn_id = $result['v1/transaction']['id'];

    $token_result = mn_fedapay_request('POST', '/transactions/' . $txn_id . '/token', array());

    if (!isset($token_result['token']['url'])) {
        return array('success' => false, 'transaction_id' => $txn_id, 'payment_url' => null,
                     'message' => 'Impossible de générer le lien de paiement.');
    }

    return array(
        'success'        => true,
        'transaction_id' => $txn_id,
        'payment_url'    => $token_result['token']['url'],
        'message'        => null
    );
}

/**
 * Récupère les détails d'une transaction FedaPay.
 */
function mn_fedapay_get_transaction($transaction_id) {
    $result = mn_fedapay_request('GET', '/transactions/' . intval($transaction_id));
    return isset($result['v1/transaction']) ? $result['v1/transaction'] : null;
}
