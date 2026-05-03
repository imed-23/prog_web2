# Rapport de Tests — Gaming Campus

**Date**: 30 avril 2026
**Méthode**: Tests HTTP automatisés (cURL), audit statique du code par agents en parallèle, vérification des logs serveur PHP, captures d'écran de l'application en cours d'exécution.
**Périmètre**: Toutes les pages publiques, le flux d'authentification, l'espace membre, l'espace admin, les formulaires, les requêtes SQL.

---

## 1. RÉSUMÉ EXÉCUTIF

L'application **démarre, se rend correctement et accepte les connexions** — toutes les pages publiques renvoient HTTP 200 sans erreur PHP visible. Cependant, les tests ont révélé **plusieurs bugs critiques cachés** :

| Sévérité | Nombre | Impact |
|----------|--------|--------|
| 🔴 **Critique** | 3 | Site partiellement non-fonctionnel + faille de sécurité majeure |
| 🟠 **Élevée** | 2 | Données erronées ou injection potentielle |
| 🟡 **Moyenne** | 5 | UX dégradée, fonctionnalités incomplètes |
| 🔵 **Faible** | 6 | Polish, cohérence, bonnes pratiques |

**Le plus urgent** : ~25 requêtes SQL utilisent la syntaxe MySQL `"texte"` (guillemets doubles) pour des chaînes alors que la base est PostgreSQL. PostgreSQL interprète les guillemets doubles comme des **identifiants de colonnes**, donc ces requêtes échouent silencieusement. Le bloc `try/catch` les avale sans rien afficher à l'utilisateur, mais les pages se vident.

---

## 2. CE QUI MARCHE ✅

- ✅ Démarrage du serveur PHP (port 5000) et connexion à PostgreSQL
- ✅ Toutes les pages publiques renvoient HTTP 200 sans erreur
- ✅ Page d'accueil, en-tête, pied de page, navigation
- ✅ Page de connexion : authentification admin et capitaine fonctionne
- ✅ Inscription : création de compte avec rôle `capitaine`, mot de passe haché en bcrypt
- ✅ Validation côté serveur des champs (pseudo, email, mot de passe, CGU)
- ✅ Vérification d'unicité (pseudo + email)
- ✅ Protection CSRF présente sur tous les POST testés
- ✅ Régénération de l'ID de session après connexion
- ✅ Cookies `HttpOnly` + `SameSite=Lax`
- ✅ Déconnexion (`deconnexion.php` et `logout.php`)
- ✅ Redirection vers la page de login pour les pages protégées
- ✅ Formulaire de contact accepte un POST avec CSRF (mais ne persiste rien — voir §4)
- ✅ Pages statiques : Blog, Article, FAQ, Événements (rendu)

---

## 3. BUGS CRITIQUES 🔴

### 🔴 BUG #1 — Requêtes SQL invalides en PostgreSQL (guillemets doubles)

**Description** : Le code utilise `"a-venir"`, `"confirmee"`, `"admin"`, etc. dans des requêtes SQL. C'est valide en MySQL mais en PostgreSQL, `"..."` désigne un **identifiant de colonne**, pas une chaîne. Résultat : `ERROR: column "a-venir" does not exist`.

**Preuves dans les logs serveur** :
```
[ADMIN DASHBOARD] SQLSTATE[42703]: column "a-venir" does not exist
[ADMIN RESERVATIONS STATS] SQLSTATE[42703]: column "confirmee" does not exist
[ADMIN INSCRIPTIONS STATS] SQLSTATE[42703]: column "capitaine" does not exist
[TOURNOIS LIST] SQLSTATE[42703]: column "annulee" does not exist
[CLASSEMENT LIST] SQLSTATE[42703]: column "confirmee" does not exist
```

