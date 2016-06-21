# removes symbolic links from the magento installed directory to the git repo directory

# app directories and files
unlink /home/ken/public_html/magento-store.com/public/app/etc/modules/Holbi_Qixol.xml
rm -rf /home/ken/public_html/magento-store.com/public/app/code/community/Holbi
unlink /home/ken/public_html/magento-store.com/public/app/design/adminhtml/default/default/layout/qixol.xml
rm -rf /home/ken/public_html/magento-store.com/public/app/design/adminhtml/default/default/template/qixol
unlink /home/ken/public_html/magento-store.com/public/app/design/frontend/base/default/layout/qixol.xml
rm -rf /home/ken/public_html/magento-store.com/public/app/design/frontend/base/default/template/qixol
rm -rf /home/ken/public_html/magento-store.com/public/app/design/frontend/rwd/default/template/qixol
unlink /home/ken/public_html/magento-store.com/public/app/locale/en_US/Holbi_Qixol.csv

# media directories and files
# TODO: should this directory be renamed to qixol_promo?
rm -rf /home/ken/public_html/magento-store.com/public/media/custom

# skin directories and files
unlink /home/ken/public_html/magento-store.com/public/skin/adminhtml/default/default/images/_run.gif
unlink /home/ken/public_html/magento-store.com/public/skin/adminhtml/default/default/images/_yes.gif
unlink /home/ken/public_html/magento-store.com/public/skin/frontend/base/default/css/qixol.css
rm -rf /home/ken/public_html/magento-store.com/public/skin/frontend/base/default/qixol
rm -rf /home/ken/public_html/magento-store.com/public/skin/frontend/base/default/media
rm -rf /home/ken/public_html/magento-store.com/public/skin/frontend/base/default/js/lib

# var directories and files
rm -rf /home/ken/public_html/magento-store.com/public/var/logs_qixol

