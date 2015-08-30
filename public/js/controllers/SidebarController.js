app = angular.module('MongoApp');

app.controller('SidebarController', function ($scope, $timeout, localStorageService) {
    var activesKey = "sidebar_actives";
    var sidebar = {};

    sidebar._isActive = function (db) {
        return _.findWhere(sidebar._getActives(), {db: db.name});
    };

    sidebar._setActive = function (db) {
        var actives = sidebar._getActives();
        if (!(_.findWhere(actives, {db: db.name}))) {
            actives.push({db: db.name});
            sidebar._saveActives(actives);
        }
    };

    sidebar._removeActive = function (db) {
        sidebar._saveActives(_.reject(sidebar._getActives(), {db: db.name}));
    };

    sidebar._toggleActive = function (db) {
        if (sidebar._isActive(db)) {
            sidebar._removeActive(db);
            return;
        }
        sidebar._setActive(db);
    };

    sidebar._getActives = function () {
        if (!localStorageService.isSupported) return [];
        var actives = localStorageService.get(activesKey);
        if (_.isArray(actives)) {
            return actives;
        }
        return [];
    };

    sidebar._saveActives = function (actives) {
        if (!localStorageService.isSupported) return;
        if (_.isArray(actives)) {
            localStorageService.set(activesKey, actives);
            return;
        }
        localStorageService.set(activesKey, []);
    };

    $scope.toggle = function (db) {
        return sidebar._toggleActive(db);
    };

    $scope.getClass = function (db) {
        return sidebar._isActive(db) ? 'active' : 'collapse';
    };
});