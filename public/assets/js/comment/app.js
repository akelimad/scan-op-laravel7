var commentApp = angular.module('commentApp', ['mainCtrl', 'commentService'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
}); 