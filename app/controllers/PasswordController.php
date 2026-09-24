<?php
/**
 * app/controllers/PasswordController.php
 * Handles forgot password & reset password flow.
 *
 * Requires PHPMailer: composer require phpmailer/phpmailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PasswordController
{
    // ── Gmail SMTP config ─────────────────────────────────
    // Credentials live in config/mail.php (git-ignored). See config/mail.example.php.
    private function mailConfig(): ?array
    {
        $file = dirname(__DIR__, 2) . '/config/mail.php';
        return is_file($file) ? require $file : null;
    }
    private const FROM_NAME  = 'MoveUp';

    // ── Helper: send email via Gmail SMTP ────────────────
    private function sendMail(string $toEmail, string $toName, string $subject, string $body): bool
    {
        $cfg = $this->mailConfig();
        if (!$cfg || empty($cfg['pass'])) {
            return false; // mail not configured
        }
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['user'];
            $mail->Password   = $cfg['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Sender & recipient
            $mail->setFrom($cfg['user'], self::FROM_NAME);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            die("<h1 style=\"color:red\">MAIL ERROR: " . $mail->ErrorInfo . "</h1>");
            return false;
        }
    }

    // ── Step 1: Show forgot password form ────────────────
    public function showForgot(): void
    {
        View::render('auth/forgot', ['flash' => null], 'public');
    }

    // ── Step 2: Send reset email ──────────────────────────
    public function sendReset(): void
    {
        $email = trim($_POST['email'] ?? '');
        $db    = Database::get();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('auth/forgot', [
                'flash' => ['type' => 'error', 'msg' => 'Email invalide.']
            ], 'public');
            return;
        }

        // Check user exists
        $s = $db->prepare("SELECT id, prenom FROM utilisateurs WHERE email = ? LIMIT 1");
        $s->bind_param('s', $email);
        $s->execute();
        $user = $s->get_result()->fetch_assoc();
        $s->close();

        // Always show success message (don't reveal if email exists)
        if ($user) {
            // Delete old tokens for this email
            $d = $db->prepare("DELETE FROM password_resets WHERE email = ?");
            $d->bind_param('s', $email);
            $d->execute();
            $d->close();

            // Generate token
            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            $ins = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $email, $token, $expiresAt);
            $ins->execute();
            $ins->close();

            // Build reset link
            $scheme    = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST']; // e.g. localhost:8080
$resetLink = $scheme . '://' . $host . View::base('password/reset') . '?token=' . $token;
            $prenom    = $user['prenom'] ?? 'Utilisateur';

            // Build email body
            $subject = 'MoveUp — Réinitialisation de votre mot de passe';
            $body    = "Bonjour $prenom,\n\n"
                     . "Vous avez demandé la réinitialisation de votre mot de passe.\n\n"
                     . "Cliquez sur ce lien (valable 1 heure) :\n"
                     . $resetLink . "\n\n"
                     . "Si vous n'avez pas fait cette demande, ignorez cet email.\n\n"
                     . "— L'équipe MoveUp";

            // Send via Gmail SMTP
            $this->sendMail($email, $prenom, $subject, $body);
        }

        View::render('auth/forgot', [
            'flash' => ['type' => 'success', 'msg' => 'Si cet email existe, un lien de réinitialisation a été envoyé.']
        ], 'public');
    }

    // ── Step 3: Show reset password form ─────────────────
    public function showReset(): void
    {
        $token = trim($_GET['token'] ?? '');
        $db    = Database::get();

        $s = $db->prepare("
            SELECT * FROM password_resets
            WHERE token = ? AND used = 0 AND expires_at > NOW()
            LIMIT 1
        ");
        $s->bind_param('s', $token);
        $s->execute();
        $reset = $s->get_result()->fetch_assoc();
        $s->close();

        if (!$reset) {
            View::render('auth/forgot', [
                'flash' => ['type' => 'error', 'msg' => 'Ce lien est invalide ou expiré. Veuillez faire une nouvelle demande.']
            ], 'public');
            return;
        }

        View::render('auth/reset', ['token' => $token, 'flash' => null], 'public');
    }

    // ── Step 4: Update password ───────────────────────────
    public function updatePassword(): void
    {
        $token    = trim($_POST['token']    ?? '');
        $password = $_POST['mot_de_passe']  ?? '';
        $confirm  = $_POST['confirmation']  ?? '';
        $db       = Database::get();

        // Validate token
        $s = $db->prepare("
            SELECT * FROM password_resets
            WHERE token = ? AND used = 0 AND expires_at > NOW()
            LIMIT 1
        ");
        $s->bind_param('s', $token);
        $s->execute();
        $reset = $s->get_result()->fetch_assoc();
        $s->close();

        if (!$reset) {
            View::render('auth/reset', [
                'token' => $token,
                'flash' => ['type' => 'error', 'msg' => 'Lien invalide ou expiré.']
            ], 'public');
            return;
        }

        if (strlen($password) < 8) {
            View::render('auth/reset', [
                'token' => $token,
                'flash' => ['type' => 'error', 'msg' => 'Le mot de passe doit contenir au moins 8 caractères.']
            ], 'public');
            return;
        }

        if ($password !== $confirm) {
            View::render('auth/reset', [
                'token' => $token,
                'flash' => ['type' => 'error', 'msg' => 'Les mots de passe ne correspondent pas.']
            ], 'public');
            return;
        }

        // Update password
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $u = $db->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?");
        $u->bind_param('ss', $hashed, $reset['email']);
        $u->execute();
        $u->close();

        // Mark token as used
        $m = $db->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $m->bind_param('s', $token);
        $m->execute();
        $m->close();

        View::render('auth/login', [
            'tab'   => 'login',
            'flash' => ['type' => 'success', 'msg' => 'Mot de passe mis à jour. Vous pouvez vous connecter.']
        ], 'public');
    }
}