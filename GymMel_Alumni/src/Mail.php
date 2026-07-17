<?php
/**
 * Mail class for sending SMTP mails.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2026 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2026-07-15
 * @todo Replace php mail function with new class at "forgetPassword"
 */


namespace src;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private PHPMailer $mailer;

    public function __construct()
    {
        include dirname(__DIR__, 1) . '/constants.php';

        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = $mail_host;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $mail_username;
        $this->mailer->Password   = $mail_password;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $mail_port;
        $this->mailer->CharSet     = 'UTF-8';
        $this->mailer->setFrom($mail_from, 'Gymnasium Melle | Alumni-System');
        $this->mailer->addReplyTo('Gymnasium-Melle@t-online.de', 'Gymnasium Melle');
    }

    public function addAddress(string $email, string $name): bool
    {
        return $this->mailer->addAddress($email, $name);
    }

    public function addSubject(string $subject): bool
    {
        return $this->mailer->Subject = $subject;
    }

    public function addBody(string $body, bool $isHtml): bool
    {
        $this->mailer->isHTML($isHtml);
        return $this->mailer->Body = $body;
    }

    public function addAltBody(string $body): bool
    {
        return $this->mailer->AltBody = $body;
    }

    public function send(): bool
    {
        // Only sent 10 emails at maximum per session to prevent abuse
        if (!isset($_SESSION['mail_count'])) {
            $_SESSION['mail_count'] = 0;
        }
        if (isset($_SESSION['mail_count']) && $_SESSION['mail_count'] <= 10) {
            $_SESSION['mail_count']++;
            return $this->mailer->send();
        } else {
            return false;
        }
    }
}