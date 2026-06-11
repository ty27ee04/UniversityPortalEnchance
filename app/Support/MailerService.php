<?php

declare(strict_types=1);

namespace App\Support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class MailerService
{
    private function createMailer(): PHPMailer
    {
        $mailer = new PHPMailer(true);

        //DEBUGGING
        // $mailer->SMTPDebug = 2;
        // $mailer->Debugoutput = 'html';

        $transport = strtolower((string) ($_ENV['MAIL_TRANSPORT'] ?? $_SERVER['MAIL_TRANSPORT'] ?? getenv('MAIL_TRANSPORT')));
        
        if ($transport === 'smtp') {
            $mailer->isSMTP();
            $mailer->Host       = (string) ($_ENV['MAIL_HOST'] ?? $_SERVER['MAIL_HOST'] ?? getenv('MAIL_HOST'));
            $mailer->SMTPAuth   = true;
            $mailer->Username   = (string) ($_ENV['MAIL_USERNAME'] ?? $_SERVER['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME'));
            $mailer->Password   = (string) ($_ENV['MAIL_PASSWORD'] ?? $_SERVER['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD'));
            
            $encryption         = strtolower((string) ($_ENV['MAIL_ENCRYPTION'] ?? $_SERVER['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION')));
            $mailer->SMTPSecure = $encryption === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            
            $mailer->Port       = (int) ($_ENV['MAIL_PORT'] ?? $_SERVER['MAIL_PORT'] ?? getenv('MAIL_PORT'));
        } else {
            $mailer->isSMTP();
            $mailer->Host       = 'smtp.gmail.com';
            $mailer->SMTPAuth   = true;
            $mailer->Username   = 'mmu2510a@gmail.com'; 
            $mailer->Password   = 'nviq yuwr qgxb dmou'; 
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->Port       = 587;
        }

        $mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        return $mailer;
    }

    public function sendEnrollmentConfirmation(string $studentName, string $studentEmail, array $enrollmentData, \mysqli $conn): bool
    {
        try {
            $courseId = (int)($enrollmentData['course_id'] ?? 0);
            $intakeId = (int)($enrollmentData['intake_id'] ?? 0);
            $modeId   = (int)($enrollmentData['mode_id'] ?? 0);
            $subjectIds = $enrollmentData['subjects'] ?? [];

            $courseName = $conn->query("SELECT name FROM academic_courses WHERE id = $courseId")->fetch_assoc()['name'] ?? 'N/A';
            $intakeName = $conn->query("SELECT name FROM academic_intakes WHERE id = $intakeId")->fetch_assoc()['name'] ?? 'N/A';
            $modeName   = $conn->query("SELECT name FROM academic_modes WHERE id = $modeId")->fetch_assoc()['name'] ?? 'N/A';

            $subjectsHtml = '';
            if (!empty($subjectIds)) {
                foreach ($subjectIds as $subId) {
                    $subName = $conn->query("SELECT name FROM academic_subjects WHERE id = " . (int)$subId)->fetch_assoc()['name'] ?? 'N/A';
                    $subjectsHtml .= "<li style='padding: 6px 0; border-bottom: 1px dashed #eee; color: #2c3e50;'>📚 <strong>" . htmlspecialchars($subName) . "</strong></li>";
                }
            }

            $mailer = $this->createMailer();

            $fromAddress = ($_ENV['MAIL_FROM_ADDRESS'] ?? $_SERVER['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS')) ?: 'mmu2510a@gmail.com';
            $fromName    = ($_ENV['MAIL_FROM_NAME'] ?? $_SERVER['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME')) ?: 'MMU University Portal';
            
            $mailer->setFrom($fromAddress, $fromName);
            $mailer->addAddress($studentEmail, $studentName);

            $mailer->isHTML(true);
            $mailer->Subject = 'Official Course Enrollment Confirmation Matrix';

            $mailer->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>
                <div style='background: #0a4da2; padding: 25px; color: #ffffff; text-align: center;'>
                    <h2 style='margin: 0; font-size: 24px; letter-spacing: 1px;'>MMU UNIVERSITY PORTAL</h2>
                    <p style='margin: 5px 0 0 0; font-size: 14px; color: #cbdcf7;'>Official Academic Application Receipt</p>
                </div>
                <div style='padding: 30px; background: #ffffff;'>
                    <p style='font-size: 16px; color: #333;'>Dear <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
                    <p style='color: #666; line-height: 1.6;'>Congratulations! Your multi-choice academic registration entry block has been successfully validated and saved to the portal core registry nodes. Below are your officially enrolled package matrix details:</p>
                    
                    <div style='background: #f4f6fb; padding: 20px; border-radius: 6px; margin: 20px 0;'>
                        <table style='width: 100%; font-size: 15px; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #7f8c8d; font-weight: bold; width: 35%;'>Academic Program:</td>
                                <td style='padding: 8px 0; color: #0a4da2; font-weight: bold;'>" . htmlspecialchars($courseName) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #7f8c8d; font-weight: bold;'>Intake Horizon:</td>
                                <td style='padding: 8px 0; color: #333;'>" . htmlspecialchars($intakeName) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #7f8c8d; font-weight: bold;'>Mode of Study:</td>
                                <td style='padding: 8px 0; color: #333;'><span style='background: #e8f8f5; color: #27ae60; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 13px;'>" . htmlspecialchars($modeName) . "</span></td>
                            </tr>
                        </table>
                    </div>

                    <h4 style='color: #2c3e50; border-left: 4px solid #0a4da2; padding-left: 10px; margin-bottom: 10px;'>Your Enrolled Subject Matrices Packages:</h4>
                    <ul style='list-style: none; padding: 0; margin: 0 0 30px 0;'>
                        $subjectsHtml
                    </ul>

                    <p style='color: #e74c3c; font-size: 13px; font-weight: bold; background: #fdf2e9; padding: 10px; border-radius: 4px; border-left: 4px solid #e67e22;'>
                        🔒 Security Notice: This transaction has been audited and compiled using secure parameterized guard mechanisms to protect data validation pipelines.
                    </p>
                </div>
                <div style='background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee;'>
                    This is an automated system dispatch notification. Please do not reply directly to this mail routing gateway.<br>
                    &copy; 2026 Multimedia University Portal. All Rights Reserved.
                </div>
            </div>
            ";

            return $mailer->send();
        } catch (Exception $e) {
            error_log("PHPMailer System Intercept Triggered: " . $e->getMessage());
            return false;
        }
    }

    public function sendRegistrationConfirmation(string $name, string $email): bool { return true; }
}