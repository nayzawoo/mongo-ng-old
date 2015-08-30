app = angular.module('MongoApp');

app.filter('readableSize', function() {
    return function readableSize(fileSizeInBytes) {
        if (fileSizeInBytes === undefined) {
            return;
        }
        return filesize(fileSizeInBytes);
    };
});
