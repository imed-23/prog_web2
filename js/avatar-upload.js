/* ============================================================
   GAMING CAMPUS — avatar-upload.js
   Prévisualisation et validation de l'avatar en temps réel
   Chargé dans : pages/inscription.php + pages/espace-membre.php
   ============================================================ */

(function () {
    /* ── Inscription : avatar-preview avec bouton Supprimer ── */
    var fileInput   = document.getElementById('avatar-file');
    var previewImg  = document.getElementById('avatar-preview-img');
    var placeholder = document.getElementById('avatar-placeholder');
    var removeBtn   = document.getElementById('avatar-remove-btn');

    if (fileInput && previewImg) {
        fileInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            /* Validation taille (2 Mo) */
            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image est trop lourde. Maximum 2 Mo.');
                this.value = '';
                return;
            }

            /* Prévisualisation */
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                if (removeBtn)   removeBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                fileInput.value = '';
                previewImg.src  = '';
                previewImg.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');
                removeBtn.classList.add('hidden');
            });
        }
    }

    /* ── Espace membre : avatar actuel mis à jour en temps réel ── */
    var currentAvatarImg   = document.getElementById('current-avatar-img');
    var currentPlaceholder = document.getElementById('current-avatar-placeholder');

    if (fileInput && currentAvatarImg) {
        fileInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image est trop lourde. Maximum 2 Mo.');
                this.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                currentAvatarImg.src = e.target.result;
                currentAvatarImg.classList.remove('hidden');
                if (currentPlaceholder) currentPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    }
}());
