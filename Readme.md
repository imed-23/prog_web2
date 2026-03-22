# 🎮 Cahier des Charges - Plateforme de Tournois Gaming Campus

---

## 🎯 Objectif du Projet

Créer un site web dynamique où n'importe qui sur le campus peut consulter les tournois à venir et inscrire son équipe. Le site doit être **beau**, **rapide** et surtout **facile à utiliser sur mobile** (car tout le monde s'inscrit depuis son téléphone à la cafétéria).

---

## 👥 Utilisateurs Cibles & Besoins Fonctionnels

### 1️⃣ Visiteurs (Public - Non Connectés)

- ✅ Voir les dates des tournois
- ✅ Voir les jeux proposés
- ✅ Voir les places disponibles/restantes
- ✅ Accéder à toutes les informations publiques sur les événements
- ✅ Consulter le catalogue des tournois sans authentification

### 2️⃣ Capitaines (Membres Connectés)

- ✅ Créer un compte personnel
- ✅ Se connecter à son espace membre
- ✅ Voir les dates des jeux
- ✅ Voir les places disponibles
- ✅ Voir les places déjà réservées/occupées
- ✅ Réserver des places pour son équipe
- ✅ Annuler une réservation existante
- ✅ Accéder à des informations supplémentaires réservées aux membres

### 3️⃣ Administrateurs (BDE - Gestion Complète)

**Gestion de base :**
- ✅ Créer un compte administrateur
- ✅ Se connecter à l'interface d'administration
- ✅ Voir les dates des jeux
- ✅ Voir les places disponibles
- ✅ Voir les places réservées
- ✅ Annuler n'importe quelle réservation
- ✅ Accéder à des informations détaillées réservées aux admins

**Gestion des tournois :**
- ✅ Ajouter de nouvelles dates de jeux
- ✅ Supprimer des dates de jeux existantes
- ✅ Ajouter des places supplémentaires à un tournoi
- ✅ Supprimer des places d'un tournoi

**Gestion des utilisateurs :**
- ✅ Ajouter de nouveaux capitaines manuellement
- ✅ Supprimer des capitaines existants
- ✅ Voir la liste complète des capitaines
- ✅ Voir la liste des visiteurs inscrits
- ✅ Voir la liste des administrateurs
- ✅ Ajouter de nouveaux administrateurs
- ✅ Supprimer des administrateurs existants

**Gestion des réservations :**
- ✅ Voir toutes les réservations en détail
- ✅ Voir l'état global des places disponibles
- ✅ Voir l'état global des places réservées

---

## 🎨 Charte Graphique & Ambiance Gaming

### Couleurs & Thème Principal

| Élément | Description |
|---------|-------------|
| **Palette principale** | Noir et rouge compétition |
| **Degrés modernes** | Utilisation de gradients fluides noir/rouge |
| **Cartes arrondies** | Border-radius généreux (12-16px minimum) |
| **Glassmorphism** | Effet verre translucide avec backdrop-filter |
| **Bordures lumineuses** | Contours glow/neon autour des éléments clés |
| **Effet glow** | Lueur douce autour des cartes et boutons |
| **Cartes avec neon** | Éléments néon pour mettre en valeur les actions importantes |

### Effets Interactifs Requis

| Effet | Description |
|-------|-------------|
| `.hover` | Feedback visuel au survol |
| `.click` | Animation tactile/visuelle au clic |
| `.scroll` | Défilement fluide et agréable |
| `.transition` | Transitions douces entre états |
| `.animation` | Micro-animations pour le feedback utilisateur |
| `.parallax` | Effet de profondeur sur le scroll (léger) |
| `.3d` | Effets de profondeur subtils (transform 3D) |
| `.neon` | Textes et bordures néon |
| `.glow` | Lueurs douces autour des éléments interactifs |
| `.gradient` | Degrés modernes dans les backgrounds et boutons |
| `.shadow` | Ombres portées pour la profondeur |
| `.border` | Bordures stylisées avec glow |
| `.background` | Arrière-plans dynamiques avec gradients |
| `.text` | Typographie gaming avec effets néon optionnels |
| `.icon` | Icônes modernes avec animations |
| `.image` | Affichage optimisé des visuels jeux |
| `.video / .audio` | Support média si nécessaire |
| `.effet de particules` | Particules subtiles en arrière-plan (optionnel) |
| `.button glow` | Boutons avec lueur au survol/clic |
| `.scroll smooth` | Défilement fluide sur toute la page |

---

## 📱 Sections du Site à Développer

### 1. Tournois en Cours
- 🎮 Affichage du nom du jeu
- 📅 Date et heure précises
- 👥 Nombre de participants actuels
- 🟢 Statut visuel (en cours/à venir/terminé)

