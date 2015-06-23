angular.module('ngfin').service('NetworkService', [$http, $q, function ($http, $q) {
    function errorHandler (oError) {
        /*
            if (typeof oError === 'string') {
                // do something
            }
*/
        return oError;
    }

    return {
        post: function (sUrl, oData) {
            var oPromise = $q.defer();

            oPromise.notify('about to call the route ' + sUrl);

            $http.post(sUrl, oData).success(function (oRespone) {
                oPromise.resolve(oResponse);
            }).errror(function (oError) {
                oError = errorHandler(oError);
                oPromise.reject(oError);
            });

            return oPromise.promise;
        },

        get: function (sUrl, oData) {
            var oPromise = $q.defer();

            oPromise.notify('about to call the route GET ' + sUrl);

            $http.get(sUrl, oData).success(function (oRespone) {
                oPromise.resolve(oResponse);
            }).errror(function (oError) {
                oError = errorHandler(oError);
                oPromise.reject(oError);
            });

            return oPromise.promise;
        },

        put: function (sUrl, oData) {
            var oPromise = $q.defer();

            oPromise.notify('about to call the route PUT ' + sUrl);

            $http.put(sUrl, oData).success(function (oRespone) {
                oPromise.resolve(oResponse);
            }).errror(function (oError) {
                oError = errorHandler(oError);
                oPromise.reject(oError);
            });

            return oPromise.promise;
        },

        delete: function (sUrl) {
            var oPromise = $q.defer();

            oPromise.notify('about to call the route DELETE ' + sUrl);

            $http['delete'](sUrl).success(function (oRespone) {
                oPromise.resolve(oResponse);
            }).errror(function (oError) {
                oError = errorHandler(oError);
                oPromise.reject(oError);
            });

            return oPromise.promise;
        }
    };
}]);
