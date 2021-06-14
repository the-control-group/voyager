# Multilanguage

Voyager supports multiple languages for your models.To get started, you need to configure some things first.

## Setup

First you need to define some `locales` in your `config/voyager.php` file and `enable` multilanguage:

```php
'multilingual' => [
        'enabled' => true,
        'default' => 'en',
        'locales' => [
            'en',
            'da',
        ],
    ],
```

After that you need to include the `Translatable` Trait in your model and define the translatable attributes:

```php
use TCG\Voyager\Traits\Translatable;
class Post extends Model
{
    use Translatable;
    protected $translatable = ['title', 'body'];
}
```

After following the previous indications, if you don't see the language-selection on your BREAD pages, you need to run the Laravel Artisan command for removing the configuration cache file:

```bash
php artisan config:clear
```

Now you will see a language-selection in your BREAD pages.

## Usage

### Eager-load translations

```php
// Loads all translations
$posts = Post::with('translations')->get();

// Loads all translations
$posts = Post::all();
$posts->load('translations');

// Loads all translations
$posts = Post::withTranslations()->get();

// Loads specific locales translations
$posts = Post::withTranslations(['en', 'da'])->get();

// Loads specific locale translations
$posts = Post::withTranslation('da')->get();

// Loads current locale translations
$posts = Post::withTranslation('da')->get();
```

### Get default language value

```php
echo $post->title;
```

### Get translated value

```php
echo $post->getTranslatedAttribute('title', 'locale', 'fallbackLocale');
```

If you do not define locale, the current application locale will be used. You can pass in your own locale as a string. If you do not define fallbackLocale, the current application fallback locale will be used. You can pass your own locale as a string. If you want to turn the fallback locale off, pass false. If no values are found for the model for a specific attribute, either for the locale or the fallback, it will set that attribute to null.

### Translate the whole model

```php
$post = $post->translate('locale', 'fallbackLocale');
echo $post->title;
echo $post->body;

// You can also run the `translate` method on the Eloquent collection
// to translate all models in the collection.
$posts = $posts->translate('locale', 'fallbackLocale');
echo $posts[0]->title;
```

If you do not define locale, the current application locale will be used. You can pass in your own locale as a string. If you do not define fallbackLocale, the current application fallback locale will be used. You can pass in your own locale as a string. If you want to turn the fallback locale off, pass false. If no values are found for the model for a specific attribute, either for the locale or the fallback, it will set that attribute to null.

### Check if model is translatable

```php
// with string
if (Voyager::translatable(Post::class)) {
    // it's translatable
}

// with object of Model or Collection
if (Voyager::translatable($post)) {
    // it's translatable
}
```

### Set attribute translations

```php
$post = $post->translate('da');
$post->title = 'foobar';
$post->save();
```

This will update or create the translation for title of the post with the locale da. Please note that if a modified attribute is not translatable, then it will make the changes directly to the model itself. Meaning that it will overwrite the attribute in the language set as default.

### Query translatable Models

To search for a translated value, you can use the `whereTranslation` method.  
For example, to search for the slug of a post, you'd use

```php
$page = Page::whereTranslation('slug', 'my-translated-slug');
// Is the same as
$page = Page::whereTranslation('slug', '=', 'my-translated-slug');
// Search only locale en, de and the default locale
$page = Page::whereTranslation('slug', '=', 'my-translated-slug', ['en', 'de']);
// Search only locale en and de
$page = Page::whereTranslation('slug', '=', 'my-translated-slug', ['en', 'de'], false);
```

`whereTranslation` accepts the following parameter:

* `field` the field you want to search in
* `operator` the operator. Defaults to `=`. Also can be the value \(Same as [where](https://laravel.com/docs/queries#where-clauses)\)
* `value` the value you want to search for
* `locales` the locales you want to search in as an array. Leave as `null` if you want to search all locales
* `default` also search in the default value/locale. Defaults to true.