### 2. Classement (Leaderboard)
- 📊 Tableau de classement stylisé
- 🥇 Badges spéciaux pour le Top 3 (Or, Argent, Bronze)
- 🏅 Badges distinctifs pour les autres rangs
- 📈 Statistiques par joueur/équipe

### 3. Prochains Matchs
- ⏱️ Countdown timer dynamique
- 📋 Liste des matchs à venir avec horaires
- 🆚 Informations sur les équipes opposées

### 4. Profil des Joueurs
- 🖼️ Avatar personnalisable
- 📊 Statistiques de jeu détaillées
- 📜 Historique des participations
- ⭐ Rang/level actuel
- 🏆 Trophées et accomplissements

### 5. Contact
- 📧 Formulaire de contact fonctionnel
- 🌐 Liens vers réseaux sociaux (Discord, Twitter, etc.)
- 📍 Adresse physique du campus/BDE
- 📞 Numéro de téléphone de contact

### 6. FAQ
- ❓ Questions fréquentes organisées par catégorie
- ✅ Réponses claires et concises
- 🔍 Recherche dans les questions (optionnel)

### 7. Blog
- 📝 Articles rédigés par l'équipe
- 📰 Actualités des tournois
- 📢 Annonces d'événements futurs

### 8. Événements
- 📅 une vraie Calendrier interactif on peut naviguer entre les mois 
- 📍 Dates et lieux précis 
- 🏆 Informations sur les prix/récompenses
- 👥 Liste des participants inscrits

### 9. Participants
- 📋 Liste complète des participants
- 🖼️ Avatar de chaque joueur
- 👤 Nom réel et pseudo
- 📊 Statistiques de jeu
- ⭐ Rang actuel
- 🏆 Trophées obtenus

---

## 🖼️ Références Visuelles à Étudier

- **Faceit** : Interface clean, focus sur l'expérience joueur
- **ChallengerMode** : Design gaming moderne, gestion d'équipes

---

## ⚙️ Exigences Techniques Strictes

### Langages & Technologies

| Technologie | Exigences |
|-------------|-----------|
| **HTML5** | Sémantique correcte, balises modernes |
| **CSS3** | Sans librairies/frameworks externes (pas de Bootstrap/Tailwind) |
| **PHP 8+** | Pour la logique serveur et base de données |
| **MySQL** | Structure de base de données propre et normalisée |

### Architecture du Code

- ❌ **Pas de framework tout fait** : Code artisanal 100% personnalisé
- ✅ **Utilisation d'includes** : Factorisation du code (header, footer, navbar)
- ✅ **Éviter le copier-coller** : Code réutilisable et modulaire
- ✅ **Préparation POO** : Structure pensée pour migrer vers la POO en v2.0
- ✅ **Responsive mobile-first** : Design adapté aux téléphones en priorité

### Sécurité

- 🔒 **Mots de passe hashés** : Jamais en clair dans la base de données (password_hash)
- 🛡️ **Protection des formulaires** : Prévention XSS et injections SQL
- 🔐 **Sessions sécurisées** : Gestion propre des connexions utilisateurs

### Organisation du Projet

- 📁 Structure de dossiers claire et logique
- 📝 Fichiers bien nommés et organisés par fonctionnalité
- 💬 Commentaires dans le code pour la maintenance future
- 🔄 Versionnement Git avec commits explicites et réguliers

---

## ✅ MVP (Minimum Viable Product) - Priorités

### 🟢 Partie Publique (Obligatoire)

- **Catalogue** : Liste des tournois avec image, date et places restantes
- **Détails** : Page dédiée par tournoi avec liste des équipes inscrites
- **Responsive** : Menu et grilles adaptés mobile/tablette/desktop

### 🟠 Partie Membre (Obligatoire)

- **Inscription/Connexion** : Création de compte sécurisée
- **Inscription Tournoi** : Réservation d'équipe avec vérification des places

### 🔴 Exigences Techniques (Non Négociables)

- ✅ HTML5 + CSS3 pur (pas de librairies CSS)
- ✅ PHP 8+ avec includes pour factoriser le code
- ✅ Base de données MySQL prête pour l'admin (même si interface admin non développée en v1)
- ✅ Design gaming noir/rouge avec glassmorphism et effets glow
- ✅ Optimisation mobile absolue (expérience téléphone prioritaire)

---

## 📱 Priorité Mobile

> **« Tout le monde s'inscrit depuis son téléphone à la cafétéria »**
> 
> → Le design mobile n'est pas une option : c'est la première version à développer. Desktop sera une amélioration secondaire.

---

