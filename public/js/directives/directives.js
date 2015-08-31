app = angular.module('MongoApp');

app.directive('nagPrism', ['$compile', function ($compile) {
    return {
        restrict: 'A',
        transclude: true,
        scope: {
            source: '@'
        },
        link: function (scope, element, attrs, controller, transclude) {
            scope.$watch('source', function (v) {
                element.find("code").html(v);

                Prism.highlightElement(element.find("code")[0]);
            });

            transclude(function (clone) {
                if (clone.html() !== undefined) {
                    element.find("code").html(clone.html());
                    $compile(element.contents())(scope.$parent);
                }
            });
        },
        template: "<code></code>"
    };
}]);

app.directive('editorResizer', function ($window, $document) {
    return {
        restrict: 'A',
        link: function postLink(scope, element, attrs) {
            var editor = angular.element('.editor-wrapper').find('.CodeMirror');
            var originalHeight = $(editor).height();
            var isEditor = false;
            element.on('mousedown', function (e) {

                e.preventDefault();
                startY = e.clientY;
                startHeight = parseInt(editor.height(), 10);
                $document.on('mousemove', mousemove);
                $document.on('mouseup', mouseup);
            });

            function mousemove(e) {
                window.editor = editor;
                var newHeight = startHeight + e.clientY - startY;
                editor.height(newHeight + 'px');
                isEditor = (originalHeight <= newHeight);
            }

            function mouseup() {
                $document.unbind('mousemove', mousemove);
                $document.unbind('mouseup', mouseup);
                if (isEditor) {
                    scope.$apply(function () {
                        scope.editorAsForm = isEditor;
                        scope.editor.setOption("theme", 'material');
                    });
                    editor.addClass('json-editor');
                } else {
                    editor.removeClass('json-editor');
                    scope.$apply(function () {
                        scope.editorAsForm = isEditor;
                        scope.editor.setOption("theme", 'default');
                    });
                }
            }
        }
    };
});