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

ln -s $1/app/etc/modules/Holbi_Qixol.xml $2/app/etc/modules/Holbi_Qixol.xml

ln -s $1/app/code/community/Holbi $2/app/code/community/Holbi

ln -s $1/app/design/adminhtml/default/default/layout/qixol.xml $2/app/design/adminhtml/default/default/layout/qixol.xml

ln -s $1/app/design/adminhtml/default/default/template/qixol $2/app/design/adminhtml/default/default/template/qixol

ln -s $1/app/design/frontend/base/default/layout/qixol.xml $2/app/design/frontend/base/default/layout/qixol.xml

ln -s $1/app/design/frontend/base/default/template/qixol $2/app/design/frontend/base/default/template/qixol

ln -s $1/app/design/frontend/rwd/default/template/qixol $2/app/design/frontend/rwd/default/template/qixol

ln -s $1/app/locale/en_US/Holbi_Qixol.csv $2/app/locale/en_US/Holbi_Qixol.csv

echo "Links set up"
echo "Now check Magento allows Symlinks:"
echo "System > Configuration > Developer > Template Settings > Allow Settings > Yes"
