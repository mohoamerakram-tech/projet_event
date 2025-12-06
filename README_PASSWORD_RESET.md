# 🔐 Guide de la Fonctionnalité "Mot de Passe Oublié"

Ce guide détaille le fonctionnement, la configuration et l'utilisation de la fonctionnalité de réinitialisation de mot de passe.

## 📋 Vue d'ensemble

Le système permet aux utilisateurs de réinitialiser leur mot de passe de manière sécurisée via un lien envoyé par email.

### Flux de travail :
1.  L'utilisateur clique sur "Mot de passe oublié ?" sur la page de connexion.
2.  Il saisit son adresse email.
3.  Si l'email existe, un token unique est généré et envoyé par email via Brevo.
4.  L'utilisateur clique sur le lien reçu.
5.  Il définit un nouveau mot de passe sur une page sécurisée.
6.  Le mot de passe est mis à jour et le token est invalidé.

---

## 🛠️ Configuration Requise

### 1. Base de Données
Le système utilise une table dédiée `password_resets`. Si elle n'existe pas, créez-la avec ce SQL :

```sql
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used TINYINT(1) DEFAULT 0,
    INDEX (email),
    INDEX (token)
);
```

### 2. Configuration Email (Brevo)
Le fichier `.env` à la racine doit contenir votre clé API Brevo valide :

```env
BREVO_API_KEY=xkeysib-votre-cle-api-longue...
```

### 3. Dépendances
Les dépendances suivantes doivent être installées via Composer :
*   `sendinblue/api-v3-sdk`
*   `guzzlehttp/guzzle`

Si besoin, réinstallez-les :
```bash
composer require sendinblue/api-v3-sdk guzzlehttp/guzzle
```

---

## 🧪 Tests

### Environnement de Développement

1.  Assurez-vous que votre serveur local tourne (XAMPP/Apache).
2.  Accédez à `http://localhost/projet_event/public/index.php?page=login`.
3.  Cliquez sur le lien "Mot de passe oublié ?".
4.  Entrez votre email personnel pour tester.
5.  Vérifiez votre boîte mail (et les spams).

### Dépannage
*   **Email non reçu ?** Vérifiez le fichier `.env` et assurez-vous que la clé API est correcte (clé `xkeysib-`).
*   **Erreur 500 ?** Vérifiez les logs PHP (`error.log` de XAMPP).
*   **Lien invalide ?** Le token expire après 30 minutes. Recommencez la procédure.

---

## 🔒 Sécurité

*   **Tokens Uniques** : Générés avec `random_bytes(32)` (cryptographiquement sécurisé).
*   **Hashage** : Les mots de passe sont hashés avec `password_hash()` (Bcrypt).
*   **Anti-Spam** : Limite de 3 demandes par 15 minutes par email.
*   **Anti-Énumération** : Le système répond toujours avec le même message, que l'email existe ou non, pour ne pas révéler quels emails sont inscrits.

---

## 📁 Structure des Fichiers

*   `controllers/ForgotPasswordController.php` : Logique principale.
*   `models/PasswordReset.php` : Gestion des tokens en base de données.
*   `services/MailService.php` : Envoi des emails via l'API Brevo.
*   `views/auth/forgot_password.php` : Formulaire de demande.
*   `views/auth/reset_password.php` : Formulaire de changement de mot de passe.
