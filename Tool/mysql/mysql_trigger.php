<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <base href="/demo/" />
        <title>Angular boostrap ui</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimal-ui" />
        <meta name="apple-mobile-web-app-status-bar-style" content="yes" />
        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />
        <link rel="stylesheet" href="//apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css">  
        <script src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>  
        <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>   
        <script src="http://cdn.bootcss.com/angular-ui-bootstrap/1.1.2/ui-bootstrap-tpls.js"></script> 
    </head>

    <body>

        <div  class="container" >

            <div class="row" ng-app="myApp" ng-controller="AccordionDemoCtrl">
                <table class="table">
                    <caption>goods表</caption>
                    <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>剩余数量</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr ng-repeat="k in good">

                            <td>{{k.name}}</td>
                            <td>{{k.num}}</td>
                        </tr>



                    </tbody>
                </table>
                <table class="table">
                    <caption>orders表</caption>
                    <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>购买数量</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr ng-repeat="o in order">
                            <td>{{o.name}}</td>
                            <td>{{o.much}}</td>
                            <td  ng-click="deleteorder(o.oid)">删除</td>
                        </tr>

                    </tbody>
                </table>   


                <div>你要购买{{gouname}} {{goumai_num}}</div>
                <div class="form-group">
                    <div class="row col-xs-12">
                        <div class="col-mb-2 pull-left">
                            <select class="form-control"  ng-model="format" ng-options="o.gid as o.name for o in good">
                                <option value="">-- 请选择 --</option>
                            </select>
                        </div>
                        <div class="col-mb-2 pull-left">
                            <input type="text" class="form-control" ng-model="goumai_num">
                        </div>

                        <div class="col-mb-2 pull-left">
                            <button  ng-click="myngclick()" type="submit" class="btn btn-default">Submit</button>
                        </div>

                    </div>


                </div>

            </div>


        </div>



        <script>
            var app = angular.module('myApp', ['ui.bootstrap'],
                    //angular 构造器改造 实现post提交
                            function ($httpProvider) {
                                $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded;charset=utf-8";
                                $httpProvider.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
                                var param = function (obj) {
                                    var query = "", name, value, fullSubName, subName, subValue, innerObj, i;
                                    for (name in obj) {
                                        value = obj[name];
                                        if (value instanceof Array) {
                                            for (i = 0; i < value.length; ++i) {
                                                subValue = value[i];
                                                fullSubName = name + "[" + i + "]";
                                                innerObj = {};
                                                innerObj[fullSubName] = subValue;
                                                query += param(innerObj) + "&";
                                            }
                                        } else if (value instanceof Object) {
                                            for (subName in value) {
                                                subValue = value[subName];
                                                fullSubName = name + "[" + subName + "]";
                                                innerObj = {};
                                                innerObj[fullSubName] = subValue;
                                                query += param(innerObj) + "&";
                                            }
                                        } else if (value !== undefined && value !== null) {
                                            query += encodeURIComponent(name) + "=" + encodeURIComponent(value) + "&";
                                        }
                                    }
                                    return query.length ? query.substr(0, query.length - 1) : query;
                                };
                                $httpProvider.defaults.transformRequest = [function (data) {
                                        return angular.isObject(data) && String(data) !== "[object File]" ? param(data) : data;
                                    }];
                            });


                    app.controller('AccordionDemoCtrl', function ($scope, $http) {

                        //快速get
                        $scope.httpget = function (url, obj) {
                            $http.get(url).success(function (response) {
                                if (obj == 'good') {
                                    $scope.good = response.records;
                                }
                                if (obj == 'order') {
                                    $scope.order = response.records;
                                }
                                if(obj==null){
                                      $scope.httpget('/mysql/get_data.php?getorder=1', 'order');
                                      $scope.httpget('/mysql/get_data.php?getgood=1', 'good');
                                }
                            }
                            );
                        }

                        $scope.httpget('/mysql/get_data.php?getgood=1', 'good');
                        $scope.httpget('/mysql/get_data.php?getorder=1', 'order');
                        $scope.gouname = '什么？';
                        $scope.gouname_value = '';





                        //$watch 脏查询可以代替ng-change
                        $scope.$watch('format', function (newValue, oldValue) {
                            //這裡輸入觸發$watch之後，欲觸發的行為  
                            if (newValue) {
                                $scope.gouname_value = newValue;
                                for (var i = 0; i < $scope.good.length; i++) {
                                    if ($scope.good[i].gid == newValue) {
                                        $scope.gouname = $scope.good[i].name;
                                    }
                                }
                            }
                        }, true);

                        //防止重复提交
                        $scope.repsub = function () {
                            $("button").attr("disabled", "disabled");
                            setTimeout(function () {
                                $("button").removeAttr("disabled");
                            }, 2000);
                        }


                        //重置  
                        $scope.master = "";
                        $scope.reset = function () {
                            $scope.goumai_num = angular.copy($scope.master);
                        };
                        $scope.reset();

                        //点击事件
                        $scope.myngclick = function () {
                            //  console.log( $scope.goumai_num);//购买数量
                            //  console.log($scope.gouname_value);//购买的东西ID
                            $scope.repsub(); //防止重复提交
                            $scope.postForm('/mysql/get_data.php');
                        }

                        //post提交的数据

                        $scope.postForm = function (url) {
                            //获取$scope.goumai_num 等信息需要在函数里面，因为防在外部不能做脏检查
                            $scope.formData = {
                                'gomainum': $scope.goumai_num,
                                'goumaivalue': $scope.gouname_value
                            };
                            $http.post(url, $scope.formData).success(function (data) {
                                 $scope.httpget('/mysql/get_data.php?getgood=1', 'good');
                                 $scope.httpget('/mysql/get_data.php?getorder=1', 'order');
                                
                            }).error(function (data) {

                                console.log("失败");
                            });
                        }

                       //删除数据
                       $scope.deleteorder = function (id){
                           $scope.httpget('/mysql/get_data.php?delete='+id);
                         
                       }



                    });


        </script>  
    </body>
</html>


