Ext.define('SafeStartExt.view.panel.UserProfile', {
	extend: 'Ext.window.Window',
	requires: [
		'SafeStartExt.view.form.UserProfile'
	],
	xtype: 'SafeStartExtUserProfileWindow',
	
	profileForm: null,
	padding: 5,
	modal: true,
	layout: 'fit',
	scrollable: false,
	cls: 'sfa-modal-form',
	closeAction: 'hide',

	initComponent: function() {
		Ext.apply(this, {
			buttons: [{
				text: 'Cancel',
				handler: function() {
					this.up('SafeStartExtUserProfileWindow').hide();
				}
			}, {
				xtype: 'box',
				flex: 1
			}, {
				text: 'Save',
				action: 'save-data',
				handler: function() {
					this.up('SafeStartExtUserProfileWindow').fireEvent('updateProfileAction', this.up('SafeStartExtUserProfileWindow'));
				}
			}],
			items: [{
				xtype: 'SafeStartExtUserProfileForm',
				record: SafeStartExt.getApplication().getUserRecord()
			}]
		});
		this.callParent();
	}
});