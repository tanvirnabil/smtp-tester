<?php
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$db     = get_db();
$action = $_POST['action'] ?? '';

if ($action === 'send') {
    $config_id  = (int)($_POST['config_id'] ?? 0);
    $to         = trim($_POST['to'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');
    $body       = trim($_POST['body'] ?? '');

    if (!$config_id || !$to || !$subject || !$body) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid recipient email address format.']);
        exit;
    }

    $cfg = $db->query("SELECT * FROM smtp_configs WHERE id=$config_id")->fetch(PDO::FETCH_ASSOC);
    if (!$cfg) {
        echo json_encode(['status' => 'error', 'message' => 'SMTP config not found.']);
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $cfg['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $cfg['username'];
        $mail->Password   = $cfg['password'];
        $mail->Port       = $cfg['port'];
        $mail->SMTPDebug  = 0;
        $mail->Timeout    = 15;

        if ($cfg['encryption'] === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($cfg['encryption'] === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPAutoTLS = false;
        }

        $fromEmail = !empty($cfg['from_email']) ? $cfg['from_email'] : $cfg['username'];
        $fromName  = !empty($cfg['from_name'])  ? $cfg['from_name']  : 'SMTP Tester';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;

        if (strip_tags($body) !== $body) {
            $mail->isHTML(true);
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
        } else {
            $mail->Body = $body;
        }

        $mail->send();

        $db->prepare("INSERT INTO send_logs (config_id, config_name, to_address, subject, status) VALUES (?,?,?,?,?)")
           ->execute([$config_id, $cfg['name'], $to, $subject, 'delivered']);

        echo json_encode(['status' => 'success', 'message' => 'Email delivered successfully.']);

    } catch (Exception $e) {
        $err = $mail->ErrorInfo;

        // Classify error
        if (stripos($err, '550') !== false || stripos($err, 'user unknown') !== false || stripos($err, 'does not exist') !== false) {
            $msg = 'Recipient address rejected by server (550): The email address may not exist.';
        } elseif (stripos($err, 'authentication') !== false || stripos($err, '535') !== false || stripos($err, 'credentials') !== false) {
            $msg = 'Authentication failed: Check your SMTP username and password.';
        } elseif (stripos($err, 'connection') !== false || stripos($err, 'timed out') !== false || stripos($err, 'could not connect') !== false) {
            $msg = 'Connection failed: Check host, port, and firewall settings.';
        } elseif (stripos($err, 'ssl') !== false || stripos($err, 'tls') !== false) {
            $msg = 'Encryption error: Try changing encryption type or port.';
        } else {
            $msg = $err;
        }

        $db->prepare("INSERT INTO send_logs (config_id, config_name, to_address, subject, status, error_message) VALUES (?,?,?,?,?,?)")
           ->execute([$config_id, $cfg['name'], $to, $subject, 'failed', $msg]);

        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
    exit;
}

if ($action === 'save_config') {
    $stmt = $db->prepare("INSERT INTO smtp_configs (name,host,port,encryption,username,password,from_email,from_name) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([
        trim($_POST['name']), trim($_POST['host']), (int)$_POST['port'],
        $_POST['encryption'], trim($_POST['username']), $_POST['password'],
        trim($_POST['from_email'] ?? ''), trim($_POST['from_name'] ?? '')
    ]);
    echo json_encode(['status' => 'success', 'id' => $db->lastInsertId()]);
    exit;
}

if ($action === 'get_config') {
    $r = $db->query("SELECT * FROM smtp_configs WHERE id=".(int)$_POST['id'])->fetch(PDO::FETCH_ASSOC);
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'update_config') {
    $db->prepare("UPDATE smtp_configs SET name=?,host=?,port=?,encryption=?,username=?,password=?,from_email=?,from_name=? WHERE id=?")
       ->execute([
           trim($_POST['name']), trim($_POST['host']), (int)$_POST['port'],
           $_POST['encryption'], trim($_POST['username']), $_POST['password'],
           trim($_POST['from_email'] ?? ''), trim($_POST['from_name'] ?? ''),
           (int)$_POST['id']
       ]);
    echo json_encode(['status' => 'success']);
    exit;
}
if ($action === 'delete_config') {
    $db->prepare("DELETE FROM smtp_configs WHERE id=?")->execute([(int)$_POST['id']]);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'save_template') {
    $stmt = $db->prepare("INSERT INTO templates (name,subject,body) VALUES (?,?,?)");
    $stmt->execute([trim($_POST['name']), trim($_POST['subject']), $_POST['body']]);
    echo json_encode(['status' => 'success', 'id' => $db->lastInsertId()]);
    exit;
}

if ($action === 'delete_template') {
    $db->prepare("DELETE FROM templates WHERE id=? AND is_default=0")->execute([(int)$_POST['id']]);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'get_template') {
    $t = $db->query("SELECT * FROM templates WHERE id=" . (int)$_POST['id'])->fetch(PDO::FETCH_ASSOC);
    echo json_encode($t ?: []);
    exit;
}

if ($action === 'get_configs') {
    $rows = $db->query("SELECT id, name, host, port, encryption, username FROM smtp_configs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'get_templates') {
    $rows = $db->query("SELECT * FROM templates ORDER BY is_default DESC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'get_logs') {
    $rows = $db->query("SELECT * FROM send_logs ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'clear_logs') {
    $db->exec("DELETE FROM send_logs");
    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
