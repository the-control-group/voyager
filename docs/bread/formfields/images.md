# Images

```php
{
    "resize": {
        "width": "1000",
        "height": null
    },
    "quality" : "70%",
    "upsize" : true,
    "preserveFileUploadName": true,
    "thumbnails": [
        {
            "name": "medium",
            "scale": "50%"
        },
        {
            "name": "small",
            "scale": "25%"
        },
        {
            "name": "cropped",
            "crop": {
                "width": "300",
                "height": "250"
            }
        }
    ]
}
```

The image input has many options. By default if you do not specify any options no problem... Your image will still be uploaded. But, if you want to resize an image, set the quality of the image, or specify thumbnails for the uploaded image you will need to specify those details.

**resize** If you want to specify a size you will need to include that in the `resize` object. If you set either **height** or **width** to null it will keep the aspect ratio based on the width or height that is set. So, for the example above the `width` is set to `1000` pixels and since the `height` is set to `null` it will resize the image width to 1000 pixels and resize the height based on the current aspect ratio.

**quality** If you wish to compress the image with a percentage quality you can specify that percentage in the `quality` key. Typically between 70 and 100% there is little notice of image quality, but the image size may be dramatically lower.

**upsize** This is only valid if you have set your image to be resized. If you specify your image to resized to 1000 pixels and the image is smaller than 1000 pixels by default it will not upsize that image to the 1000 pixels; however, if you set `upsize` to true. It will upsize all images to your specified resize values.

**thumbnails** Thumbnails takes an array of objects. Each object is a new thumbnail that is created. Each object contains 2 values, the `name` and `scale` percentage. The `name` will be attached to your thumbnail image \(as an example say the image you uploaded was ABC.jpg a thumbnail with the `name` of `medium` would now be created at ABC-medium.jpg\). The `scale` is the percentage amount you want that thumbnail to scale. This value will be a percentage of the _resize_ width and height if specified.

**preserveFileUploadName** By default, the image uploaded gets renamed with random String (20 chars - eg : iHJxYzbAmeFhOXSxMPG4.jpg). You can preserve your filename uploaded, setting `preserveFileUploadName` to `true`.
