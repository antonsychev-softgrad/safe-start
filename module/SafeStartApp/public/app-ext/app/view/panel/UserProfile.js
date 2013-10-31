Ext.define('SafeStartExt.view.panel.UserProfile', {
	extend: 'Ext.window.Window',
	requires: [
		'SafeStartExt.view.form.UserProfile'
	],
	xtype: 'SafeStartExtUserProfileWindow',
	profileForm: null,
	padding: 5,
	initComponent: function () {
		var me = this;
		Ext.apply(this, {
			modal: true,
			layout: 'fit',
			scrollable: false,
			cls: 'sfa-modal-form',
			closeAction: 'hide',
			buttons: [{
				text: 'Cancel',
				ui: 'action',
				handler: function () {
					this.up('SafeStartExtUserProfileWindow').hide();
				}
			}, {
				xtype: 'box',
				flex: 1
			}, {
				text: 'Save',
				action: 'save-data',
				ui: 'confirm',
				handler: function () {
					this.up('SafeStartExtUserProfileWindow').fireEvent('updateProfileAction', this.up('SafeStartExtUserProfileWindow'));
				}
			}
			]
		});
		this.callParent();
		this.profileForm = Ext.create('SafeStartExt.view.form.UserProfile');
		this.add(this.profileForm);
		this.profileForm.loadRecord(SafeStartExt.getApplication().getUserRecord())
	}

});