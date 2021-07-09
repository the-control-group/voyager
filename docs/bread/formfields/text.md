# Text

## Link

```json
{
  "link": {
    "{permission}": "{view}",
    "{permission2}": "{view2}"
  }
}
```

A text field can be made to link to other BREAD views (such as `edit`) from the browse view by setting `link`. `link` is an object that maps permissions (on the left) to views (on the right). If a user does not have the first permission, the second will be tested, and so on, until a permission is satisfied. If no permission is satisfied, no link will be outputted.

A common use would be to link a title to the `edit` view, assuming the user has access to it, otherwise fallback to linking it to the `show` view:

```json
{
  "link": {
    "edit": "edit",
    "read": "show"
  }
}
```
