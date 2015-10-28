// https://code.google.com/p/strftime-js/
Date.ext = {};
Date.ext.util = {};
Date.ext.util.xPad = function(x, pad, r) {
    if (typeof(r) == "undefined") {
        r = 10
    }
    for (; parseInt(x, 10) < r && r > 1; r /= 10) {
        x = pad.toString() + x
    }
    return x.toString()
};
Date.prototype.locale = "en-GB";
if (document.getElementsByTagName("html") && document.getElementsByTagName("html")[0].lang) {
    Date.prototype.locale = document.getElementsByTagName("html")[0].lang
}
Date.ext.locales = {};
Date.ext.locales.en = {
    a: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    A: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    b: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    B: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    c: "%a %d %b %Y %T %Z",
    p: ["AM", "PM"],
    P: ["am", "pm"],
    x: "%d/%m/%y",
    X: "%T"
};
Date.ext.locales["en-US"] = Date.ext.locales.en;
Date.ext.locales["en-US"].c = "%a %d %b %Y %r %Z";
Date.ext.locales["en-US"].x = "%D";
Date.ext.locales["en-US"].X = "%r";
Date.ext.locales["en-GB"] = Date.ext.locales.en;
Date.ext.locales["en-AU"] = Date.ext.locales["en-GB"];
Date.ext.formats = {
    a: function(d) {
        return Date.ext.locales[d.locale].a[d.getDay()]
    },
    A: function(d) {
        return Date.ext.locales[d.locale].A[d.getDay()]
    },
    b: function(d) {
        return Date.ext.locales[d.locale].b[d.getMonth()]
    },
    B: function(d) {
        return Date.ext.locales[d.locale].B[d.getMonth()]
    },
    c: "toLocaleString",
    C: function(d) {
        return Date.ext.util.xPad(parseInt(d.getFullYear() / 100, 10), 0)
    },
    d: ["getDate", "0"],
    e: ["getDate", " "],
    g: function(d) {
        return Date.ext.util.xPad(parseInt(Date.ext.util.G(d) / 100, 10), 0)
    },
    G: function(d) {
        var y = d.getFullYear();
        var V = parseInt(Date.ext.formats.V(d), 10);
        var W = parseInt(Date.ext.formats.W(d), 10);
        if (W > V) {
            y++
        } else {
            if (W === 0 && V >= 52) {
                y--
            }
        }
        return y
    },
    H: ["getHours", "0"],
    I: function(d) {
        var I = d.getHours() % 12;
        return Date.ext.util.xPad(I === 0 ? 12 : I, 0)
    },
    j: function(d) {
        var ms = d - new Date("" + d.getFullYear() + "/1/1 GMT");
        ms += d.getTimezoneOffset() * 60000;
        var doy = parseInt(ms / 60000 / 60 / 24, 10) + 1;
        return Date.ext.util.xPad(doy, 0, 100)
    },
    m: function(d) {
        return Date.ext.util.xPad(d.getMonth() + 1, 0)
    },
    M: ["getMinutes", "0"],
    p: function(d) {
        return Date.ext.locales[d.locale].p[d.getHours() >= 12 ? 1 : 0]
    },
    P: function(d) {
        return Date.ext.locales[d.locale].P[d.getHours() >= 12 ? 1 : 0]
    },
    S: ["getSeconds", "0"],
    u: function(d) {
        var dow = d.getDay();
        return dow === 0 ? 7 : dow
    },
    U: function(d) {
        var doy = parseInt(Date.ext.formats.j(d), 10);
        var rdow = 6 - d.getDay();
        var woy = parseInt((doy + rdow) / 7, 10);
        return Date.ext.util.xPad(woy, 0)
    },
    V: function(d) {
        var woy = parseInt(Date.ext.formats.W(d), 10);
        var dow1_1 = (new Date("" + d.getFullYear() + "/1/1")).getDay();
        var idow = woy + (dow1_1 > 4 || dow1_1 <= 1 ? 0 : 1);
        if (idow == 53 && (new Date("" + d.getFullYear() + "/12/31")).getDay() < 4) {
            idow = 1
        } else {
            if (idow === 0) {
                idow = Date.ext.formats.V(new Date("" + (d.getFullYear() - 1) + "/12/31"))
            }
        }
        return Date.ext.util.xPad(idow, 0)
    },
    w: "getDay",
    W: function(d) {
        var doy = parseInt(Date.ext.formats.j(d), 10);
        var rdow = 7 - Date.ext.formats.u(d);
        var woy = parseInt((doy + rdow) / 7, 10);
        return Date.ext.util.xPad(woy, 0, 10)
    },
    y: function(d) {
        return Date.ext.util.xPad(d.getFullYear() % 100, 0)
    },
    Y: "getFullYear",
    z: function(d) {
        var o = d.getTimezoneOffset();
        var H = Date.ext.util.xPad(parseInt(Math.abs(o / 60), 10), 0);
        var M = Date.ext.util.xPad(o % 60, 0);
        return (o > 0 ? "-" : "+") + H + M
    },
    Z: function(d) {
        return d.toString().replace(/^.*\(([^)]+)\)$/, "$1")
    },
    "%": function(d) {
        return "%"
    }
};
Date.ext.aggregates = {
    c: "locale",
    D: "%m/%d/%y",
    h: "%b",
    n: "\n",
    r: "%I:%M:%S %p",
    R: "%H:%M",
    t: "\t",
    T: "%H:%M:%S",
    x: "locale",
    X: "locale"
};
Date.ext.aggregates.z = Date.ext.formats.z(new Date());
Date.ext.aggregates.Z = Date.ext.formats.Z(new Date());
Date.ext.unsupported = {};
Date.prototype.strftime = function(fmt) {
    if (!(this.locale in Date.ext.locales)) {
        if (this.locale.replace(/-[a-zA-Z]+$/, "") in Date.ext.locales) {
            this.locale = this.locale.replace(/-[a-zA-Z]+$/, "")
        } else {
            this.locale = "en-GB"
        }
    }
    var d = this;
    while (fmt.match(/%[cDhnrRtTxXzZ]/)) {
        fmt = fmt.replace(/%([cDhnrRtTxXzZ])/g, function(m0, m1) {
            var f = Date.ext.aggregates[m1];
            return (f == "locale" ? Date.ext.locales[d.locale][m1] : f)
        })
    }
    var str = fmt.replace(/%([aAbBCdegGHIjmMpPSuUVwWyY%])/g, function(m0, m1) {
        var f = Date.ext.formats[m1];
        if (typeof(f) == "string") {
            return d[f]()
        } else {
            if (typeof(f) == "function") {
                return f.call(d, d)
            } else {
                if (typeof(f) == "object" && typeof(f[0]) == "string") {
                    return Date.ext.util.xPad(d[f[0]](), f[1])
                } else {
                    return m1
                }
            }
        }
    });
    d = null;
    return str
};


