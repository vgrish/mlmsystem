mlmsystem.window.logView = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('view'),
        width: 750,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/client/create',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    mlmsystem.window.logView.superclass.constructor.call(this, config);

    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.logView, MODx.Window, {

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
            text: _('cancel'),
            scope: this,
            handler: function () {
                this.hide();
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
            log: {
                layout: 'form',
                items: this.getLog(config)
            },
            user: {
                layout: 'form',
                items: this.getUser(config)
            }
        };

        for (var i = 0; i < mlmsystem.config.log_window_view_tabs.length; i++) {
            var tab = mlmsystem.config.log_window_view_tabs[i];
            if (add[tab]) {
                Ext.applyIf(add[tab], {
                    title: _('mlmsystem_tab_' + tab)
                });
                tabs.push(add[tab]);
            }
        }

        return tabs;
    },

    getLog: function(config) {
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
                    /*name: 'log_class_name',*/
                    fieldLabel: _('mlmsystem_object'),
                    anchor: '100%',
                    html: mlmsystem.utils.objectLink(config.record.log_class_name, config.record.identifier, config.record.log_class)
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    /*name: 'type_changes_name',*/
                    fieldLabel: config.record.type_changes_name || _('mlmsystem_name'),
                    anchor: '100%',
                    html: config.record.type_changes_description
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'log_target',
                    fieldLabel: _('mlmsystem_field'),
                    anchor: '100%',
                    cls: 'green'
                }]
            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    name: 'log_value',
                    fieldLabel: _('mlmsystem_value'),
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
                    anchor: '100%'
                }]
            }, {
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
                    name: 'log_timestamp',
                    fieldLabel: _('mlmsystem_createdon'),
                    anchor: '100%'
                }]

            }, {
                columnWidth: .25,
                layout: 'form',
                items: [{
                    xtype: 'displayfield',
                    anchor: '100%'
                }]
            }]
        }, {
            xtype: 'spacer',
            style: 'width:1px;height:10px;'
        }];
    },

    getUser: function(config) {
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
        }];
    }


});
Ext.reg('mlmsystem-log-window-view', mlmsystem.window.logView);