## 🚀 Sprint 2 : L'Habillage (CSS) & Responsive

> **Objectif** : Mettre en forme le site avec du CSS pur et le rendre utilisable sur mobile. La structure HTML du Sprint 1 est terminée, place au style.

---

### 📋 Checklist Sprint 2

#### 1. Nettoyage & Architecture CSS

- [ ] **Vérifier le HTML sémantique** : Chaque page doit utiliser `<header>`, `<nav>`, `<main>`, `<footer>` correctement avant de styliser
- [ ] **Créer plusieurs fichiers CSS** :
  - `style.css` → Style global et commun (variables, reset, typographie, header, footer, éléments partagés)
  - `index.css` → Style spécifique à la page d'accueil
  - `contact.css` → Style spécifique à la page contact
  - Un fichier `.css` par page qui a des styles spécifiques
- [ ] **Lier les fichiers CSS** dans chaque page HTML (le global + le spécifique)

#### 2. Préparation du Dark Mode (Custom Properties)

- [ ] **Ne JAMAIS écrire de couleurs en dur** dans le CSS (pas de `#333`, `#ff0000`, etc. directement sur les éléments)
- [ ] **Déclarer toutes les couleurs en CSS Custom Properties** dans `:root` de `style.css` :

```css
:root {
  --bg-color: #0a0a0a;           /* Fond principal */
  --bg-secondary: #1a1a2e;       /* Fond secondaire */
  --text-color: #ffffff;          /* Texte principal */
  --text-secondary: #b0b0b0;     /* Texte secondaire */
  --primary-color: #e63946;       /* Rouge compétition */
  --primary-hover: #ff4d5a;       /* Rouge hover */
  --accent-color: #ff2d55;        /* Accent néon */
  --card-bg: rgba(26, 26, 46, 0.8); /* Fond des cartes (glassmorphism) */
  --border-color: rgba(255, 255, 255, 0.1);
  --glow-color: rgba(230, 57, 70, 0.4); /* Lueur rouge */
  /* ... ajouter selon les besoins */
}
```

- [ ] **Utiliser uniquement `var(--nom)`** dans tout le CSS : `color: var(--text-color);`

#### 3. Mise en Page (Layout)

##### Menu / Navigation
- [ ] Utiliser **Flexbox** pour aligner le logo et les liens de navigation
- [ ] Alignement horizontal : logo à gauche, liens à droite (ou centré)

##### Grille des Tournois (Cartes)
- [ ] Utiliser **CSS Grid** (ou Flexbox) pour afficher les tournois sous forme de **cartes**
- [ ] Chaque carte contient : **Image** + **Titre** + **Date**
- [ ] Les cartes doivent être alignées proprement en grille
- [ ] Référence Grid si besoin : https://jojotique.fr/course/mon-premier-site-web/chapter/grid

##### Pied de Page (Sticky Footer)
- [ ] Le footer doit **toujours rester en bas** de la page, même avec peu de contenu
- [ ] Solution : utiliser Flexbox sur le `body` ou un wrapper :

```css
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
main {
  flex: 1;
}
```

#### 4. Responsive — Mobile First (Critère n°1 du client !)

- [ ] **Écrire le CSS mobile en premier** (base), puis ajouter les `@media` pour les écrans plus larges
- [ ] **Breakpoint principal** : `768px`
- [ ] **Sur mobile (< 768px)** :
  - Le menu passe en **vertical** (empilé)
  - Les tournois s'affichent en **1 colonne** (les uns sous les autres)
  - Les cartes prennent toute la largeur
- [ ] **Sur tablette/desktop (≥ 768px)** :
  - Le menu reste horizontal
  - Les tournois s'affichent en grille (2-3 colonnes)

