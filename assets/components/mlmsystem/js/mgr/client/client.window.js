mlmsystem.window.CreateUpdate = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 750,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/client/create',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    mlmsystem.window.CreateUpdate.superclass.constructor.call(this, config);

    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.CreateUpdate, MODx.Window, {

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
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            activeTab: 0,
            autoHeight: true,
            items: this.getTabs(config)
        }]
    },


    getTabs: function (config) {

        var tabs = [];
        var add = {
            client: {
                layout: 'form',
                items: this.getClient(config)
            },
            story_client: {
                layout: 'form',
                items: this.getClientStory(config)
            },
            story_balance: {
                layout: 'form',
                items: this.getBalanceStory(config)
            },
            story_operation: {
                layout: 'form',
                items: this.getOperationStory(config)
            }
        };

        for (var i = 0; i < mlmsystem.config.client_window_update_tabs.length; i++) {
            var tab = mlmsystem.config.client_window_update_tabs[i];
            if (add[tab]) {
                Ext.applyIf(add[tab], {
                    title: _('mlmsystem_' + tab)
                });
                tabs.push(add[tab]);
            }
        }

        return tabs;
    },

    getClient: function(config) {
        return [{
            layout: 'column',
            defaults: {
                msgTarget: 'under',
                border: false
            },
            style: 'padding:10px 0px 0px 0px;text-align:center;',
            items: [{
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    /*name: 'user_username',*/
                    fieldLabel: _('mlmsystem_user'),
                    anchor: '100%',
                    html: mlmsystem.utils.userLink(config.record.user_username, config.record.user_id)
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'client_balance',
                    fieldLabel: _('mlmsystem_balance'),
                    anchor: '100%'
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'client_incoming',
                    fieldLabel: _('mlmsystem_incoming'),
                    anchor: '100%',
                    cls: 'green'
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'client_outcoming',
                    fieldLabel: _('mlmsystem_outcoming'),
                    anchor: '100%',
                    cls: 'red'
                }]
            }]
        }, {
            layout: 'column',
            defaults: {
                msgTarget: 'under',
                border: false
            },
            style: 'padding:10px 0px 0px 0px;text-align:center;',
            items: [{
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    /* name: 'parent_user_username',*/
                    fieldLabel: _('mlmsystem_parent'),
                    anchor: '100%',
                    html: mlmsystem.utils.userLink(config.record.parent_user_username, config.record.parent_user_id)
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    /* name: 'client_url_referrer',*/
                    fieldLabel: _('mlmsystem_url_referrer'),
                    anchor: '100%',
                    html: mlmsystem.utils.Link(config.record.client_url_referrer)
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'client_date_createdon',
                    fieldLabel: _('mlmsystem_createdon'),
                    anchor: '100%'
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'client_date_updatedon',
                    fieldLabel: _('mlmsystem_updatedon'),
                    anchor: '100%'
                }]
            }]
        }, /*{
            xtype: 'textfield',
            fieldLabel: _('mlmsystem_name'),
            name: 'username',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'mlmsystem-combo-client',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_parent'),
            hiddenName: 'parent',
            anchor: '99%',
            allowBlank: true
        }*/];
    },

    getClientStory: function(config) {

    },

    getBalanceStory: function(config) {

    },

    getOperationStory: function(config) {

    }

});
Ext.reg('mlmsystem-client-window-update', mlmsystem.window.CreateUpdate);

/* ------------------------------------------------------- */

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
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: this.getRightFields(config)
                    }]
                }]
            }]
        }];
    },

    getLeftFields: function(config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('mlmsystem_name'),
            name: 'username',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'mlmsystem-combo-client',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_parent'),
            hiddenName: 'parent',
            anchor: '99%',
            allowBlank: true
        }];
    },

    getRightFields: function(config) {
        return [{
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

/* ------------------------------------------------------- */

mlmsystem.window.ChangeParent = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/client/update',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    mlmsystem.window.ChangeParent.superclass.constructor.call(this, config);
    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.ChangeParent, MODx.Window, {

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

        return [/*{
            xtype: 'hidden',
            name: 'id'
        },*/ {
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
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: this.getRightFields(config)
                    }]
                }]
            }]
        }];
    },

    getLeftFields: function(config) {
        return [{
            xtype: 'mlmsystem-combo-client',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_client'),
            hiddenName: 'id',
            anchor: '99%',
            allowBlank: false,
            listeners: {
                select: {
                    fn: function(f) {
                        this.handleClient(f.getValue());
                    },
                    scope: this
                }
            }

        }];
    },

    getRightFields: function(config) {
        return [{
            xtype: 'mlmsystem-combo-client',
            custm: true,
            clear: true,
            class: config.class,
            client: config.record.id,
            fieldLabel: _('mlmsystem_parent'),
            hiddenName: 'parent',
            anchor: '99%',
            allowBlank: true
        }];
    },

    handleClient: function(value) {
        var f = this.fp.getForm();
        var parent = f.findField('parent');

        parent.setValue('');
        parent.fireEvent('select');
        parent.store.baseParams.client = value;
        parent.store.load();
    }

});
Ext.reg('mlmsystem-client-window-change-parent', mlmsystem.window.ChangeParent);

/* ------------------------------------------------------- */

mlmsystem.window.CorrectBalance = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/misc/balance/update',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    mlmsystem.window.CorrectBalance.superclass.constructor.call(this, config);
    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.CorrectBalance, MODx.Window, {

    getKeys: function () {
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

        return [/*{
         xtype: 'hidden',
         name: 'id'
         }, */{
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
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: this.getRightFields(config)
                    }]
                }]
            }]
        }];
    },

    getLeftFields: function(config) {
        return [{
            xtype: 'mlmsystem-combo-client',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_client'),
            hiddenName: 'id',
            anchor: '99%',
            allowBlank: false,
            listeners: {
                select: {
                    fn: function(f) {
                        this.handleClient(f.getValue());
                    },
                    scope: this
                }
            }

        }, {
            xtype: 'numberfield',
            class: config.class,
            fieldLabel: _('mlmsystem_sum'),
            name: 'change_balance_sum',
            anchor: '99%',
            allowBlank: false
        }];
    },

    getRightFields: function(config) {
        return [{
            xtype: 'numberfield',
            fieldLabel: _('mlmsystem_balance'),
            msgTarget: 'under',
            name: 'balance',
            anchor: '99%',
            allowBlank: true,
            disabled: true
        }, {
            xtype: 'mlmsystem-combo-type-change-balance',
            custm: true,
            clear: true,
            class: config.class,
            fieldLabel: _('mlmsystem_type_change'),
            name: 'change_balance_type',
            anchor: '99%',
            allowBlank: false
        }];
    },

    handleClient: function(value) {
        var f = this.fp.getForm();
        var balance = f.findField('balance');

        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/client/get',
                id: value
            },
            listeners: {
                success: {
                    fn: function(r) {
                        var record = r.object;
                        balance.setValue(record.balance);
                    },
                    scope: this
                }
            }
        });
    }

});
Ext.reg('mlmsystem-client-window-change-balance', mlmsystem.window.CorrectBalance);