<?php
/**
 * Minimal SMTP client using STARTTLS + AUTH LOGIN (no external libs)
 * Works with Gmail if you use an App Password (2‑Step Verification required).
 */
function smtp_send_html($to_email, $to_name, $subject, $html, $smtp)
{
  $host = $smtp['host'];
  $port = $smtp['port'];
  $username = $smtp['username'];
  $password = $smtp['password'];
  $from_email = $smtp['from_email'];
  $from_name  = $smtp['from_name'];

  $errno = 0; $errstr = '';
  $fp = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT);
  if (!$fp) throw new Exception("SMTP connect failed: $errstr ($errno)");

  stream_set_timeout($fp, 30);

  $read = function() use ($fp) {
    $data = '';
    while (!feof($fp)) {
      $line = fgets($fp, 515);
      $data .= $line;
      if (strlen($line) < 4) break;
      if (preg_match('/^[0-9]{3} [\s\S]*\r?\n$/', $line)) break; // last line has code + space
      if (!preg_match('/^[0-9]{3}-/', $line)) break;
    }
    return $data;
  };

  $write = function($cmd) use ($fp) { fwrite($fp, $cmd); };

  $expect = function($response, $code) {
    if (substr($response, 0, 3) !== (string)$code) {
      throw new Exception("SMTP error: expected {$code}, got: " . trim($response));
    }
  };

  $resp = $read();               // initial 220
  $expect($resp, 220);

  $write("EHLO localhost\r\n");
  $resp = $read(); $expect($resp, 250);

  // STARTTLS
  $write("STARTTLS\r\n");
  $resp = $read(); $expect($resp, 220);
  if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
    throw new Exception('Failed to enable TLS');
  }

  // EHLO again after TLS
  $write("EHLO localhost\r\n");
  $resp = $read(); $expect($resp, 250);

  // AUTH LOGIN
  $write("AUTH LOGIN\r\n");
  $resp = $read(); $expect($resp, 334);
  $write(base64_encode($username) . "\r\n");
  $resp = $read(); $expect($resp, 334);
  $write(base64_encode($password) . "\r\n");
  $resp = $read(); $expect($resp, 235);

  // MAIL FROM / RCPT TO
  $write("MAIL FROM:<{$from_email}>\r\n");
  $resp = $read(); $expect($resp, 250);

  $write("RCPT TO:<{$to_email}>\r\n");
  $resp = $read(); $expect($resp, 250);

  // DATA
  $write("DATA\r\n");
  $resp = $read(); $expect($resp, 354);

  $boundary = bin2hex(random_bytes(8));
  $date = date('r');
  $subject_encoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';
  $headers = [
    "Date: {$date}",
    "From: " . mime_header_encode($from_name) . " <{$from_email}>",
    "To: " . mime_header_encode($to_name) . " <{$to_email}>",
    "Subject: {$subject_encoded}",
    "MIME-Version: 1.0",
    "Content-Type: text/html; charset=UTF-8",
    "Content-Transfer-Encoding: base64",
  ];

  $body = chunk_split(base64_encode($html));

  $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
  $write($data);
  $resp = $read(); $expect($resp, 250);

  $write("QUIT\r\n");
  fclose($fp);
  return true;
}

function mime_header_encode($text) {
  if (preg_match('/[\x80-\xFF]/', $text)) {
    return '=?UTF-8?B?' . base64_encode($text) . '?=';
  }
  return $text;
}