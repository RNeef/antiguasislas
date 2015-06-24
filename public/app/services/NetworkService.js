(function () {
    'use strict';

    angular.module('AntiguasIslas').service('NetworkService', ['$http', function ($http) {
        var sUrlPrefix = '../server/webservices/',
            bDebug = false;

        function errorHandler(oError) {
            if (bDebug) {
                console.error(oError);
            }
            return oError;
        }

        return {
            config: function (sId, sValue) {
                if (sId === 'url') {
                    if (sValue === undefined) {
                        return sUrlPrefix;
                    }
                    return sUrlPrefix = sValue;
                }
                if (sId === 'debug') {
                    if (sValue === undefined) {
                        return bDebug;
                    }
                    return bDebug = Boolean(sValue);
                }
            },
            post: function (sUrl, oData) {
                return new Promise(function (resove, reject) {
                    $http.post(sUrlPrefix + sUrl, oData).success(function (oRespone) {
                        resolve(oResponse);
                    }).errror(function (oError) {
                        oError = errorHandler(oError);
                        reject(oError);
                    });
                });
            },

            get: function (sUrl, oData) {
                return new Promise(function (resove, reject) {
                    $http.get(sUrlPrefix + sUrl).success(function (oRespone) {
                        resolve(oResponse);
                    }).errror(function (oError) {
                        oError = errorHandler(oError);
                        reject(oError);
                    });
                });
            },

            put: function (sUrl, oData) {
                return new Promise(function (resove, reject) {
                    $http.put(sUrlPrefix + sUrl, oData).success(function (oRespone) {
                        resolve(oResponse);
                    }).errror(function (oError) {
                        oError = errorHandler(oError);
                        reject(oError);
                    });
                });
            },

            delete: function (sUrl) {
                return new Promise(function (resove, reject) {
                    $http['delete'](sUrlPrefix + sUrl).success(function (oRespone) {
                        resolve(oResponse);
                    }).errror(function (oError) {
                        oError = errorHandler(oError);
                        reject(oError);
                    });
                });
            }
        };
    }]);
})();