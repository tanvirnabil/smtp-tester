<?php
function get_db() {
    static $db;
    if (!$db) {
        $db = new PDO('sqlite:' . __DIR__ . '/smtp_tester.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("PRAGMA journal_mode=WAL;");
        $db->exec("
            CREATE TABLE IF NOT EXISTS smtp_configs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                host TEXT NOT NULL,
                port INTEGER NOT NULL,
                encryption TEXT NOT NULL,
                username TEXT NOT NULL,
                password TEXT NOT NULL,
                from_email TEXT NOT NULL DEFAULT '',
                from_name TEXT NOT NULL DEFAULT '',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                subject TEXT NOT NULL,
                body TEXT NOT NULL,
                is_default INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS send_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                config_id INTEGER,
                config_name TEXT,
                to_address TEXT,
                subject TEXT,
                status TEXT,
                error_message TEXT,
                sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
        // Seed default templates
        $count = $db->query("SELECT COUNT(*) FROM templates WHERE is_default=1")->fetchColumn();
        if ($count == 0) {
            $defaults = [
                ['Plain Text Test', 'Test Email', "Hello,\n\nThis is a plain text test email.\n\nRegards,\nSMTP Tester"],
                ['HTML Welcome',    'Welcome!',   "<h2>Welcome!</h2><p>This is an <b>HTML</b> test email from SMTP Tester.</p>"],
                ['HTML Alert',      'Action Required', "<div style='border-left:4px solid #e74c3c;padding:10px;'><h3>Alert</h3><p>This is a test alert email. Please ignore.</p></div>"],
            ];
            $stmt = $db->prepare("INSERT INTO templates (name, subject, body, is_default) VALUES (?,?,?,1)");
            foreach ($defaults as $d) $stmt->execute($d);
        }
    }
    return $db;
}
