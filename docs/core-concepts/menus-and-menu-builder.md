# Menus and Menu Builder

With Voyager you can easily create menus for your application. In fact the Voyager admin is using the menu builder for the navigation you use on the left hand side.

You can view your current Menus by clicking on the _Tools-&gt;Menu Builder_ button. You can add, edit, or delete any current menu. This means that you can create a new menu for the header, sidebar, or footer of your site. Create as many menus as you would like.

When you are ready to add menu items to your menu you can click on the builder button of the corresponding menu:

![](../.gitbook/assets/menu_1.jpg)

This will take you to the Menu Builder where you can add, edit, and delete menu items.

![](../.gitbook/assets/menu_2.jpg)

After creating and configuring your menu, you can easily implement that menu in your application. Say that we have a menu called `main`. Inside of any view file we could now output the menu by using the following code:

```php
menu('main');
```

This will output your menu in an unstyled unordered list. If you do use bootstrap to stylize your web app you can pass a second argument to the menu display method telling it that you want to stylize the menu with bootstrap styles like so:

```php
menu('main', 'bootstrap');
```

Taking it one more step further you can even specify your own view and stylize your menu however you would like. Say for instance that we have a menu named `frontpage_menu`. In any of your blade files, you can do:
```markup
@php
$items =  menu('frontpage_menu','_json');

@endphp

<ul>
    @foreach($items as $menu_item)
        <li class="mycustomclass"><a href="{{ $menu_item->link()}}" class="anothercustomclass">{{ $menu_item->title }}</a></li>
    @endforeach
</ul>

```

Then anywhere you wanted to display your menu you can now call:

```php
menu('main', 'my_menu');
```

And your custom menu will now be output.

## Menu as JSON

If you dont want to render your menu but get an array instead, you can pass `_json` as the second parameter. For example:

```php
menu('main', '_json')
```

This will give you a collection of menu-items.

