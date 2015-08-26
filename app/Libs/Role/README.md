### RolePermission

User Model

```
<?php

use App\Libs\Role\Traits\UserTrait;

?>
```

Permission
```
<?php

use App\Libs\Role\PermissionMongo;

?>
```

Role
```
<?php

use App\Libs\Role\RoleMongo;

?>
```


Config
------

```
// config/auth.php
'model' => App\Models\User::class,
'role'  => App\Models\Role::class,
```
