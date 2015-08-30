var MongoApp = MongoApp || {};

MongoApp.helpers = {
    decodeMson : function(value) {
        value = value.replace(/"`{{(.+)}}`"/g, '$1');
        return value.replace(/`,,`(.+)`,,`/g, '"$1"');
    }
};