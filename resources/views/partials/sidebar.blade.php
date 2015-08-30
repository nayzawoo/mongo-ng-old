<div class="navbar-default sidebar" role="navigation">

    <div class="sidebar-nav navbar-collapse" ng-controller="SidebarController">
        <ul class="nav" id="side-menu" ng-class="{'animate': sideBarAnimation}">
            <li
                ng-repeat="db in dbs | orderBy: 'name'"
                ng-init="class=getClass(db)"
                class="sidebar-menu @{{::class}}"
                    >
                <a href="#" ng-click="toggle($event,db)"><span class="fa arrow"></span> <i class="fa fa-database fa-fw"></i> @{{db.name}} <span
                            class="badge pull-right">@{{::db.collection.length}}</span></a>
                <ul class="nav nav-second-level ">
                    <li ng-repeat="collection in db.collection ">
                        <a ng-click="browseCollection(db,collection)" href="javascript:;"><i class="fa fa-table fa-fw"></i> <span ng-bind="collection"></span></a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->