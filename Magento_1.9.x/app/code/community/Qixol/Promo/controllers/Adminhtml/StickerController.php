<?php
class Qixol_Promo_Adminhtml_StickerController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('qixol/items')
                ->_addBreadcrumb(Mage::helper('qixol')->__('Sticker Manager'), Mage::helper('qixol')->__('Sticker Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('qixol/sticker')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('sticker_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('qixol/items');

            $this->_addBreadcrumb(Mage::helper('qixol')->__('Sticker Manager'), Mage::helper('qixol')->__('Sticker Manager'));
            $this->_addBreadcrumb(Mage::helper('qixol')->__('Item Sticker'), Mage::helper('qixol')->__('Item Sticker'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('qixol/adminhtml_sticker_edit'))
                    ->_addLeft($this->getLayout()->createBlock('qixol/adminhtml_sticker_edit_tabs'));
            $version = substr(Mage::getVersion(), 0, 3);
            if (($version=='1.4' || $version=='1.5') && Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('qixol')->__('Sticker does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        $imagedata = array();
        if (!empty($_FILES['filename']['name'])) {
            try {
                $ext = substr($_FILES['filename']['name'], strrpos($_FILES['filename']['name'], '.') + 1);
                $fname = 'File-' . time() . '.' . $ext;
                $uploader = new Varien_File_Uploader('filename');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media').DS.'custom'.DS.'stickers';

                $uploader->save($path, $fname);

                $imagedata['filename'] = 'custom/stickers/'.$fname;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        if ($data = $this->getRequest()->getPost()) {
            if (!empty($imagedata['filename'])) {
                $data['filename'] = $imagedata['filename'];
            } else {
                if (isset($data['filename']['delete']) && $data['filename']['delete'] == 1) {
                    if ($data['filename']['value'] != '') {
                        $_helper = Mage::helper('qixol');
                        $this->removeFile(Mage::getBaseDir('media').DS.'custom'.DS.'stickers'.DS.$_helper->updateDirSepereator($data['filename']['value']));
                    }
                    $data['filename'] = '';
                } else {
                    unset($data['filename']);
                }
            }
            $model = Mage::getModel('qixol/sticker');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('qixol')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('qixol')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('qixol/sticker')->load($this->getRequest()->getParam('id'));
                $_helper = Mage::helper('qixol');
                $filePath = Mage::getBaseDir('media').DS.'custom'.DS.'stickers'.DS.$_helper->updateDirSepereator($model->getFilename());
                $model->delete();
                $this->removeFile($filePath);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('qixol')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $bannerIds = $this->getRequest()->getParam('sticker');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('qixol')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $model = Mage::getModel('qixol/sticker')->load($bannerId);
                    $_helper = Mage::helper('qixol');
                    $filePath = Mage::getBaseDir('media').DS.'custom'.DS.'stickers'.DS.$_helper->updateDirSepereator($model->getFilename());
                    $model->delete();
                    $this->removeFile($filePath);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('qixol')->__(
                                'Total of %d record(s) were successfully deleted', count($bannerIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $bannerIds = $this->getRequest()->getParam('sticker');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getSingleton('qixol/sticker')
                                    ->load($bannerId)
                                    ->setStatus($this->getRequest()->getParam('status'))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($bannerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function removeFile($file) {
        try {
            $io = new Varien_Io_File();
            $result = $io->rmdir($file, true);
        } catch (Exception $e) {

        }
    }

}