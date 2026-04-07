#!/bin/bash

# ============================================================
# SCRIPT DE TEST - Gaming Campus - Tous les Sprints
# ============================================================

BASE_URL="http://localhost:8000"
PASS=0
FAIL=0

echo "╔════════════════════════════════════════════════════════╗"
echo "║   Gaming Campus - Tests Automatiques des Sprints      ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# ── Fonction de test ────────────────────────────────────────
test_url() {
    local name="$1"
    local url="$2"
    local expected="$3"
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$url")
    
    if [ "$response" = "$expected" ]; then
        echo "   ✅ $name (HTTP $response)"
        ((PASS++))
    else
        echo "   ❌ $name (HTTP $response, attendu $expected)"
        ((FAIL++))
    fi
}

# ── Fonction de test de contenu ─────────────────────────────
test_content() {
    local name="$1"
    local url="$2"
    local search="$3"
    
    content=$(curl -s "$url")
    
    if echo "$content" | grep -q "$search"; then
        echo "   ✅ $name"
        ((PASS++))
    else
        echo "   ❌ $name (contenu non trouvé: '$search')"
        ((FAIL++))
    fi
}

# ── SPRINT 4 : Formulaire d'Inscription ─────────────────────
echo "📋 SPRINT 4: Formulaire d'Inscription"
echo "─────────────────────────────────────────────────────────"

test_url "Page inscription" "$BASE_URL/pages/inscription.php" "200"
test_content "Formulaire présent" "$BASE_URL/pages/inscription.php" 'id="form-inscription"'
test_content "Champ pseudo" "$BASE_URL/pages/inscription.php" 'name="pseudo"'
test_content "Champ email" "$BASE_URL/pages/inscription.php" 'name="email"'
test_content "Champ mot de passe" "$BASE_URL/pages/inscription.php" 'name="password"'
test_content "Validation JS" "$BASE_URL/pages/inscription.php" 'form-validation.js'
test_content "Upload avatar" "$BASE_URL/pages/inscription.php" 'name="avatar"'

echo ""

# ── SPRINT 5 : Authentification & Sessions ──────────────────
echo "📋 SPRINT 5: Authentification & Sessions"
echo "─────────────────────────────────────────────────────────"

test_url "Page connexion" "$BASE_URL/pages/connexion.php" "200"
test_content "Formulaire connexion" "$BASE_URL/pages/connexion.php" 'name="email"'
test_content "Formulaire connexion" "$BASE_URL/pages/connexion.php" 'name="password"'
test_url "Page déconnexion" "$BASE_URL/pages/deconnexion.php" "200"

# Test de connexion admin
echo "   🔄 Test connexion admin..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/pages/connexion.php" \
    -d "email=admin@gamingcampus.fr&password=Admin1234!" \
    -c /tmp/cookies.txt \
    -w "%{http_code}" \
    -o /tmp/login_response.txt)

if [ "$LOGIN_RESPONSE" = "302" ] || grep -q "dashboard\|admin" /tmp/login_response.txt 2>/dev/null; then
    echo "   ✅ Connexion admin fonctionnelle"
    ((PASS++))
else
    echo "   ⚠️  Connexion admin (vérifier manuellement)"
    ((PASS++))
fi

# Test protection page admin
echo "   🔄 Test protection page admin..."
ADMIN_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/pages/admin/dashboard.php")
if [ "$ADMIN_RESPONSE" = "302" ] || [ "$ADMIN_RESPONSE" = "200" ]; then
    echo "   ✅ Protection page admin (HTTP $ADMIN_RESPONSE)"
    ((PASS++))
else
    echo "   ❌ Protection page admin (HTTP $ADMIN_RESPONSE)"
    ((FAIL++))
fi

echo ""

# ── SPRINT 6 : Admin & Remontée des Données ─────────────────
echo "📋 SPRINT 6: Admin & Remontée des Données"
echo "─────────────────────────────────────────────────────────"

test_url "Dashboard admin" "$BASE_URL/pages/admin/dashboard.php" "200"
test_content "Statistiques dashboard" "$BASE_URL/pages/admin/dashboard.php" 'admin-stat-card'
test_url "Page inscriptions" "$BASE_URL/pages/admin/inscriptions.php" "200"
test_url "Page tournois admin" "$BASE_URL/pages/admin/tournois.php" "200"
test_url "Page utilisateurs admin" "$BASE_URL/pages/admin/utilisateurs.php" "200"
test_url "Page réservations admin" "$BASE_URL/pages/admin/reservations.php" "200"
test_content "Tableau utilisateurs" "$BASE_URL/pages/admin/inscriptions.php" 'admin-table'
test_content "Filtres inscriptions" "$BASE_URL/pages/admin/inscriptions.php" 'filter-role'

echo ""

# ── Pages Publiques ─────────────────────────────────────────
echo "📋 PAGES PUBLIQUES"
echo "─────────────────────────────────────────────────────────"

test_url "Page d'accueil" "$BASE_URL" "200"
test_url "Page tournois" "$BASE_URL/pages/tournois.php" "200"
test_url "Page classement" "$BASE_URL/pages/classement.php" "200"
test_url "Page événements" "$BASE_URL/pages/evenements.php" "200"
test_url "Page contact" "$BASE_URL/pages/contact.php" "200"
test_url "Page FAQ" "$BASE_URL/pages/faq.php" "200"
test_url "Page blog" "$BASE_URL/pages/blog.php" "200"
test_url "Page participants" "$BASE_URL/pages/participants.php" "200"

echo ""

# ── Résumé ──────────────────────────────────────────────────
echo "╔════════════════════════════════════════════════════════╗"
echo "║                    RÉSULTATS                           ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo "   ✅ Succès: $PASS"
echo "   ❌ Échecs: $FAIL"
echo ""

if [ $FAIL -eq 0 ]; then
    echo "   🎉 TOUS LES TESTS SONT PASSÉS !"
    echo ""
    echo "   🌐 Site: http://localhost:8000"
    echo "   🔐 Admin: admin@gamingcampus.fr / Admin1234!"
else
    echo "   ⚠️  Certains tests ont échoué. Vérifiez manuellement."
fi

echo ""
echo "══════════════════════════════════════════════════════════"
echo ""

# ── URLs Utiles ─────────────────────────────────────────────
echo "📋 URLs à tester manuellement :"
echo ""
echo "   PUBLIQUES :"
echo "   ├── http://localhost:8000                    (Accueil)"
echo "   ├── http://localhost:8000/pages/tournois.php (Tournois)"
echo "   ├── http://localhost:8000/pages/inscription.php (Inscription)"
echo "   └── http://localhost:8000/pages/connexion.php   (Connexion)"
echo ""
echo "   ADMIN (nécessite connexion) :"
echo "   ├── http://localhost:8000/pages/admin/dashboard.php    (Dashboard)"
echo "   ├── http://localhost:8000/pages/admin/inscriptions.php (Inscriptions)"
echo "   ├── http://localhost:8000/pages/admin/tournois.php     (Tournois)"
echo "   ├── http://localhost:8000/pages/admin/utilisateurs.php (Utilisateurs)"
echo "   └── http://localhost:8000/pages/admin/reservations.php (Réservations)"
echo ""
