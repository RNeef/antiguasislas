(function () {
	'use strict';

	angular.module('AntiguasIslas').service('UserService', ['NetworkService', function (NetworkService) {
		return {
			register: function (oUserData) {
				return new Promise(function (resolve, reject) {
					var sId,
						bValidData = true;
						oData = {
			                name: null,
			                password: null,
			                mail: null
						};

					if (!oUserData) {
						reject();
					}

					for (sId in oData) {
						if (oUserData.hasOwnProperty(sId)) {
							oData[sId] = oUserData[sId];
						} else {
							bValidData = false;
						}
					}

					if(!bValidData) {
						reject();
					}

					oData.action = 'register';

					NetworkService.post('registrierung.php', oData).then(resolve).caught(reject);		
				})
			},

			login: function (oUserData) {
				return new Promise(function (resolve, reject) {
					var sId,
						bValidData = true;
						oData = {
			                name: null,
			                password: null
						};

					if (!oUserData) {
						reject();
					}

					for (sId in oData) {
						if (oUserData.hasOwnProperty(sId)) {
							oData[sId] = oUserData[sId];
						} else {
							bValidData = false;
						}
					}

					if(!bValidData) {
						reject();
					}

					oData.action = 'login';

					NetworkService.post('registrierung', oData).then(resolve).caught(reject);		
				})
			}

		}
	}])
})();