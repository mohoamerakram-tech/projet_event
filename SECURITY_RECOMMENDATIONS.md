# 🛡️ Recommandations de Sécurité & Guide de Production

Ce document détaille les mesures de sécurité implémentées et les actions requises pour une mise en production sécurisée.

## ✅ Mesures Implémentées

### 1. Protection des Tokens
*   **Cryptographie** : Utilisation de `random_bytes(32)` pour générer des tokens à haute entropie (indevinables).
*   **Expiration** : Les tokens expirent automatiquement après 30 minutes.
*   **Usage Unique** : Une fois utilisé, un token est invalidé.
*   **Nettoyage** : Une méthode `cleanExpiredTokens()` permet de supprimer les tokens obsolètes.

### 2. Protection Contre l'Énumération
Pour empêcher un attaquant de savoir si une adresse email existe dans votre base :
*   **Message Identique** : Que l'email existe ou non, le système affiche toujours : *"Si cette adresse email existe, vous recevrez un lien..."*.
*   **Timing Attack Protection** : Si l'email n'existe pas, un délai aléatoire (`usleep`) est ajouté pour simuler le temps d'envoi d'un email.

### 3. Rate Limiting (Anti-Spam)
*   Via `LoginRateLimiter`, les demandes sont limitées à **3 par tranche de 15 minutes** par adresse email.
*   Cela empêche le spam d'emails et la surcharge du serveur SMTP.

### 4. Validation des Mots de Passe
*   Longueur minimale de 8 caractères forcée côté serveur.
*   Confirmation du mot de passe obligatoire.
*   Indicateur visuel de force du mot de passe côté client (JavaScript).

---

## 🚨 Actions Requises pour la Production

### 1. HTTPS Obligatoire
*   Le site **DOIT** être servi en HTTPS.
*   Sans HTTPS, les tokens envoyés dans les liens peuvent être interceptés sur le réseau.
*   Utilisez Let's Encrypt pour un certificat gratuit.

### 2. Configuration SMTP Sécurisée
*   Ne stockez **JAMAIS** vos clés API ou mots de passe SMTP directement dans le code.
*   Utilisez toujours le fichier `.env` (qui ne doit pas être commité sur Git).
*   Assurez-vous que votre clé Brevo a les permissions minimales nécessaires.

### 3. Tâche Planifiée (Cron Job)
Pour éviter que la table `password_resets` ne grossisse indéfiniment, configurez une tâche CRON qui s'exécute chaque jour :

```bash
# Exemple de cron job (tous les jours à 4h00 du matin)
0 4 * * * /usr/bin/php /chemin/vers/projet_event/scripts/cleanup_tokens.php
```

Créer le script `cleanup_tokens.php` :
```php
<?php
require_once __DIR__ . '/../models/PasswordReset.php';
$model = new PasswordReset();
$model->cleanExpiredTokens();
```

---

## 📝 Checklist de Déploiement

- [ ] La table `password_resets` est créée en production.
- [ ] Le fichier `.env` est configuré avec la clé API de production avec `xkeysib-` non partagée.
- [ ] HTTPS est actif.
- [ ] Le dossier `vendor/` est installé (ou uploadé).
- [ ] Les messages d'erreur détaillés (`display_errors`) sont désactivés dans `php.ini`.
