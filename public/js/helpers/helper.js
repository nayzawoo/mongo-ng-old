var MongoApp = MongoApp || {};

MongoApp.helpers = {
    decodeMson : function(value) {
        value = value.replace(/"`{{(.+)}}`"/g, '$1');
        return value.replace(/`,,`(.+)`,,`/g, '"$1"');
    },

    errorAlert : function(error) {
        if (_.isObject(error) && error.error) {
            var message = error.message || 'Error';
            swal("Oops...", message, "error");
            return;
        }
        return swal("Oops...", 'Error', "error");
    }
};

MongoApp.errorAlert = MongoApp.helpers.errorAlert;