mlmsystem.utils.colors = [
    '000000', '993300', '333399', '333333', '008000', '008080', '0000FF', '666699', '808080',
    'FF0000', 'FF9900', '99CC00', '339966', '33CCCC', '3366FF', '800080', '969696', 'FF00FF',
    'FFCC00', 'FFFF00', '00FF00', '00FFFF', '00CCFF', 'FF99CC', 'FFCC99', '99CCFF', 'CC99FF'
];


mlmsystem.utils.getMenu = function(actions, grid, selected) {
    var menu = [];
    var cls, icon, title, action = '';

    var has_delete = false;
    for (var i in actions) {
        if (!actions.hasOwnProperty(i)) {
            continue;
        }

        var a = actions[i];
        if (!a['menu']) {
            if (a == '-') {
                menu.push('-');
            }
            continue;
        } else if (menu.length > 0 && (/^sep/i.test(a['action']))) {
            menu.push('-');
            continue;
        }

        if (selected.length > 1) {
            if (!a['multiple']) {
                continue;
            } else if (typeof(a['multiple']) == 'string') {
                a['title'] = a['multiple'];
            }
        }

        cls = a['cls'] ? a['cls'] : '';
        icon = a['icon'] ? a['icon'] : '';
        title = a['title'] ? a['title'] : a['title'];
        action = a['action'] ? grid[a['action']] : '';

        menu.push({
            handler: action,
            text: String.format(
                '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                cls, icon, title
            )
        });
    }

    return menu;
};

mlmsystem.utils.renderActions = function(value, props, row) {
    var res = [];
    var cls, icon, title, action, item = '';
    for (var i in row.data.actions) {
        if (!row.data.actions.hasOwnProperty(i)) {
            continue;
        }
        var a = row.data.actions[i];
        if (!a['button']) {
            continue;
        }

        cls = a['cls'] ? a['cls'] : '';
        icon = a['icon'] ? a['icon'] : '';
        action = a['action'] ? a['action'] : '';
        title = a['title'] ? a['title'] : '';

        item = String.format(
            '<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
            cls, icon, action, title
        );

        res.push(item);
    }

    return String.format(
        '<ul class="mlmsystem-row-actions">{0}</ul>',
        res.join('')
    );
};


