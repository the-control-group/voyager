# Action buttons

## Action Buttons

Action buttons are displayed when browsing a BREAD next to each row

![](../.gitbook/assets/action_buttons.jpg)

You can add your own buttons very easily. First we will create an Action-class which extends Voyagers AbstractAction in app/Actions/MyAction.php

```php
<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class MyAction extends AbstractAction
{
    public function getTitle()
    {
        return 'My Action';
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('my.route');
    }
}
```

Next we need to tell Voyager that we want to use this action. For this open your `app/Providers/AppServiceProvider.php` and search for the `boot()` method

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Voyager::addAction(\App\Actions\MyAction::class);
    }
}
```

After that you will see your new button when browsing any BREAD ![](../.gitbook/assets/action_buttons_custom.jpg)

### Showing/hiding actions

If you only want to show your action for special datatypes you can implement a function `shouldActionDisplayOnDataType()` in your action:

```php
<?php

public function shouldActionDisplayOnDataType()
{
    return $this->dataType->slug == 'posts';
}
```

If you want to show your action-button on a per-row-base, simply implement a method `shouldActionDisplayOnRow($row)` and add your condition(s)

```php
<?php

public function shouldActionDisplayOnRow($row)
{
    return $row->id > 10;
}
```

## Mass Actions

Mass actions are called for multiple instances of a model.  
If you want your action to be a mass action, just implement the following method:

```php
<?php

public function massAction($ids, $comingFrom)
{
    // Do something with the IDs
    return redirect($comingFrom);
}
```

