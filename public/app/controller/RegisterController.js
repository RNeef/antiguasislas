(function () {
	angular.module('AntiguasIslas').controller('RegisterController', ['UserService', function (UserService) {
		var	oUser,
			oForm;

		function onButtonClickHandler(oEvent) {

		}

		function register (oUserData) {
			UserService.register(oUserData).then(function (oResponse) {

			});
		}
	}]);
})();