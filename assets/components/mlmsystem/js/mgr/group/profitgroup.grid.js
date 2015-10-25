mlmsystem.grid.ProfitGroup = function(config) {
    config = config || {};

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        layout: 'anchor',
        id: config.id,
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/group/getlist',
            identifier: config.record.id,
            class: config.class
        },
        save_action: 'mgr/group/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),

        sm: this.sm,
        autoHeight: true,
        paging: true,
        pageSize: 5,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        cls: 'mlmsystem-grid',
        bodyCssClass: 'grid-with-buttons'

    });
    mlmsystem.grid.ProfitGroup.superclass.constructor.call(this, config);
};
Ext.extend(mlmsystem.grid.ProfitGroup, MODx.grid.Grid, {
    windows: {},

    getFields: function(config) {
        var fields = ['id', 'name', 'identifier', 'class', 'profit', 'group', 'type', 'actions'];

        return fields;
    },

    getTopBar: function(config) {
        var tbar = [];

        tbar.push({
            text: '<i class="fa fa-cogs"></i> ', // + _('mlmsystem_actions'),
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('mlmsystem_action_create_group'),
                cls: 'mlmsystem-cogs',
                handler: this.createGroup,
                scope: this
            }, {
                text: '<i class="fa fa-share"></i> ' + _('mlmsystem_action_update_group'),
                cls: 'mlmsystem-cogs',
                handler: this.updateGroup,
                scope: this
            }]
        });

        tbar.push('->');
        tbar.push({
            xtype: 'mlmsystem-combo-profit-group',
            width: 210,
            custm: true,
            clear: true,
            identifier: config.identifier,
            class: config.class,
            listeners: {
                select: {
                    fn: this.addGroup,
                    scope: this
                }
            }
        });
        tbar.push({
            xtype: 'spacer',
            style: 'width:1px;'
        });

        return tbar;
    },

    getColumns: function(config) {
        var columns = [ /*this.exp, */ this.sm];
        var add = {
            id: {
                width: 15,
                sortable: true,
            },
            identifier: {
                width: 25,
                sortable: true,
                hidden: true,
            },
            class: {
                width: 25,
                sortable: true,
                hidden: true,
            },
            name: {
                width: 25,
                sortable: true,
            },
            type: {
                width: 25,
                sortable: true,
                renderer: function(value, metaData, record) {
                    return value ? String.format('<span class="green">{0}</span>', _('mlmsystem_group_in')) : String.format('<span class="red">{0}</span>', _('mlmsystem_group_out'));
                }
            },
            profit: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false,
                    maskRe: /[0123456789\.\-%]/
                }
            },
            actions: {
                width: 25,
                sortable: false,
                renderer: mlmsystem.utils.renderActions,
                id: 'actions'
            }
        };
        for (var field in add) {
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('mlmsystem_header_' + field),
                    tooltip: _('mlmsystem_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            }
        }

        return columns;
    },

    getMenu: function(grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = mlmsystem.utils.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },

    onClick: function(e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    setAction: function(method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/group/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function() {
                        this.refresh();

                        var tbar = this.getTopToolbar();
                        tbar.getComponent(tbar.items.length - 2).getStore().reload();
                    },
                    scope: this
                },
                failure: {
                    fn: function(response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    setIn: function(btn, e) {
        this.setAction('setproperty', 'type', 1);
    },

    setOut: function(btn, e) {
        this.setAction('setproperty', 'type', 0);
    },

    addGroup: function(combo, row) {
        if (!row) {
            return false;
        }
        combo.reset();

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/group/create',
                identifier: this.config.identifier,
                class: this.config.class,
                group: row.id
            },
            listeners: {
                success: {
                    fn: function(r) {
                        combo.getStore().reload();
                        this.refresh();
                    },
                    scope: this
                }
            }
        });
    },

    remove: function() {
        Ext.MessageBox.confirm(
            _('mlmsystem_action_remove'),
            _('mlmsystem_confirm_remove'),
            function(val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    createGroup: function() {

        var createPage = '';

        switch (this.config.class) {
            case 'modUserGroup':
                createPage = 'security/permission';
                break;
            case 'modResourceGroup':
                createPage = 'security/resourcegroup';
                break;
            default:
                return false;
        }

        window.open(MODx.config.manager_url + '?a=' + createPage);
    },

    updateGroup: function() {

        var updatePage = '';

        switch (this.config.class) {
            case 'modUserGroup':
                updatePage = 'security/permission';
                break;
            case 'modResourceGroup':
                updatePage = 'security/resourcegroup';
                break;
            default:
                return false;
        }

        window.open(MODx.config.manager_url + '?a=' + updatePage);
    },

    _updateRow: function(response) {
        this.refresh();
    },

    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('mlmsystem-grid-profit-group', mlmsystem.grid.ProfitGroup);
