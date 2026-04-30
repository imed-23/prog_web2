#!/usr/bin/env bash
# ============================================================================
# push_debug_final.sh
# Script à lancer APRES que Replit ait fait le checkpoint auto (qui commit
# automatiquement les modifications sur la branche main locale).
#
# Ce script :
#   1. Crée une branche locale `debug_final` au même commit que main
#   2. La pousse sur GitHub vers origin/debug_final
#   3. Ne touche JAMAIS à origin/main
#
# Pré-requis :
#   - Avoir cliqué sur le bouton "Connect to GitHub" dans Replit (panneau Git)
#     OU avoir exporté un Personal Access Token GitHub (variable GH_TOKEN)
# ============================================================================
set -euo pipefail

BRANCH="debug_final"

echo "📦 Branches locales actuelles :"
git --no-optional-locks branch --list

echo
echo "🌿 Création (ou reset) de la branche locale '$BRANCH' sur HEAD..."
git branch -f "$BRANCH"

echo
echo "🚀 Push vers origin/$BRANCH ..."
git push -u origin "$BRANCH"

echo
echo "✅ Branche '$BRANCH' poussée sur GitHub."
echo "   → https://github.com/imed-23/prog_web2/tree/$BRANCH"
