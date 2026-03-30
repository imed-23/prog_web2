<?php
/**
 * inscription.php — Formulaire d'inscription Gaming Campus
 * Sprint 4 : traitement PHP sécurisé + validation JS front-end
 */

require_once __DIR__ . '/../assets/php/config/auth.php';
gc_start_session();

// ── Traitement du formulaire (POST uniquement) ─────────────────────────────
$erreurs          = [];
$anciennesValeurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../assets/php/traitement/inscription.trait.php';
    // Si pas de redirection (des erreurs existent), on continue vers la vue
}

// ── Variables de la page ───────────────────────────────────────────────────
$rootPath          = '../';
$pageTitle         = 'Inscription - Gaming Campus';
$metaDescription   = 'Créer un compte sur Gaming Campus. Inscris-toi pour rejoindre les tournois et inscrire ton équipe.';
$cssSpecifique     = 'auth.css';
$jsSupplementaires = ['avatar-upload.js', 'form-validation.js'];
include '../assets/php/components/header.php';

// Raccourci pour repopuler les champs
$old = $anciennesValeurs;
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <section class="auth-section" aria-labelledby="titre-inscription">
            <div class="auth-container">
                <div class="auth-card">

                    <div class="auth-header">
                        <h1 id="titre-inscription">🎮 Créer un compte</h1>
                        <p>Rejoins la communauté gaming du campus et inscris ton équipe aux tournois !</p>
                    </div>

                    <!-- ── Erreur globale BDD ── -->
                    <?php if (!empty($erreurs['db'])): ?>
                    <div class="alert alert-error" role="alert">
                        <p>❌ <?= htmlspecialchars($erreurs['db']) ?></p>
                    </div>
                    <?php endif; ?>

                    <form class="auth-form" id="form-inscription"
                          method="post" action="inscription.php"
                          enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">

                        <!-- ── Avatar ── -->
                        <div class="form-group form-group-avatar">
                            <label>Avatar / Photo de profil</label>
                            <div class="avatar-upload">
                                <div class="avatar-preview-wrapper">
                                    <div class="avatar-preview" id="avatar-preview" aria-label="Aperçu de ton avatar">
                                        <span class="avatar-preview-placeholder" id="avatar-placeholder">👤</span>
                                        <img id="avatar-preview-img" src="" alt="Aperçu de ton avatar" class="hidden" loading="lazy">
                                    </div>
                                </div>
                                <div class="avatar-upload-actions">
                                    <label for="avatar-file" class="btn btn-outline">📷 Choisir une image</label>
                                    <input type="file" id="avatar-file" name="avatar"
                                           accept="image/png, image/jpeg, image/webp, image/gif" class="sr-only">
                                    <small class="form-help">JPG, PNG, WebP ou GIF. Max 2 Mo.</small>
                                    <button type="button" id="avatar-remove-btn"
                                            class="btn btn-sm btn-outline hidden"
                                            aria-label="Supprimer l'avatar">✕ Supprimer</button>
                                </div>
                            </div>
                            <?php if (!empty($erreurs['avatar'])): ?>
                                <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['avatar']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- ── Pseudo ── -->
                        <div class="form-group">
                            <label for="pseudo">Pseudo <span class="required" aria-label="obligatoire">*</span></label>
                            <input type="text" id="pseudo" name="pseudo" required
                                   placeholder="Ton pseudo" autocomplete="username"
                                   minlength="3" maxlength="20"
                                   value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>"
                                   class="<?= !empty($erreurs['pseudo']) ? 'input-error' : '' ?>">
                            <small class="form-help">3 à 20 caractères (lettres, chiffres, _ et -).</small>
                            <?php if (!empty($erreurs['pseudo'])): ?>
                                <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['pseudo']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- ── Prénom / Nom ── -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prenom">Prénom <span class="required">*</span></label>
                                <input type="text" id="prenom" name="prenom" required
                                       placeholder="Ton prénom" autocomplete="given-name"
                                       value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                                       class="<?= !empty($erreurs['prenom']) ? 'input-error' : '' ?>">
                                <?php if (!empty($erreurs['prenom'])): ?>
                                    <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['prenom']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="nom">Nom <span class="required">*</span></label>
                                <input type="text" id="nom" name="nom" required
                                       placeholder="Ton nom" autocomplete="family-name"
                                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                                       class="<?= !empty($erreurs['nom']) ? 'input-error' : '' ?>">
                                <?php if (!empty($erreurs['nom'])): ?>
                                    <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['nom']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ── Email ── -->
                        <div class="form-group">
                            <label for="email">Email universitaire <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required
                                   placeholder="prenom.nom@campus.fr" autocomplete="email"
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   class="<?= !empty($erreurs['email']) ? 'input-error' : '' ?>">
                            <small class="form-help">Utilise ton adresse email du campus.</small>
                            <?php if (!empty($erreurs['email'])): ?>
                                <span class="field-error" role="alert"><?= $erreurs['email'] /* Peut contenir un lien */ ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- ── Mot de passe ── -->
                        <div class="form-group">
                            <label for="password">Mot de passe <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required
                                   minlength="8" placeholder="Minimum 8 caractères"
                                   autocomplete="new-password"
                                   class="<?= !empty($erreurs['password']) ? 'input-error' : '' ?>">
                            <small class="form-help">Au moins 8 caractères, une majuscule et un chiffre.</small>
                            <?php if (!empty($erreurs['password'])): ?>
                                <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['password']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- ── Confirmation mot de passe ── -->
                        <div class="form-group">
                            <label for="password-confirm">Confirmer le mot de passe <span class="required">*</span></label>
                            <input type="password" id="password-confirm" name="password_confirm" required
                                   minlength="8" placeholder="Retape ton mot de passe"
                                   autocomplete="new-password"
                                   class="<?= !empty($erreurs['password_confirm']) ? 'input-error' : '' ?>">
                            <?php if (!empty($erreurs['password_confirm'])): ?>
                                <span class="field-error" role="alert"><?= htmlspecialchars($erreurs['password_confirm']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- ── Jeu principal ── -->
                        <div class="form-group">
                            <label for="jeu-principal">Jeu principal</label>
                            <select id="jeu-principal" name="jeu_principal">
                                <option value="" <?= empty($old['jeu']) ? 'selected' : '' ?> disabled>Choisis ton jeu principal</option>
                                <?php
                                $jeux = ['lol' => 'League of Legends', 'valorant' => 'Valorant',
                                         'cs2' => 'Counter-Strike 2', 'fortnite' => 'Fortnite',
                                         'rocket-league' => 'Rocket League', 'autre' => 'Autre'];
                                foreach ($jeux as $val => $label):
                                    $selected = (isset($old['jeu']) && $old['jeu'] === $val) ? 'selected' : '';
                                ?>
                                <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- ── CGU ── -->
                        <div class="form-group form-checkbox">
                            <input type="checkbox" id="cgu" name="cgu" required>
                            <label for="cgu">J'accepte les <a href="#" target="_blank">conditions générales d'utilisation</a>
                                <span class="required">*</span></label>
                            <?php if (!empty($erreurs['cgu'])): ?>
                                <span class="field-error" id="cgu-error" role="alert"><?= htmlspecialchars($erreurs['cgu']) ?></span>
                            <?php endif; ?>
                        </div>

                        <p class="form-note"><span class="required">*</span> Champs obligatoires</p>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Créer mon compte</button>
                    </form>

                    <div class="auth-footer">
                        <p>Tu as déjà un compte ? <a href="connexion.php">Connecte-toi ici</a></p>
                    </div>

                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