```css
/* Mobile first : styles de base = mobile */
.nav-links {
  flex-direction: column;
}
.tournois-grid {
  grid-template-columns: 1fr;
}

/* Desktop */
@media (min-width: 768px) {
  .nav-links {
    flex-direction: row;
  }
  .tournois-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

#### 5. Rappel des Interdits

| ❌ Interdit | ✅ Autorisé |
|-------------|------------|
| Bootstrap | CSS pur avec Flexbox |
| Tailwind CSS | CSS pur avec Grid |
| Tout framework CSS | Custom Properties CSS |
| Librairies externes | Code artisanal maison |

> **Objectif** : Montrer la maîtrise de `display: flex` et `display: grid` sans béquilles.

#### 6. Git — Commits Atomiques

Chaque commit doit correspondre à **une seule modification logique**. Exemples :

| Type | Exemple de message |
|------|--------------------|
| `feat:` | `feat: structure css variables dans :root` |
| `style:` | `style: header responsive avec flexbox` |
| `style:` | `style: grille tournois en css grid` |
| `fix:` | `fix: footer alignment sticky` |
| `style:` | `style: media queries mobile < 768px` |
| `feat:` | `feat: ajout fichiers css par page` |

---

### 📁 Structure CSS Attendue (Sprint 2)

```
tp-demande-client/
├── style.css              ← Global (variables, reset, header, footer, commun)
├── index.css              ← Spécifique page d'accueil
├── index.html
├── pages/
│   ├── contact.html
│   ├── contact.css        ← Spécifique page contact
│   ├── tournois.html
│   ├── tournois.css       ← Spécifique page tournois
│   ├── inscription.html
│   ├── inscription.css    ← Spécifique page inscription
│   └── ...                ← Même logique pour les autres pages
└── img/
```

---

### ⚡ Ordre de Travail Recommandé (Sprint 2)

1. Vérifier/corriger le HTML sémantique sur toutes les pages
2. Créer `style.css` avec les variables CSS (`:root`)
3. Styliser le **header/nav** avec Flexbox
4. Styliser le **footer** (sticky)
5. Styliser la **grille de tournois** avec CSS Grid
6. Ajouter les **media queries** pour le responsive mobile
7. Créer les fichiers CSS spécifiques par page
8. Tester sur mobile (DevTools → toggle device toolbar)
9. Commits atomiques à chaque étape

---

---

## 🔄 Historique des Modifications

### Sprint 3 – Corrections & Améliorations (Mars 2026)

#### 1. 🧹 Suppression des données factices

Les noms de joueurs, équipes, statistiques et trophées codés en dur ont été **entièrement retirés** de toutes les pages. Le site est une maquette fonctionnelle : les vraies données seront enregistrées par les utilisateurs et chargées depuis la base de données (PHP + MySQL).

Pages nettoyées :

| Page | Ce qui a été supprimé |
|------|----------------------|
| `classement.html` | Podium (NightSlayer, DragonFire, ShadowX) + tableau de 10 joueurs fictifs |
| `participants.html` | 8 cartes joueurs avec noms, équipes et stats fictives |
| `profil.html` | Nom, équipe, stats, trophées et historique fictifs |
| `espace-membre.html` | Pseudo "NightSlayer", réservations fictives, stats codées en dur |
| `inscription.html` | Exemples de noms dans les placeholders des champs |

Ces pages affichent maintenant un **état vide** (`empty-state`) clair et informatif qui invite l'utilisateur à s'inscrire.

#### 2. 🖼️ Upload d'avatar (photo de profil)

Un champ de **sélection d'image** a été ajouté dans les formulaires d'inscription et d'édition de profil :

- **`inscription.html`** : nouveau bloc "Avatar / Photo de profil" en haut du formulaire avec :
  - Aperçu en temps réel de l'image choisie (via `FileReader` JS)
  - Validation de la taille (max 2 Mo côté client)
  - Bouton "Supprimer" pour annuler la sélection
  - Formats acceptés : JPG, PNG, WebP, GIF

- **`espace-membre.html`** : l'avatar actuel se met à jour en temps réel lors du changement d'image.

> ⚠️ L'upload côté serveur devra être géré en PHP lors de l'intégration backend.

#### 3. 📊 Tri des colonnes du classement

Le tableau du classement (`classement.html`) supporte maintenant le **tri par colonne** :

- Cliquer sur un en-tête colonne (Rang, Équipe, Jeu, Matchs, Victoires, Défaites, Win Rate, Points) trie le tableau
- Deuxième clic sur la même colonne inverse le tri (asc ↔ desc)
- Indicateur visuel `↑` / `↓` dans l'en-tête actif
- Accessible au clavier (`Tab` + `Entrée` ou `Espace`)
- Attribut `aria-sort` mis à jour pour les lecteurs d'écran

#### 4. 🎛️ Filtres de jeu fonctionnels

Les boutons de filtre "Tous les jeux / League of Legends / Valorant / CS2" sur la page classement sont maintenant **fonctionnels** : ils masquent/affichent les lignes du tableau selon le jeu.

#### 5. 🐛 Corrections diverses

- `inscription.html` : attribut `enctype="multipart/form-data"` ajouté pour permettre l'envoi de fichiers
- Tous les formulaires de création de compte : placeholders nettoyés (plus d'exemples de noms réels)
- `profil.html` : titre de page corrigé (`Profil de NightSlayer` → `Profil Joueur`)
- Ajout de classes CSS utilitaires globales dans `style.css` : `.hidden`, `.empty-state`, `.profil-placeholder`, `.user-avatar-empty`

---

**📌 Document de référence - À utiliser comme base unique pour tout le développement du projet**
