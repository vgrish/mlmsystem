mlmsystem.window.CreateClient = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/client/create',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    mlmsystem.window.CreateClient.superclass.constructor.call(this, config);

    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.CreateClient, MODx.Window, {

    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: this.submit,
            scope: this
        }];
    },

    getButtons: function (config) {
        return [{
            text: !config.update ? _('create') : _('save'),
            scope: this,
            handler: function () {
                this.submit();
            }
        }];
    },

    getFields: function (config) {

        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'textfield',
            fieldLabel: _('mlmsystem_name'),
            name: 'username',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'textfield',
            fieldLabel: _('mlmsystem_email'),
            name: 'email',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'mlmsystem-combo-status',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_status'),
            hiddenName: 'status',
            anchor: '99%',
            allowBlank: false
        }];
    }

});
Ext.reg('mlmsystem-client-window-create', mlmsystem.window.CreateClient);