**Impact concret observé** :
- **Page Tournois** : affiche "0 tournois trouvés" même quand il y en a en base (cf. capture)
- **Dashboard Admin** : compteurs "Tournois actifs" et "Tournois terminés" toujours à 0
- **Classement** : aucun utilisateur affiché
- **Réservations Admin** : compteurs par statut tous à 0
- **Création de tournoi (admin)** : échoue avec "Impossible de créer le tournoi" (le `INSERT` contient `"a-venir"`)
- **Devenir capitaine / Réserver une équipe** sur tournoi-detail : échoue silencieusement

**Fichiers concernés (lignes spécifiques)** :
| Fichier | Ligne(s) | Extrait |
|---|---|---|
| `pages/admin/dashboard.php` | 16, 18 | `WHERE statut IN ("a-venir", "en-cours")` |
| `pages/admin/inscriptions.php` | 31, 68, 115-117 | `WHERE role = "admin"` etc. |
| `pages/admin/reservations.php` | 41-43 | `WHERE statut = "confirmee"` etc. |
| `pages/admin/tournois.php` | 102 | `VALUES (..., "a-venir")` |
| `pages/admin/utilisateurs.php` | 28, 63 | `WHERE role = "admin"` |
| `pages/classement.php` | 24, 25 | `CASE WHEN r.statut = "confirmee"` |
| `pages/tournoi-detail.php` | 56, 95, 108, 130 | `role = "capitaine"`, `<> "annulee"`, etc. |
| `pages/tournois.php` | 56 | `WHERE statut <> "annulee"` |

**Correction recommandée** : remplacer tous les `"..."` par des `'...'` dans les chaînes SQL. Compatible MySQL ET PostgreSQL.

---

### 🔴 BUG #2 — Contournement d'autorisation : N'importe quel utilisateur connecté accède à l'admin

**Description** : Toutes les pages `/pages/admin/*.php` appellent `gc_require_login()` mais **jamais** `gc_require_admin()`. Un simple visiteur ou capitaine peut donc :
- Voir le dashboard admin
- Lister tous les utilisateurs (avec emails)
- Créer/supprimer des tournois
- Promouvoir/rétrograder des utilisateurs (changer les rôles)
- Confirmer/annuler des réservations
- Supprimer des comptes

**Preuve test** (utilisateur `test_user` avec rôle `capitaine` créé puis connecté) :
```
[200] /pages/admin/dashboard.php       (52 termes admin dans la page)
[200] /pages/admin/utilisateurs.php    (30 termes admin)
[200] /pages/admin/tournois.php        (22 termes admin)
[200] /pages/admin/reservations.php    (42 termes admin)
[200] /pages/admin/inscriptions.php    (54 termes admin)
```

Une fonction `gc_require_admin()` existe pourtant déjà dans `assets/php/config/auth.php` (lignes 49-58) — elle n'est juste pas appelée.

**Fichiers concernés** :
- `pages/admin/dashboard.php` ligne 3
- `pages/admin/inscriptions.php` ligne 3
- `pages/admin/reservations.php` ligne 3
- `pages/admin/tournois.php` ligne 3
- `pages/admin/utilisateurs.php` ligne 3
- `assets/php/components/header-admin.php` ligne 3

**Correction recommandée** : remplacer `gc_require_login(...)` par `gc_require_admin(...)` dans chacun de ces fichiers.

---

### 🔴 BUG #3 — Création de tournoi par l'admin échoue à 100%

**Description** : Conséquence directe du BUG #1. Test reproduit : connexion admin OK, soumission du formulaire avec données valides, le serveur répond `Impossible de créer le tournoi`. Le `INSERT` contient `"a-venir"` (ligne 102 de `pages/admin/tournois.php`).

**Impact** : aucun tournoi ne peut être créé via l'interface — c'est pourtant la fonctionnalité principale du back-office.

---

## 4. BUGS ÉLEVÉS 🟠

### 🟠 BUG #4 — Risque d'injection SQL dans `pages/admin/inscriptions.php`

**Description** : Tri dynamique `ORDER BY $sortBy $sortOrder` (ligne 144) construit par concaténation. `$sortBy` est filtré par une whitelist (bien), mais le pattern de concaténation reste dangereux : si la whitelist change, l'injection devient possible.

