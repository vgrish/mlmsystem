mlmsystem.window.CreateTypeChange = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/change/type/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    mlmsystem.window.CreateTypeChange.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.window.CreateTypeChange, MODx.Window, {

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
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .49,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'mlmsystem-combo-object-field',
                            custm: true,
                            clear: true,
                            class: config.record.class,
                            fieldLabel: _('mlmsystem_field'),
                            name: 'field',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: [{
                            xtype: 'mlmsystem-combo-mode-change',
                            custm: true,
                            clear: true,
                            fieldLabel: _('mlmsystem_mode'),
                            name: 'mode',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }]
                }]
            }]
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
            }]
        }];
    }

});
Ext.reg('mlmsystem-type-change-window-create', mlmsystem.window.CreateTypeChange);
