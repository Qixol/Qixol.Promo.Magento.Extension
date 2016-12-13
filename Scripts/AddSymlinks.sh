###################################################################################################
# sets up symbolic links from the  from the magento installed directory to the git repo directory
# remember to turn on "allow symbolic links" in Magento
# login to Magento admin,
# go to “System > Configuration > Developer > Template Settings > Allow Symlinks > Yes”
###################################################################################################

if [ $# != 2 ]
then
	echo "Usage: AddSymLinks.sh <git directory> <magento directory>"
	echo "eg: AddSymLinks.sh ~/git/MagentoExtension /var/www/html/Magento/public"
	exit 1
fi

###################################################################################################
# Promo extension
###################################################################################################

# app directories and files
ln -s $1/app/etc/modules/Qixol_Promo.xml $2/app/etc/modules/Qixol_Promo.xml
ln -s $1/app/code/community/Qixol $2/app/code/community/
ln -s $1/app/design/adminhtml/default/default/layout/qixol $2/app/design/adminhtml/default/default/layout/
ln -s $1/app/design/adminhtml/default/default/template/qixol $2/app/design/adminhtml/default/default/template/
ln -s $1/app/design/frontend/base/default/layout/qixol $2/app/design/frontend/base/default/layout/qixol
ln -s $1/app/design/frontend/base/default/template/qixol $2/app/design/frontend/base/default/template/
ln -s $1/app/design/frontend/rwd/default/template/qixol $2/app/design/frontend/rwd/default/template/
ln -s $1/app/design/frontend/rwd/default/layout/qixol $2/app/design/frontend/rwd/default/layout/
ln -s $1/app/locale/en_US/Qixol_Promo.csv $2/app/locale/en_US/Qixol_Promo.csv

# media directories and files
# TODO: should this directory be renamed to qixol_promo?
ln -s $1/media/custom $2/media/

# skin directories and files
ln -s $1/skin/adminhtml/default/default/images/_run.gif $2/skin/adminhtml/default/default/images/_run.gif
ln -s $1/skin/adminhtml/default/default/images/_yes.gif $2/skin/adminhtml/default/default/images/_yes.gif
ln -s $1/skin/adminhtml/default/default/images/_error.png $2/skin/adminhtml/default/default/images/_error.png
ln -s $1/skin/frontend/base/default/css/qixol.css $2/skin/frontend/base/default/css/qixol.css
ln -s $1/skin/frontend/base/default/images/qixol $2/skin/frontend/base/default/
ln -s $1/skin/frontend/base/default/images/media $2/skin/frontend/base/default/
ln -s $1/skin/frontend/base/default/js/lib $2/skin/frontend/base/default/js/
ln -s $1/skin/frontend/base/default/js/qixol $2/skin/frontend/base/default/js/
ln -s $1/skin/frontend/rwd/default/js/qixol $2/skin/frontend/rwd/default/js/

# var directories and files
ln -s $1/var/logs_qixol $2/var/
ln -s $1/var/connect/Qixol_Promo.xml $2/var/connect/Qixol_Promo.xml

###################################################################################################
# Missed promotions extension
###################################################################################################

ln -s $1/app/etc/modules/Qixol_Missedpromotions.xml $2/app/etc/modules/Qixol_Missedpromotions.xml
#ln -s $1/app/code/community/Qixol $2/app/code/community/
#ln -s $1/app/design/adminhtml/default/default/layout/qixol.xml $2/app/design/adminhtml/default/default/layout/qixol.xml
#ln -s $1/app/design/adminhtml/default/default/template/qixol $2/app/design/adminhtml/default/default/template/
#ln -s $1/app/design/frontend/base/default/layout/qixol $2/app/design/frontend/base/default/layout/qixol
#ln -s $1/app/design/frontend/base/default/template/qixol $2/app/design/frontend/base/default/template/
#ln -s $1/app/design/frontend/rwd/default/template/qixol $2/app/design/frontend/rwd/default/template/
#ln -s $1/app/design/frontend/rwd/default/layout/qixol $2/app/design/frontend/rwd/default/layout/
#ln -s $1/app/locale/en_US/Qixol_Promo.csv $2/app/locale/en_US/Qixol_Promo.csv

# media directories and files
# TODO: should this directory be renamed to qixol_promo?
#ln -s $1/media/custom $2/media/

# skin directories and files
#ln -s $1/skin/adminhtml/default/default/images/_run.gif $2/skin/adminhtml/default/default/images/_run.gif
#ln -s $1/skin/adminhtml/default/default/images/_yes.gif $2/skin/adminhtml/default/default/images/_yes.gif
#ln -s $1/skin/adminhtml/default/default/images/_error.png $2/skin/adminhtml/default/default/images/_error.png
#ln -s $1/skin/frontend/base/default/css/qixol.css $2/skin/frontend/base/default/css/qixol.css
#ln -s $1/skin/frontend/base/default/images/qixol $2/skin/frontend/base/default/
#ln -s $1/skin/frontend/base/default/images/media $2/skin/frontend/base/default/
#ln -s $1/skin/frontend/base/default/js/lib $2/skin/frontend/base/default/js/
#ln -s $1/skin/frontend/base/default/js/qixol $2/skin/frontend/base/default/js/
#ln -s $1/skin/frontend/rwd/default/js/qixol $2/skin/frontend/rwd/default/js/

echo "Links set up"
echo "Now check Magento allows Symlinks:"
echo "System > Configuration > Developer > Template Settings > Allow Settings > Yes"
