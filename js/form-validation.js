/**
 * form-validation.js — Validation temps réel du formulaire d'inscription
 * Gaming Campus — Sprint 4
 *
 * Fonctionnalités :
 *  - Erreurs inline sous chaque champ (`.field-error`)
 *  - Indicateur de force du mot de passe
 *  - Vérification confirmation mdp
 *  - Bloque la soumission si des erreurs existent
 */

(function () {
    'use strict';

    // ── Sélecteurs ──────────────────────────────────────────────────────────
    var form     = document.querySelector('.auth-form');
    if (!form) return;

    var pseudo      = document.getElementById('pseudo');
    var prenom      = document.getElementById('prenom');
    var nom         = document.getElementById('nom');
    var email       = document.getElementById('email');
    var password    = document.getElementById('password');
    var passwordCfm = document.getElementById('password-confirm');
    var cgu         = document.getElementById('cgu');

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Affiche un message d'erreur sous le champ */
    function showError(input, msg) {
        clearError(input);
        input.classList.add('input-error');
        input.classList.remove('input-ok');
        var err = document.createElement('span');
        err.className   = 'field-error';
        err.textContent = msg;
        err.setAttribute('role', 'alert');
        input.parentNode.insertBefore(err, input.nextSibling);
    }

    /** Affiche un état OK sur le champ */
    function showOk(input) {
        clearError(input);
        input.classList.remove('input-error');
        input.classList.add('input-ok');
    }

    /** Supprime le message d'erreur d'un champ */
    function clearError(input) {
        input.classList.remove('input-error', 'input-ok');
        var next = input.nextSibling;
        if (next && next.classList && next.classList.contains('field-error')) {
            next.parentNode.removeChild(next);
        }
    }

    /** Regex basique email */
    function isValidEmail(val) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val);
    }

    /** Force du mot de passe (retourne 0-4) */
    function passwordStrength(val) {
        var score = 0;
        if (val.length >= 8)              score++;
        if (/[A-Z]/.test(val))            score++;
        if (/[0-9]/.test(val))            score++;
        if (/[^A-Za-z0-9]/.test(val))    score++;
        return score;
    }

    // ── Validateurs par champ ──────────────────────────────────────────────

    function validatePseudo() {
        var v = pseudo.value.trim();
        if (!v)                 { showError(pseudo, 'Le pseudo est obligatoire.'); return false; }
        if (v.length < 3)       { showError(pseudo, 'Le pseudo doit contenir au moins 3 caractères.'); return false; }
        if (v.length > 20)      { showError(pseudo, 'Le pseudo ne doit pas dépasser 20 caractères.'); return false; }
        if (!/^[a-zA-Z0-9_\-]+$/.test(v)) { showError(pseudo, 'Seuls les lettres, chiffres, _ et - sont autorisés.'); return false; }
        showOk(pseudo);
        return true;
    }

    function validatePrenom() {
        var v = prenom.value.trim();
        if (!v) { showError(prenom, 'Le prénom est obligatoire.'); return false; }
        showOk(prenom);
        return true;
    }

    function validateNom() {
        var v = nom.value.trim();
        if (!v) { showError(nom, 'Le nom est obligatoire.'); return false; }
        showOk(nom);
        return true;
    }

    function validateEmail() {
        var v = email.value.trim();
        if (!v)              { showError(email, 'L\'adresse email est obligatoire.'); return false; }
        if (!isValidEmail(v)){ showError(email, 'L\'adresse email n\'est pas valide (ex: prenom.nom@campus.fr).'); return false; }
        showOk(email);
        return true;
    }

    function validatePassword() {
        var v = password.value;
        if (!v)         { showError(password, 'Le mot de passe est obligatoire.'); updateStrengthBar(0); return false; }
        if (v.length < 8) { showError(password, 'Le mot de passe doit contenir au moins 8 caractères.'); updateStrengthBar(1); return false; }
        if (!/[A-Z]/.test(v)) { showError(password, 'Le mot de passe doit contenir au moins une lettre majuscule.'); updateStrengthBar(1); return false; }
        if (!/[0-9]/.test(v)) { showError(password, 'Le mot de passe doit contenir au moins un chiffre.'); updateStrengthBar(2); return false; }
        showOk(password);
        updateStrengthBar(passwordStrength(v));
        return true;
    }

    function validatePasswordConfirm() {
        var v = passwordCfm.value;
        if (!v)                      { showError(passwordCfm, 'Veuillez confirmer votre mot de passe.'); return false; }
        if (v !== password.value)    { showError(passwordCfm, 'Les mots de passe ne correspondent pas.'); return false; }
        showOk(passwordCfm);
        return true;
    }

    function validateCgu() {
        if (!cgu.checked) {
            // Cherche un message d'erreur existant près de la checkbox
            var existing = document.getElementById('cgu-error');
            if (!existing) {
                var err = document.createElement('span');
                err.className   = 'field-error';
                err.id          = 'cgu-error';
                err.textContent = 'Vous devez accepter les conditions générales.';
                err.setAttribute('role', 'alert');
                cgu.parentNode.parentNode.appendChild(err);
            }
            return false;
        }
        var existing = document.getElementById('cgu-error');
        if (existing) existing.parentNode.removeChild(existing);
        return true;
    }

    // ── Barre de force du mot de passe ────────────────────────────────────

    var strengthBar  = null;
    var strengthText = null;

    function createStrengthBar() {
        var wrapper = password.parentNode;
        if (wrapper.querySelector('.strength-bar-wrapper')) return;

        var wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'strength-bar-wrapper';

        strengthBar = document.createElement('div');
        strengthBar.className = 'strength-bar';

        var fill = document.createElement('div');
        fill.className = 'strength-bar-fill';
        fill.id = 'strength-fill';
        strengthBar.appendChild(fill);

        strengthText = document.createElement('small');
        strengthText.className = 'strength-text';
        strengthText.id = 'strength-text';

        wrapperDiv.appendChild(strengthBar);
        wrapperDiv.appendChild(strengthText);
        wrapper.appendChild(wrapperDiv);
    }

    function updateStrengthBar(score) {
        createStrengthBar();
        var fill = document.getElementById('strength-fill');
        var text = document.getElementById('strength-text');
        if (!fill || !text) return;

        var levels = [
            { pct: '0%',    color: 'transparent', label: '' },
            { pct: '25%',   color: '#e74c3c',     label: 'Très faible' },
            { pct: '50%',   color: '#e67e22',     label: 'Faible' },
            { pct: '75%',   color: '#f1c40f',     label: 'Moyen' },
            { pct: '100%',  color: '#2ecc71',     label: 'Fort ✓' },
        ];
        var l = levels[Math.min(score, 4)];
        fill.style.width            = l.pct;
        fill.style.backgroundColor  = l.color;
        text.textContent            = l.label;
        text.style.color            = l.color;
    }

    // ── Écouteurs temps réel ──────────────────────────────────────────────

    if (pseudo)      pseudo.addEventListener('input',  validatePseudo);
    if (prenom)      prenom.addEventListener('input',  validatePrenom);
    if (nom)         nom.addEventListener('input',     validateNom);
    if (email)       email.addEventListener('input',   validateEmail);
    if (password) {
        createStrengthBar();
        password.addEventListener('input', function () {
            validatePassword();
            if (passwordCfm && passwordCfm.value) validatePasswordConfirm();
            updateStrengthBar(passwordStrength(password.value));
        });
    }
    if (passwordCfm) passwordCfm.addEventListener('input', validatePasswordConfirm);
    if (cgu)         cgu.addEventListener('change', validateCgu);

    // Validation `blur` (quand on quitte un champ)
    if (pseudo)      pseudo.addEventListener('blur',  validatePseudo);
    if (prenom)      prenom.addEventListener('blur',  validatePrenom);
    if (nom)         nom.addEventListener('blur',     validateNom);
    if (email)       email.addEventListener('blur',   validateEmail);
    if (password)    password.addEventListener('blur', validatePassword);
    if (passwordCfm) passwordCfm.addEventListener('blur', validatePasswordConfirm);

    // ── Interception de la soumission ────────────────────────────────────

    form.addEventListener('submit', function (e) {
        var valid = true;
        if (pseudo      && !validatePseudo())          valid = false;
        if (prenom      && !validatePrenom())          valid = false;
        if (nom         && !validateNom())             valid = false;
        if (email       && !validateEmail())           valid = false;
        if (password    && !validatePassword())        valid = false;
        if (passwordCfm && !validatePasswordConfirm()) valid = false;
        if (cgu         && !validateCgu())             valid = false;

        if (!valid) {
            e.preventDefault();
            // Scroll vers la première erreur
            var firstErr = form.querySelector('.field-error, .input-error');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

}());
