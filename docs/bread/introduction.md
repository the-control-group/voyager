# Introduction

When adding or editing the current BREAD for a database table you will first see the BREAD info which allows you to set the Display Names, slug, icon, `Model` and Controller Namespace, Policy Name. You can also choose if you would like to Generate permissions for that BREAD type.

![](../.gitbook/assets/bread_details.png)

When you scroll down you will see each of the rows that are associated with that table where you can select where in your views you want to see each of those fields:

* BROWSE \(field will show up when you browse the current data\)
* READ \(field will show when you click to view the current data\)
* EDIT \(field will be visible and allow you to edit the data\)
* ADD \(field will be visible when you choose to create a new data type\)
* DELETE \(doesn't pertain to delete so this can be checked or unchecked\)

![](../.gitbook/assets/bread_fields.png)

You may also choose to specify what form type you want to use for each field. This can be a TextBox, TextArea, Checkbox, Image, and many other types of form elements.

Each field also has additional details or options that can be included. These types are checkbox, dropdown, radio button, and image.

## Validation

Inside of the _Optional Details_ section for each row in your BREAD you can also specify validation rules with some simple JSON. Here is an example of how to add a validation rule for _required_ and _max length of 12_

```php
{
    "validation": {
        "rule": "required|max:12"
    }
}
```

Additionally, you may wish to add some custom error messages which can be accomplished like so:

```php
{
    "validation": {
        "rule": "required|max:12",
        "messages": {
            "required": "This :attribute field is a must.",
            "max": "This :attribute field maximum :max."
        }
    }
}
```

You can also define multiple rules the following way:

```php
{
    "validation": {
        "rule": [
            "required",
            "max:12"
        ]
    }
}
```

### Action specific rules

You can define separate validation rules for edit and add:

```text
{
    "validation": {
        "rule": "min:3",
        "edit": {
            "rule": "nullable"
        },
        "add": {
            "rule": "required"
        }
    }
}
```

You can find a list of all available validation rules in the [Laravel docs](https://laravel.com/docs/validation#available-validation-rules).

## Tagging

Tagging gives you the possibility to add new items to a Belongs-To-Many relationship directly when editing or adding a BREAD.

To activate this function, you simply have to enable `Tagging` in the relationship details

![](../.gitbook/assets/tagging.jpg)

After that you can enter a free-text into the select and hit enter to save a new relationship.

{% hint style="info" %}
**Be aware:**

This only stores the `display-column` so you have to make sure that all other fields are either nullable or have a default value.
{% endhint %}

## Ordering Bread Items

You can define the default order for browsing BREADs and order your BREAD items with drag-and-drop.  
For this you need to change the settings for your BREAD first:

![](../.gitbook/assets/bread_settings_order.png)

**Order column** is the field in your table where the order is stored as an integer.  
**Order display column** is the field which is shown in the drag-drop list.  
**Order direction** the direction in which the field is ordered.

After this you can go to your BREAD-browse page and you will see a button **Order.**  
Clicking this button will bring you to the page where you can re-arrange your items:

![](../.gitbook/assets/bread_order.png)

## Scope browse-results

If you want to filter the browse results for a BREAD you can do so by creating a [Scope](https://laravel.com/docs/eloquent#local-scopes) in your model. For example if you want to only show posts that were created by the current user, define a Scope like the following:

```php
<?php
public function scopeCurrentUser($query)
{
    return $query->where('author_id', Auth::user()->id);
}
```

Next, go to the BREAD-settings for `posts` and look for the `Scope` input and select `currentUser`:

![](../.gitbook/assets/bread_scope.jpg)

After hitting `Submit` you will only see your own posts.

Voyager BREAD editor permits to select several scopes.
