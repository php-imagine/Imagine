#!/bin/bash

set -o errexit

AUTO_REPOSITORY_OWNER='avalanche123'
AUTO_REPOSITORY_NAME='Imagine'
AUTO_PROCESS_BRANCH='develop'
AUTO_COMMIT_NAME='[skip ci] Automatic API update'
AUTO_COMMIT_AUTHOR_NAME='Michele Locati'
AUTO_COMMIT_AUTHOR_EMAIL='michele@locati.it'

if test "${TRAVIS_PULL_REQUEST:-}" != 'false'; then
    echo "Automatic API generation: skipping because it's a pull request."
    exit 0
fi

if test "${TRAVIS_BRANCH:-}" != "${AUTO_PROCESS_BRANCH}"; then
    echo "Automatic API generation: skipping because pushing to '${TRAVIS_BRANCH:-}' instead of '${AUTO_PROCESS_BRANCH}'."
    exit 0
fi

if test "${TRAVIS_REPO_SLUG:-}" != "${AUTO_REPOSITORY_OWNER}/${AUTO_REPOSITORY_NAME}"; then
    echo "Automatic API generation: skipping because repository is '${TRAVIS_REPO_SLUG:-}' instead of '${AUTO_REPOSITORY_OWNER}/${AUTO_REPOSITORY_NAME}'."
    exit 0
fi

if test -z "${TRAVIS_COMMIT_MESSAGE:-}"; then
    echo "Automatic API generation: skipping because commit message is unavailable."
    exit 0
fi
if test "${TRAVIS_COMMIT_MESSAGE}" = "${AUTO_COMMIT_NAME}"; then
    echo "Automatic API generation: skipping because commit is already '${AUTO_COMMIT_NAME}'."
    exit 0
fi

if test -z "${GUTHUB_ACCESS_TOKEN:-}"; then
    printf 'Automatic API generation: skipping because GUTHUB_ACCESS_TOKEN is not available
To create it:
 - go to https://github.com/settings/tokens/new
 - create a new token
 - sudo apt update
 - sudo apt install -y build-essential ruby ruby-dev
 - sudo gem install travis
 - travis encrypt --repo %s/%s GUTHUB_ACCESS_TOKEN=<TOKEN>
 - Add to the env setting of:
   secure: "encrypted string"
' "${AUTO_REPOSITORY_OWNER}" "${AUTO_REPOSITORY_NAME}"
    exit 0
fi

echo "Automatic API generation: updating API"
cd "${TRAVIS_BUILD_DIR}"
git checkout -qf "${AUTO_PROCESS_BRANCH}"
cd "${TRAVIS_BUILD_DIR}/docs/_build"
rm -rf ../API
composer --no-interaction install
composer --no-interaction update-docs

cd "${TRAVIS_BUILD_DIR}/docs/API"
if test -z "$(git status --porcelain .)"; then
    echo "Automatic API generation: skipping because API is already up-to-date"
    exit 0
fi
echo "Automatic API generation: changes detected - commiting and pushing changed."
git add --all .
git config user.name "${AUTO_COMMIT_AUTHOR_NAME}"
git config user.email "${AUTO_COMMIT_AUTHOR_EMAIL}"
git commit -m "${AUTO_COMMIT_NAME}"
git remote add deploy "https://${GUTHUB_ACCESS_TOKEN}@github.com/${AUTO_REPOSITORY_OWNER}/${AUTO_REPOSITORY_NAME}.git"
git push deploy "${AUTO_PROCESS_BRANCH}"
echo "Automatic API generation: repository updated."
