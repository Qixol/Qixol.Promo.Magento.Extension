var MissedPromotionsCheckout = Class.create(Checkout, {
    initialize: function($super,accordion, urls){
        $super(accordion, urls);
        //New Code Addded
        this.steps = ['login', 'missedpromotions' ,'billing', 'shipping', 'shipping_method', 'payment', 'review'];
    },
    setMethod: function(){
        if ($('login:guest') && $('login:guest').checked) {
            this.method = 'guest';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method:'guest'}}
            );
            Element.hide('register-customer-password');
            this.gotoSection('missedpromotions'); //New Code Here
        }
        else if($('login:register') && ($('login:register').checked || $('login:register').type == 'hidden')) {
            this.method = 'register';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method:'register'}}
            );
            Element.show('register-customer-password');
            this.gotoSection('missedpromotions'); //New Code Here
        }
        else{
            alert(Translator.translate('Please choose to register or to checkout as a guest'));
            return false;
        }
    }
});
//
//MissedPromotionsCheckout.prototype.gotoSection = function (section, reloadProgressBlock) {
//    // Adds class so that the page can be styled to only show the "Checkout Method" step
//    if ((this.currentStep == 'login' || this.currentStep == 'missedpromotions') && section == 'missedpromotions') {
//        $j('body').addClass('opc-has-progressed-from-login');
//    }
//
//    if (reloadProgressBlock) {
//        this.reloadProgressBlock(this.currentStep);
//    }
//    this.currentStep = section;
//    var sectionElement = $('opc-' + section);
//    sectionElement.addClassName('allow');
//    this.accordion.openSection('opc-' + section);
//
//    // Scroll viewport to top of checkout steps for smaller viewports
//    if (Modernizr.mq('(max-width: ' + bp.xsmall + 'px)')) {
//        $j('html,body').animate({scrollTop: $j('#checkoutSteps').offset().top}, 800);
//    }
//
//    if (!reloadProgressBlock) {
//        this.resetPreviousSteps();
//    }
//}

var MissedPromotions = Class.create();
MissedPromotions.prototype = {
    initialize: function(form, saveUrl){
        console.log(saveUrl);
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        this.saveUrl = saveUrl;
        this.validator = new Validation(this.form);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
 
    validate: function() {
        if(!this.validator.validate()) {
            return false;
        }
        return true;
    },
 
    save: function(){
        console.log('save was clicked');
        if (checkout.loadWaiting!=false) return;
        if (this.validate()) {
            checkout.setLoadWaiting('missedpromotions');
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
 
    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
    },
 
    nextStep: function(transport){
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
 
        if (response.error) {
            alert(response.message);
            return false;
        }
 
        if (response.update_section) {
            $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
        }
 
 
        if (response.goto_section) {
            checkout.gotoSection(response.goto_section);
            checkout.reloadProgressBlock();
            return;
        }
 
        checkout.setBilling();
    }
}