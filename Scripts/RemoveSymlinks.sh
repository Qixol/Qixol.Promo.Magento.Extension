# removes symbolic links from the magento installed directory to the git repo directory

magentoPath="/var/www/magento"

# app directories and files
unlink $magentoPath/app/etc/modules/Qixol_Promo.xml
unlink $magentoPath/app/etc/modules/Qixol_Missedpromotions.xml
rm -rf $magentoPath/app/code/community/Qixol
unlink $magentoPath/app/design/adminhtml/default/default/layout/qixol.xml
rm -rf $magentoPath/app/design/adminhtml/default/default/template/qixol
rm -rf $magentoPath/app/design/frontend/base/default/layout/qixol
rm -rf $magentoPath/app/design/frontend/base/default/template/qixol
rm -rf $magentoPath/app/design/frontend/rwd/default/template/qixol
rm -rf $magentoPath/app/design/frontend/rwd/default/layout/qixol
unlink $magentoPath/app/locale/en_US/Qixol_Promo.csv

# media directories and files
# TODO: should this directory be renamed to qixol_promo?
rm -rf $magentoPath/media/custom

# skin directories and files
unlink $magentoPath/skin/adminhtml/default/default/images/_run.gif
unlink $magentoPath/skin/adminhtml/default/default/images/_yes.gif
unlink $magentoPath/skin/adminhtml/default/default/images/_error.png
unlink $magentoPath/skin/frontend/base/default/css/qixol.css
rm -rf $magentoPath/skin/frontend/base/default/qixol
rm -rf $magentoPath/skin/frontend/base/default/media
rm -rf $magentoPath/skin/frontend/base/default/js/lib
rm -rf $magentoPath/skin/frontend/base/default/js/qixol
rm -rf $magentoPath/skin/frontend/rwd/default/js/qixol

# var directories and files
rm -rf $magentoPath/var/logs_qixol
unlink $magentoPath/var/connect/Qixol_Promo.xml

