<?php
require_once __DIR__ . '/../models/Inscription.php';
require_once __DIR__ . '/../models/Evenement.php';

class InscriptionController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($eventId)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $user = $_SESSION['user'];
        $nom = $user['nom'] . ' ' . $user['prenom'];
        $email = $user['email'];

        $inscriptionModel = new Inscription($this->pdo);
        $result = $inscriptionModel->create($eventId, $nom, $email);

        if ($result) {
            // Success
            header('Location: index.php?page=user_events&success=registered');
        } else {
            // Already registered or error
            header('Location: index.php?page=user_events&error=already_registered');
        }
        exit();
    }

    public function cancel($inscriptionId)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $user = $_SESSION['user'];
        $email = $user['email'];

        $inscriptionModel = new Inscription($this->pdo);
        $result = $inscriptionModel->cancel($inscriptionId, $email);

        if ($result) {
            header('Location: index.php?page=user_events&success=canceled');
        } else {
            header('Location: index.php?page=user_events&error=cancel_failed');
        }
        exit();
    }
}
