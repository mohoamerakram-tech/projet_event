<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class MailService
{
    private $apiInstance;

    public function __construct()
    {
        // Charger les variables d'environnement locales si nécessaire
        $this->loadEnv();

        $apiKey = getenv('BREVO_API_KEY');
        if (!$apiKey && isset($_ENV['BREVO_API_KEY'])) {
            $apiKey = $_ENV['BREVO_API_KEY'];
        }

        if (!$apiKey) {
            error_log("Brevo API Key manquante !");
        }

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $apiKey);

        $this->apiInstance = new TransactionalEmailsApi(new Client(), $config);
    }

    private function loadEnv()
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }

    public function sendPasswordResetEmail($email, $resetLink)
    {

        $htmlContent = "
            <div style='font-family:Arial;padding:20px;color:#333'>
                <h2>Réinitialisation de votre mot de passe</h2>
                <p>Une demande de réinitialisation de votre mot de passe a été effectuée.</p>
                <p>Pour le changer, cliquez sur le bouton ci-dessous :</p>
                <a href='$resetLink' 
                    style='display:inline-block;padding:12px 20px;background:#4667ff;color:white;
                           text-decoration:none;border-radius:6px;margin-top:10px'>
                    Réinitialiser mon mot de passe
                </a>
                <p style='margin-top:20px;font-size:14px;color:#666'>
                    Si vous n’êtes pas à l’origine de cette demande, ignorez cet email.
                </p>
            </div>
        ";

        $emailPayload = new SendSmtpEmail([
            'sender' => ['email' => "saif.h.work@gmail.com", 'name' => "ENSA Events"],
            'to' => [['email' => $email]],
            'subject' => "Réinitialisation de votre mot de passe",
            'htmlContent' => $htmlContent
        ]);

        try {
            $this->apiInstance->sendTransacEmail($emailPayload);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur envoi email reset password : " . $e->getMessage());
            return false;
        }
    }
}
