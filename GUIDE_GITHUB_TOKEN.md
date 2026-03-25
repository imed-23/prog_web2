# 🔐 Guide : Créer un Personal Access Token GitHub

## ❓ Pourquoi avez-vous besoin d'un Personal Access Token (PAT) ?

Depuis août 2021, GitHub n'accepte plus les mots de passe classiques pour les opérations Git (push, pull, clone de dépôts privés). Vous devez utiliser un **Personal Access Token** à la place du mot de passe.

---

## 📋 Guide Étape par Étape pour Créer un Token

### Étape 1 : Accéder à GitHub Settings

1. Connectez-vous à votre compte GitHub (https://github.com)
2. Cliquez sur votre **photo de profil** en haut à droite
3. Dans le menu déroulant, cliquez sur **Settings** (Paramètres)

### Étape 2 : Naviguer vers Developer Settings

1. Dans la barre latérale gauche, **descendez tout en bas**
2. Cliquez sur **Developer settings** (Paramètres développeur)

### Étape 3 : Accéder aux Personal Access Tokens

1. Dans le menu de gauche, cliquez sur **Personal access tokens**
2. Cliquez sur **Tokens (classic)** — *Important : utilisez les tokens classiques*

### Étape 4 : Générer un Nouveau Token

1. Cliquez sur le bouton **Generate new token** (Générer un nouveau token)
2. Sélectionnez **Generate new token (classic)**

### Étape 5 : Authentification

GitHub vous demandera probablement de **confirmer votre mot de passe** pour des raisons de sécurité. Entrez votre mot de passe GitHub.

### Étape 6 : Configurer le Token

1. **Note** : Donnez un nom descriptif à votre token (ex : "Token pour prog_web2" ou "Token laptop développement")
   - Ce nom vous aide à identifier le token plus tard

2. **Expiration** : Choisissez une durée d'expiration
   - Pour un usage régulier : **90 days** (90 jours) ou **No expiration** (pas d'expiration)
   - ⚠️ Si vous choisissez "No expiration", soyez très prudent avec ce token

3. **Select scopes** : Cochez les permissions nécessaires
   - ✅ **Cochez la case `repo`** (Accès complet aux dépôts privés)
   - Cette permission est **obligatoire** pour push vers GitHub
   - Vous pouvez laisser les autres cases décochées pour l'instant

### Étape 7 : Générer le Token

1. Descendez en bas de la page
2. Cliquez sur le bouton vert **Generate token**

### Étape 8 : ⚠️ COPIER LE TOKEN IMMÉDIATEMENT

**🚨 TRÈS IMPORTANT 🚨**

Une fois le token généré, GitHub affiche une page avec votre token sous forme d'une longue chaîne de caractères commençant par `ghp_`.

**Exemple** : `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

➡️ **COPIEZ CE TOKEN IMMÉDIATEMENT** et sauvegardez-le dans un endroit sûr

⚠️ **ATTENTION** : GitHub ne vous montrera ce token **QU'UNE SEULE FOIS**. Si vous fermez la page sans le copier, vous devrez créer un nouveau token.

---

## 🔧 Comment Utiliser le Token

### Option 1 : Utiliser le Token lors du Push (Méthode Simple)

Quand vous faites `git push` et que Git vous demande vos identifiants :

```bash
Username: votre-nom-utilisateur-github
Password: ghp_votre_token_ici
```

⚠️ **N'utilisez PAS votre mot de passe GitHub**, utilisez le **token** comme mot de passe.

### Option 2 : Configurer le Token dans l'URL Remote (Méthode Permanente)

Pour ne pas avoir à entrer le token à chaque fois :

```bash
# 1. Vérifier l'URL actuelle de votre remote
git remote -v

# 2. Remplacer par l'URL avec le token
git remote set-url origin https://ghp_VOTRE_TOKEN@github.com/imed-23/prog_web2.git
```

Remplacez `ghp_VOTRE_TOKEN` par votre vrai token.

**Exemple complet** :
```bash
git remote set-url origin https://ghp_abc123def456xyz789@github.com/imed-23/prog_web2.git
```

Après cette configuration, vous n'aurez plus besoin d'entrer vos identifiants lors des `git push`.

### Option 3 : Utiliser Git Credential Manager (Recommandé)

Sur Linux/Mac :
```bash
# Stocker le token en mémoire pour 1 heure
git config --global credential.helper 'cache --timeout=3600'

# Ou stocker le token de façon permanente (moins sécurisé)
git config --global credential.helper store
```

Ensuite, lors du prochain `git push`, entrez :
- Username : votre nom d'utilisateur GitHub
- Password : votre token

Git se souviendra du token pour les prochaines fois.

---

## 🔒 Sécurité - Bonnes Pratiques

### ✅ À FAIRE

- ✅ Traitez le token comme un mot de passe
- ✅ Ne partagez jamais votre token publiquement
- ✅ Stockez-le dans un gestionnaire de mots de passe sécurisé
- ✅ Donnez à chaque token un nom descriptif
- ✅ Définissez une date d'expiration raisonnable
- ✅ Donnez uniquement les permissions nécessaires

### ❌ À NE PAS FAIRE

- ❌ Ne committez JAMAIS un token dans votre code source
- ❌ Ne publiez pas votre token sur Slack, Discord, forums, etc.
- ❌ N'ajoutez pas le token dans vos fichiers de configuration versionnés
- ❌ Ne partagez pas le même token entre plusieurs personnes
- ❌ Ne stockez pas le token en texte brut sur votre bureau

---

## 🆘 Problèmes Fréquents

### "remote: Support for password authentication was removed"

**Problème** : Vous utilisez votre mot de passe GitHub au lieu du token.

**Solution** : Utilisez le Personal Access Token à la place du mot de passe.

### "Authentication failed"

**Vérifiez** :
1. Le token est bien copié en entier (commence par `ghp_`)
2. La permission `repo` est bien cochée
3. Le token n'a pas expiré

### "Permission denied"

**Solution** : Retournez sur GitHub → Settings → Developer settings → Tokens, et vérifiez que la case `repo` est cochée pour votre token.

### J'ai perdu mon token

**Solution** : Vous ne pouvez pas récupérer un token perdu. Vous devez :
1. Supprimer l'ancien token (pour la sécurité)
2. Créer un nouveau token en suivant les étapes ci-dessus

---

## 🔗 Liens Utiles

- [Documentation officielle GitHub sur les PAT](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)
- [Gestion des tokens existants](https://github.com/settings/tokens)

---

## 📝 Résumé Rapide

1. GitHub → **Settings** → **Developer settings**
2. **Personal access tokens** → **Tokens (classic)** → **Generate new token**
3. Note : "Token prog_web2"
4. ✅ Cocher **`repo`**
5. **Generate token**
6. 🚨 **COPIER LE TOKEN IMMÉDIATEMENT**
7. Utiliser le token comme mot de passe lors du `git push`

---

**💡 Note importante** : Je ne peux pas générer ou vous donner directement un token. Vous devez le créer vous-même sur votre compte GitHub en suivant ces étapes. Le token est personnel et lié à votre compte.

Si vous avez des questions sur une étape spécifique, n'hésitez pas à demander !
