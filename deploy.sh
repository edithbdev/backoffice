#!/bin/bash
rsync -av -e "ssh -p 65002" ./ u154940854@51.178.134.91:/home/u154940854/public_html/backoffice/ --include=public/.htaccess --exclude-from=.gitignore --exclude=".*"

#!/bin/bash
# set -e

# OUTPUT_FILE="../website.com.zip"

# export APP_ENV=prod
# export APP_DEBUG=0

# # Optimisations Symfony
# composer install --no-dev --optimize-autoloader
# php bin/console cache:clear

# # Suppression fichiers inutiles
# rm -rf var/cache
# rm -rf var/log

# # Construction de l'archive à envoyer sur le serveur web
# if [[ -e "${OUTPUT_FILE}" ]]; then
#     rm -v "${OUTPUT_FILE}"
# fi

# zip -9 -rqq "${OUTPUT_FILE}" . \
#     -x=*.bin/* \
#     -x=*.assets/* \
#     -x=*.templates/* \
#     -x=*.tests/* \
#     -x=*.git* \
#     -x=*README.md* \

# du -hs "${OUTPUT_FILE}"
# md5sum "${OUTPUT_FILE}"
