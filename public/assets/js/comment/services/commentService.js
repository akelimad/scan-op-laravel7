angular.module('commentService', [])

        .factory('Comment', function ($http, $window) {

            return {
                // get all the comments
                get: function (commentData) {
                    return $http.get($window.root_path + '/api/comments/' + commentData.post_type + '/' + commentData.post_id);
                },
                // save a comment (pass in comment data)
                save: function (commentData) {
                    return $http({
                        method: 'POST',
                        url: $window.root_path + '/api/comments',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param(commentData)
                    });
                },
                // destroy a comment
                destroy: function (id) {
                    return $http.delete($window.root_path + 'api/comments/' + id);
                }
            }
        });
