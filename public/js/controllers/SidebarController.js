app = angular.module('MongoApp');

app.controller('SidebarController', function ($scope, $timeout, localStorageService, $state) {
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
        if ( _.isArray(sidebar.actives)) {
            return sidebar.actives;
        }
        if (!localStorageService.isSupported) return [];
        var actives = localStorageService.get(activesKey);
        if (_.isArray(actives)) {
            return sidebar.actives = actives;
        }
        return sidebar.actives = [];
    };

    sidebar._saveActives = function (actives) {
        sidebar.actives = undefined;
        if (!localStorageService.isSupported) return;
        if (_.isArray(actives)) {
            localStorageService.set(activesKey, actives);
            return;
        }
        localStorageService.set(activesKey, []);
    };

    $scope.toggle = function ($event, db) {
        if ($event && $event.currentTarget) {
            var parent = $($event.currentTarget).closest('li');
            var ul = $($event.currentTarget).next('ul');
            if (sidebar._isActive(db)) {
                parent.removeClass('active');
                ul.stop().slideUp();
            } else {
                parent.addClass('active');
                ul.stop().slideDown();
            }
        }
        return sidebar._toggleActive(db);
    };

    $scope.getClass = function (db) {
        return sidebar._isActive(db) ? 'active' : '';
    };

    $scope.browseCollection = function(db, collection) {
        $state.go('collections', {db_name: db.name,col_name:collection,page:1});
    };
});