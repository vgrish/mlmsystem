mlmsystem.window.CreateProfit = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 750,
        autoHeight: true,
        url: mlmsystem.config.connector_url,
        action: 'mgr/profit/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function() {
                this.submit()
            },
            scope: this
        }]
    });
    mlmsystem.window.CreateProfit.superclass.constructor.call(this, config);
    if (!config.update) {
        config.update = false;
    }
};
Ext.extend(mlmsystem.window.CreateProfit, MODx.Window, {

    getFields: function(config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'modx-tabs',
            defaults: {
                border: false,
                autoHeight: true
            },
            border: true,
            activeTab: 0,
            autoHeight: true,
            items: this.getTabs(config)
        }]
    },

    getTabs: function (config) {

        var tabs = [];
        var add = {
            profit: {
                layout: 'form',
                items: this.getProfit(config)
            },
            user_group: {
                layout: 'form',
                items: this.getUserGroup(config),
                disabled: !config.update
            },
            resorce_group: {
                layout: 'form',
                items: this.getResourceGroup(config),
                disabled: !config.update
            },
            //product_group: {
            //    layout: 'form',
            //    items: this.getProfit(config),
            //    disabled: !config.update
            //}
        };

        for (var i = 0; i < mlmsystem.config.profit_window_update_tabs.length; i++) {
            var tab = mlmsystem.config.profit_window_update_tabs[i];
            if (add[tab]) {
                Ext.applyIf(add[tab], {
                    title: _('mlmsystem_tab_' + tab)
                });
                tabs.push(add[tab]);
            }
        }

        return tabs;
    },

    getUserGroup: function(config) {
        return [{
            items: {
                xtype: 'mlmsystem-grid-profit-group',
                identifier: config.record.id,
                class: 'modUserGroup',
                record: config.record
            }
        }];
    },

    getResourceGroup: function(config) {
        return [{
            items: {
                xtype: 'mlmsystem-grid-profit-group',
                identifier: config.record.id,
                class: 'modResourceGroup',
                record: config.record
            }
        }];
    },

    getProfit: function(config) {
        return [{
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: _('mlmsystem_name'),
                            name: 'name',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }, {
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: []
                    }]
                }, {
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: _('mlmsystem_class'),
                            name: 'class',
                            anchor: '99%',
                            allowBlank: true
                        }]
                    }, {
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: [{
                            xtype: 'mlmsystem-combo-event',
                            custm: true,
                            clear: true,
                            fieldLabel: _('mlmsystem_event'),
                            name: 'event',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }]
                }, {
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .249,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'numberfield',
                            fieldLabel: _('mlmsystem_profit'),
                            description: _('mlmsystem_profit_decs'),
                            name: 'profit',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }, {
                        columnWidth: .249,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: _('mlmsystem_profit1'),
                            description: _('mlmsystem_profit1_decs'),
                            name: 'profit1',
                            anchor: '99%',
                            allowBlank: false,
                            maskRe: /[0123456789\.\-%]/
                        }]
                    }, {
                        columnWidth: .249,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: _('mlmsystem_profit2'),
                            description: _('mlmsystem_profit2_decs'),
                            name: 'profit2',
                            anchor: '99%',
                            allowBlank: false,
                            maskRe: /[0123456789\.\-%]/
                        }]
                    }, {
                        columnWidth: .25,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: _('mlmsystem_profit3'),
                            description: _('mlmsystem_profit3_decs'),
                            name: 'profit3',
                            anchor: '99%',
                            allowBlank: false,
                            maskRe: /[0123456789\.\-%]/
                        }]
                    }]
                }]
            }, {
                layout: 'form',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'xcheckbox',
                            hideLabel: true,
                            boxLabel: _('mlmsystem_profit_parent'),
                            description: _('mlmsystem_profit_parent_decs'),
                            name: 'parent_active',
                            checked: false,
                            listeners: {
                                check: mlmsystem.utils.handleChecked,
                                afterrender: mlmsystem.utils.handleChecked
                            }
                        }, {
                            xtype: 'textarea',
                            fieldLabel: '',
                            msgTarget: 'under',
                            name: 'parent_profit',
                            anchor: '99.5%',
                            height: 50,
                            allowBlank: true
                        }]
                    }, {
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: [{
                            xtype: 'xcheckbox',
                            hideLabel: true,
                            boxLabel: _('mlmsystem_profit_children'),
                            description: _('mlmsystem_profit_children_decs'),
                            name: 'children_active',
                            checked: false,
                            listeners: {
                                check: mlmsystem.utils.handleChecked,
                                afterrender: mlmsystem.utils.handleChecked
                            }
                        }, {
                            xtype: 'textarea',
                            fieldLabel: '',
                            msgTarget: 'under',
                            name: 'children_profit',
                            anchor: '99.5%',
                            height: 50,
                            allowBlank: true
                        }]
                    }]
                }, {
                    xtype: 'xcheckbox',
                    hideLabel: true,
                    boxLabel: _('mlmsystem_properties'),
                    name: '_properties',
                    checked: false,
                    listeners: {
                        check: mlmsystem.utils.handleChecked,
                        afterrender: mlmsystem.utils.handleChecked
                    }
                }, {
                    xtype: 'textarea',
                    fieldLabel: '',
                    msgTarget: 'under',
                    name: 'properties',
                    anchor: '99.5%',
                    height: 50,
                    allowBlank: true
                }]
            }, {
                layout: 'form',
                items: [{
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
                    anchor: '99.5%',
                    height: 50,
                    allowBlank: true
                }]
            }, {
                xtype: 'checkboxgroup',
                hideLabel: true,
                /*fieldLabel: '',*/
                columns: 4,
                items: [{
                    xtype: 'xcheckbox',
                    boxLabel: _('mlmsystem_active'),
                    name: 'active',
                    checked: config.record.active
                }]
            }]
        }];
    }


});
Ext.reg('mlmsystem-profit-window-create', mlmsystem.window.CreateProfit);