mlmsystem.utils.Hash = {
    get: function() {
        var vars = {},
            hash, splitter, hashes;
        if (!this.oldbrowser()) {
            var pos = window.location.href.indexOf('?');
            hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
            splitter = '&';
        } else {
            hashes = decodeURIComponent(window.location.hash.substr(1));
            splitter = '/';
        }

        if (hashes.length == 0) {
            return vars;
        } else {
            hashes = hashes.split(splitter);
        }

        for (var i in hashes) {
            if (hashes.hasOwnProperty(i)) {
                hash = hashes[i].split('=');
                if (typeof hash[1] == 'undefined') {
                    vars['anchor'] = hash[0];
                } else {
                    vars[hash[0]] = hash[1];
                }
            }
        }
        return vars;
    },

    set: function(vars) {
        var hash = '';
        for (var i in vars) {
            if (vars.hasOwnProperty(i)) {
                hash += '&' + i + '=' + vars[i];
            }
        }

        if (!this.oldbrowser()) {
            if (hash.length != 0) {
                hash = '?' + hash.substr(1);
            }
            window.history.pushState(hash, '', document.location.pathname + hash);
        } else {
            window.location.hash = hash.substr(1);
        }
    },

    add: function(key, val) {
        var hash = this.get();
        hash[key] = val;
        this.set(hash);
    },

    remove: function(key) {
        var hash = this.get();
        delete hash[key];
        this.set(hash);
    },

    clear: function() {
        this.set({});
    },

    oldbrowser: function() {
        return !(window.history && history.pushState);
    }
};


mlmsystem.utils.formatDate = function(string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[0-9]+$/.test(string) ? new Date(string * 1000) : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));

        return date.strftime(MODx.config.mlmsystem_date_format);
    } else {
        return '&nbsp;';
    }
};

mlmsystem.utils.renderBoolean = function(value, props, row) {

    return value ? String.format('<span class="green">{0}</span>', _('yes')) : String.format('<span class="red">{0}</span>', _('no'));
};

mlmsystem.utils.renderMoney = function(v, m) {
    v = String(v).replace(/[^0-9.\-]/g, "");
    v = (Math.round((v - 0) * 100)) / 100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v * 10 == Math.floor(v * 10)) ? v + "0" : v);
    v = String(v);

    var ps = v.split('.'),
        whole = ps[0],
        sub = ps[1] ? ',' + ps[1] : ',00',
        r = /(\d+)(\d{3})/;

    while (r.test(whole)) {
        whole = whole.replace(r, '$1' + '.' + '$2');
    }
    if (!m && MODx.lang.mlmsystem_money_unit) {
        m = MODx.lang.mlmsystem_money_unit;
    } else if (!m && !MODx.lang.mlmsystem_money_unit) {
        m = '';
    }
    return whole + sub + ' ' + m;
};

mlmsystem.utils.defaultContext = function() {
    var context = MODx.config.default_context;
    return String.format('{0}', context);
};


mlmsystem.utils.handleChecked = function(checkbox) {
    var workCount = checkbox.workCount;
    if (!!!workCount) {
        workCount = 1;
    }
    var hideLabel = checkbox.hideLabel;
    if (!!!hideLabel) {
        hideLabel = false;
    }

    var checked = checkbox.getValue();
    var nextField = checkbox.nextSibling();

    for (var i = 0; i < workCount; i++) {
        if (checked) {
            nextField.show().enable();
        } else {
            nextField.hide().disable();
        }
        nextField.hideLabel = hideLabel;
        nextField = nextField.nextSibling();
    }
    return true;
};


mlmsystem.utils.handleColor = function(palette, color) {
    var colorField = palette.nextSibling();
    if (!!color) {
        colorField.setValue(color);
    } else {
        palette.value = colorField.getValue();
    }

    return true;
};


mlmsystem.utils.renderColor = function(value, props, row) {
    return String.format('<span class="mlmsystem-grid-color" style="background: #{0}"></span>', value);
};


mlmsystem.utils.renderReplace = function(value, replace, color) {
    if (!value) {
        return '';
    } else if (!replace) {
        return value;
    }
    if (!color) {
        return String.format('<span>{0}</span>', replace);
    }
    return String.format('<span class="mlmsystem-render-color" style="color: #{1}">{0}</span>', replace, color);
};


mlmsystem.utils.Link = function(url, value) {
    if (!value) {
        value = _('mlmsystem_url');
    }
    if (!url) {
        return value;
    }
    return String.format('<a href="{0}" target="_blank" class="mlmsystem-link green">{1}</a>', url, value);
};


mlmsystem.utils.userLink = function(value, id) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }
    var action = MODx.action ? MODx.action['security/user/update'] : 'security/user/update';
    var url = 'index.php?a=' + action + '&id=' + id;

    return String.format('<a href="{0}" target="_blank" class="user-link green">{1}</a>', url, value);
};


mlmsystem.utils.objectLink = function (value, id, cls) {

    switch (cls) {
        case 'MlmSystemClient':
            cls = 'clients';
            break;
        case 'MlmSystemProfit':
            cls = 'profits';
            break;
        case 'MlmSystemLog':
            cls = 'logs';
            break;
        default:
            cls = '';
            break;
    }

    var action = mlmsystem.config.menu_actions[cls];
    if (!action || !id) {
        return mlmsystem.utils.Link(null, value);
    }
    var url = 'index.php?a=' + action + '&' + cls + '=' + id;

    return mlmsystem.utils.Link(url, value);
};