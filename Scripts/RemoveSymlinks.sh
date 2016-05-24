# removes symbolic links from the magento installed directory to the git repo directory

unlink /home/ken/public_html/magento-store.com/public/app/etc/modules/Holbi_Qixol.xml

rm -rf /home/ken/public_html/magento-store.com/public/app/code/community/Holbi

unlink /home/ken/public_html/magento-store.com/public/app/design/adminhtml/default/default/layout/qixol.xml

rm -rf /home/ken/public_html/magento-store.com/public/app/design/adminhtml/default/default/template/qixol

unlink /home/ken/public_html/magento-store.com/public/app/design/frontend/base/default/layout/qixol.xml

rm -rf /home/ken/public_html/magento-store.com/public/app/design/frontend/base/default/template/qixol

rm -rf /home/ken/public_html/magento-store.com/public/app/design/frontend/rwd/default/template/qixol

unlink /home/ken/public_html/magento-store.com/public/app/locale/en_US/Holbi_Qixol.csv
