Ext.define('SafeStartApp.controller.Contact', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],
    requires: [
        //models
        'SafeStartApp.model.Contact'
    ],

    config: {
        control: {
            sendButton: {
                tap: 'contactAction'
            }
        },

        refs: {
            sendButton: 'SafeStartContactForm > button[action=contact]',
            contactForm: 'SafeStartContactForm'
        }
    },

    contactAction: function () {
        if (!this.contactModel)this.contactModel = Ext.create('SafeStartApp.model.Contact');
        if (this.validateFormByModel(this.contactModel, this.getContactForm())) {
            SafeStartApp.AJAX('info/contact', this.getContactForm().getValues(), function (result) {
                SafeStartApp.showInfoMsg('Message sent');
            });
        }
    }
});