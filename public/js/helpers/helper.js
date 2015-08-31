var MongoApp = MongoApp || {};

MongoApp.helpers = {
    decodeMson : function(value) {
        value = value.replace(/"`{{(.+)}}`"/g, '$1');
        return value.replace(/`,,`(.+)`,,`/g, '"$1"');
    },

    errorAlert : function(error) {
        if (error) {
            console.log(error);
        }
        swal("Oops...", 'Unable to load data', "error");
    }
};