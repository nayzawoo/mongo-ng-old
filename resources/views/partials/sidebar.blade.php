<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
{{--             <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <!-- /input-group -->
            </li>
 --}}            
                <li ng-repeat="db in dbs | orderBy: 'name'">
                <a href="#" ><span class="fa arrow"></span> <i class="fa fa-database fa-fw"></i> @{{db.name}} <span class="badge pull-right">@{{db.collection.length}}</span></a>
                <ul class="nav nav-second-level ">
                    <li ng-repeat="collection in db.collection">
                        <a ui-sref="collections({db_name: db.name,col_name:collection,page:1})"><i class="fa fa-table fa-fw"></i> @{{collection}}</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
            <!-- /.navbar-static-side -->