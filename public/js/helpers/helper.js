var MongoApp = MongoApp || {};

MongoApp.helpers = {
    decodeMson: function (value) {
        value = value.replace(/"`{{(.+)}}`"/g, '$1');
        return value.replace(/`,,`(.+)`,,`/g, '"$1"');
    },

    errorAlert: function (error) {
        if (_.isObject(error) && error.error) {
            var message = error.message || 'Error';
            swal("Oops...", message, "error");
            return;
        }
        return swal("Oops...", 'Error', "error");
    },

    isJsonString: function (str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    },

    isJsonObject: function(str) {
        if (!MongoApp.helpers.isJsonString(str)) {
            return false;
        }
        var json = JSON.parse(str);
        if (! _.isObject(json)) {
            return false;
        }
        return true;
    }
};

MongoApp.errorAlert = MongoApp.helpers.errorAlert;