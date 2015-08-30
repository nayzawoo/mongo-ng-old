GenghisJSON = {
    parse: function(src) {
        if (typeof src !== 'string') {
            src = String(src);
        }

        src = 'var __genghis_json__ = ' + src;

        var opts = {
            loc:      true,
            raw:      true,
            tokens:   true,
            tolerant: true,
            range:    true
        };

        var allowedCalls = {
            'ObjectId': true,
            'Date':     true,
            'ISODate':  true,
            'DBRef':    true,
            'RegExp':   true,
            'BinData':  true
        };

        var allowedPropertyValues = {
            'Literal':          true,
            'ObjectExpression': true,
            'ArrayExpression':  true,
            'NewExpression':    true,
            'CallExpression':   true,
            'UnaryExpression':  true
        };

        var errors = [];

        function addError(msg, node) {
            // only add one error per node
            if (!node.error) {
                error      = new Error(msg);
                error.loc  = node.loc;
                error.node = node;
                node.error = error;
                errors.push(error);
            }
        }

        function throwErrors(errors) {
            var error = new Error('' + errors.length + ' parse error' + (errors.length === 1 ? '' : 's'));
            error.errors = errors;
            throw error;
        }

        function replaceCallExpression(src) {
            function ObjectId(id) {
                return {
                    '$type': 'ObjectId',
                    '$value': id ? id.toString() : null
                };
            }

            function GenghisDate(date) {
                return new ISODate(date);
            }

            function ISODate(date) {
                function ISODateString(d) {
                    function pad(n, l) {
                        l = l || 2;
                        return (new Array(l + 1).join('0') + n).substr(0 - l);
                    }

                    function msString(ms) {
                        return ms ? ('.' + pad(ms, 3)) : '';
                    }

                    d = new Date(d);

                    if (typeof d.toISOString === 'function') {
                        // Cheat code for ES5
                        return d.toISOString();
                    } else {
                        return pad(d.getUTCFullYear(), 4)+'-'
                            + pad(d.getUTCMonth()+1)+'-'
                            + pad(d.getUTCDate())+'T'
                            + pad(d.getUTCHours())+':'
                            + pad(d.getUTCMinutes())+':'
                            + pad(d.getUTCSeconds())
                            + msString(d.getUTCMilliseconds())+'Z';
                    }
                }

                return {
                    '$type': 'ISODate',
                    '$value': date ? ISODateString(date) : null
                };
            }

            // DBRef isn't so much a custom type as it is a hash factory...
            // we won't bother using a $type for this one.
            function DBRef(ref, id) {
                return {
                    '$ref': ref,
                    '$id':  id
                };
            }

            function GenghisRegExp(pattern, flags) {
                return {
                    '$type': 'RegExp',
                    '$value': {
                        '$pattern': pattern ? pattern.toString() : null,
                        '$flags': flags ? flags.toString() : null
                    }
                };
            }

            function BinData(subtype, base64str) {
                return {
                    '$type': 'BinData',
                    '$value': {
                        '$subtype': subtype,
                        '$binary': base64str
                    }
                };
            }

            // Replace Date and RegExp calls with a fake constructors
            src = src.replace(/^\s*(new\s+)?(Date|RegExp)(\b)/, '$1Genghis$2$3');

            // TODO: not this :)
            return JSON.stringify(eval(src));
        }

        function replaceRegExpLiteral(value) {
            var flags = '';

            if (value.global) {
                flags = flags + 'g';
            }
            if (value.multiline) {
                flags = flags + 'm';
            }
            if (value.ignoreCase) {
                flags = flags + 'i';
            }

            return replaceCallExpression('GenghisRegExp(' + JSON.stringify(value.source) + ', "' + flags + '")');
        }

        var chunks = src.split('');

        function insertHelpers(node) {
            if (!node.range) return;

            node.source = function () {
                return chunks.slice(node.range[0], node.range[1]).join('');
            };

            if (node.update && _.isObject(node.update)) {
                var prev = node.update;
                Object.keys(prev).forEach(function (key) {
                    update[key] = prev[key];
                });
                node.update = update;
            } else {
                node.update = update;
            }

            function update (s) {
                chunks[node.range[0]] = s;
                for (var i = node.range[0] + 1; i < node.range[1]; i++) {
                    chunks[i] = '';
                }
            }
        }

        var ast;
        try {
            ast = esprima.parse(src, opts);
        } catch (e) {
            throwErrors([e]);
        }

        if (ast.errors.length) {
            throwErrors(ast.errors);
        }

        function assertType(type, node) {
            if (node.type !== type) {
                addError('Expecting ' + type + ' but found ' + node.type, node);
            }
        }

        // remove a couple of things first :)
        var node;

        node = ast;
        assertType('Program', node);

        node = node.body;
        if (node.length !== 1) {
            addError('Unexpected statement ' + node[1].type, node[1]);
        }

        node = node[0];
        assertType('VariableDeclaration', node);

        node = node.declarations;
        if (node.length !== 1) {
            addError('Unexpected variable declarations ' + node.length, node[1]);
        }

        node = node[0];
        assertType('VariableDeclarator', node);

        node = node.init;
        if (node.type !== 'ObjectExpression') {
            addError('Expected an object expression, found ' + node.type, node);
        }

        if (errors.length) {
            throwErrors(errors);
        }

        (function walk(node) {
            insertHelpers(node);
            Object.keys(node).forEach(function (key) {
                var child = node[key];
                if (Array.isArray(child)) {
                    var result = [];
                    child.forEach(function (c) {
                        if (c && typeof c.type === 'string') {
                            walk(c);
                        }
                    });
                } else if (child && typeof child.type === 'string') {
                    insertHelpers(node);
                    walk(child);
                }
            });

            switch (node.type) {
                // Explicitly whitelist call expressions
                case 'NewExpression':
                case 'CallExpression':
                    if (node.callee && !allowedCalls[node.callee.name]) {
                        addError('Bad call, bro: '+node.callee.name, node);
                    } else {
                        node.update(replaceCallExpression(node.source()));
                    }
                    break;

                // Property value has to be an array, object, literal, or a whitelisted call
                case 'Property':
                    if (node.value && !allowedPropertyValues[node.value.type]) {
                        addError('Unexpected value: ' + node.value.source(), node.value);
                    }
                    break;

                // We like these :)
                case 'Identifier':
                case 'ArrayExpression':
                case 'ObjectExpression':
                case 'UnaryExpression':
                    break;

               // Normally literals get a pass
                case 'Literal':
                    // ... but a literal RegExp should be thunked into a "new" expression
                    if (_.isRegExp(node.value)) {
                        node.update(replaceRegExpLiteral(node.value));
                    }
                    break;

                // Deny by default
                default:
                    addError('Unexpected '+node.type, node);
                    break;
            }
        })(node);

        if (errors.length) {
            throwErrors(errors);
        }

        return (function(node) {
            var __genghis_json__;
            eval('__genghis_json__ = ' + node.source());
            return __genghis_json__;
        })(node);
    },

    stringify: function(value, pretty) {
        return jQuery('<div>' + this.prettyPrint(value, pretty, false) + '</div>').text();
    },

    prettyPrint: function (value, pretty, autoCollapse) {
        
    },

    normalize: function(value, pretty) {
        return Genghis.JSON.stringify(Genghis.JSON.parse(value), pretty);
    }
};
