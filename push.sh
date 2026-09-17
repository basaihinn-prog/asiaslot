#!/bin/bash

#########################################
# Auto GitHub Push Script (Dynamic Repo)
#########################################

# Script run သည့်အခါ argument ပါရင် ယူမည်၊ မပါရင် User ထံမှ ရယူမည်
REPO_URL="$1"

if [ -z "$REPO_URL" ]; then
    read -p "Enter GitHub Repository URL: " REPO_URL
fi

# URL မထည့်ဘဲ Enter ခေါက်ပါက ရပ်တန့်မည်
if [ -z "$REPO_URL" ]; then
    echo "Error: Repository URL ဖြည့်စွက်ရန် လိုအပ်ပါသည်။"
    exit 1
fi

BRANCH="main"
COMMIT_MESSAGE="${2:-Auto update $(date '+%Y-%m-%d %H:%M:%S')}"

set -e

echo "======================================"
echo " GitHub Auto Push"
echo "======================================"

# Check Git
if ! command -v git >/dev/null 2>&1; then
    echo "Error: Git is not installed."
    exit 1
fi

# Initialize repository if needed
if [ ! -d ".git" ]; then
    echo "[*] Initializing Git repository..."
    git init
fi

# Configure remote
if git remote get-url origin >/dev/null 2>&1; then
    git remote set-url origin "$REPO_URL"
else
    git remote add origin "$REPO_URL"
fi

# Create/switch to branch
git checkout -B "$BRANCH"

echo "[*] Adding all files..."
git add -A

# Commit only if there are changes
if git diff --cached --quiet; then
    echo "[✓] No changes to commit."
    exit 0
fi

echo "[*] Committing..."
git commit -m "$COMMIT_MESSAGE"

echo "[*] Pushing to GitHub..."
git push -u origin "$BRANCH"

echo
echo "======================================"
echo " Push Complete!"
echo " Repository: $REPO_URL"
echo " Branch: $BRANCH"
echo "======================================"
