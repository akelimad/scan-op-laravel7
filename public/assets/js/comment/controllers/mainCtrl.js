angular.module('mainCtrl', [])

        // inject the Comment service into our controller
        .controller('mainController', function ($scope, $http, $window, Comment) {
            // object to hold all the data for the new comment form
            $scope.commentData = {};

            $scope.commentData.user_id = $('#user_id').val();
            $scope.commentData.post_id = $('#post_id').val();
            $scope.commentData.post_type = $('#post_type').val();

            // loading variable to show the spinning loading icon
            $scope.loading = true;

            // get all the comments first and bind it to the $scope.comments object
            // use the function we created in our service
            // GET ALL COMMENTS ==============
            Comment.get($scope.commentData)
                    .success(function (data) {
                        $scope.comments = data;
                        $scope.loading = false;
                        $scope.uploads_path = $window.uploads_path;
                    });

            // function to handle submitting the form
            // SAVE A COMMENT ================
            $scope.submitComment = function () {
                $scope.loading = true;
                $scope.commentData.parent_comment = $('#parent_comment').val();

                // save the comment. pass in comment data from the form
                // use the function we created in our service
                Comment.save($scope.commentData)
                        .success(function (data) {

                            // if successful, we'll need to refresh the comment list
                            Comment.get($scope.commentData)
                                    .success(function (getData) {
                                        $scope.comments = getData;
                                        $scope.loading = false;

                                        $('#comment').val('');
                                        formComment = $('form.comment-form');
                                        $('#comments').append(formComment);
                                    });
                        })
                        .error(function (data) {
                            console.log(data);
                        });
            };

            // function to handle deleting a comment
            // DELETE A COMMENT ====================================================
            $scope.deleteComment = function (id) {
                $scope.loading = true;

                // use the function we created in our service
                Comment.destroy(id)
                        .success(function (data) {

                            // if successful, we'll need to refresh the comment list
                            Comment.get($scope.commentData)
                                    .success(function (getData) {
                                        $scope.comments = getData;
                                        $scope.loading = false;
                                    });

                        });
            };
        });
