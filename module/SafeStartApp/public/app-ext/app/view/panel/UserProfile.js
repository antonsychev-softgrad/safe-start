Ext.define('SafeStartExt.view.panel.UserProfile', {
	extend: 'Ext.window.Window',
	requires: [
		'SafeStartExt.view.form.UserProfile'
	],
	xtype: 'SafeStartExtUserProfileWindow',
	
	profileForm: null,
	padding: 5,
    border: 0,
	modal: true,
	layout: 'fit',
	scrollable: false,
	cls: 'sfa-modal-form',
	closeAction: 'hide',

	initComponent: function() {
		Ext.apply(this, {
			buttons: [{
				text: 'Cancel',
                ui: 'blue',
                scale: 'medium',
				handler: function() {
					this.up('SafeStartExtUserProfileWindow').hide();
				}
			}, {
				xtype: 'box',
				flex: 1
			}, {
				text: 'Save',
                ui: 'blue',
                scale: 'medium',
				action: 'save-data',
				handler: function() {
					this.up('SafeStartExtUserProfileWindow').fireEvent('updateProfileAction', this.up('SafeStartExtUserProfileWindow'));
				}
			}],
			items: [{
				xtype: 'SafeStartExtUserProfileForm',
                border: 0,
				record: SafeStartExt.getApplication().getUserRecord()
			}]
		});
		this.callParent();
	}
});
