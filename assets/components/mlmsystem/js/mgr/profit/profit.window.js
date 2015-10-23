mlmsystem.window.CreateProfit = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/profit/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    mlmsystem.window.CreateProfit.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.window.CreateProfit, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'hidden',
            name: 'class'
        }, {
            xtype: 'textfield',
            fieldLabel: _('mlmsystem_name'),
            name: 'name',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('mlmsystem_email_user'),
            name: 'email_user',
            checked: false,
            workCount: 1,
            listeners: {
                check: mlmsystem.utils.handleChecked,
                afterrender: mlmsystem.utils.handleChecked
            }
        }, {
            xtype: 'mlmsystem-combo-chunk',
            custm: true,
            clear: true,
            fieldLabel: _('mlmsystem_subject_user'),
            msgTarget: 'under',
            name: 'tpl_user',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('mlmsystem_email_manager'),
            name: 'email_manager',
            checked: false,
            workCount: 1,
            listeners: {
                check: mlmsystem.utils.handleChecked,
                afterrender: mlmsystem.utils.handleChecked
            }
        }, {
            xtype: 'mlmsystem-combo-chunk',
            custm: true,
            clear: true,
            fieldLabel: _('mlmsystem_subject_manager'),
            msgTarget: 'under',
            name: 'tpl_manager',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('mlmsystem_description'),
            name: '_description',
            checked: false,
            listeners: {
                check: mlmsystem.utils.handleChecked,
                afterrender: mlmsystem.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: '',
            msgTarget: 'under',
            name: 'description',
            anchor: '99%',
            height: 50,
            allowBlank: true
        }, {
            xtype: 'checkboxgroup',
            hideLabel: true,
            /*fieldLabel: '',*/
            columns: 3,
            items: [{
                xtype: 'xcheckbox',
                boxLabel: _('mlmsystem_active'),
                name: 'active',
                checked: config.record.active
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('mlmsystem_status_final'),
                name: 'final',
                checked: config.record.final
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('mlmsystem_status_fixed'),
                name: 'fixed',
                checked: config.record.fixed
            }]
        }];
    }

});
Ext.reg('mlmsystem-profit-window-create', mlmsystem.window.CreateProfit);