**Correction recommandée** : utiliser un `match`/`switch` qui retourne une chaîne SQL fixe par cas, au lieu de concaténer la variable.

---

### 🟠 BUG #5 — Le formulaire de contact ne persiste rien

**Description** : `pages/contact.php` valide le formulaire et affiche "Message envoyé avec succès", mais ne fait **aucune** insertion en base ni envoi d'email. C'est de la simulation pure.

**Impact** : utilisateurs croient avoir contacté l'équipe, personne ne reçoit le message.

**Correction recommandée** : créer une table `contacts` et y insérer ; ou intégrer un envoi d'email (PHPMailer/SMTP).

---

## 5. BUGS MOYENS 🟡

### 🟡 BUG #6 — Filtres serveur sur `tournois.php` court-circuités par le JS

`pages/tournois.php` accepte des filtres GET (`?jeu=valorant&statut=a-venir`), mais `js/tournois-filtres.js` fait `e.preventDefault()` et applique les filtres **uniquement côté client** sur le DOM. Conséquence : les filtres serveurs ne sont jamais utilisés en pratique (et même s'ils l'étaient, ils sont cassés par le BUG #1).

### 🟡 BUG #7 — Chemins d'upload d'avatar incohérents

- Inscription : `__DIR__ . '/../../../uploads/avatars/'` (`assets/php/traitement/inscription.trait.php` L136)
- Espace membre : `__DIR__ . '/../uploads/avatars/'` (`pages/espace-membre.php` L77)

Selon l'arborescence, ces deux chemins ne pointent **pas** vers le même dossier physique. Un avatar uploadé à l'inscription peut donc ne pas être affichable depuis le profil et vice-versa.

### 🟡 BUG #8 — Case "Se souvenir de moi" inutile

Présente dans le HTML de `pages/connexion.php` (L154) mais le serveur ne la lit jamais. La session expire à la fermeture du navigateur quoi qu'il arrive.

### 🟡 BUG #9 — Lien "Mot de passe oublié ?" cassé

`pages/connexion.php` L157 → `href="#"`. Aucun flow de reset de mot de passe.

### 🟡 BUG #10 — Cookie de session sans flag `Secure`

`assets/php/config/auth.php` ne définit pas `cookie_secure => true`. En production HTTPS, le cookie peut être transmis en HTTP par accident (vol de session via MITM).

---

## 6. BUGS FAIBLES 🔵

### 🔵 BUG #11 — Favicon manquant
Logs : `[404]: GET /favicon.ico - No such file or directory`. À ajouter à la racine.

### 🔵 BUG #12 — `pages/profil.php` accessible sans authentification
Renvoie 200 même sans cookie de session. À vérifier si c'est intentionnel (profil public ?) ou un oubli.

### 🔵 BUG #13 — Doublon `deconnexion.php` / `logout.php`
Deux endpoints de déconnexion font la même chose. Cosmétique mais déroutant.

### 🔵 BUG #14 — Token CSRF unique pour toute la session
Bonne pratique : régénérer le token après chaque action POST sensible (rotation par formulaire).

### 🔵 BUG #15 — Statut "annulee" compté comme "défaite" dans le classement
`pages/classement.php` L25 : `SUM(CASE WHEN r.statut = "annulee" THEN 1 ELSE 0 END) AS defaites`. Une annulation n'est pas une défaite ; séparer ces deux compteurs.

### 🔵 BUG #16 — Avatar : extension dérivée du nom client, pas du MIME réel
`assets/php/traitement/inscription.trait.php` L141. Faible risque (le MIME est vérifié), mais l'idéal est de mapper MIME → extension.

---

## 7. AMÉLIORATIONS POSSIBLES 💡

### Architecture & Code
1. **Centraliser les constantes de statut** : créer un fichier `assets/php/config/enums.php` avec `const STATUTS_TOURNOI = ['a-venir', 'en-cours', 'termine']` etc., réutilisé partout (validation + affichage).
2. **Couche modèle** : extraire les requêtes SQL dans des fonctions `Tournoi::findAll($filters)`, `User::findByEmail($email)` plutôt que mêler SQL et HTML dans chaque page.
3. **Routeur unique** : un seul `index.php` qui dispatche vers des contrôleurs (façon front-controller) au lieu d'un fichier `.php` par URL.
4. **Migrations versionnées** : ajouter un dossier `migrations/` avec un script qui crée le schéma — actuellement le schéma est dans `assets/sql/init.sql` (en MySQL) et personne ne l'exécute.

### Sécurité
5. **Rate limiting** sur `/connexion.php` (anti brute-force)
6. **Politique de mot de passe** : exiger un caractère spécial en plus de majuscule + chiffre
7. **2FA optionnel** pour les admins
8. **Logs d'audit** : tracer qui supprime quoi côté admin
9. **Headers de sécurité** : `X-Frame-Options`, `Content-Security-Policy`, `X-Content-Type-Options`
10. **Réencoder les images uploadées** via GD pour neutraliser les fichiers polyglottes

### UX / Fonctionnalités
11. **Reset mot de passe** par email (lien temporaire)
12. **"Se souvenir de moi"** réellement implémenté (token longue durée)
13. **Notifications/emails** : confirmation d'inscription, confirmation de réservation, statut mis à jour
14. **Pagination serveur** sur les listes (utilisateurs, tournois, réservations) au lieu de tout charger
15. **Recherche full-text** sur les tournois (nom, description, jeu)
16. **Export CSV** des participants par tournoi pour les admins
17. **Page "Mot de passe oublié"** fonctionnelle
18. **Confirmation de suppression** : modale avant `DELETE` côté admin
19. **Internationalisation** : le code est en français figé ; un système i18n permettrait l'EN

### Performance & Tests
20. **Index manquants** : ajouter un index sur `reservations(capitaine_id)` (utilisé dans tous les calculs de stats)
21. **Cache des compteurs** du dashboard (Redis ou simple cache fichier)
22. **Tests automatisés** : aucun test n'existe ; ajouter PHPUnit pour la logique d'auth/validation et un harnais de tests E2E (Playwright/Cypress)
23. **CI/CD** : GitHub Actions pour lancer les tests à chaque push

### Déploiement / Devops
24. **Variables d'environnement documentées** dans un `.env.example`
25. **Healthcheck endpoint** `/health.php` pour le monitoring
26. **Logs structurés** (JSON) au lieu de `error_log` brut

---

## 8. PRIORISATION RECOMMANDÉE

**À faire en premier (bloquant)** :
1. BUG #1 — corriger les guillemets MySQL→PostgreSQL (1h, ~25 occurrences)
2. BUG #2 — utiliser `gc_require_admin()` sur les pages admin (15 min, 6 fichiers)
3. BUG #5 — soit supprimer le formulaire contact, soit le rendre fonctionnel

**À faire ensuite (rapide & impactant)** :
4. BUG #4 — sécuriser le ORDER BY de `inscriptions.php`
5. BUG #6 — décider du modèle de filtre (tout serveur OU tout client) sur `tournois.php`
6. BUG #7 — uniformiser le chemin d'upload d'avatar
7. BUG #9 — au minimum cacher/désactiver le lien "mot de passe oublié"

**Polish** : tous les autres bugs faibles + améliorations UX.

---

## 9. ANNEXE — Méthodologie de test

- **22 endpoints HTTP** testés en GET + POST avec/sans cookies de session
- **3 comptes** utilisés : admin pré-existant, visiteur fraîchement inscrit, anonyme
- **2 sous-agents d'exploration** lancés en parallèle pour l'inventaire de fonctionnalités et l'audit de sécurité
- **Logs serveur** PHP analysés pour détecter les erreurs PostgreSQL silencieuses
- **Données de test** (1 tournoi, 1 user) seedées puis nettoyées en BDD pour valider visuellement les bugs

Aucune modification de code n'a été effectuée pendant les tests, conformément à votre demande.
