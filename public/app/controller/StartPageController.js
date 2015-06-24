(function () {
	'use strict';

	angular.module('AntiguasIslas').controller('StartPageController', function ($scope, ngDialog) {
		$scope.action = function (sId) {
			if (sId === 'register')	{
				ngDialog.open({
					templateUrl: 'app/views/register.html',
					controller: 'RegisterController',
					showClose: true
				});
			}
		};
	});
})();