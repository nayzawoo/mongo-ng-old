var app = angular.module('MongoApp');

/**
 * @item Item is passed form resolved
 */
app.controller('DocumentEditorController', function(
    $scope, $rootScope, $stateParams, api, $state, $modalInstance, item) {
    $scope.temp = null;

    setTimeout(function() {
        $('.CodeMirror').each(function(i, el) {
            el.CodeMirror.refresh();
        });
    }, 5);

    $scope.editorOptions = {
        lineWrapping: true,
        lineNumbers: true,
        keyMap: "sublime",
        tabSize: 4,
        mode: "application/ld+json",
        // readOnly: 'nocursor',
        autofocus: true,
        scrollbarStyle: "simple",
        theme: "material",
        foldGutter: true,
        lint: true,
        extraKeys: {
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption(
                    "fullScreen"));
                $('.modal-dialog').toggleClass('fullscreen');
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption(
                    "fullScreen", false);
                $('.modal-dialog').removeClass('fullscreen');
            }
        },
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter",
            "CodeMirror-lint-markers"
        ]
    };

    $scope.item = item;

    $scope.ok = function() {
        $modalInstance.close();
    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
});
